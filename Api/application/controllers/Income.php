<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Income extends REST_Controller {

    public function __construct() {
        parent::__construct();
        if(is_null($this->userInfo) || $this->userInfo->idProfileUser!=1) $this->response(['error' => "No autorizado"],401);
        $this->load->model('income_model');
        $this->load->model('check_model');
    }

		//-SERVICIO GET QUE OBTIENE TODOS LOS INGRESOS---------------------------------------------*/
    public function index_get() {

        $income = $this->income_model->get($this->get());

        if (!is_null($income)) {
            $this->response($income, 200);
        } else {
            $this->response([], 200);
        }
    }

		//-SERVICIO QUE GUARDA UN INGRESO--------------------------------------------------*/
    public function add_post() {
        if (!$this->post()) {
            $this->response(NULL, 404);
        }

    		$checks = $this->post('checks');
    		$income = $this->post('income');
    		$income['dateIncome'] =  date('Y-m-d H:i:s');
        $income = $this->income_model->add($income);

        if (!is_null($income)) {
						if (!empty($checks)) {
							foreach($checks as $check){

								$this->check_model->add($check,
                                ["table"=>'tb_check_income',
                                "check"=>[
                                    'idIncomeKf'=>$income]
                                    ]);
							}
						}
            $this->response(array('income'=>$income,'response' => "NUEVO TIPO DE INGRESO CREADO"), 200);

        } else {
            $this->response(array('error' => "ERROR INESPERADO"), 500);
        }
    }

		//-SERVICIO RETORNA UN TIPO DE INGRESO POR ID--------------------------------------*/
    public function find_post() {
        if (!$this->post('idIncome')) {
             $this->response(NULL, 404);
        }
        $post_data=$this->post();
        $income = $this->income_model->getThis($post_data['idIncome']);
        $checks = $this->check_model->get($post_data); // cheques que pertenecen a un egreso
        if (!is_null($income)) {
            $this->response(['income' => $income,'checks'=> $checks = (!is_null($checks)) ? $checks : null ], 200);
        } else {
            $this->response(['error' => 'NO HAY RESULTADOS'], 404);
        }
    }

		//-SERVICIO QUE ACTUALIZA UN INGRESO-----------------------------------------------*/
    public function update_post() {

        $income = $this->income_model->update($this->post());

        if (!is_null($income)) {
            $this->response(array('response' => "TIPO DE INGRESO MODIFICADO"), 200);
        } else {
            $this->response(array('error' => "ERROR INESPERADO"), 500);
        }
    }


		//-SERVICIO QUE ELIMINA UN INGRESO-------------------------------------------------*/
    public function delete_post() {

        $income = $this->income_model->erase($this->post()["idIncome"]);

        if (!is_null($income)) {
            $this->response(array('response' => "TIPO DE INGRESO ELIMINADO"), 200);
        } else {
            $this->response(array('error' => "ERROR INESPERADO"), 500);
        }
    }

}

?>
