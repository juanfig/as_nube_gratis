<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';



class Dashboard extends REST_Controller {

	public  function __construct()
 	{
 		parent::__construct();
 		if(is_null($this->userInfo) || !in_array($this->userInfo->idProfileUser,[1,3]) ) $this->response(['error' => "No autorizado"],401);
 		$this->load->model('usersystem_model');
 	}


 	/* SERVICIO QUE AUTENTIFICA  */
	public function index_get($a=null)
	{
		
		$param = $this->usersystem_model->dashboard($a);
		
		if(! is_null($param))
		{
			$this->response($param,200);
		}else
		{
			$this->response(array('error' => "Sin Parametros Configurados"),500);
		}

	}

	
}

?>