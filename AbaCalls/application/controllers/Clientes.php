<?php
defined('BASEPATH') or exit('No direct script access allowed');
ini_set('max_execution_time', 0); 
ini_set('memory_limit','2048M');
require_once(APPPATH.'libraries/lib/xmlrpc/xmlrpc.inc');

class Clientes extends CI_Controller
{

    private $nom_servnet = '';
    private $pass_servnet = '';

    private $key_creator = '';
    private $key_crm = '';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Clientes_model');
        $this->load->model('SincronizacionAutomatica_model');
        // Inicializo las contraseñas
        $this->nom_servnet = "ABACOM";
        $this->pass_servnet = "RnTMjphD9N57ZXZVEU";

        $this->key_creator = "";
        $this->key_crm = "";
    }

    public function index()
    {
        if (!isset($this->session->userdata['sess_data']['id_usuario'])) {
            redirect(base_url().'Auth');
        }
        try {
            $data['titulo']        = 'Abacon Telecomunicaciones | Clientes';
            $data['pagina']        = 'Llamadas';
            $data['modulo'] = 'Clientes';
            $data["header"]  = $this->load->view('layout/layout_menu', '', true);
            $data["menu_left"]  = $this->load->view('layout/layout_menu_left', '', true);
            // $data["content"] = $this->load->view('calls/calls_view', '', true);
            $data["footer"]  = $this->load->view('layout/layout_footer', '', true);
            
            $this->load->view('clientes/clientes_view', $data);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        } //./catch
    }

    function uploadClientesAjax(){  
        if (!isset($this->session->userdata['sess_data']['id_usuario'])) {
            redirect(base_url().'Auth');
        }
        try {
            // Obtengo las troncales principales
            $all_troncales = $this->getTroncalesServnet();

            $data_accounts = array();
            for ($i=0; $i < count($all_troncales->val->me['struct']['accounts']->me['array']); $i++) { 
                $rfc = $all_troncales->val->me['struct']['accounts']->me['array'][$i]->me['struct']['description']->me['string'];
                $id_cdr = $all_troncales->val->me['struct']['accounts']->me['array'][$i]->me['struct']['i_account']->me['int'];

                if (!empty($rfc) && !empty($id_cdr)) {
                    $data_accounts[] = array(
                        'id_cdr' => $id_cdr,
                        'rfc' => $rfc
                    );
                }
            }

            $data_lineas = $this->getLineasClienteServnet($data_accounts);

            $data_accounts = $this->unificarTroncalesServnet($data_lineas);
            // Obtengo las troncales de la BD
            $troncales_db = $this->getTroncalesDB();
            // $data_accounts = $this->unificarTroncalesServnet($data_accounts);
            $this->insertTroncalesDBCreator($data_accounts, $troncales_db);
            // print_r($data_accounts);

            // echo json_encode($data_accounts);
            echo json_encode(array("status" => true));

        } catch (Exception $e) {
            echo('error');
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    function unificarTroncalesServnet($data_accounts)
    {
        try {

            $data_accounts_unificado = array();
           
            foreach($data_accounts as $value) {
                if (!in_array($value['rfc'], array_column($data_accounts_unificado, 'rfc'))) {
                    $data_lineas = array();

                    foreach ($data_accounts as $key2 => $value2) {
                        if ($value['rfc'] == $value2['rfc']) {
                            $data_lineas = array_merge($data_lineas, $value2['lineas']);
                        }
                    }

                    $data_accounts_unificado[] = array(
                        'id_cdr' => $value['id_cdr'], 
                        'rfc' => $value['rfc'],
                        'razon_social' => $value['razon_social'],
                        'lineas' => $data_lineas
                    );
                }
            }

            return $data_accounts_unificado;

        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        } //./catch
    }
    // Esta funcion inserta las troncales en ls BD
    function insertTroncalesDBCreator($troncales_servnet, $troncales_db)
    {
        try {
            if (count($troncales_servnet) > 0) {
                // ------------- INSERTO EN CREATOR -------------
                // Genero key de Creator
                $this->key_creator = $this->conexionCreator();
                // Borro la cargar inicial de Creator
                $this->deleteCargaIncialCreator();
                // Iniserto la cargar inicial de Creator
                $this->armarCargaInicialCreator($troncales_servnet);

                // ------------- INSERTO EN LA BD -------------
                $data_troncales = array();
                foreach ($troncales_servnet as $key => $value) {
                    
                    $clave = array_search($value['rfc'], array_column($troncales_db, 'rfc'));
                    // Si la troncal no existe la agregamos
                    if ($clave == "") {
                        // Armo el array para insertar la troncal
                        $data_troncal = array('troncal' => $value['id_cdr'], 'rfc' => $value['rfc']);
                        // Inserto registros a la BD
                        $this->SincronizacionAutomatica_model->insertTroncalesDB($data_troncal);
                        // Veo si la linea no existe de las que ya tiene
                        foreach ($value['lineas'] as $key2 => $value2) {
                            $data_linea = array(
                                'linea' => $value2, 
                                'rfc' => $value['rfc'],
                                'razon_social' => $value['razon_social'],
                                'troncal' => $value['id_cdr']
                            );
                            // Inserto las lineas
                            $this->SincronizacionAutomatica_model->insertLineasDB($data_linea);
                        }
                    } else {
                        // Si la troncal ya existe valido que tenga todas sus lineas dadas de alta
                        if ($clave != "") {
                            $response = $troncales_db[$clave]['lineas'];
                            if (!empty($response)) {
                                $array_lineas_uno = array();
                                // Meto las lineas reistradas en la BD en un array
                                foreach ($response as $key3 => $value3) {
                                    $array_lineas_uno[] = $value3['linea'];
                                }
                                // Busco los que faltan agregar en la BD para iposteriormente insertarlos
                                foreach ($value['lineas'] as $key4 => $value4) {
                                    // Inserto las lineas que falta, pero antes de eso valido que no este
                                    $clave2 = array_search($value4, $array_lineas_uno);
                                    // Si no esta en el arreglo de la BD lo inserto
                                    if($clave2 == ""){
                                        // Inserto en la BD
                                        $data_linea = array(
                                            'linea' => $value4, 
                                            'rfc' => $value['rfc'],
                                            'razon_social' => 'falta llenar',
                                            'troncal' => $value['id_cdr']
                                        );
                                        // Inserto las lineas
                                        $this->SincronizacionAutomatica_model->insertLineasDB($data_linea);
                                    } 
                                }
                                // NOTA: HACER IMCAPIE QUE EN CRM APARECERAN LAS LINEAS CONTRATADAS ACTULMENTE, PERO EN EL SISTEMA CUANDO SE FILTREN LAS LLAMADAS POR LINEA SOLO SE OBTENDRAN LAS LLAMADAS DE LAS LINEAS ACTUALES
                            }
                        }
                    }
                }
                // Si hay lineas nuevas inserto en Creator
                // if (!empty($data_troncales)) {
                //     $this->createJsonCargaInicialCreator($data_troncales);
                // }
                // Si el cliente se va de Abacom se cambia el status en la BD (URGE IMPLEMENTAR)

                // Borro llamadas
                // foreach ($troncales_db as $key => $value) {
                //     $clave = array_search($value['troncal'], array_column($troncales_servnet, 'id_cdr'));
                //     if (empty($clave) && !empty($value['rfc'])) {
                //         $this->SincronizacionAutomatica_model->deleteTroncalDB($value['id_troncal']);
                //     }
                // }
            }
            
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        } //./catch
    }
    // Borra todos los registros de la carga inicial de ZoHo Creator
    function deleteCargaIncialCreator()
    {
        try {

            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => 'https://creator.zoho.com/api/v2/juancarlosdelatorreayala/zoho-cdr/report/Carga_Inicial_Report?process_until_limit=true',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'DELETE',
              CURLOPT_POSTFIELDS =>'{
                "criteria": "ID>0"
            }',
              CURLOPT_HTTPHEADER => array(
                'Authorization: Zoho-oauthtoken '.$this->key_creator,
                'Content-Type: application/json',
                'Cookie: 442b5845d7=55cca134caca85650525fe1564fe26d7; ZCNEWLIVEUI=true; _zcsr_tmp=390bc135-bce7-4740-bbf8-aee6d57e0eb6; zccpn=390bc135-bce7-4740-bbf8-aee6d57e0eb6'
              ),
            ));

            $response = curl_exec($curl);

            if ($response === false){
                print_r('Curl error: ' . curl_error($curl));
            }

            curl_close($curl);
            // echo $response;

        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        } //./catch
    }
    // Esta funcion arma la estructuraa de las troncales
    function armarCargaInicialCreator($array_cdr)
    {
        try {
            if (count($array_cdr) > 0) {

                $nivel_dos = array();

                foreach ($array_cdr as $key => $value) {
                    $lineas_contratadas = "";
                    if (!empty($value['lineas'])) {
                        $lineas_contratadas = implode(", ", $value['lineas']);
                    }
                    $nivel_uno = array('ID_CDR' => $value['id_cdr'], 'Lineas_Contratadas' => strval($lineas_contratadas), 'RFC_CRM' => $value['rfc']);
                    $nivel_dos[] = $nivel_uno;
                }
                
                $nivel_tres = array('data' => $nivel_dos, 'result' => array ('fields' => array (0 => 'ID_CDR', 1 => 'RFC_CRM'),'message' => true, 'tasks' => true));

                // return $nivel_tres;
                $this->insertTroncalesCargaInicial($nivel_tres);
            }
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
        
    }
    // Esta funcion inserta la carga inicial en ZOHO Creator
    function insertTroncalesCargaInicial($data_json)
    {
        try {
            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => "https://creator.zoho.com/api/v2/juancarlosdelatorreayala/zoho-cdr/form/Formulario_Carga_Inicial",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS => json_encode($data_json),
              CURLOPT_HTTPHEADER => array(
                "Authorization: Zoho-oauthtoken ".$this->key_creator,
                "Content-Type: text/plain",
                "Cookie: 442b5845d7=55cca134caca85650525fe1564fe26d7; zccpn=92587364-97e0-4644-8a10-18274199d701; _zcsr_tmp=92587364-97e0-4644-8a10-18274199d701; ZCNEWLIVEUI=true"
              ),
            ));

            $response = curl_exec($curl);

            if ($response === false){
                print_r('Curl error: ' . curl_error($curl));
            }

            curl_close($curl);
            // echo $response;
            
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }
    // Obtenemos la troncales de la base de datos de Servnet
    function getTroncalesServnet()
    {
        try {
            $params_dos = array(new xmlrpcval(array(), 'struct'));

            $msg_troncales = new xmlrpcmsg('listAccounts', $params_dos);

            $cli = new xmlrpc_client('https://sip.serv.net.mx/xmlapi/xmlapi');
            $cli->setSSLVerifyPeer(false);
            $cli->setSSLVerifyHost(false);
            $cli->setCredentials($this->nom_servnet, $this->pass_servnet, CURLAUTH_DIGEST);

            $result = $cli->send($msg_troncales);       /* 20 seconds timeout */

            if ($result->faultCode()) {
              error_log("Fault. Code: " . $r->faultCode() . ", Reason: " . $r->faultString());
            } else {
                return $result;
            }
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        } //./catch
    }
    // Esta funcion inserta las lineas que tiene contratadas en CRM
    function getLineasClienteServnet($data_accounts)
    {
        try {
            $data_lineas = array();
            foreach ($data_accounts as $key => $value) {
                $id_cdr = $value['id_cdr'];
                $rfc = $value['rfc'];
                // Optengo los DID'S de la linea principal
                $responce_lineas = $this->getDIDSServnet($id_cdr);
                $data_lineas[] = array(
                    'id_cdr' => $id_cdr,
                    'rfc' => $rfc,
                    'razon_social' => $this->getInfoTroncal($id_cdr),
                    'lineas' => $responce_lineas
                );
            }

            return $data_lineas;
            
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }        
    }
    // Obtengo informacion de la linea
    function getInfoTroncal($id_cdr)
    {
        try {
            $params = array(new xmlrpcval(array(
                "i_account"          => new xmlrpcval($id_cdr, "int"),
            ), 'struct'));

            $msg = new xmlrpcmsg('getAccountInfo', $params);

            $cli = new xmlrpc_client('https://sip.serv.net.mx/xmlapi/xmlapi');
            $cli->setSSLVerifyPeer(false);
            $cli->setSSLVerifyHost(false);
            $cli->setCredentials($this->nom_servnet, $this->pass_servnet, CURLAUTH_DIGEST);

            $r = $cli->send($msg);       /* 20 seconds timeout */

            return $r->val->me['struct']['company_name']->me['string'];
            
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        } //./catch
    }
    // Esta funcion obtiene las troncales de la BD
    function getTroncalesDB()
    {
        try {
            // Borro las Troncales que se encuentra en la BD
            $troncales_db = $this->SincronizacionAutomatica_model->getTroncalesDB();

            $data_troncales_lineas = array();

            if (!empty($troncales_db)) {
                foreach ($troncales_db as $key => $value) {
                    // Traigo las lineas de la troncal
                    $lineas_db = $this->SincronizacionAutomatica_model->getLineasTroncalDB($value['rfc']);

                    $data_troncales_lineas[] = array(
                        'id_cdr' => $value['troncal'],
                        'rfc' => $value['rfc'],
                        'lineas' => $lineas_db
                    );
                }
            }
            return $data_troncales_lineas;
             
         } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
         } 
    }
    // PROCESO COMPLEMENTARIOS
    // Esta funcion obtiene los DIDS de cada uno de los clientes
    function getDIDSServnet($id_cdr)
    {
        try {
            $params = array(new xmlrpcval(array(
                "i_account"          => new xmlrpcval($id_cdr, "int"),
            ), 'struct'));

            $msg = new xmlrpcmsg('getDIDsList', $params);

            $cli = new xmlrpc_client('https://sip.serv.net.mx/xmlapi/xmlapi');
            $cli->setSSLVerifyPeer(false);
            $cli->setSSLVerifyHost(false);
            $cli->setCredentials($this->nom_servnet, $this->pass_servnet, CURLAUTH_DIGEST);

            $r = $cli->send($msg);       /* 20 seconds timeout */

            $array_size_dids = sizeof($r->val->me['struct']['dids']->me['array']);

            $array_troncal_lineas = array();

            for ($i=0; $i < $array_size_dids; $i++) { 
                $did_troncal = $r->val->me['struct']['dids']->me['array'][$i]->me['struct']['did']->me['string'];
                // Inserto cada uno de los DID'S en el array
                $array_troncal_lineas[] = $did_troncal;
            }

            return $array_troncal_lineas;
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        } //./catch
    }

    function getClientesAJAX(){  
        if (!isset($this->session->userdata['sess_data']['id_usuario'])) {
            redirect(base_url().'Auth');
        }
        try {
            $clientes = $this->Clientes_model->getClientesAJAX();
            echo json_encode($clientes);
        } catch (Exception $e) {
            echo('error');
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    function getCallsUsuario($rfc) {
        if (!isset($this->session->userdata['sess_data']['id_usuario'])) {
            redirect(base_url().'Auth');
        }
        try {
            $data["lineas"] = $this->Clientes_model->getLineasUsuario($rfc);
            $data["llamadas"] = $this->Clientes_model->getCallsUsuario($rfc);
            $data["rfc"] = $rfc;

            $data['titulo']        = 'Reporte | '.$data["lineas"][0]->razon_social;
            $data['pagina']        = 'Llamadas';
            $data["header"]  = $this->load->view('layout/layout_menu', '', true);
            $data["menu_left"]  = $this->load->view('layout/layout_menu_left', '', true);
            // $data["content"] = $this->load->view('clientes/calls_usuario_view', $llamadas_usuario, true);
            $data["footer"]  = $this->load->view('layout/layout_footer', '', true);

            $this->load->view('clientes/cliente_llamadas_view', $data);

            // $this->load->view('main_view.php', $data);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        } //./catch
    }

    function filtrarLlamadasAjax(){  
        if (!isset($this->session->userdata['sess_data']['id_usuario'])) {
            redirect(base_url().'Auth');
        }
        try {

            $data['llamada'] = array(
                'linea' => $this->input->post('select-lienas'),
                'fecha_inicio' => substr($this->input->post('inp-filtro-calls-fechas'), 0, 10),
                'fecha_fin' => substr($this->input->post('inp-filtro-calls-fechas'), -10, 10),
                'rfc' => $this->input->post('btn-form-filtro-calls')
            );

            $all_calls_usuario = $this->Clientes_model->getAllCallsUsuario($data);

            echo json_encode($all_calls_usuario);
            
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

    // Keys ZOHO
    // Conexion retorna token para ZOHO Creator
    function conexionCreator()
    {
        try {

            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => "https://accounts.zoho.com/oauth/v2/token",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS => array('client_id' => '1000.YONV8KREOHCS32PH7RMYQ42HS1PP5H','client_secret' => 'fc3162f7f3e0ede057de01d7388d5a37a3d45cec07','refresh_token' => '1000.8491c97fb001da2800fa1616bc7c7599.38ee25ee9e88502e952583bec3b27932','grant_type' => 'refresh_token'),
              CURLOPT_HTTPHEADER => array(
                "Cookie: b266a5bf57=a711b6da0e6cbadb5e254290f114a026; iamcsr=18c3d923-533f-4955-8b7e-cbe0113c45fb; _zcsr_tmp=18c3d923-533f-4955-8b7e-cbe0113c45fb"
              ),
            ));

            $response = curl_exec($curl);

            if ($response === false){
                print_r('Curl error: ' . curl_error($curl));
            }

            $obj = json_decode($response);

            curl_close($curl);
            // print_r($obj);
            return $obj->{'access_token'};
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        } //./catch
    }
}
