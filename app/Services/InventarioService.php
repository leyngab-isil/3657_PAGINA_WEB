<?php

namespace App\Services;

use App\Models\Insumo;
use App\Models\MovimientoInventario;
use PDO;
use Exception;

class InventarioService
{
    private PDO $db;

    private Insumo $insumo;

    private MovimientoInventario $movimiento;


    public function __construct(PDO $db)
    {
        $this->db = $db;

        $this->insumo =
            new Insumo($db);

        $this->movimiento =
            new MovimientoInventario($db);
    }


    // =====================================================
    // REGISTRAR ENTRADA
    // =====================================================

    public function registrarEntrada(
        int $idInsumo,
        float $cantidad,
        int $idUsuario,
        string $motivo = 'Entrada de inventario'
    ): array {

        if ($cantidad <= 0) {
            throw new Exception(
                'La cantidad debe ser mayor que cero.'
            );
        }


        $insumo =
            $this->insumo->obtenerPorId(
                $idInsumo
            );


        if (!$insumo) {
            throw new Exception(
                'El insumo no existe.'
            );
        }


        /*
         * Leer stock actual
         */

        $stockAnterior =
            (float) $insumo['stock_actual'];


        /*
         * Calcular nuevo stock
         */

        $nuevoStock =
            $stockAnterior +
            $cantidad;


        /*
         * Actualizar stock
         */

        $this->insumo->actualizarStock(
            $idInsumo,
            $nuevoStock
        );


        /*
         * Registrar movimiento
         */

        $idMovimiento =
            $this->movimiento->crear([

                'id_insumo' =>
                    $idInsumo,

                'tipo' =>
                    'ENTRADA',

                'cantidad' =>
                    $cantidad,

                'motivo' =>
                    $motivo,

                'id_usuario' =>
                    $idUsuario
            ]);


        return [

            'id_movimiento' =>
                $idMovimiento,

            'id_insumo' =>
                $idInsumo,

            'tipo' =>
                'ENTRADA',

            'cantidad' =>
                $cantidad,

            'stock_anterior' =>
                $stockAnterior,

            'stock_actual' =>
                $nuevoStock
        ];
    }


    // =====================================================
    // REGISTRAR SALIDA
    // =====================================================

    public function registrarSalida(
        int $idInsumo,
        float $cantidad,
        int $idUsuario,
        string $motivo = 'Salida de inventario'
    ): array {

        if ($cantidad <= 0) {

            throw new Exception(
                'La cantidad debe ser mayor que cero.'
            );
        }


        $insumo =
            $this->insumo->obtenerPorId(
                $idInsumo
            );


        if (!$insumo) {

            throw new Exception(
                'El insumo no existe.'
            );
        }


        $stockAnterior =
            (float) $insumo['stock_actual'];


        /*
         * Verificar stock disponible
         */

        if ($cantidad > $stockAnterior) {

            throw new Exception(
                'Stock insuficiente para realizar la salida.'
            );
        }


        /*
         * Calcular nuevo stock
         */

        $nuevoStock =
            $stockAnterior -
            $cantidad;


        /*
         * Actualizar stock
         */

        $this->insumo->actualizarStock(
            $idInsumo,
            $nuevoStock
        );


        /*
         * Registrar movimiento
         */

        $idMovimiento =
            $this->movimiento->crear([

                'id_insumo' =>
                    $idInsumo,

                'tipo' =>
                    'SALIDA',

                'cantidad' =>
                    $cantidad,

                'motivo' =>
                    $motivo,

                'id_usuario' =>
                    $idUsuario
            ]);


        return [

            'id_movimiento' =>
                $idMovimiento,

            'id_insumo' =>
                $idInsumo,

            'tipo' =>
                'SALIDA',

            'cantidad' =>
                $cantidad,

            'stock_anterior' =>
                $stockAnterior,

            'stock_actual' =>
                $nuevoStock
        ];
    }


    // =====================================================
    // AJUSTAR STOCK
    // =====================================================

    public function ajustarStock(
        int $idInsumo,
        float $nuevoStock,
        int $idUsuario,
        string $motivo
    ): array {

        if ($nuevoStock < 0) {

            throw new Exception(
                'El stock no puede ser negativo.'
            );
        }


        if (trim($motivo) === '') {

            throw new Exception(
                'Debe indicar el motivo del ajuste.'
            );
        }


        $insumo =
            $this->insumo->obtenerPorId(
                $idInsumo
            );


        if (!$insumo) {

            throw new Exception(
                'El insumo no existe.'
            );
        }


        $stockAnterior =
            (float) $insumo['stock_actual'];


        /*
         * Diferencia encontrada
         */

        $diferencia =
            abs(
                $nuevoStock -
                $stockAnterior
            );


        /*
         * Actualizar stock
         */

        $this->insumo->actualizarStock(
            $idInsumo,
            $nuevoStock
        );


        /*
         * Registrar movimiento
         */

        $idMovimiento =
            $this->movimiento->crear([

                'id_insumo' =>
                    $idInsumo,

                'tipo' =>
                    'AJUSTE',

                'cantidad' =>
                    $diferencia,

                'motivo' =>
                    $motivo,

                'id_usuario' =>
                    $idUsuario
            ]);


        return [

            'id_movimiento' =>
                $idMovimiento,

            'id_insumo' =>
                $idInsumo,

            'tipo' =>
                'AJUSTE',

            'stock_anterior' =>
                $stockAnterior,

            'stock_actual' =>
                $nuevoStock,

            'diferencia' =>
                $diferencia
        ];
    }
}