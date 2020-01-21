<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Agency_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    // GET DE LISTADO BUSQUEDA DE AGENCYA //
    public function get($id = null,  $searchFilter = null) {
        $quuery = null;
        $this->load->model('Support_model', 'support');

            // SI RECIBIMOS EL ID DEL AGENCYA //
        if (!is_null($id)) {

            $this->db->select("tb_agency.*,tb_clouds.*,tb_country.*,tb_language.*,tb_credit.*")->from("tb_agency");
            $this->db->join('tb_clouds', 'tb_clouds.idCloud = tb_agency.idCloudKf', 'left');
            $this->db->join('tb_credit', 'tb_credit.idCredit = tb_agency.idCreditFk', 'left');
            $this->db->join('tb_country', 'tb_country.idCountry = tb_clouds.idCountryKf', 'left');
            $this->db->join('tb_language', 'tb_language.idLanguage = tb_clouds.idLanguageKf', 'left');
            $this->db->where("tb_agency.idAgency = ", $id)->where("tb_agency.statuAgency =", 1);
           if(!is_null($this->userInfo) && $this->userInfo->idProfileUser == 3) {
             $this->db->join('tb_partner', 'tb_clouds.idCountryKf = tb_partner.idCountryKf', 'inner');
             $this->db->where("tb_partner.idUserKf", $this->userInfo->idUser);
           }
            if (!is_null($searchFilter['searchFilter'])) {
                /*  $this->db->like('tb_user_company.firstNameUserCompany', $searchFilter['searchFilter']);
                $this->db->or_like('tb_user_company.lastNameUserCompany', $searchFilter['searchFilter']);
                $this->db->or_like('tb_user_company.emailUserCompany', $searchFilter['searchFilter']);
                $this->db->or_like('tb_user_company.phoneUserCompany', $searchFilter['searchFilter']);*/
            }

            // Si recibimos un limite //
            if ($searchFilter['topFilter'] > 0) {
                $this->db->limit($searchFilter['topFilter'])->get();
            }

            $quuery = $this->db->order_by("tb_agency.idAgency", "DESC")->get();

            if ($quuery->num_rows() > 0) {
                return $this->support->addCantTickets($quuery->result_array());

  //                $rs = $quuery->row_array();
  //                $rs = array('usercompany' => $quuery->row_array());
  //
  //                $rs['usercompany']['country']['addresUserCompany'] = $rs['usercompany']['addresUserCompany'];
  //                $rs['usercompany']['country']['addresLongUserCompany'] = $rs['usercompany']['addresLongUserCompany'];
  //                $rs['usercompany']['country']['addresLatUserCompany'] = $rs['usercompany']['addresLatUserCompany'];
  //                return $rs;
            }
            return null;
        } else { // SI NO RECIBIMOS EL ID DEL AGENCYA RETORNAMOS TODOS LOS REGISTROS
            $saldoTotal="(select IFNULL(SUM(amount),0) as saldoTotal, idAgencyFk as agency from tb_liquidation_agency where status=0 group by idAgencyFk) as liq";

            $this->db->select("*, IFNULL(`liq`.saldoTotal,0) as saldoTotal")->from("tb_agency");
            $this->db->join('tb_clouds', 'tb_clouds.idCloud = tb_agency.idCloudKf', 'left');
            $this->db->join('tb_credit', 'tb_credit.idCredit = tb_agency.idCreditFk', 'left');
            $this->db->join($saldoTotal, 'liq.agency = tb_agency.idAgency', 'left');
           if(!is_null($this->userInfo) && $this->userInfo->idProfileUser == 3) {
             $this->db->join('tb_partner', 'tb_clouds.idCountryKf = tb_partner.idCountryKf', 'inner');
             $this->db->where("tb_partner.idUserKf", $this->userInfo->idUser);
           }
            /* Busqueda por filtro */
            if (!is_null($searchFilter['searchFilter'])) {
                $this->db->or_like('nameAgency', $searchFilter['searchFilter']);
                $this->db->or_like('phoneAgnecy', $searchFilter['searchFilter']);
                $this->db->or_like('mailAgency', $searchFilter['searchFilter']);
            }
            // Si recibimos un limite //
            if ($searchFilter['topFilter'] > 0) {
                $this->db->limit($searchFilter['topFilter']);
            }
            $this->db->where("tb_agency.statuAgency = ", 1);

            $quuery = $this->db->order_by("tb_agency.idAgency", "DESC")->get();


            if ($quuery->num_rows() > 0) {
                $rs =  $this->support->addCantTickets($quuery->result_array());
                $rs = $this->usersOnline($rs);

                return $rs;
            }
            return null;
        }
    }

//     SELECT * FROM `tb_user_ex` 
// JOIN tb_profile ON tb_profile.idProfile = tb_user_ex.idProfileUser
// WHERE 1
    private function usersOnline($list){
/*
 select 
 sum(if(idProfileUser = 3,1,0)) as totaldrivers, 
 sum(if(idProfileUser = 2,1,0)) as totalclients, 
 sum(if(idProfileUser = 3 and idResourceSocket != '' and idResourceSocket is not NULL, 1,0)) as activeDriver, 
 sum(if(idProfileUser = 2 and idResourceSocket != '' and idResourceSocket is not NULL, 1,0)) as activeclients 

from tb_user as u 
left join tb_driver as d on d.idUserDriver = u.idUser 
where 
idProfileUser in (2,3) and
idStatusUser>-1 and 
(idStatusDriver>-1 or
idStatusDriver is NULL);
*/
        foreach ($list as $index => $instance) {
          $result = $this->db->select([
 "sum(if(idProfileUser = 2 and idStatusUser > -1 ,1,0)) as totalclients ",
 "sum(if(idProfileUser = 2 and idStatusUser = 1 and idResourceSocket != '' and idResourceSocket is not NULL, 1,0)) as activeclients ",
                                                      //idStatusDriver
 "sum(if(idProfileUser = 3 and idStatusUser > -1  and idStatusDriverTravelKf > -1 ,1,0)) as totaldrivers ",
 "sum(if(idProfileUser = 3 and idStatusUser = 1  and idStatusDriverTravelKf = 1 and idResourceSocket != '' and idResourceSocket is not NULL, 1,0)) as activeDriver ",
          															])
          					 ->from($instance["instancedb"].".tb_user as u")
            				 ->join($instance["instancedb"].".tb_driver as d", 'd.idUserDriver = u.idUser', "left")
            				 ->where_in("idProfileUser", [2,3])
            				 ->where("idStatusUser >", -1)
            				 ->group_start()//idStatusDriver
            				 ->where("idStatusDriverTravelKf >", -1)
            				 ->or_where("idStatusDriverTravelKf is NULL",null,false)
            				 ->group_end()
            				 ->get();

					if(!$result) {
						$list[$index]["totaldrivers"] = 0;
						$list[$index]["activeDriver"] = 0;
						$list[$index]["totalclients"] = 0;
						$list[$index]["activeclients"] = 0;
					}else {
            $result = $result->row_array();
						$list[$index]["totaldrivers"] = empty($result["totaldrivers"])?0:$result["totaldrivers"];
						$list[$index]["activeDriver"] = empty($result["activeDriver"])?0:$result["activeDriver"];
						$list[$index]["totalclients"] = empty($result["totalclients"])?0:$result["totalclients"];
						$list[$index]["activeclients"] = empty($result["activeclients"])?0:$result["activeclients"];
					}
        }
        return $list;
    }

    public function seacrhAgency($id = null,  $searchFilter = null) {
        $quuery = null;
        $this->load->model('Support_model', 'support');

            // SI RECIBIMOS EL ID DEL AGENCYA //
        if (!is_null($id)) {

            $this->db->select("*")->from("tb_agency");
            $this->db->join('tb_clouds', 'tb_clouds.idCloud = tb_agency.idCloudKf', 'left');
            $this->db->join('tb_credit', 'tb_credit.idCredit = tb_agency.idCreditFk', 'left');
            $this->db->join('tb_country', 'tb_country.idCountry = tb_clouds.idCountryKf', 'left');
            $this->db->join('tb_language', 'tb_language.idLanguage = tb_clouds.idLanguageKf', 'left');
            $this->db->where("tb_agency.idAgency = ", $id)->where("tb_agency.statuAgency =", 1);

            if (!is_null($searchFilter['searchFilter'])) {
                /*  $this->db->like('tb_user_company.firstNameUserCompany', $searchFilter['searchFilter']);
                $this->db->or_like('tb_user_company.lastNameUserCompany', $searchFilter['searchFilter']);
                $this->db->or_like('tb_user_company.emailUserCompany', $searchFilter['searchFilter']);
                $this->db->or_like('tb_user_company.phoneUserCompany', $searchFilter['searchFilter']);*/
            }

            // Si recibimos un limite //
            if ($searchFilter['topFilter'] > 0) {
                $this->db->limit($searchFilter['topFilter'])->get();
            }

            $quuery = $this->db->order_by("tb_agency.idAgency", "DESC")->get();

            if ($quuery->num_rows() > 0) {
                return $this->support->addCantTickets($quuery->result_array());

            }
            return null;
        } else { // SI NO RECIBIMOS EL ID DEL AGENCYA RETORNAMOS TODOS LOS REGISTROS
            $saldoTotal="(select IFNULL(SUM(a.abono),0) from tb_credit_agency as a where  a.status=0  and a.idAgencyFk=tb_agency.idAgency) as saldoTotal";   
            $this->db->select("*,".$saldoTotal."")->from("tb_agency");
            $this->db->join('tb_clouds', 'tb_clouds.idCloud = tb_agency.idCloudKf', 'left');
            $this->db->join('tb_credit', 'tb_credit.idCredit = tb_agency.idCreditFk', 'left');

            /* Busqueda por filtro */
            if (!is_null($searchFilter['searchFilter'])) {
                $this->db->or_like('nameAgency', $searchFilter['searchFilter']);
                $this->db->or_like('phoneAgnecy', $searchFilter['searchFilter']);
                $this->db->or_like('mailAgency', $searchFilter['searchFilter']);
            }
            // Si recibimos un limite //
            if ($searchFilter['topFilter'] > 0) {
                $this->db->limit($searchFilter['topFilter']);
            }

            if (!is_null($searchFilter['idAgencyFilter'])) {
                $this->db->where("tb_agency.idAgency = ", $searchFilter['idAgencyFilter']);
            }


            $quuery = $this->db->where("tb_agency.statuAgency =", 1)->order_by("tb_agency.idAgency", "DESC")->get();


            if ($quuery->num_rows() > 0) {
                $rs =  $this->support->addCantTickets($quuery->result_array());



                return $rs;
            }
            return null;
        }
    }


    /* EDITAR DATOS DE UN CLIENTE */
    public function update($client) {

        $this->db->set(
            array(
                'phoneAgnecy' => $client['phoneAgnecy'],
                'mailAgency' => $client['mailAgency'],
                'idCloudKf' => $client['idCloudKf'],
                'idCreditFk' => $client['idCreditFk'],
                'liquidationPercent' => $client['idCreditFk'] == 1 ? $client['liquidationPercent'] : 0,
                'liquidationFixed' => $client['idCreditFk'] == 2 ? $client['liquidationFixed'] : 0,
                // 'modificationPermissions' => $client['modificationPermissions']
            )
        )->where("idAgency", $client['idAgency'])->update("tb_agency");

        if ($this->db->trans_status() === true)
        {
            return true;
        }
        else
        {
            return null;
        }
    }
    public function delete($id) {
            $this->db->set(['statuAgency' => 0])->where("idAgency", $id)->update("tb_agency");
            return $this->db->trans_status() ? true : null;
    }
    public function updatePayment($id = null) {

        $status = 0;
        if (!is_null($id)) {
            $this->db->set(
                array(
                    'statusPayment' => $status,
                )
            )->where("idAgency", $id)->update("tb_agency");

            if ($this->db->trans_status() === true)
            {
                
                return true;
            }
            else
            {
                return null;
            }
        }
        return null;
    }

    public function updatePayment2($id = null) {

        $status = 1;
        if (!is_null($id)) {
            $this->db->set(
                array(
                    'statusPayment' => $status,
                )
            )->where("idAgency", $id)->update("tb_agency");

            if ($this->db->trans_status() === true)
            {

                return true;
            }
            else
            {
                return null;
            }
        }
        return null;
    }


    /* LISTADO DE FILTROS */
    public function  getFilterForm()
    {
        $clouds = null;
        /* LISTADO DE RESPOSNABLES */
        $query = $this->db->select("*")->from("tb_clouds")->where("idStatuCloud !=",-1)->get();
        if($query->num_rows() > 0){
            $clouds = $query->result_array();
        }

        $filter = array(
            'clouds' => $clouds
        );

        return $filter;
    }

    // RESUMEN DE LAS AGENCIAS POR PAIS
    public function reportAgencyByContry()
    {
        $countrys = null;

        $query = $this->db->query("
            SELECT t1.hcKey,
            (
            SELECT COUNT(*)FROM
            tb_clouds
            LEFT JOIN tb_agency 
            ON (tb_clouds.idCloud = tb_agency.idCloudKf)
            WHERE tb_clouds.idCountryKf = t1.idCountry
            ) AS value
            FROM
            tb_country AS t1"
        );

        if($query->num_rows() > 0){
            $countrys = $query->result_array();
        }

        return $countrys;
    }

    // AGENCIAS POR PAIS
    public function agencyByContry($hcKey)
    {
        $countrys = null;

        $query = $this->db->query("
            SELECT tb_agency.*,
            (SELECT idConnection FROM tb_connection_agency WHERE idAgencyKf1 = 1 AND idAgencyKf2 = idAgency) AS idConnection,
            (SELECT isConnection FROM tb_connection_agency WHERE idAgencyKf1 = 1 AND idAgencyKf2 = idAgency) AS connecty
            FROM
            tb_agency
            LEFT JOIN  tb_clouds
            ON (tb_clouds.idCloud = tb_agency.idCloudKf)
            LEFT JOIN db_asnube.tb_country 
            ON (tb_clouds.idCountryKf = tb_country.idCountry)
            WHERE hcKey = '".$hcKey."'
            "
        );

        if($query->num_rows() > 0){
            $countrys = $query->result_array();


        }

        return $countrys;
    }

    /* EDITAR DATOS DE UN CLIENTE */
    public function changeConnect($data) {


        if (isset($data['idConnection'])) {

            $this->db->set(
                array(
                    'idAgencyKf1' => $data['idAgencyKf1'],
                    'idAgencyKf2' => $data['idAgencyKf2'],
                    'isConnection' => $data['isConnection']
                )
            )->where("idConnection", $data['idConnection'])->update("tb_connection_agency");

            if ($this->db->trans_status() === true){
                return true;
            }else{
                return false;
            }
            
        }else{
            $this->db->insert('tb_connection_agency',
                array(
                    'idAgencyKf1' => $data['idAgencyKf1'],
                    'idAgencyKf2' => $data['idAgencyKf2'],
                    'isConnection' => $data['isConnection']
                ));

            if($this->db->affected_rows() === 1){
                return  $this->db->insert_id();
            }else{
                return null;
            }
        }       
    }



    public function add($agency) {
        //Se valida si existe una agencia con el msimo nombre
        $this->db->select("*")->from("tb_agency");
        $query =$this->db->where("tb_agency.nameAgency = ", @$agency['nameAgency'])->get();
        if ($query->num_rows() > 0) {
            return false;
        }else{

            $this->db->set($this->_setAgency($agency))->insert("tb_agency");
            if ($this->db->affected_rows() === 1) {
                //Se crea una carpeta con el nombre de la agencia
                //$serv = $_SERVER['DOCUMENT_ROOT']."/";
                //$ruta = $serv.@$agency['nameAgency'];
                //if(!file_exists($ruta)){
                  //  mkdir ($ruta);
                //} 

                $id = $this->db->insert_id();
                return $id;
            } else {
                return null;
            }

        }
    }
    
    
    private function _setAgency($agency) {
        return array(
            'nameAgency' => @$agency['nameAgency'],
            'statuAgency' => @$agency['statusAgency'],
            'idPlan' => 1,
            'phoneAgnecy' => @$agency['phoneAgnecy'],
            'mailAgency' => @$agency['mailAgency'],
            'idCloudKf' => @$agency['idCloudKf'],
            'idCreditFk' => @$agency['idCreditFk'],
            'statusFolder'=>0,
            'statusRevision' => @$agency['statusRevision'],
            'instancedb' => empty($agency['instancedb']) ? "db_asremis_".$agency['nameAgency'] : $agency['instancedb'],
            'recalculate' => isset($agency['recalculate']) ? $agency['recalculate'] : 0,
            'liquidationPercent' => $agency['idCreditFk'] == 1 ? $agency['liquidationPercent'] : 0,
            'liquidationFixed' => $agency['idCreditFk'] == 2 ? $agency['liquidationFixed'] : 0,
        );
    }
    

    public function getCreditAgency($id = null) {
        $query = null;
        $sum=null;
        $creditAgency=null;
        $saldoAgency=null;
        $agency=null;

        if (!is_null($id)) {
            $query = $this->db->select("*, if(liquidationPercent=0, 'Por Chofer', 'Por Porcentaje') as nameCredit")
                     ->from("tb_liquidation_agency")->order_by("idLiquidation", "DESC")
                     ->where("idAgencyFk =", $id)->get();


            if ($query->num_rows() > 0) {
             $creditAgency=$query->result_array();
         }

         $this->db->select("*, if(liquidationPercent=0, 'Por Chofer', 'Por Porcentaje') as  nameCredit")->from("tb_agency");
         $query = $this->db->where("idAgency =",$id)->get();

         if ($query->num_rows() > 0) {
            $agency = $query->result_array();
        }

        $sum="SUM(amount) AS saldoTotal";
        $this->db->select($sum)->from("tb_liquidation_agency");
        $this->db->where("idAgencyFk = ", $id);
        $query = $this->db->where("status= ", 0)->get();


        if ($query->num_rows() > 0) {
         $saldoAgency=$query->result_array();
     }

     $filter = array(
        'creditAgency' => $creditAgency,
        'saldoAgency' => $saldoAgency,
        'agency' => $agency
    );
     return $filter;
 }else{

    return null;
}
}

public function ingresarCobro($credit) {

    $this->db->set(
        array(
            'status' =>1,
            'userName'=>$credit['userNameUser'],
            'paymentdate'=>date("Y-m-d") //$credit['fechaCobro']
        )
    )->where("idLiquidation", $credit['idLiquidation'])->update("tb_liquidation_agency");

    if ($this->db->trans_status() === true)
    {
        return true;
    }
    else
    {
        return null;
    }
}

public function getStatusCreditAgency($id){
    $query = null;
    $sum=null;


    $sum="SUM(tb_credit_agency.abono) AS saldoTotal";
    $this->db->select($sum)->from("tb_credit_agency");
    $this->db->where("tb_credit_agency.idAgencyFk= ", $id);
    $query = $this->db->where("tb_credit_agency.status= ", 0)->get();


    if ($query->num_rows() > 0) {
     $saldoAgency=$query->result_array();
     if($saldoAgency[0]["saldoTotal"]!=null){
        $saldoAgency[0]["statusSaldo"]=1;
    }else{
        $saldoAgency[0]["saldoTotal"]=0;
        $saldoAgency[0]["statusSaldo"]=0;
    }
}

$filter = array(
    'saldoAgency' => $saldoAgency,
);
return $filter;

}

public function cardPayment(){
    $query = null;
    $sum=null;

    $query = $this->db->select('*')->from("tb_agency")->get();

    $data = array();
    if ($query->num_rows() > 0) {
        $agencys=$query->result_array();

        foreach ($agencys as $agency) {
            $headers = array ( 'Content-Type: application/json' );

            $param = array(
                'filter' => [
                    "idPaymentFormAsigned" => 3,
                    "searchFilter" => null,
                    "topFilter" => 0
                ]
            );

            $uri = URI_BASE_SERVE.$agency['nameAgency'].'/Api/ingress/search';

            $ch = curl_init();
            curl_setopt( $ch,CURLOPT_URL, $uri );
            curl_setopt( $ch,CURLOPT_POST, true );
            curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
            curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
            curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
            curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $param ) );
            $result = curl_exec($ch);

            curl_close( $ch );

            $ingress = json_decode($result, true);

            $total = 0;
            if (!is_null($ingress)) {
                foreach ($ingress as $i) {
                    $total+=$i['amountBox'];
                }
            }

            $data[]=[
                'agency'=>$agency,
                'cardPayments' => $ingress,
                'totalPayments' => $total 
            ];
        }

    }

    return $data;
}


}
?>
