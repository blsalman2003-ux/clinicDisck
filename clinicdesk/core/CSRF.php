<?php

class CSRF
{
    private const SESSION_KEY = 'csrf_token';

    public static function generateToken(): string
    {
        if (empty($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }
        return $_SESSION[self::SESSION_KEY];
    }

    public static function validateToken(string $submitted): bool
    {
        if (empty($_SESSION[self::SESSION_KEY])) {
            return false;
        }
        return hash_equals($_SESSION[self::SESSION_KEY], $submitted);
    }

    public static function verify(string $submitted): void
    {
        if (!self::validateToken($submitted)) {
            Auth::flash('danger', 'Invalid request. Please try again.');
            http_response_code(403);

            $back = $_SERVER['HTTP_REFERER'] ?? (BASE_URL . '/index.php');
            header('Location: ' . $back);
            exit;
        }
    }
}
