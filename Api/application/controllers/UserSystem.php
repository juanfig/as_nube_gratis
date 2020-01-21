<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class UserSystem extends REST_Controller {

    public function __construct() {
        parent::__construct();

		if((is_null($this->userInfo) && $this->router->fetch_method() != "delete") ||
		  (!is_null($this->userInfo) && !in_array($this->userInfo->idProfileUser,[1,3])) )
    		$this->response(['error' => "No autorizado"],401);

        $this->load->model('usersystem_model');
    }

    /* SERVICIO GET QUE OBTIENE TODO LOS USUARIOS REGISTRADOS */

    public function index_get() {

        $user = $this->usersystem_model->get();
        if (!is_null($user)) {
            $this->response($user, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }

    /* SERVICIO QUE GUARDA UN USUARIOS */

    public function index_post() {

      if($this->userInfo->idProfileUser!=1) $this->response(['error' => "No autorizado"],401);

        if (!$this->post('user')) {
            $this->response(NULL, 404);
        }

        $user = $this->usersystem_model->add($this->post('user'));

        if (!is_null($user)) {
            $this->response(array('response' => "USUARIO SISTEMA AGREGADO "), 200);
        } else {
            $this->response(array('error' => "ERROR INESPERADO"), 500);
        }
    }

    /* SERVICIO RETORNA UN USUARIOS POR ID */

    public function find_get($id) {
        if (!$id) {
            $this->response(NULL, 404);
        }

        $user = null;
        $user = $this->usersystem_model->get($id);

        if (!is_null($user)) {
            $this->response($user, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }

    /* SERVICIO GET QUE OBTIENE LOS USUARIOS POR FILTRO */

    public function search_post() {

        $searchFilter = $this->post('filter');

        $user = $this->usersystem_model->get(null, $searchFilter);

        if (!is_null($user)) {
            $this->response($user, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }

    /* SERVICIO QUE RETORNA LOS FILTROS DE EL MODULO DE USUARIOS */

    public function filterForm_get() {
        $filters = $this->usersystem_model->getFilterForm();

        if (!is_null($filters)) {
            $this->response($filters, 200);
        } else {
            $this->response(array('response' => 'NO HAY RESULTADOS'), 404);
        }
    }

    /* SERVICIO EDITA UN USUARIOS  */

    public function update_post() {

      if($this->userInfo->idProfileUser!=1) $this->response(['error' => "No autorizado"],401);

        if (!$this->post('user')) {
            $this->response(NULL, 404);
        }

        $rs = $this->usersystem_model->update($this->post('user'));

        if (!is_null($rs)) {
            $this->response(array('response' => "USUARIO SISTEMA EDITADO"), 200);
        } else {
            $this->response(array('error' => "ERROR INESPERADO"), 500);
        }
    }


    /* SERVICIO INACTIVA  UN USUARIOS POR ID */

    public function inactive_get($id) {

      if($this->userInfo->idProfileUser!=1) $this->response(['error' => "No autorizado"],401);

        if (!$id) {
            $this->response(NULL, 404);
        }

        $user = null;
        $user = $this->usersystem_model->changueStatus($id, 0);

        if (!is_null($user)) {
            $this->response($user, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }

    /* SERVICIO INACTIVA  UN USUARIOS POR ID */

    public function delete_delete($id) {
      if($this->userInfo->idProfileUser!=1) $this->response(['error' => "No autorizado"],401);

        if (!$id) {
            $this->response(NULL, 404);
        }

        $user = null;
        $user = $this->usersystem_model->changueStatus($id, -1);

        if (!is_null($user)) {
            $this->response($user, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }


    // SERVICIO QUE GUARDA EL FORMATO
    public function add_post()
    {
        $rs = $this->usersystem_model->addUser($this->post('user'));

        if (!is_null($rs)) {

            $this->response(array('response' => "Usuario agregado", "idUser"=>$rs), 200);
        } else {
            $this->response(array('error' => "ERROR INESPERADO"), 500);
        }
    }

    public function updateUser_post() {

        if (!$this->post('user')) {
            $this->response(NULL, 404);
        }

        $rs = $this->usersystem_model->updateUser($this->post('user'));

        if (!is_null($rs)) {
            $this->response(array('response' => "Usuario Actualizado"), 200);
        } else {
            $this->response(array('error' => "ERROR INESPERADO"), 500);
        }
    }


    public function changueStatusUser_post() {

        if (!$this->post('user')) {
            $this->response(NULL, 404);
        }

        $user = $this->usersystem_model->changueStatusUser($this->post('user'));

        if (!is_null($user)) {
            $this->response(array('response' => "Usuario Editado"), 200);
        } else {
            $this->response(array('error' => "ERROR INESPERADO"), 500);
        }
    }

    public function userType_get($type)
    {
        if($this->userInfo->idProfileUser!=1) $this->response(['error' => "No autorizado"],401);

        if (!$type) {
            $this->response(NULL, 404);
        }

        $users = null;
        $users = $this->usersystem_model->getTypeName($type);

        if (!is_null($users)) {
            $this->response($users, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }




}

?>