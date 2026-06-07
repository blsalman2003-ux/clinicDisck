<?php
require_once __DIR__ . '/../config/config.php';

class Auth
{

    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_name('clinicdesk_sess');
            session_start();
        }
    }

    public static function login(array $user): void
    {
        $_SESSION['user'] = [
            'id'    => (int) $user['id'],
            'name'  => $user['name'],
            'email' => $user['email'],
            'role'  => $user['role'],
        ];

        session_regenerate_id(true);
    }

    public static function logout(): void
    {
        session_unset();
        session_destroy();
        header('Location: ' . BASE_URL . '/index.php?page=auth&action=login');
        exit;
    }

    public static function check(): bool
    {
        return isset($_SESSION['user']['id']);
    }

    public static function currentUser(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function role(): string
    {
        return $_SESSION['user']['role'] ?? '';
    }

    public static function requireRole(string ...$roles): void
    {
        if (!self::check()) {

            header('Location: ' . BASE_URL . '/index.php?page=auth&action=login');
            exit;
        }

        if (!in_array(self::role(), $roles, true)) {

            http_response_code(403);
            require_once __DIR__ . '/../views/errors/403.php';
            exit;
        }
    }

    public static function flash(string $type, string $message): void
    {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }
}
