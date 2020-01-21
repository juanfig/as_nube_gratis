<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Driver extends REST_Controller {

    public function __construct() {
        parent::__construct();

		    if((is_null($this->userInfo) && !in_array($this->router->fetch_method(), [
    		"index","update","delete" ]) ) || (!is_null($this->userInfo) && !in_array($this->userInfo->idProfileUser,[1,3])) )
    		$this->response(['error' => "No autorizado"],401);
        $this->load->model('driver_model');
    }


    /* SERVICIO QUE GUARDA UN  CHOFER */
   public function index_post() {
       $idUser = $this->driver_model->add($this->post('driver'));

        if (!is_null($idUser)) {
          $this->response($idUser, 200);
        } else {
            $this->response(array('error' => "ERROR INESPERADO"), 500);
        }
    }

    /* SERVICIO EDITA UN CHOFEER  */
    public function update_post() {

        if (!$this->post('driver')) {
            $this->response(NULL, 404);
        }
        $rs = $this->driver_model->update($this->post('driver'));

        if (!is_null($rs)) {
            $this->response($rs, 200);
        } else {
            $this->response(array('error' => "ERROR INESPERADO"), 500);
        }
    }

    /* SERVICIO INACTIVA  UN USUARIOS POR ID */

    public function delete_delete($id) {
        
        if (!$id) {
            $this->response(NULL, 404);
        }
        $user = $this->driver_model->changueStatus($id, -1);

        if (!is_null($user)) {
            $this->response($user, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }


}

?>
