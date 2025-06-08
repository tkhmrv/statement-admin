<?php
require_once __DIR__ . '/services/db-connection.php';
require_once __DIR__ . '/services/functions.php';
require_once __DIR__ . '/services/auth-required.php';

// Получаем до 5 чатов, отсортированных по времени добавления
$tgChats = [];
$result = $conn->query("SELECT ChatTitle, ChatId FROM TgChats ORDER BY ChatIdAddedAt ASC LIMIT 5");
if ($result !== false) {
  $tgChats = $result->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html data-wf-page="6775a4946057800443f76668" lang="ru-RU">

<head>
  <!-- Default meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex, nofollow">
  <title>Настройка ТГ бота</title>
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
  <?php include 'templates/admin-navbar.php'; ?>

  <section class="section hero-contact">
    <div class="w-layout-blockcontainer main-container w-container">
      <div class="headline-contact">
        <div class="heading-contact">
          <div class="hide">
            <h1 data-w-id="c6e35a31-fec2-c14b-2c9f-3bc5d2187d6a"
              style="-webkit-transform:translate3d(0, 200%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0, 200%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0, 200%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0, 200%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0)">
              Настройка ТГ бота</h1>
          </div>
          <div class="hide">
            <h1 data-w-id="918205aa-520f-066c-c1f0-c46c1c02b7a5"
              style="-webkit-transform:translate3d(0, 200%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0, 200%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0, 200%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0, 200%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0)"
              class="text-dark-32">для&nbsp;получения фидбека</h1>
          </div>
        </div>
      </div>

      <div class="w-layout-grid contact-halves">
        <div id="w-node-c6e35a31-fec2-c14b-2c9f-3bc5d2187d67-43f76668" class="contact-left">
          <div class="contact-info-block" style="padding-right: 20px;">


            <div class="contact-info-tile">
              <div class="no-margins text-big dark">
                <h5 class="no-margins">Инструкция по настройке</h5><br>

                1.&nbsp;Перейдите в приложение Телеграм <a href="https://t.me/statement_feedback_bot" target="_blank"
                  style="color: #121212; text-decoration: underline;">по этой ссылке.</a><br>
                2.&nbsp;В меню бота найдите команду /start и выполните её.<br>
                3.&nbsp;Создайте группу в Телеграм и пригласите туда бота, либо можете добавить бота в уже существующую
                группу.<br>
                4.&nbsp;В группе в меню бота найдите команду /activate и выполните её.<br>
                5.&nbsp;В появившемся сообщении нажмите кнопку "✅ Запустить бота".<br>
                6.&nbsp;Из обновленного сообщения вставьте название чата и его ID в форму на этой странице<br>
                7.&nbsp;Под формой нажмите кнопку "Сохранить". Все готово!<br><br>

                <h5 class="no-margins">Важная информация!</h5><br>

                -&nbsp;Бот поддерживает только группы, и не поддерживает личные сообщения.<br>
                -&nbsp;Максимальное количество групп - 5.<br>
                -&nbsp;Чтобы удалить чат из рассылки, просто уберите его название и ID из формы и нажмите кнопку
                "Сохранить".<br>
              </div>
            </div>
          </div>
        </div>

        <div id="w-node-c6e35a31-fec2-c14b-2c9f-3bc5d2187d6f-43f76668" class="form-contact w-form form-margin">

          <form id="tg-bot-form" name="tg-bot-form" data-name="tg-bot-form" method="post" class="contact-form">

            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <?php
            for ($i = 1; $i <= 5; $i++):
              $chatTitle = $tgChats[$i - 1]['ChatTitle'] ?? '';
              $chatId = $tgChats[$i - 1]['ChatId'] ?? '';
              ?>
              <div class="w-layout-grid input-halves contact-input-halves">
                <div class="contact-input-wrap">
                  <div class="label">Название чата №<?= $i ?></div>
                  <input class="text-field w-input" type="text" autocomplete="off" name="chat-name-<?= $i ?>"
                    minlength="1" maxlength="255" id="chat-name-<?= $i ?>" data-name="chat-name-<?= $i ?>"
                    pattern="[\p{L}\p{N} _\-]+" title="Только буквы, цифры, пробелы, дефис, подчёркивание"
                    value="<?= htmlspecialchars($chatTitle) ?>">
                </div>
                <div class="contact-input-wrap">
                  <div class="label">ID чата №<?= $i ?></div>
                  <input class="text-field w-input" type="text" autocomplete="off" name="chat-id-<?= $i ?>"
                    data-name="chat-id-<?= $i ?>" id="chat-id-<?= $i ?>" value="<?= htmlspecialchars($chatId) ?>"
                    pattern="-?\d{5,20}" title="Только цифры и дефис, длина 5-20" minlength="5" maxlength="20">
                </div>
              </div>
            <?php endfor; ?>

            <div class="contact-submit-wrap">
              <input type="submit" class="cta-main w-button" value="Сохранить">
            </div>
          </form>

        </div>
      </div>
    </div>
  </section>

  <script src="/js/jquery-3.5.1.min.js" type="text/javascript" crossorigin="anonymous"></script>
  <script src="/js/design-lib.js" type="text/javascript"></script>
  <script src="/js/toast.js" type="text/javascript"></script>
  <script src="/js/forms-manager.js" type="text/javascript"></script>

</body>

</html>