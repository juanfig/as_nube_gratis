<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class UserCompany extends REST_Controller {

    public function __construct() {
        parent::__construct();
		if((is_null($this->userInfo) && !in_array($this->router->fetch_method(), [
    		"index","updatePassaguerUser","changeStatusPassaguerUser"]) ) || (!is_null($this->userInfo) && !in_array($this->userInfo->idProfileUser,[1,3])) )
    		$this->response(['error' => "No autorizado"],401);
        $this->load->model('usercompany_model');
    }

    /* SERVICIO GET QUE OBTIENE TODO LOS USUARIOS REGISTRADOS */

    public function index_get($id) {

        $user = $this->usercompany_model->get($id);
        if (!is_null($user)) {
            $this->response($user, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }

    /* SERVICIO QUE GUARDA UN USUARIOS */

    public function index_post() {
        if (!$this->post('user')) {
            $this->response(NULL, 404);
        }


        $idUser = $this->usercompany_model->add($this->post('user'));

        if (!is_null($idUser)) {
           $this->response($idUser, 200);
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
        $user = $this->usercompany_model->get(null, $id);

        if (!is_null($user)) {
            $this->response($user, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }

    /* SERVICIO GET QUE OBTIENE LOS USUARIOS POR FILTRO */

    public function search_post() {

        $searchFilter = $this->post('filter');

        $user = $this->usercompany_model->get($searchFilter["idCompany"], null, $searchFilter);

        if (!is_null($user)) {
            $listUser = null;
            foreach ($user as $item) {

                if ($item['idCompanyClientKf'] == $searchFilter["idCompany"] && $item['idStatusUserCompany'] != -1) {
                    $listUser[] = $item;
                }
            }
            if (!is_null($listUser)) {
                $this->response($listUser, 200);
            } else {
                $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
            }
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }

    /* SERVICIO QUE RETORNA LOS FILTROS DE EL MODULO DE USUARIOS */

    public function filterForm_get($id) {
        $filters = $this->usercompany_model->getFilterForm($id);

        if (!is_null($filters)) {
            $this->response($filters, 200);
        } else {
            $this->response(array('response' => 'NO HAY RESULTADOS'), 404);
        }
    }

    /* SERVICIO EDITA UN USUARIOS  */

    public function update_post() {

        if (!$this->post('user')) {
            $this->response(NULL, 404);
        }

        $rs = $this->usercompany_model->update($this->post('user'));

        if (!is_null($rs)) {
            $this->response(array('response' => "USUARIO EMPRESA EDITADO"), 200);
        } else {
            $this->response(array('error' => "ERROR INESPERADO"), 500);
        }
    }

    /* SERVICIO INACTIVA  UN USUARIOS POR ID */

    public function inactive_get($id) {
        if (!$id) {
            $this->response(NULL, 404);
        }

        $user = null;
        $user = $this->usercompany_model->changueStatus($id, 0);

        if (!is_null($user)) {
            $this->response($user, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }

    /* SERVICIO INACTIVA  UN USUARIOS POR ID */

    public function delete_delete($id) {
        
        if (!$id) {
            $this->response(NULL, 404);
        }

        $user = null;
        $user = $this->usercompany_model->changueStatus($id, -1);

        if (!is_null($user)) {
            $this->response($user, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }

    public function updatePassaguerUser_post(){
        if(!$this->post('user')){
            $this->response(NULL, 404);
        }

        $pasaguer = $this->usercompany_model->updatePasaguer($this->post('user'));

        if($pasaguer !== -1)
        {
            $this->response($pasaguer,200);
        }else
        {
            $this->response(array($pasaguer),500);
        }
    }

    public function changeStatusPassaguerUser_post(){
        if(!$this->post('user')){
            $this->response(NULL, 404);
        }

        $pasaguer = $this->usercompany_model->changeStatusPasaguer($this->post('user'));

        if($pasaguer !== -1)
        {
            $this->response($pasaguer,200);
        }else
        {
            $this->response(array($pasaguer),500);
        }
    }

}

?>
