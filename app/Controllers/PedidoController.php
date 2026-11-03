<?php

namespace App\Controllers;

use App\Models\Pedido;
use App\Models\DetallePedido;
use App\Services\PedidoService;
use Core\Request;
use Core\Response;
use PDO;
use Throwable;

class PedidoController
{
    private Pedido $pedido;

    private DetallePedido $detalle;

    private PedidoService $service;

    public function __construct(PDO $db)
    {
        $this->pedido = new Pedido($db);

        $this->detalle = new DetallePedido($db);

        $this->service = new PedidoService($db);
    }

    public function index(): void
    {
        Response::success(
            $this->pedido->obtenerTodos(),
            'Pedidos obtenidos correctamente'
        );
    }

    public function show(int $id): void
    {
        $pedido = $this->pedido->obtenerPorId($id);

        if (!$pedido) {
            Response::error(
                'Pedido no encontrado',
                404
            );
        }

        $pedido['productos'] =
            $this->detalle->obtenerPorPedido($id);

        Response::success(
            $pedido,
            'Pedido obtenido correctamente'
        );
    }

    public function store(): void
    {
        try {

            $data = Request::body();

            $pedido = $this->service->crear($data);

            Response::success(
                $pedido,
                'Pedido registrado correctamente',
                201
            );

        } catch (Throwable $e) {

            Response::error(
                $e->getMessage(),
                422
            );
        }
    }

    public function cambiarEstado(int $id): void
    {
        $pedido = $this->pedido->obtenerPorId($id);

        if (!$pedido) {
            Response::error(
                'Pedido no encontrado',
                404
            );
        }

        $data = Request::body();

        $estados = [
            'PENDIENTE',
            'PREPARANDO',
            'LISTO',
            'ENTREGADO',
            'CANCELADO'
        ];

        if (
            empty($data['estado']) ||
            !in_array($data['estado'], $estados)
        ) {
            Response::error(
                'Estado de pedido no válido',
                422
            );
        }

        $this->pedido->cambiarEstado(
            $id,
            $data['estado']
        );

        Response::success(
            null,
            'Estado del pedido actualizado'
        );
    }
}