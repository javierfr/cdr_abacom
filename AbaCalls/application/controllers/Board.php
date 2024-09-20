<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Board extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // $this->load->helper('url');
        // $this->load->model('Calls_model');
        $this->load->model('Board_model');
    }

    public function index(){
        if (!isset($this->session->userdata['sess_data']['id_usuario'])) {
            redirect(base_url().'Auth');
        }
        try {
            $data['titulo']        = 'Abacon Telecomunicaciones | Gráficas';
            $data['pagina']        = 'Llamadas';
            $data["header"]  = $this->load->view('layout/layout_menu', '', true);
            $data["menu_left"]  = $this->load->view('layout/layout_menu_left', '', true);
            $data["content"] = $this->load->view('board/board_view', '', true);
            $data["footer"]  = $this->load->view('layout/layout_footer', '', true);
            
            $this->load->view('main_view.php', $data);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        } //./catch
    }

    function getClientesAJAX(){  
        if (!isset($this->session->userdata['sess_data']['id_usuario'])) {
            redirect(base_url().'Auth');
        }
        try {
            $productos = $this->Board_model->getProductosAJAX();
            echo json_encode($productos);
        } catch (Exception $e) {
            echo('error');
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    public function getCallsUsuario($rfc) {
        if (!isset($this->session->userdata['sess_data']['id_usuario'])) {
            redirect(base_url().'Auth');
        }
        try {
            // TLE011122SC2
            $llamadas_usuario["lineas"] = $this->Board_model->getLineasUsuario($rfc);
            $llamadas_usuario["llamadas"] = $this->Board_model->getCallsUsuario($rfc);

            $data['pagina']        = 'Llamadas';
            $data["header"]  = $this->load->view('layout/layout_menu', '', true);
            $data["menu_left"]  = $this->load->view('layout/layout_menu_left', '', true);
            $data["content"] = $this->load->view('calls/calls_usuario_view', $llamadas_usuario, true);
            $data["footer"]  = $this->load->view('layout/layout_footer', '', true);

            $this->load->view('main_view.php', $data);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        } //./catch
    }

    function filtrarLlamadasAjax(){  
        if (!isset($this->session->userdata['sess_data']['id_usuario'])) {
            redirect(base_url().'Auth');
        }
        try {

            $razon_social = trim($this->input->post('razon_social'));
            $linea = $this->input->post('linea');
            $fecha = $this->input->post('fecha');

            $fecha_inicio = date("Y-m-d",strtotime(substr($fecha, 0, -13)));
            $fecha_fin = date("Y-m-d",strtotime(substr($fecha, 13)));

            if ($linea == "Todas") {
                $all_calls_usuario = $this->Board_model->getAllCallsUsuario($razon_social, $fecha_inicio, $fecha_fin);
                echo json_encode($all_calls_usuario);
            } else {
                $linea_calls_usuario = $this->Board_model->getLineaCallsUsuario($linea, $fecha_inicio, $fecha_fin);
                echo json_encode($linea_calls_usuario);
            }

            
        } catch (Exception $e) {
            echo('error');
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

    function getDataClientesAJAX(){  
        if (!isset($this->session->userdata['sess_data']['id_usuario'])) {
            redirect(base_url().'Auth');
        }
        try {
            
            $fecha_inicio = substr($this->input->post('inp-filtro-graficas-fechas'), 0, 10);
            $fecha_fin = substr($this->input->post('inp-filtro-graficas-fechas'), -10, 10);

            $clientes = $this->Board_model->getClientesAJAX();
            $cliente = array();
            $llamadas = array();

            foreach ($clientes as $key => $value) {
                array_push($cliente, $value->razon_social);
                array_push($llamadas, $this->Board_model->getClienteLlamadasAJAX($value->rfc , $fecha_inicio, $fecha_fin));
            }

            $data['clientes'] = $cliente;
            $data['llamadas'] = $llamadas;

            echo json_encode($data);
        } catch (Exception $e) {
            echo('error');
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    // Graficas
    function getClientesGraficasAJAX(){  
        if (!isset($this->session->userdata['sess_data']['id_usuario'])) {
            redirect(base_url().'Auth');
        }
        try {

            $fecha_inicio = substr($this->input->post('inp-filtro-calls-fechas'), 0, 10);
            $fecha_fin = substr($this->input->post('inp-filtro-calls-fechas'), -10, 10);

            $clientes = $this->Board_model->getClientesGraficasAJAX();

            $cliente = array();
            $rfc = array();
            $lineas = array();
            
            $llamadas = array();

            foreach ($clientes as $key => $value) {

                $lineas_1 = array();
                $llamadas_1 = array();

                $cliente[] = $value->razon_social;
                $rfc[] = $value->rfc;

                $data_lineas = $this->Board_model->getClienteLineasAJAX($value->rfc);

                // obtengo las llamadas de cada linea
                foreach ($data_lineas as $key => $value1) {
                    array_push($llamadas_1, $this->Board_model->getClienteLineaLlamadasAJAX($value->rfc, $value1->linea, $fecha_inicio, $fecha_fin));
                }

                // obtengo las lineas
                foreach ($data_lineas as $key => $value2) {
                    array_push($lineas_1, $value2->linea);
                }

                $lineas[] = $lineas_1;
                $llamadas[] = $llamadas_1;

            }

            $data['clientes'] = $cliente;
            $data['rfc'] = $rfc;
            $data['lineas'] = $lineas;
            $data['llamadas'] = $llamadas;

            echo json_encode($data);

        } catch (Exception $e) {
            echo('error');
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }
}
