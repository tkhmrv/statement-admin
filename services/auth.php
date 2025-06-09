<?php
require_once __DIR__ . '/db-connection.php';
require_once __DIR__ . '/functions.php';

header('Content-Type: application/json');

try {
    // Проверка запроса
    if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
        throw new Exception('Неверный тип запроса!');
    }

    // Начало сессии
    SessionStartWithCheck();

    // Ограничение количества запросов: по пользователю, если авторизован, иначе по IP
    $rateKey = 'ip_' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
    if (!RateLimit($rateKey, 10, 60)) {
        LogAction(basename(__FILE__), __DIR__ . '/../logs/services.log', "ERROR", "Количество запросов превышено для $rateKey");
        throw new Exception('Слишком много запросов, попробуйте позже.');
    }

    // Данные формы
    $honeypot = trim($_POST['email'] ?? '');
    $pass = $_POST['pass'] ?? '';
    $page_id = $_POST['page_id'] ?? '';
    $timestamp = $_POST['ts'] ?? '';
    $turnstile = $_POST['cf-turnstile-response'] ?? '';

    // Минимальная длина пароля
    if (mb_strlen($pass) < 6) {
        throw new Exception('Пароль слишком короткий!');
    }

    // Проверка honeypot
    if (!empty($honeypot)) {
        throw new Exception('Обнаружен бот!');
    }

    // Проверка соли
    if ($page_id !== $_ENV['PAGE_ID']) {
        throw new Exception('Неверная соль!');
    }

    // Проверка времени (5 минут)
    if (abs(time() - (int) $timestamp) > 300) {
        throw new Exception('Форма устарела. Перезагрузите страницу.');
    }

    // Проверка Turnstile
    function verifyTurnstile($token)
    {
        $secretKey = $_ENV['TURNSTILE_SECRET_KEY'];
        $url = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

        $data = [
            'secret' => $secretKey,
            'response' => $token,
            'remoteip' => $_SERVER['REMOTE_ADDR']
        ];

        $options = [
            'http' => [
                'method' => 'POST',
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'content' => http_build_query($data)
            ]
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        return json_decode($result)->success ?? false;
    }

    if (empty($turnstile) || !verifyTurnstile($turnstile)) {
        throw new Exception('Проверка Cloudflare Turnstile не пройдена!');
    }

    // Проверка пароля
    if (empty($pass)) {
        throw new Exception('Пароль не может быть пустым!');
    }

    // Получение всех пользователей
    $query = $conn->query("SELECT * FROM Users");
    $matchedUser = null;

    while ($user = $query->fetch(PDO::FETCH_ASSOC)) {
        if (password_verify($pass, $user['Password'])) {
            $matchedUser = $user;
            break;
        }
    }

    if (!$matchedUser) {
        throw new Exception('Неверный пароль!');
    }

    // Генерация безопасного токена
    $token = bin2hex(random_bytes(32));
    $tokenHash = hash('sha256', $token);

    // Обновление пользователя в БД
    $updateStmt = $conn->prepare("UPDATE Users SET Token = ?, LastLoginAt = NOW() WHERE IdUser = ?");
    $updateStmt->execute([$tokenHash, $user['IdUser']]);

    session_regenerate_id(true);

    // Установка токена в куки и сессию
    $_SESSION['id_user'] = $user['IdUser'];
    $_SESSION['token'] = $token;

    // Установка токена в куки на 3 дня
    setcookie('id_user', $user['IdUser'], [
        'expires' => time() + 3600 * 24 * 3,
        'path' => '/',
        'domain' => $_ENV['DOMAIN'],
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
    setcookie('token', $token, [
        'expires' => time() + 3600 * 24 * 3,
        'path' => '/',
        'domain' => $_ENV['DOMAIN'],
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Strict'
    ]);

    // Успешный ответ
    echo json_encode(['success' => true, 'message' => 'Вход выполнен успешно!']);
    LogAction(basename(__FILE__), __DIR__ . '/../logs/services.log', "SUCCESS", "Вход выполнен успешно! IP: " . ($_SERVER['REMOTE_ADDR'] ?? ''));
} catch (Exception $e) {
    LogAction(basename(__FILE__), __DIR__ . '/../logs/services.log', "ERROR", $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}