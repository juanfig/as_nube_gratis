<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';



class Liquidation extends REST_Controller
{

	public function __construct()
	{
		parent::__construct();

		if((is_null($this->userInfo) && $this->router->fetch_method() != "set" ) || (!is_null($this->userInfo) && !in_array($this->userInfo->idProfileUser,[1,3])) )
    		$this->response(['error' => "No autorizado"],401);
		$this->load->model('liquidation_model');
	}


	public function index_get() {

		$rs = $this->liquidation_model->get($this->get());

		if (!is_null($rs)) {
			$this->response($rs, 200);
		} else {
			$this->response(array('error' => "NO HAY RESULTADOS"), 404);
		}
	}


    public function set_get() {
        $response = $this->liquidation_model->setLiquidations();

        if(! is_null($response))
        {
            $this->response($response,200);
        }else{
            $this->response(false,500);
        }
    }

}
