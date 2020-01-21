<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auth_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
	}


	public function authnube($user)
	{

//echo(sha1(md5($user['userPass'])));

			$auth = null;
      		$hours = true;
     		$VehicleType = null;
			$driver = null;
			$client = null;

			/* verificamos el usuario  */
			$this->db->select(["idUser","userNameUser","emailUser","passUser","idProfileUser"])->from("tb_users");
			$this->db->group_start()
			         ->where("userNameUser =",$user['userName'])
			         ->or_where("emailUser =",$user['userName'])
        ->group_end()
        ->where("idStatusUser",1);
			$query = $this->db->where("passUser =",sha1(md5($user['userPass'])))->get();

			if($query->num_rows() > 0){
  				$user= $query->row_array();

               $modules =   array();

                     /* LISTADO OPCIONES MENU WEB   */
                    $this->db->select("*")->from("tb_sys_module_profile");
                    $this->db->join('tb_sys_modules', 'tb_sys_modules.idItem = tb_sys_module_profile.idModuleKf', 'left');
                    $this->db->where("positionItem >",0);
                    $this->db->where("tb_sys_module_profile.idProfileKf",$user['idProfileUser']);

                    $query = $this->db->order_by("positionItem", "asc")->get();

                    if($query->num_rows() > 0){
                        $headmodules = $query->result_array();
                        $numRs = $query->num_rows();

                        for($i=0; $i<$numRs; $i++)    {

	                        $idRoot =  $headmodules[$i]['idItem'];

	                        /*BUSCAMOS SUB MODULO*/
	                        $query = $this->db->query(" SELECT * from  tb_sys_module_profile
	                            left join tb_sys_modules on tb_sys_modules.idItem = tb_sys_module_profile.idModuleKf
	                            where tb_sys_modules.positionItem = 0 and tb_sys_modules.idRoot = $idRoot and
	                            tb_sys_modules.idItem in (SELECT idModuleKf  from tb_sys_module_profile
	                            where idProfileKf = ".$user['idProfileUser'].")
	                            and idProfileKf = ".$user['idProfileUser']." order by tb_sys_modules.subItemProorityItem
	                            " );

	                        if ($query->num_rows() > 0) {
		                        $subModules = $query->result_array();
		                        $numRs2= $query->num_rows();
		                        $newSub = array();

		                        for($j=0; $j<$numRs2; $j++)    {
		                            array_push($newSub, $subModules[$j]);
		                        }
		                        $headmodules[$i]['submodules'] = $newSub;
		                        array_push($modules ,$headmodules[$i]);
                        	}   
                    	}
                    } else return null;

        $user["header"] = $this->encrypt->encode(json_encode(["idUser" => $user["idUser"],"idProfileUser" => $user["idProfileUser"], "time" => time()]));
        unset($user["passUser"]);
				$auth = array(
					'user' => $user,
					'modules' =>$modules
				);
				return $auth;

			}else{
				return null;
			}
	}

 	public function auth($user)
	{

//echo(sha1(md5($user['userPass'])));

			$auth = null;
      		$hours = true;
      		$VehicleType = null;
			$driver = null;
			$client = null;

			/* verificamos el usuario  */
			$this->db->select("*")->from("tb_user_ex");
			$this->db->join('tb_agency', 'tb_agency.idAgency = tb_user_ex.idAgencyKf', 'left');
			$this->db->where("userNameUser =",$user['userName']);
			$query = $this->db->where("passUser =",sha1(md5($user['userPass'])))->get();



			if($query->num_rows() > 0)
			{


  				$rs = $query->row_array();
  				//print_r($rs);

  				$rsIntance = $this->AutIntance($rs['nameAgency'],$user);
  				//print_r($rsIntance);

				return $rsIntance;

			}else
			{
				return null;
			}
	}


  /* BUSCAMOS LA INSTANCIA Y LO AUTENTICAMOS*/
	public function AutIntance($instance,$user)
	{
		# code...
		$result = null;

		$headers = array
		(
			'Content-Type: application/json'
		);

		$param = array(
						"user" => $user
		);

		$uri = 'http://127.0.0.1/'.$instance.'/Api/index.php/auth';

		//echo $uri;

		$ch = curl_init();
		curl_setopt( $ch,CURLOPT_URL, $uri );
		curl_setopt( $ch,CURLOPT_POST, true );
		curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $param ) );
		$result = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

	

		$json = json_decode($result, true);
			//print_r($result);

		if($httpcode != 200)
		{
			$json = null;
		}

			return $json;
	}





}
