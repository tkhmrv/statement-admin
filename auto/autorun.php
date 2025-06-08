<?php
// Проверка, что скрипт запущен через CLI
if (php_sapi_name() !== 'cli') {
    LogAction(basename(__FILE__), __DIR__ . '/../logs/cron.log', "DANGER", 'Скрипт запущен не через CLI');
}

// Проверка, что скрипт запущен через cron
if (!isset($_SERVER['CRON_JOB'])) {
    LogAction(basename(__FILE__), __DIR__ . '/../logs/cron.log', "DANGER", 'Скрипт запущен не через cron');
}

require_once __DIR__ . '/../services/db-connection.php';
require_once __DIR__ . '/../services/functions.php';
$config = include __DIR__ . '/../services/config.php';

// Валидация конфигурационных путей
$main_site_posts_path = realpath($config['main_site_posts_path']);
$main_site_images_path = realpath($config['main_site_images_path']);

if (!$main_site_posts_path || !$main_site_images_path) {
    LogAction(basename(__FILE__), __DIR__ . '/../logs/cron.log', "ERROR", "Неверные конфигурационные пути");
    exit(1);
}

try {
    // Начинаем транзакцию
    $conn->beginTransaction();

    // 1. Найти все посты, помеченные как удалённые более месяца назад
    $monthAgo = date('Y-m-d H:i:s', strtotime('-1 month'));
    $sql = 'SELECT IdPost, RelativeUrl, MediaFolderName FROM Posts WHERE IsSoftDeleted = 1 AND SoftDeletedAt < ?';
    $stmt = $conn->prepare($sql);
    $stmt->execute([$monthAgo]);
    $postsToDelete = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($postsToDelete) == 0) {
        LogAction(basename(__FILE__), __DIR__ . '/../logs/cron.log', "INFO", 'Нет удаляемых постов');
        $conn->commit();
        exit(0);
    }

    foreach ($postsToDelete as $post) {
        try {
            // Валидация данных поста
            if (empty($post['IdPost']) || empty($post['RelativeUrl']) || empty($post['MediaFolderName'])) {
                throw new Exception("Неверные данные поста: " . json_encode($post['IdPost'] . ' ' . $post['RelativeUrl'] . ' ' . $post['MediaFolderName']));
            }

            LogAction(basename(__FILE__), __DIR__ . '/../logs/cron.log', "INFO", 'Удаление поста IdPost=' . $post['IdPost'] . ' (RelativeUrl=' . $post['RelativeUrl'] . ')\n');

            // 2. Удалить папку с фотографиями
            $mediaFolder = rtrim($main_site_images_path, '/') . '/' . basename($post['MediaFolderName']);

            // Проверка, что путь находится в разрешенной директории
            if (strpos(realpath($mediaFolder), $main_site_images_path) !== 0) {
                throw new Exception("Попытка несанкционированного доступа к директории: " . $mediaFolder);
            }

            if (is_dir($mediaFolder)) {
                $files = glob($mediaFolder . '/*');
                foreach ($files as $file) {
                    if (is_file($file) && strpos(realpath($file), $main_site_images_path) === 0) {
                        if (@unlink($file)) {
                            LogAction(basename(__FILE__), __DIR__ . '/../logs/cron.log', "INFO", 'Удалён файл: ' . $file);
                        } else {
                            throw new Exception('Ошибка удаления файла: ' . $file);
                        }
                    }
                }
                if (@rmdir($mediaFolder)) {
                    LogAction(basename(__FILE__), __DIR__ . '/../logs/cron.log', "INFO", 'Удалена папка: ' . $mediaFolder);
                } else {
                    throw new Exception('Ошибка удаления папки: ' . $mediaFolder);
                }
            }

            // 3. Удалить PHP-файл поста
            $phpFile = rtrim($main_site_posts_path, '/') . '/' . basename($post['RelativeUrl']) . '.php';

            // Проверка, что путь находится в разрешенной директории
            if (strpos(realpath($phpFile), $main_site_posts_path) !== 0) {
                throw new Exception("Попытка несанкционированного доступа к директории: " . $phpFile);
            }

            if (file_exists($phpFile)) {
                if (@unlink($phpFile)) {
                    LogAction(basename(__FILE__), __DIR__ . '/../logs/cron.log', "INFO", 'Удалён PHP-файл: ' . $phpFile);
                } else {
                    throw new Exception('(Default behavior) PHP-файл был удалён ранее: ' . $phpFile);
                }
            }

            // 4. Удалить запись из базы данных
            $delStmt = $conn->prepare('DELETE FROM Posts WHERE IdPost = ?');
            if (!$delStmt->execute([$post['IdPost']])) {
                throw new Exception('Ошибка удаления записи из БД');
            }
            LogAction(basename(__FILE__), __DIR__ . '/../logs/cron.log', "INFO", 'Запись удалена из БД');

        } catch (Exception $e) {
            LogAction(basename(__FILE__), __DIR__ . '/../logs/cron.log', "ERROR", $e->getMessage());
            // Продолжаем с следующим постом
            continue;
        }
    }

    // Если все прошло успешно, фиксируем транзакцию
    $conn->commit();
    LogAction(basename(__FILE__), __DIR__ . '/../logs/cron.log', "INFO", "Скрипт завершен успешно");

} catch (Exception $e) {
    // В случае ошибки откатываем транзакцию
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    LogAction(basename(__FILE__), __DIR__ . '/../logs/cron.log', "ERROR", $e->getMessage());
    exit(1);
}