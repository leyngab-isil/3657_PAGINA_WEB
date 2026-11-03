<?php

namespace App\Controllers;

use App\Models\Proveedor;
use Core\Request;
use Core\Response;
use PDO;

class ProveedorController
{
    private Proveedor $proveedor;

    public function __construct(PDO $db)
    {
        $this->proveedor = new Proveedor($db);
    }

    public function index(): void
    {
        Response::success(
            $this->proveedor->obtenerTodos(),
            'Proveedores obtenidos correctamente'
        );
    }

    public function show(int $id): void
    {
        $proveedor = $this->proveedor->obtenerPorId($id);

        if (!$proveedor) {
            Response::error('Proveedor no encontrado', 404);
        }

        Response::success($proveedor);
    }

    public function store(): void
    {
        $data = Request::body();

        if (empty($data['razon_social'])) {
            Response::error(
                'La razón social es obligatoria',
                422
            );
        }

        $id = $this->proveedor->crear($data);

        Response::success(
            ['id_proveedor' => $id],
            'Proveedor registrado correctamente',
            201
        );
    }

    public function destroy(int $id): void
    {
        if (!$this->proveedor->obtenerPorId($id)) {
            Response::error('Proveedor no encontrado', 404);
        }

        $this->proveedor->eliminar($id);

        Response::success(
            null,
            'Proveedor eliminado correctamente'
        );
    }
}