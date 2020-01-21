<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// API access key from Google API's Console
	define( 'API_ACCESS_KEY', 'AAAAaJmqTcE:APA91bGeFWhf0cgIbGiHcp3z2_Dzntn9T4zTke5ii5sWKWoVgugh5g9o6aTz4-PWp5PjbfWIXY0sXc8OrlvTxjObVtDBmtfolrOGAEnAlV5jt7rpIIaKnttSDuxkN_Nj5L9CHdLdUX4E' );
	date_default_timezone_set("America/Argentina/Buenos_Aires");

  require APPPATH . '/libraries/vendor/autoload.php';
	use WebSocket\Client;


class Travel_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
	}


	public function getClientByName($searchFilter)
	{
			$this->db->select("*")->from("tb_client");
		 	$this->db->join('tb_condicion_type', 'tb_condicion_type.idCondicionType = tb_client.idCondicionTypeClient', 'left');
			$this->db->join('tb_type_car', 'tb_type_car.idTypeCar = tb_client.idTypeCarClient', 'left');
			$this->db->join('tb_company', 'tb_company.idClientKf = tb_client.idClient', 'left');
			$this->db->join('tb_user', 'tb_user.idUser = tb_client.idClient', 'left');
			$this->db->join('tb_type_client', 'tb_type_client.idTypeClient = tb_client.idTypeClient', 'left');
			$this->db->join('tb_status_client', 'tb_status_client.idStatusClient = tb_client.idStatusClient', 'left');


			/*Busqueda por filtro */


			if( ! is_null($searchFilter['name']))
			{
			   $this->db->where("(  tb_client.firtNameClient like '%".$searchFilter['name']."%' OR tb_client.lastNameClient like '%".$searchFilter['name']."%') OR tb_company.nameClientCompany like '%".$searchFilter['name']."%' ");

			}

			$this->db->where("tb_client.idStatusClient !=",-1);

			$isTravelComany = json_encode($searchFilter['isTravelComany']);



			if($isTravelComany === 'true')
			{
				$this->db->where("tb_client.idTypeClient",2);
			}else if($isTravelComany === 'false')
			{
				$this->db->where("tb_client.idTypeClient",1);
			}


			$quuery = $this->db->order_by("tb_client.idStatusClient", "asc")->get();


			if($quuery->num_rows() > 0)
			{
				return $quuery->result_array();
			}
			return null;
	}


	public function findDriverHeader($idClient = null)
	{
		$rs = null;

			$query = $this->db->query(" SELECT
				t1.codTravel,
				t1.distanceLabel,
				t1.durationLabel,
				t1.totalAmount,
				t1.dateCreatedTravel,
				t1.nameOrigin,
				t1.idTravel,
				t1.nameDestination,
				concat(t2.fisrtNameDriver,' ',IFNULL(t2.lastNameDriver,'')) driver,
				 case idTypeClient
					when 1 then concat(t3.firtNameClient,' ',t3.lastNameClient)
					when 2 then t4.nameClientCompany 	END as client
				 FROM `tb_travel` t1
				left join tb_driver t2 on t1.idDriverKf = t2.idDriver
				LEFT join tb_client t3 on t3.idClient=t1.idClientKf
				LEFT join tb_company t4 on t4.idClientKf = t1.idClientKf
				WHERE t1.idTravel = ".$idClient."
				order by t1.dateTravel desc
				");

			if ($query->num_rows() === 1) {
				$rs = $query->result_array();
			}

		return $rs;
	}


	/* LISTADO DE FILTROS */
	public function  getFilterForm()
	{

		$risposable = null;
		$typevehicle = null;
		$drivers = null;
		$aribos = null;
		$hoteles = null;
		$typetravel = null;
		$preTravel = null;
		$listLanguajes = null;

		/* LISTADO DE RESPOSNABLES */
		$query = $this->db->select("idUser,userNameUser,firstNameUser,lastNameUser,emailUser")->from("tb_user")->where("idStatusUser !=",-1)->get();
		if($query->num_rows() > 0){
				$risposable = $query->result_array();
			}



		/* LISTADO DE TYPOS DE VEIHICULOS*/
		$query = $this->db->select("*")->from("tb_vehicle_type")->get();
		if($query->num_rows() > 0){
				$typevehicle = $query->result_array();
			}



		/* LISTADO CONDUCTORES  */
		$query = $this->db->query(" SELECT *  from tb_driver where idStatusDriver != -1 and idDriver not in (SELECT idDriverKf FROM tb_travel where idSatatusTravel in(2,4,5) group by idDriverKf)");

		if($query->num_rows() > 0){
				$drivers = $query->result_array();
			}

			/* LISTADO HOTELES  */
		$query = $this->db->select("*")->from("tb_hotel")->where("idStatusHotel !=",-1)->get();
		if($query->num_rows() > 0){
				$hoteles = $query->result_array();
			}



		/* LISTADO ARRIBOS  */
		$query = $this->db->select("*")->from("tb_aribos")->get();
		if($query->num_rows() > 0){
				$aribos = $query->result_array();
			}


		/* LISTADO TIPOS DE VIAJES  */
		$query = $this->db->select("*")->from("tb_type_travel")->get();
		if($query->num_rows() > 0){
				$typetravel = $query->result_array();
			}

		/* LISTADO PRE DESTINOS  */
		$query = $this->db->select("*")->from("tb_pre_destination")->get();
		if($query->num_rows() > 0){
				$preTravel = $query->result_array();
			}

		/* LISTADO LENGUAJES  */
		$query = $this->db->select("*")->from("tb_languaje")->get();
		if($query->num_rows() > 0){
				$listLanguajes = $query->result_array();
			}


			$filter = array(
				'risposable' => $risposable,
				'typevehicle' => $typevehicle,
				'drivers' => $drivers,
				'aribos' => $aribos,
				'hoteles' => $hoteles,
				'typetravel' => $typetravel,
				'preTravel'  => $preTravel,
				'listLanguajes' => $listLanguajes
			);

			return $filter;
	}


	/* Listado de conductores para el mapa */
	public function  getDriverMap()
	{
		$sql = "SELECT
				t1.idVeichle,
				t1.nameVeichle,
				t1.longAddresVehicle,
				t1.latAddresVehicle,
				t1.addresVehicle,
				t2.dniDriver,
				t2.phoneNumberDriver,
				t2.fisrtNameDriver,
				IFNULL(t2.lastNameDriver,''),
				t2.idStatusDriverTravelKf,
				concat(t2.fisrtNameDriver,' ',IFNULL(t2.lastNameDriver,''))
				 as driver,
				t2.idDriver
				FROM tb_veichle t1
				left JOIN tb_driver t2 ON t2.idVeichleAsigned = t1.idVeichle
				inner join tb_user t3 on t3.idUser = t2.idUserDriver
				where  t2.idStatusDriverTravelKf =  1 and idResourceSocket >0 ";


		$drivers = null;

		/* LISTADO CONDUCTORES DISPONIBLES */
		$query = $this->db->query($sql);

		if($query->num_rows() > 0){
				$drivers = $query->result_array();
			}


		return $drivers;
	}


	/* LISTADO DE FILTROS */
	public function  getFilterFormAsignDriver($travel)
	{


		$driversfree = null;
		$driverspnextfree = null;
		$driversbilingual = null;
		$driversbycategory = null;



		/* LISTADO CONDUCTORES DISPONIBLES */
		$query = $this->db->query(
			$this->queryReturnRemisNearby(
			$travel['latOrigin'],
			$travel['lonOrigin'],1,$travel['dateFilter']));

		if($query->num_rows() > 0){
				$driversfree = $query->result_array();
			}




		/* LISTADO CONDUCTORES BILINGUES */

		if(date("Y/m/d",strtotime($travel['dateFilter'])) > date('Y/m/d'))
		{
			$sqlFi = "";
		}else
		{

			$sqlFi = " AND t2.idDriver not in (
					  SELECT idDriverKf FROM tb_travel where idSatatusTravel in(2,4,5) group by idDriverKf)";
		}


		// VERIFIAMOS SI EL VIAJE ES SOLICITA CHOFER BIINGUE //
		if($travel['isBilingualDriver'] == 1)
		{

		$query = $this->db->query("SELECT
				t1.idVeichle,
				t1.nameVeichle,
				t1.longAddresVehicle,
				t1.latAddresVehicle,
				t1.addresVehicle,
				t2.dniDriver,
				t2.phoneNumberDriver,
				t2.fisrtNameDriver,
				IFNULL(t2.lastNameDriver,''),
				concat(t2.fisrtNameDriver,' ',IFNULL(t2.lastNameDriver,''))
				 as driver,t2.idDriver,
				concat('Distancia: ' ,' ',
				IFNULL(round(( 6371 * ACOS(
				 COS( RADIANS(".$travel['latOrigin'].") )
				 * COS(RADIANS( t1.latAddresVehicle ) )
				 * COS(RADIANS( t1.longAddresVehicle )
				 - RADIANS(".$travel['lonOrigin'].") )
				 + SIN( RADIANS(".$travel['latOrigin'].") )
				 * SIN(RADIANS( t1.latAddresVehicle ) )
				)
				 ),2),0)) AS distance
				FROM tb_veichle t1
				left JOIN tb_driver t2 ON t2.idVeichleAsigned = t1.idVeichle
				inner join tb_driver_schedule t4 on t4.idDriverKf =  t2.idDriver
				where
					  t2.idStatusDriverTravelKf =  1
				and t2.isBilingual = 1
				and

                    (case (SELECT DAYOFWEEK(NOW()))
                    when 1 then isMonday
                    when 2 then isTuesday
                    when 3 then isWednesday
                    when 4 then isThursday
                    when 5 then isFriday
                    when 6 then isSaturday
                    when 7 then isSunday  END) = 1
					  ".$sqlFi."  ");

			if($query->num_rows() > 0){
				$driversbilingual = $query->result_array();
			}
		}


		/* LISTADO CONDUCTORES POR CATEGORIA */
		if($travel['idTypeVehicle'] == 1)
		{

		    $query = $this->db->query("SELECT
				t1.idVeichle,
				t1.nameVeichle,
				t1.longAddresVehicle,
				t1.latAddresVehicle,
				t1.addresVehicle,
				t2.dniDriver,
				t2.phoneNumberDriver,
				t2.fisrtNameDriver,
				IFNULL(t2.lastNameDriver,''),
				concat(t2.fisrtNameDriver,' - ',IFNULL(t5.vehiclenType,''))
				 as driver,t2.idDriver,
				concat('Distancia: ' ,' ',
				IFNULL(round(( 6371 * ACOS(
				 COS( RADIANS(".$travel['latOrigin'].") )
				 * COS(RADIANS( t1.latAddresVehicle ) )
				 * COS(RADIANS( t1.longAddresVehicle )
				 - RADIANS(".$travel['lonOrigin'].") )
				 + SIN( RADIANS(".$travel['latOrigin'].") )
				 * SIN(RADIANS( t1.latAddresVehicle ) )
				)
				 ),2),0)) AS distance
				FROM tb_veichle t1
				left JOIN tb_driver t2 ON t2.idVeichleAsigned = t1.idVeichle
				inner join tb_veichle t3 on t3.idVeichle =  t2.idVeichleAsigned
				inner join tb_vehicle_type t5 on t5.idVehicleType =  t3.idVehiclenTypeAsigned
				inner join tb_driver_schedule t4 on t4.idDriverKf =  t2.idDriver
				where
					  t2.idStatusDriverTravelKf =  1
				and t5.idVehicleType = ".$travel['idTypeVehicle']."
				and

                    (case (SELECT DAYOFWEEK(NOW()))
                    when 1 then isMonday
                    when 2 then isTuesday
                    when 3 then isWednesday
                    when 4 then isThursday
                    when 5 then isFriday
                    when 6 then isSaturday
                    when 7 then isSunday  END) = 1
					  ".$sqlFi." ORDER BY t3.idVehiclenTypeAsigned  ");

			if($query->num_rows() > 0){
				$driversbycategory = $query->result_array();
			}
		}




			$filter = array(
				'driversfree' => $driversfree,
				'driverspnextfree' => $driverspnextfree,
				'driversbilingual' => $driversbilingual,
				'driversbycategory' => $driversbycategory
			);

			return $filter;
	}



	public function getReason()
	{
		$rs = null;
			$query = $this->db->query("SELECT * FROM  tb_reason");

			if($query->num_rows() > 0){
				$rs = $query->result_array();
			}

		return $rs;
	}


	public function driverHeader($idClient = null)
	{
		$rs = null;
			$query = $this->db->query("
				SELECT   count(*),idDriverKf,t2.emailDriver from tB_travel t1
 				INNER JOIN tb_driver t2 on t1.idDriverKf=t2.idDriver
 				where `idSatatusTravel`= 6 and `idClientKf_` = ".$idClient."   group by `idDriverKf` order by count(*) desc limit 1");

			if($query->num_rows() > 0){
				$rs = $query->result_array();
			}

		return $rs;
	}


 	public function addResponsable($cost) {

        $this->db->set(
        	array(
				'idClienteKf'  => $cost['idClienteKf'],
				'nameResposable'  => $cost['nameResposable']
				)

        	)->insert("tb_resposable_client");


        if ($this->db->affected_rows() === 1) {
             $id = $this->db->insert_id();
            return $id;
        } else {
            return null;
        }
    }




	 public function addPasaguer($pasager) {

        $this->db->set(
        	array(
				'idClienteKf' => @$pasager['idClienteKf'],
	            'IdUserKf' => @$pasager['IdUserKf'],
	            'fullNamePanguer' => @$pasager['fullNamePanguer'],
	            'idCostCenter'=> @$pasager['idCostCenter'],
	            'idCompanyAcount'=> @$pasager['idCompanyAcount'],
	            'emailPasaguer' => @$pasager['emailPasaguer'],
	            'direcctionPasaguer'=> @$pasager['direcctionPasaguer'],
	            'phonePasaguer' => @$pasager['phonePasaguer']
				)

        	)->insert("tb_pasangue_client");


        if ($this->db->affected_rows() === 1) {
             $id = $this->db->insert_id();
            return $id;
        } else {
            return null;
        }
    }


    public function getCostCenterByidAcount($id)
    {
    	$query = $this->db->query("
				SELECT idCostCenter,costCenter
				FROM tb_cost_center t1
				inner join tb_company t2 on t2.idClientKf = t1.idCompanyKf
				WHERE    t1.idCompanyAcount = ".$id."  ");


    	$rs = null;
    	if($query->num_rows() > 0){
				$rs = $query->result_array();
			}


		return $rs;
    }


	public function  pasangue($idClient)
	{

		$pasangue= null;
		$acountcompany = null;
		$originClient = null;
		$desinationClient = null;
		$risponsable = null;

			$query = $this->db->query("
				SELECT idPasaguer,idClienteKf,fullNamePanguer
				FROM tb_pasangue_client WHERE idClienteKf = ".$idClient."  ");

			if($query->num_rows() > 0){
				$pasangue = $query->result_array();
			}




			$query = $this->db->query("
				SELECT idCompanyAcount,nrAcount,priceReturn,priceHourDriverMultiLan,
				priceDitanceCompany,priceMinSleepCompany,priceHour
				FROM tb_company_acount t1
				inner join tb_company t2 on t2.idClientKf = t1.idClientKf
				WHERE    t2.idClientKf = ".$idClient."  ");

			if($query->num_rows() > 0){
				$acountcompany = $query->result_array();
			}

			$query = $this->db->query("
				SELECT idTravel,nameOrigin,latOrigin,lonOrigin
				FROM tb_travel WHERE idClientKf = ".$idClient." group by nameOrigin   LIMIT 0 , 5 ");

			if($query->num_rows() > 0){
				$originClient = $query->result_array();
			}

			$query = $this->db->query("
				SELECT idTravel,nameDestination,latDestination,lonDestination
				FROM tb_travel WHERE idClientKf = ".$idClient." group by nameOrigin   LIMIT 0 , 5 ");

			if($query->num_rows() > 0){
				$desinationClient = $query->result_array();
			}

			$query = $this->db->query("
				SELECT * FROM tb_resposable_client WHERE idClienteKf = ".$idClient." ");

			if($query->num_rows() > 0){
				$risponsable = $query->result_array();
			}

			$rs = array(
				'pasangue' => $pasangue,
				'acountcompany' => $acountcompany,
				'originClient' => $originClient,
				'desinationClient' => $desinationClient,
				'risponsable' => $risponsable
			);

			return $rs;
	}


	public function add($travel)
	{


			$getCodeSys = null;
			$codTravel = null;

			/* BUSCAMOS UN CODIGO PARA ASIGNARLO */
			$query = $this->db->select("*")->from("tb_sys_code")->where("idCodeSys =",4)->get();


			if($query->num_rows() > 0){
				$getCodeSys = $query->row_array();
				$codTravel = $getCodeSys['label']."-".$this->formatCode($getCodeSys['code']+1);
			}

			$this->db->set(
			array(
				'code' => $getCodeSys['code']+1
				)
			)->where("idCodeSys =",4)->update("tb_sys_code");

			$travel = $this->_setTravel($travel,$codTravel);



			if(date("Y/m/d",strtotime($travel['dateTravel'])) > date('Y/m/d'))
			{
				$travel['idTypeTravelKf'] = 2;
			}else
			{
				$travel['idTypeTravelKf'] = 1;
			}


			if($travel['idDriverKf'] < 1)
			{
				$travel['idSatatusTravel'] = 1;

			}else
			{
				$travel['idSatatusTravel'] = 2;
			}

			$this->db->set($travel)->insert("tb_travel");

			if($this->db->affected_rows() === 1)
			{

				$idTravel = $this->db->insert_id();

				if($travel['idDriverKf'] > 0)
				{
					$this->sendNotificationMobil($travel['idDriverKf'],$idTravel);
				}

				return $idTravel;

			}else
			{
				return null;
			}
	}

	//* AGREGAR RESERVA FIJA *//
	public function addReservation($data)
	{

		$header = $data['header'];
		$travel = $data['tr']['travel'];





			$travel = $this->_setTravel($travel,null);

			// 2 por que son RESERVAS //
			$travel['idTypeTravelKf'] = 2;



			if($travel['idDriverKf'] < 1)
			{
				$travel['idSatatusTravel'] = 1;
			}else
			{
				$travel['idSatatusTravel'] = 2;
			}



			$this->db->set($travel)->insert("tb_reservation_group_body");

			if($this->db->affected_rows() === 1)
			{

				$idReservationGroupBodi = $this->db->insert_id();

				// Insertamos el header //
				$this->db->set(
				array(
					'nameGroupRes' => $header['nameGroupRes'],
					'idStatusGruopRes'  => 1,
					'idGroupReservationBodyKf'  => $idReservationGroupBodi,
					'idDayWeek'     => json_encode($header['idDayWeek']),
					'idMounth'     => json_encode($header['idMounth']),
					'idYear'     => json_encode($header['idYear']),
					'idUser'   => $header['idUser'],
					'hours' => $header['hours'],
					'idTypeReservationKf' => 1
					)
				)->insert("tb_reservation_group_header");

				$idReservationGroupHeader = $this->db->insert_id();



				for ($y=0; $y < count($header['idYear']); $y++) {
					# Año...

					for ($m=0; $m < count($header['idMounth']); $m++) {
					# mes...



						for ($d=0; $d < count($header['idDayWeek']); $d++) {
						# dia...

								// MES, dia de la semana, año - RETORNA NUMERO DE DIAS QUE POSEE //
								$fechas = $this->diaS($header['idMounth'][$m],$header['idDayWeek'][$d] , $header['idYear'][$y]);

								for ($f=0; $f < count($fechas); $f++) {
								# Insertamos las fechas...
									$travel['dateTravel'] = $fechas[$f]." ".$header['hours'];

									//echo $travel['dateTravel'];
									//echo "</br>";



									$travel['codTravel'] = $this->getCodeTravel();


									$travel['idReservarionGoupHeaderkf'] = $idReservationGroupHeader;

									$this->db->set($travel)->insert("tb_travel");
									if($this->db->affected_rows() === 1){}else
									{
										break;
										return null;
									}

								}
						}
					}

				}

			}



			return true;
	}


	public function addReservationCalendar($data)
	{
		# code...
		$header = $data['header'];
		$travel = $data['tr']['travel'];



			$travel = $this->_setTravel($travel,null);

			// 2 por que son RESERVAS //
			$travel['idTypeTravelKf'] = 2;



			if($travel['idDriverKf'] < 1)
			{
				$travel['idSatatusTravel'] = 1;
			}else
			{
				$travel['idSatatusTravel'] = 2;
			}



			$this->db->set($travel)->insert("tb_reservation_group_body");

			if($this->db->affected_rows() === 1)
			{

				$idReservationGroupBodi = $this->db->insert_id();

				// Insertamos el header //
				$this->db->set(
				array(
					'nameGroupRes' => $header['nameGroupRes'],
					'idStatusGruopRes'  => 1,
					'idGroupReservationBodyKf'  => $idReservationGroupBodi,
					'idDayWeek'     => json_encode($header['idDayWeek']),
					'idUser'   => $header['idUser'],
					'idTypeReservationKf' => 2
					)
				)->insert("tb_reservation_group_header");

				$idReservationGroupHeader = $this->db->insert_id();


					for ($f=0; $f < count($header['idDayWeek']); $f++) {
					# Insertamos las fechas...
						$travel['dateTravel'] = $header['idDayWeek'][$f];

						//echo $travel['dateTravel'];
						//echo "</br>";



						$travel['codTravel'] = $this->getCodeTravel();


						$travel['idReservarionGoupHeaderkf'] = $idReservationGroupHeader;

						$this->db->set($travel)->insert("tb_travel");
						if($this->db->affected_rows() === 1){}else
						{
							break;
							return null;
						}
					}

			}



			return true;
	}


	public function getCodeTravel()
	{

		$getCodeSys = null;
			$codTravel = null;

			/* BUSCAMOS UN CODIGO PARA ASIGNARLO */
			$query = $this->db->select("*")->from("tb_sys_code")->where("idCodeSys =",4)->get();


			if($query->num_rows() > 0){
				$getCodeSys = $query->row_array();
				$codTravel = $getCodeSys['label']."-".$this->formatCode($getCodeSys['code']+1);
			}

			$this->db->set(
			array(
				'code' => $getCodeSys['code']+1
				)
			)->where("idCodeSys =",4)->update("tb_sys_code");


			return $codTravel;
	}



	// ENCIAMOS LA NOTIFICACION A EL MOBIL //
	public function sendNotificationMobil($idDriver,$idTravel)
	{
		/* BUSCAMOS EL TOCKEN MOBIL */
			$query = $query = $this->db->query("
				SELECT  t2.tokenFB FROM  tb_driver t1
				inner join tb_user t2 on t1.idUserDriver =t2.idUser
			 where idDriver =".$idDriver." ");


			$tokenFB = null;

			if($query->num_rows() > 0){
				$rs = $query->row_array();
				$tokenFB = $rs['tokenFB'];
			}



			//$tokenFB = "cV5vfUQaKCc:APA91bHdmuT3h8JaUEQRLpr4sEMYgbkDwK1Yx1eZTbUbE88qPQ2OOo0E-kosRp0or1Pus-oAZP5lvTO72uqaH-oKxzUze-mauWtC3FyK5KsbDaqzDnvp4-v6q8I8gqAfFjsKUvCNsby7";
			// prep the bundle


			if($tokenFB != null){

				$data = $this->findMobil($idTravel);

			 	$notification = array(
						            'title' => "NUEVO VIAJES !", // works fine here
						            'body' => $data['nameOrigin'],
						            'sound' => 'default',
						            'vibrate' =>'1'
						        );

		 	$param = array(
	            "to" => $tokenFB,
	            "notification" => $notification,
	            "data" => $data
	        );



			$headers = array
			(
				'Authorization: key=' . API_ACCESS_KEY,
				'Content-Type: application/json'
			);

			$ch = curl_init();
			curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
			curl_setopt( $ch,CURLOPT_POST, true );
			curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
			curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
			curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $param ) );
			$result = curl_exec($ch );
			curl_close( $ch );
			//echo $result;


			$this->db->set(
        	array(
				'travelId'  => $idTravel,
				'idDriver'  => $idDriver,
				'desc'  => $result,
				'travel' => json_encode( $param )
				)

        	)->insert("tb_tracking_send_travel");




			}
	}



	public function sendPosition($travel)
	{

		$time = date('H:i:s', time());

			$this->db->set(
			array(
				'hour' => $time,
				'latLocation' => $travel['latLocation'],
				'longLocation'  => $travel['longLocation'],
				'location'  => $travel['location'],
				'idTravelKf'     => $travel['idTravelKf'],
				'idDriverKf'     => $travel['idDriverKf']
				)
			)->insert("tb_history_travel_location");


			if($this->db->affected_rows() === 1)
			{
				$this->db->set(
				array(
					'longAddresVehicle' => $travel['longLocation'],
					'latAddresVehicle'  => $travel['latLocation'],
					'addresVehicle'     => $travel['location']
					)
				)->where("idVeichle",$travel['idVeichleAsigned'])->update("tb_veichle");

				$this->socketNotificateLocationToClient($travel);


				// BUSCAMOS LAS NOTIFICACIONES PENDIENTES//

				$rs = "";
				$query = $this->db->query("
				SELECT  t1.`idNotification`,t1.`idType`,t1.`isRead`,t1.`subTitle`,t2.`title`FROM tb_notification_send t1
 				INNER JOIN tb_notification_send_type t2 ON t1.`idType` = t2.`idTypeNotType`WHERE t1.`idUser` = ".$travel['idUser']."
				and t1.isRead = 0
				");

				if($query->num_rows() > 0){
					$rs = $query->result_array();
				}

				return $rs;

			}else
			{
				return null;
			}


	}



	public function socketNotificateLocationToClient($travel)
	{


			$query = $query = $this->db->query("
				SELECT  t2.idResourceSocket FROM  tb_client t1
				inner join tb_user t2 on t1.idUserClient =t2.idUser
			 where idClient =".$travel['idClientKf']." ");


			$idResourceSocket = null;

			if($query->num_rows() > 0){
				$rs = $query->row_array();
				$idResourceSocket = $rs['idResourceSocket'];


				$params = array(
				    'id' => $idResourceSocket
				);

				$ps = http_build_query($params);

				$client = new Client("ws://127.0.0.1:3389"."?".$ps);
				$client->send(json_encode($travel));
			}








	}





	public function cancelTravel($travel)
	{

			$this->db->set(
			array(
					'idSatatusTravel' => $travel['idSatatusTravel'],
					'descriptionCancelTravel' => $travel['descriptionCancelTravel'],
					'idReasonCancelKf' => $travel['idReasonCancelKf'],
					'idUserCancel' => $travel['idUserCancel']
				)
			)->where("idTravel",$travel['idTravel'])->update("tb_travel");


			return $this->findMobil($travel['idTravel']);


	}





	public function infoTravelByDriver($filter)
	{
		$rs = null;

		if($filter['chekin'])
		{

			$query = $this->db->query(" SELECT t1.phoneNumber,t1.distanceLabel,t1.idClientKf,t4.nameStatusTravel,t1.idTravel,t1.idSatatusTravel,t1.codTravel,t1.nameOrigin,concat(t2.fisrtNameDriver,' ',IFNULL(t2.lastNameDriver,'')) driver
				, case idTypeClient
							when 1 then concat(t3.firtNameClient,' ',t3.lastNameClient)
							when 2 then t6.nameClientCompany 	END as client,
				case idTypeClient
							when 1 then concat(t3.firtNameClient,' ',t3.lastNameClient)
							when 2 then t7.fullNamePanguer 	END as pasajero,
				t1.latOrigin,t1.lonOrigin,t1.nameDestination,t1.latDestination,t1.totalAmount,t1.amountGps,t1.amountCalculate, t1.lonDestination,t4.classColorTwo
				 FROM `tb_travel` t1
				INNER join tb_driver t2 on t1.idDriverKf = t2.idDriver
				LEFT join tb_client t3 on t3.idClient=t1.idClientKf
				INNER JOIN tb_travel_status t4 on t1.idSatatusTravel = t4.idStatusTravel
				left join tb_company t6 on t6.idClientKf = t3.idClient
				left join tb_pasangue_client t7 on t7.idPasaguer = t1.passenger1
				WHERE t1.idDriverKf = ".$filter['idDriverKf']." and t1.idSatatusTravel = 2
				order by t1.dateTravel desc
				");

			if($query->num_rows() > 0){
				$rs = $query->result_array();
			}
		}else
		{
			$rs = "";
		}


		$this->db->set(
			array(
				'longAddresVehicle' => $filter['longAddresVehicle'],
				'latAddresVehicle'  => $filter['latAddresVehicle'],
				'addresVehicle'     => $filter['addresVehicle']
				)
			)->where("idVeichle",$filter['idVeichleAsigned'])->update("tb_veichle");





		return $rs;
	}

	public function getInfoTravelByDriver2()
	{
		$rs = null;
		$query = $this->db->query(" SELECT t1.phoneNumber,t1.distanceLabel,t1.idClientKf,t1.totalAmount,t1.amountGps,t1.amountCalculate,t1.codTravel,t1.nameOrigin,concat(t2.fisrtNameDriver,' ',IFNULL(t2.lastNameDriver,'')) driver,t1.latOrigin,t1.lonOrigin,t1.nameDestination,t1.latDestination,t1.totalAmount,t1.amountGps,t1.amountCalculate, t1.lonDestination,t4.classColorTwo
			,case idTypeClient
							when 1 then concat(t3.firtNameClient,' ',t3.lastNameClient)
							when 2 then t6.nameClientCompany 	END as client,
		   case idTypeClient
							when 1 then concat(t3.firtNameClient,' ',t3.lastNameClient)
							when 2 then t7.fullNamePanguer 	END as pasajero FROM `tb_travel` t1
			INNER join tb_driver t2 on t1.idDriverKf = t2.idDriver
			LEFT join tb_client t3 on t3.idClient=t1.idClientKf
			INNER JOIN tb_travel_status t4 on t1.idSatatusTravel = t4.idStatusTravel
			left join tb_company t6 on t6.idClientKf = t3.idClient
			left join tb_pasangue_client t7 on t7.idPasaguer = t1.passenger1
			WHERE  t1.idSatatusTravel = 2
			order by t1.dateTravel desc
			");

		if($query->num_rows() > 0){
			$rs = $query->result_array();
		}

		return $rs;
	}



	public function pasaguerByIdTravel($id)
	{

		$rs = null;
		$isTravelComany = null;
		$idClient = null;

		/* Buscamos el viaje  */
			$query3 = $this->db->select("isTravelComany,idClientKf")->from("tb_travel")->where("idTravel =", $id)->get();
			if($query3->num_rows() > 0){
				$r = $query3->row_array();
				$isTravelComany = $r['isTravelComany'];
				$idClient = $r['idClientKf'];
			}


		if($isTravelComany == 1)
		{



		$sql = "SELECT t1.idPasaguer,t1.fullNamePanguer,t1.emailPasaguer,t1.direcctionPasaguer,t1.phonePasaguer
			 FROM tb_pasangue_client t1
			 LEFT JOIN tb_travel t2 ON t2.`passenger1` = t1.`idPasaguer`
			 WHERE t2.`idTravel` = '".$id."'
			 UNION
			 SELECT t1.idPasaguer,t1.fullNamePanguer,t1.emailPasaguer,t1.direcctionPasaguer,t1.phonePasaguer
			 FROM tb_pasangue_client t1
			 LEFT JOIN tb_travel t3 ON t3.`passenger2` = t1.`idPasaguer`
			 WHERE t3.`idTravel` = '".$id."'
			 UNION
			 SELECT t1.idPasaguer,t1.fullNamePanguer,t1.emailPasaguer,t1.direcctionPasaguer,t1.phonePasaguer
			 FROM tb_pasangue_client t1
			 LEFT JOIN tb_travel t4 ON t4.`passenger3` = t1.`idPasaguer`
			 WHERE t4.`idTravel` = '".$id."'
			 UNION
			 SELECT t1.idPasaguer,t1.fullNamePanguer,t1.emailPasaguer,t1.direcctionPasaguer,t1.phonePasaguer
			 FROM tb_pasangue_client t1
			 LEFT JOIN tb_travel t5 ON t5.`passenger4` = t1.`idPasaguer`
			 WHERE t5.`idTravel` = '".$id."'  ";
			}else
			{
				$sql = "SELECT CONCAT(t1.firtNameClient,'',t1.lastNameClient) AS fullNamePanguer, t1.mailClient AS emailPasaguer,
					t1.addresClient AS direcctionPasaguer,
					t1.phoneClient AS phonePasaguer
					FROM tb_client  t1 WHERE t1.idClient = ".$idClient." ";
			}

		$query = $this->db->query($sql);


		if($query->num_rows() > 0){

			$rs = $query->result_array();

		}

		return $rs;
	}

	// BUSCAR VIAJE POR ID//
	public function find($id)
	{

		$rs = null;
		$sql = "select *,
			concat(t3.fisrtNameDriver,' ',IFNULL(t3.lastNameDriver,'')) driver,
			case idTypeClient
			when 1 then concat(t2.firtNameClient,' ',t2.lastNameClient)
			when 2 then t4.nameClientCompany 	END as client,
			(SELECT (t1.fullNamePanguer)pasaguer1 FROM tb_pasangue_client t1 WHERE t1.`idPasaguer` = t1.passenger1) as pasaguer1,
			(SELECT (t1.fullNamePanguer)pasaguer2 FROM tb_pasangue_client t1 WHERE t1.`idPasaguer` = t1.passenger2) as pasaguer2,
			(SELECT (t1.fullNamePanguer)pasaguer3 FROM tb_pasangue_client t1 WHERE t1.`idPasaguer` = t1.passenger3) as pasaguer3,
			(SELECT (t1.fullNamePanguer)pasaguer4 FROM tb_pasangue_client t1 WHERE t1.`idPasaguer` = t1.passenger4) as pasaguer4
			 from tb_travel t1
			 left JOIN tb_client t2 on t1.idClientKf=t2.idClient
			 LEFT join tb_driver t3 on t3.idDriver=t1.idDriverKf
			 LEFT JOIN tb_company_acount t6 on t6.idCompanyAcount = t1.idCompanyAcountKf
			 LEFT join tb_company t4 on t4.idClientKf = t1.idClientKf
			 inner join tb_travel_status t5 on t5.idStatusTravel = t1.idSatatusTravel
			 where t1.idTravel = '".$id."' ";

		$query = $this->db->query($sql);


		if($query->num_rows() > 0){

			$rs = $query->row_array();

			$rs['origin']['nameOrigin'] = $rs['nameOrigin'];
			$rs['origin']['latOrigin'] = $rs['latOrigin'];
			$rs['origin']['lonOrigin'] = $rs['lonOrigin'];
			$rs['destination']['nameDestination'] = $rs['nameDestination'];
			$rs['destination']['latDestination'] = $rs['latDestination'];
			$rs['destination']['lonDestination'] = $rs['lonDestination'];


			$rs1 = null;
			$query = $this->db->query(" SELECT * from  tb_history_travel_location
				where idTravelKf = '".$id."'
			");

			if($query->num_rows() > 0){
				$rs1 = $query->result_array();
			}


			$rs['pointlocation'] = $rs1;




		}

		return $rs;
	}


	public function findMobil($id)
	{

		$rs = null;
		$sql = "select t1.*,t2.idClient,t1.idClientKf,t3.idDriver,t5.idStatusTravel,t5.nameStatusTravel,t5.classColorTwo,
			concat(t3.fisrtNameDriver,' ',IFNULL(t3.lastNameDriver,'')) driver,
			case idTypeClient
			when 1 then concat(t2.firtNameClient,' ',t2.lastNameClient)
			when 2 then t4.nameClientCompany 	END as client
			 from tb_travel t1
			 left JOIN tb_client t2 on t1.idClientKf=t2.idClient
			 LEFT join tb_driver t3 on t3.idDriver=t1.idDriverKf
			 LEFT join tb_company t4 on t4.idClientKf = t1.idClientKf
			 inner join tb_travel_status t5 on t5.idStatusTravel = t1.idSatatusTravel
			 where t1.idTravel = '".$id."' ";

		$query = $this->db->query($sql);


		if($query->num_rows() > 0){

			$rs = $query->row_array();

			$rs['origin']['nameOrigin'] = $rs['nameOrigin'];
			$rs['origin']['latOrigin'] = $rs['latOrigin'];
			$rs['origin']['lonOrigin'] = $rs['lonOrigin'];
			$rs['destination']['nameDestination'] = $rs['nameDestination'];
			$rs['destination']['latDestination'] = $rs['latDestination'];
			$rs['destination']['lonDestination'] = $rs['lonDestination'];


		}

		return $rs;
	}




	//SERVICO QUE CONTRLA LA INFORMACION DE EL MONITOR DE VIAJES //
	public function dataFromDashboard($filter)
	{
		$originalDate = $filter['date'];
		$newDate = date("d/m/Y", strtotime($originalDate));


		$counterRs = null;

		$sql1 = "
			select t1.idStatusTravel,t1.nameStatusTravel,count(t2.idTravel) as total
				, t1.classColorOne,t1.classColorTwo,t1.classIcon  from tb_travel_status t1
			left JOIN tb_travel t2 on t1.idStatusTravel=t2.idSatatusTravel  ";


		if($filter['filterDate'])
		{
			$sql1 = $sql1." where DATE_FORMAT(t2.dateTravel, '%d/%m/%Y') = '".$newDate."'";
		}



		$filter1 ="";


		// BUSCAMOS UN CLIENTE ESPESIFICO //
		 if($filter['idClientKf'] > -1)
		 {

			$query3 = $this->db->select("idClient")->from("tb_client")->where("idUserClient =", $filter['idClientKf'])->get();
			if($query3->num_rows() > 0){
				$client = $query3->row_array();
			}

			if($filter['filterDate'])
			{
				$filter1 ="and t2.idClientKf =".$filter['idClientKf'];
			}
			else
			{
				$filter1 ="WHERE t2.idClientKf =".$filter['idClientKf'];
			}

		 }


		 if($filter['typeTravelFilter'] == "1")
		 {

			if($filter['filterDate'] || $filter['idClientKf'] > -1)
			{
				$filter1 = $filter1." and ";
			}else
			{
				$filter1 = $filter1." where ";
			}

		 	$filter1 = $filter1."  idTypeTravelKf = 1 ";
		 }
		 else  if($filter['typeTravelFilter'] == "2")
		 {
		 	if($filter['filterDate'] || $filter['idClientKf'] > -1)
			{
				$filter1 = $filter1." and ";
			}else
			{
				$filter1 = $filter1." where ";
			}

		 	$filter1 = $filter1."  idTypeTravelKf = 2 ";
		 }


		$query = $this->db->query($sql1.$filter1."
				group by t1.idStatusTravel,t1.nameStatusTravel,t1.classColorOne,t1.classColorTwo,t1.classIcon order by t1.idStatusTravel
			");




		if($query->num_rows() > 0){
			$counterRs = $query->result_array();
		}



		$sql = "select t1.isBilingualDriver,t1.idTypeVehicle,t1.isTravelByHour,t1.isSendingCloud,t1.codTravel,t1.idTravel,time(t1.dateTravel) hour,date(t1.dateTravel) date,t2.idClient,idTypeClient,t3.nrDriver,t1.isTravelComany,t1.idClientKf,
			case
			when (t1.dateTravel > now() &&
			( now() >= DATE_SUB(t1.dateTravel, INTERVAL
			(select value from tb_sys_param where idParam = 10) MINUTE) &&
			now() < t1.dateTravel
			)
			) then 1 else 0 END as notifReservation,
		   case idTypeClient
			when 1 then concat(t2.firtNameClient,' ',t2.lastNameClient)
			when 2 then t6.nameClientCompany 	END as client, t1.nameOrigin,t1.nameDestination,CONCAT(t3.fisrtNameDriver,' ',IFNULL(t3.lastNameDriver,'')) driver ,t4.nameVeichle,t1.distanceLabel,t1.amountCalculate,t1.totalAmount, t5.idStatusTravel,t5.nameStatusTravel,t5.classColorOne,t5.classColorTwo,t1.lonOrigin,t1.latOrigin from tb_travel t1 left JOIN tb_client t2 on t1.idClientKf=t2.idClient LEFT join tb_driver t3 on t3.idDriver=t1.idDriverKf LEFT JOIN tb_veichle t4 on t4.idVeichle = t3.idVeichleAsigned inner join tb_travel_status t5 on t5.idStatusTravel = t1.idSatatusTravel left join tb_company t6 on t6.idClientKf = t1.idClientKf
			 ";

		 $sqlStatus = "";
		 $sqlFilter = "";
		 $where = false;

		if($filter['filterDate'])
		{
			$sql = $sql . " where DATE_FORMAT(t1.dateTravel, '%d/%m/%Y') = '".$newDate."'   ";
			$where = true;

		}




		 if($filter['filter'] != null)
		 {

		 	if(!$where)
			{
				$sqlFilter = $sqlFilter." where ";
				$where = true;
			}else
			{
				$sqlFilter = $sqlFilter." and ";
			}


		 	$sqlFilter = $sqlFilter ."(
		 	t1.codTravel like '%".$filter['filter']."%' or
		 	t3.nrDriver like '%".$filter['filter']."%' or
		 	t2.firtNameClient like '%".$filter['filter']."%' or
		 	 t2.lastNameClient LIKE '%".$filter['filter']."%') or
		 	 t1.nameOrigin like '%".$filter['filter']."%' or
		 	 t1.nameDestination like '%".$filter['filter']."%' or
		 	 (t3.fisrtNameDriver like '%".$filter['filter']."%' or t3.lastNameDriver LIKE '%".$filter['filter']."%') or t6.nameClientCompany like '%".$filter['filter']."%' ";
		 }



		if($filter['idStatusTravel'] > -1)
		 {
		 	if(!$where)
			{
				$sqlStatus = $sqlStatus." where ";
				$where = true;
			}else
			{
				$sqlStatus = $sqlStatus." and ";
			}

		 	$sqlStatus = $sqlStatus."t1.idSatatusTravel =".$filter['idStatusTravel'];
		 }else
		 {

		 	if(!$where)
			{
				$sqlStatus = $sqlStatus." where ";
				$where = true;
			}else
			{
				$sqlStatus = $sqlStatus." and ";
			}

		 	$sqlStatus = $sqlStatus."t1.idSatatusTravel != 6 and t1.idSatatusTravel != 0";
		 }

		 if($filter['idClientKf'] > -1)
		 {
		 	/* LISTADO DE CONDICIONES DE IVA */
			$query3 = $this->db->select("idClient")->from("tb_client")->where("idUserClient =", $filter['idClientKf'])->get();
			if($query3->num_rows() > 0){
				$client = $query3->row_array();
			}


			if(!$where)
			{
				$sqlStatus = $sqlStatus." where ";
				$where = true;
			}else
			{
				$sqlStatus = $sqlStatus." and ";
			}


		 	$sqlStatus =  $sqlStatus." t1.idClientKf =".$filter['idClientKf'];
		 }


		  if($filter['typeTravelFilter'] == "1")
		 {
		 	if(!$where)
			{
				$sqlStatus = $sqlStatus." where ";
				$where = true;
			}else
			{
				$sqlStatus = $sqlStatus." and ";
			}

		 	$sqlStatus = $sqlStatus."  idTypeTravelKf = 1 ";
		 }
		 else  if($filter['typeTravelFilter'] == "2")
		 {
		 	if(!$where)
			{
				$sqlStatus = $sqlStatus." where ";
				$where = true;
			}else
			{
				$sqlStatus = $sqlStatus." and ";
			}

			$sqlStatus = $sqlStatus."  idTypeTravelKf = 2 ";
		 }





		$query = $this->db->query($sql.$sqlFilter.$sqlStatus."
			order by t1.idSatatusTravel desc,
			date(t1.dateTravel) asc,
			hour(t1.dateTravel) asc  ");

		$data = "";
		if($query->num_rows() > 0){
			$data = $query->result_array();
		}


		$driversfree = "";






			$rs = array(
				'infoFilter' => $counterRs,
				'data' => $data
				);


		return $rs;
	}





	/* EDITAR DATOS DE UN VIAJE SOLO CHOFER */
	public function editTravel($travel)
	{
			$this->db->set($this->_setTravelLite($travel))
			->where("idTravel",$travel['idTravel'])
			->update("tb_travel");


			if ($this->db->trans_status() === true)
			{
				$this->sendNotificationMobil($travel['idDriverKf'],$travel['idTravel']);
				return true;
			}else
			{
				return null;
			}
	}

	/* EDITAR DATOS DE UN VIAJE  */
	public function editTravelFull($travel)
	{
		$this->db->set($this->_setTravel($travel,$travel['codTravel']))
			->where("idTravel",$travel['idTravel'])
			->update("tb_travel");


	 	if($travel['idSatatusTravel'] == 7 || $travel['idSatatusTravel'] == 1)
	 	{
	 		if($travel['idDriverKf'] >0)
			{
				$this->db->set(
				array(
					'	idSatatusTravel' => 2
					)
				)->where("idTravel",$travel['idTravel'])->update("tb_travel");
			}
	 	}

	 	if ($this->db->trans_status() === true)
			{
				return true;
			}else
			{
				return null;
			}
	}




	public function changueStatus($id,$idSatatusTravel)
	{
		$this->db->set(
			array(
				'	idSatatusTravel' => $idSatatusTravel
				)
			)->where("idTravel",$id)->update("tb_travel");

		  	if($idSatatusTravel == 4)
      		{
       			 $this->sendNotificationMobilClient($id);
      		}


			return $this->findMobil($id);

	}

	 // ENCIAMOS LA NOTIFICACION A EL MOBIL A EL Cliente //
  public function sendNotificationMobilClient($idTravel)
  {
    // Buscamos e viaje //
    $data = $this->findMobil($idTravel);

    /* BUSCAMOS EL CLIENTE MOBIL */
    $idClient = $data['idClientKf'];

     /* BUSCAMOS EL TOCKEN MOBIL */
     $tokenFB = null;
     $query = $query = $this->db->query("
      SELECT  t2.tokenFB FROM  tb_client t1
      inner join tb_user t2 on t1.idUserClient =t2.idUser
     where idClient =".$idClient." ");

    if($query->num_rows() > 0){
      $rs = $query->row_array();
      $tokenFB = $rs['tokenFB'];
    }

    if($tokenFB != null){

      $notification = array(
                      'title' => "Chofer en Camino !", // works fine here
                      'body' => $data['nameOrigin']
                  );

       $param = array(
              "to" => $tokenFB,
              "notification" => $notification,
              "data" => $data
          );



      $headers = array
      (
        'Authorization: key=' . API_ACCESS_KEY,
        'Content-Type: application/json'
      );

      $ch = curl_init();
      curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
      curl_setopt( $ch,CURLOPT_POST, true );
      curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
      curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
      curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
      curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $param ) );
      $result = curl_exec($ch );
      curl_close( $ch );
      //echo $result;


      $this->db->set(
        array(
        'travelId'  => $idTravel,
        'idClient'  => $idClient,
        'desc'  => $result,
        'travel' => json_encode( $param )
        )

        )->insert("tb_tracking_send_travel");




    }
  }

	public function infoStatusFinishHour($travel)
	{
		if($travel['isTravelComany']) // VERIFCAMOS SI ES UN VIAJE A UN EMPRESA
			{


				$query = $this->db->query("SELECT IFNULL(((SELECT IFNULL(EXTRACT(HOUR FROM TIMEDIFF(NOW(),t1.dateStartDriver)),0)
   					FROM tb_travel t1 WHERE t1.idTravel = ".$travel['idTravel'].") *
    				(SELECT IFNULL(ta2.priceHour,0) FROM tb_company ta2 WHERE ta2.idClientKf = ".$travel['idClientKf'].")),0)  total ");

				if($query->num_rows() > 0){
					$rs = $query->row_array();

					$totalAmount = $rs['total'];
				}
			}
			else
			{
				$query = $this->db->query("SELECT IFNULL(((SELECT IFNULL(EXTRACT(HOUR FROM TIMEDIFF(NOW(),t1.dateStartDriver)),0)
   					FROM tb_travel t1 WHERE t1.idTravel = ".$travel['idTravel'].") * (SELECT ta2.value FROM tb_sys_param ta2 WHERE ta2.idParam = 14)),0) total ");

				if($query->num_rows() > 0){
					$rs = $query->row_array();

					$totalAmount = $rs['total'];
				}
			}

			return $totalAmount;
	}


	public function changueStatusFinish($travel)
	{

		//$totalAmount = 	0;

		/*if($travel['isTravelByHour'])// VRIFICAMOS SI EL VIAJE ES POR HORA
		{

		}
		else
		{*/
			$totalAmount = $travel['totalAmount'];
		/*}

			*/

		$this->db->set(
			array(
			    'totalAmount' => $totalAmount,
				'amountGps' => $totalAmount,
				'amountTiemeSlepp' => $travel['amountTiemeSlepp'],
				'amounttoll' => $travel['amounttoll']
				)
			)->where("idTravel",$travel['idTravel'])->update("tb_travel");


			if($this->db->affected_rows() === 1)
			{

				$this->db->set(
					array(
						'idSatatusTravel' => 6
						)
					)->where("idTravel",$travel['idTravel'])->update("tb_travel");


					return true;
			}else
			{
				return null;
			}
	}



	public function changueStatusFinishMobil($travel)
	{
		$this->db->set(
			array(
				'amountGps' => $travel['totalAmount'],
				'distanceGps' => $travel['distanceGps'],
				'distanceGpsLabel' => $travel['distanceGpsLabel'],
				'lonDestination' => $travel['longLocation'],
				'latDestination'  => $travel['latLocation'],
				'nameDestination'     => $travel['location']
				)
			)->where("idTravel",$travel['idTravelKf'])->update("tb_travel");


			if($this->db->affected_rows() === 1)
			{

				$this->db->set(
					array(
						'idSatatusTravel' => 6
						)
					)->where("idTravel",$travel['idTravelKf'])->update("tb_travel");


					if($this->db->affected_rows() === 1)
					{
						return true;
					}else
					{
						return null;
					}

			}else
			{
				return null;
			}
	}




	private function formatCode($value)
	{
		$CODE = sprintf("%08d",$value);
		 return $CODE;
	}

	private function _setTravelLite($travel)
	{

		return array(
			'idSatatusTravel' => 2,
			'idDriverKf' => @$travel['idDriverKf'],
		);
	}


	 //----------------------------------------------------------
    //-------- Consulta que permite Buscar EL taxista mas cercano apartir de mi distacia actual --- //
	private function queryReturnRemisNearby($latitud,$longitud,$idStatus,$date)
	{
		$sqlFi = "";


		;

		if(date("Y/m/d",strtotime($date)) > date('Y/m/d'))
		{
			$sqlFi = "";
		}else
		{

			$sqlFi = " AND t2.idDriver not in (
					  SELECT idDriverKf FROM tb_travel where idSatatusTravel in(2,4,5) group by idDriverKf)";
		}

		$sql = "SELECT
				t1.idVeichle,
				t1.nameVeichle,
				t1.longAddresVehicle,
				t1.latAddresVehicle,
				t1.addresVehicle,
				t2.dniDriver,
				t2.phoneNumberDriver,
				t2.fisrtNameDriver,
				IFNULL(t2.lastNameDriver,''),
				concat(t2.fisrtNameDriver,' ',IFNULL(t2.lastNameDriver,''))
				 as driver,t2.idDriver,
				concat('Distancia: ' ,' ',
				IFNULL(round(( 6371 * ACOS(
				 COS( RADIANS(".$latitud.") )
				 * COS(RADIANS( t1.latAddresVehicle ) )
				 * COS(RADIANS( t1.longAddresVehicle )
				 - RADIANS(".$longitud.") )
				 + SIN( RADIANS(".$latitud.") )
				 * SIN(RADIANS( t1.latAddresVehicle ) )
				)
				 ),2),0)) AS distance
				FROM tb_veichle t1
				left JOIN tb_driver t2 ON t2.idVeichleAsigned = t1.idVeichle
				inner join tb_driver_schedule t4 on t4.idDriverKf =  t2.idDriver
				where
					  t2.idStatusDriverTravelKf =  ".$idStatus."
				and

                    (case (SELECT DAYOFWEEK(NOW()))
                    when 1 then isMonday
                    when 2 then isTuesday
                    when 3 then isWednesday
                    when 4 then isThursday
                    when 5 then isFriday
                    when 6 then isSaturday
                    when 7 then isSunday  END) = 1
					   ".$sqlFi."  ";



			$sql .=	" ORDER BY distance ASC  ";

			//echo $sql;


		 return $sql;


	}
    //----------------------------------------------------------

	private function _setTravel($travel,$codTravel)
	{

		return array(

			'codTravel' => $codTravel,
			'isTravelComany' => $travel['isTravelComany'],
			'idClientKf' => $travel['idClientKf'],
			'idUserRes' => @$travel['idUserRes'],
			'idCostCenter' => @$travel['idCostCenter'],
			'idTypeVehicle' => @$travel['idTypeVehicle'],
			'idDriverKf' => @$travel['idDriverKf'],
			'isDriverHeader' => @$travel['isDriverHeader'],
			'idAribos' => @$travel['idAribos'],
			'flight' => @$travel['flight'],
			'precedence' => @$travel['precedence'],
			'companyLabel' => @$travel['companyLabel'],
			'hoursAribo' => @$travel['hoursAribo'],
			'dateTravel' => @$travel['dateTravel'],
			'distanceLabel' => @$travel['distanceLabel'],
			'distance' => @$travel['distance'],
			'durationLabel' => @$travel['durationLabel'],
			'duration' => @$travel['duration'],
			'nameOrigin' => @$travel['origin']['nameOrigin'],
			'latOrigin' => @$travel['origin']['latOrigin'],
			'lonOrigin' => @$travel['origin']['lonOrigin'],
			'nameDestination' => @$travel['destination']['nameDestination'],
			'latDestination' => @$travel['destination']['latDestination'],
			'lonDestination' => @$travel['destination']['lonDestination'],
			'floor' => @$travel['floor'],
			'department' => @$travel['department'],
			'lot' => @$travel['lot'],
			'phoneNumber' => @$travel['phoneNumber'],
			'localNumber' => @$travel['localNumber'],
			'isDriverBilin' => @$travel['isDriverBilin'],
			'isExtraLuggage' => @$travel['isExtraLuggage'],
			'idUserAdd' => @$travel['IdUserKf'],// ojo usuario
			'passenger1' => @$travel['passenger1'],
			'passenger2' => @$travel['passenger2'],
			'passenger3' => @$travel['passenger3'],
			'passenger4' => @$travel['passenger4'],
			'idHotel' => @$travel['idHotel'],
			'idCompanyAcountKf' => @$travel['idCompanyAcountKf'],
			'isTravelByHour'  	=> @$travel['isTravelByHour'],
			'isBilingualDriver' => @$travel['isBilingualDriver'],
			'amountBilingualDriver' =>  @$travel['amountBilingualDriver']

		);
	}


// METODO QUE RETORNA DIAS FECHAS BASADAS EN MES DIA DE LA SEMANA Y AÑO //
	public function diaS($mes, $diasem,$year){

    if(trim($mes)!="")    {

        if (((fmod($year,4)==0) and (fmod($year,100)!=0)) or (fmod($year,400)==0)) {
       $dias_febrero = 29;
   } else {
       $dias_febrero = 28;
   }




   switch($mes) {

       case 01: $cant_dias = 31; break;
       case 02: $cant_dias = $dias_febrero; break;
       case 03: $cant_dias = 31; break;
       case 04: $cant_dias = 30; break;
       case 05: $cant_dias = 31; break;
       case 06: $cant_dias = 30; break;
       case 07: $cant_dias = 31; break;
       case 08: $cant_dias = 31; break;
       case 09: $cant_dias = 30; break;
       case 10: $cant_dias = 31; break;
       case 11: $cant_dias = 30; break;
       case 12: $cant_dias = 31; break;
   }


	if($mes == "8"){$cant_dias = 31;}



        	$arrayDate =  array();
	        for($i=1; $i<=$cant_dias; $i++)    {
	            if(date('w',strtotime($year.'-'.$mes.'-'.$i))==$diasem)    {

	              array_push($arrayDate ,date($year.'-'.$mes.'-'.$i));

	            }
	        }


        return $arrayDate;
    }else    {
        return null;
    }
}




}
