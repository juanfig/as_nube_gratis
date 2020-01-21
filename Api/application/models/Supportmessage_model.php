<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

date_default_timezone_set("America/Argentina/Buenos_Aires");

class Supportmessage_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }


    public function get($id = null, $searchFilter = null, $idAgency = null) {
        $quuery = null;

        
    }

    /* AGRAGR NUEVO */

    public function add($support_message) {
        $this->db->insert('tb_support_agency_messages', $support_message
            );

        if ($this->db->affected_rows() === 1) {
            return $this->db->insert_id();
        } else {
            return null;
        }
    }

   

}