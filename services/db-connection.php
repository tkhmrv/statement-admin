<?php
require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Настройки подключения к базе данных
$db_name = $_ENV['DB_NAME'];
$db_username = $_ENV['DB_USERNAME'];
$db_password = $_ENV['DB_PASSWORD'];

// Подключение к базе данных через PDO
try {
    $dsn = "mysql:host=localhost;dbname=$db_name;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ];
    
    $conn = new PDO($dsn, $db_username, $db_password, $options);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Возникла ошибка при подключении к базе данных: ' . $e->getMessage()]);
    exit();
}