<?php

namespace App\Models;

use PDO;

class Usuario
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
                u.id_usuario,
                u.nombres,
                u.apellidos,
                u.email,
                u.estado,
                u.fecha_creacion,
                r.id_rol,
                r.nombre AS rol
            FROM usuarios u
            INNER JOIN roles r
                ON u.id_rol = r.id_rol
            WHERE u.estado = 1
            ORDER BY u.nombres
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }


    public function obtenerPorId(int $id): array|false
    {
        $sql = "
            SELECT
                u.id_usuario,
                u.nombres,
                u.apellidos,
                u.email,
                u.estado,
                r.id_rol,
                r.nombre AS rol
            FROM usuarios u
            INNER JOIN roles r
                ON u.id_rol = r.id_rol
            WHERE u.id_usuario = :id
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        return $stmt->fetch();
    }


    public function obtenerPorEmail(string $email): array|false
    {
        $sql = "
            SELECT *
            FROM usuarios
            WHERE email = :email
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':email' => $email
        ]);

        return $stmt->fetch();
    }


    public function crear(array $data): int
    {
        $sql = "
            INSERT INTO usuarios
            (
                id_rol,
                nombres,
                apellidos,
                email,
                password
            )
            VALUES
            (
                :id_rol,
                :nombres,
                :apellidos,
                :email,
                :password
            )
        ";

        $stmt = $this->db->prepare($sql);

        $passwordHash = password_hash(
            $data['password'],
            PASSWORD_DEFAULT
        );

        $stmt->execute([
            ':id_rol' => $data['id_rol'],
            ':nombres' => $data['nombres'],
            ':apellidos' => $data['apellidos'],
            ':email' => $data['email'],
            ':password' => $passwordHash
        ]);

        return (int) $this->db->lastInsertId();
    }


    public function eliminar(int $id): bool
    {
        $stmt = $this->db->prepare("
            UPDATE usuarios
            SET estado = 0
            WHERE id_usuario = :id
        ");

        return $stmt->execute([
            ':id' => $id
        ]);
    }
}