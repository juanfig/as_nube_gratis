<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';



class Seller extends REST_Controller
{

	public function __construct()
	{
		parent::__construct();
		if(is_null($this->userInfo) || $this->userInfo->idProfileUser!=1) $this->response(['error' => "No autorizado"],401);
		$this->load->model('seller_model');
	}

	/* SERVICIO QUE RETORNA */
	public function filterForm_get()
	{
		$filters = $this->seller_model->getFilterForm();

		if (!is_null($filters))
			{
			$this->response($filters, 200);
		}
		else
			{
			$this->response(array('response' => 'NO HAY RESULTADOS'), 404);
		}
	}


	public function index_get()
	{
		$rs = $this->seller_model->get($this->post('seller'));


		if (!is_null($rs))
			{
			$this->response($rs, 200);
		}
		else
			{
			$this->response(array('error' => "NO HAY RESULTADOS"), 404);

		}
	}

    // SERVICIO QUE GUARDA EL VENDEDOR
	public function add_post()
	{

		if (!$this->post('seller')) {
			$this->response(NULL, 404);
		}

		$_POST = json_decode(file_get_contents("php://input"), true);
		$dniVerify = $this->seller_model->dniVerify($_POST['seller']['dniSeller']);

		if (!$dniVerify) {

			$emailVerify = $this->seller_model->emailVerify($_POST['seller']['emailSeller']);

			if (!$emailVerify) {

				$add_seller = $this->seller_model->add($this->post('seller'));
				if (!is_null($add_seller)) {
					$this->response(array('response' => "VENDEDOR AGREGADO"), 200);
				}
				else {
					$this->response(array('error' => "ERROR INESPERADO"), 500);
				}
			}
			else {
				$this->response(array('error' => "<br> EL EMAIL YA SE ENCUENTRA REGISTRADO"), 500);
			}
		}
		else {
			$this->response(array('error' => "<br> EL DNI YA SE ENCUENTRA REGISTRADO"), 500);
		}




	}

	public function update_post()
	{

		if (!$this->post('seller')) {
			$this->response(NULL, 404);
		}
		$_POST = json_decode(file_get_contents("php://input"), true);
		$dniVerify = $this->seller_model->dniVerify($_POST['seller']['dniSeller']);

		if (!$dniVerify) {

			$emailVerify = $this->seller_model->emailVerify($_POST['seller']['emailSeller']);

			if (!$emailVerify) {
				$seller = $this->seller_model->update($this->post('seller'));

				if (!is_null($seller)) {
					$this->response(array('response' => "VENDEDOR MODIFICADO", "idSeller" => $seller), 200);
				}
				else {
					$this->response(array('error' => "ERROR INESPERADO"), 500);
				}
			}
			else {
				$this->response(array('error' => "<br> EL EMAIL YA SE ENCUENTRA REGISTRADO"), 500);
			}
		}
		else {
			$this->response(array('error' => "<br> EL DNI YA SE ENCUENTRA REGISTRADO"), 500);
		}
	}
	public function changestatus_post()
	{


		$seller = $this->seller_model->changeStatus($this->post('seller'));

		if (!is_null($seller)) {
			$this->response(array('response' => "VENDEDOR MODIFICADO", "idSeller" => $seller), 200);
		}
		else {
			$this->response(array('error' => "ERROR INESPERADO"), 500);
		}
	}



    // SERVICIO GET QUE OBTIENE LOS FORMATOS POR FILTRO
	public function search_post()
	{
		$searchFilter = $this->post('filter');

		$seller = $this->seller_model->get(null, $searchFilter);

		if (!is_null($seller)) {
			$this->response($seller, 200);
		}
		else {
			$this->response(array('error' => 'NO HAY RESULTADOS'), 404);
		}
	}


	/* SERVICIO RETORNA UN VENDEDOR POR ID */
	public function find_get($id)
	{

		$rs = $this->seller_model->get($id, null);

		if (!is_null($rs))
			{
			$this->response($rs, 200);
		}
		else
			{
			$this->response(array('error' => "ERROR INESPERADO"), 500);
		}
	}


	/* SERVICIO RETORNA UN VENDEDOR POR ID */
	public function account_get($id)
	{

		$rs = $this->seller_model->get($id, null);

		if (!is_null($rs))
			{
			$this->response($rs, 200);
		}
		else
			{
			$this->response(array('error' => "ERROR INESPERADO"), 500);
		}
	}

	public function delete_post()
	{


		$seller = null;
		$seller = $this->seller_model->delete($this->post('seller'));

		if (!is_null($seller)) {
			$this->response($seller, 200);
		}
		else {
			$this->response(array('error' => 'NO HAY RESULTADOS'), 404);
		}
	}



}
?>
