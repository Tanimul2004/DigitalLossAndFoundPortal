<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('Asia/Dhaka');

const APP_NAME = 'Lost & Found Nexus';
const DB_HOST = 'localhost';
const DB_NAME = 'lost_found_nexus';
const DB_USER = 'root';
const DB_PASS = '';

$documentRoot = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: '');
$projectRoot = str_replace('\\', '/', realpath(dirname(__DIR__)) ?: dirname(__DIR__));
$baseUrl = '/';

if ($documentRoot !== '' && str_starts_with($projectRoot, $documentRoot)) {
    $relativePath = trim(substr($projectRoot, strlen($documentRoot)), '/');
    $baseUrl = '/' . ($relativePath !== '' ? $relativePath . '/' : '');
}

define('BASE_URL', $baseUrl);
define('ROOT_PATH', rtrim($projectRoot, '/') . '/');
define('UPLOAD_PATH', ROOT_PATH . 'assets/uploads/');

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (Throwable $e) {
    die('Database connection failed: ' . $e->getMessage());
}
