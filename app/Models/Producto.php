<?php

namespace App\Models;

use PDO;

class Producto
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
                p.id_producto,
                p.id_categoria,
                c.nombre AS categoria,
                p.nombre,
                p.descripcion,
                p.precio,
                p.imagen,
                p.disponible,
                p.estado,
                p.fecha_creacion
            FROM productos p
            INNER JOIN categorias c
                ON p.id_categoria = c.id_categoria
            WHERE p.estado = 1
            ORDER BY p.nombre
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }


    public function obtenerPorId(int $id): array|false
    {
        $sql = "
            SELECT
                p.id_producto,
                p.id_categoria,
                c.nombre AS categoria,
                p.nombre,
                p.descripcion,
                p.precio,
                p.imagen,
                p.disponible,
                p.estado
            FROM productos p
            INNER JOIN categorias c
                ON p.id_categoria = c.id_categoria
            WHERE p.id_producto = :id
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
            INSERT INTO productos
            (
                id_categoria,
                nombre,
                descripcion,
                precio,
                imagen,
                disponible
            )
            VALUES
            (
                :id_categoria,
                :nombre,
                :descripcion,
                :precio,
                :imagen,
                :disponible
            )
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':id_categoria' => $data['id_categoria'],
            ':nombre' => $data['nombre'],
            ':descripcion' => $data['descripcion'] ?? null,
            ':precio' => $data['precio'],
            ':imagen' => $data['imagen'] ?? null,
            ':disponible' => $data['disponible'] ?? 1
        ]);

        return (int) $this->db->lastInsertId();
    }


    public function actualizar(int $id, array $data): bool
    {
        $sql = "
            UPDATE productos
            SET
                id_categoria = :id_categoria,
                nombre = :nombre,
                descripcion = :descripcion,
                precio = :precio,
                imagen = :imagen,
                disponible = :disponible
            WHERE id_producto = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id_categoria' => $data['id_categoria'],
            ':nombre' => $data['nombre'],
            ':descripcion' => $data['descripcion'] ?? null,
            ':precio' => $data['precio'],
            ':imagen' => $data['imagen'] ?? null,
            ':disponible' => $data['disponible'] ?? 1,
            ':id' => $id
        ]);
    }


    public function eliminar(int $id): bool
    {
        $sql = "
            UPDATE productos
            SET estado = 0
            WHERE id_producto = :id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id' => $id
        ]);
    }
}