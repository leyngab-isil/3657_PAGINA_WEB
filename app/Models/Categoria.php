<?php

namespace App\Models;

use PDO;

class Categoria
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
                id_categoria,
                nombre,
                descripcion,
                estado
            FROM categorias
            ORDER BY nombre
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function obtenerPorId(int $id): array|false
    {
        $sql = "
            SELECT
                id_categoria,
                nombre,
                descripcion,
                estado
            FROM categorias
            WHERE id_categoria = :id
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->fetch();
    }

    public function crear(array $data): bool
    {
        $sql = "
            INSERT INTO categorias
            (
                nombre,
                descripcion,
                estado
            )
            VALUES
            (
                :nombre,
                :descripcion,
                1
            )
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':nombre' => $data['nombre'],
            ':descripcion' => $data['descripcion'] ?? null
        ]);
    }

    public function actualizar(
        int $id,
        array $data
    ): bool {

        $sql = "
            UPDATE categorias
            SET
                nombre = :nombre,
                descripcion = :descripcion
            WHERE id_categoria = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':nombre' => $data['nombre'],
            ':descripcion' => $data['descripcion'] ?? null,
            ':id' => $id
        ]);
    }

    public function eliminar(int $id): bool
    {
        $sql = "
            UPDATE categorias
            SET estado = 0
            WHERE id_categoria = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id' => $id
        ]);
    }
}