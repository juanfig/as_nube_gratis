<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

date_default_timezone_set("America/Argentina/Buenos_Aires");

class Expense_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

  	//-GET DE LISTADO DE TIPOS DE EGRESOS-----------------------------------------------------*/
    public function get($search=null) {
        $query = null;

            $this->db->select("tb_expense.*, tb_cashBox.cashBoxName ,
            tb_typeexpense.nameExpense, tb_paymentform.namePaymentForm")->from("tb_expense");
            $this->db->join('tb_cashBox', 'tb_cashBox.idCashBox = tb_expense.idCashBoxKf', 'inner');
            $this->db->join('tb_typeexpense', 'tb_typeexpense.idTypeExpense = tb_expense.idTypeExpenseKf', 'inner');
            $this->db->join('tb_paymentform', 'tb_paymentform.idPaymentForm = tb_expense.idPaymentFormKf', 'inner');
            if(!empty($search['box'])){
            	  $this->db->where("tb_expense.idCashBoxKf=", $search['box']);
            }
            if(!empty($search['type'])){
            	  $this->db->where("tb_expense.idTypeExpenseKf =", $search['type']);
            }
            if(!empty($search['form'])){
            	  $this->db->where("tb_expense.idPaymentFormKf =", $search['form']);
            }
            if(!empty($search['info'])){
            	  $this->db->where("tb_expense.aditionalInfo  like ", '%'.$search['info'].'%');
            }
            if(!empty($search['agency'])){
            	  $this->db->where("tb_cashBox.idAgencyKf  =", $search['agency']);
            }
            if(!empty($search['date'])){
            	  $this->db->where("tb_expense. like", $search['date'].'%');
            }
            $query = $this->db->get();

              if ($query->num_rows() >0) {
                  $result = $query->result_array();
                  return $result;
              }

            return null;
    }

  	//-AGRAGR NUEVO---------------------------------------------------------------------------*/
    public function add($expense) {

        $this->db->insert('tb_expense', $expense);

        if ($this->db->affected_rows() === 1) {
            return $this->db->insert_id();
        }
        return null;
    }

  	//-OBTENER UN TIPO DE EGRESO PARTICULAR---------------------------------------------------*/
    public function getThis($idExpense = null) {
        $query = null;

            $this->db->select("*")->from("tb_expense");
            $this->db->where("idExpense = ", $idExpense);
            $query = $this->db->get();

            if ($query->num_rows() === 1) {
                  return $query->row_array();
              }

            return null;
    }

    public function getCheck($idExpense = null) {
    $query = null;

        $this->db->select("*")->from("tb_expense");
        $this->db->where("idExpense = ", $idExpense);
        $query = $this->db->get();

        if ($query->num_rows() === 1) {
              return $query->row_array();
          }

        return null;
    }


  	//-PERMITE MODIFICAR EL NOMBRE DEL TIPO DE EGRESO-----------------------------------------*/
    public function update($expense) {

        $this->db->set($expense)->where("idExpense", $expense['idExpense'])->update("tb_expense");
        if ($this->db->trans_status() === true) {
            return true;
        }
            return null;
    }

  	//-PERMITE ELIMINAR UN TIPO DE EGRESO-----------------------------------------------------*/
    public function erase($expense) {

        $this->db->where("idExpense", $expense)->delete("tb_expense");
        if ($this->db->trans_status() === true) {
            return true;
        }
            return null;
    }
}
