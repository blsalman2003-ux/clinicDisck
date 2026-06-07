<?php

declare(strict_types=1);

ini_set('display_errors', '0');
ini_set('log_errors',     '1');
error_reporting(E_ALL);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/core/Auth.php';
require_once __DIR__ . '/core/CSRF.php';
require_once __DIR__ . '/core/Paginator.php';
require_once __DIR__ . '/core/helpers.php';

Auth::startSession();

$page   = sanitize($_GET['page']   ?? 'dashboard');
$action = sanitize($_GET['action'] ?? 'index');

$routes = [
    'auth'          => 'controllers/AuthController.php',
    'dashboard'     => 'controllers/DashboardController.php',
    'users'         => 'controllers/UserController.php',
    'doctors'       => 'controllers/DoctorController.php',
    'appointments'  => 'controllers/AppointmentController.php',
    'prescriptions' => 'controllers/PrescriptionController.php',
    'reports'       => 'controllers/ReportController.php',
];

if (array_key_exists($page, $routes)) {
    $controllerFile = __DIR__ . '/' . $routes[$page];
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
    } else {
        http_response_code(404);
        require_once __DIR__ . '/views/errors/404.php';
    }
} else {

    http_response_code(404);
    require_once __DIR__ . '/views/errors/404.php';
}
