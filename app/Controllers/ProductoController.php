<?php

namespace App\Controllers;

use App\Models\Producto;
use Core\Request;
use Core\Response;
use PDO;

class ProductoController
{
    private Producto $producto;

    public function __construct(PDO $db)
    {
        $this->producto = new Producto($db);
    }

    public function index(): void
    {
        $search = trim($_GET['search'] ?? '');

        $page = filter_input(
            INPUT_GET,
            'page',
            FILTER_VALIDATE_INT
        );

        $limit = filter_input(
            INPUT_GET,
            'limit',
            FILTER_VALIDATE_INT
        );

        $precio_min = filter_input(
            INPUT_GET,
            'precio_min',
            FILTER_VALIDATE_INT
        );

        Response::success(
            // $this->producto->obtenerTodos(),
            $this->producto->obtenerPaginados(
                $search,
                $page,
                $limit,
                $precio_min
            ),
            'Productos obtenidos correctamente'
        );
    }

    public function show(int $id): void
    {
        $producto = $this->producto->obtenerPorId($id);

        if (!$producto) {
            Response::error(
                'Producto no encontrado',
                404
            );
        }

        Response::success($producto);
    }

    public function store(): void
    {
        $data = Request::body();

        if (
            empty($data['nombre']) ||
            empty($data['id_categoria']) ||
            !isset($data['precio'])
        ) {
            Response::error(
                'Nombre, categoría y precio son obligatorios',
                422
            );
        }

        if ((float) $data['precio'] < 0) {
            Response::error(
                'El precio no puede ser negativo',
                422
            );
        }

        $id = $this->producto->crear($data);

        Response::success(
            ['id_producto' => $id],
            'Producto registrado correctamente',
            201
        );
    }

    public function update(int $id): void
    {
        if (!$this->producto->obtenerPorId($id)) {
            Response::error('Producto no encontrado', 404);
        }

        $data = Request::body();

        if (
            empty($data['nombre']) ||
            empty($data['id_categoria']) ||
            !isset($data['precio'])
        ) {
            Response::error(
                'Nombre, categoría y precio son obligatorios',
                422
            );
        }

        $this->producto->actualizar($id, $data);

        Response::success(
            null,
            'Producto actualizado correctamente'
        );
    }

    public function destroy(int $id): void
    {
        if (!$this->producto->obtenerPorId($id)) {
            Response::error('Producto no encontrado', 404);
        }

        $this->producto->eliminar($id);

        Response::success(
            null,
            'Producto eliminado correctamente'
        );
    }
}
