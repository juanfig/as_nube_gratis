<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';



class Config extends REST_Controller {

	public  function __construct()
 	{
 		parent::__construct();
 		if(is_null($this->userInfo) || $this->userInfo->idProfileUser!=1) $this->response(['error' => "No autorizado"],401);
 		$this->load->model('config_model');
 	}


 	/* SERVICIO QUE AUTENTIFICA  */
	public function param_get()
	{
		

		$param = $this->config_model->getParam();
		
		if(! is_null($param))
		{
			$this->response($param,200);
		}else
		{
			$this->response(array('error' => "Sin Parametros Configurados"),500);
		}

	}


	/* SERVICIO QUE AUTENTIFICA  */
	public function chagueParam_post()
	{
		

		$rs = $this->config_model->chagueParam($this->post('filter'));
		
		if(! is_null($rs))
		{
			$this->response($rs,200);
		}else
		{
			$this->response(array('error' => "Usuario Invalido"),500);
		}

	}
	
}

?>