<?php
namespace App\Middleware;

final class AuthMiddleware
{
    public static function handle(): void
    {
        if (empty($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'Admin') {
            header('Location: ' . BASE_URL . '/login'); 
            exit;
        }
    }
}
