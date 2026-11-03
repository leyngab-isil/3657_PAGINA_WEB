<?php

namespace App\Models;

use PDO;

class Compra
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }


    public function obtenerTodos(): array
    {
        $sql = "
            SELECT
                c.id_compra,
                c.fecha_compra,
                c.subtotal,
                c.impuesto,
                c.total,
                c.estado,

                p.razon_social AS proveedor,

                CONCAT(
                    u.nombres,
                    ' ',
                    u.apellidos
                ) AS usuario

            FROM compras c

            INNER JOIN proveedores p
                ON c.id_proveedor = p.id_proveedor

            INNER JOIN usuarios u
                ON c.id_usuario = u.id_usuario

            ORDER BY c.fecha_compra DESC
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }


    public function obtenerPorId(int $id): array|false
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM compras
            WHERE id_compra = :id
        ");

        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->fetch();
    }


    public function crear(array $data): int
    {
        $sql = "
            INSERT INTO compras
            (
                id_proveedor,
                id_usuario,
                subtotal,
                impuesto,
                total
            )
            VALUES
            (
                :id_proveedor,
                :id_usuario,
                :subtotal,
                :impuesto,
                :total
            )
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':id_proveedor' => $data['id_proveedor'],
            ':id_usuario' => $data['id_usuario'],
            ':subtotal' => $data['subtotal'],
            ':impuesto' => $data['impuesto'],
            ':total' => $data['total']
        ]);

        return (int) $this->db->lastInsertId();
    }


    public function anular(int $id): bool
    {
        $stmt = $this->db->prepare("
            UPDATE compras
            SET estado = 'ANULADA'
            WHERE id_compra = :id
        ");

        return $stmt->execute([
            ':id' => $id
        ]);
    }
}