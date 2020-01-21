<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class ProductOnline extends REST_Controller {

  public function __construct()
  {
    parent::__construct();
    		if((is_null($this->userInfo) && $this->router->fetch_method() !="index" ) ||
		  (!is_null($this->userInfo) && !in_array($this->userInfo->idProfileUser,[1,3])) )
    		$this->response(['error' => "No autorizado"],401);
    //Load Dependencies
    $this->load->model(['productonline_model']);
  }

  //SERVICIO PARA LISTAR LOS PRODUCTOS
  public function index_get()
  {
    $result = $this->productonline_model->get();

    if ($result) {
        $this->response($result, 200);
    } else {
        $this->response(array('error' => 'Sin registros!'), 404);
    }
  }

  //SERVICIO QUE GUARDA UN REGISTRO
  public function add_post()
  {
    if(!$this->post('product')){
      return $this->response(['error' => 'Petición incorrecta'],500);
    }

    $id_product = $this->productonline_model->add($this->post('product'));
    if($id_product){
      return $this->response(['idProduct' => $id_product,'response' => "Registro agregado con éxito"],200);
    }else{
      return $this->response(['error'  => 'Ha ocurrido un error'],500);
    }
  }

  // SERVICIO PARA BUSCAR UN REGISTRO 
  public function find_get($id){
    
    $result = $this->productonline_model->get($id);

    if($result){
      return $this->response($result[0],200);
    }else{
      return $this->response(['error' => ""],500);
    }
  }
  
  // SERVICIO PARA MODIFICAR UN REGISTRO
  public function update_post()
  {
    if(!$this->post('product')){
      return $this->response(['error' => "Petición Incorrecta"],500);
    }

    $res = $this->productonline_model->update($this->post('product'));
    if($res){
      return $this->response(['response' => "Registro modificado con éxito"],200);
    }else{
      return $this->response(['error' => "Ha ocurrido un error"],500);
    }
  }

  // SERVICIO PARA ELIMINAR UN REGISTRO
  public function delete_post( $id = NULL )
  {
    if(!$id){
      return $this->response(["error" => 'Petición Incorrecta'],500);
    }

    $register = $this->productonline_model->get($id);
    
    
    
    $images = ['principalPicture' => $register[0]['principalPicture'], 'detailPicture' => $register[0]['detailPicture'] ];

    $res = $this->productonline_model->delete($id);
    if($res){
      return $this->response(['response' => "Registro eliminado con éxito", "images" => $images],200);
    }else{
      return $this->response(['error' => "Ha ocurrido un error"],500);
    }
  }

  // SERVICIO PARA MOSTRAR VARIOS REGISTROS POR PARAMETROS DE BUSQUEDA
  public function show_post(){
    $result = $this->productonline_model->get(null,$this->post('filter'));

    if ($result) {
        $this->response($result, 200);
    } else {
        $this->response(array('error' => 'Sin registros!'), 404);
    }
  }
}

/* End of file ProductOnline.php */
/* Location: ./application/controllers/ProductOnline.php */
