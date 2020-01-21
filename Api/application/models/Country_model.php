<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Country_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }
    
    // GET DE LISTADO BUSQUEDA DE countryO //
    public function get($id = null, $searchFilter = null) {
        
        $quuery = null;

        // SI RECIBIMOS EL ID DEL countryO //
        if (!is_null($id)) {
            $this->db->select("*")->from("tb_country");
            $quuery = $this->db->where("idCountry = ", $id)->get();

            if ($quuery->num_rows() === 1) {
                $rs = array('country' => $quuery->row_array());
                return $rs;
            }
            return null;
        } else { // SI NO RECIBIMOS EL ID DEL countryO RETORNAMOS TODOS LOS REGISTROS 
            $this->db->select("*")->from("tb_country");

            /* Busqueda por filtro */
            if (!is_null($searchFilter['searchFilter'])) {
                $this->db->or_like('country', $searchFilter['searchFilter']);
            }
            // Si recibimos un limite //
            if ($searchFilter['topFilter'] > 0) {
                $this->db->limit($searchFilter['topFilter']);
            }

            $quuery = $this->db->order_by("tb_country.idCountry", "DESC")->get();


            if ($quuery->num_rows() > 0) {
                return $quuery->result_array();
            }
            return null;
        }
    }


    public function add($country) {
        $this->db->set($this->_setCountry($country))->insert("tb_country");
        if ($this->db->affected_rows() === 1) {
            $id = $this->db->insert_id();
            return $id;
        } else {
            return null;
        }
    }
    
    
    private function _setCountry($country) {
        return array(
            'country' => @$country['country'],
            'hcKey' => @$country['hcKey']
        );
    }
    
    /* EDITAR DATOS DE UN countryO */

    public function update($country) {
        $this->db->set($this->_setCountry($country))->where("idCountry", $country['idCountry'])->update("tb_country");
        if ($this->db->trans_status() === true) {
            return true;
        } else {
            return null;
        }
    }

}
