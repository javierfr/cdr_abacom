<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // $this->load->helper('url');
        $this->load->model('Auth_model');
    }

    public function index()
    {
        $data['titulo']        = 'Abacon Telecomunicaciones | Login';
        $data['pagina']        = 'Login';
        $data["header"]  = NULL;
        $data["menu_left"]  = NULL;
        // $data["content"] = $this->load->view('auth/auth_view', '', true);
        $data["footer"]  = NULL;
        $this->load->view('auth/auth_view', $data);
    }
    function crearSesionAjax(){  
     
        try {

            $this->form_validation->set_rules('inp-login-email', 'inp-login-email', 'required');
            $this->form_validation->set_rules('inp-login-password', 'inp-login-password', 'required');

            if ($this->form_validation->run()) {

                $data['info_usuario'] = array(
                    'email'    => $this->input->post('inp-login-email'),
                    'password' => $this->input->post('inp-login-password')
                );

                $get_usuario = $this->Auth_model->crearSesionAjax($data['info_usuario']['email'], $data['info_usuario']['password']);

                if ($get_usuario) {
                    $sess_array = array(
                        'id_usuario' => $get_usuario[0]->id_usuario,
                        'usu_nombre' => $get_usuario[0]->usu_nombres,
                        'usu_foto' => $get_usuario[0]->usu_foto
                        // 'usu_nombre' => $get_usuario[0]->username
                    );

                    $this->session->set_userdata('sess_data', $sess_array);
                    // $data['sess_data'] = $sess_array;
                    echo json_encode(array("status" => true));
                }
            }
            
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }
    public function cerrarSesion() {
        if (!isset($this->session->userdata['sess_data']['id_usuario'])) {
            redirect(base_url().'Auth');
        }
        try {
            $this->session->sess_destroy();
            redirect('Auth');
            // redirect('login_controller', 'refresh');
            // echo json_encode(array("status" => true));
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        } //./catch
    }
}
