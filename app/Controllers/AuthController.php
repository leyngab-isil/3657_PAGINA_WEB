<?php
namespace App\Controllers;

use App\Models\Usuario;
use PDO;

final class AuthController
{
    private Usuario $usuario;

    public function __construct(PDO $db)
    {
        $this->usuario = new Usuario($db);
    }

    public function showLogin(): void
    {
        if (!empty($_SESSION['user'])) { 
            header('Location: ' . BASE_URL . '/admin'); 
            exit; 
        }

         // Generar token CSRF si todavía no existe
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        require dirname(__DIR__) . '/Views/auth/login.php';
    }

    public function login(): void
    {
        $this->validateCsrf();
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = (string)($_POST['password'] ?? '');
        if (!$email || $password === '') { $this->fail('Introduce un correo y una contraseña válidos.'); }

        // Sustituye la construcción según tu inyección de dependencias real.
        $user = $this->usuario->findByEmail($email);

        if (!$user || !password_verify($password, $user['password_hash']) || ($user['role'] ?? '') !== 'Admin') {
            $this->fail('Las credenciales no son correctas.');
        }

        session_regenerate_id(true);
        $_SESSION['user'] = ['id'=>$user['id'], 'name'=>$user['name'], 'email'=>$user['email'], 'role'=>$user['role']];
        header('Location: ' . BASE_URL . '/admin'); 
        exit;
    }

    public function logout(): void
    {
        $this->validateCsrf();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) { $p=session_get_cookie_params(); setcookie(session_name(),'',time()-42000,$p['path'],$p['domain'],$p['secure'],$p['httponly']); }
        session_destroy(); 
        header('Location: ' . BASE_URL); 
        exit;
    }

    private function fail(string $message): never { 
        $_SESSION['flash_error']=$message; 
        header('Location: ' . BASE_URL . '/admin'); 
        exit; 
    }
    
    private function validateCsrf(): void
    {
        $sent=(string)($_POST['csrf_token']??''); 
        $saved=(string)($_SESSION['csrf_token']??'');
        if ($saved==='' || !hash_equals($saved,$sent)) { http_response_code(419); exit('Solicitud no válida.'); }
    }
}
