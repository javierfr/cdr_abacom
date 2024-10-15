<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use CodeIgniter\RESTful\ResourceController;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController extends ResourceController
{
    protected $usuarioModel;
    private $secretKey;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
        $this->secretKey = getenv('JWT_SECRET');
    }

    public function login()
    {
        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required'
        ];

        if (!$this->validate($rules)) {
            return $this->failValidationErrors($this->validator->getErrors());
        }

        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');

        // Verificar si el usuario existe
        $usuario = $this->usuarioModel->where('usu_email', $email)->first();

        if (!$usuario || !password_verify($password, $usuario['usu_password'])) {
            return $this->fail('Credenciales de acceso incorrectas');
        }

        // Crear token JWT
        $tokenData = [
            'id_usuario' => $usuario['id_usuario'],
            'usu_nombres' => $usuario['usu_nombres'],
            'email' => $usuario['usu_email'],
            'iat' => time(),
            'exp' => time() + 3600 // Expira en 1 hora
        ];

        $token = JWT::encode($tokenData, $this->secretKey, 'HS256');

        return $this->respond([
            'status' => 200,
            'message' => 'Inicio de sesión exitoso',
            'token' => $token
        ]);
    }

    public function verifyToken()
    {
        $authHeader = $this->request->getHeader("Authorization");
        $token = null;

        // Extraer el token del encabezado
        if ($authHeader) {
            $token = $authHeader->getValue();
        }

        if (!$token) {
            return $this->failUnauthorized('Token no proporcionado');
        }

        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
            return $this->respond(['status' => 200, 'data' => $decoded]);
        } catch (\Exception $e) {
            return $this->fail('Token no válido');
        }
    }
}
