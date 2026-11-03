<?php

namespace App\Controllers;

use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Services\CompraService;
use Core\Request;
use Core\Response;
use PDO;
use Throwable;

class CompraController
{
    private Compra $compra;

    private DetalleCompra $detalle;

    private CompraService $service;

    public function __construct(PDO $db)
    {
        $this->compra = new Compra($db);

        $this->detalle = new DetalleCompra($db);

        $this->service = new CompraService($db);
    }

    public function index(): void
    {
        Response::success(
            $this->compra->obtenerTodos(),
            'Compras obtenidas correctamente'
        );
    }

    public function show(int $id): void
    {
        $compra = $this->compra->obtenerPorId($id);

        if (!$compra) {
            Response::error(
                'Compra no encontrada',
                404
            );
        }

        $compra['insumos'] =
            $this->detalle->obtenerPorCompra($id);

        Response::success(
            $compra,
            'Compra obtenida correctamente'
        );
    }

    public function store(): void
    {
        try {

            $data = Request::body();

            $compra = $this->service->crear($data);

            Response::success(
                $compra,
                'Compra registrada correctamente',
                201
            );

        } catch (Throwable $e) {

            Response::error(
                $e->getMessage(),
                422
            );
        }
    }

    public function anular(int $id): void
    {
        $compra = $this->compra->obtenerPorId($id);

        if (!$compra) {
            Response::error(
                'Compra no encontrada',
                404
            );
        }

        $this->compra->anular($id);

        Response::success(
            null,
            'Compra anulada correctamente'
        );
    }
}