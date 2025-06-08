<?php
require_once __DIR__ . '/services/db-connection.php';
require_once __DIR__ . '/services/functions.php';
require_once __DIR__ . '/services/auth-required.php';

?>

<!DOCTYPE html>
<html data-wf-page="675f341bf9e521989d9fac10" lang="ru-RU">

<head>
  <!-- Default meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex, nofollow">
  <title>Панель управления</title>
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

<body class="body-home-b">

  <?php include 'templates/toast.php'; ?>

  <?php include 'templates/admin-navbar.php'; ?>

  <style>
    @media (min-width: 992px) {
      .hero-home-b {
        display: none;
      }
    }
  </style>

  <section class="section hero-home-b">
    <div class="w-layout-blockcontainer main-container w-container">
      <div class="w-layout-grid hero-thirds">
        <div class="home-b-left-column"></div>
        <div id="w-node-_20a873f1-fbea-bc32-2bbf-1c47ac56222b-9d9fac10" class="hero-b-right-column">
          <div class="heading-home" style="margin-bottom: 3vh;">
            <div data-w-id="20a873f1-fbea-bc32-2bbf-1c47ac56222f" class="heading-animation-hidden">
              <div class="heading-rotating-wrap _1">
                <div class="text-h1">Добро пожаловать,</div>
              </div>
              <div class="heading-rotating-wrap _2">
                <div class="text-h1">Welcome aboard,</div>
              </div>
              <div class="heading-rotating-wrap _3">
                <div class="text-h1">Bienvenue à bord,</div>
              </div>
              <div class="heading-rotating-wrap _4">
                <div class="text-h1">欢迎。,</div>
              </div>
              <div class="heading-rotating-wrap _5">
                <div class="text-h1">ようこそ,</div>
              </div>
            </div>
            <h1><?php echo htmlspecialchars($user['Name']); ?></h1>
          </div>

          <div data-w-id="61c0776b-9e68-5c33-94dc-43ee282ef0a8" class="home-b-video" style="height: 30vh;">
            <div data-poster-url="" data-video-urls="media/vid-4.mp4,media/vid-4.webm"
              class="video-cover parallax-video w-background-video w-background-video-atom" style="height: 100%;">
              <video id="ee2acdac-8b2a-26ad-4119-32ac7434d585-video" autoplay loop style="background-image:url('')"
                muted="" playsinline="" data-object-fit="cover">
                <source src="media/vid-4.mp4">
                <source src="media/vid-4.webm">
              </video>
            </div>
            <div style="height:100%" class="mask-image"></div>
          </div>

          <div class="home-b-about-wrap">
            <div class="label"></div>
            <div class="text-h3">Здесь вы можете управлять настройками сайта statement™.</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="section services-section-dark-bg">
    <div class="w-layout-blockcontainer main-container w-container">
      <div class="services-centered-master">

        <div class="service-item" onclick="window.location.href='/tg-bot-settings'" style="cursor:pointer;">
          <div class="text-h1">Настройки ТГ бота</div>
          <div class="text-small opacity-50">01</div>
          <img src="images/telegram.webp" loading="lazy" class="service-big-graphic">
        </div>

        <div class="service-item" onclick="window.location.href='/manage-posts'" style="cursor:pointer;">
          <div class="text-h1">Список постов</div>
          <div class="text-small opacity-50">02</div><img
            src="images/speaker.webp" loading="lazy"
            class="service-big-graphic">
        </div>

        <div class="service-item" onclick="window.location.href='/create-update-post'" style="cursor:pointer;">
          <div class="text-h1">Создать пост</div>
          <div class="text-small opacity-50">03</div><img
            src="images/hands.webp" loading="lazy"
            class="service-big-graphic">
        </div>

        <div class="service-item" onclick="window.location.href='/notifications'" style="cursor:pointer;">
          <div class="text-h1">Уведомления</div>
          <div class="text-small opacity-50">04</div><img
            src="images/bell.webp" loading="lazy"
            class="service-big-graphic">
        </div>

      </div>
    </div>
  </section>

  <script src="/js/jquery-3.5.1.min.js" type="text/javascript" crossorigin="anonymous"></script>
  <script src="/js/design-lib.js" type="text/javascript"></script>
</body>

</html>