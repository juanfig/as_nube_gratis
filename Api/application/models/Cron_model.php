 <?php
 if (!defined('BASEPATH'))
    exit('No direct script access allowed');

date_default_timezone_set("America/Argentina/Buenos_Aires");

class Cron_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /* LISTADO DE FILTROS */
    public function noticationExpirationFleet() {
        $query = null;
        $sum=null;
        $query = $this->db->select('*')->from("tb_agency")->get();
        $data = array();
        if ($query->num_rows() > 0) {
            $agencys=$query->result_array();
            foreach ($agencys as $agency) {
                $headers = array ( 'Content-Type: application/json' );
                $uri = URI_BASE_SERVE.$agency['nameAgency'].'/Api/cron/noticationExpirationFleet';
                $ch = curl_init();
                curl_setopt( $ch,CURLOPT_URL, $uri );
                curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET');
                curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
                curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
                curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
                $result = curl_exec($ch);
                curl_close( $ch );
            }
        }
        return $data;
    }

    /* SERVICIO GET QUE ENVIA MAIL NOTIFICACNDO DE RESERVA PROXIMA*/
    public function noticationRervationComing(){
        $query = null;
        $sum=null;
        $query = $this->db->select('*')->from("tb_agency")->get();
        $data = array();
        if ($query->num_rows() > 0) {
            $agencys=$query->result_array();
            foreach ($agencys as $agency) {
                $headers = array ( 'Content-Type: application/json' );
                $uri = URI_BASE_SERVE.$agency['nameAgency'].'/Api/cron/noticationRervationComing';
                $ch = curl_init();
                curl_setopt( $ch,CURLOPT_URL, $uri );
                curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET');
                curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
                curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
                curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
                $result = curl_exec($ch);
                curl_close( $ch );
            }
        }
        return $data;
    }

    public function notificationExpirationUser()
    {
        set_time_limit(600000000000);
        ini_set('memory_limit', '2000M');
        $query = null;
        $sum=null;
        $query = $this->db->select('*')->from("tb_agency")->get();
        $data = array();
        if ($query->num_rows() > 0) {
            $agencys=$query->result_array();
            foreach ($agencys as $agency) {
                $headers = array ( 'Content-Type: application/json' );
                $uri = URI_BASE_SERVE.$agency['nameAgency'].'/Api/cron/notificationExpirationUser';
                $ch = curl_init();
                curl_setopt( $ch,CURLOPT_URL, $uri );
                curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET');
                curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
                curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
                curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
                $result = curl_exec($ch);
                curl_close( $ch );
            }
        }
        return $data;
    }

}


