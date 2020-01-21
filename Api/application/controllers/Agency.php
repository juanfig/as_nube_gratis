<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Agency extends REST_Controller {

	public  function __construct()
	{
		parent::__construct();
		if((is_null($this->userInfo) && !in_array($this->router->fetch_method(), [
    		"find","agencyByContry","changeConnect","reportAgencyByContry","statusCreditAgency","filterForm","update", "creditAgency", "ingresarCobro"
    		]) ) || (!is_null($this->userInfo) && !in_array($this->userInfo->idProfileUser,[1,3])) )
    		$this->response(['error' => "No autorizado"],401);
		$this->load->model('agency_model');
	}


	/* SERVICIO QUE RETORNA LOS FILTROS DE EL MODULO DE CLIENTES */
	public function filterForm_get()
	{
		$filters = $this->agency_model->getFilterForm();

		if(! is_null($filters))
		{
			$this->response($filters,200);
		}else
		{
			$this->response(array('response' => 'NO HAY RESULTADOS'),404);
		}
	}

	/* SERVICIO QUE GUARDA UN CLIENTES */
	public function index_get()
	{
		$rs = $this->agency_model->get($this->post('agency'));

		if(! is_null($rs))
		{
			$this->response($rs,200);
		}else
		{
			$this->response(array('error' => "ERROR INESPERADO"),500);
		}
	}



	/* SERVICIO RETORNA UN CLIENTE POR ID */
	public function find_get($id)
	{

		$rs = $this->agency_model->get($id,null);

		if(! is_null($rs))
		{
			$this->response($rs,200);
		}else
		{
			$this->response(array('error' => "ERROR INESPERADO"),500);
		}
	}

	/* SERVICIO EDITA   */
	public function update_post()
	{
        if(!in_array($this->userInfo->idProfileUser,[1,3])) $this->response(['error' => "No autorizado"],401);

		if(!$this->post('agency'))
		{
			$this->response(NULL,404);
		}


		$rs = $this->agency_model->update($this->post('agency'));


		if(! is_null($rs))
		{
			$this->response(array('response' => "Agencia Actualizada "),200);
		}else
		{
			$this->response(array('error' => "ERROR INESPERADO"),500);
		}

	}
	/* SERVICIO ELIMINA   */
	public function delete_post()
	{
        if($this->userInfo->idProfileUser!=1) $this->response(['error' => "No autorizado"],401);

		if(!$this->post('agency'))
		{
			$this->response(NULL,404);
		}


		$rs = $this->agency_model->delete($this->post('agency')['id']);


		if(! is_null($rs))
		{
			$this->response(array('response' => "Agencia Eliminada "),200);
		}else
		{
			$this->response(array('error' => "ERROR INESPERADO"),500);
		}

	}
	/* SERVICIO EDITA STATUS PAYMENT   */
	public function updatePayment_post($id)
	{
        if($this->userInfo->idProfileUser!=1) $this->response(['error' => "No autorizado"],401);


		$rs = $this->agency_model->updatePayment($id);


		if(! is_null($rs))
		{
			$this->response(array('response' => "Agencia Actualizada "),200);
		}else
		{
			$this->response(array('error' => "ERROR INESPERADO"),500);
		}

	}

	public function updatePayment2_post($id)
	{
        if($this->userInfo->idProfileUser!=1) $this->response(['error' => "No autorizado"],401);


		$rs = $this->agency_model->updatePayment2($id);


		if(! is_null($rs))
		{
			$this->response(array('response' => "Agencia Actualizada "),200);
		}else
		{
			$this->response(array('error' => "ERROR INESPERADO"),500);
		}

	}


	// SERVICIO QUE CONSULTA EL TOTAOL DE AGENCIAS POR PAIS
	public function reportAgencyByContry_get(){

        //if($this->userInfo->idProfileUser!=1) $this->response(['error' => "No autorizado"],401);

		$rs = $this->agency_model->reportAgencyByContry();

		if(! is_null($rs))
		{
			$this->response($rs,200);
		}else
		{
			$this->response(array('error' => "ERROR INESPERADO"),500);
		}
	}

	// SERVICIO QUE EXTRAE LA AGENCIA POR PAIS
	public function agencyByContry_get($hcKey, $idAgency){
        //if($this->userInfo->idProfileUser!=1) $this->response(['error' => "No autorizado"],401);

		$rs = $this->agency_model->agencyByContry($hcKey, $idAgency);
		if(! is_null($rs))
		{
			$this->response($rs,200);
		}else
		{
			$this->response(array('error' => "ERROR INESPERADO"),500);
		}
	}


	public function changeConnect_post()
	{
        //if($this->userInfo->idProfileUser!=1) $this->response(['error' => "No autorizado"],401);

		if(!$this->post('agency'))
		{
			$this->response(NULL,404);
		}

		$rs = $this->agency_model->changeConnect($this->post('agency'));

		if(! is_null($rs))
		{
			$this->response(array('response' => "Agencia Actualizada "),200);
		}else
		{
			$this->response(array('error' => "ERROR INESPERADO"),500);
		}
	}


	// SERVICIO QUE GUARDA EL FORMATO
    public function add_post()
    {
        if($this->userInfo->idProfileUser!=1) $this->response(['error' => "No autorizado"],401);

        if (!$this->post('agency')) {
            $this->response(NULL, 404);
        }

        $rs = $this->agency_model->add($this->post('agency'));

        if (!is_null($rs)) {
        	if($rs===false){
        		$this->response(array('error' => "Debe ingresar otro nombre"), 501);
        	}else{
            	 $this->response(array('response' => "Agencia Agregada", "idAgency"=>$rs), 200);      		
        	}
           
        } else {
            $this->response(array('error' => "ERROR INESPERADO"), 500);
        }
    }



    // SERVICIO GET QUE OBTIENE LOS FORMATOS POR FILTRO
    public function search_post()
    {
        $searchFilter = $this->post('filter');

        $agency = $this->agency_model->get(null, $searchFilter);

        if (!is_null($agency)) {
            $this->response($agency, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }

    public function searchAgency_post()
    {
        $searchFilter = $this->post('filter');

        $agency = $this->agency_model->seacrhAgency(null, $searchFilter);

        if (!is_null($agency)) {
            $this->response($agency, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }


    /* SERVICIO QUE RETORNA ABONOS MESUALES/ANUALES POR AGENCIA */
	public function creditAgency_get($id)
	{
    if(!in_array($this->userInfo->idProfileUser,[1,3])) $this->response(['error' => "No autorizado"],401);

		$rs = $this->agency_model->getCreditAgency($id);

		if(! is_null($rs))
		{
			$this->response($rs,200);
		}else
		{
			$this->response(array('error' => "No se encontraron resultados"),500);
		}
	}

	/* SERVICIO QUE REALIZA COBRO DE CREDITO DE UNA AGENCIA */
	public function ingresarCobro_post()
	{
    if(!in_array($this->userInfo->idProfileUser,[1,3])) $this->response(['error' => "No autorizado"],401);

		if(!$this->post('credit'))
		{
			$this->response(NULL,404);
		}


		$rs = $this->agency_model->ingresarCobro($this->post('credit'));


		if(! is_null($rs))
		{
			$this->response(array('response' => "Agencia Actualizada "),200);
		}else
		{
			$this->response(array('error' => "ERROR INESPERADO"),500);
		}
	}


	/* SERVICIO QUE RETORNA ABONOS MESUALES/ANUALES POR AGENCIA */
	public function statusCreditAgency_get($id)
	{
        //if($this->userInfo->idProfileUser!=1) $this->response(['error' => "No autorizado"],401);

		$rs = $this->agency_model->getStatusCreditAgency($id);

		if(! is_null($rs))
		{
			$this->response($rs,200);
		}else
		{
			$this->response(array('error' => "No se encontraron resultados"),500);
		}
	}

    /*SERVICIO QUE CONSULTA LOS PAGOS DE PARJETA DE LAS AGENCIAS*/
    public function cardPayment_get()
    {
        if($this->userInfo->idProfileUser!=1) $this->response(['error' => "No autorizado"],401);

        $rs = $this->agency_model->cardPayment();

        if(! is_null($rs))
        {
            $this->response($rs,200);
        }else
        {
            $this->response(array('error' => "No se encontraron resultados"),500);
        }
    }


}
?>
