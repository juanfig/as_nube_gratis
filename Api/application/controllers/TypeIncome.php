<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class TypeIncome extends REST_Controller {

    public function __construct() {
        parent::__construct();
        if(is_null($this->userInfo) || $this->userInfo->idProfileUser!=1) $this->response(['error' => "No autorizado"],401);
        $this->load->model('typeIncome_model');
    }

		//-SERVICIO GET QUE OBTIENE TODOS LOS TIPOS DE INGRESOS------------------------------------*/
    public function index_get() {

        $typeIncome = $this->typeIncome_model->get($this->get('search'));

        if (!is_null($typeIncome)) {
            $this->response($typeIncome, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }

		//-SERVICIO QUE GUARDA UN TIPO DE INGRESO--------------------------------------------------*/
    public function add_post() {
        if (!$this->post()) {
            $this->response(NULL, 404);
        }

    		$typeIncome = $this->post();
        $typeIncome = $this->typeIncome_model->add($typeIncome);

        if (!is_null($typeIncome)) {
            $this->response(array('typeIncome'=>$typeIncome,'response' => "NUEVO TIPO DE INGRESO CREADO"), 200);
        } else {
            $this->response(array('error' => "ERROR INESPERADO"), 500);
        }
    }

		//-SERVICIO RETORNA UN TIPO DE INGRESO POR ID----------------------------------------------*/
    public function find_post() {
        if (!$this->post('idTypeIncome')) {
             $this->response(NULL, 404);
        }

        $typeIncome = $this->typeIncome_model->getThis($this->post('idTypeIncome'));

        if (!is_null($typeIncome)) {
            $this->response(['typeIncome' => $typeIncome], 200);
        } else {
            $this->response(['error' => 'NO HAY RESULTADOS'], 404);
        }
    }

		//-SERVICIO QUE ACTUALIZA UN TIPO DE INGRESO-----------------------------------------------*/
    public function update_post() {

        $typeIncome = $this->typeIncome_model->update($this->post());

        if (!is_null($typeIncome)) {
            $this->response(array('response' => "TIPO DE INGRESO MODIFICADO"), 200);
        } else {
            $this->response(array('error' => "ERROR INESPERADO"), 500);
        }
    }


		//-SERVICIO QUE ELIMINA UN TIPO DE INGRESO-------------------------------------------------*/
    public function delete_post() {

        $typeIncome = $this->typeIncome_model->erase($this->post()["idTypeIncome"]);

        if (!is_null($typeIncome)) {
            $this->response(array('response' => "TIPO DE INGRESO ELIMINADO"), 200);
        } else {
            $this->response(array('error' => "ERROR INESPERADO"), 500);
        }
    }

}

?>
