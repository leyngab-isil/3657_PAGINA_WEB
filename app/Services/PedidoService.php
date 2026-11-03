<?php

namespace App\Services;

use App\Models\Pedido;
use App\Models\DetallePedido;
use App\Models\Producto;
use App\Models\Mesa;
use PDO;
use Exception;

class PedidoService
{
    private PDO $db;

    private Pedido $pedido;
    private DetallePedido $detallePedido;
    private Producto $producto;
    private Mesa $mesa;


    public function __construct(PDO $db)
    {
        $this->db = $db;

        $this->pedido = new Pedido($db);
        $this->detallePedido = new DetallePedido($db);
        $this->producto = new Producto($db);
        $this->mesa = new Mesa($db);
    }


    public function crear(array $data): array
    {
        /*
         * Validaciones básicas
         */

        if (empty($data['id_usuario'])) {
            throw new Exception(
                'El usuario es obligatorio.'
           );
        }

        if (
           empty($data['productos']) ||
            !is_array($data['productos'])
        ) {
            throw new Exception(
               'El pedido debe contener productos*'
            );
        }


       try {

            // ==========*=================
            // I*ICIAR TRANSACCIÓN
            // =*==========================

            $this->db->beginTransaction();


            // ============================
            // CALCULAR PEDIDO
            // ============================

            $subtotalPedido = 0;

            $productosProcesados = [];


            foreach ($data['productos'] as $item) {

                if (
                    empty($item['id_producto']) ||
                    empty($item['cantidad'])
                ) {
                    throw new Exception(
                        'Producto o cantidad no válidos.'
                    );
                }


                $producto = $this->producto->obtenerPorId(
                    (int) $item['id_producto']
                );


                if (!$producto) {
                    throw new Exception(
                        'Uno de los productos no existe.'
                    );
                }


                if (!$producto['disponible']) {
                    throw new Exception(
                        'El producto ' .
                        $producto['nombre'] .
                        ' no está disponible.'
                    );
                }


                $cantidad = (int) $item['cantidad'];

                if ($cantidad <= 0) {
                    throw new Exception(
                        'La cantidad debe ser mayor que cero.'
                    );
                }


                /*
                 * IMPORTANTE:
                 *
                 * El precio se obtiene desde MySQL.
                 *
                 * No confiamos en un precio enviado
                 * desde Postman o JavaScript.
                 */

                $precio = (float) $producto['precio'];

                $subtotalProducto =
                    $precio * $cantidad;


                $subtotalPedido +=
                    $subtotalProducto;


                $productosProcesados[] = [

                    'id_producto' =>
                        $producto['id_producto'],

                    'cantidad' =>
                        $cantidad,

                    'precio_unitario' =>
                        $precio,

                    'subtotal' =>
                        $subtotalProducto,

                    'observacion' =>
                        $item['observacion'] ?? null
                ];
            }


            // ============================
            // CALCULAR IMPUESTO
            // ============================

            /*
             * Por ahora lo dejamos parametrizado.
             *
             * Posteriormente puede venir de
             * configuración del sistema.
             */

            $porcentajeImpuesto = 0.18;

            $impuesto =
                $subtotalPedido *
                $porcentajeImpuesto;


            $total =
                $subtotalPedido +
                $impuesto;


            // ============================
            // CREAR PEDIDO
            // ============================

            $idPedido = $this->pedido->crear([

                'id_mesa' =>
                    $data['id_mesa'] ?? null,

                'id_cliente' =>
                    $data['id_cliente'] ?? null,

                'id_usuario' =>
                    $data['id_usuario'],

                'estado' =>
                    'PENDIENTE',

                'subtotal' =>
                    $subtotalPedido,

                'impuesto' =>
                    $impuesto,

                'total' =>
                    $total,

                'observacion' =>
                    $data['observacion'] ?? null

            ]);


            // ============================
            // CREAR DETALLES
            // ============================

            foreach (
                $productosProcesados as $producto
            ) {

                $producto['id_pedido'] = $idPedido;

                $this->detallePedido->crear(
                    $producto
                );
            }


            // ============================
            // CAMBIAR ESTADO DE LA MESA
            // ============================

            if (!empty($data['id_mesa'])) {

                $this->mesa->cambiarEstado(
                    (int) $data['id_mesa'],
                    'OCUPADA'
                );
            }


            // ============================
            // CONFIRMAR TRANSACCIÓN
            // ============================

            $this->db->commit();


            return [

                'id_pedido' => $idPedido,

                'subtotal' =>
                    round($subtotalPedido, 2),

                'impuesto' =>
                    round($impuesto, 2),

                'total' =>
                    round($total, 2),

                'estado' => 'PENDIENTE',

                'productos' =>
                    $productosProcesados
            ];


        } catch (\Throwable $e) {

            // ============================
            // REVERTIR TRANSACCIÓN
            // ============================

            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }
}