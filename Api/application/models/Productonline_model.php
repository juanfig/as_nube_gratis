<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Productonline_model extends CI_Model {
  
  public function __construct() {
        parent::__construct();
    }
    
  // BUSCAR LOS PRODUCTOS PARA VENTA ONLINE
  public function get($id = null, $searchFilter = null){

    $this->db->select('*');
    
    // si la busqueda es por id
    if($id){
      $this->db->where('idProductOnline',$id);
    }

    // Si se recibe un filtro //

    if(!is_null($searchFilter['searchFilter'])){
      $this->db->or_like('name',$searchFilter['searchFilter']);
      $this->db->or_like('price',$searchFilter['searchFilter']);
    }

    // Si recibimos un limite //
    if (!is_null($searchFilter['topFilter']) && $searchFilter['topFilter'] > 0) {
        $this->db->limit($searchFilter['topFilter']);
    }

    $query = $this->db->get('tb_product_online');

    if ($query->num_rows() > 0) {
      return $query->result_array();
    }
    return null;
  }

  public function add($object){
    $this->db->set($this->_setProduct($object))->insert('tb_product_online');
    if ($this->db->affected_rows() === 1) {
      $id = $this->db->insert_id();
      return $id;
    } else {
        return null;
    }
  }

  public function update($object){
    $this->db->set($this->_setProduct($object))->where('idProductOnline',$object['idProductOnline'])->update('tb_product_online');
    if($this->db->affected_rows() === 1){
      return true;
    }else{
      return null;
    }
    

  }

  public function delete($id){
    $this->db->where('idProductOnline',$id)->delete('tb_product_online');
    if($this->db->affected_rows() === 1){
      return true;
    }else{
      return false;
    }
  }

  private function _setProduct($product) {
      return array(
          'name' => @$product['nameProduct'],
          'price' => @$product['price'],
          'principalPicture' => @$product['principalPicture'],
          'detailPicture' => @$product['detailPicture'],
      );
    }
}

/* End of file ProductOnline_model.php */
/* Location: ./application/models/ProductOnline_model.php */