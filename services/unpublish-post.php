<?php
require_once __DIR__ . '/db-connection.php';
require_once __DIR__ . '/functions.php';
$config = include __DIR__ . '/config.php';

$main_site_posts_path = $config['main_site_posts_path'];

header('Content-Type: application/json');

try {
    FetchSecurityActions(false, false);

    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $post = GetPostById($id);
    } else {
        throw new Exception('Не указан ID поста!');
    }

    if (!$post) {
        throw new Exception('Пост не найден!');
    }

    if ($post['IsPublished'] == 0) {
        throw new Exception('Пост №' . $post['IdPost'] . ' уже снят с публикации!');
    }

    $sql = 'UPDATE Posts SET IsPublished = ? WHERE IdPost = ?';
    $conn->prepare($sql)->execute([0, $post['IdPost']]);

    $phpFile = $main_site_posts_path . '/' . $post['RelativeUrl'] . '.php';
    if (file_exists($phpFile) && !unlink($phpFile)) {
        throw new Exception('Не удалось удалить файл поста №' . $post['IdPost'] . '!');
    }

    LogAction(basename(__FILE__), __DIR__ . '/../logs/services.log', "SUCCESS", "Пост №{$post['IdPost']} успешно снят с публикации!");
    echo json_encode(['success' => true, 'message' => 'Пост №' . $post['IdPost'] . ' успешно снят с публикации!']);
} catch (Exception $e) {
    LogAction(basename(__FILE__), __DIR__ . '/../logs/services.log', "ERROR", $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}