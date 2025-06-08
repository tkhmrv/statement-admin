<?php
require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/');
$dotenv->load();

require_once __DIR__ . '/services/functions.php';

SessionStartWithCheck();

if (IsAuthenticated()) {
  header('Location: /panel');
  exit();
}
?>

<!DOCTYPE html>
<html data-wf-page="675f341bf9e521989d9fab63" lang="ru-RU">

<head>
  <!-- Default meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex, nofollow">
  <title>Совершенно секретно</title>
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

  <div class="utility-page-wrap password-page-wrap">
    <div class="utility-page-content w-password-page w-form">
      <form method="post" id="auth-form" name="auth-form" data-name="auth-form"
        class="utility-page-form password-form w-password-page">
        <div class="pw-halves">
          <div class="pw-wrap">
            <div class="password-headline">
              <div class="pw-headlng">
                <div class="hide">
                  <h1 class="text-h0">Тсс… </h1>
                </div>
                <div class="hide">
                  <h1 class="text-h0">Cекретная</h1>
                </div>
                <div class="hide">
                  <h1 class="text-h0">Информация</h1>
                </div>
              </div>
            </div>

            <div class="pw-bottom-master">
              <div class="pw-form">

                <input class="text-field w-password-page w-input" autofocus="true" maxlength="256" name="pass"
                  data-name="Password" placeholder="Пароль" type="password" id="pass" required
                  autocomplete="current-password" minlength="8" maxlength="256">

                <input type="text" name="email" class="honeypot-field" autocomplete="off" tabindex="-1"
                  style="position:absolute;left:-9999px;top:auto;width:1px;height:1px;overflow:hidden;" />

                <input type="hidden" name="page_id" value="675f341bf9e521989d9fab63" />

                <input type="hidden" name="ts" value="<?= time(); ?>" />

                <div class="submit-button-wrap">
                  <input type="submit" class="submit-button w-password-page w-button" value="Войти">
                  <div class="icon-submit w-embed">
                    <svg width="100%" height="100%" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M0 8H14" stroke="currentColor" stroke-width="2"></path>
                      <path d="M7 1L14 8L7 15" stroke="currentColor" stroke-width="2"></path>
                    </svg>
                  </div>
                </div>

              </div>

              <!-- Turnstile -->
              <!-- <div class="cf-turnstile" data-sitekey="0x4AAAAAABckF0Iox-_gLbxs" data-theme="light" data-size="flexible"
                style="width: 100%; margin-top: 1vw;">
              </div> -->

            </div>
          </div>

          <div class="pw-image">
            <img loading="lazy" src="/images/shh.gif" class="image-cover parallax" style="object-position: center top;">
          </div>
        </div>
      </form>

    </div>
  </div>
  <script src="/js/jquery-3.5.1.min.js" type="text/javascript" crossorigin="anonymous"></script>
  <script src="/js/design-lib.js" type="text/javascript"></script>
  <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" defer></script>
  <script src="/js/toast.js" type="text/javascript"></script>
  <script src="/js/auth.js" type="text/javascript"></script>

</body>

</html>