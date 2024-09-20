<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

date_default_timezone_set('America/Mexico_City');

class Board_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }
    public function getProductosAJAX() {
        try {
            $this->db->select('t.rfc, l.razon_social');
            $this->db->from('troncales t');
            $this->db->join('lineas l', 'l.rfc = t.rfc');
            $this->db->group_by('t.rfc, l.razon_social'); 


            // $result = $this->db->get('troncales');
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
    public function getAllCallsUsuario($razon_social, $fecha_inicio, $fecha_fin){
        try {

            // $this->db->select('rfc');        
            // $this->db->from('lineas');
            // $this->db->where('razon_social', $razon_social);
            // $this->db->limit(1);  
            // $query = $this->db->get();

            $this->db->select('*');        
            $this->db->from('llamadas');
            $this->db->where('razon_social', $razon_social);
            $this->db->where('fecha >=', $fecha_inicio);
            $this->db->where('fecha <=', $fecha_fin);
            $query = $this->db->get();

            return $query->result();
        } catch(Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }
    public function getLineaCallsUsuario($linea, $fecha_inicio, $fecha_fin){
        try {

            $this->db->select('*');        
            $this->db->from('llamadas');
            $this->db->where('origen', $linea);
            $this->db->where('fecha >=', $fecha_inicio);
            $this->db->where('fecha <=', $fecha_fin);

            $query = $this->db->get();
            return $query->result();
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


    public function getClientesAJAX() {
        try {
            $this->db->select('t.rfc, l.razon_social');
            $this->db->from('troncales AS t');
            $this->db->join('lineas AS l', 'l.rfc = t.rfc');
            $this->db->group_by('t.rfc, l.razon_social'); 


            // $result = $this->db->get('troncales');
            $result = $this->db->get();
            return $result->result();
            // return $result;
        } catch(Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    public function getClienteLlamadasAJAX($rfc, $fecha_inicio, $fecha_fin){
        try {

            $this->db->select('*');        
            $this->db->from('llamadas');
            $this->db->where('rfc', $rfc);
            $this->db->where('fecha >=', $fecha_inicio);
            $this->db->where('fecha <=', $fecha_fin);

            $query = $this->db->get();
            return $query->num_rows();
        } catch(Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    // Graficas
    public function getClientesGraficasAJAX() {
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

    public function getClienteLineasAJAX($rfc) {
        try {
            $this->db->select('linea');
            $this->db->from('lineas');
            $this->db->where('rfc', $rfc);

            $result = $this->db->get();

            return $result->result();
            // return $result;
        } catch(Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    public function getClienteLineaLlamadasAJAX($rfc, $linea, $fecha_inicio, $fecha_fin){
        try {

            $this->db->select('*');        
            $this->db->from('llamadas');
            $this->db->where('rfc', $rfc);
            $this->db->where('fecha >=', $fecha_inicio);
            $this->db->where('fecha <=', $fecha_fin);
            $this->db->where('origen', $linea);

            $query = $this->db->get();
            return $query->num_rows();
        } catch(Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }


}
