<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

date_default_timezone_set("America/Argentina/Buenos_Aires");

class PaymentForm_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

  	//-GET DE LISTADO DE TIPOS DE INGRESOS-----------------------------------------------------*/
    public function get($search=null) {
        $query = null;

            $this->db->select("*")->from("tb_paymentform");
            if(!is_null($search)){
            	  $this->db->where("namePaymentForm like ", '%'.$search.'%');
            }
            $query = $this->db->get();

              if ($query->num_rows() >0) {
                  $result = $query->result_array();
                  return $result;
              }

            return null;
    }

  	//-AGRAGR NUEVO---------------------------------------------------------------------------*/
    public function add($paymentForm) {

        $this->db->insert('tb_paymentform', $paymentForm);

        if ($this->db->affected_rows() === 1) {
            return $this->db->insert_id();
        }
        return null;
    }

  	//-OBTENER UN TIPO DE INGRESO PARTICULAR---------------------------------------------------*/
    public function getThis($idPaymentForm = null) {
        $query = null;

            $this->db->select("*")->from("tb_paymentform");
            $this->db->where("idPaymentForm = ", $idPaymentForm);
            $query = $this->db->get();

            if ($query->num_rows() === 1) {
                  return $query->row_array();
              }

            return null;
    }

  	//-PERMITE MODIFICAR EL NOMBRE DEL TIPO DE INGRESO-----------------------------------------*/
    public function update($paymentForm) {

        $this->db->set($paymentForm)->where("idPaymentForm", $paymentForm['idPaymentForm'])->update("tb_paymentform");
        if ($this->db->trans_status() === true) {
            return true;
        }
            return null;
    }

  	//-PERMITE ELIMINAR UN TIPO DE INGRESO-----------------------------------------------------*/
    public function erase($paymentForm) {

        $this->db->where("idPaymentForm", $paymentForm)->delete("tb_paymentform");
        if ($this->db->trans_status() === true) {
            return true;
        }
            return null;
    }
}
