<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

class Expense extends REST_Controller {

    public function __construct() {
        parent::__construct();
        if(is_null($this->userInfo) || $this->userInfo->idProfileUser!=1) $this->response(['error' => "No autorizado"],401);
        $this->load->model('expense_model');
        $this->load->model('check_model');
    }

		//-SERVICIO GET QUE OBTIENE TODOS LOS TIPOS DE EGRESOS------------------------------------*/
    public function index_get() {

        $expense = $this->expense_model->get($this->get());

        if (!is_null($expense)) {
            $this->response($expense, 200);
        } else {
            $this->response([], 200);
            //$this->response(['error' => 'NO HAY RESULTADOS'], 200);
        }
    }

		//-SERVICIO QUE GUARDA UN TIPO DE EGRESO--------------------------------------------------*/
    public function add_post() {
        if (!$this->post()) {
            $this->response(NULL, 404);
        }

    		$checks = $this->post('checks');
    		$expense = $this->post('expense');
    		$expense['dateExpense'] =  date('Y-m-d H:i:s');
        $expense = $this->expense_model->add($expense);

        if (!is_null($expense)) {
						if (!empty($checks)) {
							foreach($checks as $check){


                        $this->check_model->add($check,
                        ["table"=>'tb_check_expense',
                        "check"=>[
                        'idExpenseKf'=>$expense,
                        'idCheckKf'=>!empty($check['idCheck']) ? $check['idCheck'] : null
                        ]
                        ]);
							}
						}
            $this->response(['expense'=>$expense,'response' => "NUEVO TIPO DE EGRESO CREADO"], 200);

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

        $expense = $this->expense_model->getThis($post_data['idExpense']);

      $checks = $this->check_model->get($post_data); // cheques que pertenecen a un egreso

        if (!is_null($expense)) {
            $this->response(['expense' => $expense,'checks'=> $checks = (!is_null($checks)) ? $checks : null ], 200);
        } else {
            $this->response(['error' => 'NO HAY RESULTADOS'], 404);
        }
    }

		//-SERVICIO QUE ACTUALIZA UN TIPO DE EGRESO-----------------------------------------------*/
    public function update_post() {

        $expense = $this->expense_model->update($this->post());

        if (!is_null($expense)) {
            $this->response(['response' => "TIPO DE EGRESO MODIFICADO"], 200);
        } else {
            $this->response(['error' => "ERROR INESPERADO"], 500);
        }
    }


		//-SERVICIO QUE ELIMINA UN TIPO DE EGRESO-------------------------------------------------*/
    public function delete_post() {

        $expense = $this->expense_model->erase($this->post()["idExpense"]);

        if (!is_null($expense)) {
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
