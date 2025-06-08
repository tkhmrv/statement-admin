<?php
require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

SessionStartWithCheck();

if (!IsAuthenticated()) {
    header('Location: /');
    exit();
}

$user = GetCurrentUser();

if (!$user) {
    NullifySessionAndExit();
}

if (empty($_SESSION['csrf_token']) || empty($_SESSION['csrf_token_time']) || (time() - $_SESSION['csrf_token_time'] > 600)) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_SESSION['csrf_token_time'] = time();
    setcookie('csrf_token', $_SESSION['csrf_token'], [
        'expires' => time() + 600,
        'path' => '/',
        'domain' => $_ENV['DOMAIN'],
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
}