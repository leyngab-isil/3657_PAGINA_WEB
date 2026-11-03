<?php

namespace App\Controllers;

use App\Models\Categoria;
use Core\Request;
use Core\Response;
use PDO;

class CategoriaController
{
    private Categoria $categoria;

    public function __construct(PDO $db)
    {
        $this->categoria = new Categoria($db);
    }

    public function index(): void
    {
        $categorias = $this->categoria->obtenerTodos();

        Response::success(
            $categorias,
            'Categorías obtenidas correctamente'
        );
    }

    public function show(int $id): void
    {
        $categoria = $this->categoria->obtenerPorId($id);

        if (!$categoria) {
            Response::error(
                'Categoría no encontrada',
                404
            );
        }

        Response::success($categoria);
    }

    public function store(): void
    {
        $data = Request::body();

        if (empty($data['nombre'])) {
            Response::error(
                'El nombre es obligatorio',
                422
            );
        }

        $this->categoria->crear($data);

        Response::success(
            null,
            'Categoría registrada correctamente',
            201
        );
    }

    public function update(int $id): void
    {
        if (!$this->categoria->obtenerPorId($id)) {
            Response::error(
                'Categoría no encontrada',
                404
            );
        }

        $data = Request::body();

        if (empty($data['nombre'])) {
            Response::error(
                'El nombre es obligatorio',
                422
            );
        }

        $this->categoria->actualizar($id, $data);

        Response::success(
            null,
            'Categoría actualizada correctamente'
        );
    }

    public function destroy(int $id): void
    {
        if (!$this->categoria->obtenerPorId($id)) {
            Response::error(
                'Categoría no encontrada',
                404
            );
        }

        $this->categoria->eliminar($id);

        Response::success(
            null,
            'Categoría eliminada correctamente'
        );
    }
}