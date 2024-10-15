<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    protected $allowedFields = [
        'usu_nombres', 
        'usu_apaterno', 
        'usu_amaterno', 
        'usu_telefono', 
        'usu_email', 
        'usu_password', 
        'usu_fecha_creacion', 
        'usu_foto', 
        'id_tipo_usuario'
    ];

    protected $beforeInsert = ['hashPassword'];

    // Hash de la contraseña antes de guardar
    protected function hashPassword(array $data)
    {
        if (isset($data['data']['usu_password'])) {
            $data['data']['usu_password'] = password_hash($data['data']['usu_password'], PASSWORD_DEFAULT);
        }
        return $data;
    }
}
