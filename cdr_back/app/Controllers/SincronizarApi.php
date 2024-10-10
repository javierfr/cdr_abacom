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
    protected $format    = 'json';

    public function __construct()
    {
        helper(['form', 'url']);
    }

    // Método para procesar la carga de Excel
    public function uploadExcel()
    {
        $json = [];

        // Configuración para la carga de archivos
        $validationRule = [
            'file' => [
                'label' => 'CSV File',
                'rules' => 'uploaded[file]|mime_in[file,text/csv,application/csv,text/plain,application/vnd.ms-excel]|max_size[file,10240]',
            ],
        ];        

        if (! $this->validate($validationRule)) {
            return $this->fail($this->validator->getErrors());
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
                
                // Procesa el contenido del archivo CSV
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
        
                return $this->respond(['status' => 'success', 'data' => $list], 200);
            } catch (\Exception $e) {
                return $this->failServerError($e->getMessage());
            }
        }
        

        return $this->fail('Archivo no válido o no se pudo procesar.');
    }
}
