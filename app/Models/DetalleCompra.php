<?php

// uso para servide

namespace App\Models;

use PDO;

class DetalleCompra
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }


    public function crear(array $data): int
    {
        $sql = "
            INSERT INTO detalle_compra
            (
                id_compra,
                id_insumo,
                cantidad,
                costo_unitario,
                subtotal
            )
            VALUES
            (
                :id_compra,
                :id_insumo,
                :cantidad,
                :costo_unitario,
                :subtotal
            )
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':id_compra' => $data['id_compra'],
            ':id_insumo' => $data['id_insumo'],
            ':cantidad' => $data['cantidad'],
            ':costo_unitario' => $data['costo_unitario'],
            ':subtotal' => $data['subtotal']
        ]);

        return (int) $this->db->lastInsertId();
    }


    public function obtenerPorCompra(int $idCompra): array
    {
        $sql = "
            SELECT
                d.id_detalle_compra,
                d.id_insumo,
                i.nombre AS insumo,
                d.cantidad,
                d.costo_unitario,
                d.subtotal
            FROM detalle_compra d

            INNER JOIN insumos i
                ON d.id_insumo = i.id_insumo

            WHERE d.id_compra = :id_compra
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':id_compra' => $idCompra
        ]);

        return $stmt->fetchAll();
    }
}