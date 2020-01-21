<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Bank extends REST_Controller {

    public function __construct() {
        parent::__construct();
        if(is_null($this->userInfo) || $this->userInfo->idProfileUser!=1) $this->response(['error' => "No autorizado"],401);
        $this->load->model('bank_model');
    }

		//-SERVICIO GET QUE OBTIENE TODOS LOS TIPOS DE CHEQUES------------------------------------*/
    public function index_get() {

        $bank = $this->bank_model->get($this->get('search'));

        if (!is_null($bank)) {
            $this->response($bank, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }

		//-SERVICIO QUE GUARDA UN TIPO DE CHEQUE--------------------------------------------------*/
    public function add_post() {
        if (!$this->post()) {
            $this->response(NULL, 404);
        }

    		$bank = $this->post();
        $bank = $this->bank_model->add($bank);

        if (!is_null($bank)) {
            $this->response(array('bank'=>$bank,'response' => "NUEVO TIPO DE CHEQUE CREADO"), 200);
        } else {
            $this->response(array('error' => "ERROR INESPERADO"), 500);
        }
    }

		//-SERVICIO RETORNA UN TIPO DE CHEQUE POR ID----------------------------------------------*/
    public function find_post() {
        if (!$this->post('idBank')) {
             $this->response(NULL, 404);
        }

        $bank = $this->bank_model->getThis($this->post('idBank'));

        if (!is_null($bank)) {
            $this->response(['bank' => $bank], 200);
        } else {
            $this->response(['error' => 'NO HAY RESULTADOS'], 404);
        }
    }

		//-SERVICIO QUE ACTUALIZA UN TIPO DE CHEQUE-----------------------------------------------*/
    public function update_post() {

        $bank = $this->bank_model->update($this->post());

        if (!is_null($bank)) {
            $this->response(array('response' => "TIPO DE CHEQUE MODIFICADO"), 200);
        } else {
            $this->response(array('error' => "ERROR INESPERADO"), 500);
        }
    }


		//-SERVICIO QUE ELIMINA UN TIPO DE CHEQUE-------------------------------------------------*/
    public function delete_post() {

        $bank = $this->bank_model->erase($this->post()["idBank"]);

        if (!is_null($bank)) {
            $this->response(array('response' => "TIPO DE CHEQUE ELIMINADO"), 200);
        } else {
            $this->response(array('error' => "ERROR INESPERADO"), 500);
        }
    }

}

?>
