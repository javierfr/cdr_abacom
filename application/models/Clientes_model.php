<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

date_default_timezone_set('America/Mexico_City');

class Clientes_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }
    public function getClientesAJAX() {
        try {
            $this->db->select('t.rfc, l.razon_social');
            $this->db->from('troncales AS t');
            $this->db->join('lineas AS l', 'l.rfc = t.rfc');
            $this->db->group_by('t.rfc, l.razon_social'); 

            $result = $this->db->get();
            return $result->result();
            // return $result;
        } catch(Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }
    public function getCallsUsuario($rfc){
        try {
            $this->db->select('*');        
            $this->db->from('llamadas');
            $this->db->where('rfc', $rfc);

            $query = $this->db->get();
            return $query->result();
        } catch(Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }
    public function getAllCallsUsuario($data){
        try {

            $this->db->select('*');        
            $this->db->from('llamadas');
            $this->db->where('rfc', $data['llamada']['rfc']);
            $this->db->where('fecha >=', $data['llamada']['fecha_inicio']);
            $this->db->where('fecha <=', $data['llamada']['fecha_fin']);
            if ($data['llamada']['linea'] != 'Todas') {
                $this->db->where('origen', $data['llamada']['linea']);
            }

            $query = $this->db->get();
        
            return $query->result_array();
        } catch(Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }
    
    public function getLineasUsuario($rfc){
        try {
            $this->db->select('linea, razon_social');        
            $this->db->from('lineas');
            $this->db->where('rfc', $rfc);

            $query = $this->db->get();
            return $query->result();
        } catch(Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }
}
