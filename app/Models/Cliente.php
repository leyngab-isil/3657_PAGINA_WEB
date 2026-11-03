<?php

namespace App\Models;

use PDO;

class Cliente
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
                id_cliente,
                nombres,
                apellidos,
                documento,
                telefono,
                email,
                estado,
                fecha_registro
            FROM clientes
            WHERE estado = 1
            ORDER BY nombres, apellidos
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }


    public function obtenerPorId(int $id): array|false
    {
        $sql = "
            SELECT *
            FROM clientes
            WHERE id_cliente = :id
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
            INSERT INTO clientes
            (
                nombres,
                apellidos,
                documento,
                telefono,
                email
            )
            VALUES
            (
                :nombres,
                :apellidos,
                :documento,
                :telefono,
                :email
            )
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':nombres' => $data['nombres'],
            ':apellidos' => $data['apellidos'] ?? null,
            ':documento' => $data['documento'] ?? null,
            ':telefono' => $data['telefono'] ?? null,
            ':email' => $data['email'] ?? null
        ]);

        return (int) $this->db->lastInsertId();
    }


    public function actualizar(int $id, array $data): bool
    {
        $sql = "
            UPDATE clientes
            SET
                nombres = :nombres,
                apellidos = :apellidos,
                documento = :documento,
                telefono = :telefono,
                email = :email
            WHERE id_cliente = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':nombres' => $data['nombres'],
            ':apellidos' => $data['apellidos'] ?? null,
            ':documento' => $data['documento'] ?? null,
            ':telefono' => $data['telefono'] ?? null,
            ':email' => $data['email'] ?? null,
            ':id' => $id
        ]);
    }


    public function eliminar(int $id): bool
    {
        $sql = "
            UPDATE clientes
            SET estado = 0
            WHERE id_cliente = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id' => $id
        ]);
    }
}