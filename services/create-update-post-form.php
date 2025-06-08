<?php
require_once __DIR__ . '/db-connection.php';
require_once __DIR__ . '/functions.php';
$config = include __DIR__ . '/config.php';

$main_site_posts_path = $config['main_site_posts_path'];
$main_site_posts_url = $config['main_site_posts_url'];
$main_site_images_path = $config['main_site_images_path'];
$main_site_images_url = $config['main_site_images_url'];

header('Content-Type: application/json');

try {
    $user = FetchSecurityActions();

    // 1. Получение и фильтрация данных
    $fields = [
        'browser-title',
        'description',
        'relative-url',
        'title',
        'subtitle',
        'article',
        'conclusion',
        'IdPost'
    ];
    $data = [];
    foreach ($fields as $f) {
        $data[$f] = trim($_POST[$f] ?? '');
    }
    // Проверка обязательных полей и длины
    foreach (['browser-title' => 100, 'description' => 512, 'relative-url' => 50, 'title' => 100, 'subtitle' => 256, 'article' => 2048, 'conclusion' => 256] as $f => $maxLen) {
        if (empty($data[$f]) || mb_strlen($data[$f]) > $maxLen) {
            throw new Exception('Пустое или слишком длинное поле: ' . $f);
        }
    }
    // Строгая валидация текстовых полей (разрешены буквы латиницы, кириллицы, цифры, эмодзи, знаки препинания, ™, &nbsp;, пробелы и часто используемые символы)
    $textPattern = '/^[\p{L}\p{N}\p{Zs}\p{P}\p{S}\x{2122}\x{00A0}\x{1F300}-\x{1FAFF}\n\r\t]+$/u';
    foreach (['browser-title', 'description', 'title', 'article', 'conclusion'] as $f) {
        if (!preg_match($textPattern, $data[$f])) {
            throw new Exception('Недопустимые символы в поле: ' . $f);
        }
    }
    $relativeUrl = preg_replace('/[^a-zA-Z0-9\-_]/', '', $data['relative-url']);
    if (strlen($relativeUrl) < 3 || strlen($relativeUrl) > 50) {
        LogAction(basename(__FILE__), __DIR__ . '/../logs.log', "ERROR", "Некорректная ссылка на пост: $relativeUrl");
        throw new Exception('Некорректная ссылка на пост!');
    }
    // Проверка уникальности URL
    $stmt = $conn->prepare('SELECT IdPost FROM Posts WHERE RelativeUrl = ? AND IdPost != ?');
    $stmt->execute([$relativeUrl, $data['IdPost'] ?? 0]);
    if ($stmt->fetch()) {
        LogAction(basename(__FILE__), __DIR__ . '/../logs.log', "ERROR", "Пост с таким URL уже существует: $relativeUrl");
        throw new Exception('Пост с таким URL уже существует!' . $data['IdPost']);
    }

    // 2. Работа с файлами (4 фото)
    $imageFields = [
        'first-photo',
        'second-photo',
        'third-photo',
        'fourth-photo'
    ];
    $imageNames = [];
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/gif', 'image/heic'];
    $maxFileSize = 5 * 1024 * 1024; // 5MB
    $mediaFolderName = $relativeUrl . '_' . uniqid();
    $mediaFolderPath = $main_site_images_path . $mediaFolderName;
    if (!is_dir($mediaFolderPath)) {
        if (!mkdir($mediaFolderPath, 0775, true)) {
            LogAction(basename(__FILE__), __DIR__ . '/../logs.log', "ERROR", "Не удалось создать папку для изображений: $mediaFolderPath");
            throw new Exception('Не удалось создать папку для изображений!');
        }
    }
    $finfo = new finfo(FILEINFO_MIME_TYPE);

    // Получаем старый пост, если это обновление
    $isUpdate = !empty($data['IdPost']);
    $oldPost = null;
    if ($isUpdate) {
        $stmt = $conn->prepare('SELECT * FROM Posts WHERE IdPost = ?');
        $stmt->execute([$data['IdPost']]);
        $oldPost = $stmt->fetch();
        $postCreatedAt = $oldPost['PostCreatedAt'];
        if (!$oldPost) {
            LogAction(basename(__FILE__), __DIR__ . '/../logs.log', "ERROR", "Пост не найден для обновления: " . $data['IdPost']);
            throw new Exception('Пост не найден для обновления!');
        }
    }

    foreach ($imageFields as $idx => $field) {
        if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES[$field];
            if ($file['size'] > $maxFileSize) {
                throw new Exception('Файл слишком большой: ' . $file['name']);
            }
            $mimeType = $finfo->file($file['tmp_name']);
            if (!in_array($mimeType, $allowedTypes)) {
                throw new Exception('Недопустимый формат файла: ' . $file['name']);
            }
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            // Исключаем PHP-расширения и двойные расширения
            if (preg_match('/\.(php|phtml|phar)$/i', $file['name']) || preg_match('/\./', basename($file['name'], ".{$ext}"))) {
                throw new Exception('Недопустимое расширение файла: ' . $file['name']);
            }
            $baseName = ['first', 'second', 'third', 'fourth'][$idx] . '.' . $ext;
            $targetPath = $mediaFolderPath . '/' . $baseName;
            if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
                throw new Exception('Не удалось сохранить файл: ' . $file['name']);
            }
            $imageNames[] = $baseName;
        } else if ($oldPost) {
            // Если это обновление и файл не загружен, используем старый файл
            $oldImageField = ['FirstImageName', 'SecondImageName', 'ThirdImageName', 'FourthImageName'][$idx];
            $oldImageName = $oldPost[$oldImageField];
            if (empty($oldImageName)) {
                throw new Exception('Отсутствует старое изображение: ' . $field);
            }
            $oldImagePath = rtrim($main_site_images_path, '/') . '/' . $oldPost['MediaFolderName'] . '/' . $oldImageName;
            if (!file_exists($oldImagePath)) {
                throw new Exception('Не найден файл изображения: ' . $oldImageName);
            }
            $ext = strtolower(pathinfo($oldImageName, PATHINFO_EXTENSION));
            $baseName = ['first', 'second', 'third', 'fourth'][$idx] . '.' . $ext;
            $targetPath = $mediaFolderPath . '/' . $baseName;
            if (!copy($oldImagePath, $targetPath)) {
                throw new Exception('Не удалось скопировать файл: ' . $oldImageName);
            }
            $imageNames[] = $baseName;
        } else {
            throw new Exception('Ошибка загрузки файла: ' . $field);
        }
    }

    // 3. Определение режима (создание/обновление) и выполнение действий
    $canonicalUrl = $main_site_posts_url . $relativeUrl;
    $now = date('Y-m-d H:i:s');
    $publishedAt = $now;
    if ($isUpdate) {
        // Удалить старые картинки и папку
        $oldMediaPath = rtrim($main_site_images_path, '/') . '/' . $oldPost['MediaFolderName'];
        if (is_dir($oldMediaPath)) {
            $files = glob($oldMediaPath . '/*');
            foreach ($files as $f)
                @unlink($f);
            @rmdir($oldMediaPath);
        }
        // Удалить старый php-файл
        $oldPhp = rtrim($main_site_posts_path, '/') . '/' . $oldPost['RelativeUrl'] . '.php';
        if (file_exists($oldPhp))
            @unlink($oldPhp);
        // Обновить запись
        $sql = 'UPDATE Posts SET UpdatedBy=?, CanonicalUrl=?, RelativeUrl=?, MediaFolderName=?, PostUpdatedAt=?, IsPublished=?, PublishedAt=?, BrowserTitle=?, Description=?, Title=?, Subtitle=?, Article=?, Conclusion=?, FirstImageName=?, SecondImageName=?, ThirdImageName=?, FourthImageName=? WHERE IdPost=?';
        $params = [
            $user['IdUser'],
            $canonicalUrl,
            $relativeUrl,
            $mediaFolderName,
            $now,
            1,
            $now,
            $data['browser-title'],
            $data['description'],
            $data['title'],
            $data['subtitle'],
            $data['article'],
            $data['conclusion'],
            $imageNames[0],
            $imageNames[1],
            $imageNames[2],
            $imageNames[3],
            $data['IdPost']
        ];
        $conn->prepare($sql)->execute($params);
        $postId = $data['IdPost'];
    } else {
        // Вставить новую запись
        $sql = 'INSERT INTO Posts (CreatedBy, CanonicalUrl, RelativeUrl, MediaFolderName, PostCreatedAt, PostUpdatedAt, IsPublished, PublishedAt, BrowserTitle, Description, Title, Subtitle, Article, Conclusion, FirstImageName, SecondImageName, ThirdImageName, FourthImageName) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
        $params = [
            $user['IdUser'],
            $canonicalUrl,
            $relativeUrl,
            $mediaFolderName,
            $now,
            $now,
            1,
            $now,
            $data['browser-title'],
            $data['description'],
            $data['title'],
            $data['subtitle'],
            $data['article'],
            $data['conclusion'],
            $imageNames[0],
            $imageNames[1],
            $imageNames[2],
            $imageNames[3]
        ];
        $conn->prepare($sql)->execute($params);
        $postId = $conn->lastInsertId();
    }

    // 4. Генерация PHP-файла поста
    $mediaUrl = $main_site_images_url . $mediaFolderName . '/';
    $templatePath = __DIR__ . '/../templates/post-template.php';
    if (!file_exists($templatePath))
        throw new Exception('Шаблон поста не найден!');
    ob_start();
    $browserTitle = $data['browser-title'];
    $description = $data['description'];
    $canonicalUrlTemp = $canonicalUrl;
    $publishedAt = $postCreatedAt;
    $title = $data['title'];
    $subtitle = nl2br($data['subtitle']);
    $article = nl2br($data['article']);
    $conclusion = nl2br($data['conclusion']);
    $firstImageName = $imageNames[0];
    $secondImageName = $imageNames[1];
    $thirdImageName = $imageNames[2];
    $fourthImageName = $imageNames[3];
    include $templatePath;
    $phpContent = ob_get_clean();
    $targetPhp = rtrim($main_site_posts_path, '/') . '/' . $relativeUrl . '.php';
    if (file_put_contents($targetPhp, $phpContent) === false) {
        throw new Exception('Не удалось сохранить файл поста!');
    }

    // После успешного создания или обновления поста:
    LogAction(
        basename(__FILE__),
        __DIR__ . '/../logs/services.log',
        'SUCCESS',
        ($isUpdate ? 'Пост обновлен' : 'Пост создан') . ": ID = {$postId}, Пользователь = {$user['Name']}, URL = {$relativeUrl}"
    );
    echo json_encode(['success' => true, 'message' => 'Пост успешно сохранён!', 'url' => $main_site_posts_url . $relativeUrl]);
} catch (Exception $e) {
    // Откатить загрузку файлов, если что-то пошло не так
    if (isset($mediaFolderPath) && is_dir($mediaFolderPath)) {
        $files = glob($mediaFolderPath . '/*');
        foreach ($files as $f)
            @unlink($f);
        @rmdir($mediaFolderPath);
    }
    LogAction(basename(__FILE__), __DIR__ . '/../logs/services.log', 'ERROR', $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
