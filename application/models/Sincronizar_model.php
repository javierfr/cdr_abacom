<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

date_default_timezone_set('America/Mexico_City');

class Sincronizar_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }
    // Obtengo todas las troncales
    public function getTroncales() {
        try {
            $this->db->select('*');        
            $this->db->from('troncales');
            $query = $this->db->get();
        
            return $query->result_array();
        } catch(Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }
    // Obtengo todas las llamadas
    public function getLlamadas() {
        try {
            $this->db->select('*');        
            $this->db->from('llamadas');
            $query = $this->db->get();
        
            return $query->result_array();
        } catch(Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }
    // Inserto una llamada en llamadas
    public function addLlamada($data) {
        try {
            $result = $this->db->insert('llamadas', $data);
            return $result;
        } catch(Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }
    // Inserto una llamada en full_llamadas
    public function addLlamadaFull($data) {
        try {
            $result = $this->db->insert('full_llamadas', $data);
            return $result;
        } catch(Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }
    // Actualiza la troncal de la linea
    public function updateTroncalLinea($rfc, $data) {
        $this->db->where('rfc', $rfc);
        $query = $this->db->update('llamadas', $data);
        
        // return $query->result();
    }
    // Borro todas las llamadas
    public function deleteLlamadas() {
        try {
            $this->db->where('id_llamada >', 0);
            $this->db->delete('llamadas');
            // return $result;
        } catch(Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }
    
}
