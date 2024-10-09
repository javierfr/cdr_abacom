<?php

namespace App\Models;

use CodeIgniter\Model;

class BorrarModel extends Model
{
    protected $DBGroup = 'default'; // Asegúrate de que el grupo de DB sea el correcto

    // Método para borrar todos los registros de la tabla especificada
    public function deleteAllRecords($table)
    {
        $db = \Config\Database::connect();
        $builder = $db->table($table);

        // Borra todos los registros de la tabla
        return $builder->truncate();  // Esto borra todos los registros de la tabla
    }
}
