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

    // Método para procesar la carga de Excel
    public function uploadExcel()
    {
        $validationRule = [
            'file' => [
                'label' => 'CSV File',
                'rules' => 'uploaded[file]|mime_in[file,text/csv,application/csv,text/plain,application/vnd.ms-excel]|max_size[file,10240]',
            ],
        ];
    
        if (! $this->validate($validationRule)) {
            // Registrar los errores en los logs para depuración
            log_message('error', 'Error de validación: ' . json_encode($this->validator->getErrors()));
            
            // Devolver un error de validación en formato JSON
            return $this->fail($this->validator->getErrors(), 400);
        }
    
        $file = $this->request->getFile('file');
    
        if ($file->isValid() && ! $file->hasMoved()) {
            $fileName = $file->getRandomName();
            $file->move(WRITEPATH . 'uploads', $fileName);
    
            $filePath = WRITEPATH . 'uploads/' . $fileName;
            $extension = $file->getClientExtension();
    
            // Elige el lector correcto para el archivo CSV o Excel
            if ($extension == 'csv') {
                $reader = new Csv();  // Lector de archivos CSV
            } else {
                $reader = new Xlsx();  // Lector de archivos XLSX
            }
    
            try {
                // Cargar y procesar el archivo
                $spreadsheet = $reader->load($filePath);
                $sheetData = $spreadsheet->getActiveSheet()->toArray();
    
                $list = [];
                foreach ($sheetData as $key => $val) {
                    if ($key != 0) {
                        $registro = [
                            'id_cdr'           => $val[0],
                            'origen'           => "52" . $val[1],
                            'destino'          => $val[2],
                            'poblacion_destino' => $val[3],
                            'fecha'            => date('Y-m-d', strtotime($val[4])),
                            'duracion'         => $val[7],
                            'monto_final'      => $val[9],
                            'tarifa_base'      => $val[10],
                            'tipo_trafico'     => $val[11],
                            'tipo_tel_destino' => $val[13],
                            'rfc'              => $val[16],
                            'razon_social'     => $val[17],
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
}
