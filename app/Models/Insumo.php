<?php

namespace App\Models;

use PDO;

class Insumo
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }


    public function obtenerTodos(): array
    {
        $stmt = $this->db->prepare("
            SELECT
                id_insumo,
                nombre,
                unidad_medida,
                stock_actual,
                stock_minimo,
                costo_unitario,
                estado
            FROM insumos
            WHERE estado = 1
            ORDER BY nombre
        ");

        $stmt->execute();

        return $stmt->fetchAll();
    }


    public function obtenerPorId(int $id): array|false
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM insumos
            WHERE id_insumo = :id
        ");

        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->fetch();
    }


    public function crear(array $data): int
    {
        $sql = "
            INSERT INTO insumos
            (
                nombre,
                unidad_medida,
                stock_actual,
                stock_minimo,
                costo_unitario
            )
            VALUES
            (
                :nombre,
                :unidad_medida,
                :stock_actual,
                :stock_minimo,
                :costo_unitario
            )
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':nombre' => $data['nombre'],
            ':unidad_medida' => $data['unidad_medida'],
            ':stock_actual' => $data['stock_actual'] ?? 0,
            ':stock_minimo' => $data['stock_minimo'] ?? 0,
            ':costo_unitario' => $data['costo_unitario'] ?? 0
        ]);

        return (int) $this->db->lastInsertId();
    }


    public function actualizarStock(
        int $id,
        float $stock
    ): bool {

        $stmt = $this->db->prepare("
            UPDATE insumos
            SET stock_actual = :stock
            WHERE id_insumo = :id
        ");

        return $stmt->execute([
            ':stock' => $stock,
            ':id' => $id
        ]);
    }


    public function eliminar(int $id): bool
    {
        $stmt = $this->db->prepare("
            UPDATE insumos
            SET estado = 0
            WHERE id_insumo = :id
        ");

        return $stmt->execute([
            ':id' => $id
        ]);
    }
}