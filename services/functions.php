<?php

// Роли пользователей
const ROLE_DEVELOPER = 1;
const ROLE_ADMIN = 2;

function SessionStartWithCheck()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function NullifySessionAndExit()
{
    setcookie('id_user', '', [
        'expires' => time() - 3600,
        'path' => '/',
        'domain' => $_ENV['DOMAIN'],
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
    setcookie('token', '', [
        'expires' => time() - 3600,
        'path' => '/',
        'domain' => $_ENV['DOMAIN'],
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
    setcookie('csrf_token', '', [
        'expires' => time() - 3600,
        'path' => '/',
        'domain' => $_ENV['DOMAIN'],
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
    session_unset();
    session_destroy();
    header('Location: /');
    exit();
}

function IsAuthenticated()
{
    return isset($_SESSION['id_user']) && isset($_SESSION['token']);
}

function GetCurrentUser()
{
    global $conn;

    if (!IsAuthenticated()) {
        return null;
    }

    $id_user = $_SESSION['id_user'];
    $token = $_SESSION['token'];
    $tokenHash = hash('sha256', $token);

    $query = $conn->prepare("SELECT * FROM Users WHERE IdUser = ? AND Token = ?");
    $query->execute([$id_user, $tokenHash]);
    $user = $query->fetch();

    return $user ?: null;
}

function UserHasRole($roleId)
{
    $user = GetCurrentUser();
    return isset($user['UserRoleId']) && $user['UserRoleId'] == $roleId;
}

function RateLimit($key, $limit = 10, $window = 60)
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $now = time();
    if (!isset($_SESSION['rate_limit'])) {
        $_SESSION['rate_limit'] = [];
    }
    if (!isset($_SESSION['rate_limit'][$key])) {
        $_SESSION['rate_limit'][$key] = ['count' => 1, 'start' => $now];
        return true;
    }
    $entry = &$_SESSION['rate_limit'][$key];
    if ($now - $entry['start'] < $window) {
        $entry['count']++;
        if ($entry['count'] > $limit) {
            return false;
        }
    } else {
        $entry = ['count' => 1, 'start' => $now];
    }
    return true;
}

function FetchSecurityActions($isReturnUser = true, $isCheckCsrf = true)
{
    // Проверка запроса
    if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
        echo json_encode(['success' => false, 'message' => 'Неверный тип запроса!']);
        exit();
    }

    // Ограничение количества запросов: по пользователю, если авторизован, иначе по IP
    $rateKey = isset($_SESSION['id_user']) ? 'user_' . $_SESSION['id_user'] : 'ip_' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
    if (!RateLimit($rateKey, 10, 60)) {
        LogAction(basename(__FILE__), __DIR__ . '/../logs/services.log', "ERROR", "Количество запросов превышено для $rateKey");
        echo json_encode(['success' => false, 'message' => 'Слишком много запросов, попробуйте позже.']);
        exit();
    }

    SessionStartWithCheck();
    // Проверка роли пользователя
    if (!IsAuthenticated() || !(UserHasRole(ROLE_DEVELOPER) || UserHasRole(ROLE_ADMIN))) {
        LogAction(basename(__FILE__), __DIR__ . '/../logs/services.log', "ERROR", "Неавторизованный доступ. IP: " . ($_SERVER['REMOTE_ADDR'] ?? ''));
        echo json_encode(['success' => false, 'message' => 'Нет доступа!']);
        exit();
    }

    if ($isCheckCsrf) {
        // Проверка CSRF токена
        if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
            LogAction(basename(__FILE__), __DIR__ . '/../logs/services.log', "ERROR", "CSRF токен недействителен. Пользователь: " . ($_SESSION['id_user'] ?? 'guest'));
            echo json_encode(['success' => false, 'message' => 'CSRF токен недействителен!']);
            exit();
        }
    }


    $user = GetCurrentUser();
    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Пользователь не найден!']);
        exit();
    }

    if ($isReturnUser) {
        return $user;
    }
}

function GetPostById($id)
{
    global $conn;

    $query = $conn->prepare("SELECT * FROM Posts WHERE IdPost = ?");
    $query->execute([$id]);
    return $query->fetch(PDO::FETCH_ASSOC);
}

function sendTelegramMessage($token, $chatId, $message)
{
    $url = "https://api.telegram.org/bot$token/sendMessage";
    $postData = http_build_query([
        'chat_id' => $chatId,
        'text' => $message,
        'parse_mode' => 'HTML'
    ]);

    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/x-www-form-urlencoded",
            'content' => $postData,
            'timeout' => 10
        ]
    ]);

    $response = file_get_contents($url, false, $context);
    return json_decode($response, true) ?? ['ok' => false];
}

function LogAction($sourceFile, $logFile, $type, $message)
{
    $timestamp = date("Y-m-d H:i:s");
    $entry = sprintf("[%s] [%s] %s: %s\n", $timestamp, $sourceFile, $type, $message);
    file_put_contents($logFile, $entry, FILE_APPEND);
}