<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';



class Client extends REST_Controller {

	public  function __construct()
 	{
 		parent::__construct();

		if((is_null($this->userInfo) && !in_array($this->router->fetch_method(), [
    		"index","clientImport","update","delete"
    		]) ) || (!is_null($this->userInfo) && !in_array($this->userInfo->idProfileUser,[1,3])) )
    		$this->response(['error' => "No autorizado"],401);

 		$this->load->model('client_model');
 	}


	/* SERVICIO QUE GUARDA UN CLIENTES */
	public function index_post()
	{
		if(!$this->post('client'))
		{
			$this->response(NULL,404);
		}

		$ClientId = $this->client_model->add($this->post('client'));

		if(! is_null($ClientId))
		{
			$this->response($ClientId,200);
		}else
		{
			$this->response(array('error' => "ERROR INESPERADO"),500);
		}
	}

		/* SERVICIO QUE GUARDA UN CLIENTES */
		public function clientImport_post()
		{
			if(!$this->post('client'))
			{
				$this->response(NULL,404);
			}
	
			$ClientId = $this->client_model->addImport($this->post('client'));
	
			if(! is_null($ClientId))
			{
				$this->response($ClientId,200);
			}else
			{
				$this->response(array('error' => "ERROR INESPERADO"),500);
			}
		}

	/* SERVICIO EDITA UN CLIENTE  */
	public function update_post()
	{
		if(!$this->post('client'))
		{
			$this->response(NULL,404);
		}


		$rs = $this->client_model->update($this->post('client'));


		if(! is_null($rs))
		{
			$this->response($rs,200);
		}else
		{
			$this->response(array('error' => "ERROR INESPERADO"),500);
		}

	}

	/* SERVICIO INACTIVA  UN USUARIOS POR ID */

    public function delete_delete($id) {
        
        if (!$id) {
            $this->response(NULL, 404);
        }
        $user = $this->client_model->changueStatus($id, -1);

        if (!is_null($user)) {
            $this->response($user, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }
}
?>
