<?php

namespace App\Controllers;

use App\Models\MovimientoInventario;
use App\Services\InventarioService;
use Core\Request;
use Core\Response;
use PDO;
use Throwable;

class InventarioController
{
    private InventarioService $service;

    private MovimientoInventario $movimiento;


    public function __construct(PDO $db)
    {
        $this->service =
            new InventarioService($db);

        $this->movimiento =
            new MovimientoInventario($db);
    }


    public function movimientos(): void
    {
        Response::success(
            $this->movimiento->obtenerTodos(),
            'Movimientos obtenidos correctamente'
        );
    }


    public function movimientosPorInsumo(int $id): void
    {
        Response::success(
            $this->movimiento->obtenerPorInsumo($id),
            'Movimientos del insumo obtenidos correctamente'
        );
    }


    public function entrada(): void
    {
        try {

            $data = Request::body();

            if (
                empty($data['id_insumo']) ||
                !isset($data['cantidad']) ||
                empty($data['id_usuario'])
            ) {
                Response::error(
                    'Insumo, cantidad y usuario son obligatorios',
                    422
                );
            }

            $resultado =
                $this->service->registrarEntrada(

                    (int) $data['id_insumo'],

                    (float) $data['cantidad'],

                    (int) $data['id_usuario'],

                    $data['motivo']
                        ?? 'Entrada manual de inventario'
                );

            Response::success(
                $resultado,
                'Entrada registrada correctamente',
                201
            );

        } catch (Throwable $e) {

            Response::error(
                $e->getMessage(),
                422
            );
        }
    }


    public function salida(): void
    {
        try {

            $data = Request::body();

            if (
                empty($data['id_insumo']) ||
                !isset($data['cantidad']) ||
                empty($data['id_usuario'])
            ) {
                Response::error(
                    'Insumo, cantidad y usuario son obligatorios',
                    422
                );
            }

            $resultado =
                $this->service->registrarSalida(

                    (int) $data['id_insumo'],

                    (float) $data['cantidad'],

                    (int) $data['id_usuario'],

                    $data['motivo']
                        ?? 'Salida manual de inventario'
                );

            Response::success(
                $resultado,
                'Salida registrada correctamente',
                201
            );

        } catch (Throwable $e) {

            Response::error(
                $e->getMessage(),
                422
            );
        }
    }


    public function ajuste(): void
    {
        try {

            $data = Request::body();

            if (
                empty($data['id_insumo']) ||
                !isset($data['nuevo_stock']) ||
                empty($data['id_usuario']) ||
                empty($data['motivo'])
            ) {
                Response::error(
                    'Todos los datos son obligatorios',
                    422
                );
            }

            $resultado =
                $this->service->ajustarStock(

                    (int) $data['id_insumo'],

                    (float) $data['nuevo_stock'],

                    (int) $data['id_usuario'],

                    $data['motivo']
                );

            Response::success(
                $resultado,
                'Inventario ajustado correctamente'
            );

        } catch (Throwable $e) {

            Response::error(
                $e->getMessage(),
                422
            );
        }
    }
}