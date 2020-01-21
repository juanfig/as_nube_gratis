<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class PaymentForm extends REST_Controller {

    public function __construct() {
        parent::__construct();
        if(is_null($this->userInfo) || $this->userInfo->idProfileUser!=1) $this->response(['error' => "No autorizado"],401);
        $this->load->model('paymentForm_model');
    }

		//-SERVICIO GET QUE OBTIENE TODOS LOS TIPOS DE INGRESOS------------------------------------*/
    public function index_get() {

        $paymentForm = $this->paymentForm_model->get($this->get('search'));

        if (!is_null($paymentForm)) {
            $this->response($paymentForm, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }

		//-SERVICIO QUE GUARDA UN TIPO DE INGRESO--------------------------------------------------*/
    public function add_post() {
        if (!$this->post()) {
            $this->response(NULL, 404);
        }

    		$paymentForm = $this->post();
        $paymentForm = $this->paymentForm_model->add($paymentForm);

        if (!is_null($paymentForm)) {
            $this->response(array('paymentForm'=>$paymentForm,'response' => "NUEVO TIPO DE INGRESO CREADO"), 200);
        } else {
            $this->response(array('error' => "ERROR INESPERADO"), 500);
        }
    }

		//-SERVICIO RETORNA UN TIPO DE INGRESO POR ID----------------------------------------------*/
    public function find_post() {
        if (!$this->post('idPaymentForm')) {
             $this->response(NULL, 404);
        }

        $paymentForm = $this->paymentForm_model->getThis($this->post('idPaymentForm'));

        if (!is_null($paymentForm)) {
            $this->response(['paymentForm' => $paymentForm], 200);
        } else {
            $this->response(['error' => 'NO HAY RESULTADOS'], 404);
        }
    }

		//-SERVICIO QUE ACTUALIZA UN TIPO DE INGRESO-----------------------------------------------*/
    public function update_post() {

        $paymentForm = $this->paymentForm_model->update($this->post());

        if (!is_null($paymentForm)) {
            $this->response(array('response' => "TIPO DE INGRESO MODIFICADO"), 200);
        } else {
            $this->response(array('error' => "ERROR INESPERADO"), 500);
        }
    }


		//-SERVICIO QUE ELIMINA UN TIPO DE INGRESO-------------------------------------------------*/
    public function delete_post() {

        $paymentForm = $this->paymentForm_model->erase($this->post()["idPaymentForm"]);

        if (!is_null($paymentForm)) {
            $this->response(array('response' => "TIPO DE INGRESO ELIMINADO"), 200);
        } else {
            $this->response(array('error' => "ERROR INESPERADO"), 500);
        }
    }

}

?>
