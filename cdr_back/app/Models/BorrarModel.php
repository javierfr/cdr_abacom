<?php

namespace App\Models;

use CodeIgniter\Model;

class BorrarModel extends Model
{
    protected $DBGroup = 'default'; // Asegúrate de que el grupo de DB sea el correcto

    public function deleteAllRecords($table)
    {
        try {
            $db = \Config\Database::connect();
            $builder = $db->table($table);

            // Borra todos los registros de la tabla
            return $builder->truncate();  // Esto borra todos los registros de la tabla
        } catch (\Exception $e) {
            log_message('error', 'Error al borrar los registros de la tabla ' . $table . ': ' . $e->getMessage());
            throw $e;  // Relanza la excepción para que sea manejada por el controlador
        }
    }
}
