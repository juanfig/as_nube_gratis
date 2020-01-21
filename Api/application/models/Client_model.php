<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Client_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }


    /* AGRAGR NUEVO CLIENTE */
    public function add($client) {
      // VERIFICAMOS SI EL CHOFER ESTA REGISTRADO //
      /* BUSCAMOS UN CODIGO PARA ASIGNARLO */
      if(@$client['mailClient'] !== ""){
        $this->db->select("*")->from("tb_user_ex");
        $this->db->where("userNameUser =", @$client['mailClient']);
    //    $this->db->where("idProfileUser =", 2);
        $query = $this->db->where("passUser =",sha1(md5(@$client['passClient'])))->get();

        if ($query->num_rows() < 1) {


              /* CREAMOS UN USUARIO PARA ESE CLIENE */
              $this->db->insert('tb_user_ex', array(
                  'userNameUser' => @$client['mailClient'],
                  'firstNameUser' => $client['firtNameClient'],
                  'lastNameUser' => $client['lastNameClient'],
                  'emailUser' => @$client['mailClient'],
                  'passUser' => sha1(md5($client['passClient'])),
                  'idProfileUser' => 2,
                  'idAgencyKf' => @$client['idAgencyKf']
                      )
              );

              if ($this->db->affected_rows() === 1) {
                  $idUser = $this->db->insert_id();
                  return $idUser;
              } else {
                  return null;
              }

        }else {
          return -1;// USUARIO EXITE
        }

      }else{
          $this->db->insert('tb_user_ex', array(
            'userNameUser' => @$client['mailClient'],
            'firstNameUser' => $client['firtNameClient'],
            'lastNameUser' => $client['lastNameClient'],
            'emailUser' => @$client['mailClient'],
            'passUser' => sha1(md5($client['passClient'])),
            'idProfileUser' => 2,
            'idAgencyKf' => @$client['idAgencyKf']
                )
        );

        if ($this->db->affected_rows() === 1) {
            $idUser = $this->db->insert_id();
            return $idUser;
        } else {
            return null;
        }
      }
        

    }

        /* AGRAGR NUEVO CLIENTE */
        public function addImport($client) {
         
          
            // VERIFICAMOS SI EL CHOFER ESTA REGISTRADO //
            /* BUSCAMOS UN CODIGO PARA ASIGNARLO */
            $this->db->select("*")->from("tb_user_ex");
            $this->db->where("userNameUser =", @$client['email']);
        //    $this->db->where("idProfileUser =", 2);
            $query = $this->db->where("passUser =",sha1(md5(@$client['passClient'])))->get();
      
      
      
            if ($query->num_rows() < 1) {
      
      
                  /* CREAMOS UN USUARIO PARA ESE CLIENE */
                  $this->db->insert('tb_user_ex', array(
                      'userNameUser' => $client['email'],
                      'firstNameUser' => $client['name'],
                      'lastNameUser' => $client['last_name'],
                      'emailUser' => $client['email'],
                      'passUser' => sha1(md5('12345')),
                      'idProfileUser' => 2,
                      'idAgencyKf' => @$client['idAgencyKf']
                          )
                  );
      
                  if ($this->db->affected_rows() === 1) {
                      $idUser = $this->db->insert_id();
                      return $idUser;
                  } else {
                      return null;
                  }
      
            }else {
              return -1;// USUARIO EXITE
            }
      
          }


    	/* LLAMAMO A EL SERVICIO DE ASNUBE */
    	public function addCloud($driver)
    	{

    		$result = null;

    		$headers = array
    		(
    			'Content-Type: application/json'
    		);

    		$driver['idAgencyKf'] = ID_AGENCY;
    		$param = array(
    						"driver" => $driver
    		);


    		$uri = URI_NUBE.'client';

    		$ch = curl_init();
    		curl_setopt( $ch,CURLOPT_URL, $uri );
    		curl_setopt( $ch,CURLOPT_POST, true );
    		curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
    		curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
    		curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
    		curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $param ) );
    		$result = curl_exec($ch);

    		curl_close( $ch );



    			return $result;
    	}

    /* AGRAGR NUEVO CLIENTE EMPRESA */
    public function addCompany($client) {



        /* CREAMOS UN USUARIO PARA ESA EMPRESA */
        $this->db->insert('tb_user_ex', array(
            'userNameUser' => $client['client']['mailClient'],
            'firstNameUser' => $client['client']['firtNameClient'],
            "lastNameUser" => $client['lastNameClient'],
            'emailUser' => $client['client']['mailClient'],
            'passUser' => sha1(md5($client['client']['passClient'])),
            'idProfileUser' => 4
                )

        );

        if ($this->db->affected_rows() === 1) {
            $idUser = $this->db->insert_id();
            return $idUser;
        } else {
            return null;
        }
    }

    /* EDITAR DATOS DE UN CLIENTE */
    public function update($client) {



        // EDITAMO EL USUARIO //
        $this->db->set(array(
          "emailUser"  =>  $client['mailClient'],
          "firstNameUser"  => $client['firtNameClient'],
          "lastNameUser" => $client['lastNameClient']
        //  "passUser" => $user['passDriver']
          )
        )->where("idUser", $client['idUserCloud'])->update("tb_user");

        if ($this->db->trans_status() === true)
        {
            if ($client['isEditUser']) {
                $this->db->set(
                        array(
                            'passUser' => sha1(md5($client['passClient']))
                        )
                )->where("idUser", $client['idUserCloud'])->update("tb_user");
              }
          return true;
        }
        else {
          return false;
        }
    }

    public function changueStatus($id, $idStatus) {
        $this->db->set(array(
          "idStatusUser"  =>  $idStatus
          )
        )->where("idUser", $id)->update("tb_user_ex");


        if ($this->db->affected_rows() === 1) {
            return true;
        } else {
            return null;
        }
    }
}
?>
