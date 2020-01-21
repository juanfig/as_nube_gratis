<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Support extends REST_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('support_model');
    }

    /* SERVICIO GET QUE OBTIENE TODO LOS REGISTROS */

    public function index_get() {

        $support = $this->support_model->get();
        if (!is_null($support)) {
            $this->response($support, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }

    public function agencyDetail_get($id)
    {
        $support = $this->support_model->supportsAgency($id);
        if (!is_null($support)) {
            $this->response($support, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }

    /* SERVICIO QUE GUARDA UN SOPORTE */

    public function add_post() {
        if (!$this->post('support')) {
            $this->response(NULL, 404);
        }

        $consulting = false;
        if (!empty($this->post('idConsultingKf'))) {

            $consulting =  $this->post('idConsultingKf');

        }

        $support = $this->support_model->add($this->post('support'));

        if (!is_null($support)) {

            if ($consulting){

                $consulting = $this->support_model->addConsulting(['idTicketKf'=>$support,'idConsultingKf'=>$consulting])
                                ? 'TICKET AGREGADO' : 'ERROR: NO SE PUDO RELACIONAR EL TICKET CREADO';
            }

            $this->response(array('response' => !$consulting ? "SOLICITUD DE SOPORTE AGREGADO" : $consulting), 200);
        } else {
            $this->response(array('error' => "ERROR INESPERADO"), 500);
        }
    }


    public function addMessage_post() {


        $support = $this->support_model->addMessage($this->post('support_message'));

        if (!is_null($support)) {

            $support = $this->supportMessageNotification($this->post('support_message_email'));

            $this->response(array('response' => "MENSAJE AGREGADO"), 200);
        } else {
            $this->response(array('error' => "ERROR INESPERADO"), 500);
        }

    }


    public function supportMessageNotification($mail){

        $config = array(
            'protocol' => 'smtp',
            'smtp_host' => 'smtp.googlemail.com',
            'smtp_user' => 'remisasnube@gmail.com', 
            'smtp_pass' => 'AdMg1210#*', 
            'smtp_port' => '465',
            'smtp_crypto' => 'ssl',
            'mailtype' => 'html',
            'wordwrap' => TRUE,
            'charset' => 'utf-8'
            );
   
           $mail = $this->post('support_email');

  
           $this->load->library('email', $config);
           $this->email->set_newline("\r\n");
           $this->email->from('remisasnube@gmail.com');
           $this->email->subject('Se ha enviado un mensaje en base al ticket '.$mail['codeSupport']);
   
           $cuerpo = "";
           $cuerpo .=  "Nombre de Usuario:".$mail['fullNameSupport']."<br> ";
           $cuerpo .=  "Correo de Usuario:".$mail['emailResponSupport']."<br> ";
           $cuerpo .=  "-------------------------------"."<br> ";
           $cuerpo .=  "Mensaje:<br>".$mail['message']."<br> ";
           $cuerpo .=  "-------------------------------"."<br> ";
     
           $this->email->message($cuerpo);
   
           $this->email->to('tonymanyula@gmail.com,jorguti58@gmail.com,valentina@apreciasoft.com,remisasnube@gmail.com,ruben@apreciasoft.com,soporte@apreciasoft.com');
   
           if($this->email->send(FALSE)){
               $this->load->library('email', $config);
               $this->email->set_newline("\r\n");
               $this->email->from('remisasnube@gmail.com');
               $this->email->subject('¡Has recibido un mensaje!');
               $cuerpo =  "Hola,<br>";
               $cuerpo.= "Has recibido el siguiente mensaje relacionado al ticket . ".$mail['codeSupport']."<br><br>";
               $cuerpo .=  "-------------------------------"."<br> ";
               $cuerpo .=  "Mensaje:<br>".$mail['message']."<br> ";
               $cuerpo .=  "-------------------------------"."<br> ";    
               $cuerpo.=  "Estamos a su entera disposición,<br>";
               $cuerpo.=  "Equipo de Soporte!<br>";
               $cuerpo.=  "ApreciaSoft Software <br>";
               $cuerpo.=  "soporte@apreciasoft.com";
               $this->email->message($cuerpo);
               $this->email->to($mail['emailResponSupport'].','.$mail['emailTwoResponSupport']);
   
               if(!$this->email->send(FALSE)){
                   $this->response($this->email->print_debugger(array('headers')),500);
               }
           }else {
               $this->response($this->email->print_debugger(array('headers')),500);
           }
        
    }


    /* SERVICIO RETORNA UN SOPORTE POR ID */

    public function find_get($id) {
        if (!$id) {
            $this->response(NULL, 404);
        }

        $support = null;
        $support = $this->support_model->get($id);

        if (!is_null($support)) {
            $this->response($support, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }
    /* SERVICIO RETORNA UN SOPORTE POR ID */

    public function filterList_get() {

        $support = $this->support_model->getfilterList();

        if (!is_null($support)) {
            $this->response($support, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }

    public function findin_post() {

      $id_support = $this->post('id_support');
        $support = null;
        $support = $this->support_model->get($id_support);

        if (!is_null($support)) {
            $this->response($support, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }

    public function findLast_post() {

        $idAgencyKf = $this->post('idAgencyKf');
        $support = null;
        $support = $this->support_model->getLast($idAgencyKf);

        if (!is_null($support)) {
            $this->response($support, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }

    /* SERVICIO GET QUE OBTIENE LOS SOPORTES POR FILTRO */

    public function search_post() {

        $search  = $this->post('filter');

        $support = $this->support_model->get(null, $search);

        if (!is_null($support)) {
            $this->response($support, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }

    public function searchByIdAgency_post() {

        $searchFilter = $this->post('filter');
        $idAgency = $this->post('idAgencyKf');

        $support = $this->support_model->get(null, $searchFilter, $idAgency);

        if (!is_null($support)) {
            $this->response($support, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }

    public function byIdAgency_get($idAgency) {

                $idAgency = $idAgency;

                $support = $this->support_model->get(null, null, $idAgency);

                if (!is_null($support)) {
                    $this->response($support, 200);
                } else {
                    $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
                }
            }


    public function searchByIdAgency2_post() {

        $searchFilter = $this->post('filter');
        $id_support = $this->post('id_support');

        $support = $this->support_model->getMessagesByIdSupport($id_support);

        if (!is_null($support)) {
            $this->response($support, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }


    public function supportMessages_post() {

       $id = $this->post('id');

        $support = $this->support_model->getMessagesByIdSupport($id);

        if (!is_null($support)) {
            $this->response($support, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }


    public function supportMessages_get($id) {



        $support = $this->support_model->getMessagesByIdSupport($id);

        if (!is_null($support)) {
            $this->response($support, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }


    /* SERVICIO QUE RETORNA LOS FILTROS DE EL MODULO DE SOPORTES */

    public function filterForm_get() {
        $filters = $this->support_model->getFilterForm();

        if (!is_null($filters)) {
            $this->response($filters, 200);
        } else {
            $this->response(array('response' => 'NO HAY RESULTADOS'), 404);
        }
    }

    /* SERVICIO CAMBIAR STATUS DEL SOPORTE  */

    public function changeStatus_post() {

        if (!$this->post('support')) {
            $this->response(NULL, 404);
        }

        $rs = $this->support_model->changeStatus($this->post('support'));

        if (!is_null($rs)) {
            $this->response(array('response' => "SOLICITUD DE SOPORTE EDITADO"), 200);
        } else {
            $this->response(array('error' => "ERROR INESPERADO"), 500);
        }
    }

    /* SERVICIO INACTIVA  UN SOPORTES POR ID */

    public function inactive_get($id) {
        if (!$id) {
            $this->response(NULL, 404);
        }

        $support = null;
        $support = $this->support_model->changueStatus($id, 0);

        if (!is_null($support)) {
            $this->response($support, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }

    /* SERVICIO INACTIVA  UN SOPORTES POR ID */

    public function delete_delete($id) {
        if (!$id) {
            $this->response(NULL, 404);
        }

        $support = null;
        $support = $this->support_model->changueStatus($id, -1);

        if (!is_null($support)) {
            $this->response($support, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }

    /* SERVICIO QUE GUARDA UN SOPORTE */

    public function update_post() {
        if (!$this->post('support')) {
            $this->response(NULL, 404);
        }

        $support = $this->support_model->update($this->post('support'));

        if (!is_null($support)) {
            $this->response(array('response' => "SOLICITUD DE SOPORTE EDITADA"), 200);
        } else {
            $this->response(array('error' => "ERROR INESPERADO"), 500);
        }
    }

    /* SERVICIO QUE NOTIFICA LA REPUESTA DE UNA FALLA  */

    public function notificationResponseNube_post() {

        if (!$this->post('support')) {
            $this->response(NULL, 404);
        }

        $rs = $this->support_model->notificationResponseNube($this->post('support'));

        if (!is_null($rs)) {
            $this->response($rs, 200);
        } else {
            $this->response(array('error' => "ERROR INESPERADO"), 500);
        }
    }

}

?>
