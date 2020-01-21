<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

date_default_timezone_set("America/Argentina/Buenos_Aires");

class CashBox_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

  	//-GET DE LISTADO DE CAJAS-----------------------------------------------------------------*/
    public function balance($search=null) {
        $query = null;

            $this->db->select("tb_cashBox.*,  if(i.income is NULL, 0, i.income) as income ,if(e.expense is NULL, 0, e.expense) as expense")->from("tb_cashBox");
//     tb_agency.nameAgency,   $this->db->join('tb_agency', 'tb_agency.idAgency = tb_cashBox.idAgencyKf', 'inner');

            $this->db->join('(select idCashBoxKf ,sum(amountIncome) as income from tb_income group by idCashBoxKf) as i', 'i.idCashBoxKf=tb_cashBox.idCashBox', 'left');

            $this->db->join('(select idCashBoxKf ,sum(amountExpense) as expense from tb_expense group by idCashBoxKf) as e', 'e.idCashBoxKf=tb_cashBox.idCashBox', 'left');
            if(!empty($search['box'])){
            	  $this->db->where("idCashBox = ", $search['box']);
            }
            if(!empty($search['search'])){
            	  $this->db->where("cashBoxName like ", '%'.$search['search'].'%');
            }
/*
            if(!empty($search['agency'])){
            	  $this->db->where("idAgencyKf = ", $search['agency']);
            }
*/
            $query = $this->db->get();

              if ($query->num_rows() >0) {
			           if(!empty($search['box'])){
			           		$result = $query->row_array();

										$this->db->select("idPaymentFormKf, namePaymentForm,sum(amountIncome) as paid")->from("tb_income");
										$this->db->join('tb_paymentform', 'tb_paymentform.idPaymentForm = idPaymentFormKf', 'inner');
										$this->db->where("idCashBoxKf = ", $search['box'])->group_by('idPaymentFormKf');
										$query = $this->db->get();
										$result['incomePayForm'] = ($query->num_rows()>0) ? $query->result_array() : [];
										//----------
										$this->db->select("idPaymentFormKf, namePaymentForm ,sum(amountExpense) as paid")->from("tb_expense");
										$this->db->join('tb_paymentform', 'tb_paymentform.idPaymentForm = idPaymentFormKf', 'inner');
										$this->db->where("idCashBoxKf = ", $search['box'])->group_by('idPaymentFormKf');
										$query = $this->db->get();
										$result['expensePayForm'] = ($query->num_rows()>0) ? $query->result_array() : [];

										return $result;
			            }else
	                  return $query->result_array();
              }

            return null;
    }
  	//-GET DE LISTADO DE CAJAS-----------------------------------------------------------------*/
    public function get($search=null) {
        $query = null;

            $this->db->select("tb_cashBox.*")->from("tb_cashBox");
//, tb_agency.nameAgency     $this->db->join('tb_agency', 'tb_agency.idAgency = tb_cashBox.idAgencyKf', 'inner');
            if(!empty($search['search'])){
            	  $this->db->where("cashBoxName like ", '%'.$search['search'].'%');
            }
            if(!empty($search['agency'])){
            	  $this->db->where("idAgencyKf = ", $search['agency']);
            }
            $query = $this->db->get();

              if ($query->num_rows()>0) {
                  return $query->result_array();
              }

            return null;
    }

  	//-AGRAGR NUEVA CAJA----------------------------------------------------------------------*/
    public function add($cashBox) {

        $this->db->insert('tb_cashBox', $cashBox);

        if ($this->db->affected_rows() === 1) {
            return $this->db->insert_id();
        }
        return null;
    }

  	//-OBTENER UNA CAJA PARTICULAR-------------------------------------------------------------*/
    public function getThis($idCashBox = null) {
        $query = null;

            $this->db->select("*")->from("tb_cashBox");
            $this->db->where("idCashBox = ", $idCashBox);
            $query = $this->db->get();

            if ($query->num_rows() === 1) {
                  return $query->row_array();
              }

            return null;
    }

  	//-PERMITE MODIFICAR EL NOMBRE DE LA CAJA--------------------------------------------------*/
    public function update($cashBox) {

        $this->db->set($cashBox)->where("idCashBox", $cashBox['idCashBox'])->update("tb_cashBox");
        if ($this->db->trans_status() === true) {
            return true;
        }
            return null;
    }

  	//-PERMITE ELIMINAR UNA CAJA---------------------------------------------------------------*/
    public function erase($cashBox) {

        $this->db->where("idCashBox", $cashBox)->delete("tb_cashBox");
        if ($this->db->trans_status() === true) {
            return true;
        }
            return null;
    }
}
