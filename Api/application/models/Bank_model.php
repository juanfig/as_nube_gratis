<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

date_default_timezone_set("America/Argentina/Buenos_Aires");

class Bank_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

  	//-GET DE LISTADO DE TIPOS DE CHEQUES-----------------------------------------------------*/
    public function get($search=null) {
        $query = null;

            $this->db->select("*")->from("tb_bank");
            if(!is_null($search)){
            	  $this->db->where("nameBank like ", '%'.$search.'%');
            }
            $query = $this->db->get();

              if ($query->num_rows() >0) {
                  $result = $query->result_array();
                  return $result;
              }

            return null;
    }

  	//-AGRAGR NUEVO---------------------------------------------------------------------------*/
    public function add($bank) {

        $this->db->insert('tb_bank', $bank);

        if ($this->db->affected_rows() === 1) {
            return $this->db->insert_id();
        }
        return null;
    }

  	//-OBTENER UN TIPO DE CHEQUE PARTICULAR---------------------------------------------------*/
    public function getThis($idBank = null) {
        $query = null;

            $this->db->select("*")->from("tb_bank");
            $this->db->where("idBank = ", $idBank);
            $query = $this->db->get();

            if ($query->num_rows() === 1) {
                  return $query->row_array();
              }

            return null;
    }

  	//-PERMITE MODIFICAR EL NOMBRE DEL TIPO DE CHEQUE-----------------------------------------*/
    public function update($bank) {

        $this->db->set($bank)->where("idBank", $bank['idBank'])->update("tb_bank");
        if ($this->db->trans_status() === true) {
            return true;
        }
            return null;
    }

  	//-PERMITE ELIMINAR UN TIPO DE CHEQUE-----------------------------------------------------*/
    public function erase($bank) {

        $this->db->where("idBank", $bank)->delete("tb_bank");
        if ($this->db->trans_status() === true) {
            return true;
        }
            return null;
    }
}
