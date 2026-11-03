<?php

namespace App\Models;

use PDO;

class Proveedor
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }


    public function obtenerTodos(): array
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM proveedores
            WHERE estado = 1
            ORDER BY razon_social
        ");

        $stmt->execute();

        return $stmt->fetchAll();
    }


    public function obtenerPorId(int $id): array|false
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM proveedores
            WHERE id_proveedor = :id
        ");

        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->fetch();
    }


    public function crear(array $data): int
    {
        $sql = "
            INSERT INTO proveedores
            (
                razon_social,
                documento,
                telefono,
                email,
                direccion
            )
            VALUES
            (
                :razon_social,
                :documento,
                :telefono,
                :email,
                :direccion
            )
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':razon_social' => $data['razon_social'],
            ':documento' => $data['documento'] ?? null,
            ':telefono' => $data['telefono'] ?? null,
            ':email' => $data['email'] ?? null,
            ':direccion' => $data['direccion'] ?? null
        ]);

        return (int) $this->db->lastInsertId();
    }


    public function eliminar(int $id): bool
    {
        $stmt = $this->db->prepare("
            UPDATE proveedores
            SET estado = 0
            WHERE id_proveedor = :id
        ");

        return $stmt->execute([
            ':id' => $id
        ]);
    }
}