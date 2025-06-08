<?php
require_once __DIR__ . '/services/db-connection.php';
require_once __DIR__ . '/services/functions.php';
require_once __DIR__ . '/services/auth-required.php';

$notification = $conn->query("SELECT * FROM Notifications ORDER BY LastUpdatedAt DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);

if ($notification) {
  $notificationText = $notification['Text'];
  $notificationImage = $notification['ImageName'];
}
?>
<!DOCTYPE html>
<html data-wf-page="6775a4946057800443f76668" lang="ru-RU">

<head>
  <!-- Default meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex, nofollow">
  <title>Уведомления для гостей</title>
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

  <div class="toast-cookie-fixed w-inline-block toasts-only" style="bottom: calc(12vh + 4vw);">
    <img src="" loading="lazy" class="icon-toast-fixed toast-icon">
    <div class="text-body semibold toasts-only-text-body"></div>
  </div>

  <?php include 'templates/notification.php'; ?>
  <?php include 'templates/admin-navbar.php'; ?>

  <section class="section hero-contact">
    <div class="w-layout-blockcontainer main-container w-container">
      <div class="headline-contact">
        <div class="heading-contact">
          <div class="hide">
            <h1 data-w-id="c6e35a31-fec2-c14b-2c9f-3bc5d2187d6a"
              style="-webkit-transform:translate3d(0, 200%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0, 200%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0, 200%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0, 200%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0)">
              Настройка уведомлений</h1>
          </div>
          <div class="hide">
            <h1 data-w-id="918205aa-520f-066c-c1f0-c46c1c02b7a5"
              style="-webkit-transform:translate3d(0, 200%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0, 200%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0, 200%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0, 200%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0)"
              class="text-dark-32">для&nbsp;клиентов</h1>
          </div>
        </div>
      </div>

      <div class="w-layout-grid contact-halves">
        <div id="w-node-c6e35a31-fec2-c14b-2c9f-3bc5d2187d6f-43f76668" class="form-contact w-form form-margin">

          <form id="notification-form" name="notification-form" data-name="notification-form" method="post"
            class="contact-form">

            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <div class="contact-input-wrap">
              <div class="label">Текст уведомления</div>
              <textarea oninput="Services.updateNotification()" id="notification-text-form" name="notification-text-form"
                minlength="10" required data-name="notification-text-form" placeholder="Текст уведомления"
                class="text-field text-area w-input word-count-area"
                data-max-words="40"><?php echo htmlspecialchars($notificationText); ?></textarea>
              <div class="message-counter">
                <span class="word-counter counter-text"></span>
              </div>
            </div>

            <div class="contact-input-wrap">
              <div class="label">Изображение</div>
              <select onchange="Services.updateNotification()" id="notification-image-form" name="notification-image-form"
                data-name="notification-image-form" required class="text-field select-field w-select"
                style="-webkit-appearance: none; -moz-appearance: none; appearance: none; color: #333 !important">
                <option value="celebration.webp" <?php echo htmlspecialchars($notificationImage) === 'celebration.webp' ? 'selected' : ''; ?>>Праздничное</option>
                <option value="warning.webp" <?php echo htmlspecialchars($notificationImage) === 'warning.webp' ? 'selected' : ''; ?>>
                  Предупреждение</option>
                <option value="calendar.webp" <?php echo htmlspecialchars($notificationImage) === 'calendar.webp' ? 'selected' : ''; ?>>
                  Календарь</option>
              </select>
            </div>

            <div class="contact-submit-wrap">
              <input id="save-notification-btn" type="submit" class="cta-main w-button" value="Сохранить">
            </div>
          </form>

          <div class="contact-form" style="margin-top: 10vh;">

            <h4 class="no-margins" style="margin-bottom: var(--scaling--8);">Создание ссылок для текста</h4>

            <div class="w-layout-grid input-halves contact-input-halves">
              <div class="contact-input-wrap">
                <div class="label">Текст ссылки</div>
                <input oninput="Services.createLinks()" class="text-field w-input" type="text" name="url-text" id="url-text"
                  data-name="url-text" placeholder="Будет виден посетителю">
              </div>

              <div class="contact-input-wrap">
                <div class="label">URL</div>
                <input oninput="Services.createLinks()" class="text-field w-input" type="text" name="url" id="url"
                  data-name="url" placeholder="Полный или относительный">
              </div>
            </div>

            <div class="contact-input-wrap">
              <div class="label">Результат</div>
              <textarea readonly id="result" name="result" minlength="10" required data-name="result"
                placeholder="Готовая ссылка" class="text-field text-area w-input"
                style="min-height: 100px !important;"></textarea>
            </div>

            <div class="contact-submit-wrap">
              <button onclick="Services.copyToClipboard()" class="cta-main w-button">Скопировать ссылку</button>
            </div>
          </div>

        </div>

        <div id="w-node-c6e35a31-fec2-c14b-2c9f-3bc5d2187d67-43f76668" class="contact-left">
          <div class="contact-info-block" style="padding-right: 20px; position: sticky; top: 11vh;">

            <div class="contact-info-tile">
              <style>
                @media (max-width: 992px) {
                  .custom-margin {
                    margin-bottom: 10vh;
                  }
                }
              </style>

              <div class="no-margins text-big dark custom-margin">
                <h5 class="no-margins">Как работают уведомления?</h5><br>
                Уведомления отображаются для новых посетителей сайта после того, как они согласились с&nbsp;политикой
                конфиденциальности. Они появляются в&nbsp;правом нижнем углу сайта на&nbsp;компьютере и&nbsp;в&nbsp;виде
                всплывающего окна внизу экрана на&nbsp;мобильных устройствах.
                <br><br>
                Продолжительность отображения уведомления&nbsp;&mdash; 10&nbsp;секунд. Если уведомление было прочитано
                пользователем, оно
                будет показано снова через 7&nbsp;дней.
              </div>
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
  <script src="/js/services.js" type="text/javascript"></script>
  <script src="/js/forms-manager.js" type="text/javascript"></script>

</body>

</html>