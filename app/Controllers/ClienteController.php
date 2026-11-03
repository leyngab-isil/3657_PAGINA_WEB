<?php

namespace App\Controllers;

use App\Models\Cliente;
use Core\Request;
use Core\Response;
use PDO;

class ClienteController
{
    private Cliente $cliente;

    public function __construct(PDO $db)
    {
        $this->cliente = new Cliente($db);
    }

    public function index(): void
    {
        Response::success(
            $this->cliente->obtenerTodos(),
            'Clientes obtenidos correctamente'
        );
    }

    public function show(int $id): void
    {
        $cliente = $this->cliente->obtenerPorId($id);

        if (!$cliente) {
            Response::error('Cliente no encontrado', 404);
        }

        Response::success($cliente);
    }

    public function store(): void
    {
        $data = Request::body();

        if (empty($data['nombres'])) {
            Response::error(
                'El nombre del cliente es obligatorio',
                422
            );
        }

        $id = $this->cliente->crear($data);

        Response::success(
            ['id_cliente' => $id],
            'Cliente registrado correctamente',
            201
        );
    }

    public function update(int $id): void
    {
        if (!$this->cliente->obtenerPorId($id)) {
            Response::error('Cliente no encontrado', 404);
        }

        $data = Request::body();

        if (empty($data['nombres'])) {
            Response::error('El nombre es obligatorio', 422);
        }

        $this->cliente->actualizar($id, $data);

        Response::success(
            null,
            'Cliente actualizado correctamente'
        );
    }

    public function destroy(int $id): void
    {
        if (!$this->cliente->obtenerPorId($id)) {
            Response::error('Cliente no encontrado', 404);
        }

        $this->cliente->eliminar($id);

        Response::success(
            null,
            'Cliente eliminado correctamente'
        );
    }
}