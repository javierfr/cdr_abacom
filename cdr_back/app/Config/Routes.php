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
});