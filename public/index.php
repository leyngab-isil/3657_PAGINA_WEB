<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Middleware\CorsMiddleware;
use Config\Database;
use Core\Router;

define('BASE_URL', '/3657_PAGINA_WEB/public');

// =====================================
// SESIÓN
// =====================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// =====================================
// CONEXIÓN
// =====================================

$database = new Database();
$db = $database->connect();


// =====================================
// ROUTER
// =====================================

$router = new Router();


// =====================================
// CARGAR RUTAS
// =====================================

require_once __DIR__ . '/../routes/web.php';
require_once __DIR__ . '/../routes/api.php';


// =====================================
// MÉTODO HTTP
// =====================================

$method = $_SERVER['REQUEST_METHOD'];


// =====================================
// OBTENER URI
// =====================================

$uri = parse_url(
    $_SERVER['REQUEST_URI'],
    PHP_URL_PATH
);


// =====================================
// ELIMINAR RUTA BASE
// =====================================

$basePath = '/3657_PAGINA_WEB/public';

if (str_starts_with($uri, $basePath)) {
    $uri = substr(
        $uri,
        strlen($basePath)
    );
}


// =====================================
// NORMALIZAR URI
// =====================================

if ($uri === '' || $uri === false) {
    $uri = '/';
}

if ($uri !== '/') {
    $uri = rtrim($uri, '/');
}


// =====================================
// CORS SOLO PARA API
// =====================================

if (str_starts_with($uri, '/api')) {
    CorsMiddleware::handle();
}


// =====================================
// EJECUTAR ROUTER
// =====================================

$router->dispatch(
    $method,
    $uri
);