<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

date_default_timezone_set("America/Argentina/Buenos_Aires");

class Support_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    // GET DE LISTADO BUSQUEDA DE SOPORTE //
    public function get($id = null, $searchFilter = null, $idAgency = null) {
        $quuery = null;

        // SI RECIBIMOS EL ID DE La SOLICITUD //
        if (!is_null($id)) {
            $this->db->select("*")->from("tb_support_agency");
            $this->db->join('tb_agency', 'tb_agency.idAgency = tb_support_agency.idAgencyKf', 'left');
            $quuery = $this->db->where("tb_support_agency.idSupport = ", $id)->get();
            if ($quuery->num_rows() === 1) {
                $rs = array('support' => $quuery->row_array());

                return $rs;
            }
            return null;
        } else { // SINO RECIBES UN ID
            $this->db->select("*")->from("tb_support_agency");
            $this->db->join('tb_agency', 'tb_agency.idAgency = tb_support_agency.idAgencyKf', 'left');

            if(!is_null($idAgency)){
                $this->db->where("tb_support_agency.idAgencyKf = ", $idAgency);
            }

            if(!is_null($searchFilter['idAgencyFilter'])){
                $this->db->where("tb_support_agency.idAgencyKf = ", $searchFilter['idAgencyFilter']);
            }
            /* Busqueda por filtro */
            if (!is_null($searchFilter['searchFilter'])) {

                $this->db->or_like('tb_support_agency.detailSupport', $searchFilter['searchFilter']);
                $this->db->or_like('tb_agency.nameAgency', $searchFilter['searchFilter']);
                $this->db->or_like('tb_agency.mailAgency', $searchFilter['searchFilter']);

                $this->db->or_like('tb_support_agency.codeSupport', $searchFilter['searchFilter']);
            }

            if(!is_null($searchFilter['statusFilter'])){
                $this->db->where("tb_support_agency.statusSupport = ", $searchFilter['statusFilter']);
            }

                // Si recibimos un limite //
            if ($searchFilter['topFilter'] > 0) {
                $this->db->limit($searchFilter['topFilter']);
            }

            $quuery = $this->db->order_by("FIELD (tb_support_agency.statusSupport,0,2,1,-1)")->get();

            if ($quuery->num_rows() > 0) {
                return $quuery->result_array();
            }
            return null;
        }
    }
    // GET DE LISTADO BUSQUEDA DE SOPORTE //
    public function getLast($idAgencyKf) {
        $quuery = null;

        $this->db->select("idSupport")->from("tb_support_agency")->where('idAgencyKf',$idAgencyKf)->order_by('idSupport','DESC')->limit(1);

        $quuery = $this->db->get();
        if ($quuery->num_rows() === 1) {
            $rs = array('support' => $quuery->row_array());

            return $rs;
        }else{
            $arrayName1 = array('idSupport' => 1 );
            $arrayName2 = array('support' => $arrayName1);
            return $arrayName2;
        }


    }


    public function addCantTickets($agencys){
        foreach ($agencys as $key => $agency) {
            $n=$this->db->select("*")->from("tb_support_agency")->where('idAgencyKf',$agency['idAgency'])->get();
            $agencys[$key]['nTickets']=count($n->result_array());
        }
        return $agencys;
    }

    // GET DE LISTADO BUSQUEDA DE SOPORTE //
    public function getfilterList() {
        $quuery = null;

        $this->db->select("*")->from("tb_agency")->order_by('idAgency','DESC');

        $quuery = $this->db->get();
        if ($quuery->num_rows() >0) {
            $rs = array('agencys' => $this->addCantTickets($quuery->result_array()));

            return $rs;
        }else{

            return null;
        }


    }

    // GET DE LISTADO BUSQUEDA DE SOPORTE //
    public function getMessagesByIdSupport($id = null) {

        $quuery = null;


        $this->db->select("*")->from("tb_support_agency_messages");
        $this->db->join('tb_agency', 'tb_agency.idAgency = tb_support_agency_messages.idAgencyKf', 'left');
        $this->db->join('tb_users', 'tb_users.idUser = tb_support_agency_messages.idUserKf', 'left');
        $this->db->where("tb_support_agency_messages.idSupportKf", $id);
        $quuery =  $this->db->get();

        if ($quuery->num_rows()>0) {
            $rs = array('support' => $quuery->result_array());

            return $rs;
        }
        return null;

    }


    /* AGRAGR NUEVO */

    public function add($support) {


        $this->db->insert('tb_support_agency', $support);

        if ($this->db->affected_rows() === 1) {
            return $this->db->insert_id();
        } else {
            return null;
        }
    }

    /* AGRAGR NUEVA CONSULTORIA */
    public function addConsulting($ticket) {

        $this->db->insert('tb_consulting_ticket', $ticket);

        return ($this->db->affected_rows() === 1) ? true : false;
    }

    public function addMessage($support_message) {


        $this->db->insert('tb_support_agency_messages', $support_message);

        if ($this->db->affected_rows() === 1) {

            $idChat = $this->db->insert_id();

            $s = $this->db->select('*')->from('tb_support_agency')
            ->join('tb_agency', 'tb_agency.idAgency = tb_support_agency.idAgencyKf', 'left')
            ->where("idSupport", $support_message['idSupportKf'])
            ->get();

            $s = $s->row_array();

            $s['responseChat'] =  $support_message['supportMessage'];

            if(!isset($support_message['fullNameUser'])){
                $notificacion = $this->notificationResponse($s);
            }

            return $idChat;
        } else {
            return true;
        }
    }
    /* CAMBIAR EL STATUS DE LA SOLICITUD */

    public function changeStatus($support) {
        $data['statusSupport'] = $support['statusSupport'];
        $date = date('Y-m-d H:i:s');

        if($support['statusSupport'] == 1){
            $data['dateDevelopmentSupport'] = $date;
        }

        if($support['statusSupport'] == -1){
            $data['dateFinalizedSupport'] = $date;
        }

        $this->db->set($data)->where("idSupport", $support['idSupport'])->update("tb_support_agency");

        if ($this->db->affected_rows() === 1) {

            $s = $this->db->select('*')->from('tb_support_agency')
            ->join('tb_agency', 'tb_agency.idAgency = tb_support_agency.idAgencyKf', 'left')
            ->where("idSupport", $support['idSupport'])
            ->get();

            $s = $s->row_array();

            if($support['statusSupport'] == 2){
                $mail = $this->sendMailSupport($s, "Reporte de Falla en Evaluación");
            }

            $notificacion = $this->notificate($s);

            return true;
        } else {
            return null;
        }
    }

    public function update($support) {
        $this->db->set($support)->where("idSupport", $support['idSupport'])->update("tb_support_agency");
        if ($this->db->trans_status() === true) {

            if($support['statusSupport'] == -1){

                    $query = $this->db->select('tb_consulting.idConsulting, tb_consulting.statusConsulting')->from('tb_consulting')
                    ->join('tb_consulting_ticket', 'tb_consulting.idConsulting = tb_consulting_ticket.idConsultingKf', 'inner')
                    ->where("tb_consulting_ticket.idTicketKf", $support['idSupport'])
                    ->get();
                    if ($query->num_rows() === 1) {
                        $consulting = $query->row_array();

                        $query = $this->db->select('count(tb_support_agency.statusSupport) as lasting')
                        ->from('tb_consulting_ticket')
                    ->join('tb_support_agency','tb_support_agency.idSupport = tb_consulting_ticket.idTicketKf', 'inner')
                    ->where("tb_support_agency.statusSupport>-1")
                    ->where("tb_consulting_ticket.idConsultingKf", $consulting['idConsulting'])->get();

                            $result = $query->row_array();
                            if($result['lasting'] == 0 && $consulting['statusConsulting']>0){
                            	$updata = ['statusConsulting' => -1,'dateClosedConsulting' => date('Y-m-d H:i:s')];
                            	$this->db->set($updata)->where("idConsulting", $consulting['idConsulting'])->update("tb_consulting");
                            }

                    }

            }//*/



            return true;
        } else {
            return null;
        }
    }

    public function sendMailSupport($mail, $subject)
    {
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

        $this->load->library('email', $config);
        $this->email->set_newline("\r\n");
        $this->email->from('remisasnube@gmail.com');
        $this->email->subject($subject);


        $cuerpo = "Motivo: ";
        $cuerpo.= $mail['reasonSupport']."<br>";
        $cuerpo.= "Detalles: <br>";
        $cuerpo.= $mail['detailSupport'].'<br>';
        $cuerpo.= "------------------------------<br>";
        $cuerpo.= "Su Reporte de Falla a pasado a Evaluación!<br>";

        $this->email->message($cuerpo);

        $this->email->to('jorguti58@gmail.com,remisasnube@gmail.com,leonardo@apreciasoft.com,'.
            $mail['emailResponSupport'].','.
            $mail['emailTwoResponSupport'].','.
            $mail['mailAgency']);

        if($this->email->send(FALSE)){

        }else {

        }
    }

    public function notificate($support)
    {
        $result = null;

        $headers = array ( 'Content-Type: application/json' );

        // $support['idAgencyKf'] = ID_AGENCY;
        // $support = array(
        //     'agency' => $agency,
        //     'support' => $support
        //     );

        $param = array(
          "support" => $support
      );



        $uri = "http://localhost/".$support['nameAgency'].'/Api/support/changeStatus';

        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, $uri );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $param ) );
        $result = curl_exec($ch);

        curl_close( $ch );

        $resultd = json_decode($result);


        return $resultd;
    }

    public function notificationResponse($support)
    {
        $result = null;

        $headers = array ( 'Content-Type: application/json' );

        $param = array(
          "support" => $support
      );

        $uri = "http://localhost/".$support['nameAgency'].'/Api/support/notificationResponse';

        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, $uri );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $param ) );
        $result = curl_exec($ch);

        curl_close( $ch );

        $resultd = json_decode($result);

        return $resultd;
    }

    public function notificationResponseNube($support) {

        $date = date('Y/m/d H:i:s');

        $s = $this->db->select('*')->from('tb_support_agency')
        ->join('tb_agency', 'tb_agency.idAgency = tb_support_agency.idAgencyKf', 'left')
        ->where("idSupport", $support['idSupportKf'])
        ->get();

        $s = $s->row_array();

        $this->db->insert('tb_notification_send', array(
            'idTypeUser' =>-1,
            'idType' => 1,
            'subTitle' =>
            'ID: '.@$support['idUserAgencyKf'].', Email: '.@$s['emailResponSupport'].
            ', Fecha: '.$date.', Razón: '.@$s['reasonSupport'].', Respuesta: '.@$support['supportMessage'],
            'idUser' => @$support['idUserKf']
        ));

        if ($this->db->affected_rows() === 1) {
            return true;
        } else {
            return null;
        }
    }

    public function supportsAgency($id){
        $quuery = null;

        $this->db->select("*")->from("tb_support_agency")->where('idAgencyKf',$id);
        $quuery = $this->db->get();
        if ($quuery->num_rows() >0) {
            $rs = $quuery->result_array();

            return $rs;
        }else{

            return null;
        }
    }

}
