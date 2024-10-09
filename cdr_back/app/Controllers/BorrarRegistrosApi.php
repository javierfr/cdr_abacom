<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\BorrarModel;
use CodeIgniter\API\ResponseTrait;

class BorrarRegistrosApi extends ResourceController
{
    use ResponseTrait;  // Para manejar las respuestas JSON de manera sencilla

    protected $modelName = 'App\Models\BorrarModel';
    protected $format    = 'json';

    public function deleteAll()
    {
        // Nombre de la tabla que se quiere borrar
        $table = $this->request->getVar('table');

        if (empty($table)) {
            return $this->fail('Por favor, proporciona el nombre de la tabla.', 400);
        }

        try {
            // Llamamos al modelo para borrar los registros
            $result = $this->model->deleteAllRecords($table);

            if ($result) {
                return $this->respond(['status' => 'success', 'message' => 'Todos los registros han sido eliminados'], 200);
            } else {
                return $this->failServerError('Ocurrió un error al intentar borrar los registros.');
            }
        } catch (\Exception $e) {
            log_message('error', 'Error en deleteAll: ' . $e->getMessage());
            return $this->failServerError('Error interno del servidor: ' . $e->getMessage());
        }
    }
}
