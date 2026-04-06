<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function requireAdminAuth(string $loginPath = 'login.php'): void
{
    if (empty($_SESSION['admin_id'])) {
        header('Location: ' . $loginPath);
        exit;
    }
}

function adminIsAuthenticated(): bool
{
    return !empty($_SESSION['admin_id']);
}

function regenerateSessionAfterLogin(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    session_regenerate_id(true);
}

function getCsrfToken(): string
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return (string) $_SESSION['csrf_token'];
}

function validateCsrfToken(string $token): bool
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    if (empty($_SESSION['csrf_token'])) {
        return false;
    }

    return hash_equals((string) $_SESSION['csrf_token'], $token);
}
