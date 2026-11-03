<?php

namespace App\Controllers;

use App\Models\Usuario;
use Core\Request;
use Core\Response;
use PDO;

class UsuarioController
{
    private Usuario $usuario;

    public function __construct(PDO $db)
    {
        $this->usuario = new Usuario($db);
    }

    public function index(): void
    {
        Response::success(
            $this->usuario->obtenerTodos(),
            'Usuarios obtenidos correctamente'
        );
    }

    public function show(int $id): void
    {
        $usuario = $this->usuario->obtenerPorId($id);

        if (!$usuario) {
            Response::error('Usuario no encontrado', 404);
        }

        Response::success($usuario);
    }

    public function store(): void
    {
        $data = Request::body();

        if (
            empty($data['id_rol']) ||
            empty($data['nombres']) ||
            empty($data['apellidos']) ||
            empty($data['email']) ||
            empty($data['password'])
        ) {
            Response::error(
                'Todos los datos obligatorios deben ser enviados',
                422
            );
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            Response::error('Email no válido', 422);
        }

        if ($this->usuario->obtenerPorEmail($data['email'])) {
            Response::error(
                'El email ya está registrado',
                409
            );
        }

        $id = $this->usuario->crear($data);

        Response::success(
            ['id_usuario' => $id],
            'Usuario registrado correctamente',
            201
        );
    }

    public function destroy(int $id): void
    {
        if (!$this->usuario->obtenerPorId($id)) {
            Response::error('Usuario no encontrado', 404);
        }

        $this->usuario->eliminar($id);

        Response::success(
            null,
            'Usuario desactivado correctamente'
        );
    }
}