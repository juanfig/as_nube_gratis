<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Usersystem_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    // GET DE LISTADO BUSQUEDA DE USUARIO //
    public function get($id = null, $searchFilter = null) {
        $quuery = null;

        // SI RECIBIMOS EL ID DE EL USUARIO //
        if (!is_null($id)) {
            $this->db->select("*")->from("tb_users");

            $this->db->join('tb_profile_system', 'tb_profile_system.idProfile = tb_users.idProfileUser', 'left');
           if($this->userInfo->idProfileUser == 3 && $this->userInfo->idUser != $id) {
              $this->db->where("parentUserKf", $this->userInfo->idUser);
           }
           // $this->db->where("tb_users.idStatusUser !=", -1);
            $quuery = $this->db->where("tb_users.idUser = ", $id)->get();

            if ($quuery->num_rows() === 1) {
                $rs = array('usersystem' => $quuery->row_array());
                return $rs;
            }
            return null;
        } else { // SI NO RECIBIMOS EL ID DE EL CLIENTE RETORNAMOS TODOS LOS REGISTROS 
            $this->db->select("*")->from("tb_users");
            $this->db->join('tb_profile_system', 'tb_profile_system.idProfile = tb_users.idProfileUser', 'left');
           if($this->userInfo->idProfileUser == 3 && $this->userInfo->idUser != $id) {
              $this->db->where("parentUserKf", $this->userInfo->idUser);
              $this->db->or_where("idUser", $this->userInfo->idUser);
           }
            /* Busqueda por filtro */
            if (!is_null($searchFilter['searchFilter'])) {

                $this->db->or_like('tb_users.userNameUser', $searchFilter['searchFilter']);
                $this->db->or_like('tb_users.emailUser', $searchFilter['searchFilter']);
            }


            // Si recibimos un limite //
            if ($searchFilter['topFilter'] > 0) {
                $this->db->where("tb_users.idStatusUser !=", -1);
                $this->db->limit($searchFilter['topFilter']);
            } else {
               // $this->db->where("tb_user.idStatusUser !=", -1);
            }

            $quuery = $this->db->order_by("tb_users.idUser", "DESC")->get();


            if ($quuery->num_rows() > 0) {
                return $quuery->result_array();
            }
            return null;
        }
    }

    /* AGRAGR NUEVO USUARIO EMPRESA */

    public function add($user) {

        /* CREAMOS UN USUARIO PARA ESE CLIENE */
        $this->db->insert('tb_user', array(
            'userNameUser' => $user['emailUser'],
            'firstNameUser' => $user['firstNameUser'],
            'lastNameUser' => $user['lastNameUser'],
            'emailUser' => $user['emailUser'],
            'passUser' => sha1(md5($user['passUser'])),
            'idProfileUser' => $user['idProfileUser']
                )
        );

        if ($this->db->affected_rows() === 1) {
            $idUser = $this->db->insert_id();
            /*if ($user['idProfileUser'] == 6) {
                $this->db->set($this->_setUserSystem($user, $idUser))->insert("tb_user_system");
                $idUserSystem = $this->db->insert_id();
            }*/
            return $idUser;
        } else {
            return null;
        }
    }

    private function _setUserSystem($user, $idUser) {

        return array(
            'phoneUserSystem' => @$user['phoneUserSystem'],
            'addresUserSystem' => @$user['country']['addresUserSystem'],
            'addresLongUserSystem' => @$user['country']['addresLongUserSystem'],
            'addresLatUserSystem' => @$user['country']['addresLatUserSystem'],
            'idUserKf' => $idUser,
        );
    }

    /* LISTADO DE FILTROS */

    public function getFilterForm() {

        $empresa = null;
        $profile = null;
        $banckacount = null;
        $companyacount = null;
        $user = null;


        /* LISTADO DE EMPRESAS ACTIVAS */
        $this->db->select("*")->from("tb_client");
        $this->db->join('tb_company', 'tb_company.idClientKf = tb_client.idClient', 'left');
        $query = $this->db->where("tb_client.idStatusClient !=", -1)->get();

        if ($query->num_rows() > 0) {
            $empresa = $query->result_array();
        }


        $query = $this->db->select("*")->from("tb_bank_acount")->where("idStatusBanckAcount !=", -1)->get();
        if ($query->num_rows() > 0) {
            $banckacount = $query->result_array();
        }
        $this->db->select("*")->from("tb_user");
        $query = $this->db->where("tb_user.idStatusUser !=", -1)->get();
        if ($query->num_rows() > 0) {
            $user = $query->result_array();
        }

        $query = $this->db->query(" SELECT * FROM `tb_profile` WHERE idProfile in (1,6)");
        if ($query->num_rows() > 0) {
            $profile = $query->result_array();
        }


        $filter = array(
            'profile' => $profile,
            'empresa' => $empresa,
            'banckacount' => $banckacount,
            'user' => $user,
            'companyacount' => $companyacount
        );

        return $filter;
    }

    /* EDITAR DATOS DE UN EMPRESA */

    public function update($user) {

        $data =[
                'userNameUser' => $user['emailUser'],
                'firstNameUser' => $user['firstNameUser'],
                'lastNameUser' => $user['lastNameUser'],
                'emailUser' => $user['emailUser'],
                'idProfileUser' => $user['idProfileUser']
                ];
        if(empty($user['idProfileUser'])) unset($data['idProfileUser']);

        $this->db->set($data)->where("idUser", $user['idUser'])->update("tb_user");

        if ($user['isEditUser']) {

            $this->db->set(
                    array(
                        'passUser' => sha1(md5($user['passUser']))
                    )
            )->where("idUser", $user['idUser'])->update("tb_user");
        }

        if ($user['idProfileUser'] == 6) {
            $this->db->set($this->_setUserSystem($user, $user['idUser']))->where("idUserKf", $user['idUser'])->update("tb_user_system");
        } 
        
        if ($this->db->affected_rows() === 1) {
            return true;
        } else {
            return null;
        }
    }

    public function changueStatus($id, $idStatus) {
        $this->db->set(
                array(
                    'idStatusUser' => $idStatus
                )
        )->where("idUser", $id)->update("tb_user_ex");


        if ($this->db->affected_rows() === 1) {
            return true;
        } else {
            return null;
        }
    }


    public function addUser($user) {

        $this->db->insert('tb_users',array(
            'userNameUser' => $user['userNameUser'],
            'emailUser' => $user['emailUser'],
            'passUser' => sha1(md5($user['passUser'])),
            'parentUserKf' => $this->userInfo->idUser,
            'idProfileUser' => $this->userInfo->idProfileUser == 1 ?
                $user['idProfileUser'] : ($user['idProfileUser']>3 ? $user['idProfileUser'] : 4)
                )
        );

        //$this->db->set($this->_setUser($user))->insert("tb_users");
        if ($this->db->affected_rows() === 1) {

                $id = $this->db->insert_id();
                return $id;
        } else {
                return null;
        }

        
    }
    
    
    public function _setUser($user) {
        return array(
            'userNameUser' => @$user['userNameUser'],
            'emailUser' => @$user['emailUser'],
            'passUser' => @$user['passUser'],
            'idProfileUser' => @$user['idProfileUser'] 
            );
    }

    public function changueStatusUser($user) {
        $this->db->set(
            array(
                'idStatusUser' => $user['idStatusUser']
                )
            )->where("idUser", $user['idUser']);

           if($this->userInfo->idProfileUser == 3 && $this->userInfo->idUser != $user['idUser']) {
              $this->db->where("parentUserKf", $this->userInfo->idUser);
           }
        $this->db->update("tb_users");

        if ($this->db->affected_rows() === 1) {
            return true;
        } else {
            return null;
        }
    }




    public function updateUser($user) {
        $data = [];
        $data['userNameUser'] = $user['userNameUser'];
        $data['emailUser'] = $user['emailUser'];

       if($this->userInfo->idProfileUser == 1) {
            $data['idProfileUser'] = $user['idProfileUser'];
       }elseif($this->userInfo->idProfileUser == 3) {
         if($this->userInfo->idUser != $user['idUser']) {
            $data['idProfileUser'] = $user['idProfileUser']>3 ? $user['idProfileUser'] : 4;
            $this->db->where("parentUserKf", $this->userInfo->idUser);
          }
       }

        $this->db->set($data)->where("idUser", $user['idUser'])->update("tb_users");


        if ($user['isEditUser']) {

            $this->db->set(
                    array(
                        'passUser' => sha1(md5($user['passUser']))
                    )
            )->where("idUser", $user['idUser'])->update("tb_users");
        }

        if ($this->db->affected_rows() === 1) {
            return true;
        } else {
            return null;
        }
    }

    public function getTypeName($name)
    {
        if (!is_null($name)) {
            $this->db->select("*")->from("tb_users");

            $this->db->join('tb_profile_system', 'tb_profile_system.idProfile = tb_users.idProfileUser', 'left');

           // $this->db->where("tb_users.idStatusUser !=", -1);
            $quuery = $this->db->where("tb_profile_system.profileName = ", $name)->get();

            if ($quuery->num_rows() > 0) {
                return $quuery->result_array();
            }
            return null;
        }
    }

    private function getInstancesDbs($globe) {

			$this->db->select("instancedb")->from("tb_agency");
           if($this->userInfo->idProfileUser == 3) {
             $this->db->join('tb_clouds', 'tb_clouds.idCloud = tb_agency.idCloudKf', 'inner')
                      ->join('tb_partner', 'tb_clouds.idCountryKf = tb_partner.idCountryKf', 'inner')
                      ->where("tb_partner.idUserKf", $this->userInfo->idUser);
           }

      if(!empty($globe)) {
        $this->db->where("tb_agency.globeMobility =", $globe == 1?0:1);
      }

			$query = $this->db->where("tb_agency.statuAgency =", 1)->get();
			return  $query->result_array();
    }

    public function dashboard($get = null)
    {
        $dashboard = array();

      $join='where';
           if($this->userInfo->idProfileUser == 3) {
             $join ='inner join tb_clouds on tb_clouds.idCloud = tb_agency.idCloudKf '.
                    'inner join tb_partner on tb_clouds.idCountryKf = tb_partner.idCountryKf '.
                    'where idUserKf ='.$this->userInfo->idUser.' and ';
           }

           $globe = '';
          if(!empty($get) && $this->userInfo->idProfileUser == 1) {
            $globe = ' globeMobility = '.($get == 1?0:1).' and ' ;
          } else $get = null;
//Agencias
				$instances = $this->db->query("select (select count(idAgency) as num  from tb_agency ".$join." ".$globe." statuAgency = 1) as todos".
				" ,(select count(idAgency) as num  from tb_agency ".$join." ".$globe." statusPayment = 1  and statuAgency = 1) as activos".
				", (select count(idAgency) as num  from tb_agency ".$join." ".$globe." statusPayment = 0  and statuAgency = 1) as inactivos");
				$instances = $instances->row_array();
        $dashboard['total_agencies'] = $instances["todos"];
        $dashboard['agency_active'] = $instances["activos"];
        $dashboard['agency_inactive'] = $instances["inactivos"];
//Agencias

				$instances = $this->getInstancesDbs($get);

         if(!count($instances)) {
    				$dashboard['users'] = 0;
    				$dashboard['drivers'] = 0;
    				$dashboard['drivers_online'] = 0;
    				$dashboard['clients'] = 0;
    				$dashboard['clionline'] = 0;
    				$dashboard['companies'] = 0;
      			$dashboard['total'] = 0;
      			$dashboard['actives'] = 0;
      			$dashboard['suspended'] = 0;
            return $dashboard;
        }

      $result=null;
      if(count($instances)<62) {
        $result = $this->dashboardInfo($instances);
      } else {

        $instances = array_chunk($instances, 60);

        if(count($instances[count($instances)-1]) == 1 ) {
          $instances[count($instances)-2] = array_merge($instances[count($instances)-2],$instances[count($instances)-1]);
          unset($instances[count($instances)-1]);
        }
				$result['users'] = 0;
				$result['drivers'] = 0;
				$result['actives'] = 0;
				$result['clients'] = 0;
				$result['clionline'] = 0;
				$result['companies'] = 0;
          foreach($instances as $i) {
            $res = $this->dashboardInfo($i);
    				$result['users'] += $res['users'];
    				$result['drivers'] += $res['drivers'];
    				$result['actives'] += $res['actives'];
    				$result['clients'] += $res['clients'];
    				$result['clionline'] += $res['clionline'];
    				$result['companies'] += $res['companies'];
          }
      }

				$dashboard['users'] = $result['users'];
				$dashboard['drivers'] = $result['drivers'];
				$dashboard['drivers_online'] = $result['actives'];
				$dashboard['clients'] = $result['clients'];
				$dashboard['clionline'] = $result['clionline'];
				$dashboard['companies'] = $result['companies'];
date_default_timezone_set('UTC');
			$this->db->select([
			"IFNULL(sum(amount),0) as total",
			"IFNULL(sum(if(statusPayment = 1,amount,0)),0) as actives",
			"IFNULL(sum(if(statusPayment = 0,amount,0)),0) as suspended"
			])->from("tb_liquidation_agency")
				->join('tb_agency', 'idAgency = idAgencyFk', 'inner')
				->where("statuAgency", 1)
				->where("monthClosure like", date("Y-m-%", strtotime("previous month")));

           if($this->userInfo->idProfileUser == 3) {
             $this->db->join('tb_clouds', 'tb_clouds.idCloud = tb_agency.idCloudKf', 'inner')
                      ->join('tb_partner', 'tb_clouds.idCountryKf = tb_partner.idCountryKf', 'inner')
                      ->where("tb_partner.idUserKf", $this->userInfo->idUser);
           }
      if(!is_null($get)) {
        $this->db->where("tb_agency.globeMobility =", $get == 1?0:1);
      }
      $result = $this->db->get();
			$result = $result->row_array();

			$dashboard['total'] = $result['total'];
			$dashboard['actives'] = $result['actives'];
			$dashboard['suspended'] = $result['suspended'];

/*
			$result = $this->db->select([
			"IFNULL(sum(amount),0) as acumulated",
			"IFNULL(sum(if(statusPayment = 0,amount,0)),0) as unpaid",
			"IFNULL(sum(if(statusPayment = 1,amount,0)),0) as outpaid"
			])->from("tb_liquidation_agency")
												->join('tb_agency', 'idAgency = idAgencyFk', 'inner')
												->where("status", 0)
												->where("statuAgency", 1)->get();
			$result = $result->row_array();

			$dashboard['acumulated'] = $result['acumulated'];
			$dashboard['unpaid'] = $result['unpaid'];
			$dashboard['outpaid'] = $result['outpaid'];
*/
        if (count($dashboard>0)) {
            return $dashboard;
        }



        return null;
    }

    private function dashboardInfo($instances) {
      //MySQL can only use 61 tables in a join

				$n=1;
				$usr = '(';
				$cli = '(';
				$onlcli = '(';
				$drv = '(';
				$act = '(';
				$com = '(';
				$from = ' from ';

				foreach($instances as $db){
					if(!in_array($db,['db_asremis_developer','db_asremis_TESTING'])) {//omitir developer y testing

						//consulta de choferes totales
						$usr.= ($n>1?" + ":"")."t".$n.".usr ";
						$cli.= ($n>1?" + ":"")."t".$n.".cli ";
						$onlcli.= ($n>1?" + ":"")."t".$n.".onlcli ";
						$drv.= ($n>1?" + ":"")."t".$n.".drv ";
						$act.= ($n>1?" + ":"")."t".$n.".act ";
						$com.= ($n>1?" + ":"")."t".$n.".com ";

						$from.= ($n>1?" join ":"").
						 "(select count(idUser) as usr, ".
						 "sum( IF(idProfileUser = 2 and idStatusUser > -1, 1, 0)) as cli, ".
						 "sum( IF(idProfileUser = 2 and idStatusUser=1 and idResourceSocket is not NULL and idResourceSocket != '', 1, 0)) as onlcli, ".
                                                                  //idStatusDriver
						 "sum( IF(idProfileUser = 3 and idStatusUser > -1 and idStatusDriverTravelKf > -1 , 1, 0)) as drv, ".
						 "sum( IF(idProfileUser = 3 and idStatusUser=1 and idStatusDriverTravelKf = 1 and idResourceSocket is not NULL and idResourceSocket != '', 1, 0)) as act, ".

						 "sum( IF(idProfileUser = 4, 1, 0)) as com ".
						 "from ".$db["instancedb"].".tb_user a1 ".
						 "left join ".$db["instancedb"].".tb_driver a2 on idUserDriver = idUser where idStatusUser > -1) as t".$n." ";

						$n++;
					}
				}

 				$result = "select ".
 						$usr.") as  users, ".
						$cli.") as  clients, ".
						$onlcli.") as clionline, ".
						$drv.") as  drivers, ".
						$act.") as  actives, ".
						$com.") as  companies ".
						$from;
				$dashboard['companies'] = $result;
				$result = $this->db->query($result);
				return $result->row_array();
    }
}
