<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Advertising_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function get($id = null, $searchFilter = null, $status = null)
    {
        $quuery = null;

        if (!is_null($id)) {

            $this->db->select("*")->from("tb_advertising");
            $this->db->where("idAdvertising", $id);

            $query = $this->db->order_by("tb_advertising.idAdvertising", "DESC")->get();
            ;

            if ($query->num_rows() > 0) {
                return $query->row_array();

            }
            return null;

        }
        else { // SI NO RECIBIMOS EL ID DEL PRODUCTO RETORNAMOS TODOS LOS REGISTROS 
            $this->db->select("*")->from("tb_advertising");
            /* Busqueda por filtro */
            if (!is_null($searchFilter['searchFilter'])) {
                $this->db->or_like('nameAdvertising', $searchFilter['searchFilter']);
      
            }
            // Si recibimos un limite //
            if ($searchFilter['topFilter'] > 0) {
                $this->db->limit($searchFilter['topFilter']);
            }    

            /* Busqueda por filtro */
            if (!is_null($status)){
                $this->db->where('status', $status);
      
            }        

            $quuery = $this->db->order_by("tb_advertising.idAdvertising", "DESC")->get();

            if ($quuery->num_rows() > 0) {
                return $quuery->result_array();
            }
            return null;
        }
    }


    private function _setAdvertising($advertising)
    {
        return array(
            'nameAdvertising' => @$advertising['nameAdvertising'],
            'descAdvertising' => @$advertising['descAdvertising'],
            'linkAdvertising' => @$advertising['linkAdvertising'],
            'status' => 1

        );
    }

    public function add($advertising)
    {
        $this->db->set($this->_setAdvertising($advertising))->insert("tb_advertising");
        if ($this->db->affected_rows() === 1) {
            $id = $this->db->insert_id();
            return $id;
        }
        else {
            return null;
        }
    }

    private function _setAdvertisingUpdate($advertising){
        return array(
            'nameAdvertising' => @$advertising['nameAdvertising'],
            'descAdvertising' => @$advertising['descAdvertising'],
            'linkAdvertising' => @$advertising['linkAdvertising']

        );
    }
    
    public function update($advertising){
        $this->db->set($this->_setAdvertisingUpdate($advertising))->where("idAdvertising", $advertising['idAdvertising'])->update("tb_advertising");
        if ($this->db->trans_status() === true) {
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
                'status' => $data['status']
            )
        )->where("idAdvertising", $data['idAdvertising'])->update("tb_advertising");


        if ($this->db->affected_rows() === 1) {
            return true;
        }
        else {
            return null;
        }
    }





}
?>
