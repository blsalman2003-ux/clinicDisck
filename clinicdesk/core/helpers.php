<?php

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function sanitize(string $value): string
{
    return trim(strip_tags($value));
}

function formatDate(string $date): string
{
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d ? $d->format('d M Y') : e($date);
}

function formatTime(string $time): string
{
    $t = DateTime::createFromFormat('H:i:s', $time)
      ?? DateTime::createFromFormat('H:i', $time);
    return $t ? $t->format('g:i A') : e($time);
}

function statusBadge(string $status): string
{
    return match($status) {
        'pending'   => 'warning',
        'confirmed' => 'info',
        'completed' => 'success',
        'cancelled' => 'danger',
        default     => 'secondary',
    };
}

function url(string $page, string $action = '', array $extra = []): string
{
    $params = array_filter(['page' => $page, 'action' => $action] + $extra);
    return BASE_URL . '/index.php?' . http_build_query($params);
}

function old(string $field, string $default = ''): string
{
    return e($_SESSION['old_input'][$field] ?? $default);
}

function flashOldInput(array $data): void
{
    $_SESSION['old_input'] = $data;
}
