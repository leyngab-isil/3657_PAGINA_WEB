<?php

namespace App\Controllers;

use App\Models\Mesa;
use Core\Request;
use Core\Response;
use PDO;

class MesaController
{
    private Mesa $mesa;

    public function __construct(PDO $db)
    {
        $this->mesa = new Mesa($db);
    }

    public function index(): void
    {
        Response::success(
            $this->mesa->obtenerTodos(),
            'Mesas obtenidas correctamente'
        );
    }

    public function show(int $id): void
    {
        $mesa = $this->mesa->obtenerPorId($id);

        if (!$mesa) {
            Response::error('Mesa no encontrada', 404);
        }

        Response::success($mesa);
    }

    public function store(): void
    {
        $data = Request::body();

        if (
            !isset($data['numero']) ||
            !isset($data['capacidad'])
        ) {
            Response::error(
                'Número y capacidad son obligatorios',
                422
            );
        }

        $id = $this->mesa->crear($data);

        Response::success(
            ['id_mesa' => $id],
            'Mesa registrada correctamente',
            201
        );
    }

    public function update(int $id): void
    {
        if (!$this->mesa->obtenerPorId($id)) {
            Response::error('Mesa no encontrada', 404);
        }

        $data = Request::body();

        $this->mesa->actualizar($id, $data);

        Response::success(
            null,
            'Mesa actualizada correctamente'
        );
    }

    public function cambiarEstado(int $id): void
    {
        if (!$this->mesa->obtenerPorId($id)) {
            Response::error('Mesa no encontrada', 404);
        }

        $data = Request::body();

        $estados = [
            'DISPONIBLE',
            'OCUPADA',
            'RESERVADA',
            'INACTIVA'
        ];

        if (
            empty($data['estado']) ||
            !in_array($data['estado'], $estados)
        ) {
            Response::error(
                'Estado de mesa no válido',
                422
            );
        }

        $this->mesa->cambiarEstado(
            $id,
            $data['estado']
        );

        Response::success(
            null,
            'Estado actualizado correctamente'
        );
    }

    public function destroy(int $id): void
    {
        $this->mesa->eliminar($id);

        Response::success(
            null,
            'Mesa desactivada correctamente'
        );
    }
}
