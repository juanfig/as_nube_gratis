<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Product_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }
    
    // GET DE LISTADO BUSQUEDA DE PRODUCTO //
    public function get($id = null, $searchFilter = null) {
        
        $quuery = null;

        // SI RECIBIMOS EL ID DEL PRODUCTO //
        if (!is_null($id)) {
            $this->db->select("*")->from("tb_product");
            $quuery = $this->db->where("idProduct = ", $id)->get();

            if ($quuery->num_rows() === 1) {
                $rs = array('product' => $quuery->row_array());
                return $rs;
            }
            return null;
        } else { // SI NO RECIBIMOS EL ID DEL PRODUCTO RETORNAMOS TODOS LOS REGISTROS 
            $this->db->select("*")->from("tb_product");

            /* Busqueda por filtro */
            if (!is_null($searchFilter['searchFilter'])) {
                $this->db->or_like('nameProduct', $searchFilter['searchFilter']);
                $this->db->or_like('descriptionProduct', $searchFilter['searchFilter']);
            }
            // Si recibimos un limite //
            if ($searchFilter['topFilter'] > 0) {
                $this->db->limit($searchFilter['topFilter']);
            }

            $quuery = $this->db->order_by("tb_product.idProduct", "DESC")->get();


            if ($quuery->num_rows() > 0) {
                return $quuery->result_array();
            }
            return null;
        }
    }


    public function add($product) {
        $this->db->set($this->_setProduct($product))->insert("tb_product");
        if ($this->db->affected_rows() === 1) {
            $id = $this->db->insert_id();
            return $id;
        } else {
            return null;
        }
    }
    
    
    private function _setProduct($product) {
        return array(
            'nameProduct' => @$product['nameProduct'],
            'descriptionProduct' => @$product['descriptionProduct'],
            'salePriceProduct' => @$product['salePriceProduct'],
            'creditPriceProduct' => @$product['creditPriceProduct'],
            'idStatusProduct' => 1
        );
    }
    
    /* EDITAR DATOS DE UN PRODUCTO */

    public function update($product) {
        $this->db->set($this->_setProduct($product))->where("idProduct", $product['idProduct'])->update("tb_product");
        if ($this->db->trans_status() === true) {
            return true;
        } else {
            return null;
        }
    }
    
    public function changueStatus($product) {
        $this->db->set(
                array(
                    'idStatusProduct' => $product['idStatusProduct']
                )
        )->where("idProduct", $product['idProduct'])->update("tb_product");

        if ($this->db->affected_rows() === 1) {
            return true;
        } else {
            return null;
        }
    }
      
}
