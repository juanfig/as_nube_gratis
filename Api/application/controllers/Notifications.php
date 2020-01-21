<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Notifications extends REST_Controller {

    public function __construct() {
        parent::__construct();
        if(is_null($this->userInfo)) $this->response(['error' => "No autorizado"],401);
        $this->load->model('notifications_model');
    }

    /* SERVICIO GET QUE OBTIENE LAS NOTIFICACIONE POR ID DE USUARIO  */
    public function find_get($id) {
        if (!$id) {
            $this->response(NULL, 404);
        }

        $notifications = null;
        $notifications = $this->notifications_model->get($id);

        if (!is_null($notifications)) {
            $this->response($notifications, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }

    public function all_get($id) {
        if (!$id) {
            $this->response(NULL, 404);
        }

        $notifications = null;
        $notifications = $this->notifications_model->get($id, 1);

        if (!is_null($notifications)) {
            $this->response($notifications, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }

    public function search_post()
    {
        $searchFilter = $this->post('filter');

        $notifications = $this->notifications_model->get($searchFilter['idUser'], 1, $searchFilter);

        if (!is_null($notifications)) {
            $this->response($notifications, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }


    /* SERVICIO GET QUE OBTIENE LAS NOTIFICACIONE POR ID DE USUARIO  */
    public function read_get($id,$idUser) {
        if (!$id) {
            $this->response(NULL, 404);
        }

        $notifications = null;
        $notifications = $this->notifications_model->read($id);

        if (!is_null($notifications)) {
            $notifications = $this->notifications_model->get($idUser);

            if (!is_null($notifications)) {
                $this->response($notifications, 200);
            } else {
                $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
            }

        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }

    /* SERVICIO GET QUE OBTIENE LAS NOTIFICACIONE POR ID DE USUARIO  */
    public function readAll_get($idUser) {
        if (!$idUser) {
            $this->response(NULL, 404);
        }

        $notifications = null;
        $notifications = $this->notifications_model->readAll($idUser);

        if (!is_null($notifications)) {
            $notifications = $this->notifications_model->get($idUser);

            if (!is_null($notifications)) {
                $this->response($notifications, 200);
            } else {
                $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
            }

        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }

    public function notificateChangeDriver_get()
    {
        $notifications = $this->notifications_model->notificateChangeDriver();

        if (!is_null($notifications)) {
            $this->response($notifications, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }


    }

    public function notificateChangeClient_get()
    {
        $notifications = $this->notifications_model->notificateChangeClient();

        if (!is_null($notifications)) {
            $this->response($notifications, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }


    }

}
?>
