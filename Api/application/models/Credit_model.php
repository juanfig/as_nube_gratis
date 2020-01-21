<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Credit_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

   // GET DE LISTADO BUSQUEDA DE VENDEDORES //
    public function get($id = null, $searchFilter = null)
    {
        $quuery = null;

            // SI RECIBIMOS EL ID DEL DEL DEL ABONO //
        if (!is_null($id)) {

            $this->db->select("*")->from("tb_credit");
            $this->db->where("idCredit", $id);

            $query = $this->db->order_by("tb_credit.idCredit", "DESC")->get();
            ;

            if ($query->num_rows() > 0) {
                return $query->row_array();

            }
            return null;

        }
        else { // SI NO RECIBIMOS EL ID DEL PRODUCTO RETORNAMOS TODOS LOS REGISTROS 
            $this->db->select("*")->from("tb_credit");
            $this->db->join('tb_agency', 'tb_agency.idAgency = tb_credit.idAgencyFk', 'left');
            /* Busqueda por filtro */
            if (!is_null($searchFilter['searchFilter'])) {
                $this->db->or_like('idAgencyFk', $searchFilter['searchFilter']);
      
            }
            // Si recibimos un limite //
            if ($searchFilter['topFilter'] > 0) {
                $this->db->limit($searchFilter['topFilter']);
            }
            

            $quuery = $this->db->order_by("tb_credit.idCredit", "DESC")->get();



            if ($quuery->num_rows() > 0) {
                return $quuery->result_array();
            }
            return null;
        }
    }

    public function add($credit)
    {
        $this->db->set($this->_setCredit($credit))->insert("tb_credit");
        if ($this->db->affected_rows() === 1) {
            $id = $this->db->insert_id();
            return $id;
        }
        else {
            return null;
        }
    }




    public function changeStatus($data)
    {

        $this->db->set(
            array(
                'statusCredit' => $data['statusCredit']
            )
        )->where("idCredit", $data['idCredit'])->update("tb_credit");


        if ($this->db->affected_rows() === 1) {
            return true;
        }
        else {
            return null;
        }
    }



    public function update($credit)
    {
        $this->db->set($this->_setCredit($credit))->where("idCredit", $credit['idCredit'])->update("tb_credit");
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
                'statusCredit' => -1
            )
        )->where("idCredit", $data['idCredit'])->update("tb_credit");


        if ($this->db->affected_rows() === 1) {
            return true;
        }
        else {
            return null;
        }
    }

    private function _setCredit($credit)
    {
        return array(
            'nameCredit' => @$credit['nameCredit'],
            'valueCredit' => @$credit['valueCredit'],
            'periodCredit' => @$credit['periodCredit'],
            'descriptionCredit' => @$credit['descriptionCredit'],
            //'idAgencyFk' => @$credit['idAgencyFk'],

        );
    }


    public function ejecutarMensuales()
    {

       if ($this->db->query("CALL `add_tb_credit_agency1`();")) {
            return true;
       }else {
            return null;
        }
    }



}
?>
