<?php
require_once __DIR__ . '/../models/UserModel.php';

$action = $_GET['action'] ?? 'login';

match ($action) {
    'login'  => handleLogin(),
    'logout' => handleLogout(),
    default  => redirect(url('auth', 'login')),
};

function handleLogin(): void
{

    if (Auth::check()) {
        redirect(url('dashboard'));
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        processLogin();
    } else {

        require_once __DIR__ . '/../views/auth/login.php';
    }
}

function processLogin(): void
{

    CSRF::verify($_POST['csrf_token'] ?? '');

    $email    = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';

    flashOldInput(['email' => $email]);

    $userModel = new UserModel();
    $user      = $userModel->findByEmail($email);

    $fail = fn() => Auth::flash('danger', 'Invalid credentials. Please try again.');

    if (!$user) {
        $fail();
        redirect(url('auth', 'login'));
    }

    if ((int) $user['is_active'] !== 1) {
        Auth::flash('warning', 'Your account has been suspended. Please contact the administrator.');
        redirect(url('auth', 'login'));
    }

    if (!password_verify($password, $user['password'])) {
        $fail();
        redirect(url('auth', 'login'));
    }

    Auth::login($user);
    unset($_SESSION['old_input']);

    if ((int) ($user['first_login'] ?? 0) === 1) {
        redirect(url('users', 'change_password'));
    }

    redirect(url('dashboard'));
}

function handleLogout(): void
{

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect(url('dashboard'));
    }

    CSRF::verify($_POST['csrf_token'] ?? '');
    Auth::logout();
}
