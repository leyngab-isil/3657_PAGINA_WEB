<?php

namespace App\Models;

use PDO;

class MovimientoInventario
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }


    /**
     * Registrar un movimiento de inventario.
     */
    public function crear(array $data): int
    {
        $sql = "
            INSERT INTO movimientos_inventario
            (
                id_insumo,
                tipo,
                cantidad,
                motivo,
                id_usuario
            )
            VALUES
            (
                :id_insumo,
                :tipo,
                :cantidad,
                :motivo,
                :id_usuario
            )
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':id_insumo' => $data['id_insumo'],
            ':tipo'      => $data['tipo'],
            ':cantidad'  => $data['cantidad'],
            ':motivo'    => $data['motivo'] ?? null,
            ':id_usuario'=> $data['id_usuario']
        ]);

        return (int) $this->db->lastInsertId();
    }


    /**
     * Obtener todos los movimientos.
     */
    public function obtenerTodos(): array
    {
        $sql = "
            SELECT
                m.id_movimiento,
                m.id_insumo,
                i.nombre AS insumo,
                i.unidad_medida,
                m.tipo,
                m.cantidad,
                m.motivo,
                m.fecha_movimiento,
                m.id_usuario,

                CONCAT(
                    u.nombres,
                    ' ',
                    u.apellidos
                ) AS usuario

            FROM movimientos_inventario m

            INNER JOIN insumos i
                ON m.id_insumo = i.id_insumo

            INNER JOIN usuarios u
                ON m.id_usuario = u.id_usuario

            ORDER BY m.fecha_movimiento DESC
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll();
    }


    /**
     * Obtener movimientos de un insumo.
     */
    public function obtenerPorInsumo(int $idInsumo): array {

        $sql = "
            SELECT
                id_movimiento,
                tipo,
                cantidad,
                motivo,
                fecha_movimiento,
                id_usuario
            FROM movimientos_inventario
            WHERE id_insumo = :id_insumo
            ORDER BY fecha_movimiento DESC
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':id_insumo' => $idInsumo
        ]);

        return $stmt->fetchAll();
    }
}