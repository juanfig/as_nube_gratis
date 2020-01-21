<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';



class Partner extends REST_Controller
{

	public function __construct()
	{
		parent::__construct();
		if(is_null($this->userInfo) || $this->userInfo->idProfileUser!=1) $this->response(['error' => "No autorizado"],401);
		$this->load->model('partner_model');
	}

	/* SERVICIO QUE RETORNA */
	public function filterForm_get()
	{
		$filters = $this->partner_model->getFilterForm();

		if (!is_null($filters))
		{
			$this->response($filters, 200);
		}
		else
		{
			$this->response(array('response' => 'NO HAY RESULTADOS'), 404);
		}
	}

	/***/
	public function cloudByCountry_get()
	{
		$filters = $this->partner_model->getCloudByCountry();

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
		$rs = $this->partner_model->get();

		if (!is_null($rs))
		{
			$this->response($rs, 200);
		}
		else
		{
			$this->response(array('error' => "NO HAY RESULTADOS"), 404);
		}
	}

    // SERVICIO QUE GUARDA EL PARTNER
	public function add_post()
	{

		if (!$this->post('partner')) {
			$this->response(NULL, 404);
		}

		$_POST = $this->post('partner');
		$verify = $this->partner_model->dniVerify($_POST['dniPartner']);

		if ($verify) {
			$this->response(array('error' => "<br> EL DNI YA SE ENCUENTRA REGISTRADO"), 500);
		}

		$verify = $this->partner_model->emailVerify($_POST['emailPartner']);

		if ($verify) {
			$this->response(array('error' =>"<br> EL EMAIL YA SE ENCUENTRA REGISTRADO"), 500);
		}

		$verify = $this->partner_model->countryVerify($_POST['idCountryKf']);

		if ($verify) {
			$this->response(array('error' => "<br> EL PAIS YA SE ENCUENTRA REGISTRADO"), 500);
		}

		$add_partner = $this->partner_model->add($this->post('partner'));
		if (is_null($add_partner)) {
					$this->response(array('error' => "ERROR INESPERADO"), 500);
		} else {
					$this->response(array('response' => "PARTNER AGREGADO", "idPartner" => $add_partner), 200);
		}

	}

	public function update_post()
	{

		if (!$this->post('partner')) {
			$this->response(NULL, 404);
		}
		$_POST = json_decode(file_get_contents("php://input"), true);
		$dniVerify = $this->partner_model->dniVerify($_POST['partner']['dniPartner'], $_POST['partner']['idPartner']);

		if (!$dniVerify) {

			$emailVerify = $this->partner_model->emailVerify($_POST['partner']['emailPartner'], $_POST['partner']['idUserKf']);

			if (!$emailVerify) {
				$partner = $this->partner_model->update($this->post('partner'));

				if (!is_null($partner)) {
					$this->response(array('response' => "PARTNER MODIFICADO", "idPartner" => $partner), 200);
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
	
	public function changeStatus_post()
	{


		$partner = $this->partner_model->changeStatus($this->post('partner'));

		if (!is_null($partner)) {
			$this->response(array('response' => "PARTNER MODIFICADO", "idStatus" => $partner), 200);
		}
		else {
			$this->response(array('error' => "ERROR INESPERADO"), 500);
		}
	}



    // SERVICIO GET QUE OBTIENE LOS FORMATOS POR FILTRO
	public function search_post()
	{
		$searchFilter = $this->post('filter');

		$partner = $this->partner_model->get(null, $searchFilter);

		if (!is_null($partner)) {
			$this->response($partner, 200);
		}
		else {
			$this->response(array('error' => 'NO HAY RESULTADOS'), 404);
		}
	}


	/* SERVICIO RETORNA UN PARTNER POR ID */
	public function find_get($id)
	{

		$rs = $this->partner_model->get($id, null);

		if (!is_null($rs))
		{
			$this->response($rs, 200);
		}
		else
		{
			$this->response(array('error' => "ERROR INESPERADO"), 500);
		}
	}


	/* SERVICIO RETORNA UN PARTNER POR ID */
	public function account_get($id)
	{

		$rs = $this->partner_model->get($id, null);

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


		$partner = null;
		$partner = $this->partner_model->delete($this->post('partner'));

		if (!is_null($partner)) {
			$this->response($partner, 200);
		}
		else {
			$this->response(array('error' => 'NO HAY RESULTADOS'), 404);
		}
	}


	public function validateEmail_post()
	{

		$rs = $this->partner_model->emailVerify($this->post('emailPartner'));

		if (!is_null($rs)){
			$this->response($rs, 200);
		}
		else
		{
			$this->response(array('error' => "ERROR INESPERADO"), 500);
		}
	}


}
