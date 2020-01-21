<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';


class Cloud extends REST_Controller
{

    public function __construct() {
        parent::__construct();
        if(is_null($this->userInfo) || !in_array($this->userInfo->idProfileUser,[1,3]) ) $this->response(['error' => "No autorizado"],401);
        $this->load->model('cloud_model');
    }

    // SERVISIO QUE LISTA LOS FORMATOS
    public function index_get()
    {
        $cloud = $this->cloud_model->get(null, null);
        if (!is_null($cloud)) {
            $this->response($cloud, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }

    // SERVICIO QUE GUARDA EL FORMATO
    public function add_post()
    {
        if($this->userInfo->idProfileUser!=1) $this->response(['error' => "No autorizado"],401);

        if (!$this->post('cloud')) {
            $this->response(NULL, 404);
        }

        $cloud = $this->cloud_model->add($this->post('cloud'));

        if (!is_null($cloud)) {
            $this->response(array('response' => "NUBE AGREGADA", "idcloud"=>$cloud), 200);
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

        $cloud = null;
        $cloud = $this->cloud_model->get($id);

        if (!is_null($cloud)) {
            $this->response($cloud, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }


    // SERVICIO GET QUE OBTIENE LOS FORMATOS POR FILTRO
    public function search_post()
    {
        $searchFilter = $this->post('filter');

        $cloud = $this->cloud_model->get(null, $searchFilter);

        if (!is_null($cloud)) {
            $this->response($cloud, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }

    /* SERVICIO EDITA UN USUARIOS  */

    public function update_post() {

        if (!$this->post('cloud')) {
            $this->response(NULL, 404);
        }

        $cloud = $this->cloud_model->update($this->post('cloud'));

        if (!is_null($cloud)) {
            $this->response(array('response' => "NUBE EDITADA"), 200);
        } else {
            $this->response(array('error' => "ERROR INESPERADO"), 500);
        }
    }


     /* SERVICIO INACTIVA  UN USUARIOS POR ID */

    public function delete_delete($id) {

        if($this->userInfo->idProfileUser!=1) $this->response(['error' => "No autorizado"],401);

        if (!$id) {
            $this->response(NULL, 404);
        }

        $cloud = null;
        $cloud = $this->cloud_model->delete($id);

        if (!is_null($cloud)) {
            $this->response($cloud, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }


    public function changueStatus_post() {

        if($this->userInfo->idProfileUser!=1) $this->response(['error' => "No autorizado"],401);

        if (!$this->post('cloud')) {
            $this->response(NULL, 404);
        }

        $cloud = $this->cloud_model->changueStatus($this->post('cloud'));

        if (!is_null($cloud)) {
            $this->response(array('response' => "NUBE EDITADA"), 200);
        } else {
            $this->response(array('error' => "ERROR INESPERADO"), 500);
        }
    }


    public function filter_get() {

        $filter = $this->cloud_model->getFilter();

        if (!is_null($filter)) {
            $this->response($filter, 200);
        } else {
            $this->response(array('error' => "ERROR INESPERADO"), 500);
        }
    }



}
?>
