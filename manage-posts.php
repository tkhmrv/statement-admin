<?php
require_once __DIR__ . '/services/db-connection.php';
require_once __DIR__ . '/services/functions.php';
require_once __DIR__ . '/services/auth-required.php';
$config = include __DIR__ . '/services/config.php';

$main_site_images_url = $config['main_site_images_url'];
?>

<!DOCTYPE html>
<html data-wf-page="675f341bf9e521989d9fac1f" lang="ru-RU">

<head>
  <!-- Default meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex, nofollow">
  <title>Управление постами</title>
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
  <script src="/js/toast.js" type="text/javascript"></script>

  <?php include 'templates/w-mod.php'; ?>

</head>

<body>

  <?php include 'templates/toast.php'; ?>
  <?php include 'templates/admin-navbar.php'; ?>

  <section class="section hero-store">
    <div class="w-layout-blockcontainer main-container w-container">
      <div class="headline-pricing">
        <div class="hide">
          <h1 data-w-id="0e336aad-2a1a-2515-428a-3a409677f2a8"
            style="-webkit-transform:translate3d(0, 200%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0, 200%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0, 200%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0, 200%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0)">
            Управление постами</h1>
        </div>
        <div class="hide">
          <h1 data-w-id="8facb611-64cf-b09b-f710-c9cc067d8855"
            style="-webkit-transform:translate3d(0, 200%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0, 200%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0, 200%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0, 200%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0)"
            class="text-dark-32">Из журнала</h1>
        </div>
      </div>

      <div data-w-id="7828e928-3456-e643-a2aa-8606ad4745f6" style="opacity:0" class="products w-dyn-list">
        <div role="list" class="pricing-thirds w-dyn-items">

          <?php
          try {
            $query = $conn->prepare("SELECT * FROM Posts WHERE IsSoftDeleted = FALSE ORDER BY PostCreatedAt DESC ");
            $query->execute();
            $posts = $query->fetchAll(PDO::FETCH_ASSOC);

          } catch (PDOException $e) {
            echo '<script>showToast("Ошибка базы данных: ' . addslashes($e->getMessage()) . '", "error");</script>';
            exit;
          }

          if (empty($posts)) {
            echo '<div class="text-big">Нет доступных постов!</div>';
          } else {
            $months = [
              'января',
              'февраля',
              'марта',
              'апреля',
              'мая',
              'июня',
              'июля',
              'августа',
              'сентября',
              'октября',
              'ноября',
              'декабря'
            ];

            foreach ($posts as $post) {
              $date = new DateTime($post['PostCreatedAt']);
              $formattedDate = $date->format('d') . ' ' . $months[$date->format('n') - 1] . ' ' . $date->format('Y') . ' года';

              if (!empty($post['MediaFolderName']) && !empty($post['FirstImageName'])) {
                $postHtml = '<div role="listitem" class="w-dyn-item">
                  <a href="/manage-post?id=' . htmlspecialchars($post['IdPost']) . '" class="product-card w-inline-block">
                    <div class="product-image-wrap">
                      <img src="' . htmlspecialchars($main_site_images_url . $post['MediaFolderName'] . '/' . $post['FirstImageName']) . '" loading="lazy" class="product-thumbnail">
                    </div>
                    <div class="product-info-wrap">
                      <div class="product-card-info">
                        <div class="text-big">' . htmlspecialchars($post['Title']) . '</div>
                      </div>
                      <div class="product-card-price">
                        <div class="text-big">' . htmlspecialchars($formattedDate) . '</div>
                      </div>
                    </div>
                  </a>
                </div>';

                echo $postHtml;
              }
            }
          }
          ?>

        </div>
      </div>
      <div class="section-divider store-bottom"></div>
    </div>
  </section>

  <script src="/js/jquery-3.5.1.min.js" type="text/javascript" crossorigin="anonymous"></script>
  <script src="/js/design-lib.js" type="text/javascript"></script>

</body>

</html>