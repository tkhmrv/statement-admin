<?php
require_once __DIR__ . '/db-connection.php';
require_once __DIR__ . '/functions.php';
$config = include __DIR__ . '/config.php';

$main_site_templates_path = $config['main_site_templates_path'];

header('Content-Type: application/json');

try {
    FetchSecurityActions(false, true);

    $notificationImageTemp = $_POST['notification-image-form'];
    $notificationTextTemp = $_POST['notification-text-form'];

    // Валидация имени файла изображения
    if (!preg_match('/^[a-zA-Z0-9._-]{1,100}$/', $notificationImageTemp)) {
        throw new Exception('Недопустимое имя файла изображения!');
    }
    // Валидация текста уведомления
    if (mb_strlen($notificationTextTemp) > 512) {
        throw new Exception('Текст уведомления слишком длинный!');
    }

    $sql = "UPDATE Notifications SET ImageName = ?, Text = ? WHERE IdNotification = 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$notificationImageTemp, $notificationTextTemp]);

    $templatePath = __DIR__ . '/../templates/notification-template.php';
    if (!file_exists($templatePath))
        throw new Exception('Шаблон уведомления не найден!');
    ob_start();
    $notificationImage = '/images/notification/' . $notificationImageTemp;
    $notificationText = $notificationTextTemp;
    include $templatePath;
    $phpContent = ob_get_clean();
    $targetPhp = rtrim($main_site_templates_path, '/') . '/notification.php';
    if (file_put_contents($targetPhp, $phpContent) === false) {
        throw new Exception('Не удалось сохранить файл уведомления!');
    }

    LogAction(basename(__FILE__), __DIR__ . '/../logs/services.log', "SUCCESS", "Уведомление успешно сохранено!");
    echo json_encode(['success' => true, 'message' => 'Уведомление успешно сохранено!']);
} catch (Exception $e) {
    LogAction(basename(__FILE__), __DIR__ . '/../logs/services.log', "ERROR", $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}