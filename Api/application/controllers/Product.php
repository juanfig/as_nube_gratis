<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';


class Product extends REST_Controller
{

    public function __construct() {
        parent::__construct();
        if(is_null($this->userInfo) || $this->userInfo->idProfileUser!=1) $this->response(['error' => "No autorizado"],401);
        $this->load->model('product_model');
    }

    // SERVISIO QUE LISTA LOS FORMATOS
    public function index_get()
    {
        $product = $this->product_model->get(null, null);
        if (!is_null($product)) {
            $this->response($product, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }

    // SERVICIO QUE GUARDA EL FORMATO
    public function add_post()
    {
        if (!$this->post('product')) {
            $this->response(NULL, 404);
        }

        $product = $this->product_model->add($this->post('product'));

        if (!is_null($product)) {
            $this->response(array('response' => "PRODUCTO AGREGADO", "idProduct"=>$product), 200);
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

        $product = null;
        $product = $this->product_model->get($id);

        if (!is_null($product)) {
            $this->response($product, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }


    // SERVICIO GET QUE OBTIENE LOS FORMATOS POR FILTRO
    public function search_post()
    {
        $searchFilter = $this->post('filter');

        $product = $this->product_model->get(null, $searchFilter);

        if (!is_null($product)) {
            $this->response($product, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }

    /* SERVICIO EDITA UN USUARIOS  */

    public function update_post() {

        if (!$this->post('product')) {
            $this->response(NULL, 404);
        }

        $product = $this->product_model->update($this->post('product'));

        if (!is_null($product)) {
            $this->response(array('response' => "PRODUCTO EDITADO", "idProduct"=>$product), 200);
        } else {
            $this->response(array('error' => "ERROR INESPERADO"), 500);
        }
    }


     /* SERVICIO INACTIVA  UN USUARIOS POR ID */

    public function delete_delete($id) {
        if (!$id) {
            $this->response(NULL, 404);
        }

        $product = null;
        $product = $this->product_model->delete($id);

        if (!is_null($product)) {
            $this->response($product, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }


    public function changueStatus_post() {

        if (!$this->post('product')) {
            $this->response(NULL, 404);
        }

        $product = $this->product_model->changueStatus($this->post('product'));

        if (!is_null($product)) {
            $this->response(array('response' => "PRODUCTO EDITADO", "idProduct"=>$product), 200);
        } else {
            $this->response(array('error' => "ERROR INESPERADO"), 500);
        }
    }



}
?>
