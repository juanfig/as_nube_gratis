<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

date_default_timezone_set("America/Argentina/Buenos_Aires");

class TypeExpense_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

  	//-GET DE LISTADO DE TIPOS DE EGRESOS-----------------------------------------------------*/
    public function get($search=null) {
        $query = null;

            $this->db->select("*")->from("tb_typeexpense");
            if(!is_null($search)){
            	  $this->db->where("nameExpense like ", '%'.$search.'%');
            }
            $query = $this->db->get();

              if ($query->num_rows() >0) {
                  $result = $query->result_array();
                  return $result;
              }

            return null;
    }

  	//-AGRAGR NUEVO---------------------------------------------------------------------------*/
    public function add($typeExpense) {

        $this->db->insert('tb_typeexpense', $typeExpense);

        if ($this->db->affected_rows() === 1) {
            return $this->db->insert_id();
        }
        return null;
    }

  	//-OBTENER UN TIPO DE EGRESO PARTICULAR---------------------------------------------------*/
    public function getThis($idTypeExpense = null) {
        $query = null;

            $this->db->select("*")->from("tb_typeexpense");
            $this->db->where("idTypeExpense = ", $idTypeExpense);
            $query = $this->db->get();

            if ($query->num_rows() === 1) {
                  return $query->row_array();
              }

            return null;
    }

  	//-PERMITE MODIFICAR EL NOMBRE DEL TIPO DE EGRESO-----------------------------------------*/
    public function update($typeExpense) {

        $this->db->set($typeExpense)->where("idTypeExpense", $typeExpense['idTypeExpense'])->update("tb_typeexpense");
        if ($this->db->trans_status() === true) {
            return true;
        }
            return null;
    }

  	//-PERMITE ELIMINAR UN TIPO DE EGRESO-----------------------------------------------------*/
    public function erase($typeExpense) {

        $this->db->where("idTypeExpense", $typeExpense)->delete("tb_typeexpense");
        if ($this->db->trans_status() === true) {
            return true;
        }
            return null;
    }
}
