<?php
require_once __DIR__ . '/services/db-connection.php';
require_once __DIR__ . '/services/functions.php';
require_once __DIR__ . '/services/auth-required.php';

$config = include __DIR__ . '/services/config.php';
$main_site_images_url = $config['main_site_images_url'];
$main_site_images_path = $config['main_site_images_path'];

if (isset($_GET['id'])) {
  $id = $_GET['id'];
  $post = GetPostById($id);
} else {
  $post = null;
}
?>

<!DOCTYPE html>
<html data-wf-page="6775a4946057800443f76668" lang="ru-RU">

<head>
  <!-- Default meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex, nofollow">
  <title><?php echo $post ? 'Редактировать пост №' . htmlspecialchars($id) : 'Создать пост'; ?></title>
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
              <?php echo $post ? 'Редактировать пост' : 'Создать новый пост'; ?>
            </h1>
          </div>
          <div class="hide">
            <h1 data-w-id="918205aa-520f-066c-c1f0-c46c1c02b7a5"
              style="-webkit-transform:translate3d(0, 200%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0, 200%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0, 200%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0, 200%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0)"
              class="text-dark-32"><?php echo $post ? '№' . htmlspecialchars($id) : 'прямо сейчас'; ?></h1>
          </div>
        </div>
      </div>

      <div class="w-layout-grid contact-halves">
        <div id="w-node-c6e35a31-fec2-c14b-2c9f-3bc5d2187d67-43f76668" class="contact-left">
          <div class="contact-info-block" style="width: -webkit-fill-available; position: sticky; top: 11vh;">
            <div class="contact-info-tile" style="width: -webkit-fill-available;">
              <iframe id="iframe-post"
                src="<?php echo $post ? 'https://smmrv.online/journal-posts/' . htmlspecialchars($post['RelativeUrl']) : 'https://smmrv.online/templates/post-mock'; ?>"
                style="
                  width: 100%;
                  height: 80vh;
                  border: 2px solid #121212;
                  border-radius: 10px;
                  margin: 0;
                  z-index: 10;
                " frameborder="0" allowfullscreen>
              </iframe>
            </div>
          </div>
        </div>

        <div id="w-node-c6e35a31-fec2-c14b-2c9f-3bc5d2187d6f-43f76668" class="form-contact w-form form-margin">

          <form id="create-update-post-form" name="create-update-post-form" data-name="create-update-post-form"
            method="post" class="contact-form" enctype="multipart/form-data">

            <?php if ($post): ?>
              <input type="hidden" name="IdPost" value="<?php echo htmlspecialchars($id); ?>">
            <?php endif; ?>

            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

            <div class="contact-input-wrap">
              <div class="label">Название страницы для браузера (в конце — студия statement™)</div>
              <input class="text-field w-input" required minlength="3" maxlength="100" type="text" name="browser-title"
                id="browser-title" data-name="browser-title" placeholder="Название поста — студия statement™"
                value="<?php echo $post ? htmlspecialchars($post['BrowserTitle']) : ''; ?>">
            </div>

            <div class="contact-input-wrap">
              <div class="label">Описание поста (для браузера и админки)</div>
              <textarea id="description" name="description" minlength="5" maxlength="512" required data-name="description"
                placeholder="Краткое описание поста" class="text-field text-area w-input word-count-area"
                data-max-words="60"
                style="min-height: 100px !important;"><?php echo $post ? htmlspecialchars($post['Description']) : ''; ?></textarea>
              <div class="message-counter">
                <span class="word-counter counter-text"></span>
              </div>
            </div>

            <div class="contact-input-wrap">
              <div class="label">Название поста в ссылке</div>
              <input class="text-field w-input" required minlength="3" maxlength="50" type="text" name="relative-url"
                id="relative-url" data-name="relative-url" placeholder="Например — la-maison"
                value="<?php echo $post ? htmlspecialchars($post['RelativeUrl']) : ''; ?>">
            </div>

            <div class="contact-input-wrap">
              <div class="label">Заголовок</div>
              <textarea id="title" name="title" minlength="5" maxlength="100" required data-name="title" placeholder="О чем будет пост?"
                class="text-field text-area w-input word-count-area" data-max-words="15"
                style="min-height: 100px !important;"><?php echo $post ? htmlspecialchars($post['Title']) : ''; ?></textarea>
              <div class="message-counter">
                <span class="word-counter counter-text"></span>
              </div>
            </div>

            <div class="contact-input-wrap">
              <div class="label">Подзаголовок</div>
              <textarea id="subtitle" name="subtitle" minlength="10" maxlength="256" required data-name="subtitle"
                placeholder="Лаконичное описание" class="text-field text-area w-input word-count-area"
                data-max-words="30"><?php echo $post ? htmlspecialchars($post['Subtitle']) : ''; ?></textarea>
              <div class="message-counter">
                <span class="word-counter counter-text"></span>
              </div>
            </div>

            <div class="contact-input-wrap">
              <div class="label">Основной текст</div>
              <textarea id="article" name="article" minlength="30" maxlength="2048" required data-name="article"
                placeholder="Можно рассказать о новом зале или достижениях"
                class="text-field text-area w-input word-count-area"
                data-max-words="150"><?php echo $post ? htmlspecialchars($post['Article']) : ''; ?></textarea>
              <div class="message-counter">
                <span class="word-counter counter-text"></span>
              </div>
            </div>

            <div class="contact-input-wrap">
              <div class="label">Заключение или цитата</div>
              <textarea id="conclusion" name="conclusion" minlength="10" maxlength="256" required data-name="conclusion"
                placeholder="Подведение итогов" class="text-field text-area w-input word-count-area"
                data-max-words="30"><?php echo $post ? htmlspecialchars($post['Conclusion']) : ''; ?></textarea>
              <div class="message-counter">
                <span class="word-counter counter-text"></span>
              </div>
            </div>

            <!-- 1st photo -->
            <div class="w-layout-grid input-halves contact-input-halves">
              <div class="contact-input-wrap">
                <div class="label">Первая фотография</div>
                <label class="custom-file-upload-box" style="height: 40vh;">
                  <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512"
                    class="custom-file-upload-icon" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                    <path fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"
                      d="M320 367.79h76c55 0 100-29.21 100-83.6s-53-81.47-96-83.6c-8.89-85.06-71-136.8-144-136.8-69 0-113.44 45.79-128 91.2-60 5.7-112 43.88-112 106.4s54 106.4 120 106.4h56">
                    </path>
                    <path fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"
                      d="m320 255.79-64-64-64 64m64 192.42V207.79"></path>
                  </svg>
                  <span class="custom-file-upload-text text-big">Перетащите файл или нажмите для выбора</span>
                  <span class="custom-file-upload-subtext">Поддерживаются форматы: JPG, PNG, WebP, HEIC, GIF</span>
                  <input type="file" name="first-photo" id="first-photo" accept="image/*"
                    onchange="Services.previewImage(this, 'first-photo-preview')">
                </label>
              </div>

              <div class="contact-input-wrap">
                <div class="label">Превью фотографии</div>
                <div style="height: 40vh; overflow: hidden; border-radius: 12px;">
                  <img id="first-photo-preview" loading="lazy"
                    src="<?php echo $post ? htmlspecialchars($main_site_images_url . $post['MediaFolderName'] . '/' . $post['FirstImageName']) : '/images/tumbleweed.gif'; ?>"
                    style="object-position: 50% 50%;" class="image-cover">
                </div>
              </div>
            </div>

            <!-- 2nd photo -->
            <div class="w-layout-grid input-halves contact-input-halves">
              <div class="contact-input-wrap">
                <div class="label">Вторая фотография</div>
                <label class="custom-file-upload-box" style="height: 25vh;">
                  <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512"
                    class="custom-file-upload-icon" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                    <path fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"
                      d="M320 367.79h76c55 0 100-29.21 100-83.6s-53-81.47-96-83.6c-8.89-85.06-71-136.8-144-136.8-69 0-113.44 45.79-128 91.2-60 5.7-112 43.88-112 106.4s54 106.4 120 106.4h56">
                    </path>
                    <path fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"
                      d="m320 255.79-64-64-64 64m64 192.42V207.79"></path>
                  </svg>
                  <span class="custom-file-upload-text text-big">Перетащите файл или нажмите для выбора</span>
                  <span class="custom-file-upload-subtext">Поддерживаются форматы: JPG, PNG, WebP, HEIC, GIF</span>
                  <input type="file" name="second-photo" id="second-photo" accept="image/*"
                    onchange="Services.previewImage(this, 'second-photo-preview')">
                </label>
              </div>

              <div class="contact-input-wrap">
                <div class="label">Превью фотографии</div>
                <div style="height: 25vh; overflow: hidden; border-radius: 12px;">
                  <img id="second-photo-preview" loading="lazy"
                    src="<?php echo $post ? htmlspecialchars($main_site_images_url . $post['MediaFolderName'] . '/' . $post['SecondImageName']) : '/images/tumbleweed.gif'; ?>"
                    style="object-position: 50% 50%;" class="image-cover">
                </div>
              </div>
            </div>

            <!-- 3rd photo -->
            <div class="w-layout-grid input-halves contact-input-halves">
              <div class="contact-input-wrap">
                <div class="label">Третья фотография</div>
                <label class="custom-file-upload-box" style="height: 50vh;">
                  <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512"
                    class="custom-file-upload-icon" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                    <path fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"
                      d="M320 367.79h76c55 0 100-29.21 100-83.6s-53-81.47-96-83.6c-8.89-85.06-71-136.8-144-136.8-69 0-113.44 45.79-128 91.2-60 5.7-112 43.88-112 106.4s54 106.4 120 106.4h56">
                    </path>
                    <path fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"
                      d="m320 255.79-64-64-64 64m64 192.42V207.79"></path>
                  </svg>
                  <span class="custom-file-upload-text text-big">Перетащите файл или нажмите для выбора</span>
                  <span class="custom-file-upload-subtext">Поддерживаются форматы: JPG, PNG, WebP, HEIC, GIF</span>
                  <input type="file" name="third-photo" id="third-photo" accept="image/*"
                    onchange="Services.previewImage(this, 'third-photo-preview')">
                </label>
              </div>

              <div class="contact-input-wrap">
                <div class="label">Превью фотографии</div>
                <div style="height: 50vh; overflow: hidden; border-radius: 12px;">
                  <img id="third-photo-preview" loading="lazy"
                    src="<?php echo $post ? htmlspecialchars($main_site_images_url . $post['MediaFolderName'] . '/' . $post['ThirdImageName']) : '/images/tumbleweed.gif'; ?>"
                    style="object-position: 50% 50%;" class="image-cover">
                </div>
              </div>
            </div>

            <!-- 4th photo -->
            <div class="w-layout-grid input-halves contact-input-halves">
              <div class="contact-input-wrap">
                <div class="label">Четвертая фотография</div>
                <label class="custom-file-upload-box" style="height: 25vh;">
                  <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512"
                    class="custom-file-upload-icon" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg">
                    <path fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"
                      d="M320 367.79h76c55 0 100-29.21 100-83.6s-53-81.47-96-83.6c-8.89-85.06-71-136.8-144-136.8-69 0-113.44 45.79-128 91.2-60 5.7-112 43.88-112 106.4s54 106.4 120 106.4h56">
                    </path>
                    <path fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"
                      d="m320 255.79-64-64-64 64m64 192.42V207.79"></path>
                  </svg>
                  <span class="custom-file-upload-text text-big">Перетащите файл или нажмите для выбора</span>
                  <span class="custom-file-upload-subtext">Поддерживаются форматы: JPG, PNG, WebP, HEIC, GIF</span>
                  <input type="file" name="fourth-photo" id="fourth-photo" accept="image/*"
                    onchange="Services.previewImage(this, 'fourth-photo-preview')">
                </label>
              </div>

              <div class="contact-input-wrap">
                <div class="label">Превью фотографии</div>
                <div style="height: 25vh; overflow: hidden; border-radius: 12px;">
                  <img id="fourth-photo-preview" loading="lazy"
                    src="<?php echo $post ? $main_site_images_url . $post['MediaFolderName'] . '/' . $post['FourthImageName'] : '/images/tumbleweed.gif'; ?>"
                    style="object-position: 50% 50%;" class="image-cover">
                </div>
              </div>
            </div>

            <div class="contact-submit-wrap">
              <input id="create-update-post-btn" type="submit" class="cta-main w-button"
                value="Сохранить и опубликовать">
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
      </div>
    </div>
  </section>

  <script src="/js/jquery-3.5.1.min.js" type="text/javascript" crossorigin="anonymous"></script>
  <script src="/js/design-lib.js" type="text/javascript"></script>
  <script src="/js/toast.js" type="text/javascript"></script>
  <script src="/js/services.js" type="text/javascript"></script>
  <script src="/js/forms-manager.js" type="text/javascript"></script>

</body>

</html>