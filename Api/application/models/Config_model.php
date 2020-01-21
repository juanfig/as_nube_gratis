<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Config_model extends CI_Model
{
	
	public function __construct()
	{
		parent::__construct();
	}

 	public function getParam()
	{
		$param = null;
			/* LISTADO PARAMETROS DE EL SISTEMA  */
			$query = $this->db->select("*")->from("tb_sys_param")->get();
		
			if($query->num_rows() > 0){ 
				$param = $query->result_array();
				return $param;
				
			}else
			{
				return null;
			}
	}


	 public function chagueParam($filter) {
        
        $this->db->set(
                        array(
                            'value' => $filter['valueParam']
                        )
                )->where("idParam", $filter['idParam'])->update("tb_sys_param");


        if ($this->db->trans_status() === true) {
            return true;
        } else {
            return null;
        }
    }
}