<?php

namespace App\Models;

use PDO;

class Mesa
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
                id_mesa,
                numero,
                capacidad,
                estado
            FROM mesas
            ORDER BY numero
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }


    public function obtenerPorId(int $id): array|false
    {
        $sql = "
            SELECT *
            FROM mesas
            WHERE id_mesa = :id
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->fetch();
    }


    public function crear(array $data): int
    {
        $sql = "
            INSERT INTO mesas
            (
                numero,
                capacidad,
                estado
            )
            VALUES
            (
                :numero,
                :capacidad,
                :estado
            )
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':numero' => $data['numero'],
            ':capacidad' => $data['capacidad'],
            ':estado' => $data['estado'] ?? 'DISPONIBLE'
        ]);

        return (int) $this->db->lastInsertId();
    }


    public function actualizar(int $id, array $data): bool
    {
        $sql = "
            UPDATE mesas
            SET
                numero = :numero,
                capacidad = :capacidad,
                estado = :estado
            WHERE id_mesa = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':numero' => $data['numero'],
            ':capacidad' => $data['capacidad'],
            ':estado' => $data['estado'],
            ':id' => $id
        ]);
    }


    public function cambiarEstado(int $id, string $estado): bool
    {
        $sql = "
            UPDATE mesas
            SET estado = :estado
            WHERE id_mesa = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':estado' => $estado,
            ':id' => $id
        ]);
    }


    public function eliminar(int $id): bool
    {
        return $this->cambiarEstado(
            $id,
            'INACTIVA'
        );
    }
}