<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

date_default_timezone_set('America/Mexico_City');

class SincronizacionAutomatica_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }
    // public function deleteTroncalesDB() {
    //     try {
    //         $this->db->where('id_troncal >', 0);
    //         $this->db->delete('troncales');
    //     } catch(Exception $e) {
    //         show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
    //     }
    // }
    public function deleteTroncalDB($id_troncal) {
        try {
            $this->db->where('id_troncal', $id_troncal);
            $this->db->delete('troncales');
            // return $result;
        } catch(Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }
    public function insertTroncalesDB($data) {
        try {
            $result = $this->db->insert('troncales',$data);
            return $result;
        } catch(Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }
    public function getTroncalesDB() {
        try {
            $this->db->select('*');        
            $this->db->from('troncales');
            $query = $this->db->get();
        
            return $query->result_array();
        } catch(Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }
    public function getLineasDB() {
        try {
            $this->db->select('*');        
            $this->db->from('lineas');
            $query = $this->db->get();
        
            return $query->result_array();
        } catch(Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }
    public function getLineasTroncalDB($rfc) {
        try {
            $this->db->select('*');        
            $this->db->from('lineas');
            $this->db->where('rfc', $rfc );
            $query = $this->db->get();
        
            return $query->result_array();
        } catch(Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }


    // Actualiza la troncal de la linea
    public function updateTroncalLineaDB($rfc, $data) {
        $this->db->where('rfc', $rfc);
        $query = $this->db->update('lineas', $data);
        
        // return $query->result();
    }
            
    // Falta llamar a esta funcion
    public function insertLineasDB($data) {
        try {
            $result = $this->db->insert('lineas',$data);
            return $result;
        } catch(Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }
    public function deleteLineasDB() {
        try {
            $this->db->where('id_linea >', 0);
            $this->db->delete('lineas');
            // return $result;
        } catch(Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }
    public function insertLlamadasDB($data) {
        try {
            $result = $this->db->insert('llamadas',$data);
            return $result;
        } catch(Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }
    public function deleteLlamadasDB() {
        try {
            $this->db->where('id_llamada >', 0);
            $this->db->delete('llamadas');
            // return $result;
        } catch(Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }
    public function getLlamadasDB() {
        try {
            $this->db->select('*');        
            $this->db->from('llamadas');
            $query = $this->db->get();
            
            return $query->result();
        } catch(Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }
    public function updateTroncalLlamadaDB($rfc, $data) {
        try {
            //$this->db->where('rfc', $rfc);
            //$this->db->update('llamadas', $data);
            
            $this->db->where('rfc', $rfc);
            $query = $this->db->update('llamadas', $data);
            
            //return TRUE;
        } catch(Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

}
