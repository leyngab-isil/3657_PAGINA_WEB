<?php

namespace App\Services;

use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Insumo;
use PDO;
use Exception;
use App\Services\InventarioService;

class CompraService
{
    private PDO $db;

    private Compra $compra;
    private DetalleCompra $detalleCompra;
    private Insumo $insumo;

    private InventarioService $inventarioService;

    public function __construct(PDO $db)
    {
        $this->db = $db;

        $this->compra = new Compra($db);

        $this->detalleCompra =
            new DetalleCompra($db);

        $this->insumo = new Insumo($db);

        $this->inventarioService = new InventarioService($db);
    }


    public function crear(array $data): array
    {
        // ============================
        // VALIDACIONES
        // ============================

        if (empty($data['id_proveedor'])) {

            throw new Exception(
                'El proveedor es obligatorio.'
            );
        }


        if (empty($data['id_usuario'])) {

            throw new Exception(
                'El usuario es obligatorio.'
            );
        }


        if (
            empty($data['insumos']) ||
            !is_array($data['insumos'])
        ) {

            throw new Exception(
                'La compra debe contener insumos.'
            );
        }


        try {

            // ============================
            // INICIAR TRANSACCIÓN
            // ============================

            $this->db->beginTransaction();


            $subtotalCompra = 0;

            $insumosProcesados = [];


            // ============================
            // PROCESAR INSUMOS
            // ============================

            foreach ($data['insumos'] as $item) {

                if (
                    empty($item['id_insumo']) ||
                    !isset($item['cantidad']) ||
                    !isset($item['costo_unitario'])
                ) {

                    throw new Exception(
                        'Datos del insumo incompletos.'
                    );
                }


                $insumo = $this->insumo->obtenerPorId(
                    (int) $item['id_insumo']
                );


                if (!$insumo) {

                    throw new Exception(
                        'Uno de los insumos no existe.'
                    );
                }


                $cantidad =
                    (float) $item['cantidad'];

                $costoUnitario =
                    (float) $item['costo_unitario'];


                if ($cantidad <= 0) {

                    throw new Exception(
                        'La cantidad debe ser mayor que cero.'
                    );
                }


                if ($costoUnitario < 0) {

                    throw new Exception(
                        'El costo unitario no puede ser negativo.'
                    );
                }


                $subtotal =
                    $cantidad *
                    $costoUnitario;


                $subtotalCompra += $subtotal;


                $insumosProcesados[] = [

                    'id_insumo' =>
                        (int) $item['id_insumo'],

                    'cantidad' =>
                        $cantidad,

                    'costo_unitario' =>
                        $costoUnitario,

                    'subtotal' =>
                        $subtotal,

                    'stock_anterior' =>
                        (float) $insumo['stock_actual']
                ];
            }


            // ============================
            // IMPUESTO
            // ============================

            $porcentajeImpuesto = 0.18;

            $impuesto =
                $subtotalCompra *
                $porcentajeImpuesto;


            $total =
                $subtotalCompra +
                $impuesto;


            // ============================
            // CREAR COMPRA
            // ============================

            $idCompra =
                $this->compra->crear([

                    'id_proveedor' =>
                        $data['id_proveedor'],

                    'id_usuario' =>
                        $data['id_usuario'],

                    'subtotal' =>
                        $subtotalCompra,

                    'impuesto' =>
                        $impuesto,

                    'total' =>
                        $total
                ]);


            // ============================
            // DETALLE + STOCK
            // ============================

            foreach (
                $insumosProcesados as $item
            ) {

                // Crear el detalle

                $this->detalleCompra->crear([

                    'id_compra' =>
                        $idCompra,

                    'id_insumo' =>
                        $item['id_insumo'],

                    'cantidad' =>
                        $item['cantidad'],

                    'costo_unitario' =>
                        $item['costo_unitario'],

                    'subtotal' =>
                        $item['subtotal']
                ]);


                // Calcular nuevo stock

                $nuevoStock =
                    $item['stock_anterior'] +
                    $item['cantidad'];


                // Actualizar stock

                // $this->insumo->actualizarStock(
                //     $item['id_insumo'],
                //     $nuevoStock
                // );
            }


            // ============================
            // CONFIRMAR
            // ============================

            $this->db->commit();


            return [

                'id_compra' => $idCompra,

                'subtotal' =>
                    round($subtotalCompra, 2),

                'impuesto' =>
                    round($impuesto, 2),

                'total' =>
                    round($total, 2),

                'estado' =>
                    'REGISTRADA',

                'insumos' =>
                    $insumosProcesados
            ];


        } catch (\Throwable $e) {

            if ($this->db->inTransaction()) {

                $this->db->rollBack();
            }

            throw $e;
        }
    }
}