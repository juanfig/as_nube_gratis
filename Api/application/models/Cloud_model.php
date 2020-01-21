<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Cloud_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }
    
    // GET DE LISTADO BUSQUEDA DE CloudO //
    public function get($id = null, $searchFilter = null) {

        $quuery = null;

        // SI RECIBIMOS EL ID DEL CloudO //
        if (!is_null($id)) {
            $this->db->select("*")->from("tb_clouds");
            $this->db->join('tb_country', 'tb_country.idCountry = tb_clouds.idCountryKf', 'inner');
           if($this->userInfo->idProfileUser == 3) {
             $this->db->join('tb_partner', 'tb_clouds.idCountryKf = tb_partner.idCountryKf', 'inner');
             $this->db->where("tb_partner.idUserKf", $this->userInfo->idUser);
           }
            $quuery = $this->db->where("idCloud = ", $id)->get();

            if ($quuery->num_rows() === 1) {
                $rs = array('cloud' => $quuery->row_array());
                return $rs;
            }
            return null;
        } else { // SI NO RECIBIMOS EL ID DEL CloudO RETORNAMOS TODOS LOS REGISTROS 
            $this->db->select("*")->from("tb_clouds");
            $this->db->join('tb_country', 'tb_country.idCountry = tb_clouds.idCountryKf', 'inner');
            $this->db->join('tb_language', 'tb_language.idLanguage = tb_clouds.idLanguageKf', 'left');


           if($this->userInfo->idProfileUser == 3) {
             $this->db->join('tb_partner', 'tb_clouds.idCountryKf = tb_partner.idCountryKf', 'inner');
             $this->db->where("tb_partner.idUserKf", $this->userInfo->idUser);
           }

            /* Busqueda por filtro */
            if (!is_null($searchFilter['searchFilter'])) {
                $this->db->or_like('nameCloud', $searchFilter['searchFilter']);
                $this->db->or_like('country', $searchFilter['searchFilter']);
            }
            // Si recibimos un limite //
            if ($searchFilter['topFilter'] > 0) {
                $this->db->limit($searchFilter['topFilter']);
            }

            $quuery = $this->db->order_by("tb_clouds.idCloud", "DESC")->get();


            if ($quuery->num_rows() > 0) {
                return $quuery->result_array();
            }
            return null;
        }
    }

    // GET DE LISTADO BUSQUEDA DE CloudO //
    public function getFilter() {

        $country = null;
        $language = null;

        $query = $this->db->select("*")->from("tb_country")
        //->where("nube",1)
        ->get();
        if ($query->num_rows() > 0) {
            $country = $query->result_array();
        }

        $query = $this->db->select("*")->from("tb_language")->get();
        if ($query->num_rows() > 0) {
            $language = $query->result_array();
        }

        $query = $this->db->select("*")->from("tb_agency")->get();
        if ($query->num_rows() > 0) {
            $agencys = $query->result_array();
        }

        return [
            'country' => $country,
            'language' => $language,
            'agencys' => $agencys
        ];
        
    }


    public function add($cloud) {
        $this->db->set($this->_setCloud($cloud))->insert("tb_clouds");
        if ($this->db->affected_rows() === 1) {
            $id = $this->db->insert_id();
            return $id;
        } else {
            return null;
        }
    }
    
    
    private function _setCloud($cloud) {
        return array(
            'nameCloud' => @$cloud['nameCloud'],
            'uri' => @$cloud['uri'],
            'uriLogo' => @$cloud['uriLogo'],
            'idCountryKf' => @$cloud['idCountryKf'],
            'idLanguageKf' => @$cloud['idLanguageKf'],
            'titleFastTravel' => @$cloud['titleFastTravel'],
            'activateReport' => @$cloud['activateReport'],
            'iva' => @$cloud['iva'],
            'idStatuCloud' => 1

            );
    }
    
    /* EDITAR DATOS DE UN CloudO */

    public function update($cloud) {
        $this->db->set($this->_setCloud($cloud))->where("idCloud", $cloud['idCloud'])->update("tb_clouds");
        if ($this->db->trans_status() === true) {
            return true;
        } else {
            return null;
        }
    }

    //ELIMINAR DATOS 
    public function delete($id)
    {
        $this->db->where("idCloud", $id)->delete("tb_clouds");

        if ($this->db->trans_status() === true){
            return  true;
        }else{
            return null;
        }
    }

    
    public function changueStatus($cloud) {
        $this->db->set(
            array(
                'idStatuCloud' => $cloud['idStatuCloud']
                )
            )->where("idCloud", $cloud['idCloud'])->update("tb_clouds");

        if ($this->db->affected_rows() === 1) {
            return true;
        } else {
            return null;
        }
    }

}
