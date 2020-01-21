<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Seller_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

   // GET DE LISTADO BUSQUEDA DE VENDEDORES //
    public function get($id = null, $searchFilter = null)
    {
        $quuery = null;

            // SI RECIBIMOS EL ID DEL DEL VENDEDOR //
        if (!is_null($id)) {

            $this->db->select("*")->from("tb_seller");
            $this->db->where("idSeller", $id);

            $query = $this->db->order_by("tb_seller.idSeller", "DESC")->get();

            if ($query->num_rows() > 0) {
                return $query->row_array();

            }
            return null;

        }
        else { // SI NO RECIBIMOS EL ID DEL PRODUCTO RETORNAMOS TODOS LOS REGISTROS 
            $this->db->select("*")->from("tb_seller");

            /* Busqueda por filtro */
            if (!is_null($searchFilter['searchFilter'])) {
                $this->db->or_like('nameSeller', $searchFilter['searchFilter']);
                $this->db->or_like('lastnameSeller', $searchFilter['searchFilter']);
            }
            // Si recibimos un limite //
            if ($searchFilter['topFilter'] > 0) {
                $this->db->limit($searchFilter['topFilter']);
            }
            $this->db->where("statusSeller !=", '-1');

            $quuery = $this->db->order_by("tb_seller.idSeller", "DESC")->get();


            if ($quuery->num_rows() > 0) {
                return $quuery->result_array();
            }
            return null;
        }
    }

    public function add($seller)
    {
        $this->db->set($this->_setSeller($seller))->insert("tb_seller");
        if ($this->db->affected_rows() === 1) {
            $id = $this->db->insert_id();
            return $id;
        }
        else {
            return null;
        }
    }


    public function emailVerify($email)
    {

        $this->db->select("*")->from("tb_seller");
        $query = $this->db->where("emailSeller", $email)->get();

        if ($query->num_rows() > 0) {
            return true;

        }
        else {
            return null;
        }

    }


    public function dniVerify($dni)
    {

        $this->db->select("*")->from("tb_seller");
        $query = $this->db->where("dniSeller", $dni)->get();

        if ($query->num_rows() > 0) {
            return true;

        }
        else {
            return null;
        }

    }



    public function changeStatus($data)
    {

        $this->db->set(
            array(
                'statusSeller' => $data['statusSeller']
            )
        )->where("idSeller", $data['idSeller'])->update("tb_seller");


        if ($this->db->affected_rows() === 1) {
            return true;
        }
        else {
            return null;
        }
    }



    public function update($seller)
    {
        $this->db->set($this->_setSeller($seller))->where("idSeller", $seller['idSeller'])->update("tb_seller");
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
                'statusSeller' => -1
            )
        )->where("idSeller", $data['idSeller'])->update("tb_seller");


        if ($this->db->affected_rows() === 1) {
            return true;
        }
        else {
            return null;
        }
    }

    private function _setSeller($seller)
    {
        return array(
            'nameSeller' => @$seller['nameSeller'],
            'lastnameSeller' => @$seller['lastnameSeller'],
            'dniSeller' => @$seller['dniSeller'],
            'pictureSeller' => @$seller['pictureSeller'],
            'phoneSeller' => @$seller['phoneSeller'],
            'passwordSeller' => @$seller['passwordSeller'],
            'emailSeller' => @$seller['emailSeller'],
            'csalesSeller' => @$seller['csalesSeller'],
            'creditsSeller' => @$seller['creditsSeller'],
            'statusSeller' => @$seller['statusSeller'],

        );
    }



}
?>
