<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Driverschedule_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }
    
    // GET DE LISTADO BUSQUEDA DE HORARIO //
    public function get($id = null, $searchFilter = null) {
        
        $quuery = null;

        // SI RECIBIMOS EL ID DEL HORARIO //
        if (!is_null($id)) {
            $this->db->select("*")->from("tb_driver_schedule");
            $this->db->join('tb_driver', 'tb_driver.idDriver = tb_driver_schedule.idDriverKf', 'left');
            $quuery = $this->db->where("tb_driver_schedule.idDriverSchedule = ", $id)->get();
            if ($quuery->num_rows() === 1) {
                $rs = array('driver' => $quuery->row_array());
                
                if ($rs['driver']['isMonday'] == 1) {
                    $rs['driver']['isMonday'] = true;
                }
                if ($rs['driver']['isTuesday'] == 1) {
                    $rs['driver']['isTuesday'] = true;
                }
                if ($rs['driver']['isWednesday'] == 1) {
                    $rs['driver']['isWednesday'] = true;
                }
                if ($rs['driver']['isThursday'] == 1) {
                    $rs['driver']['isThursday'] = true;
                }
                if ($rs['driver']['isFriday'] == 1) {
                    $rs['driver']['isFriday'] = true;
                }
                if ($rs['driver']['isSaturday'] == 1) {
                    $rs['driver']['isSaturday'] = true;
                }
                if ($rs['driver']['isSunday'] == 1) {
                    $rs['driver']['isSunday'] = true;
                }
                return $rs;
            }
            return null;
        } else { // SI NO RECIBIMOS EL ID DEL HORARIO RETORNAMOS TODOS LOS REGISTROS 
            $this->db->select("*")->from("tb_driver_schedule");
            $this->db->join('tb_driver', 'tb_driver.idDriver = tb_driver_schedule.idDriverKf', 'left');
            /* Busqueda por filtro */

            if (!is_null($searchFilter['searchFilter'])) {

                $this->db->like('tb_driver.fisrtNameDriver', $searchFilter['searchFilter']);
                $this->db->or_like('tb_driver.lastNameDriver', $searchFilter['searchFilter']);
            }


            // Si recibimos un limite //
            if ($searchFilter['topFilter'] > 0) {
                $this->db->where("tb_driver_schedule.idStatusDriverSchedule !=", -1);
                $this->db->limit($searchFilter['topFilter']);
            } else {
                $this->db->where("tb_driver_schedule.idStatusDriverSchedule !=", -1);
            }

            $quuery = $this->db->order_by("tb_driver_schedule.idDriverSchedule", "DESC")->get();


            if ($quuery->num_rows() > 0) {
                return $quuery->result_array();
            }
            return null;
        }
    }
	
	/* LISTADO DE FILTROS */
    public function getFilterForm() {

        $preferentdriver = null;
        $car =  null;

        /* LISTADO DE CHOFERE */
        $query = $this->db->select("*")->from("tb_driver")->where("idStatusDriver !=", -1)->get();
        if ($query->num_rows() > 0) {
            $preferentdriver = $query->result_array();
        }


        /* LISTADO DE VEHICULOS */
        $query = $this->db->query(" 
            SELECT * FROM `tb_veichle` 
            LEFT JOIN td_vehicle_brand t2 ON t2.idVehicleBrand = tb_veichle.idVeichleBrandAsigned 
            LEFT JOIN td_vehicle_model t3 ON t3.idVehicleModel = tb_veichle.idVehicleModelAsigned 
            where idStatusVeichle = 1
        ");
        if ($query->num_rows() > 0) {
            $car = $query->result_array();
        }

       

        $filter = array(
            'preferentdriver' => $preferentdriver,
            'car' => $car
        );

        return $filter;
    }

    public function add($driver) {


        $this->db->set($this->_setDriver($driver))->insert("tb_driver_schedule");


        if ($this->db->affected_rows() === 1) {
            $id = $this->db->insert_id();
            return $id;
        } else {
            return null;
        }
    }
    
    
    private function _setDriver($driver) {

        return array(
            'idDriverKf' => @$driver['idDriverKf'],
            'hourStart' => @$driver['hourStart'],
            'hourEnd' => @$driver['hourEnd'],
            'isMonday' => @$driver['isMonday'],
            'isTuesday' => @$driver['isTuesday'],
            'isWednesday' => @$driver['isWednesday'],
            'isThursday' => @$driver['isThursday'],
            'isFriday' => @$driver['isFriday'],
            'isSaturday' => @$driver['isSaturday'],
            'isSunday' => @$driver['isSunday'],
            'idStatusDriverSchedule' => 1
        );
    }
    
    /* EDITAR DATOS DE UN HORARIO */

    public function update($driver) {

        $this->db->set($this->_setDriver($driver))->where("idDriverSchedule", $driver['idDriverSchedule'])->update("tb_driver_schedule");

        if ($this->db->trans_status() === true) {
            return true;
        } else {
            return null;
        }
    }
    
    public function changueStatus($id, $idStatus) {
        $this->db->set(
                array(
                    'idStatusHotel' => $idStatus
                )
        )->where("idStatusDriverSchedule", $id)->update("tb_driver_schedule");


        if ($this->db->affected_rows() === 1) {
            return true;
        } else {
            return null;
        }
    }
  
}
