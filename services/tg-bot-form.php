<?php
require_once __DIR__ . '/db-connection.php';
require_once __DIR__ . '/functions.php';

try {
    FetchSecurityActions(false, true);

    $botToken = $_ENV['BOT_TOKEN'];
    $maxChats = 5;
    $validChats = [];
    $failedChats = [];

    for ($i = 1; $i <= $maxChats; $i++) {
        $name = trim($_POST["chat-name-$i"] ?? '');
        $id = trim($_POST["chat-id-$i"] ?? '');

        // Серверная валидация
        $isValidName = ($name !== '' && mb_strlen($name) <= 255 && preg_match('/^[\p{L}\p{N} _\-]+$/u', $name));
        $isValidId = (preg_match('/^-?\d{5,20}$/', $id));
        if ($isValidName && $isValidId) {
            $response = sendTelegramMessage($botToken, $id, "✅ Тестовое сообщение для проверки связи с чатом #$i");

            if ($response['ok']) {
                $validChats[] = [
                    'name' => $name,
                    'id' => $id
                ];
                LogAction(basename(__FILE__), __DIR__ . '/../logs/telegram.log', "SUCCESS", "Сообщение успешно отправлено в чат #$i");
            } else {
                $failedChats[] = [
                    'name' => $name,
                    'id' => $id,
                    'error_code' => $response['error_code'] ?? 0
                ];
                LogAction(basename(__FILE__), __DIR__ . '/../logs/telegram.log', "ERROR", "Ошибка при отправке сообщения в чат #$i: " . ($response['description'] ?? 'Неизвестная ошибка'));
            }
        } elseif ($name !== '' || $id !== '') {
            $failedChats[] = [
                'name' => $name,
                'id' => $id,
                'error_code' => 'validation'
            ];
            LogAction(basename(__FILE__), __DIR__ . '/../logs/telegram.log', "ERROR", "Валидация не пройдена для чата #$i: name='$name', id='$id'");
        }
    }

    $successCount = count($validChats);
    $totalCount = 0;

    foreach ($validChats as $chat) {
        $chatId = htmlspecialchars($chat['id']);
        $chatName = htmlspecialchars($chat['name']);

        // Проверяем, существует ли уже чат
        $res = $conn->query("SELECT IdTgChat, ChatTitle FROM TgChats WHERE ChatId = '$chatId'");
        $chatDbId = null;
        $chatTitle = $chatName;
        if ($res && $res->rowCount() > 0) {
            $row = $res->fetch(PDO::FETCH_ASSOC);
            $chatDbId = $row['IdTgChat'];
            $chatTitle = $row['ChatTitle'];
            // Обновляем только LastMessageSuccessAt
            $conn->query("UPDATE TgChats SET LastMessageSuccessAt = NOW() WHERE ChatId = '$chatId'");
        } else {
            // Добавляем новый
            $conn->query("INSERT INTO TgChats (ChatId, ChatTitle, ChatIdAddedAt, LastMessageSuccessAt) VALUES ('$chatId', '$chatName', NOW(), NOW())");
            $chatDbId = $conn->lastInsertId();
            LogAction(basename(__FILE__), __DIR__ . '/../logs/telegram.log', "SUCCESS", "Добавлен новый чат #$i: $chatId");
        }

        // Получаем актуальное имя чата из Telegram
        $getChatUrl = "https://api.telegram.org/bot$botToken/getChat?chat_id=$chatId";
        $chatInfo = @file_get_contents($getChatUrl);
        $chatInfoData = json_decode($chatInfo, true);
        $actualTitle = $chatInfoData['result']['title'] ?? null;
        if ($actualTitle && $actualTitle !== $chatTitle && $chatDbId) {
            // Обновляем имя чата в БД через PDO
            $pdo = new PDO('mysql:host=localhost;dbname=' . $_ENV['DB_NAME'] . ';charset=utf8mb4', $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $pdo->prepare("UPDATE TgChats SET ChatTitle = ? WHERE IdTgChat = ?");
            $stmt->execute([$actualTitle, $chatDbId]);
            $chatTitle = $actualTitle;
            LogAction(basename(__FILE__), __DIR__ . '/../logs/telegram.log', "SUCCESS", "Обновлено имя чата #$i: $chatId");
        }

        $totalCount++;

        sendTelegramMessage($botToken, $chatId, "✅ Настройки успешно обновлены. Номер чата в списке: $totalCount/$maxChats");
    }

    if (!empty($failedChats)) {
        $errorMessages = array_map(fn($c) => "{$c['name']} ({$c['id']})", $failedChats);
        LogAction(basename(__FILE__), __DIR__ . '/../logs/telegram.log', "ERROR", "Ошибка у чатов: " . implode(', ', $errorMessages) . ". Успешно: $totalCount/$maxChats");
        throw new Exception("❌ Ошибка у чатов: " . implode(', ', $errorMessages) . ". Успешно: $totalCount/$maxChats");
    } else {
        LogAction(basename(__FILE__), __DIR__ . '/../logs/telegram.log', "SUCCESS", "Все чаты успешно сохранены: $totalCount/$maxChats");
        echo json_encode([
            'success' => true,
            'message' => "Все чаты успешно сохранены: $totalCount/$maxChats"
        ]);
    }

    LogAction(basename(__FILE__), __DIR__ . '/../logs/telegram.log', "SUCCESS", "Все чаты успешно сохранены: $totalCount/$maxChats");
    exit();
} catch (Exception $e) {
    LogAction(basename(__FILE__), __DIR__ . '/../logs/telegram.log', "ERROR", $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}