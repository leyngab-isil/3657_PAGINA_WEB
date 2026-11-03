<?php

namespace App\Controllers;

use App\Models\Pago;
use Core\Request;
use Core\Response;
use PDO;

class PagoController
{
    private Pago $pago;

    public function __construct(PDO $db)
    {
        $this->pago = new Pago($db);
    }

    public function index(): void
    {
        Response::success(
            $this->pago->obtenerTodos(),
            'Pagos obtenidos correctamente'
        );
    }

    public function show(int $id): void
    {
        $pago = $this->pago->obtenerPorId($id);

        if (!$pago) {
            Response::error('Pago no encontrado', 404);
        }

        Response::success($pago);
    }

    public function store(): void
    {
        $data = Request::body();

        if (
            empty($data['id_pedido']) ||
            empty($data['metodo_pago']) ||
            !isset($data['monto'])
        ) {
            Response::error(
                'Pedido, método de pago y monto son obligatorios',
                422
            );
        }

        $metodos = [
            'EFECTIVO',
            'TARJETA',
            'TRANSFERENCIA',
            'OTRO'
        ];

        if (!in_array($data['metodo_pago'], $metodos)) {
            Response::error(
                'Método de pago no válido',
                422
            );
        }

        if ((float) $data['monto'] <= 0) {
            Response::error(
                'El monto debe ser mayor que cero',
                422
            );
        }

        $id = $this->pago->crear($data);

        Response::success(
            ['id_pago' => $id],
            'Pago registrado correctamente',
            201
        );
    }
}