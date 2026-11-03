<?php

use App\Controllers\CategoriaController;

$categoriaController = new CategoriaController($db);

$router->get(
    '/api/categorias',
    [$categoriaController, 'index']
);

$router->get(
    '/api/categorias/{id}',
    [$categoriaController, 'show']
);

$router->post(
    '/api/categorias',
    [$categoriaController, 'store']
);

$router->put(
    '/api/categorias/{id}',
    [$categoriaController, 'update']
);

$router->delete(
    '/api/categorias/{id}',
    [$categoriaController, 'destroy']
);