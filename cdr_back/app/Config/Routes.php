<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->group('api', function($routes) {
    // Api para borrar los registros de la base de datos
    $routes->delete('borrar', 'BorrarRegistrosApi::deleteAll');    
    // Api para sincronizar los registros de la base de datos
    $routes->post('sincronizar/uploadExcel', 'SincronizarApi::uploadExcel');
    // Api para sincronizar los registros de la base de datos con Zoho Creator
    $routes->post('sincronizar/zoho', 'SincronizarApi::sincronizarZoho');
    // Api para 
    $routes->post('auth/login', 'AuthController::login');

    $routes->post('auth/verify', 'AuthController::verifyToken');

});