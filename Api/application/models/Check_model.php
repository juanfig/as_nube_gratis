<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

date_default_timezone_set("America/Argentina/Buenos_Aires");

class Check_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

  	//-GET DE LISTADO DE CHEQUES---------------------------------------------------------------*/
    public function get($search=null) {
        $query = null;

            $this->db->from("tb_check");

            if(!empty($search['use'])){
            	switch($search['use']) {
								case "in":
										$this->db->select("tb_check.*,tb_income.idTypeIncomeKf,tb_income.idCashBoxKf as incomeBox");
										$this->db->join('tb_check_income', 'tb_check_income.idCheckKf = idCheck', 'inner');
										$this->db->join('tb_income', 'tb_check_income.idIncomeKf = idIncome', 'inner');

				            if(!empty($search['box'])){
				            	  $this->db->where("tb_income.idCashBoxKf= ", $search['box']);
                            }
                            if(!empty($search['idIncome'])){
                                $this->db->where("tb_check_income.idIncomeKf ", $search['idIncome']);
                          }
									break;
								case "out":
										$this->db->select("tb_check.*,tb_expense.idTypeExpenseKf ,tb_expense.idCashBoxKf as expenseBox ,tb_expense.dateExpense");
										$this->db->join('tb_check_expense', 'tb_check_expense.idCheckKf = idCheck', 'inner');
										$this->db->join('tb_expense', 'tb_check_expense.idExpenseKf = idExpense', 'inner');
				            if(!empty($search['box'])){
				            	  $this->db->where("tb_expense.idCashBoxKf= ", $search['box']);
				            }
                            if(!empty($search['idExpense'])){
                                  $this->db->where("tb_check_expense.idExpenseKf ", $search['idExpense']);
                            }
									break;
								default:
									$this->db->select("tb_check.*,tb_income.idTypeIncomeKf,tb_income.idCashBoxKf as incomeBox ,tb_income.dateIncome, tb_expense.idTypeExpenseKf ,tb_expense.idCashBoxKf as expenseBox ,tb_expense.dateExpense");
									$this->db->join('tb_check_income', 'tb_check_income.idCheckKf = idCheck', 'left');
									$this->db->join('tb_income', 'tb_check_income.idIncomeKf = idIncome', 'left');
									$this->db->join('tb_check_expense', 'tb_check_expense.idCheckKf = idCheck', 'left');
									$this->db->join('tb_expense', 'tb_check_expense.idExpenseKf = idExpense', 'left');

				            if(!empty($search['box'])){

				            	  $this->db->group_start()->where("tb_income.idCashBoxKf= ", $search['box']);
				            	  $this->db->or_where("tb_expense.idCashBoxKf= ", $search['box'])->group_end();
				            }
            	 }
            } 

            if(!empty($search['number'])){
            	  $this->db->where("checkNumber = ", $search['number']);
            }

            if(isset($search['own']))
            	  $this->db->where("own = ", $search['own']);
     

            if(!empty($search['bank'])){
            	  $this->db->where("idBankKf = ", $search['bank']);
            }

            if(!empty($search['type'])){
            	  $this->db->where("idTypeChecKf = ", $search['type']);
            }

            $query = $this->db->get();

              if ($query->num_rows() >0) {
                  return $query->result_array();
              }

            return null;
    }
  	//-GET DE LISTADO DE CHEQUES DE TERCEROS DISPONIBLES PARA GASTOS---------------------------*/
    public function availables() {
        $query = null;

            $this->db->select("tb_check.*")->from("tb_check");
            $this->db->join('tb_check_income', 'idCheckKf = idCheck', 'inner');
						$this->db->where("idCheck  NOT IN (select idCheckKf from tb_check_expense)");
         	  $this->db->where("own = ", 0);
            $query = $this->db->get();

              if ($query->num_rows() >0){
                  return $query->result_array();
                }

            return null;
    }
  	//-AGRAGR NUEVO---------------------------------------------------------------------------*/
    public function add($check,$joinTo=null) {

    	if(!empty($check['idCheck'])) { //si el cheque ya existe y esta siendo usado para pagar un gasto
            if(!empty($joinTo) && isset($joinTo["table"]) && isset($joinTo["check"]) &&
            											(isset($joinTo["check"]["idIncomeKf"]) || isset($joinTo["check"]["idExpenseKf"]) ) ){
							$joinTo["check"]["idCheckKf"]=$check['idCheck'];
							$this->db->insert( $joinTo["table"], $joinTo["check"]);
            }

    	}else{//si el cheque es nuevo

        $this->db->insert('tb_check', $check);

        if ($this->db->affected_rows() === 1) {
            $idCheck= $this->db->insert_id();
            if(!empty($joinTo) && isset($joinTo["table"]) && isset($joinTo["check"]) &&
            											(isset($joinTo["check"]["idIncomeKf"]) || isset($joinTo["check"]["idExpenseKf"]) ) ){
							$joinTo["check"]["idCheckKf"]=$idCheck;
							$this->db->insert( $joinTo["table"], $joinTo["check"]);
            }
            return $idCheck;
        }
        return null;
    	}
    }

  	//-OBTENER UN CHEQUE PARTICULAR------------------------------------------------------------*/
    public function getThis($idCheck = null) {
        $query = null;

            $this->db->select("*")->from("tb_check");
            $this->db->where("idCheck = ", $idCheck);
            $query = $this->db->get();

            if ($query->num_rows() === 1) {
                  return $query->row_array();
              }

            return null;
    }

  	//-PERMITE MODIFICAR EL CHEQUE-------------------------------------------------------------*/
    public function update($check) {

        $this->db->set($check)->where("idCheck", $check['idCheck'])->update("tb_check");
        if ($this->db->trans_status() === true) {
            return true;
        }
            return null;
    }

  	//-PERMITE ELIMINAR UN CHEQUE--------------------------------------------------------------*/
    public function erase($check) {

        $this->db->where("idCheck", $check)->delete("tb_check");
        if ($this->db->trans_status() === true) {
            return true;
        }
            return null;
    }
}
