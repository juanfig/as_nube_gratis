<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

date_default_timezone_set("America/Argentina/Buenos_Aires");

class TypeIncome_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

  	//-GET DE LISTADO DE TIPOS DE INGRESOS-----------------------------------------------------*/
    public function get($search=null) {
        $query = null;

            $this->db->select("*")->from("tb_typeincome");
            if(!is_null($search)){
            	  $this->db->where("nameIncome like ", '%'.$search.'%');
            }
            $query = $this->db->get();

              if ($query->num_rows() >0) {
                  $result = $query->result_array();
                  return $result;
              }

            return null;
    }

  	//-AGRAGR NUEVO---------------------------------------------------------------------------*/
    public function add($typeIncome) {

        $this->db->insert('tb_typeincome', $typeIncome);

        if ($this->db->affected_rows() === 1) {
            return $this->db->insert_id();
        }
        return null;
    }

  	//-OBTENER UN TIPO DE INGRESO PARTICULAR---------------------------------------------------*/
    public function getThis($idTypeIncome = null) {
        $query = null;

            $this->db->select("*")->from("tb_typeincome");
            $this->db->where("idTypeIncome = ", $idTypeIncome);
            $query = $this->db->get();

            if ($query->num_rows() === 1) {
                  return $query->row_array();
              }

            return null;
    }

  	//-PERMITE MODIFICAR EL NOMBRE DEL TIPO DE INGRESO-----------------------------------------*/
    public function update($typeIncome) {

        $this->db->set($typeIncome)->where("idTypeIncome", $typeIncome['idTypeIncome'])->update("tb_typeincome");
        if ($this->db->trans_status() === true) {
            return true;
        }
            return null;
    }

  	//-PERMITE ELIMINAR UN TIPO DE INGRESO-----------------------------------------------------*/
    public function erase($typeIncome) {

        $this->db->where("idTypeIncome", $typeIncome)->delete("tb_typeincome");
        if ($this->db->trans_status() === true) {
            return true;
        }
            return null;
    }
}
