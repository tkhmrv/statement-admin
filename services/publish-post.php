<?php
require_once __DIR__ . '/db-connection.php';
require_once __DIR__ . '/functions.php';
$config = include __DIR__ . '/config.php';

$main_site_posts_path = $config['main_site_posts_path'];
$main_site_images_url = $config['main_site_images_url'];
$main_site_posts_url = $config['main_site_posts_url'];

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

    if ($post['IsPublished'] == 1) {
        throw new Exception('Пост №' . $post['IdPost'] . ' уже опубликован!');
    }

    $now = date('Y-m-d H:i:s');

    $sql = 'UPDATE Posts SET IsPublished = ?, PublishedAt = ? WHERE IdPost = ?';
    $conn->prepare($sql)->execute([1, $now, $post['IdPost']]);

    $mediaFolderName = $post['MediaFolderName'];
    $templatePath = __DIR__ . '/../templates/post-template.php';
    if (!file_exists($templatePath))
        throw new Exception('Шаблон поста №' . $post['IdPost'] . ' не найден!');
    ob_start();
    $browserTitle = $post['BrowserTitle'];
    $description = $post['Description'];
    $canonicalUrlTemp = $post['CanonicalUrl'];
    $publishedAt = $now;
    $title = $post['Title'];
    $subtitle = $post['Subtitle'];
    $article = $post['Article'];
    $conclusion = $post['Conclusion'];
    $firstImageName = $post['FirstImageName'];
    $secondImageName = $post['SecondImageName'];
    $thirdImageName = $post['ThirdImageName'];
    $fourthImageName = $post['FourthImageName'];
    include $templatePath;
    $phpContent = ob_get_clean();
    $targetPhp = rtrim($main_site_posts_path, '/') . '/' . $post['RelativeUrl'] . '.php';
    if (file_put_contents($targetPhp, $phpContent) === false) {
        throw new Exception('Не удалось сохранить файл поста №' . $post['IdPost'] . '!');
    }
    LogAction(basename(__FILE__), __DIR__ . '/../logs/services.log', "SUCCESS", "Пост №{$post['IdPost']} успешно опубликован!");
    echo json_encode(['success' => true, 'message' => 'Пост №' . $post['IdPost'] . ' успешно опубликован!', 'url' => $main_site_posts_url . $post['RelativeUrl']]);
} catch (Exception $e) {
    LogAction(basename(__FILE__), __DIR__ . '/../logs/services.log', "ERROR", $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}





