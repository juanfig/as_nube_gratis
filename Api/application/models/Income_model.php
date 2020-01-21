<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

date_default_timezone_set("America/Argentina/Buenos_Aires");

class Income_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

  	//-GET DE LISTADO DE TIPOS DE EGRESOS-----------------------------------------------------*/
    public function get($search=null) {
        $query = null;

            $this->db->select("tb_income.*, tb_cashBox.cashBoxName ,
            tb_typeincome.nameIncome, tb_paymentform.namePaymentForm")->from("tb_income");
            $this->db->join('tb_cashBox', 'tb_cashBox.idCashBox = tb_income.idCashBoxKf', 'inner');
            $this->db->join('tb_typeincome', 'tb_typeincome.idTypeIncome = tb_income.idTypeIncomeKf', 'inner');
            $this->db->join('tb_paymentform', 'tb_paymentform.idPaymentForm = tb_income.idPaymentFormKf', 'inner');
            if(!empty($search['box'])){
            	  $this->db->where("tb_income.idCashBoxKf=", $search['box']);
            }
            if(!empty($search['type'])){
            	  $this->db->where("tb_income.idTypeIncomeKf =", $search['type']);
            }
            if(!empty($search['form'])){
            	  $this->db->where("tb_income.idPaymentFormKf =", $search['form']);
            }
            if(!empty($search['info'])){
            	  $this->db->where("tb_income.aditionalInfo  like ", '%'.$search['info'].'%');
            }
            if(!empty($search['agency'])){
            	  $this->db->where("tb_cashBox.idAgencyKf  =", $search['agency']);
            }
            if(!empty($search['date'])){
            	  $this->db->where("tb_income. like", $search['date'].'%');
            }
            $query = $this->db->get();

              if ($query->num_rows() >0) {
                  $result = $query->result_array();
                  return $result;
              }

            return null;
    }

  	//-AGRAGR NUEVO---------------------------------------------------------------------------*/
    public function add($income) {

        $this->db->insert('tb_income', $income);

        if ($this->db->affected_rows() === 1) {
            return $this->db->insert_id();
        }
        return null;
    }

  	//-OBTENER UN TIPO DE EGRESO PARTICULAR---------------------------------------------------*/
    public function getThis($idIncome = null) {
        $query = null;

            $this->db->select("*")->from("tb_income");
            $this->db->where("idIncome = ", $idIncome);
            $query = $this->db->get();

            if ($query->num_rows() === 1) {
                  return $query->row_array();
              }

            return null;
    }

  	//-PERMITE MODIFICAR EL NOMBRE DEL TIPO DE EGRESO-----------------------------------------*/
    public function update($income) {

        $this->db->set($income)->where("idIncome", $income['idIncome'])->update("tb_income");
        if ($this->db->trans_status() === true) {
            return true;
        }
            return null;
    }

  	//-PERMITE ELIMINAR UN TIPO DE EGRESO-----------------------------------------------------*/
    public function erase($income) {

        $this->db->where("idIncome", $income)->delete("tb_income");
        if ($this->db->trans_status() === true) {
            return true;
        }
            return null;
    }
}
