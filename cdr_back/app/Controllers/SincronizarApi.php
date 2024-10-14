<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use App\Models\SincronizarModel;

class SincronizarApi extends ResourceController
{
    protected $modelName = 'App\Models\SincronizarModel';
    protected $format = 'json';

    public function __construct()
    {
        helper(['form', 'url']);
    }
    // Método para procesar la carga de Excel o CSV
    public function uploadExcel()
    {
        // Reglas de validación para aceptar tanto CSV como XLSX
        $validationRule = [
            'file' => [
                'label' => 'Excel or CSV File',
                'rules' => 'uploaded[file]|mime_in[file,text/csv,application/csv,text/plain,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet]|max_size[file,10240]',
            ],
        ];

        if (!$this->validate($validationRule)) {
            // Registrar los errores en los logs para depuración
            log_message('error', 'Error de validación: ' . json_encode($this->validator->getErrors()));

            // Devolver un error de validación en formato JSON
            return $this->fail($this->validator->getErrors(), 400);
        }

        $file = $this->request->getFile('file');

        if ($file->isValid() && !$file->hasMoved()) {
            $fileName = $file->getRandomName();
            $file->move(WRITEPATH . 'uploads', $fileName);

            $filePath = WRITEPATH . 'uploads/' . $fileName;
            $extension = $file->getClientExtension();

            // Elige el lector correcto para el archivo CSV o Excel
            if ($extension == 'csv') {
                $reader = new Csv();  // Lector de archivos CSV
            } elseif ($extension == 'xlsx') {
                $reader = new Xlsx();  // Lector de archivos XLSX
            } else {
                return $this->fail('Formato de archivo no soportado.', 400);
            }

            try {
                // Cargar y procesar el archivo
                $spreadsheet = $reader->load($filePath);
                $sheetData = $spreadsheet->getActiveSheet()->toArray();

                $list = [];
                foreach ($sheetData as $key => $val) {
                    if ($key != 0) {
                        $registro = [
                            'id_cdr' => $val[0],
                            'origen' => "52" . $val[1],
                            'destino' => $val[2],
                            'poblacion_destino' => $val[3],
                            'fecha' => date('Y-m-d', strtotime($val[4])),
                            'duracion' => $val[7],
                            'monto_final' => $val[9],
                            'tarifa_base' => $val[10],
                            'tipo_trafico' => $val[11],
                            'tipo_tel_destino' => $val[13],
                            'rfc' => $val[16],
                            'razon_social' => $val[17],
                        ];
                        // Guarda los registros en la base de datos
                        $this->model->insert($registro);
                        $list[] = $registro;
                    }
                }

                // Borra el archivo una vez procesado
                unlink($filePath);

                // Devuelve un resumen de la operación sin los registros
                return $this->respond([
                    'status' => 'success',
                    'message' => "Se han insertado " . count($list) . " registros correctamente.",
                ], 200);
            } catch (\Exception $e) {
                log_message('error', 'Error procesando el archivo: ' . $e->getMessage());
                return $this->failServerError('Error interno al procesar el archivo.');
            }
        }

        return $this->fail('Archivo no válido o no se pudo procesar.', 400);
    }

    // Método para sincronizar con Zoho
    public function sincronizarZoho()
    {
        try {
            // Creo un Token de acceso
            $token_creator = $this->generateTokenCreator();
            // Obtengo las llamadas de la BD
            $llamadas = $this->model->getLlamadas();
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
                        'MONTO_FINAL' => $value['monto_final']
                    );
                    // Cada informacion de llamada la meto en el array vacio
                    $nivel_dos[] = $info_llamada;
                }
            }

            // Caundo termine de iterar las llamadas meto todas las llamadas en un array nuevo llamado data
            while (count($nivel_dos) > 0) {
                $data200 = array_splice($nivel_dos, 0, 200);
                $data = array(
                    'data' => $data200,
                    'result' =>
                        array(
                            'fields' =>
                                array(
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
                $response = $this->insertLlamadaCreator($data, $token_creator);

                // Verificar la respuesta de la inserción
                if ($response['status'] === 'error') {
                    return $this->failServerError($response['message']);
                }
            }

            // return $this->respond(['status' => 'success', 'message' => 'Sincronización exitosa'], 200);
            return $this->respond([
                'status' => 'success',
                'message' => 'Sincronización exitosa',
            ], 200)->setHeader('Access-Control-Allow-Origin', '*')
                    ->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
                    ->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');
            
        } catch (\Exception $e) {
            log_message('error', 'Error al sincronizar con Zoho: ' . $e->getMessage());
            return $this->failServerError('Error al sincronizar con Zoho.');
        }
    }


    // Función para generar el token de acceso a Zoho Creator
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
                CURLOPT_POSTFIELDS => http_build_query([
                    'client_id' => '1000.YONV8KREOHCS32PH7RMYQ42HS1PP5H',
                    'client_secret' => 'fc3162f7f3e0ede057de01d7388d5a37a3d45cec07',
                    'refresh_token' => '1000.8491c97fb001da2800fa1616bc7c7599.38ee25ee9e88502e952583bec3b27932',
                    'grant_type' => 'refresh_token'
                ]),
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/x-www-form-urlencoded"
                ),
            ));

            $response = curl_exec($curl);
            $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            // Si el status HTTP no es 200, registrar el error
            if ($httpStatus != 200) {
                log_message('error', 'Error al obtener el token de Zoho. HTTP Status: ' . $httpStatus . ' Response: ' . $response);
                curl_close($curl);
                return null;
            }

            $obj = json_decode($response);

            // Si no se puede decodificar la respuesta JSON, registrar el error
            if (!$obj) {
                log_message('error', 'Error al decodificar la respuesta de Zoho: ' . $response);
                curl_close($curl);
                return null;
            }

            curl_close($curl);
            return $obj->{'access_token'};
        } catch (\Exception $e) {
            log_message('error', 'Error al generar el token de acceso: ' . $e->getMessage());
            return null;
        }
    }
    // Función para insertar las llamadas en Zoho Creator
    private function insertLlamadaCreator($data, $token_creator)
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
                    "Authorization: Zoho-oauthtoken " . $token_creator,
                    "Content-Type: text/plain",
                    "Cookie: 442b5845d7=55cca134caca85650525fe1564fe26d7; zccpn=aa64348f-e71a-40ca-896b-24416154b376; _zcsr_tmp=aa64348f-e71a-40ca-896b-24416154b376; ZCNEWLIVEUI=true"
                ),
            ));

            $response = curl_exec($curl);

            // Si hay error en la ejecución del CURL, registrar el error y retornar
            if (curl_errno($curl)) {
                $errorMessage = 'CURL Error: ' . curl_error($curl);
                log_message('error', $errorMessage);
                curl_close($curl);
                return ['status' => 'error', 'message' => $errorMessage];
            }

            // Verificar el código de estado HTTP
            $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if ($httpStatus != 200) {
                $errorMessage = 'Error al sincronizar con Zoho. HTTP Status: ' . $httpStatus . ' Response: ' . $response;
                log_message('error', $errorMessage);
                curl_close($curl);
                return ['status' => 'error', 'message' => $errorMessage];
            } else {
                $successMessage = 'Sincronización exitosa con Zoho. Respuesta: ' . $response;
                log_message('info', $successMessage);
                curl_close($curl);
                return ['status' => 'success', 'message' => $successMessage];
            }
        } catch (\Exception $e) {
            $errorMessage = 'Error al insertar las llamadas en Zoho: ' . $e->getMessage();
            log_message('error', $errorMessage);
            return ['status' => 'error', 'message' => $errorMessage];
        }
    }
}
