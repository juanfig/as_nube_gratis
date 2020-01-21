<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';


class Country extends REST_Controller
{

    public function __construct() {
        parent::__construct();
        if(is_null($this->userInfo) || $this->userInfo->idProfileUser!=1) $this->response(['error' => "No autorizado"],401);
        $this->load->model('country_model');
    }

    // SERVISIO QUE LISTA LOS FORMATOS
    public function index_get()
    {
        $country = $this->country_model->get(null, null);
        if (!is_null($country)) {
            $this->response($country, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }

    // SERVICIO QUE GUARDA EL FORMATO
    public function add_post()
    {
        if (!$this->post('country')) {
            $this->response(NULL, 404);
        }

        $country = $this->country_model->add($this->post('country'));

        if (!is_null($country)) {
            $this->response(array('response' => "NUBE AGREGADA", "idcountry"=>$country), 200);
        } else {
            $this->response(array('error' => "ERROR INESPERADO"), 500);
        }
    }

    // SERVICIO RETORNA UN FORMATO POR ID
    public function find_get($id)
    {
        if (!$id) {
            $this->response(NULL, 404);
        }

        $country = null;
        $country = $this->country_model->get($id);

        if (!is_null($country)) {
            $this->response($country, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }


    // SERVICIO GET QUE OBTIENE LOS FORMATOS POR FILTRO
    public function search_post()
    {
        $searchFilter = $this->post('filter');

        $country = $this->country_model->get(null, $searchFilter);

        if (!is_null($country)) {
            $this->response($country, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }

    /* SERVICIO EDITA UN USUARIOS  */

    public function update_post() {

        if (!$this->post('country')) {
            $this->response(NULL, 404);
        }

        $country = $this->country_model->update($this->post('country'));

        if (!is_null($country)) {
            $this->response(array('response' => "NUBE EDITADA"), 200);
        } else {
            $this->response(array('error' => "ERROR INESPERADO"), 500);
        }
    }


     /* SERVICIO INACTIVA  UN USUARIOS POR ID */

    public function delete_delete($id) {
        if (!$id) {
            $this->response(NULL, 404);
        }

        $country = null;
        $country = $this->country_model->delete($id);

        if (!is_null($country)) {
            $this->response($country, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }


    public function changueStatus_post() {

        if (!$this->post('country')) {
            $this->response(NULL, 404);
        }
        $country = $this->country_model->changueStatus($this->post('country'));

        if (!is_null($country)) {
            $this->response(array('response' => "NUBE EDITADA"), 200);
        } else {
            $this->response(array('error' => "ERROR INESPERADO"), 500);
        }
    }



}
?>
