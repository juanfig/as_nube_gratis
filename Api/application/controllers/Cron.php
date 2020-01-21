<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';



class Cron extends REST_Controller {

	public function __construct() {
        parent::__construct();
        if(is_null($this->userInfo) || $this->userInfo->idProfileUser!=1) $this->response(['error' => "No autorizado"],401);
        $this->load->model('cron_model');
    }

    /* SERVICIO GET QUE ENVIA MAIL NOTIFICACNDO DE FECHS DE VENCIMIENTO EN FLOTA*/
    public function noticationExpirationFleet_get() {
        $cron = $this->cron_model->noticationExpirationFleet();
        $this->response($cron, 200);
    }


    /* SERVICIO GET QUE ENVIA MAIL NOTIFICACNDO DE RESERVA PROXIMA*/
    public function noticationRervationComing_get() {
        $cron = $this->cron_model->noticationRervationComing();
        $this->response($cron, 200);
    }

    public function notificationExpirationUser_get()
    {
        $cron = $this->cron_model->notificationExpirationUser();
        $this->response($cron, 200);
    }

}
?>
