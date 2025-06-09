<?php
require_once __DIR__ . '/../services/functions.php';
// Проверка, что скрипт запущен через CLI
if (php_sapi_name() !== 'cli') {
    LogAction(basename(__FILE__), __DIR__ . '/../logs/cron.log', "DANGER", 'Скрипт запущен не через CLI');
    exit(1);
}

// Проверка, что скрипт запущен через cron
if (!isset($_SERVER['CRON_JOB'])) {
    LogAction(basename(__FILE__), __DIR__ . '/../logs/cron.log', "DANGER", 'Скрипт запущен не через cron');
    exit(1);
}

require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$url = $_ENV['BOT_PING_URL'];

$context = stream_context_create([
    'http' => [
        'timeout' => 10,
        'user_agent' => 'CronJob/1.0'
    ]
]);

$response = @file_get_contents($url, false, $context);
if ($response === false) {
    LogAction(basename(__FILE__), __DIR__ . '/../logs/cron.log', "ERROR", "Ошибка при выполнении запроса к $url");
    exit(1);
}

LogAction(basename(__FILE__), __DIR__ . '/../logs/cron.log', "INFO", "Pinged: $response");
exit(0);