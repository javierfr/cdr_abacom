<?php
defined('BASEPATH') or exit('No direct script access allowed');
ini_set('max_execution_time', 0); 
ini_set('memory_limit','2048M');
// require_once(APPPATH.'libraries/vendor/php-excel-reader/excel_reader2.php');
require_once(APPPATH.'libraries/vendor/SpreadsheetReader.php');
require_once(APPPATH.'libraries/lib/xmlrpc/xmlrpc.inc');
// Variables SERVNET
const nom_servnet = "ABACOM";
const pass_servnet = "RnTMjphD9N57ZXZVEU";

class Borrar extends CI_Controller
{
    // private $key_zoho = array();
    private $key_creator = '';
    private $key_crm = '';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('SincronizacionAutomatica_model');
        // agrego la key de creator
        $this->key_creator = $this->conexionCreator();
        // $this->key_zoho[] = array('key_type' => 'creator', 'key' => $this->conexionCreator());
        // agrego la key de crm
        $this->key_crm = $this->conexionCRM();
    }

    public function index()
    {
        try {
            // PROCESO 1 ---------- Insertar carga inicial en ZOHO Creator
            // Obtengo un Token Creator
            // echo($this->key_creator);
            // $this->key_creator = 'Hola';
            // echo($this->key_creator);
            // Inicio el proceso
            // deleteCargaIncial($clave_acceso);
            // Obtengo las Troncales de Tralix
            $all_troncales = $this->getTroncalesTralix();
            // Unifico las troncales que optuve de Tralix y las retorno
            $troncales_servnet = $this->unificarTroncalesTralix($all_troncales);
            // Obtengo las Troncales de la DB
            $troncales_db = $this->getTroncalesDB();
            // Inserto las Troncales a la BD
            $this->insertTroncalesDB($troncales_servnet, $troncales_db);
            
            // Armo el json y despues inserto las troncales en la carga inicial de ZOHO Creator
            // $array_cdr_db = $this->createJsonCargaInicialCreator($troncales_db);
            // Insertamos las troncaales en ZOHO Creator
            // $this->insertCargaInicialCreator($array_cdr_db, $clave_acceso);

            // PROCESO 2 ---------- Insertar lineas contratadas en ZOHO CRM
            // $clave_acceso_crm = $this->conexionCRM();
            // Obtengo las lineas de cada troncal de Tralix
            $array_cuentas = $this->getLienasClienteTralix($all_troncales);
            // print_r($array_cuentas);
            // Unifico las lineas por troncal
            // unificarLineasCuentaTralix($array_cuentas, $clave_acceso_crm);

            // PROCESO 3 ---------- Insertar las llamadas que realizaron cada una de las troncales
            // getLlamadasTralix($array_cuentas);
            // Aqui falta obtener las llamadas temporales y meterlas en la tabla que siempre estaran

            // Aqui obtengo las llamadas de la BD
            // getLlamadasDB();


        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        } //./catch
    }
    // Borra todos los registros de la carga inicial de ZoHo Creator
    function deleteCargaIncialCreator($clave_acceso)
    {
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

            if ($response === false){
                print_r('Curl error: ' . curl_error($curl));
            }

            curl_close($curl);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        } //./catch
    }
    // Obtenemos la troncales de la base de datos de Tralix
    function getTroncalesTralix()
    {
        try {
            $params_dos = array(new xmlrpcval(array(), 'struct'));

            $msg_troncales = new xmlrpcmsg('listAccounts', $params_dos);

            $cli = new xmlrpc_client('https://sip.serv.net.mx/xmlapi/xmlapi');
            $cli->setSSLVerifyPeer(false);
            $cli->setSSLVerifyHost(false);
            $cli->setCredentials(nom_servnet, pass_servnet, CURLAUTH_DIGEST);

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
    // Esta funciona unifica las troncales que obtuvieron de Tralix
    function unificarTroncalesTralix($all_troncales)
    {
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
    function insertTroncalesDB($troncales_servnet, $troncales_db)
    {
        try {
            if (count($troncales_servnet) > 0) {
                // Inserto llamadas
                $data_troncales = array();
                foreach ($troncales_servnet as $key => $value) {
                    $clave = array_search($value['id_cdr'], array_column($troncales_db, 'troncal'));
                    if (empty($clave) && !empty($value['RFC'])) {
                        $data_troncal = array('troncal' => $value['id_cdr'], 'rfc' => $value['RFC']);
                        // Inserto registros a la BD
                        $this->SincronizacionAutomatica_model->insertTroncalesDB($data_troncal);
                        // Insertar registro en Creator Carga Inicial
                        $data_troncales[] = $data_troncal;
                        // $this->SincronizacionAutomatica_model->insertTroncalesCargaInicial($value);
                    }
                }
                // Si hay lineas nuevas inserto en Creator
                if (!empty($data_troncales)) {
                    $this->createJsonCargaInicialCreator($data_troncales);
                }
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
    // Esta funcion obtiene las troncales de la BD
    function getTroncalesDB()
    {
        try {
            // Borro las Troncales que se encuentra en la BD
            $troncales_db = $this->SincronizacionAutomatica_model->getTroncalesDB();
            return $troncales_db;
             
         } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
         } 
    }
    // Esta funcion arma la estructuraa de las troncales
    function createJsonCargaInicialCreator($array_cdr)
    {
        try {
            echo('entra al armado');
            if (count($array_cdr) > 0) {

                $nivel_dos = array();

                foreach ($array_cdr as $key => $value) {
                    $nivel_uno = array('ID_CDR' => $value['troncal'], 'RFC_CRM' => $value['rfc']);
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
    // Esta funcion inserta las lineas que tiene contratadas en CRM
    function getLienasClienteTralix($all_troncales)
    {
        try {

            $array_size_troncales = sizeof($all_troncales->val->me['struct']['accounts']->me['array']);
            $contador = 0;

            while ($contador < $array_size_troncales) {
                //obtengo la primera troncal del arreglo
                $linea_principal = $all_troncales->val->me['struct']['accounts']->me['array'][$contador]->me['struct']['i_account']->me['int'];
                $linea_rfc = $all_troncales->val->me['struct']['accounts']->me['array'][$contador]->me['struct']['description']->me['string'];
                // Optengo los DID'S de la linea principal
                $data_lineas = $this->getDIDSTralix($linea_principal);
                // Optengo el nombre de la empresa
                $info_cuenta = $this->getInfoCuentaTralix($linea_principal);
                // if (is_numeric($info_cuenta)) {
                    // echo($linea_principal);
                    // echo "</br>";
                    // print_r($info_cuenta);
                    // echo "</br>";
                    // print_r($linea_rfc);
                    // echo "</br>";
                    // print_r($data_lineas);
                    // echo "</br>";
                    // echo "</br>---------------------</br>";
                // }
                            
                // Optengo ID de la cuenta de ZOHO CRM
                if (!empty($linea_rfc)) {
                    $estatus_get_data = $this->getIDCuentaCRM($linea_rfc);

                    if (!empty($estatus_get_data)) {
                        // echo($estatus_get_data);

                        $data_cuenta = array(
                            "troncal" => $linea_principal,
                            "RFC" => $linea_rfc,
                            "nombre_cuenta" => $info_cuenta,
                            "DIDS" => $data_lineas,
                            "id_cdr" => $estatus_get_data[1]
                        );

                        $array_cuentas[] = $data_cuenta;

                        $contador++;  
                    }

                    // print_r($estatus_get_data);
                }
                
                
            }
            print_r($array_cuentas);
            // return $array_cuentas;
            
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }        
    }

    function unificarLineasCuentaTralix($data_cuentas, $clave_acceso) 
    {
        try {
            $data_rfcs = array();

            foreach ($data_cuentas as $key => $value) {
                if(isset($data_rfcs[$value["RFC"]])){
                    $data_rfcs[$value["RFC"]] = array_merge($value["DIDS"], $data_rfcs[$value["RFC"]]);  
                }
                else {
                    $data_rfcs[$value["RFC"]] = $value["DIDS"];     
                }
            }
            
            foreach ($data_rfcs as $rfc => $lineas) {
                if (!empty($lineas)) {

                    $estatus_get_data = getIDCuentaCRM($rfc, $clave_acceso);
                    // echo('</br>datos: ');
                    // print_r($estatus_get_data);
                    // echo('</br>');

                    // switch($estatus_get_data[0]){
                    //     case "54017000000889022":
                    //     case "54017000025555206":
                    //         $separado_por_comas_ilpea = implode(",", $lineas);
                    //         $estatus_get_data_ilpea = "54017000025555206";

                    //         for ($i = 1; $i < 3; $i++) {
                    //             insertLienasCuentaCRM($estatus_get_data_ilpea, $clave_acceso, $separado_por_comas_ilpea);
                    //             $estatus_get_data_ilpea = "54017000000889022";
                    //         }
                    //         break;     
                    //     default:
                    //         $separado_por_comas = implode(",", $lineas);
                    //         insertLienasCuentaCRM($estatus_get_data[0], $clave_acceso, $separado_por_comas);
                    //         break;
                    // }
                }   
            }
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        } //./catch
    }

    function insertLienasCuentaCRM($id_account, $token_acceso, $lineas_cliente)
    {
        try {
            echo("</br>--entra al update--</br>");

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
            // echo $response;
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        } //./catch
    }

    function getLlamadasTralix($data_cuentas)
    {
        try {
            // Elimino las llamadas de la tabla que contendra las llamadas temporalmente
            $this->SincronizacionAutomatica_model->deleteLlamadasDB();
            $array_size = 0;

            foreach ($data_cuentas as $key => $value) {

                $get_troncal = intval($value['troncal']);
                $get_nombre_cuenta = $value['nombre_cuenta'];
                $get_RFC = $value['RFC'];
                $get_id_cdr = intval($value['id_cdr']);

                $params = array(new xmlrpcval(array(
                    "i_account"          => new xmlrpcval($get_troncal, "int"),
                    "start_date"          => new xmlrpcval("05:00:00.000 GMT Thu Apr 09 2020", "string"),
                    "end_date"          => new xmlrpcval("05:00:00.000 GMT Fri Apr 10 2020", "string")
                ), 'struct'));

                $msg = new xmlrpcmsg('getAccountCDRs', $params);

                /* replace here URL  and credentials to access to the API */
                $cli = new xmlrpc_client('https://sip.serv.net.mx/xmlapi/xmlapi');
                $cli->setSSLVerifyPeer(false);
                $cli->setSSLVerifyHost(false);
                $cli->setCredentials('ABACOM', 'RnTMjphD9N57ZXZVEU', CURLAUTH_DIGEST);

                $r = $cli->send($msg);       /* 20 seconds timeout */

                $array_size = count($r->val->me['struct']['cdrs']->me['array']);

                if ($r->faultCode([me])) {
                  error_log("Fault. Code: " . $r->faultCode() . ", Reason: " . $r->faultString());
                  return false;
                } else {
                    
                    if ($array_size > 0) {

                        for ($i=0; $i < $array_size; $i++) {
                            // Guarda el dato obtenido de la plataforma de Servnet
                            $prefix = $r->val->me['struct']['cdrs']->me['array'][$i]->me['struct']['prefix']->me[string];
                            $costo = $r->val->me['struct']['cdrs']->me['array'][$i]->me['struct']['cost']->me[double];
                            // $TARIFA_BASE = round($costo);

                            if ($prefix != "onnet_in" && $costo > 0) {

                                $ORIGEN = $r->val->me['struct']['cdrs']->me['array'][$i]->me['struct']['cli']->me[string];  
                                $fecha_inicio = $r->val->me['struct']['cdrs']->me['array'][$i]->me['struct']['connect_time']->me[string];
                                $fecha_inicio1 = gmdate('Y-m-d',strtotime($fecha_inicio));
                                $duracion_segundos = $r->val->me['struct']['cdrs']->me['array'][$i]->me['struct']['billed_duration']->me[double];
                                $duracion_minutos = round(($duracion_segundos * 1)/60);
                                // echo($duracion_minutos);
                                $POBLACION_DESTINO = $r->val->me['struct']['cdrs']->me['array'][$i]->me['struct']['description']->me[string];
                                // Guarda el dato obtenido de la plataforma de Servnet

                                $DESTINO = $r->val->me['struct']['cdrs']->me['array'][$i]->me['struct']['cld_in']->me[string];
                                $tarifa_base = $r->val->me['struct']['cdrs']->me['array'][$i]->me['struct']['price_n']->me[double];
                                $MONTO_FINAL = $r->val->me['struct']['cdrs']->me['array'][$i]->me['struct']['cost']->me[double];

                                $nivel_uno = array(
                                    "id_cdr" => $get_id_cdr,
                                    "origen" => "52".$ORIGEN,
                                    "destino" => $DESTINO,
                                    "poblacion_destino" => $POBLACION_DESTINO,
                                    "fecha   " => $fecha_inicio1,
                                    "duracion" => $duracion_minutos,
                                    "monto_final " => $MONTO_FINAL,
                                    "tarifa_base " => $tarifa_base,
                                    "tipo_trafico    " => $prefix,
                                    "tipo_tel_destino" => $prefix,
                                    "rfc " => $get_RFC,
                                    "razon_social" => $get_nombre_cuenta
                                );

                                insertLlamadasDB($nivel_uno);
                            }
                        } //./ foreach
                    }
                } //./ else
            }
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        } //./catch
    }
    // Esta funcion inserta la llamada en la BD (tabla donde estaran las llamadas temporalmente)
    function insertLlamadasDB($data)
    {
        try {
            $this->SincronizacionAutomatica_model->insertLlamadasDB($data);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        } //./catch
    }
    // Esta funcion obtiene las llamadas de la BD
    function getLlamadasDB()
    {
        try {
            // Creo un Token de acceso
            $token_creator = $this->conexionCreator();
            // Obtengo las llamadas de la BD
            $llamadas = $this->SincronizacionAutomatica_model->getLlamadasDB();
            // Creo un array vacio
            $nivel_dos = array();
            // Valido que el array no este vacio
            if (!empty($llamadas)) {
                // Empiezo a iterar las llamadas
                for ($i=0; $i < sizeof($llamadas); $i++) {
                    // Meto las llamadas en un array nuevo
                    $info_llamada = array(
                        'id_CLIENTE' => $llamadas[$i]->id_cdr,
                        'RAZON_SOCIAL' => $llamadas[$i]->razon_social,
                        'FECHA' => date("m-d-Y", strtotime($llamadas[$i]->fecha)),
                        'ORIGEN' => $llamadas[$i]->origen,
                        'TIPO_TRAFICO' => $llamadas[$i]->tipo_trafico,
                        'DESTINO' => $llamadas[$i]->destino,
                        'TIPO_TEL_DESTINO' => $llamadas[$i]->tipo_tel_destino,
                        'POBLACION_DESTINO' => $llamadas[$i]->poblacion_destino,
                        'DURACION_MIN' => $llamadas[$i]->duracion,
                        'TARIFA_BASE' => $llamadas[$i]->tarifa_base,
                        'MONTO_FINAL' => $llamadas[$i]->monto_final);
                    // Cada informacion de llamada la meto en el array vacio
                    $nivel_dos[] = $info_llamada;
                }
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
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        } //./catch
    }
    // Esta funcion inserta las llamadas en Creator
    function insertLlamadaCreator($data, $token_creator)
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
            // echo $response;
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        } //./catch
    }

    // -------------------------------------

    // Notas: 
    // 1.- checar que la hora este bien en la llamada para que despues no la tengams que modificar cuando 
    // tengamos que insertar las llamadas a Creator

    // -------------------------------------

    // PROCESO COMPLEMENTARIOS
    // Esta funcion obtiene los DIDS de cada uno de los clientes
    function getDIDSTralix($troncal)
    {
        try {
            $params = array(new xmlrpcval(array(
                "i_account"          => new xmlrpcval($troncal, "int"),
            ), 'struct'));

            $msg = new xmlrpcmsg('getDIDsList', $params);

            $cli = new xmlrpc_client('https://sip.serv.net.mx/xmlapi/xmlapi');
            $cli->setSSLVerifyPeer(false);
            $cli->setSSLVerifyHost(false);
            $cli->setCredentials(nom_servnet, pass_servnet, CURLAUTH_DIGEST);

            $r = $cli->send($msg);       /* 20 seconds timeout */

            $array_size_dids = sizeof($r->val->me['struct']['dids']->me['array']);

            $contador = 0;
            $array_troncal_lienas = array();

            while ($contador < $array_size_dids) {
                $did_troncal = $r->val->me['struct']['dids']->me['array'][$contador]->me['struct']['did']->me['string'];
                // Inserto cada uno de los DID'S en el array
                array_push($array_troncal_lienas, $did_troncal);
                $contador++;  
            }

            return $array_troncal_lienas;
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        } //./catch
    }
    // Esta funcion retorna el nombre de la empresa
    function getInfoCuentaTralix($i_account)
    {
        try {
            $params_tres = array(new xmlrpcval(array(
                "i_account"          => new xmlrpcval($i_account, "int")
            ), 'struct'));

            $msg_did = new xmlrpcmsg('getAccountInfo', $params_tres);

            $cli = new xmlrpc_client('https://sip.serv.net.mx/xmlapi/xmlapi');
            $cli->setSSLVerifyPeer(false);
            $cli->setSSLVerifyHost(false);
            $cli->setCredentials(nom_servnet, pass_servnet, CURLAUTH_DIGEST);

            $result = $cli->send($msg_did);       /* 20 seconds timeout */
            $cliente = $result->val->me['struct']['company_name']->me['string'];

            return $cliente;
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        } //./catch
    }
    // Esta funcion obtiene el ID de una cuenta en especifico de CRM-ZOHO
    function getIDCuentaCRM($RFC)
    {
        try {
            $ch = curl_init();
            $url = "https://www.zohoapis.com/crm/v2/Accounts/search?criteria=(RFC:equals:".$RFC.")";

            $headr = array();
            $headr[] = 'Content-type: application/json;charset=utf-8';
            $headr[] = 'Authorization: '."Zoho-oauthtoken ".$this->key_crm."";

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
            curl_close ($ch);
            $data_zoho = array();

            if (!empty($obj)) {
                
                $data_zoho[] = $obj->data[0]->id;
                $data_zoho[] = $obj->data[0]->ID_CDR;

                return $data_zoho;
            } else {
                return $data_zoho;
            }            
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        } //./catch
    }


    // CONEXIONES QUE RETORNAN TOKEN

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
    // Conexion retorna token para ZOHO CRM
    function conexionCRM()
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

            if ($response === false){
                print_r('Curl error: ' . curl_error($curl));
            }

            $obj = json_decode($response);
            curl_close ($ch);
            // print_r($obj);
            return $obj->{'access_token'};
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        } //./catch
    }

}
