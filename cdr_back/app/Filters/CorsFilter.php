<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class CorsFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Añadir los encabezados CORS permitidos
        header('Access-Control-Allow-Origin: *');  // Puedes cambiar * por el dominio específico
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        // Manejar solicitudes OPTIONS
        if ($request->getMethod() === 'options') {
            $response = service('response');
            $response->setStatusCode(ResponseInterface::HTTP_OK);
            return $response;
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No necesitamos modificar nada después de la solicitud en este caso
    }
}
