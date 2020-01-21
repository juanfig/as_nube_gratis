<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Usercompany_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

 

    /* AGRAGR NUEVO USUARIO EMPRESA */

    public function add($user) {
       
       // VERIFICAMOS SI EL CHOFER ESTA REGISTRADO //
      /* BUSCAMOS UN CODIGO PARA ASIGNARLO */
      $this->db->select("*")->from("tb_user_ex");
      $this->db->where("userNameUser =", @$user['emailUser']);
      $this->db->where('idStatusUser',1);
    //  $this->db->where("idProfileUser =", 3);
      $query = $this->db->where("passUser =",sha1(md5(@$user['passUser'])))
      ->get();



      if ($query->num_rows() < 1) {



        /* CREAMOS UN USUARIO PARA ESE CLIENE */
        $this->db->insert('tb_user_ex', array(
            'userNameUser' => @$user['emailUser'],
            'firstNameUser' => @$user['firstNameUser'],
            'lastNameUser' => @$user['lastNameUser'],
            'emailUser' => @$user['emailUser'],
            'passUser' => sha1(md5(@$user['passUser'])),
            'idProfileUser' => 5,
            'idAgencyKf' => @$user['idAgencyKf']
                )
        );

        if ($this->db->affected_rows() === 1) {
            $idUser = $this->db->insert_id();

            return $idUser;
        } else {
            return null;
        }


      }else {
        //return $query->num_rows();
        return -1;// USUARIO EXITE
      }
    }

    public function updatePasaguer($pasaguer){

        $email = $this->db->where([
                    'emailUser',$pasaguer['emailUser'],
                    'idUser <> ',$pasaguer['idUser']
                  ])
                ->get('tb_user_ex');

        if($email->num_rows() == 0){
          $update = [
            'userNameUser' => @$pasaguer['emailUser'],
            'firstNameUser' => $pasaguer['firstNameUser'],
            'lastNameUser' => "",
            'emailUser' => @$pasaguer['emailUser'],
            'passUser' => sha1(md5($pasaguer['passUser'])),
            'idProfileUser' => 5,
            'idAgencyKf' => @$pasaguer['idAgencyKf']
          ];

          $this->db->where('idUser',$pasaguer['idUser'])->update('tb_user_ex',$update);

          if($this->db->affected_rows() === 1){
            return 1;
          }

        }else{
          return -1;
        }
  }

  public function deletePasaguer($pasager){

    /*$this->db->where('idUser',$pasager['idUser'])->delete('tb_user_ex');
    if($this->db->affected_rows() === 1){
      return 1;
    }else{
      return -1;
    }*/
    $this->db->where('idUser',$pasager['idUser'])->update('tb_user_ex',['idStatusUser' => $pasager['status']]);

    if($this->db->affected_rows() === 1){
      return 1;
    }else{
      return -1;
    }
  }


    private function _setUserCompany($user, $idUser) {

        return array(
            'firstNameUserCompany' => @$user['firstNameUserCompany'],
            'lastNameUserCompany' => @$user['lastNameUserCompany'],
            'emailUserCompany' => @$user['emailUserCompany'],
            'phoneUserCompany' => @$user['phoneUserCompany'],
            'addresUserCompany' => @$user['country']['addresUserCompany'],
            'addresLongUserCompany' => @$user['country']['addresLongUserCompany'],
            'addresLatUserCompany' => @$user['country']['addresLatUserCompany'],
            'addresLatUserCompany' => @$user['country']['addresLatUserCompany'],
            'idUserKf' => $idUser,
            'IdUserKfCreate' => @$user['IdUserKf'],
            'idCompanyClientKf' => @$user['idCompanyClientKf'],
            'idCostCenterKf' => @$user['idCostCenterKf'],
            'idBankAcaountCompanyKf' => @$user['idBankAcaountCompanyKf'],
            'autorizante' => @$user['autorizante'],
            'idStatusUserCompany' => 1
        );
    }

  

    /* EDITAR DATOS DE UN EMPRESA */

    public function update($user) {

        $this->db->set($this->_setUserCompany($user, NULL))->where("idUserCompany", $user['idUserCompany'])->update("tb_user_company");

        if ($this->db->trans_status() === true) {
            if ($user['isEditUser']) {

                $this->db->set(
                        array(
                            'passUser' => sha1(md5($user['passUser']))
                        )
                )->where("idUser", $user['idUserKf'])->update("tb_user");
            }


            return true;
        } else {
            return null;
        }
    }

    public function changueStatus($id, $idStatus) {
        $this->db->set(
                array(
                    'idStatusUserCompany' => $idStatus
                )
        )->where("idUserCompany", $id)->update("tb_user_company");


        if ($this->db->affected_rows() === 1) {
            return true;
        } else {
            return null;
        }
    }

    public function changeStatusPasaguer($pasager){

        $this->db->where('idUser',$pasager['idUser'])->update('tb_user_ex',['idStatusUser' => $pasager['status']]);
        
        if($this->db->affected_rows() === 1){
          return 1;
        }else{
          return -1;
        }

    }

}
