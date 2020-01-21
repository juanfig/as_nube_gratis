<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';


class Travel extends REST_Controller {



	public  function __construct()
 	{
 		parent::__construct();
 		if(is_null($this->userInfo) || $this->userInfo->idProfileUser!=1) $this->response(['error' => "No autorizado"],401);
 		$this->load->model('travel_model');
 	}




	/* SERVICIO QUE RETORNA LOS FILTROS DE EL MODULO DE CLIENTES */
	public function filterForm_get()
	{
		$filters = $this->travel_model->getFilterForm();

		if(! is_null($filters))
		{
			$this->response($filters,200);
		}else
		{
			$this->response(array('response' => 'NO HAY RESULTADOS'),404);
		}
	}

	/* SERVICIO QUE RETORNA LOS MOTIVOS DE CANCELACION DE UN VIAJE */
	public function reason_get()
	{
		$filters = $this->travel_model->getReason();

		if(! is_null($filters))
		{
			$this->response($filters,200);
		}else
		{
			$this->response(array('response' => 'NO HAY MOTIVOS CONFIGURADOS'),404);
		}
	}

	/* SERVICIO RETORNA UN CLIENTE POR NOBRE Y TIPO */
	public function clientByName_post()
	{
		if(!$this->post('filter'))
		{
			$this->response(NULL,404);
		}


		$client = null;
		$client = $this->travel_model->getClientByName($this->post('filter'));

		if(! is_null($client))
		{
			$this->response($client,200);
		}else
		{
			$this->response(array('error' => 'NO HAY RESULTADOS'),404);
		}
	}

	/* SERVICIO CANCELA UN VIAJE */
	public function cancel_get($id)
	{
		if(!$id)
		{
			$this->response(NULL,404);
		}

		$driver = null;
		$driver = $this->travel_model->changueStatus($id,0);

		if(! is_null($driver))
		{
			$this->response($driver,200);
		}else
		{
			$this->response(array('error' => 'NO HAY RESULTADOS'),404);
		}
	}

	/* SERVICIO RECHAZA UN VIAJE */
	public function refuse_get($id)
	{
		if(!$id)
		{
			$this->response(NULL,404);
		}

		$driver = null;
		$driver = $this->travel_model->changueStatus($id,7);

		if(! is_null($driver))
		{
			$this->response($driver,200);
		}else
		{
			$this->response(array('error' => 'NO HAY RESULTADOS'),404);
		}
	}


	/* SERVICIO CANCELA UN VIAJE */
	public function cancel_post()
	{
		if(!$this->post('travel'))
		{
			$this->response(NULL,404);
		}


		$travel = null;
		$travel = $this->travel_model->cancelTravel($this->post('travel'));

		if(! is_null($travel))
		{
			$this->response(array('response' => "VIAJE CANCELADO "),200);
		}else
		{
			$this->response(array('error' => 'NO HAY RESULTADOS'),404);
		}
	}



	/* SERVICIO FINALIZA UN VIAJE */
	public function finish_get($id)
	{


		if(!$id)
		{
			$this->response(NULL,404);
		}

		$driver = null;
		$driver = $this->travel_model->changueStatus($id,6);

		if(! is_null($driver))
		{
			$this->response($driver,200);
		}else
		{
			$this->response(array('error' => 'NO HAY RESULTADOS'),404);
		}
	}

        /* SERVICIO EN CURSO DE UN VIAJE */
	public function progress_get($id)
	{


		if(!$id)
		{
			$this->response(NULL,404);
		}

		$driver = null;
		$driver = $this->travel_model->changueStatus($id,5);

		if(! is_null($driver))
		{
			$this->response($driver,200);
		}else
		{
			$this->response(array('error' => 'NO HAY RESULTADOS'),404);
		}
	}

	/* SERVICIO INICIAR UN VIAJE  */
	public function init_get($id)
	{

		if(!$id)
		{
			$this->response(NULL,404);
		}

		$travel = null;
		$travel = $this->travel_model->changueStatus($id,5);

		if(! is_null($travel))
		{
			$this->response($travel,200);
		}else
		{
			$this->response(array('error' => 'NO HAY RESULTADOS'),404);
		}
	}







	/* SERVICIO GUARDA RECORRIDOS DE EL VIAJE   */
	public function sendPosition_post()
	{


		if(!$this->post('travel'))
		{
			$this->response(NULL,404);
		}


		$response = null;
		$response = $this->travel_model->sendPosition($this->post('travel'));

		if(! is_null($response))
		{
			$this->response($response,200);
		}else
		{
			$this->response(array('error' => 'NO HAY RESULTADOS'),404);
		}
	}





	/* SERVICIO QUE RETORN LA INFORMACION DE EL VIAJE POR HORA */
	public function infoStatusFinishHour_post()
	{


		if(!$this->post('travel'))
		{
			$this->response(NULL,404);
		}


		$amount = 0;
		$amount = $this->travel_model->infoStatusFinishHour($this->post('travel'));

		if(! is_null($amount))
		{
			$this->response($amount,200);
		}else
		{
			$this->response(array('error' => 'NO HAY RESULTADOS'),404);
		}
	}

	/* SERVICIO FINALIZA UN VIAJE POST PARA GUARDAR MONTO */
	public function finish_post()
	{


		if(!$this->post('travel'))
		{
			$this->response(NULL,404);
		}


		$driver = null;
		$driver = $this->travel_model->changueStatusFinish($this->post('travel'));

		if(! is_null($driver))
		{
			$this->response($driver,200);
		}else
		{
			$this->response(array('error' => 'NO HAY RESULTADOS'),404);
		}
	}


	/* SERVICIO FINALIZA UN VIAJE POST PARA GUARDAR MONTO */
	public function finishMobil_post()
	{


		if(!$this->post('travel'))
		{
			$this->response(NULL,404);
		}


		$driver = null;
		$driver = $this->travel_model->changueStatusFinishMobil($this->post('travel'));

		if($driver)
		{
			$this->response($driver,200);
		}else
		{
			$this->response(array('error' => "ERROR INESPERADO"),500);
		}
	}


	/* SERVICIO   ACEPTA VIAJE */
	public function accept_get($id)
	{
		if(!$id)
		{
			$this->response(NULL,404);
		}

		$travel = null;
		$travel = $this->travel_model->changueStatus($id,4);

		if(! is_null($travel))
		{
			$this->response($travel,200);
		}else
		{
			$this->response(array('error' => 'NO HAY RESULTADOS'),404);
		}
	}



	/* BUSCAR Centros de costo  */
	public function costCenterByidAcount_get($id)
	{
		if(!$id)
		{
			$this->response(NULL,404);
		}

		$costCneter = null;
		$costCneter = $this->travel_model->getCostCenterByidAcount($id);

		if(! is_null($costCneter))
		{
			$this->response($costCneter,200);
		}
	}


	/* BUSCAR PASAGEROS DE UN CLIENTE  */
	public function pasangue_get($id)
	{
		if(!$id)
		{
			$this->response(NULL,404);
		}

		$pasangue = null;
		$pasangue = $this->travel_model->pasangue($id);

		if(! is_null($pasangue))
		{
			$this->response($pasangue,200);
		}
	}




	/* editar viaje solo chofer */
	public function update_post()
	{
		if(!$this->post('travel'))
		{
			$this->response(NULL,404);
		}


		$edit = null;
		$edit = $this->travel_model->editTravel($this->post('travel'));

		if(! is_null($edit))
		{
			$this->response($edit,200);
		}else
		{
			$this->response(array('error' => 'NO HAY RESULTADOS'),404);
		}
	}

	/* editar viaje */
	public function updatefull_post()
	{
		if(!$this->post('travel'))
		{
			$this->response(NULL,404);
		}


		$edit = null;
		$edit = $this->travel_model->editTravelFull($this->post('travel'));

		if(! is_null($edit))
		{
			$this->response(array('response' => "VIAJE EDITADO "),200);
		}else
		{
			$this->response(array('error' => 'NO HAY RESULTADOS'),404);
		}
	}


	/* SERVICIO RETORNA UN CLIENTE POR ID */
	public function findDriverHeader_get($id)
	{
		if(!$id)
		{
			$this->response(NULL,404);
		}

		$driver = null;
		$driver = $this->travel_model->findDriverHeader($id);

		if(! is_null($driver))
		{
			$this->response($driver,200);
		}else
		{
			$this->response(array('error' => 'NO HAY RESULTADOS'),404);
		}
	}


	public function find_get($id)
	{
		if(!$id)
		{
			$this->response(NULL,404);
		}

		$travel = null;
		$travel = $this->travel_model->find($id);

		if(! is_null($travel))
		{
			$this->response($travel,200);
		}else
		{
			$this->response(array('error' => 'NO HAY RESULTADOS'),404);
		}
	}

	public function getFilterFormAsignDriver_post()
	{

		if(!$this->post('travel'))
		{
			$this->response(NULL,404);
		}


		$travel = null;
		$travel = $this->travel_model->getFilterFormAsignDriver($this->post('travel'));

		if(! is_null($travel))
		{
			$this->response($travel,200);
		}else
		{
			$this->response(array('error' => 'NO HAY RESULTADOS'),404);
		}
	}


	// SERVICIO QUE BUSCA CHOFERS PARA E MAPA //
	public function getDriverMap_get()
	{



		$driver = null;
		$driver = $this->travel_model->getDriverMap();

		if(! is_null($driver))
		{
			$this->response($driver,200);
		}else
		{
			$this->response(array('error' => 'NO HAY CONDUCTORES'),404);
		}
	}

	/* SERVICIO QUE GUARDA UN VIAJE */
	public function index_post()
	{
		if(!$this->post('travel'))
		{
			$this->response(NULL,404);
		}


		$travelId = $this->travel_model->add($this->post('travel'));

		if(! is_null($travelId))
		{
			$this->response(array('response' => "VIAJE AGREGADO "),200);
		}else
		{
			$this->response(array('error' => "ERROR INESPERADO"),500);
		}

	}


	public function addPasaguer_post()
	{
		if(!$this->post('pasaguer'))
		{
			$this->response(NULL,404);
		}



		$pasaguerId = $this->travel_model->addPasaguer($this->post('pasaguer'));



		if(! is_null($pasaguerId))
		{
			$this->response(array('response' => "PASAJERO AGREGADO "),200);
		}else
		{
			$this->response(array('error' => "ERROR INESPERADO"),500);
		}

	}


	public function addResponsable_post()
	{
		if(!$this->post('risponsable'))
		{
			$this->response(NULL,404);
		}



		$responsableId = $this->travel_model->addResponsable($this->post('risponsable'));



		if(! is_null($responsableId))
		{
			$this->response(array('response' => "PASAJERO AGREGADO "),200);
		}else
		{
			$this->response(array('error' => "ERROR INESPERADO"),500);
		}

	}


	//* SERVICIO PARA AGREGAR RESERVAS  */
	public function addReservation_post()
	{
		if(!$this->post('reservation'))
		{
			$this->response(NULL,404);
		}


		$travelId = $this->travel_model->addReservation($this->post('reservation'));



		if(! is_null($travelId))
		{
			$this->response(array('response' => "RESERVA AGREGADA "),200);
			//$this->response(array('response' => json_encode($this->post('travel'))),200);
		}else
		{
			$this->response(array('error' => "ERROR INESPERADO"),500);
		}

	}

	//* SERVICIO PARA AGREGAR RESERVAS  */
	public function addReservationCalendar_post()
	{
		if(!$this->post('reservation'))
		{
			$this->response(NULL,404);
		}


		$travelId = $this->travel_model->addReservationCalendar($this->post('reservation'));



		if(! is_null($travelId))
		{
			$this->response(array('response' => "RESERVA AGREGADA "),200);
		}else
		{
			$this->response(array('error' => "ERROR INESPERADO"),500);
		}

	}



	//* SERVICIO PARA AGREGAR VIAJES */
	public function add_post()
	{
		if(!$this->post('travel'))
		{
			$this->response(NULL,404);
		}

		//$travelId = 1;

		$travelId = $this->travel_model->add($this->post('travel'));



		if(! is_null($travelId))
		{
			$this->response(array('response' => "VIAJE AGREGADO "),200);
			//$this->response(array('response' => json_encode($this->post('travel'))),200);
		}else
		{
			$this->response(array('error' => "ERROR INESPERADO"),500);
		}

	}

	/* SERVICIO GET QUE BUSCA LA INFORMACION DE EL MONITOR DE VIAJES  */
	public function dataFromDashboard_post()
	{

		$searchFilter = $this->post('filter');

		$result = $this->travel_model->dataFromDashboard($searchFilter);

		if(! is_null($result))
		{
			$this->response($result,200);
		}else
		{
			$this->response(array('error' => 'NO HAY RESULTADOS'),404);
		}


	}


	/* SERVICIO POST QUE BUSCA VIAJES DE UN CHOFER CON UN STATUS ESPECIFICO */
	public function infoTravelByDriver_post()
	{

		$searchFilter = $this->post('filter');

		$result = $this->travel_model->infoTravelByDriver($searchFilter);

		if(! is_null($result))
		{
			$this->response($result,200);
		}else
		{
			$this->response(array('error' => 'NO HAY VIAJES'),404);
		}


	}



	// PASAGEROS DE UN VIAJE  //
	public function pasaguerByIdTravel_get($idTravel)
	{
		if(!$idTravel)
		{
			$this->response(NULL,404);
		}

		$pasangue = null;
		$pasangue = $this->travel_model->pasaguerByIdTravel($idTravel);

		if(! is_null($pasangue))
		{
			$this->response($pasangue,200);
		}else
		{
			$this->response(array('error' => 'NO HAY RESULTADOS'),404);
		}


	}

	// SERVICIO PARA WEB SOCKET //
	public function getMyTravels_get($idDriveer)
	{
		if(!$idDriveer)
		{
			$this->response(NULL,404);
		}

		$travel = null;
		$travel = $this->travel_model->getInfoTravelByDriver($idDriveer);

		if(! is_null($travel))
		{
			$this->response($travel,200);
		}else
		{
			$this->response(array('error' => 'NO HAY RESULTADOS'),404);
		}


	}



}
