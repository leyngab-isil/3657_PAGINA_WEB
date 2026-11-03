<?php

namespace App\Models;

use PDO;

class Pedido
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
                p.id_pedido,
                p.fecha_pedido,
                p.estado,
                p.subtotal,
                p.impuesto,
                p.total,
                p.observacion,

                m.numero AS mesa,

                CONCAT(
                    c.nombres,
                    ' ',
                    COALESCE(c.apellidos, '')
                ) AS cliente,

                CONCAT(
                    u.nombres,
                    ' ',
                    u.apellidos
                ) AS usuario

            FROM pedidos p

            LEFT JOIN mesas m
                ON p.id_mesa = m.id_mesa

            LEFT JOIN clientes c
                ON p.id_cliente = c.id_cliente

            INNER JOIN usuarios u
                ON p.id_usuario = u.id_usuario

            ORDER BY p.fecha_pedido DESC
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }


    public function obtenerPorId(int $id): array|false
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM pedidos
            WHERE id_pedido = :id
        ");

        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->fetch();
    }


    public function crear(array $data): int
    {
        $sql = "
            INSERT INTO pedidos
            (
                id_mesa,
                id_cliente,
                id_usuario,
                estado,
                subtotal,
                impuesto,
                total,
                observacion
            )
            VALUES
            (
                :id_mesa,
                :id_cliente,
                :id_usuario,
                :estado,
                :subtotal,
                :impuesto,
                :total,
                :observacion
            )
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':id_mesa' => $data['id_mesa'] ?? null,
            ':id_cliente' => $data['id_cliente'] ?? null,
            ':id_usuario' => $data['id_usuario'],
            ':estado' => $data['estado'] ?? 'PENDIENTE',
            ':subtotal' => $data['subtotal'],
            ':impuesto' => $data['impuesto'],
            ':total' => $data['total'],
            ':observacion' => $data['observacion'] ?? null
        ]);

        return (int) $this->db->lastInsertId();
    }


    public function cambiarEstado(
        int $id,
        string $estado
    ): bool {

        $stmt = $this->db->prepare("
            UPDATE pedidos
            SET estado = :estado
            WHERE id_pedido = :id
        ");

        return $stmt->execute([
            ':estado' => $estado,
            ':id' => $id
        ]);
    }
}