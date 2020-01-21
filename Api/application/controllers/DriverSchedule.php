<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class DriverSchedule extends REST_Controller {

    public function __construct() {
        parent::__construct();
        if(is_null($this->userInfo) || $this->userInfo->idProfileUser!=1) $this->response(['error' => "No autorizado"],401);
        $this->load->model('driverschedule_model');
    }

    /* SERVICIO GET QUE OBTIENE TODO LOS HORARIO REGISTRADOS */


    public function index_get() {

        $user = $this->driverschedule_model->get();

        if (!is_null($user)) {
            $this->response($user, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }
    
    

    /* SERVICIO QUE GUARDA UN HORARIO */

    public function index_post() {
          
        if (!$this->post('driver')) {
           $this->response(NULL, 404);
        }

        $user = $this->driverschedule_model->add($this->post('driver'));

        if (!is_null($user)) {
            $this->response(array('response' => "HORARIO DE CHOFER AGREGADO "), 200);
        } else {
            $this->response(array('error' => "ERROR INESPERADO"), 500);
        }
    }
    
   
	

    /* SERVICIO RETORNA UN HORARIO POR ID */
    
    public function find_get($id) {
        
        if (!$id) {
            $this->response(NULL, 404);
        }

        $user = null;
        $user = $this->driverschedule_model->get($id, null);

        if (!is_null($user)) {
            $this->response($user, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }

    /* SERVICIO GET QUE OBTIENE LOS HORARIO POR FILTRO */

    public function search_post() {
        
        
        $searchFilter = $this->post('filter');
        
        $user = $this->driverschedule_model->get(null, $searchFilter);

        if (!is_null($user)) {
            $this->response($user, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }

    /* SERVICIO QUE RETORNA LOS FILTROS DE EL MODULO DE USUARIOS */

    public function filterForm_get() {
        $filters = $this->driverschedule_model->getFilterForm();

        if (!is_null($filters)) {
            $this->response($filters, 200);
        } else {
            $this->response(array('response' => 'NO HAY RESULTADOS'), 404);
        }
    }

    /* SERVICIO EDITA UN HORARIO  */
    
    public function update_post() {

        if (!$this->post('driver')) {
            $this->response(NULL, 404);
        }

        $rs = $this->driverschedule_model->update($this->post('driver'));

        if (!is_null($rs)) {
            $this->response(array('response' => "HORARIO DE CHOFER EDITADO"), 200);
        } else {
            $this->response(array('error' => "ERROR INESPERADO"), 500);
        }
    }

    

    /* SERVICIO INACTIVA  UN USUARIOS POR ID */

    
    public function delete_delete($id) {
        if (!$id) {
            $this->response(NULL, 404);
        }

        $user = null;
        $user = $this->driverschedule_model->changueStatus($id, -1);

        if (!is_null($user)) {
            $this->response($user, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }
    
    
    public function active_get($id) {
        if (!$id) {
            $this->response(NULL, 404);
        }

        $user = null;
        $user = $this->driverschedule_model->changueStatus($id, 1);

        if (!is_null($user)) {
            $this->response($user, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }

    public function inactive_get($id) {
        if (!$id) {
            $this->response(NULL, 404);
        }

        $user = null;
        $user = $this->driverschedule_model->changueStatus($id, 0);

        if (!is_null($user)) {
            $this->response($user, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }

}

?>