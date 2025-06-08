<?php
require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

require_once __DIR__ . '/functions.php';

SessionStartWithCheck();

session_regenerate_id(true);

NullifySessionAndExit();

header('Location: /');
exit();
