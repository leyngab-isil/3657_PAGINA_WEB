<?php

namespace App\Controllers;

use App\Models\Rol;
use Core\Request;
use Core\Response;
use PDO;

class RolController
{
    private Rol $rol;

    public function __construct(PDO $db)
    {
        $this->rol = new Rol($db);
    }

    public function index(): void
    {
        Response::success(
            $this->rol->obtenerTodos(),
            'Roles obtenidos correctamente'
        );
    }

    public function show(int $id): void
    {
        $rol = $this->rol->obtenerPorId($id);

        if (!$rol) {
            Response::error('Rol no encontrado', 404);
        }

        Response::success($rol);
    }

    public function store(): void
    {
        $data = Request::body();

        if (empty($data['nombre'])) {
            Response::error(
                'El nombre del rol es obligatorio',
                422
            );
        }

        $id = $this->rol->crear($data);

        Response::success(
            ['id_rol' => $id],
            'Rol registrado correctamente',
            201
        );
    }

    public function destroy(int $id): void
    {
        if (!$this->rol->obtenerPorId($id)) {
            Response::error('Rol no encontrado', 404);
        }

        $this->rol->eliminar($id);

        Response::success(
            null,
            'Rol eliminado correctamente'
        );
    }
}