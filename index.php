<?php

// Ruta a la carpeta 'public' dentro de 'cdr_back'
$publicFolder = __DIR__ . '/cdr_back/public';

// Cambia al directorio público
chdir($publicFolder);

// Incluye el archivo `index.php` de CodeIgniter que está en `cdr_back/public`
require_once 'index.php';
