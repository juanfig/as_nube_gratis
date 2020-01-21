<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

date_default_timezone_set("America/Argentina/Buenos_Aires");

class Liquidation_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

  	//-GET DE LISTADO DE LIQUIDACIONES-----------------------------------------------------------*/
    public function get($search = null) {
        date_default_timezone_set('UTC');
         if(empty($search['id'])) {
            $this->db->select("*, MONTHNAME(monthClosure) as month, if(liquidationPercent=0, 'Por Chofer', 'Por Porcentaje') as mode")
                     ->from("tb_liquidation_agency")->order_by("idLiquidation", "DESC");

            if(!empty($search['agency'])){
            	  $this->db->where("idAgencyFk =", $search['agency']);
            }
            if(!empty($search['closure'])){
            	  $this->db->where("monthClosure like", $search['date'].'%');
            }else{
            		$this->db->where("monthClosure like", date("Y-m", strtotime("previous month")).'%');
            }
            if(!empty($search['state'])){
            	  $this->db->where("status =", $search['status']);
            }

            //$query = $this->db->get_compiled_select(); return $query;
            $query = $this->db->get();

              if ($query->num_rows() >0) {
                  $result = $query->result_array();
                  return $result;
              }
         } else {
            $query = $this->db->select("*, MONTHNAME(monthClosure) as month, if(liquidationPercent=0, 'Por Chofer', 'Por Porcentaje') as mode")
                     ->from("tb_liquidation_agency")->where("idLiquidation", $search['id'])->get();
              if ($query->num_rows() >0) {
                  $result = $query->row_array();
                  return $result;
              }
         }

            return null;
    }

  	//-AGRAGR NUEVA LIQUIDACION------------------------------------------------------------------*/
    public function add($liquidation) {

				$query = $this->db->set($liquidation)->get_compiled_insert('tb_liquidation_agency');
				$query = str_replace("INSERT", "INSERT IGNORE", $query);

				$this->db->query($query);
        if ($this->db->affected_rows() === 1) {
            return $this->db->insert_id();
        }
        return null;
    }

  	//-PERMITE MODIFICAR EL NOMBRE DE LA LIQUIDACION---------------------------------------------*/
    public function update($liquidation) {

        $this->db->set($liquidation)->where("idLiquidation", $liquidation['idLiquidation'])->update("tb_liquidation_agency");
        if ($this->db->trans_status() === true) {
            return true;
        }
            return null;
    }

  	//-CREA LAS LIQUIDACIONES AL CIERRE DEL MES--------------------------------------------------*/
    public function setLiquidations() {

			$instances = $this->db->select("idAgency, nameAgency, instancedb, liquidationPercent, liquidationFixed")->where("statuAgency",1)->from("tb_agency")->get();
			$instances = !$instances ? [] : $instances->result_array();

        $closure = date("Y-m-", strtotime("previous month"));

        $response = [];
				foreach($instances as $db) {

          $liquidation = [
                					"idAgencyFk"=> $db["idAgency"],
                					"status"=> 0,
                					//"liquidationDate"=> date("Y-m-d"),
                					"monthClosure"=> date("Y-m-d", strtotime("last day of previous month")),
                					//"paymentdate"=> ,
                					//"userSystem"=> ,
                					"liquidationPercent"=> 0,
                					"liquidationFixed"=> 0,
                					"numberDrivers"=> 0,
                					"amountTravels"=> 0,
                					"amount"=>0
                				];

          if($db["liquidationPercent"] > 0) {//------------Si es por porcentaje

            $liquidation["liquidationPercent"] = $db["liquidationPercent"];
            //$amount = $db["liquidationPercent"]
            //for para sumar los montos de los viajes;

            $query = 
            "sum(".
              " IF(amountPayment IS NULL,".
                " IF( pool = 0 ,".
                  " (totalAmount * pool) ".
                ", totalAmount)".
              ", amountPayment)".
            ") as amount_travel ";

            $travels = $this->db->select($query)->from($db["instancedb"].'.tb_travel')
            ->join($db["instancedb"].".tb_card_payment =","idTravelKf = idTravel","left")
            ->where("idSatatusTravel",6)
            ->group_start()
              ->group_start()
                ->like("dateTravel",$closure)
                ->like("travelStart",$closure)
              ->group_end()
              ->or_group_start()
                ->not_like("dateTravel",$closure)
                ->like("travelStart",$closure)
              ->group_end()
            ->group_end()
            ->get()->row_array()['amount_travel'];
            $liquidation["amount"] = $travels*$db["liquidationPercent"]/100;

          } else if($db["liquidationFixed"] > 0) {//---------Si es por Choferes

            $liquidation["liquidationFixed"] = $db["liquidationFixed"];
					  $ndrivers = $this->db->query("select count(idUser) as ndrivers from ".$db["instancedb"].".tb_user inner join ".$db["instancedb"].".tb_driver on idUserDriver = idUser where idStatusDriver >-1 and idStatusUser >-1")
					                       ->row_array()['ndrivers'];
					  $liquidation["numberDrivers"] = $ndrivers;
            $liquidation["amount"] = $ndrivers*$db["liquidationFixed"];
          }

          if($liquidation["amount"]==0) $liquidation["status"] = 1;

          $response[] = $this->add($liquidation);

				}
				return $response;
    }

}
