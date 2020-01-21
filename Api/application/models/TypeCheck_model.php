<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

date_default_timezone_set("America/Argentina/Buenos_Aires");

class TypeCheck_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

  	//-GET DE LISTADO DE TIPOS DE CHEQUES-----------------------------------------------------*/
    public function get($search=null) {
        $query = null;

            $this->db->select("*")->from("tb_typecheck");
            if(!is_null($search)){
            	  $this->db->where("nameTypeCheck like ", '%'.$search.'%');
            }
            $query = $this->db->get();

              if ($query->num_rows() >0) {
                  $result = $query->result_array();
                  return $result;
              }

            return null;
    }

  	//-AGRAGR NUEVO---------------------------------------------------------------------------*/
    public function add($typeCheck) {

        $this->db->insert('tb_typecheck', $typeCheck);

        if ($this->db->affected_rows() === 1) {
            return $this->db->insert_id();
        }
        return null;
    }

  	//-OBTENER UN TIPO DE CHEQUE PARTICULAR---------------------------------------------------*/
    public function getThis($idTypeCheck = null) {
        $query = null;

            $this->db->select("*")->from("tb_typecheck");
            $this->db->where("idTypeCheck = ", $idTypeCheck);
            $query = $this->db->get();

            if ($query->num_rows() === 1) {
                  return $query->row_array();
              }

            return null;
    }

  	//-PERMITE MODIFICAR EL NOMBRE DEL TIPO DE CHEQUE-----------------------------------------*/
    public function update($typeCheck) {

        $this->db->set($typeCheck)->where("idTypeCheck", $typeCheck['idTypeCheck'])->update("tb_typecheck");
        if ($this->db->trans_status() === true) {
            return true;
        }
            return null;
    }

  	//-PERMITE ELIMINAR UN TIPO DE CHEQUE-----------------------------------------------------*/
    public function erase($typeCheck) {

        $this->db->where("idTypeCheck", $typeCheck)->delete("tb_typecheck");
        if ($this->db->trans_status() === true) {
            return true;
        }
            return null;
    }
}
