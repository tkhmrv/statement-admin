<?php
require_once __DIR__ . '/services/db-connection.php';
require_once __DIR__ . '/services/functions.php';
require_once __DIR__ . '/services/auth-required.php';
$config = include __DIR__ . '/services/config.php';

$main_site_posts_url = $config['main_site_posts_url'];
$main_site_images_url = $config['main_site_images_url'];

if (isset($_GET['id'])) {
  $id = $_GET['id'];
  $post = GetPostById($id);
} else {
  header('Location: /manage-posts');
  exit;
}

if (!$post) {
  header('Location: /manage-posts');
  exit;
}

$isPublished = $post['IsPublished'];
?>

<!DOCTYPE html>
<html data-wf-page="675f341bf9e521989d9fac0d" lang="ru-RU">

<head>
  <!-- Default meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex, nofollow">
  <title>Управление постом №<?php echo htmlspecialchars($id); ?></title>
  <meta name="description" content="">
  <link href="/images/favicon.ico" rel="icon" type="image/x-icon">
  <link href="/images/favicon-apple.svg" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin="anonymous">
  <script src="/js/webfont.js" type="text/javascript"></script>
  <script type="text/javascript">
    WebFont.load({
      google: {
        families: ["Inter:100,200,300,regular,500,600,700,800,900,100italic,200italic,300italic,italic,500italic,600italic,700italic,800italic,900italic"]
      }
    });
  </script>

  <!-- Libs, scripts and css -->
  <link href="/css/styles.css" rel="stylesheet" type="text/css">

  <?php include 'templates/w-mod.php'; ?>

</head>

<body>

  <?php include 'templates/toast.php'; ?>
  <?php include 'templates/confirmation.php'; ?>
  <?php include 'templates/admin-navbar.php'; ?>

  <section class="section hero-product">
    <div class="w-layout-blockcontainer main-container w-container">
      <div class="w-layout-grid product-halves">
        <div class="product-info-block">
          <div class="heading-product">
            <div class="hide">
              <h1 data-w-id="33b6b5c8-11a0-df12-60a9-44daabc73a28"
                style="-webkit-transform:translate3d(0, 200%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0, 200%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0, 200%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0, 200%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0)">
                Управление</h1>
            </div>
            <div class="hide">
              <h1 data-w-id="33b6b5c8-11a0-df12-60a9-44daabc73a2b"
                style="-webkit-transform:translate3d(0, 200%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0, 200%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0, 200%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0, 200%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0)"
                class="text-dark-32">постом №<?php echo htmlspecialchars($id); ?></h1>
            </div>
          </div>
          <p class="text-big" id="post-title">
            Название поста: <?php echo htmlspecialchars($post['Title']); ?>
          </p>
          <p class="text-big" id="post-url" style="margin-top: -2vw;">
            <?php if ($isPublished == 1) {
              echo 'Ссылка на пост: <a href="' . htmlspecialchars($main_site_posts_url . $post['RelativeUrl']) . '" class="text-underline" target="_blank">' . htmlspecialchars($main_site_posts_url . $post['RelativeUrl']) . '</a>';
            }
            ?>
          </p>
          <p class="text-big" id="post-status" style="margin-top: -2vw;">
            Статус:
            <?php switch ($isPublished) {
              case 1:
                echo 'пост опубликован';
                break;
              case 0:
                echo 'пост не опубликован';
                break;
            }
            ?>
          </p>

          <div id="publish-post-div" class="w-commerce-commerceaddtocartform default-state" style="<?php if ($isPublished == 1) {
            echo 'display: none;';
          } ?>">
            <a id="publish-post-btn" data-id="<?php echo htmlspecialchars($id); ?>" style="cursor: pointer;"
              class="w-commerce-commerceaddtocartbutton cta-main">
              Опубликовать
            </a>
          </div>

          <div id="unpublish-post-div" class="w-commerce-commerceaddtocartform default-state" style="margin-top: -1.5vw; <?php if ($isPublished == 0) {
            echo 'display: none;';
          } ?>">
            <a id="unpublish-post-btn" data-id="<?php echo htmlspecialchars($id); ?>" style="cursor: pointer;"
              class="w-commerce-commerceaddtocartbutton cta-main">
              Снять с публикации
            </a>
          </div>

          <div class="w-commerce-commerceaddtocartform default-state" style="margin-top: -1.5vw;">
            <a href="/create-update-post?id=<?php echo htmlspecialchars($id); ?>"
              class="w-commerce-commerceaddtocartbutton cta-main">
              Редактировать
            </a>
          </div>

          <div id="delete-post-div" class="w-commerce-commerceaddtocartform default-state" style="margin-top: -1.5vw;">
            <a id="delete-post-btn" data-id="<?php echo htmlspecialchars($id); ?>" style="cursor: pointer;"
              class="w-commerce-commerceaddtocartbutton cta-main">
              Удалить
            </a>
          </div>

          <div class="faq-block">

            <div class="expandable-single">
              <div class="expandable-top">
                <div class="text-body text-bold">Подсказки для кнопок</div>
                <div class="faq-animated-box">
                  <div class="faq-horizontal"></div>
                  <div class="faq-vertical"></div>
                </div>
              </div>
              <div class="expandable-bottom">
                <p class="faq-paragraph">
                  -&nbsp;Кнопка "Снять с публикации" удаляет страницу поста с сайта, но не удаляет пост из базы данных.
                  Его можно будет опубликовать снова в любое время.<br><br>
                  -&nbsp;Кнопка "Опубликовать" создает страницу поста на сайте и публикует его. Пост будет доступен для
                  просмотра всем пользователям.<br><br>
                  -&nbsp;Кнопка "Редактировать" открывает страницу редактирования поста. Вы можете изменить текст,
                  изображения, ссылки и другие параметры поста.<br><br>
                  -&nbsp;Кнопка "Удалить" удаляет пост с сайта и из панели администратора. Пост можно будет восстановить
                  в течении 30 дней после удаления, но для этого нужно будет обратиться к <a href="https://t.me/smmrv"
                    class="text-underline" style="color: #646464;" target="_blank">разработчику</a>. После 30 дней пост
                  будет удален навсегда автоматически.
                </p>
              </div>
            </div>

          </div>
        </div>


        <div id="w-node-c6e35a31-fec2-c14b-2c9f-3bc5d2187d67-43f76668" class="contact-left">
          <div class="contact-info-block" style="width: -webkit-fill-available; position: sticky; top: 11vh;">
            <div class="contact-info-tile" style="width: -webkit-fill-available;">

              <iframe id="iframe-post"
                src="<?php echo htmlspecialchars($main_site_posts_url . $post['RelativeUrl']); ?>" style="
                  width: 100%;
                  height: 80vh;
                  border: 2px solid #121212;
                  border-radius: 10px;
                  margin: 0;
                  z-index: 10;
                  <?php if ($isPublished == 0) {
                    echo 'display: none;';
                  } ?>
                " frameborder="0" allowfullscreen>
              </iframe>

              <img id="post-image"
                src="<?php echo htmlspecialchars($main_site_images_url . $post['MediaFolderName'] . '/' . $post['FirstImageName']); ?>"
                style="width: 100%; height: 80vh; border-radius: 10px; margin: 0; z-index: 10; object-fit: cover; <?php if ($isPublished == 1) {
                  echo 'display: none;';
                } ?>">
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <script src="/js/jquery-3.5.1.min.js" type="text/javascript" crossorigin="anonymous"></script>
  <script src="/js/design-lib.js" type="text/javascript"></script>
  <script src="/js/toast.js" type="text/javascript"></script>
  <script src="/js/notification.js" type="text/javascript"></script>
  <script src="/js/posts-crud-manager.js" type="text/javascript"></script>
</body>

</html>