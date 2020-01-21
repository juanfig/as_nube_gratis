<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
    date_default_timezone_set("America/Argentina/Buenos_Aires");

class Notifications_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    // GET DE LISTADO NOTIFICACIONE//
    public function get($idUser = null, $all = null, $searchFilter = null) {
// BUSCAMOS LAS NOTIFICACIONES PENDIENTES//
        $rs = null;

        $user = $this->db->select('*')->from("tb_users")->where('idUser =', $idUser)->get();

        $user = $user->row_array();

        $sql = "SELECT  
        *
        FROM tb_notification_send t1
        INNER JOIN tb_notification_send_type t2  
        ON (t1.`idType` = t2.`idTypeNotType`) ";

        if($user['idProfileUser'] == 1){
            $sql.=" WHERE t1.`idTypeUser` = -1 ";
        }else{
            $sql.=" WHERE t1.`idUser` = ".$idUser." ";    
        }

        if(is_null($all)){
            $sql.=" AND isRead = 0 ";
        }

        if (!is_null($searchFilter['searchFilter'])) {
            $sql.=" AND (t2.title LIKE '%".$searchFilter['searchFilter']."%' OR t1.subTitle LIKE '%".$searchFilter['searchFilter']."%') ";
        }

        $sql.=" ORDER BY idNotification DESC ";


        if (isset($searchFilter['topFilter'])){
            if ($searchFilter['topFilter'] > 0) {
                $sql.=" LIMIT ".$searchFilter['topFilter'];
            }
        }


        // echo $sql;
        $query = $this->db->query($sql);

        if($query->num_rows() > 0){
            $rs = $query->result_array();
        }

        return $rs;

    }

    // LEER NOTIFIACION //
    public function read($id = null) {

      $newDate = date("Y/m/d H:i:s");
      $this->db->set(
              array(
                  'isRead' => true,
                  'dateRead' =>   $newDate
              )
      )->where("idNotification", $id)->update("tb_notification_send");


      if ($this->db->affected_rows() === 1) {
          return true;
      } else {
          return null;
      }

    }

    public function readAll($idUser) {

        $newDate = date("Y/m/d H:i:s");
        $user = $this->db->select('*')->from("tb_users")->where('idUser =', $idUser)->get();
        $user = $user->row_array();

        if($user['idProfileUser'] == 1){
            $this->db->set(
                array(
                    'isRead' => true,
                    'dateRead' =>   $newDate
                )
            )->where("idTypeUser", '-1')->update("tb_notification_send");

        }else{
            $this->db->set(
                array(
                    'isRead' => true,
                    'dateRead' =>   $newDate
                )
            )->where("idUser", $idUser)->update("tb_notification_send"); 
        }

    
        if ($this->db->affected_rows() === 1) {
            return true;
        } else {
            return null;
        }

    }



}
?>
