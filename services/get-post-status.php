<?php
require_once __DIR__ . '/db-connection.php';
require_once __DIR__ . '/functions.php';

header('Content-Type: application/json');

try {
    FetchSecurityActions(false, false);

    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $post = GetPostById($id);
    } else {
        throw new Exception('ID не указан');
    }

    if (!$post) {
        throw new Exception('Пост не найден');
    }

    if ($post['IsSoftDeleted'] == 1) {
        throw new Exception('Пост №' . $post['IdPost'] . ' удален');
    }

    echo json_encode([
        'isPublished' => $post['IsPublished'],
        'url' => $post['RelativeUrl'],
        'title' => $post['Title']
    ]);

    LogAction(basename(__FILE__), __DIR__ . '/../logs/services.log', "SUCCESS", "Пост №" . $post['IdPost'] . " успешно получен, статус: " . ($post['IsPublished'] == 1 ? 'опубликован' : 'не опубликован'));
} catch (Exception $e) {
    LogAction(basename(__FILE__), __DIR__ . '/../logs/services.log', "ERROR", $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

