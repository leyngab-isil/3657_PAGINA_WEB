<?php

namespace App\Models;

use PDO;

class Pago
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
                pa.id_pago,
                pa.id_pedido,
                pa.metodo_pago,
                pa.monto,
                pa.fecha_pago,
                pa.referencia
            FROM pagos pa
            ORDER BY pa.fecha_pago DESC
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }


    public function obtenerPorId(int $id): array|false
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM pagos
            WHERE id_pago = :id
        ");

        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->fetch();
    }


    public function crear(array $data): int
    {
        $sql = "
            INSERT INTO pagos
            (
                id_pedido,
                metodo_pago,
                monto,
                referencia
            )
            VALUES
            (
                :id_pedido,
                :metodo_pago,
                :monto,
                :referencia
            )
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':id_pedido' => $data['id_pedido'],
            ':metodo_pago' => $data['metodo_pago'],
            ':monto' => $data['monto'],
            ':referencia' => $data['referencia'] ?? null
        ]);

        return (int) $this->db->lastInsertId();
    }
}