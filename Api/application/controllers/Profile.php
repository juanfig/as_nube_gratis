<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Profile extends REST_Controller {

    public function __construct() {
        parent::__construct();
        if(is_null($this->userInfo) || !in_array($this->userInfo->idProfileUser,[1,3]) ) $this->response(['error' => "No autorizado"],401);
        $this->load->model('profile_model');
    }

    /* SERVICIO GET QUE OBTIENE TODOS LOS PERFILES */
    public function index_get() {

        if(!empty($this->get('restrict'))) {
          //$this->response($this->get(), 200);
          $rs = $this->profile_model->get(null,$this->get());
        } else {
          $rs = $this->profile_model->get();
        }

        if (!is_null($rs)) {
            $this->response($rs, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }

    /* SERVICIO RETORNA UN PERFIL POR ID */
    public function find_get($id) {
        if($this->userInfo->idProfileUser!=1) $this->response(['error' => "No autorizado"],401);
        if (!$id) {
            $this->response(NULL, 404);
        }

        $rs = null;
        $rs = $this->profile_model->get($id, null);

        if (!is_null($rs)) {
            $this->response($rs, 200);
        } else {
            $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
        }
    }


    /* SERVICIO QUE RETORNA LOS FILTROS DE EL MODULO DE CLIENTES */
  	public function filterForm_get($id)
  	{
        if($this->userInfo->idProfileUser!=1) $this->response(['error' => "No autorizado"],401);
       if (!$id) {
            $this->response(NULL, 404);
        }
        
  		$filters = $this->profile_model->getFilterForm($id);

  		if(! is_null($filters))
  		{
  			$this->response($filters,200);
  		}else
  		{
  			$this->response(array('response' => 'NO HAY RESULTADOS'),404);
  		}
  	}


  /* SERVICIO GET QUE OBTIENE LOS PERFILES */
  public function search_post()
  {
        if($this->userInfo->idProfileUser!=1) $this->response(['error' => "No autorizado"],401);

    $searchFilter = $this->post('filter');

    $box = $this->profile_model->get(null,$searchFilter);

    if(! is_null($box))
    {
      $this->response($box,200);
    }else
    {
      $this->response(array('error' => 'NO HAY RESULTADOS'),404);
    }
  }

  /* SERVICIO QUE GUARDA UN PERFIL */
  public function index_post() {

      if($this->userInfo->idProfileUser!=1) $this->response(['error' => "No autorizado"],401);
      if (!$this->post('profile')) {
          $this->response(NULL, 404);
      }

      $idProfile = $this->profile_model->addProfile($this->post('profile'));

      if (!is_null($idProfile)) {
          $this->response($idProfile, 200);
      } else {
          $this->response(array('error' => "ERROR INESPERADO"), 500);
      }
  }

  /* SERVICIO QUE GUARDA MODULOS A PERFILES */
  public function addModule_post() {

        if($this->userInfo->idProfileUser!=1) $this->response(['error' => "No autorizado"],401);
      if (!$this->post('profile')) {
          $this->response(NULL, 404);
      }

      $idProfile = $this->profile_model->addModule($this->post('profile'));

      if (!is_null($idProfile)) {
          $this->response(array('response' => "MODULO AGREGADO "), 200);
      } else {
          $this->response(array('error' => "ERROR INESPERADO"), 500);
      }
  }

  /* SERVICIO EDITA NOMBRE DE PERFIL  */
	public function update_post()
	{
        if($this->userInfo->idProfileUser!=1) $this->response(['error' => "No autorizado"],401);
		if(!$this->post('profile'))
		{
			$this->response(NULL,404);
		}


		$rs = $this->profile_model->update($this->post('profile'));
		if(!is_null($rs))
		{
			$this->response(array('response' => "Perfil Actualizado"),200);
		}else
		{
			$this->response(array('error' => "ERROR INESPERADO"),500);
		}

	}

  /* SERVICIO CAMBIA EL SATAUS DE PERFIL POR ID */
  public function inactive_get($id) {
        if($this->userInfo->idProfileUser!=1) $this->response(['error' => "No autorizado"],401);
      if (!$id) {
          $this->response(NULL, 404);
      }

      $rs = null;
      $rs = $this->profile_model->changueStatus($id, 0);

      if (!is_null($rs)) {
          $this->response($rs, 200);
      } else {
          $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
      }
  }

  /* SERVICIO CAMBIA EL SATAUS DE PERFIL POR ID */
  public function active_get($id) {
        if($this->userInfo->idProfileUser!=1) $this->response(['error' => "No autorizado"],401);
      if (!$id) {
          $this->response(NULL, 404);
      }

      $rs = null;
      $rs = $this->profile_model->changueStatus($id, 1);

      if (!is_null($rs)) {
          $this->response($rs, 200);
      } else {
          $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
      }
  }

  /* SERVICIO INACTIVA  UN CHOFER POR ID */
  public function deleteModule_post() {
        if($this->userInfo->idProfileUser!=1) $this->response(['error' => "No autorizado"],401);
      if (!$this->post('profile')) {
          $this->response(NULL, 404);
      }

      $driver = null;
      $driver = $this->profile_model->deleteModule($this->post('profile'));

      if (!is_null($driver)) {
          $this->response($driver, 200);
      } else {
          $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
      }
  }

  /* SERVICIO CAMBIA EL SATAUS DE PERFIL POR ID */
  public function delete_get($id) {
        if($this->userInfo->idProfileUser!=1) $this->response(['error' => "No autorizado"],401);
      if (!$id) {
          $this->response(NULL, 404);
      }

      $rs = null;
      $rs = $this->profile_model->changueStatus($id, -1);

      if (!is_null($rs)) {
          $this->response($rs, 200);
      } else {
          $this->response(array('error' => 'NO HAY RESULTADOS'), 404);
      }
  }



    

}
?>
