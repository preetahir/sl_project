<?php
namespace Libris\Services;

class Auth {
    public static function startSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function getCsrfToken(): string {
        self::startSession();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function csrfField(): string {
        $token = self::getCsrfToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    public static function validateCsrf(?string $token): bool {
        self::startSession();
        if (empty($token) || empty($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function setFlash(string $type, string $message): void {
        self::startSession();
        $_SESSION['flash'] = [
            'type' => $type, // 'success', 'danger', 'warning', 'info'
            'message' => $message
        ];
    }

    public static function getFlash(): ?array {
        self::startSession();
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }

    public static function isAdmin(): bool {
        self::startSession();
        return !empty($_SESSION['is_admin']);
    }

    public static function loginAsAdmin(): void {
        self::startSession();
        $_SESSION['is_admin'] = true;
        $_SESSION['admin_user'] = 'Administrator';
    }

    public static function logout(): void {
        self::startSession();
        unset($_SESSION['is_admin']);
        unset($_SESSION['admin_user']);
    }
}

