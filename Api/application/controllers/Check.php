<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Check extends REST_Controller {

    public function __construct() {
        parent::__construct();
        if(is_null($this->userInfo) || $this->userInfo->idProfileUser!=1) $this->response(['error' => "No autorizado"],401);
        $this->load->model('check_model');
    }

		//-SERVICIO GET QUE OBTIENE TODOS LOS CHEQUES------------------------------------*/
    public function index_get() {

        $check = $this->check_model->get($this->get());

        if (!is_null($check)) {
            $this->response($check, 200);
        } else {
            $this->response([], 200);
            //$this->response(['error' => 'NO HAY RESULTADOS'], 200);
        }
    }

		//-SERVICIO QUE GUARDA UN CHEQUE-------------------------------------------------*/
    public function add_post() {
        if (!$this->post()) {
            $this->response(NULL, 404);
        }


    		$check = $this->post('check');
    		$check['datecheck'] =  date('Y-m-d H:i:s');
        $check = $this->check_model->add($check);

        if (!is_null($check)) {
						if (!empty($checks)) {
							foreach($checks as $check){


                        $this->check_model->add($check,
                        ["table"=>'tb_check_expense',
                        "check"=>[
                        'idcheckKf'=>$check,
                        'idCheckKf'=>!empty($check['idCheck']) ? $check['idCheck'] : null
                        ]
                        ]);
							}
						}
            $this->response(['check'=>$check,'response' => "CHEQUE CREADO CORRECTAMENTE"], 200);

        } else {
            $this->response(['error' => "ERROR INESPERADO"], 500);
        }
    }

		//-SERVICIO RETORNA UN TIPO DE EGRESO POR ID----------------------------------------------*/
    public function find_post() {

        if (!$this->post()) {
             $this->response(NULL, 404);
        }

        $post_data=$this->post();

     $check = $this->check_model->getThis($post_data['idcheck']);

      $checks = $this->check_model->get($post_data); // cheques que pertenecen a un egreso

        if (!is_null($check) || !is_null($checks)) {
            $this->response(['check' => $check = (!is_null($check)) ? $check : null ,'checks'=> $checks = (!is_null($checks)) ? $checks : null ], 200);
        } else {
            $this->response(['error' => 'NO HAY RESULTADOS'], 404);
        }
    }

		//-SERVICIO QUE ACTUALIZA UN TIPO DE EGRESO-----------------------------------------------*/
    public function update_post() {

        $check = $this->check_model->update($this->post('check'));

        if (!is_null($check)) {
            $this->response(['response' => "TIPO DE EGRESO MODIFICADO"], 200);
        } else {
            $this->response(['error' => "ERROR INESPERADO"], 500);
        }
    }


		//-SERVICIO QUE ELIMINA UN TIPO DE EGRESO-------------------------------------------------*/
    public function delete_post() {

        $check = $this->check_model->erase($this->post()["idcheck"]);

        if (!is_null($check)) {
            $this->response(['response' => "TIPO DE EGRESO ELIMINADO"], 200);
        } else {
            $this->response(['error' => "ERROR INESPERADO"], 500);
        }
    }
		//-SERVICIO GET QUE OBTIENE TODOS LOS CHEQUES DE TERCEROS NO USADOS-----------------------*/
    public function checksAvailables_get() {

        $availables = $this->check_model->availables();

        if (!is_null($availables)) {
            $this->response($availables, 200);
        } else {
            $this->response([], 200);
        }
    }
}

?>
