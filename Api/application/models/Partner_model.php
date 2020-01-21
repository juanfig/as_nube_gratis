<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Partner_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('UTC');
    }

   // GET DE LISTADO BUSQUEDA DE PARTNERES //
    public function get($id = null, $searchFilter = null)
    {
        $quuery = null;

            // SI RECIBIMOS EL ID DEL DEL PARTNER //
        if (!is_null($id)) {

            $this->db->select("*")->from("tb_partner");
            $this->db->join('tb_users', 'tb_partner.idUserKf = tb_users.idUser');
            $this->db->where("idPartner", $id);

            $query = $this->db->order_by("tb_partner.idPartner", "DESC")->get();

            if ($query->num_rows() > 0) {
                return $query->row_array();

            }
            return null;

        }
        else { // SI NO RECIBIMOS EL ID DEL PRODUCTO RETORNAMOS TODOS LOS REGISTROS 
            $this->db->select("*")->from("tb_partner");
            $this->db->join('tb_clouds', 'tb_partner.idCloudKf = tb_clouds.idCloud', 'left');
            $this->db->join('tb_users', 'tb_partner.idUserKf = tb_users.idUser', 'left');
            $this->db->join('tb_country', 'tb_partner.idCountryKf = tb_country.idCountry', 'left');

            /* Busqueda por filtro */
            if (!is_null($searchFilter['searchFilter'])) {
                $this->db->or_like('firstnamePartner', $searchFilter['searchFilter']);
                $this->db->or_like('lastnamePartner', $searchFilter['searchFilter']);
            }
            // Si recibimos un limite //
            if ($searchFilter['topFilter'] > 0) {
                $this->db->limit($searchFilter['topFilter']);
            }
            $this->db->where("idStatus !=", '-1');

            $quuery = $this->db->order_by("tb_partner.idPartner", "DESC")->get();


            if ($quuery->num_rows() > 0) {
                return $quuery->result_array();
            }
            return null;
        }
    }

    public function add($partner)
    {
    	$this->db->trans_start();
        $this->db->set(
            array(
                'userNameUser' =>$partner['emailPartner'],
                'emailUser' =>$partner['emailPartner'],
                'passUser' => sha1(md5($partner['passPartner'])),
                'idProfileUser' => 3,
                'dateCreated' => date('Y-m-d H:i:s'),
                'parentUserKf' => $this->userInfo->idUser
            )
        )->insert("tb_users");

        if (!$this->db->affected_rows() === 1) return null;
            $iduser = $this->db->insert_id();

            $this->db->set($this->_setPartner($partner, $iduser))->insert("tb_partner");
        if (!$this->db->affected_rows() === 1) {
            $this->db->trans_rollback();
            return null;
            }
            $id = $this->db->insert_id();

            $this->db->select("*")->from("tb_clouds");
            $this->db->join('tb_country', 'tb_country.idCountry = tb_clouds.idCountryKf', 'left');
            $quuery = $this->db->where("idCountryKf = ", $partner['idCountryKf'])->get();
            if ($quuery->num_rows() == 0) {
                $country = $this->db->select("country")->from("tb_country")->where("idCountry = ", $partner['idCountryKf'])->get()->row_array()['country'];
              $this->db->set([
                'nameCloud' => $country,
                'idCountryKf' => $partner['idCountryKf'],
                'idLanguageKf' => 1,
                'titleFastTravel' => "Express",
                'idStatuCloud' => 1
              ])->insert("tb_clouds");

            }


        $this->db->trans_complete();
        return $id;
    }


    public function emailVerify($email, $idUser = null)
    {

        $this->db->select("*")->from("tb_users");

        if(!is_null($idUser)){
            $this->db->where('idUser !=', $idUser);
        }
        $query = $this->db->where("emailUser", $email)->get();

        if ($query->num_rows() > 0) {
            return true;

        }
        else {
            return null;
        }

    }


    public function dniVerify($dni, $idPartner = null)
    {

        $this->db->select("*")->from("tb_partner");
        if(!is_null($idPartner)){
            $this->db->where('idPartner !=', $idPartner);
        }
        $query = $this->db->where("dniPartner", $dni)->get();

        if ($query->num_rows() > 0) {
            return true;

        }
        else {
            return null;
        }

    }


    public function countryVerify($country, $idPartner = null)
    {
        $this->db->select("*")->from("tb_partner");
        if(!is_null($idPartner)){
            $this->db->where('idPartner !=', $idPartner);
        }
        $query = $this->db->where("idCountryKf", $country)->where('idStatus =', 1)->get();

        return $query->num_rows() > 0 ? true : false;
    }


    public function changeStatus($data)
    {

        $this->db->set(
            array(
                'idStatus' => $data['idStatus']
            )
        )->where("idPartner", $data['idPartner'])->update("tb_partner");


        if ($this->db->affected_rows() === 1) {
            return true;
        }
        else {
            return null;
        }
    }



    public function update($partner)
    {
        $this->db->set($this->_setPartner($partner, $partner['idUserKf']))->where("idPartner", $partner['idPartner'])->update("tb_partner");
        if ($this->db->trans_status() === true) {
            return true;
        }
        else {
            return null;
        }
    }


    public function delete($data)
    {   
        $this->db->set(
            array(
                'idStatus' => -1
            )
        )->where("idPartner", $data['idPartner'])->update("tb_partner");


        if ($this->db->affected_rows() === 1) {
            return true;
        }
        else {
            return null;
        }
    }

    private function _setPartner($partner, $idUserKf)
    {
        return array(
            'firstnamePartner' => @$partner['firstnamePartner'],
            'lastnamePartner' => @$partner['lastnamePartner'],
            'emailPartner' => @$partner['emailPartner'],
            'dniPartner' => @$partner['dniPartner'],
            'phonePartner' => @$partner['phonePartner'],
            'idUserKf' => $idUserKf,
            'idCloudKf' => @$partner['idCloudKf'],
            'idCountryKf' => @$partner['idCountryKf'],
            'currentAccountLimit' => @$partner['currentAccountLimit'],

        );
    }

    // public function getFilterForm()
    // {
    //     $clounds = null;

    //     $query = $this->db->select('*')->from('tb_clouds')->get();

    //     if($query->num_rows()>0)
    //     {
    //         $clounds = $query->result_array();
    //     }

    //     return array(
    //         'clounds'=>$clounds
    //     );
    // }

    public function getFilterForm()
    {
        $countries = null;

        $query = $this->db->select('*')->from('tb_country')->get();

        if($query->num_rows()>0)
        {
            $countries = $query->result_array();
        }

        return array(
            'countries'=>$countries
        );
    }


    public function getCloudByCountry()
    {

        $clouds = null;

        $query = $this->db->select('*')->from('tb_clouds')->get();

        if($query->num_rows()>0)
        {
            $clouds = $query->result_array();
        }

        return array(
            'clouds'=>$clouds
        );
    }

}
?>
