<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

date_default_timezone_set('America/Mexico_City');

class Auth_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }
    public function crearSesionAjax($email, $password)
    {
        try {
            $this->db->select('*');
            $this->db->from('usuarios');
            $this->db->where('usu_email', $email);
            $this->db->where('usu_password', md5($password));
            $this->db->limit(1);
            $query = $this->db->get();

            if ($query->num_rows() == 1) {
                return $query->result();
            } else {
                return false;
            }
        } catch(Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }
    public function addUserAjax($data) {
        try {
            $result = $this->db->insert('usuarios', $data);
            return $result;
        } catch(Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }



    // --------------------------------------------------------------------

    public function getDirectorioAjax() {
        try {
            $this->db->select('ia.*, il.order, il.name leadership, id.description department');        
            $this->db->from('institutional_administrative as ia');
            $this->db->join('institutional_departments as id', 'id.id = ia.idDepartment');
            $this->db->join('institutional_leaderships as il', 'il.id = id.idLeadership');
            $this->db->where('ia.status', 'ACTIVO');
            $this->db->where('ia.directory', 1);

            $query = $this->db->get();
            return $query->result();
        } catch(Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }


        // $sql = "SELECT PP.location, LED.`order`, PP.enrollment, TRIM(CONCAT(PP.`name`,' ',PP.lastName,' ',PP.lastName2)) AS `name`, PP.job,
        //     PP.instEmail, PP.extension, LED.name leadership, DEP.description department,
        //     IF(ISNULL(PP.photo),'files/admin_photos/aguila.png',PP.photo) AS photo
        //     FROM institutional_administrative PP
        //     INNER JOIN institutional_departments DEP ON PP.idDepartment = DEP.id
        //     INNER JOIN institutional_leaderships LED ON DEP.idLeadership = LED.id
        //     WHERE PP.`status` = 'ACTIVO' AND PP.`directory` = 1
        //     ORDER BY PP.location ASC, LED.`order` ASC,  DEP.isLeadership DESC, DEP.isAssistant DESC, DEP.description ASC;";

        // return $this->db->query($sql);
    }

    // -----------------------------------------------------------------
    public function getAlumnosAjax() {
        try {
            $this->db->select('*');        
            $this->db->from('institutional_students');
            $this->db->where('type', 'ALUMNO');

            $query = $this->db->get();
            return $query->result();
        } catch(Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    public function searchAlumnoAjax($id_matricula){
        try {

            $this->db_sqlserver->select('p.*, pt.PEOPLE_TYPE, a.CURRICULUM, ea.Email, pp.PhoneType, pp.PhoneNumber, ad.STATE, ad.CITY, ad.ADDRESS_LINE_1, ad.ADDRESS_LINE_2, ad.ADDRESS_LINE_3, ad.ADDRESS_LINE_4, a.CLASS_LEVEL, a.ACADEMIC_YEAR, a.ACADEMIC_TERM, a.POPULATION');        
            $this->db_sqlserver->from('PEOPLE as p');
            $this->db_sqlserver->join('PEOPLETYPE as pt', 'p.PEOPLE_ID = pt.PEOPLE_ID');
            $this->db_sqlserver->join('ACADEMIC as a', 'p.PEOPLE_ID = a.PEOPLE_ID');
            $this->db_sqlserver->join('EmailAddress as ea', 'p.PrimaryEmailId = ea.EmailAddressId');
            $this->db_sqlserver->join('PersonPhone as pp', 'p.PrimaryPhoneId = pp.PersonPhoneId');
            $this->db_sqlserver->join('ADDRESS as ad', 'p.PEOPLE_ID = ad.PEOPLE_ORG_ID');
            $this->db_sqlserver->where('p.PEOPLE_ID', 00000+$id_matricula);
            $this->db_sqlserver->limit(1);

            $query = $this->db_sqlserver->get();
            return $query->result();
        } catch(Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }
    
    public function searchCarrera($carrera){
        try {
            $this->db->select('*');        
            $this->db->from('parking_career');
            $this->db->where('abbreviation', $carrera);

            $query = $this->db->get();
            return $query->result();
        } catch(Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }
}
