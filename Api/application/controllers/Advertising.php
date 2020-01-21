<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';



class Advertising extends REST_Controller
{

	public function __construct()
	{
		parent::__construct();
		if((is_null($this->userInfo) && $this->router->fetch_method() !="status" ) ||
		  (!is_null($this->userInfo) && !in_array($this->userInfo->idProfileUser,[1,3])) )
    		$this->response(['error' => "No autorizado"],401);
		$this->load->model('advertising_model');
	}


	public function index_get()
	{
		$rs = $this->advertising_model->get($this->post('advertising'));

		if (!is_null($rs))
			{
			$this->response($rs, 200);
		}
		else
			{
			$this->response(array('error' => "NO HAY RESULTADOS"), 404);
		}
	}


	public function find_get($id)
	{

		$rs = $this->advertising_model->get($id, null);

		if (!is_null($rs))
			{
			$this->response($rs, 200);
		}
		else
			{
			$this->response(array('error' => "ERROR INESPERADO"), 500);
		}
	}

	public function status_get($status)
	{

		$rs = $this->advertising_model->get(null, null,$status);

		if (!is_null($rs))
			{
			$this->response($rs, 200);
		}
		else
			{
			$this->response(array('error' => "ERROR INESPERADO"), 500);
		}
	}

	public function add_post()
	{

		if (!$this->post('advertising')) {
			$this->response(NULL, 404);
		}

		$_POST = json_decode(file_get_contents("php://input"), true);


				$add = $this->advertising_model->add($this->post('advertising'));
				if (!is_null($add)) {
					$this->response(array('response' => "Publicidad Agregada",'idAdvertising' =>$add), 200);
				}
	}

	public function update_post()
	{

		if (!$this->post('advertising')) {
			$this->response(NULL, 404);
		}

				$rs = $this->advertising_model->update($this->post('advertising'));

				if (!is_null($rs)) {
					$this->response(array('response' => "Publicidad Modificada"), 200);
				}
				else {
					$this->response(array('error' => "ERROR INESPERADO"), 500);
				}
	}
	public function changeStatus_post()
	{


		$rs = $this->advertising_model->changeStatus($this->post('advertising'));

		if (!is_null($rs)) {
			$this->response(array('response' => "Publicidad Modificada"), 200);
		}
		else {
			$this->response(array('error' => "ERROR INESPERADO"), 500);
		}
	}



	public function search_post()
	{
		$searchFilter = $this->post('filter');

		$rs = $this->advertising_model->get(null, $searchFilter);

		if (!is_null($rs)) {
			$this->response($rs, 200);
		}
		else {
			$this->response(array('error' => 'NO HAY RESULTADOS'), 404);
		}
	}



}
?>
