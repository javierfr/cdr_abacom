<?php

namespace App\Models;

use CodeIgniter\Model;

class SincronizarModel extends Model
{
    protected $table      = 'llamadas';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id_cdr', 'origen', 'destino', 'poblacion_destino', 'fecha', 'duracion', 'monto_final', 'tarifa_base', 'tipo_trafico', 'tipo_tel_destino', 'rfc', 'razon_social'
    ];
}
