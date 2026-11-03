<?php

use App\Controllers\CategoriaController;
use App\Controllers\ProductoController;
use App\Controllers\ClienteController;
use App\Controllers\MesaController;
use App\Controllers\RolController;
use App\Controllers\UsuarioController;
use App\Controllers\ProveedorController;
use App\Controllers\InsumoController;
use App\Controllers\PagoController;
use App\Controllers\PedidoController;
use App\Controllers\CompraController;
use App\Controllers\InventarioController;


// ==========================================================
// INSTANCIAR CONTROLLERS
// ==========================================================

$categoriaController = new CategoriaController($db);

$productoController = new ProductoController($db);

$clienteController = new ClienteController($db);

$mesaController = new MesaController($db);

$rolController = new RolController($db);

$usuarioController = new UsuarioController($db);

$proveedorController = new ProveedorController($db);

$insumoController = new InsumoController($db);

$pagoController = new PagoController($db);

$pedidoController = new PedidoController($db);

$compraController = new CompraController($db);

$inventarioController = new InventarioController($db);


// ==========================================================
// CATEGORÍAS
// ==========================================================

// Listar categorías
$router->get(
    '/api/categorias',
    [$categoriaController, 'index']
);


// Obtener categoría
$router->get(
    '/api/categorias/{id}',
    [$categoriaController, 'show']
);


// Crear categoría
$router->post(
    '/api/categorias',
    [$categoriaController, 'store']
);


// Actualizar categoría
$router->put(
    '/api/categorias/{id}',
    [$categoriaController, 'update']
);


// Eliminar categoría
$router->delete(
    '/api/categorias/{id}',
    [$categoriaController, 'destroy']
);


// ==========================================================
// PRODUCTOS
// ==========================================================

$router->get(
    '/api/productos',
    [$productoController, 'index']
);


$router->get(
    '/api/productos/{id}',
    [$productoController, 'show']
);


$router->post(
    '/api/productos',
    [$productoController, 'store']
);


$router->put(
    '/api/productos/{id}',
    [$productoController, 'update']
);


$router->delete(
    '/api/productos/{id}',
    [$productoController, 'destroy']
);


// ==========================================================
// CLIENTES
// ==========================================================

$router->get(
    '/api/clientes',
    [$clienteController, 'index']
);


$router->get(
    '/api/clientes/{id}',
    [$clienteController, 'show']
);


$router->post(
    '/api/clientes',
    [$clienteController, 'store']
);


$router->put(
    '/api/clientes/{id}',
    [$clienteController, 'update']
);


$router->delete(
    '/api/clientes/{id}',
    [$clienteController, 'destroy']
);


// ==========================================================
// MESAS
// ==========================================================

$router->get(
    '/api/mesas',
    [$mesaController, 'index']
);


$router->get(
    '/api/mesas/{id}',
    [$mesaController, 'show']
);


$router->post(
    '/api/mesas',
    [$mesaController, 'store']
);


$router->put(
    '/api/mesas/{id}',
    [$mesaController, 'update']
);


// Cambiar estado de mesa
$router->put(
    '/api/mesas/{id}/estado',
    [$mesaController, 'cambiarEstado']
);


$router->delete(
    '/api/mesas/{id}',
    [$mesaController, 'destroy']
);


// ==========================================================
// ROLES
// ==========================================================

$router->get(
    '/api/roles',
    [$rolController, 'index']
);


$router->get(
    '/api/roles/{id}',
    [$rolController, 'show']
);


$router->post(
    '/api/roles',
    [$rolController, 'store']
);


$router->delete(
    '/api/roles/{id}',
    [$rolController, 'destroy']
);


// ==========================================================
// USUARIOS
// ==========================================================

$router->get(
    '/api/usuarios',
    [$usuarioController, 'index']
);


$router->get(
    '/api/usuarios/{id}',
    [$usuarioController, 'show']
);


$router->post(
    '/api/usuarios',
    [$usuarioController, 'store']
);


$router->delete(
    '/api/usuarios/{id}',
    [$usuarioController, 'destroy']
);


// ==========================================================
// PROVEEDORES
// ==========================================================

$router->get(
    '/api/proveedores',
    [$proveedorController, 'index']
);


$router->get(
    '/api/proveedores/{id}',
    [$proveedorController, 'show']
);


$router->post(
    '/api/proveedores',
    [$proveedorController, 'store']
);


$router->delete(
    '/api/proveedores/{id}',
    [$proveedorController, 'destroy']
);


// ==========================================================
// INSUMOS
// ==========================================================

$router->get(
    '/api/insumos',
    [$insumoController, 'index']
);


$router->get(
    '/api/insumos/{id}',
    [$insumoController, 'show']
);


$router->post(
    '/api/insumos',
    [$insumoController, 'store']
);


$router->delete(
    '/api/insumos/{id}',
    [$insumoController, 'destroy']
);


// ==========================================================
// PEDIDOS
// ==========================================================

// Listar pedidos
$router->get(
    '/api/pedidos',
    [$pedidoController, 'index']
);


// Obtener pedido + detalles
$router->get(
    '/api/pedidos/{id}',
    [$pedidoController, 'show']
);


// Crear pedido
$router->post(
    '/api/pedidos',
    [$pedidoController, 'store']
);


// Cambiar estado del pedido
$router->put(
    '/api/pedidos/{id}/estado',
    [$pedidoController, 'cambiarEstado']
);


// ==========================================================
// PAGOS
// ==========================================================

$router->get(
    '/api/pagos',
    [$pagoController, 'index']
);


$router->get(
    '/api/pagos/{id}',
    [$pagoController, 'show']
);


$router->post(
    '/api/pagos',
    [$pagoController, 'store']
);


// ==========================================================
// COMPRAS
// ==========================================================

// Listar compras
$router->get(
    '/api/compras',
    [$compraController, 'index']
);


// Obtener compra + detalles
$router->get(
    '/api/compras/{id}',
    [$compraController, 'show']
);


// Registrar compra
$router->post(
    '/api/compras',
    [$compraController, 'store']
);


// Anular compra
$router->put(
    '/api/compras/{id}/anular',
    [$compraController, 'anular']
);


// ==========================================================
// INVENTARIO
// ==========================================================

// Todos los movimientos
$router->get(
    '/api/inventario/movimientos',
    [$inventarioController, 'movimientos']
);


// Movimientos de un insumo
$router->get(
    '/api/inventario/insumos/{id}/movimientos',
    [$inventarioController, 'movimientosPorInsumo']
);


// Entrada manual
$router->post(
    '/api/inventario/entrada',
    [$inventarioController, 'entrada']
);


// Salida manual
$router->post(
    '/api/inventario/salida',
    [$inventarioController, 'salida']
);


// Ajuste manual
$router->post(
    '/api/inventario/ajuste',
    [$inventarioController, 'ajuste']
);