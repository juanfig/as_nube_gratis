<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Profile_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    // GET DE LISTADO BUSQUEDA DE PERFILES //
    public function get($id = null, $searchFilter = null) {
        $quuery = null;

        // SI RECIBIMOS EL ID DEL PERFILE//
      if (!is_null($id)) {
            $this->db->select("*")->from("tb_profile_system");
            $this->db->where("tb_profile_system.idStatusProfile !=", -1);
            $quuery = $this->db->where("tb_profile_system.idProfile = ", $id)->get();



          if ($quuery->num_rows() === 1) {

              $profile = $quuery->row_array();

              $modules = null;

                $this->db->select("*")->from("tb_sys_module_profile");
                $this->db->join('tb_sys_modules', 'tb_sys_modules.idItem = tb_sys_module_profile.idModuleKf', 'left');
                $this->db->where_not_in("tb_sys_modules.idItem ", "SELECT idModuleProfile  from tb_sys_module_profile  where idProfileKf = ".$id."" );

                $quuery =   $this->db->where("idProfileKf =", $id)->get();

              if ($quuery->num_rows() > 0) {
                  $active_modules = $quuery->result_array();
              }

              $sqlwhere = "";
              if(@$idProfile == 'undefined')
              {
                   $sqlwhere = "";
              }else{
                   $sqlwhere = "where t1.idItem not in 
                    (
                    SELECT idModuleKf   from tb_sys_module_profile where idProfileKf = ".$id."  and  idModuleKf IS NOT NULL
                    )";
              }

              $query = $this->db->query(" SELECT * FROM tb_sys_modules t1  ".$sqlwhere." ");

              if ($query->num_rows() > 0) {
                  $inactive_modules = $query->result_array();
              }
  

                $rs = array(
                  'profile' => $profile,
                  'active_modules' => @$active_modules,
                  'inactive_modules' => @$inactive_modules
                 );

                return $rs;
            }
            return null;
      } else { // SI NO RECIBIMOS EL ID DE PERFIL
          
          $this->db->select("*")->from("tb_profile_system");

            if ($this->userInfo->idProfileUser == 3) {
                $this->db->where("idProfile >", $this->userInfo->idProfileUser);
            }

            if (!empty($searchFilter['searchFilter'])) {

                $this->db->or_like('profileName', $searchFilter['searchFilter']);
            }elseif (!empty($searchFilter['restrict'])) {
              $this->db->where("idProfile !=", $searchFilter['restrict']);
            }
            // Si recibimos un limite //
            if (!empty($searchFilter['topFilter'])) {
                $this->db->limit($searchFilter['topFilter'])->get();
            }
            $quuery = $this->db->order_by("tb_profile_system.idProfile", "DES")->get();


            if ($quuery->num_rows() > 0) {
                return $quuery->result_array();
            }
            return null;
        }
    }

    /* LISTADO DE FILTROS */
    public function getFilterForm($idProfile) {

        $modules = null;

       

        /* LISTADO DE TIPO DE CAJAS */


        $this->db->select("*")->from("tb_sys_module_profile");
        $query= $this->db->where("idProfileKf",$idProfile)->get();
        if ($query->num_rows() > 0) {
            $modules = $query->result_array();
        }

        $filter = array(
            'modules_actives' => $modules
        );

        return $filter;
    }

    /* AGRAGR NUEVO PERFIL */
    public function addModule($profile) {

        $this->db->insert('tb_sys_module_profile', array(
            'idModuleKf'  => $profile['idModuleKf'],
            'idProfileKf' => $profile['idProfileKf'])
        );

        if ($this->db->affected_rows() === 1) {
            $idModuleProfile = $this->db->insert_id();
            return $idModuleProfile;
        } else {
            return null;
        }
    }


    public function changueStatus($id, $idStatusProfile) {
        $this->db->set(
                array(
                    'idStatusProfile' => $idStatusProfile
                )
        )->where("idProfile", $id)->update("tb_profile_system");


        if ($this->db->affected_rows() === 1) {
            return true;
        } else {
            return null;
        }
    }

    /* EDITAR DATOS DE UN PERFIL  */
    public function update($profile) {

      $this->db->set(
                  array(
                      'profileName' => $profile['profileName']
                      )
                )->where("idProfile", $profile['idProfile'])->update("tb_profile_system");


        if ($this->db->trans_status() === true) {
            return true;
        } else {
            return null;
        }
    }


    public function deleteModule($parameters)
    {

        $this->db->where('idProfileKf',$parameters['idProfileKf']);
        $this->db->where('idModuleProfile',$parameters['idModuleKf']);
        $this->db->delete("tb_sys_module_profile");
        return true;
    }

    public function addProfile($profile) {

            $this->db->set($this->_setProfile($profile))->insert("tb_profile_system");
            if ($this->db->affected_rows() === 1) {
                $id = $this->db->insert_id();
                return $id;
            } else {
                return null;
            }

    }
    
    
    public function _setProfile($profile) {
        return array(
            'profileName' => @$profile['profileName']
            );
    }

}
  ?>
