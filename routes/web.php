<?php

declare(strict_types=1);

use App\Controllers\ViewController;
use App\Controllers\AuthController;
use App\Middleware\AuthMiddleware;
use App\Models\Usuario;

// MODELOS
$userModel = new Usuario($db);

// CONTROLADORES
$viewController = new ViewController();
$authController = new AuthController($db);

// LANDING
$router->get('/', [
    $viewController,
    'landing'
]);

// LOGIN
$router->get('/login', [
    $authController,
    'showLogin'
]);

$router->post('/login', [
    $authController,
    'login'
]);

// LOGOUT
$router->post('/logout', [
    $authController,
    'logout'
]);

// ADMIN
$router->get('/admin', function () use ($viewController): void {
    AuthMiddleware::handle();
    $viewController->dashboard();
});

// PRODUCTOS
$router->get('/admin/productos', function () use ($viewController): void {
    AuthMiddleware::handle();
    $viewController->products();
});