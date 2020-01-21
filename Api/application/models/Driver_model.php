<?php
if (!defined('BASEPATH'))  exit('No direct script access allowed');

class Driver_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }


    /* AGRAGR NUEVO CHOFER */
    public function add($driver) {

      //print_r($driver);
      $idioma = null;

      // VERIFICAMOS SI EL CHOFER ESTA REGISTRADO //
      $this->db->select("*")->from("tb_user_ex");
      $this->db->where("emailUser =", @$driver['emailDriver']);
      $query =$this->db->where("idStatusUser =", 1)->get();
      //$query = $this->db->where("passUser =",sha1(md5(@$driver['passDriver'])))->get();



      if ($query->num_rows() < 1) {

        /* CREAMOS UN USUARIO PARA ESE CLIENE */
        $this->db->insert('tb_user_ex', array(
            'userNameUser' => @$driver['emailDriver'],
            'firstNameUser' => @$driver['fisrtNameDriver'],
            'lastNameUser' => @$driver['lastNameDriver'],
            'emailUser' => @$driver['emailDriver'],
            'passUser' => sha1(md5(@$driver['passDriver'])),
            'idProfileUser' => 3,
            'idAgencyKf' => @$driver['idAgencyKf']
                )
        );

        if ($this->db->affected_rows() === 1) {
            $idUser = $this->db->insert_id();

            $this->db->select("*")->from("tb_agency");
            $this->db->join('tb_clouds', 'tb_clouds.idCloud = tb_agency.idCloudKf', 'left');
            $cloud = $this->db->where("tb_agency.idAgency = ", @$driver['idAgencyKf'])->get();

            if ($cloud->num_rows() > 0) {
                $cloud = $cloud->row_array();
                $idioma = $cloud['idLanguageKf'];
            }

            return [
              'idUser' => $idUser,
              'idLanguageKf' => $idioma
            ];
        } else {
            return null;
        }


      }else {
        return -1;// USUARIO EXITE
      }
    }

    public function update($user)
    {

        // EDITAMO EL USUARIO //
        $this->db->set(array(
          "emailUser"  =>  $user['emailDriver'],
         "userNameUser"  =>  $user['emailDriver'],
          "firstNameUser"  => $user['fisrtNameDriver'],
          "passUser" =>  $user['passUser']
        //  "passUser" => $user['passDriver']
          )
        )->where("idUser", $user['idUserCloud'])->update("tb_user_ex");

        if ($this->db->trans_status() === true)
        {
          return true;
        }
        else {
          return false;
        }

    }

    public function changueStatus($id, $idStatus) {
        $this->db->set(array(
          "idStatusUser"  =>  $idStatus
          )
        )->where("idUser", $id)->update("tb_user_ex");


        if ($this->db->affected_rows() === 1) {
            return true;
        } else {
            return null;
        }
    }


}
