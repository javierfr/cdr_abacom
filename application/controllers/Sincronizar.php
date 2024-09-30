<?php
defined('BASEPATH') or exit('No direct script access allowed');
ini_set('max_execution_time', 0); 
ini_set('memory_limit','2048M');
// require_once(APPPATH.'libraries/vendor/php-excel-reader/excel_reader2.php');
// require_once(APPPATH.'libraries/vendor/PhpSpreadsheet/');

// require 'vendor/autoload.php';

// use PhpOffice\PhpSpreadsheet\Spreadsheet;
// use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// require_once(APPPATH.'libraries/vendor/SpreadsheetReader.php');
require_once(APPPATH.'libraries/xmlrpc.inc');

class Sincronizar extends CI_Controller
{
    var $access_servnet = array();
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Sincronizar_model');
        $this->load->model('SincronizacionAutomatica_model');
        // $this->load->library('xmlrpc.inc');
        $this->access_servnet['user'] = 'ABACOM';
        $this->access_servnet['password'] = 'RnTMjphD9N57ZXZVEU';
    }

    public function index()
    {
        if (!isset($this->session->userdata['sess_data']['id_usuario'])) {
            redirect(base_url().'Auth');
        }
        try {
            $data['titulo']        = 'Abacon Telecomunicaciones | Sincronizaci�n';
            $data['pagina']        = 'Llamadas';
            $data["header"]  = $this->load->view('layout/layout_menu', '', true);
            $data["menu_left"]  = $this->load->view('layout/layout_menu_left', '', true);
            $data["footer"]  = $this->load->view('layout/layout_footer', '', true);
            
            $this->load->view('sincronizar/sincronizar_view', $data);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        } //./catch
    }

    function uploadExcelAjax()
    {  
        if (!isset($this->session->userdata['sess_data']['id_usuario'])) {
            redirect(base_url().'Auth');
        }
        try {

            $json = [];
            $config['upload_path'] ="./assets/excel_files";
            $config['allowed_types']='xlsx|csv|xls';

            $this->load->library('upload',$config);

            if (!$this->upload->do_upload('file')) {
                $json = [
                    'error_message' => showErrorMessage($this->upload->display_errors()),
                ];
            } else {

                $file_data  = $this->upload->data();
                $file_name  = $file_data['file_name'];
                $arr_file   = explode('.', $file_name);
                $extension  = end($arr_file);

                // Borramos las llamadas
                $data = $this->Sincronizar_model->deleteLlamadas();

                if('csv' == $extension) {
                    $reader     = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
                } else {
                    $reader     = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                }

                $spreadsheet    = $reader->load($config['upload_path'].'/'.$file_name);
                $sheet_data     = $spreadsheet->getActiveSheet()->toArray();
                $list = array();

                foreach($sheet_data as $key => $val) {
                    if($key != 0) {

                        $date = new DateTimeImmutable($val[4]);

                        $registro = array(
                                'id_cdr' => $val[0],
                                'origen' => "52".$val[1],
                                'destino' => $val[2],
                                'poblacion_destino' => $val[3],
                                'fecha' => $date->format('Y-m-d'),
                                'duracion' => $val[7],
                                'monto_final' => $val[9],
                                'tarifa_base' => $val[10],
                                'tipo_trafico' => $val[11],
                                'tipo_tel_destino' => $val[13],
                                'rfc' => $val[16],
                                'razon_social' => $val[17]
                            );
                        // Inserto los registros a la BD
                        $data = $this->Sincronizar_model->addLlamada($registro);
                        // Agrego el registro al array
                        $list [] = $registro;
                    }
                }

                if(file_exists($file_name))
                    unlink($file_name);
                if(count($list) > 0) {
                    $result     = $this->Sincronizar_model->getLlamadas();
                    
                    if($result) {

                        $data_troncales = $this->Sincronizar_model->getTroncales();

                        if (!empty($data_troncales)) {
                            foreach ($data_troncales as $key => $value) {

                                $llamada_update = array(
                                    'id_cdr' => $value['troncal'],
                                );

                                $data_troncales = $this->Sincronizar_model->updateTroncalLinea($value['rfc'], $llamada_update);
                            }
                            // Obtengo las llamadas     
                            $data_llamadas = $this->Sincronizar_model->getLlamadas();
                            
                            foreach ($data_llamadas as $key => $value) {
                                $llamada_full_update = array(
                                    'id_cdr' => $value['id_cdr'],
                                    'origen' => $value['origen'],
                                    'destino' => $value['destino'],
                                    'poblacion_destino' => $value['poblacion_destino'],
                                    'fecha' => $value['fecha'],
                                    'duracion' => $value['duracion'],
                                    'monto_final' => $value['monto_final'],
                                    'tarifa_base' => $value['tarifa_base'],
                                    'tipo_trafico' => $value['tipo_trafico'],
                                    'tipo_tel_destino' => $value['tipo_tel_destino'],
                                    'rfc' => $value['rfc'],
                                    'razon_social' => $value['razon_social']
                                );
                                // Inserto las llamadas en full_llamadas
                                $result = $this->Sincronizar_model->addLlamadaFull($llamada_full_update);
                            }
                        }
                        $json = ['success_message'   => "All Entries are imported successfully."];
                    } else {
                        $json = ['error_message'     => "Something went wrong. Please try again."];
                    }
                } else {
                    $json = ['error_message' => "No new record is found."];
                }
            }
            echo json_encode($json);

        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }
    
    public function javier(){
        try {
            // PROCESO 1 ---------- Insertar carga inicial en ZOHO Creator
            // Genero un token para Creator
            $clave_acceso = $this->generateTokenCreator();
            // Inicio el proceso (NO FUNCIONA)
            $this->deleteCargaIncialCreator($clave_acceso);
            // Obtengo las troncales
            $troncalesServnet = $this->getTroncalesServnet();
            print_r($troncalesServnet);
            // Unifico las troncales
            // $array_cdr = $this->unificarTroncalesServnet($troncalesServnet);
            // Inserto las Troncales a la BD local
            // $this->insertTroncalesDB($array_cdr);
            // Obtengo las Troncales de la DB
            // Tambien actualizo la troncal de las llamadas
            //Paso pendiente
            // $troncales_db = $this->SincronizacionAutomatica_model->getTroncalesDB();
            //$troncales_db = $this->getTroncalesDB();
            // Armo el json y despues inserto las troncales en la carga inicial de ZOHO Creator
            // $array_cdr_db = $this->createJsonCargaInicialCreator($troncales_db);
            // Insertamos las troncaales en ZOHO Creator
            // $this->insertCargaInicialCreator($array_cdr_db, $clave_acceso);
            
            // PROCESO 2 ---------- Insertar lineas contratadas en ZOHO CRM
            // $clave_acceso_crm = $this->generateTokenCRM();
            // Obtengo las lineas de cada troncal de Tralix
            // $array_cuentas = $this->getLienasClienteServnet($troncalesServnet, $clave_acceso_crm);
            // Unifico las lineas por troncal
            // $this->unificarLineasCuentaServnet($array_cuentas, $clave_acceso_crm);
            
            //$data_troncales_bd = $this->SincronizacionAutomatica_model->getTroncalesDB();
            
            // foreach ($troncales_db as $key => $value) {
                
            //     if($value->rfc != ''){
            //         $data_troncal_bd = array(
            //             'troncal' => $value->troncal 
            //         );
                    
            //         $data_troncal_llamada_bd = array(
            //             'id_cdr' => $value->troncal 
            //         );
                    
            //         $this->SincronizacionAutomatica_model->updateTroncalLineaDB($value->rfc, $data_troncal_bd);
                    
            //         //$this->updateTroncalLlamadaDB($data_troncal_bd, $value->rfc);
                    
            //         $this->SincronizacionAutomatica_model->updateTroncalLlamadaDB($value->rfc, $data_troncal_llamada_bd);
            //     }
            // }
            
            // echo('Super bien');
            
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        } //./catch
            
    } 
    // -------------------------------------------------------------------------
    // Borra todos los registros de la carga inicial de ZoHo Creator
    function deleteCargaIncialCreator($clave_acceso){
        try {

            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => "https://creator.zoho.com/api/v2/juancarlosdelatorreayala/zoho-cdr/report/Carga_Inicial_Report",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "DELETE",
              CURLOPT_POSTFIELDS =>"{\n  \"criteria\": \"ID!=null\",\n  \"process_until_limit\":\"true\"\n}",
              CURLOPT_HTTPHEADER => array(
                "Authorization: Zoho-oauthtoken ".$clave_acceso,
                "Content-Type: text/plain",
                "Cookie: 442b5845d7=55cca134caca85650525fe1564fe26d7; zccpn=92587364-97e0-4644-8a10-18274199d701; _zcsr_tmp=92587364-97e0-4644-8a10-18274199d701; ZCNEWLIVEUI=true"
              ),
            ));

            $response = curl_exec($curl);

            print_r($response);

            if ($response === false){
                print_r('Curl error: ' . curl_error($curl));
            }

            curl_close($curl);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        } //./catch
    }
    
    // Obtenemos la troncales de la base de datos
    function getTroncalesServnet(){
        try {
            $params_dos = array(new xmlrpcval(array(), 'struct'));

            $msg_troncales = new xmlrpcmsg('listAccounts', $params_dos);

            $cli = new xmlrpc_client('https://sip.serv.net.mx/xmlapi/xmlapi');
            $cli->setSSLVerifyPeer(false);
            $cli->setSSLVerifyHost(false);
            $cli->setCredentials($this->access_servnet['user'], $this->access_servnet['password'], CURLAUTH_DIGEST);

            $result = $cli->send($msg_troncales);       /* 20 seconds timeout */
            
            return $result;
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        } //./catch
    }
    // Esta funciona unifica las troncales que obtuvieron de Tralix
    function unificarTroncalesServnet($all_troncales){
        try {
            $array_cdr = array();

            for ($i=0; $i < count($all_troncales->val->me['struct']['accounts']->me['array']); $i++) {
                
                $linea_rfc = $all_troncales->val->me['struct']['accounts']->me['array'][$i]->me['struct']['description']->me['string'];
                $id_cdr = $all_troncales->val->me['struct']['accounts']->me['array'][$i]->me['struct']['i_account']->me['int'];

                if (empty($array_cdr)) {
                    $data = array('RFC' => $linea_rfc, 'id_cdr' => $id_cdr);
                    $array_cdr[] = $data;
                } else {
                    $contador = 0;
                    foreach ($array_cdr as $key => $value) {
                        if ($value['RFC'] == $linea_rfc) {
                            $contador++;
                        }
                    }

                    if ($contador <= 0) {
                        $data = array('RFC' => $linea_rfc, 'id_cdr' => $id_cdr);
                        $array_cdr[] = $data;
                    }
                }
                
            }

            return $array_cdr;

        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        } //./catch
    }
    
    // Esta funcion inserta las troncales en ls BD
    function insertTroncalesDB($array_cdr){
        try {
            // Borro las Troncales que se encuentra en la BD
            $llamadas = $this->SincronizacionAutomatica_model->deleteTroncalesDB();

            if (count($array_cdr) > 0) {

                $nivel_dos = array();

                foreach ($array_cdr as $key => $value) {
                    $nivel_uno = array('troncal' => $value['id_cdr'], 'rfc' => $value['RFC']);
                    // Inserto registros a la BD
                    $this->SincronizacionAutomatica_model->insertTroncalesDB($nivel_uno);
                }
            }
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        } //./catch
    }
    
    // Esta funcion obtiene las troncales de la BD
    function getTroncalesDB(){
        try {
            // Obtengo las Troncales que se encuentra en la BD
            $troncales_db = $this->SincronizacionAutomatica_model->getTroncalesDB();
            foreach ($troncales_db as $key => $value) {

                $data = array(
                    'id_cdr' => $value->troncal
                );

                $this->updateTroncalLlamadaDB($data, $value->rfc);
            }
            
            return $troncales_db;
             
         } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
         } 
    }
    
    function updateTroncalLlamadaDB($data, $rfc){
       try {
            // Actualizo las Troncales de las llamadas
            $this->SincronizacionAutomatica_model->updateTroncalLlamadaDB($data, $rfc);
             
         } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
         }  
    }
    
    // Esta funcion arma la estructuraa de las troncales
    function createJsonCargaInicialCreator($array_cdr){
        try {
  
            if (count($array_cdr) > 0) {

                $nivel_dos = array();

                foreach ($array_cdr as $key => $value) {
                    $nivel_uno = array('ID_CDR' => $value->troncal, 'RFC_CRM' => $value->rfc);
                    $nivel_dos[] = $nivel_uno;
                }
                
                $nivel_tres = array('data' => $nivel_dos, 'result' => array ('fields' => array (0 => 'ID_CDR', 1 => 'RFC_CRM'),'message' => true, 'tasks' => true));

                return $nivel_tres;
            }
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
        
    }
    
    // Esta funcion inserta la carga inicial en ZOHO Creator
    function insertCargaInicialCreator($data_json, $clave_acceso) {
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
                "Authorization: Zoho-oauthtoken ".$clave_acceso,
                "Content-Type: text/plain",
                "Cookie: 442b5845d7=55cca134caca85650525fe1564fe26d7; zccpn=92587364-97e0-4644-8a10-18274199d701; _zcsr_tmp=92587364-97e0-4644-8a10-18274199d701; ZCNEWLIVEUI=true"
              ),
            ));

            $response = curl_exec($curl);

            if ($response === false){
                print_r('Curl error: ' . curl_error($curl));
            }

            curl_close($curl);
            
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }
    
    // Esta funcion inserta las lineas que tiene contratadas en CRM
    function getLienasClienteServnet($all_troncales, $clave_acceso){
        try {

            $array_size_troncales = sizeof($all_troncales->val->me['struct']['accounts']->me['array']);
            
            $array_cuentas = Array();
            $contador = 0;

            while ($contador < $array_size_troncales) {
                //obtengo la primera troncal del arreglo
                $linea_principal = $all_troncales->val->me['struct']['accounts']->me['array'][$contador]->me['struct']['i_account']->me['int'];
                $linea_rfc = $all_troncales->val->me['struct']['accounts']->me['array'][$contador]->me['struct']['description']->me['string'];
                // Optengo los DID'S de la linea principal
                $data_lineas = $this->getDIDSServnet($linea_principal);

                // Optengo el nombre de la empresa
                //$info_cuenta = $this->getInfoCuentaServnet($linea_principal);
                // Optengo ID de la cuenta de ZOHO CRM
                // Aqui javier
                // $estatus_get_data = $this->getIDCuentaCRM($linea_rfc, $clave_acceso);

                $data_cuenta = array(
                    "troncal" => $linea_principal,
                    "RFC" => $linea_rfc,
                    // "nombre_cuenta" => $data_lineas['nombre_cuenta'],
                    "DIDS" => $data_lineas
                );

                $array_cuentas[] = $data_cuenta;

                $contador++;  
            }

            return $array_cuentas;
            
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }        
    }
    
    // PROCESO COMPLEMENTARIOS
    // Esta funcion obtiene los DIDS de cada uno de los clientes
    function getDIDSServnet($troncal){
        try {
            
            $params = array(new xmlrpcval(array(
                "i_account"          => new xmlrpcval($troncal, "int"),
            ), 'struct'));
            // Objetngo informaci��n de la cuenta
            //$msg_did = new xmlrpcmsg('getAccountInfo', $params);
            // Obtengo lineas contratadas
            $msg = new xmlrpcmsg('getDIDsList', $params);

            $cli = new xmlrpc_client('https://sip.serv.net.mx/xmlapi/xmlapi');
            $cli->setSSLVerifyPeer(false);
            $cli->setSSLVerifyHost(false);
            $cli->setCredentials($this->access_servnet['user'], $this->access_servnet['password'], CURLAUTH_DIGEST);

            //$result = $cli->send($msg_did);       /* 20 seconds timeout */
            $r = $cli->send($msg);       /* 20 seconds timeout */
            
            //$cliente = $result->val->me['struct']['company_name']->me['string'];

            $array_dids = $r->val->me['struct']['dids']->me['array'];

            
            $array_lienas = array();
            
            foreach ($array_dids as $key => $value) {
                array_push($array_lienas, $value->me['struct']['did']->me['string']);
            }
            
            //$data_lineas = array(
              //  "nombre_cuenta" => $cliente,
                //"DIDS" => $array_lienas
            //);

            return $array_lienas;
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        } //./catch
    }
    // Esta funcion obtiene el ID de una cuenta en especifico de CRM-ZOHO
    function getIDCuentaCRM($RFC, $token_acceso){
        try {
        
            $ch = curl_init();
        
            $url = "https://www.zohoapis.com/crm/v2/Accounts/search?criteria=(RFC:equals:".$RFC.")";

            $headr = array();
            $headr[] = 'Content-type: application/json;charset=utf-8';
            $headr[] = 'Authorization: '."Zoho-oauthtoken ".$token_acceso."";

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headr);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);

            if ($response === false)
            {
                print_r('Curl error: ' . curl_error($ch));
            }
            
            $obj = json_decode($response);

            
            //$id = '';
            //$ID_CDR = '';
            //$nombre_cuenta = '';
            
            //if(sizeof((array) $obj) === 2){
              //  $id = $obj->data[0]->id;
                //$ID_CDR = $obj->data[0]->ID_CDR;
                //$nombre_cuenta = $obj->data[0]->Account_Name;
            //}
            
            $data_zoho = array();
            $data_zoho[] = $obj->data[0]->id;
            $data_zoho[] = $obj->data[0]->ID_CDR;
            $data_zoho[] = $obj->data[0]->Account_Name;
            
            return $data_zoho;
                    
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        } //./catch
    }
    // FALTA COMENTAR
    function unificarLineasCuentaServnet($array_cuentas, $clave_acceso){
        try {
            
            $data_rfcs = array();

            foreach ($array_cuentas as $key => $value) {
                if(isset($data_rfcs[$value["RFC"]])){
                    $data_rfcs[$value["RFC"]] = array_merge($value["DIDS"], $data_rfcs[$value["RFC"]]);  
                }
                else {
                    $data_rfcs[$value["RFC"]] = $value["DIDS"];     
                }
            }
            
            // Borro lineas que estan en la BD
            $this->SincronizacionAutomatica_model->deleteLineasDB();
            
            foreach ($data_rfcs as $key => $value) {
                if(!empty($key)){
                
                    $estatus_get_data = $this->getIDCuentaCRM($key, $clave_acceso);
                    
                    foreach ($value as $key2) {
                        
                        $data_linea = array(
                            'linea' => $key2, 
                            'rfc' => $key,
                            'razon_social' => $estatus_get_data[2], 
                            'troncal' => 0 
                        );
                        
                        // Inserto las lineas en la BD
                        $this->SincronizacionAutomatica_model->insertLineasDB($data_linea);
                    }
                    
                    switch($estatus_get_data[0]){
                        case "54017000000889022":
                        case "54017000025555206":
                            $separado_por_comas_ilpea = implode(",", $value);
                            $estatus_get_data_ilpea = "54017000025555206";

                            for ($i = 1; $i < 3; $i++) {
                                $this->insertLienasCuentaCRM($estatus_get_data_ilpea, $clave_acceso, $separado_por_comas_ilpea);
                                $estatus_get_data_ilpea = "54017000000889022";
                            }
                            break;     
                        default:
                            $separado_por_comas = implode(",", $value);
                            $this->insertLienasCuentaCRM($estatus_get_data[0], $clave_acceso, $separado_por_comas);
                            break;
                    }
                    
                }
                 
            }
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        } //./catch
    }
    
    function insertLienasCuentaCRM($id_account, $token_acceso, $lineas_cliente){
        try {
            // echo("</br>--entra al update--</br>");

            $headr = array();
            $headr[] = 'Content-type: application/json;charset=utf-8';
            $headr[] = 'Authorization: '."Zoho-oauthtoken ".$token_acceso."";

            $url = "https://www.zohoapis.com/crm/v2/Accounts/".$id_account."";

            $fields = "{\n\t\"data\": [\n\t\t{\n\t\t\t\"Lineas_contratadas\": \"".$lineas_cliente."\"\n\t\t}\n\t],\n\t\"wf_trigger\": true\n}";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headr);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);

            if ($response === false)
            {
                print_r('Curl error: ' . curl_error($ch));
            }

            curl_close($ch);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        } //./catch
    }
    
    // -------------------------------------------------------------------------

    function uploadMysqlZohoAjax()
    {
        try {
            // Creo un Token de acceso
            $token_creator = $this->generateTokenCreator();
            // Obtengo las llamadas de la BD
            $llamadas = $this->Sincronizar_model->getLlamadas();
            // Creo un array vacio
            $nivel_dos = array();
            // Valido que el array no este vacio
            if (!empty($llamadas)) {
                // Empiezo a iterar las llamadas
                foreach ($llamadas as $key => $value) {
                    // Meto las llamadas en un array nuevo
                    $info_llamada = array(
                        'id_CLIENTE' => $value['id_cdr'],
                        'RAZON_SOCIAL' => $value['razon_social'],
                        'FECHA' => date("m-d-Y", strtotime($value['fecha'])),
                        'ORIGEN' => $value['origen'],
                        'TIPO_TRAFICO' => $value['tipo_trafico'],
                        'DESTINO' => $value['destino'],
                        'TIPO_TEL_DESTINO' => $value['tipo_tel_destino'],
                        'POBLACION_DESTINO' => $value['poblacion_destino'],
                        'DURACION_MIN' => $value['duracion'],
                        'TARIFA_BASE' => $value['tarifa_base'],
                        'MONTO_FINAL' => $value['monto_final']);
                    // Cada informacion de llamada la meto en el array vacio
                    $nivel_dos[] = $info_llamada;
                }
                // for ($i=0; $i < sizeof($llamadas); $i++) {
                //     // Meto las llamadas en un array nuevo
                //     $info_llamada = array(
                //         'id_CLIENTE' => $llamadas[$i]->id_cdr,
                //         'RAZON_SOCIAL' => $llamadas[$i]->razon_social,
                //         'FECHA' => date("m-d-Y", strtotime($llamadas[$i]->fecha)),
                //         'ORIGEN' => $llamadas[$i]->origen,
                //         'TIPO_TRAFICO' => $llamadas[$i]->tipo_trafico,
                //         'DESTINO' => $llamadas[$i]->destino,
                //         'TIPO_TEL_DESTINO' => $llamadas[$i]->tipo_tel_destino,
                //         'POBLACION_DESTINO' => $llamadas[$i]->poblacion_destino,
                //         'DURACION_MIN' => $llamadas[$i]->duracion,
                //         'TARIFA_BASE' => $llamadas[$i]->tarifa_base,
                //         'MONTO_FINAL' => $llamadas[$i]->monto_final);
                //     // Cada informacion de llamada la meto en el array vacio
                //     $nivel_dos[] = $info_llamada;
                // }
            }
            
            // Caundo termine de iterar las llamadas meto todas las llamadas en un array nuevo llamado data
            while(count($nivel_dos) > 0) {
              $data200 = array_splice($nivel_dos, 0, 200);
              $data = array('data' => $data200,
                  'result' => 
                      array (
                          'fields' => 
                              array (
                                  0 => 'id_CLIENTE',
                                  1 => 'RAZON_SOCIAL',
                                  2 => 'FECHA',
                                  3 => 'ORIGEN',
                                  4 => 'TIPO_TRAFICO',
                                  5 => 'DESTINO',
                                  6 => 'TIPO_TEL_DESTINO',
                                  7 => 'POBLACION_DESTINO',
                                  8 => 'DURACION_MIN',
                                  9 => 'TARIFA_BASE',
                                  10 => 'MONTO_FINAL',
                              ),
                          'message' => true,
                          'tasks' => true,
                      )
              );
              
              // Finalmente inserto las llamadas
              $this->insertLlamadaCreator($data, $token_creator);
              // break;
            }
            echo json_encode(array("status" => true));
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }
    
    function generateTokenCreator()
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
                "Cookie: b266a5bf57=a711b6da0e6cbadb5e254290f114a026; iamcsr=555ee5de-01b9-4d3e-80ca-13896c31ace9; _zcsr_tmp=555ee5de-01b9-4d3e-80ca-13896c31ace9"
              ),
            ));

            $response = curl_exec($curl);
            $obj = json_decode($response);

            curl_close($curl);
            return $obj->{'access_token'};
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }
    
    public function generateTokenCRM()
    {   
        try {
            $url = "https://accounts.zoho.com/oauth/v2/token";
            $ClientID = "1000.YONV8KREOHCS32PH7RMYQ42HS1PP5H";
            $ClientSecret = "fc3162f7f3e0ede057de01d7388d5a37a3d45cec07";
            $refresh_token = "1000.17758697c2b01fcf85e3bbd3802a20da.886d3200bb13ede5c265c4a5d629c4b7";
            $grant_type = "refresh_token";

            
            $fields = array( 'client_id'=>$ClientID, 'client_secret'=>$ClientSecret, 'refresh_token'=>$refresh_token, 'grant_type'=>$grant_type);

            $postvars = '';
              
            foreach($fields as $key=>$value) {
                $postvars .= $key . "=" . $value . "&";
            }

            $ch = curl_init();
            $url = "https://accounts.zoho.com/oauth/v2/token";
            curl_setopt($ch,CURLOPT_URL, $url);
            curl_setopt($ch,CURLOPT_POST, 1);                //0 for a get request
            curl_setopt($ch,CURLOPT_POSTFIELDS, $postvars);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            $obj = json_decode($response);
            
            curl_close ($ch);
            return $obj->{'access_token'};
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }
    
    public function insertLlamadaCreator($data, $token_creator)
    {
        try {
            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => "https://creator.zoho.com/api/v2/juancarlosdelatorreayala/zoho-cdr/form/Detalle_de_Llamada1",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS => json_encode($data),
              CURLOPT_HTTPHEADER => array(
                "Authorization: Zoho-oauthtoken ".$token_creator,
                "Content-Type: text/plain",
                "Cookie: 442b5845d7=55cca134caca85650525fe1564fe26d7; zccpn=aa64348f-e71a-40ca-896b-24416154b376; _zcsr_tmp=aa64348f-e71a-40ca-896b-24416154b376; ZCNEWLIVEUI=true"
              ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        } //./catch
    }
}
