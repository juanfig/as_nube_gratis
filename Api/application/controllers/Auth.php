<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Auth extends REST_Controller {

	public  function __construct()
 	{
 		parent::__construct();
 		$this->load->model('auth_model');
 	}

 	/* SERVICIO QUE AUTENTIFICA  */
	public function index_post()
	{
		if(!$this->post('user'))
		{
			$this->response(NULL,404);
		}


		$user = $this->auth_model->auth($this->post('user'));
		
		if(!is_null($user))
        {
					$this->response( $user,200);
        }else
        {
        	$this->response(array('error' => "Usuario Invalido"),203);
        }


	}

	/* SERVICIO QUE AUTENTIFICA A LA NUBE  */
public function nube_post()
{
	if(!$this->post('user'))
	{
		$this->response(NULL,404);
	}


	$user = $this->auth_model->authnube($this->post('user'));
			if(!is_null($user))
			 {
				$this->response( $user,200);
			 }else
			 {
				$this->response(array('error' => "Usuario Invalido"),203);
			 }


}

	// SERVICIO PARA AVERIFICAR VERSION //
	public function checkVersion_get($versionApp)
	{
		$response = false;
		$version = "1.8.63";

		if($version != $versionApp)
		{
			$response = true;
		}


		$this->response($response,200);

	}

}


 ?>
