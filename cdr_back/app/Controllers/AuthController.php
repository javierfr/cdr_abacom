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
        // $this->secretKey = getenv('JWT_SECRET');
        $this->secretKey = getenv('JWT_SECRET_KEY');
        if (!$this->secretKey) {
            throw new \RuntimeException('JWT_SECRET_KEY no está configurada');
        }
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

        // if (!$usuario || !password_verify($password, $usuario['usu_password'])) {
        //     return $this->fail('Credenciales de acceso incorrectas');
        // }
        if (!$usuario || md5($password) !== $usuario['usu_password']) {
            return $this->fail('Credenciales de acceso incorrectas');
        }

        // Obtener la clave secreta desde el archivo .env
        $secretKey = getenv('JWT_SECRET_KEY');
        $expirationTime = getenv('JWT_EXPIRATION') ?: 3600; 

        // Crear token JWT
        $tokenData = [
            'id_usuario' => $usuario['id_usuario'],
            'usu_nombres' => $usuario['usu_nombres'],
            'email' => $usuario['usu_email'],
            'iat' => time(),
            'exp' => time() + $expirationTime
        ];
        // Generar el token JWT
        $token = JWT::encode($tokenData, $secretKey, 'HS256');

        // $token = JWT::encode($tokenData, $this->secretKey, 'HS256');

        // return $this->respond([
        //     'status' => 200,
        //     'message' => 'Inicio de sesión exitoso',
        //     'token' => $token
        // ]);
        return $this->respond([
            'status' => 200,
            'message' => 'Inicio de sesión exitoso',
            'token' => $token
        ], 200)->setHeader('Access-Control-Allow-Origin', '*')
                ->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
                ->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization');
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
