<?php

declare(strict_types=1);

namespace App\Controllers;

final class ViewController
{
    public function landing(): void
    {
        $this->view('home/index');
    }

    public function dashboard(): void
    {
        $this->view('admin/dashboard');
    }

    public function products(): void
    {
        $this->view('admin/productos');
    }

    private function view(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);

        $path = dirname(__DIR__) . '/Views/' . $view . '.php';

        if (!is_file($path)) {
            http_response_code(404);
            exit('Vista no encontrada: ' . $path);
        }

        require $path;
    }
}