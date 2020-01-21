<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

date_default_timezone_set("America/Argentina/Buenos_Aires");

class Consulting_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

  	//-GET DE LISTADO DE CONSULTORIAS DEL USUARIO---------------------------------------------*/
    public function get($params) {
        $query = null;

        // SI RECIBIMOS EL ID DEL USUARIO QUE HACE LA SOLICITUD
        if (!empty($params['idUser'])) {

        	$user = $this->db->select("tb_users.userNameUser")->from("tb_users")
        							->where("tb_users.idUser = ", $params['idUser'])->get();
          $user = $user->row_array()['userNameUser'];

            $this->db->select("tb_consulting.*,tb_agency.*,tb_users.userNameUser, a.lasting")->from("tb_consulting");
            $this->db->join('tb_agency', 'tb_agency.idAgency = tb_consulting.idAgencyKf', 'inner');
            $this->db->join('tb_users', 'tb_users.idUser= tb_consulting.idUserKf', 'inner');
            $this->db->join('(select idConsultingKf, count(idSupport) as lasting from tb_consulting_ticket
inner join tb_support_agency on tb_support_agency.idSupport = tb_consulting_ticket.idTicketKf
where tb_support_agency.statusSupport>-1 GROUP BY idConsultingKf) as a ',
            'tb_consulting.idConsulting = a.idConsultingKf', 'left');

            if (!empty($params['idAgency'])) {
            			$this->db->where("tb_consulting.idAgencyKf  = ", $params['idAgency']);
            }
            if (!empty($params['date'])){
            			$this->db->where("tb_consulting.dateOpenConsulting like ", $params['date']);
            }
            if($user!="ADMIN"){
            	$this->db->where("tb_users.idUser = ", $params['idUser']);
            }
            $query = $this->db->get();

              if ($query->num_rows() >0) {
                  $result = $query->result_array();
                  return $result;
              }
            }
            return null;
    }
/*
select idSupport, count(tb_support_agency.statusSupport) as lasting from tb_consulting_ticket
inner join tb_support_agency on tb_support_agency.idSupport = tb_consulting_ticket.idTicketKf
where tb_support_agency.statusSupport>-1 GROUP BY idSupport
*/
  	//-AGRAGR NUEVO---------------------------------------------------------------------------*/
    public function add($consulting) {
				$consulting['dateOpenConsulting'] = date('Y-m-d H:i:s');
        $this->db->insert('tb_consulting', $consulting);

        if ($this->db->affected_rows() === 1) {
            return $this->db->insert_id();
        }
        return null;
    }

  	//-GET INFO DE UNA CONSULTORIA ESPECIFICA-------------------------------------------------*/
    public function getThis($idUser = null, $idConsulting = null) {
        $query = null;

        	$user = $this->db->select("tb_users.userNameUser")->from("tb_users")
        							->where("tb_users.idUser = ", $idUser)->get();
          $user = $user->row_array()['userNameUser'];

        // SI RECIBIMOS EL ID DEL USUARIO QUE HACE LA SOLICITUD Y EL ID DE LA CONSULTORIA
        if (!is_null($idUser) || !is_null($idConsulting)) {
            $this->db->select("tb_consulting.*,tb_agency.*,tb_users.userNameUser")->from("tb_consulting");
            $this->db->join('tb_users',  'tb_users.idUser= tb_consulting.idUserKf', 'inner');
            $this->db->join('tb_agency', 'tb_agency.idAgency = tb_consulting.idAgencyKf', 'inner');
            $this->db->where("tb_consulting.idConsulting = ", $idConsulting);
            if($user!="ADMIN"){
            	$this->db->where("tb_consulting.idUserKf = ", $idUser);
            }
            $query = $this->db->get();

            if ($query->num_rows() === 1) {
                $result = array('consulting' => $query->row_array());

              // BUSCA LOS TICKETS ASOCIADOS A LA CONSULTORIA
		          $this->db->select("*")->from("tb_consulting_ticket");
		          $this->db->join('tb_support_agency', 'tb_support_agency.idSupport = tb_consulting_ticket.idTicketKf', 'inner');
		          $query = $this->db->where("tb_consulting_ticket.idConsultingKf = ", $idConsulting)->get();
							$result["tickets"] = $query->num_rows() > 0 ? $query->result_array() : null;
              return $result;
            }
        }
        return null;
    }

    public function getThisWithConsultant($idConsulting = null) {
        $query = null;

        // SI RECIBIMOS EL ID DEL USUARIO QUE HACE LA SOLICITUD Y EL ID DE LA CONSULTORIA
        if (!is_null($idConsulting)) {
            $this->db->select("tb_consulting.*,tb_agency.*, tb_users.userNameUser, tb_users.emailUser")->from("tb_consulting");
            $this->db->join('tb_users',  'tb_users.idUser= tb_consulting.idConsultantKf', 'inner');
            $this->db->join('tb_agency', 'tb_agency.idAgency = tb_consulting.idAgencyKf', 'inner');
            $this->db->where("tb_consulting.idConsulting = ", $idConsulting);

            $query = $this->db->get();
            if ($query->num_rows() === 1) {
                $result = array('consulting' => $query->row_array());
              return $result;
            }
        }
        return null;
    }

  	/*-CAMBIAR EL STATUS DE LA CONSULTORIA-----------------------------------------------------/
  	El cambio de estado a cerrada se hace en el modelo de support, al cambiar el estado de un
  	support se comprueba si esta asociado a una consultoría en cuyo caso se hace una busqueda
  	de mas supports asociados a la misma, si no encuentra mas, cambia el estatus de la misma
  	a -1, osea cerrada.
  	/-----------------------------------------------------------------------------------------*/
    public function changeStatus($consulting) {

    		$data['statusConsulting'] = $consulting['statusConsulting'];
        $data['dateEvalConsulting'] = date('Y-m-d H:i:s');
        $this->db->set($data)->where("idConsulting", $consulting['idConsulting'])->update("tb_consulting");
				if ($this->db->affected_rows() === 1) {
            return true;
        }
            return null;
    }

  	//-PERMITE MODIFICAR LA AGENCIA Y EL ONLINE-----------------------------------------------*/
    public function update($consulting) {
    	$data = [];
    	if(!empty($consulting)) {
    		$data['code'] = $consulting['code'];
    	}else {
    		$data = [
					'idAgencyKf' => $consulting['idAgencyKf'],
					'online' => $consulting['online']
    		];
    	}
        $this->db->set($data)->where("idConsulting", $consulting['idConsulting'])->update("tb_consulting");
        if ($this->db->trans_status() === true) {
            return true;
        }
            return null;
    }
}
