<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class CashBox extends REST_Controller {

    public function __construct() {
        parent::__construct();
        if(is_null($this->userInfo) || $this->userInfo->idProfileUser!=1) $this->response(['error' => "No autorizado"],401);
        $this->load->model('cashBox_model');
    }

		//-SERVICIO GET QUE OBTIENE TODOS LOS TIPOS DE INGRESOS------------------------------------*/
    public function index_get() {
			$cashBox = null;
			if(!empty($this->get('balance')))
        $cashBox = $this->cashBox_model->balance($this->get());
      else
        $cashBox = $this->cashBox_model->get($this->get());

        if (!is_null($cashBox)) {
            $this->response($cashBox, 200);
        } else {
            $this->response([], 200);
        }
    }

		//-SERVICIO GET QUE OBTIENE TODOS LOS CHEQUES----------------------------------------------*/
    public function checks_get() {

      $this->load->model('check_model');
			$checks = null;

        $checks = $this->check_model->get($this->get());

        if (!is_null($checks)) {
            $this->response($checks, 200);
        } else {
            $this->response([], 200);
        }
    }

		//-SERVICIO QUE GUARDA UN TIPO DE INGRESO--------------------------------------------------*/
    public function add_post() {
        if (!$this->post()) {
            $this->response(NULL, 404);
        }

    		$cashBox = $this->post();
        $cashBox = $this->cashBox_model->add($cashBox);

        if (!is_null($cashBox)) {
            $this->response(array('cashBox'=>$cashBox,'response' => "NUEVO TIPO DE INGRESO CREADO"), 200);
        } else {
            $this->response(array('error' => "ERROR INESPERADO"), 500);
        }
    }

		//-SERVICIO RETORNA UN TIPO DE INGRESO POR ID----------------------------------------------*/
    public function find_post() {
        if (!$this->post('idCashBox')) {
             $this->response(NULL, 404);
        }
			$cashBox = null;
			if(!empty($this->post('balance')))
        $cashBox = $this->cashBox_model->balance(['box' => $this->post('idCashBox')]);
      else
        $cashBox = $this->cashBox_model->getThis($this->post('idCashBox'));

        if (!is_null($cashBox)) {
            $this->response($cashBox, 200);
        } else {
            $this->response([], 200);
        }
    }

		//-SERVICIO QUE ACTUALIZA UN TIPO DE INGRESO-----------------------------------------------*/
    public function update_post() {

        $cashBox = $this->cashBox_model->update($this->post());

        if (!is_null($cashBox)) {
            $this->response(array('response' => "TIPO DE INGRESO MODIFICADO"), 200);
        } else {
            $this->response(array('error' => "ERROR INESPERADO"), 500);
        }
    }


		//-SERVICIO QUE ELIMINA UN TIPO DE INGRESO-------------------------------------------------*/
    public function delete_post() {

        $cashBox = $this->cashBox_model->erase($this->post()["idCashBox"]);

        if (!is_null($cashBox)) {
            $this->response(array('response' => "TIPO DE INGRESO ELIMINADO"), 200);
        } else {
            $this->response(array('error' => "ERROR INESPERADO"), 500);
        }
    }

}

?>
