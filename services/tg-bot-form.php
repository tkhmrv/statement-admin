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
            // Проверяем доступность чата
            $response = sendTelegramMessage($botToken, $id, "✅ Тестовое сообщение для проверки связи с чатом #$i");

            // LogAction(
            //     basename(__FILE__),
            //     __DIR__ . '/../logs/telegram.log',
            //     "DEBUG",
            //     "Response for chatId: " . $id . " http_code: " . $response['http_code']
            // );

            if ($response['ok']) {
                $validChats[] = [
                    'name' => $name,
                    'id' => $id
                ];
                LogAction(
                    basename(__FILE__),
                    __DIR__ . '/../logs/telegram.log',
                    "SUCCESS",
                    "Сообщение успешно отправлено в чат #$i"
                );
            } else {
                // LogAction(
                //     basename(__FILE__),
                //     __DIR__ . '/../logs/telegram.log',
                //     "DEBUG",
                //     "Response for chatId: " . $id . " error_code: " . $response['error_code'] . " http_code: " . $response['http_code']
                // );

                $errorCode = $response['error_code'] ?? $response['http_code'] ?? 0;
                $errorDescription = $response['description'] ?? 'Unknown error';

                // Проверяем существование чата в БД
                $stmt = $conn->prepare("SELECT IdTgChat, ErrorCount FROM TgChats WHERE ChatId = ?");
                $stmt->execute([$id]);
                $chatData = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($chatData) {
                    // Инкрементируем счетчик ошибок
                    $conn->prepare("UPDATE TgChats SET ErrorCount = ErrorCount + 1 WHERE IdTgChat = ?")->execute([$chatData['IdTgChat']]);

                    LogAction(
                        basename(__FILE__),
                        __DIR__ . '/../logs/telegram.log',
                        "ERROR",
                        "⚠️ Ошибка $errorCode при отправке в чат {$id} ({$name}): $errorDescription. Увеличен ErrorCount."
                    );

                    // Проверяем количество ошибок
                    if ($chatData['ErrorCount'] + 1 > 3) {
                        // Удаляем чат из БД
                        $conn->prepare("DELETE FROM TgChats WHERE IdTgChat = ?")->execute([$chatData['IdTgChat']]);
                        LogAction(
                            basename(__FILE__),
                            __DIR__ . '/../logs/telegram.log',
                            "ERROR",
                            "❌ Чат {$id} ({$name}) удалён из базы после более 3 ошибок."
                        );
                    }
                }

                $failedChats[] = [
                    'name' => $name,
                    'id' => $id,
                    'error_code' => $errorCode,
                    'error_description' => $errorDescription
                ];
            }
        } elseif ($name !== '' || $id !== '') {
            $failedChats[] = [
                'name' => $name,
                'id' => $id,
                'error_code' => 'validation'
            ];
            LogAction(
                basename(__FILE__),
                __DIR__ . '/../logs/telegram.log',
                "ERROR",
                "Валидация не пройдена для чата #$i: name='$name', id='$id'"
            );
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
        } else {
            // Добавляем новый
            $conn->query("INSERT INTO TgChats (ChatId, ChatTitle, ChatIdAddedAt, LastMessageSuccessAt, ErrorCount) VALUES ('$chatId', '$chatName', NOW(), NOW(), 0)");
            $chatDbId = $conn->lastInsertId();
            LogAction(
                basename(__FILE__),
                __DIR__ . '/../logs/telegram.log',
                "SUCCESS",
                "Добавлен новый чат #$i: $chatId"
            );
        }

        // Получаем актуальное имя чата из Telegram
        $getChatUrl = "https://api.telegram.org/bot$botToken/getChat?chat_id=$chatId";
        $chatInfo = @file_get_contents($getChatUrl);
        $chatInfoData = json_decode($chatInfo, true);
        $actualTitle = $chatInfoData['result']['title'] ?? null;
        if ($actualTitle && $actualTitle !== $chatTitle && $chatDbId) {
            $stmt = $conn->prepare("UPDATE TgChats SET ChatTitle = ? WHERE IdTgChat = ?");
            $stmt->execute([$actualTitle, $chatDbId]);
            $chatTitle = $actualTitle;
            LogAction(
                basename(__FILE__),
                __DIR__ . '/../logs/telegram.log',
                "SUCCESS",
                "Обновлено имя чата #$i: $chatId"
            );
        }

        $totalCount++;

        // Отправляем сообщение об успешном обновлении
        $response = sendTelegramMessage($botToken, $chatId, "✅ Настройки успешно обновлены. Номер чата в списке: $totalCount/$maxChats");

        if ($response['ok']) {
            // Успешная отправка
            $updateSuccess = $conn->prepare("UPDATE TgChats SET LastMessageSuccessAt = NOW(), ErrorCount = 0 WHERE IdTgChat = ?");
            $updateSuccess->execute([$chatDbId]);
            LogAction(
                basename(__FILE__),
                __DIR__ . '/../logs/telegram.log',
                "SUCCESS",
                "✅ Успешно отправлено в чат {$chatId} ({$chatTitle})"
            );
        }
    }

    if (!empty($failedChats)) {
        $errorMessages = array_map(
            fn($c) => "{$c['name']} ({$c['id']})" .
            (isset($c['error_description']) ? ": {$c['error_description']}" : ""),
            $failedChats
        );
        LogAction(
            basename(__FILE__),
            __DIR__ . '/../logs/telegram.log',
            "ERROR",
            "Ошибка у чатов: " . implode(', ', $errorMessages) . ". Успешно: $totalCount/$maxChats"
        );
        throw new Exception("❌ Ошибка у чатов: " . implode(', ', $errorMessages) . ". Успешно: $totalCount/$maxChats");
    } else {
        LogAction(
            basename(__FILE__),
            __DIR__ . '/../logs/telegram.log',
            "SUCCESS",
            "Все чаты успешно сохранены: $totalCount/$maxChats"
        );
        echo json_encode([
            'success' => true,
            'message' => "Все чаты успешно сохранены: $totalCount/$maxChats"
        ]);
    }

    LogAction(
        basename(__FILE__),
        __DIR__ . '/../logs/telegram.log',
        "SUCCESS",
        "Все чаты успешно сохранены: $totalCount/$maxChats"
    );
    exit();
} catch (Exception $e) {
    LogAction(basename(__FILE__), __DIR__ . '/../logs/telegram.log', "ERROR", $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}