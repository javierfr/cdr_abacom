<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\BorrarModel;

class BorrarRegistrosApi extends ResourceController
{
    protected $modelName = 'App\Models\BorrarModel';
    protected $format    = 'json';

    // Método para borrar todos los registros de una tabla específica
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
            return $this->failServerError($e->getMessage());
        }
    }
}
