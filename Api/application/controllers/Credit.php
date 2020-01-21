<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';



class Credit extends REST_Controller
{

	public function __construct()
	{
		parent::__construct();
		if((is_null($this->userInfo) && $this->router->fetch_method() != "index") || (!is_null($this->userInfo) && !in_array($this->userInfo->idProfileUser,[1,3])) )
    		$this->response(['error' => "No autorizado"],401);
		$this->load->model('credit_model');
		$this->load->model('cloud_model');
	}

	/* SERVICIO QUE RETORNA */
	public function filterForm_get()
	{
		$filters = $this->credit_model->getFilterForm();

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
		$rs = $this->credit_model->get($this->post('credit'));

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

		if (!$this->post('credit')) {
			$this->response(NULL, 404);
		}

		$_POST = json_decode(file_get_contents("php://input"), true);
		// $dniVerify = $this->credit_model->dniVerify($_POST['partner']['dniPartner']);

		// if (!$dniVerify) {

		// 	$emailVerify = $this->credit_model->emailVerify($_POST['partner']['emailPartner']);

		// 	if (!$emailVerify) {

				$add_partner = $this->credit_model->add($this->post('credit'));
				if (!is_null($add_partner)) {
					$this->response(array('response' => "ABONO AGREGADO"), 200);
				}
			// 	else {
			// 		$this->response(array('error' => "ERROR INESPERADO"), 500);
			// 	}
			// }
			// else {
			// 	$this->response(array('error' => "<br> EL EMAIL YA SE ENCUENTRA REGISTRADO"), 500);
			// }
		// }
		// else {
		// 	$this->response(array('error' => "<br> EL DNI YA SE ENCUENTRA REGISTRADO"), 500);
		// }




	}

	public function update_post()
	{

		if (!$this->post('credit')) {
			$this->response(NULL, 404);
		}
		// $_POST = json_decode(file_get_contents("php://input"), true);
		// $dniVerify = $this->credit_model->dniVerify($_POST['partner']['dniPartner']);

		// if (!$dniVerify) {

		// 	$emailVerify = $this->credit_model->emailVerify($_POST['partner']['emailPartner']);

		// 	if (!$emailVerify) {
				$credit = $this->credit_model->update($this->post('credit'));

				if (!is_null($credit)) {
					$this->response(array('response' => "ABONO MODIFICADO"), 200);
				}
				else {
					$this->response(array('error' => "ERROR INESPERADO"), 500);
				}
		// 	}
		// 	else {
		// 		$this->response(array('error' => "<br> EL EMAIL YA SE ENCUENTRA REGISTRADO"), 500);
		// 	}
		// }
		// else {
		// 	$this->response(array('error' => "<br> EL DNI YA SE ENCUENTRA REGISTRADO"), 500);
		// }
	}
	public function changestatus_post()
	{


		$partner = $this->credit_model->changeStatus($this->post('partner'));

		if (!is_null($partner)) {
			$this->response(array('response' => "PARTNER MODIFICADO", "idPartner" => $partner), 200);
		}
		else {
			$this->response(array('error' => "ERROR INESPERADO"), 500);
		}
	}



    // SERVICIO GET QUE OBTIENE LOS FORMATOS POR FILTRO
	public function search_post()
	{
		$searchFilter = $this->post('filter');

		$partner = $this->credit_model->get(null, $searchFilter);

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

		$rs = $this->credit_model->get($id, null);

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

		$rs = $this->credit_model->get($id, null);

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
		$partner = $this->credit_model->delete($this->post('partner'));

		if (!is_null($partner)) {
			$this->response($partner, 200);
		}
		else {
			$this->response(array('error' => 'NO HAY RESULTADOS'), 404);
		}
	}

	 public function filter_get() {

        $filter = $this->cloud_model->getFilter();

        if (!is_null($filter)) {
            $this->response($filter, 200);
        } else {
            $this->response(array('error' => "ERROR INESPERADO"), 500);
        }
    }


    public function ejecutarMensuales_get()
	{

		$rs = $this->credit_model->ejecutarMensuales();

		if (!is_null($rs)){
			$this->response($rs, 200);
		}
		else{
			$this->response(array('error' => "ERROR INESPERADO"), 500);
		}
	}


}
?>
