<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class SupportMessage extends REST_Controller {

    public function __construct() {
        parent::__construct();
        if(is_null($this->userInfo) || $this->userInfo->idProfileUser!=1) $this->response(['error' => "No autorizado"],401);
        $this->load->model('supportmessage_model');
    }

    /* SERVICIO GET QUE OBTIENE TODO LOS REGISTROS */

    public function index_get() {

        $support = $this->supportmessage_model->get();
        if (!is_null($support)) {
            $this->response($support, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }

    // /* SERVICIO QUE GUARDA UN MENSAJE DE SOPORTE */

    public function add_post() {
        echo "string";
        // if (!$this->post('support_message')) {
        //     $this->response(NULL, 404);
        // }

        // $support = $this->supportmessage_model->add($this->post('support_message'));

        // if (!is_null($support)) {
        //     $this->response(array('response' => "MENSAJE DE SOPORTE AGREGADO"), 200);
        // } else {
        //     $this->response(array('error' => "ERROR INESPERADO"), 500);
        // }
    }

    // /* SERVICIO RETORNA UN SOPORTE POR ID */

    // public function find_get($id) {
    //     if (!$id) {
    //         $this->response(NULL, 404);
    //     }

    //     $support = null;
    //     $support = $this->supportmessage_model->get($id);

    //     if (!is_null($support)) {
    //         $this->response($support, 200);
    //     } else {
    //         $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
    //     }
    // }

    // /* SERVICIO GET QUE OBTIENE LOS SOPORTES POR FILTRO */

    public function search_post() {

        $searchFilter = $this->post('filter');

        $support = $this->supportmessage_model->get(null, $searchFilter);

        if (!is_null($support)) {
            $this->response($support, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }

    // public function searchByIdAgency_post() {

    //     $searchFilter = $this->post('filter');
    //     $idAgency = $this->post('idAgencyKf');

    //     $support = $this->supportmessage_model->get(null, $searchFilter, $idAgency);

    //     if (!is_null($support)) {
    //         $this->response($support, 200);
    //     } else {
    //         $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
    //     }
    // }

    // /* SERVICIO QUE RETORNA LOS FILTROS DE EL MODULO DE SOPORTES */

    // public function filterForm_get() {
    //     $filters = $this->supportmessage_model->getFilterForm();

    //     if (!is_null($filters)) {
    //         $this->response($filters, 200);
    //     } else {
    //         $this->response(array('response' => 'NO HAY RESULTADOS'), 404);
    //     }
    // }

    // /* SERVICIO CAMBIAR STATUS DEL SOPORTE  */

    // public function changeStatus_post() {

    //     if (!$this->post('support')) {
    //         $this->response(NULL, 404);
    //     }

    //     $rs = $this->supportmessage_model->changeStatus($this->post('support'));

    //     if (!is_null($rs)) {
    //         $this->response(array('response' => "SOLICITUD DE SOPORTE EDITADO"), 200);
    //     } else {
    //         $this->response(array('error' => "ERROR INESPERADO"), 500);
    //     }
    // }

    // /* SERVICIO INACTIVA  UN SOPORTES POR ID */

    // public function inactive_get($id) {
    //     if (!$id) {
    //         $this->response(NULL, 404);
    //     }

    //     $support = null;
    //     $support = $this->supportmessage_model->changueStatus($id, 0);

    //     if (!is_null($support)) {
    //         $this->response($support, 200);
    //     } else {
    //         $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
    //     }
    // }

    // /* SERVICIO INACTIVA  UN SOPORTES POR ID */

    // public function delete_delete($id) {
    //     if (!$id) {
    //         $this->response(NULL, 404);
    //     }

    //     $support = null;
    //     $support = $this->supportmessage_model->changueStatus($id, -1);

    //     if (!is_null($support)) {
    //         $this->response($support, 200);
    //     } else {
    //         $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
    //     }
    // }

    // /* SERVICIO QUE GUARDA UN SOPORTE */

    // public function update_post() {
    //     if (!$this->post('support')) {
    //         $this->response(NULL, 404);
    //     }

    //     $support = $this->supportmessage_model->update($this->post('support'));

    //     if (!is_null($support)) {
    //         $this->response(array('response' => "SOLICITUD DE SOPORTE EDITADA"), 200);
    //     } else {
    //         $this->response(array('error' => "ERROR INESPERADO"), 500);
    //     }
    // }

}

?>