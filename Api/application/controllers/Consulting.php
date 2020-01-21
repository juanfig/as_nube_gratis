<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Consulting extends REST_Controller {

    public function __construct() {
        parent::__construct();
        if(is_null($this->userInfo) || $this->userInfo->idProfileUser!=1) $this->response(['error' => "No autorizado"],401);
        $this->load->model('consulting_model');
    }

		//-SERVICIO GET QUE OBTIENE TODAS LAS CONSULTORIAS DE UN USUARIO--------------------------*/
    public function index_get() {

        $consulting = $this->consulting_model->get(['idUser'=>$this->get('idUser')]);

        if (!is_null($consulting)) {
            $this->response($consulting, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }

		//-SERVICIO QUE GUARDA UNA CONSULTORIA----------------------------------------------------*/
    public function add_post() {
        if (!$this->post()) {
            $this->response(NULL, 404);
        }
					$params=[
						'idUser'=>$this->post('idUserKf'),
						'idAgency'=>$this->post('idAgencyKf'),
						'date'=>date('Y-m-d').'%'
					];

        if (!empty( $this->consulting_model->get($params))) {
            $this->response(array('error' => "CONSULTORIA YA CREADA CON IGUALES: USUARIO, AGENCIA Y FECHA"), 401);
        }

    		$consulting = $this->post();
    		$consulting["code"] = uniqid();
        $consulting = $this->consulting_model->add($consulting);

        if (!is_null($consulting)) {

        		$this->consulting_model->update(
        		['idConsulting'=>$consulting, 'code'=>str_pad($consulting, 8, "0", STR_PAD_LEFT)]
        		);

            $this->response(array('consulting'=>$consulting,'response' => "NUEVA CONSULTORIA CREADA"), 200);
        } else {
            $this->response(array('error' => "ERROR INESPERADO"), 500);
        }
    }

		//-SERVICIO RETORNA UNA CONSULTORIA POR SU ID---------------------------------------------*/
    public function find_post() {
        if (!$this->post('idConsulting') || !$this->post('idUser')) {
             $this->response(NULL, 404);
        }

        $consulting = $this->consulting_model->getThis($this->post('idUser'),$this->post('idConsulting'));

        if (!is_null($consulting)) {
            $this->response($consulting, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }

    public function findWithConsultant_get($idConsulting) {
        $consulting = $this->consulting_model->getThisWithConsultant($idConsulting);

        if (!is_null($consulting)) {
            $this->response($consulting, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }

		//-SERVICIO CAMBIAR STATUS DE LA CONSULTORIA----------------------------------------------*/
    public function changeStatus_post() {

        if (!$this->post('idUser')) {
            $this->response(NULL, 404);
        }

        $result = $this->consulting_model->changeStatus($this->post());

        if (!is_null($result)) {
            $this->response(array('response' => "SOLICITUD DE SOPORTE EDITADO"), 200);
        } else {
            $this->response(array('error' => "ERROR INESPERADO"), 500);
        }
    }

		//-SERVICIO QUE GUARDA ACTUALIZA UNA CONSULTORIA------------------------------------------*/
    public function update_post() {

        $consulting = $this->consulting_model->update($this->post());

        if (!is_null($consulting)) {
            $this->response(array('response' => "CONSULTORIA MODIFICADA"), 200);
        } else {
            $this->response(array('error' => "ERROR INESPERADO"), 500);
        }
    }

}

?>
