<?php
/**
 * INTERLIGA - Configuración de Base de Datos
 * Cambiar estos valores según tu servidor
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'interliga_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Zona horaria Costa Rica
date_default_timezone_set('America/Costa_Rica');

// Timeout de sesión admin (30 minutos)
define('SESSION_TIMEOUT', 1800);

// Iniciar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Rate limiting basado en sesión
function rateLimit(string $key, int $maxAttempts = 5, int $windowSeconds = 300): bool {
    $now = time();
    $sessionKey = 'rl_' . $key;
    if (!isset($_SESSION[$sessionKey])) {
        $_SESSION[$sessionKey] = [];
    }
    // Limpiar intentos viejos
    $_SESSION[$sessionKey] = array_filter($_SESSION[$sessionKey], function($t) use ($now, $windowSeconds) {
        return ($now - $t) < $windowSeconds;
    });
    if (count($_SESSION[$sessionKey]) >= $maxAttempts) {
        return false; // bloqueado
    }
    $_SESSION[$sessionKey][] = $now;
    return true; // permitido
}

// Conexión PDO
function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }
    return $pdo;
}

// CSRF token
function csrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrf(string $token): bool {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Sanitizar salida
function e(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// Respuesta JSON
function jsonResponse(array $data, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

// Verificar si es admin (con timeout de sesión)
function isAdmin(): bool {
    if (empty($_SESSION['admin_id'])) return false;
    // Verificar timeout
    if (isset($_SESSION['admin_last_activity'])) {
        if (time() - $_SESSION['admin_last_activity'] > SESSION_TIMEOUT) {
            unset($_SESSION['admin_id'], $_SESSION['admin_user'], $_SESSION['admin_last_activity']);
            return false;
        }
    }
    $_SESSION['admin_last_activity'] = time();
    return true;
}

// Registrar acción en audit_log
function auditLog(PDO $db, string $accion, string $detalle = ''): void {
    try {
        $usuario = $_SESSION['admin_user'] ?? 'sistema';
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $stmt = $db->prepare("INSERT INTO audit_log (usuario, accion, detalle, ip) VALUES (?, ?, ?, ?)");
        $stmt->execute([$usuario, $accion, $detalle, $ip]);
    } catch (Exception $e) {
        // Silenciar errores de log para no interrumpir operaciones
    }
}
