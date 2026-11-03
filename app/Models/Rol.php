<?php

namespace App\Models;

use PDO;

class Rol
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
                id_rol,
                nombre,
                descripcion,
                estado
            FROM roles
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
            FROM roles
            WHERE id_rol = :id
        ");

        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->fetch();
    }


    public function crear(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO roles
            (
                nombre,
                descripcion
            )
            VALUES
            (
                :nombre,
                :descripcion
            )
        ");

        $stmt->execute([
            ':nombre' => $data['nombre'],
            ':descripcion' => $data['descripcion'] ?? null
        ]);

        return (int) $this->db->lastInsertId();
    }


    public function eliminar(int $id): bool
    {
        $stmt = $this->db->prepare("
            UPDATE roles
            SET estado = 0
            WHERE id_rol = :id
        ");

        return $stmt->execute([
            ':id' => $id
        ]);
    }
}