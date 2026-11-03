<?php

namespace App\Controllers;

use App\Models\Insumo;
use Core\Request;
use Core\Response;
use PDO;

class InsumoController
{
    private Insumo $insumo;

    public function __construct(PDO $db)
    {
        $this->insumo = new Insumo($db);
    }

    public function index(): void
    {
        Response::success(
            $this->insumo->obtenerTodos(),
            'Insumos obtenidos correctamente'
        );
    }

    public function show(int $id): void
    {
        $insumo = $this->insumo->obtenerPorId($id);

        if (!$insumo) {
            Response::error('Insumo no encontrado', 404);
        }

        Response::success($insumo);
    }

    public function store(): void
    {
        $data = Request::body();

        if (
            empty($data['nombre']) ||
            empty($data['unidad_medida'])
        ) {
            Response::error(
                'Nombre y unidad de medida son obligatorios',
                422
            );
        }

        $id = $this->insumo->crear($data);

        Response::success(
            ['id_insumo' => $id],
            'Insumo registrado correctamente',
            201
        );
    }

    public function destroy(int $id): void
    {
        if (!$this->insumo->obtenerPorId($id)) {
            Response::error('Insumo no encontrado', 404);
        }

        $this->insumo->eliminar($id);

        Response::success(
            null,
            'Insumo eliminado correctamente'
        );
    }
}