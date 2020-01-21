<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class TypeExpense extends REST_Controller {

    public function __construct() {
        parent::__construct();
        if(is_null($this->userInfo) || $this->userInfo->idProfileUser!=1) $this->response(['error' => "No autorizado"],401);
        $this->load->model('typeExpense_model');
    }

		//-SERVICIO GET QUE OBTIENE TODOS LOS TIPOS DE EGRESOS------------------------------------*/
    public function index_get() {

        $typeExpense = $this->typeExpense_model->get($this->get('search'));

        if (!is_null($typeExpense)) {
            $this->response($typeExpense, 200);
        } else {
            $this->response(['error' => 'NO HAY RESULTADOS'], 404);
        }
    }

		//-SERVICIO QUE GUARDA UN TIPO DE EGRESO--------------------------------------------------*/
    public function add_post() {
        if (!$this->post()) {
            $this->response(NULL, 404);
        }

    		$typeExpense = $this->post();
        $typeExpense = $this->typeExpense_model->add($typeExpense);

        if (!is_null($typeExpense)) {
            $this->response(['typeExpense'=>$typeExpense,'response' => "NUEVO TIPO DE EGRESO CREADO"], 200);
        } else {
            $this->response(['error' => "ERROR INESPERADO"], 500);
        }
    }

		//-SERVICIO RETORNA UN TIPO DE EGRESO POR ID----------------------------------------------*/
    public function find_post() {
        if (!$this->post('idTypeExpense')) {
             $this->response(NULL, 404);
        }

        $typeExpense = $this->typeExpense_model->getThis($this->post('idTypeExpense'));

        if (!is_null($typeExpense)) {
            $this->response(['typeExpense' => $typeExpense], 200);
        } else {
            $this->response(['error' => 'NO HAY RESULTADOS'], 404);
        }
    }

		//-SERVICIO QUE ACTUALIZA UN TIPO DE EGRESO-----------------------------------------------*/
    public function update_post() {

        $typeExpense = $this->typeExpense_model->update($this->post());

        if (!is_null($typeExpense)) {
            $this->response(['response' => "TIPO DE EGRESO MODIFICADO"], 200);
        } else {
            $this->response(['error' => "ERROR INESPERADO"], 500);
        }
    }


		//-SERVICIO QUE ELIMINA UN TIPO DE EGRESO-------------------------------------------------*/
    public function delete_post() {

        $typeExpense = $this->typeExpense_model->erase($this->post()["idTypeExpense"]);

        if (!is_null($typeExpense)) {
            $this->response(['response' => "TIPO DE EGRESO ELIMINADO"], 200);
        } else {
            $this->response(['error' => "ERROR INESPERADO"], 500);
        }
    }

}

?>
