<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class TypeCheck extends REST_Controller {

    public function __construct() {
        parent::__construct();
        if(is_null($this->userInfo) || $this->userInfo->idProfileUser!=1) $this->response(['error' => "No autorizado"],401);
        $this->load->model('typeCheck_model');
    }

		//-SERVICIO GET QUE OBTIENE TODOS LOS TIPOS DE CHEQUES------------------------------------*/
    public function index_get() {

        $typeCheck = $this->typeCheck_model->get($this->get('search'));

        if (!is_null($typeCheck)) {
            $this->response($typeCheck, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }

		//-SERVICIO QUE GUARDA UN TIPO DE CHEQUE--------------------------------------------------*/
    public function add_post() {
        if (!$this->post()) {
            $this->response(NULL, 404);
        }

    		$typeCheck = $this->post();
        $typeCheck = $this->typeCheck_model->add($typeCheck);

        if (!is_null($typeCheck)) {
            $this->response(array('typeCheck'=>$typeCheck,'response' => "NUEVO TIPO DE CHEQUE CREADO"), 200);
        } else {
            $this->response(array('error' => "ERROR INESPERADO"), 500);
        }
    }

		//-SERVICIO RETORNA UN TIPO DE CHEQUE POR ID----------------------------------------------*/
    public function find_post() {
        if (!$this->post('idTypeCheck')) {
             $this->response(NULL, 404);
        }

        $typeCheck = $this->typeCheck_model->getThis($this->post('idTypeCheck'));

        if (!is_null($typeCheck)) {
            $this->response(['typeCheck' => $typeCheck], 200);
        } else {
            $this->response(['error' => 'NO HAY RESULTADOS'], 404);
        }
    }

		//-SERVICIO QUE ACTUALIZA UN TIPO DE CHEQUE-----------------------------------------------*/
    public function update_post() {

        $typeCheck = $this->typeCheck_model->update($this->post());

        if (!is_null($typeCheck)) {
            $this->response(array('response' => "TIPO DE CHEQUE MODIFICADO"), 200);
        } else {
            $this->response(array('error' => "ERROR INESPERADO"), 500);
        }
    }


		//-SERVICIO QUE ELIMINA UN TIPO DE CHEQUE-------------------------------------------------*/
    public function delete_post() {

        $typeCheck = $this->typeCheck_model->erase($this->post()["idTypeCheck"]);

        if (!is_null($typeCheck)) {
            $this->response(array('response' => "TIPO DE CHEQUE ELIMINADO"), 200);
        } else {
            $this->response(array('error' => "ERROR INESPERADO"), 500);
        }
    }

}

?>
