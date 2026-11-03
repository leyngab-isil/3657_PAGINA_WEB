<?php

// uso para servide

namespace App\Models;

use PDO;

class DetallePedido
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function crear(array $data): int
    {
        $sql = "
            INSERT INTO detalle_pedido
            (
                id_pedido,
                id_producto,
                cantidad,
                precio_unitario,
                subtotal,
                observacion
            )
            VALUES
            (
                :id_pedido,
                :id_producto,
                :cantidad,
                :precio_unitario,
                :subtotal,
                :observacion
            )
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':id_pedido' => $data['id_pedido'],
            ':id_producto' => $data['id_producto'],
            ':cantidad' => $data['cantidad'],
            ':precio_unitario' => $data['precio_unitario'],
            ':subtotal' => $data['subtotal'],
            ':observacion' => $data['observacion'] ?? null
        ]);

        return (int) $this->db->lastInsertId();
    }


    public function obtenerPorPedido(int $idPedido): array
    {
        $sql = "
            SELECT
                d.id_detalle,
                d.id_producto,
                p.nombre AS producto,
                d.cantidad,
                d.precio_unitario,
                d.subtotal,
                d.observacion
            FROM detalle_pedido d

            INNER JOIN productos p
                ON d.id_producto = p.id_producto

            WHERE d.id_pedido = :id_pedido

            ORDER BY d.id_detalle
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':id_pedido' => $idPedido
        ]);

        return $stmt->fetchAll();
    }
}