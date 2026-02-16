<?php
// includes/config.php
declare(strict_types=1);

session_start();
ob_start();

// ===== DB CONFIG =====
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'lost_found_nexus');

// ===== APP CONFIG =====
// Auto-detect BASE_URL (project root) so links/assets work from ANY sub-folder.
// Fixes issues like BASE_URL becoming "/lost-found-nexus/items/" when you open pages inside /items.
if (!defined('BASE_URL')) {
  $https  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
  $scheme = $https ? 'https' : 'http';
  $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';

  // Script path examples:
  //  - /lost-found-nexus/public/index.php
  //  - /lost-found-nexus/items/search.php
  //  - /lost-found-nexus/admin/index.php
  $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
  $basePath  = rtrim($scriptDir, '/');

  // Remove known app subfolders to always land at the project root.
  // (Add more here if you create new top-level folders later.)
  $basePath = preg_replace('#/(public|items|admin|claims|profile)$#', '', $basePath);
  $basePath = rtrim($basePath, '/');
  if ($basePath === '') $basePath = '/';

  define('BASE_URL', $scheme . '://' . $host . ($basePath === '/' ? '/' : $basePath . '/'));
}
define('UPLOAD_PATH', __DIR__ . '/../assets/uploads/');

// Security
define('APP_NAME', 'Lost & Found Nexus');
define('CSRF_KEY', 'csrf_token');

// PDO connection
try {
  $pdo = new PDO(
    "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
    DB_USER,
    DB_PASS,
    [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES => false,
    ]
  );
} catch (PDOException $e) {
  http_response_code(500);
  die("Database connection failed.");
}

// Helpers
function isLoggedIn(): bool {
  return isset($_SESSION['user_id']);
}

function isAdmin(): bool {
  return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function redirect(string $path): void {
  header('Location: ' . BASE_URL . ltrim($path, '/'));
  exit;
}

function e(string $value): string {
  return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function sanitize(string $value): string {
  return trim($value);
}

function csrf_token(): string {
  if (empty($_SESSION[CSRF_KEY])) {
    $_SESSION[CSRF_KEY] = bin2hex(random_bytes(32));
  }
  return $_SESSION[CSRF_KEY];
}

function csrf_validate(?string $token): bool {
  return isset($_SESSION[CSRF_KEY]) && is_string($token) && hash_equals($_SESSION[CSRF_KEY], $token);
}

function flash_set(string $type, string $message): void {
  $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function flash_get(): ?array {
  if (!isset($_SESSION['flash'])) return null;
  $f = $_SESSION['flash'];
  unset($_SESSION['flash']);
  return $f;
}

// Upload helper (images only)
function upload_image(array $file): array {
  if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
    return ['ok' => false, 'error' => 'Upload failed'];
  }
  $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
  $mime = mime_content_type($file['tmp_name']);
  if (!isset($allowed[$mime])) {
    return ['ok' => false, 'error' => 'Only JPG, PNG, GIF, or WEBP allowed'];
  }
  if ($file['size'] > 5 * 1024 * 1024) {
    return ['ok' => false, 'error' => 'Max file size is 5MB'];
  }
  if (!is_dir(UPLOAD_PATH)) {
    @mkdir(UPLOAD_PATH, 0755, true);
  }
  $name = bin2hex(random_bytes(8)) . '_' . time() . '.' . $allowed[$mime];
  $dest = UPLOAD_PATH . $name;
  if (!move_uploaded_file($file['tmp_name'], $dest)) {
    return ['ok' => false, 'error' => 'Could not save file'];
  }
  return ['ok' => true, 'filename' => $name];
}
