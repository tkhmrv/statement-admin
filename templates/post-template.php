<?php
function img($filename)
{
  global $mediaFolderName;
  return htmlspecialchars('/images/journal-posts/' . $mediaFolderName . '/' . $filename);
}

function imgUrl($filename)
{
  global $mediaUrl;
  return htmlspecialchars($mediaUrl . $filename);
}
?>

<!DOCTYPE html>
<html xmlns:og="http://opengraphprotocol.org/schema/" data-wf-page="675f341bf9e521989d9fac27" lang="ru-RU">

<head>
  <!-- Default meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlspecialchars($browserTitle); ?></title>
  <meta name="description" content="<?php echo htmlspecialchars($description); ?>">
  <link href="/images/favicon.ico" rel="icon" type="image/x-icon">
  <link href="/images/favicon-apple.svg" rel="apple-touch-icon">
  <link rel="canonical" href="<?php echo htmlspecialchars($canonicalUrlTemp); ?>">

  <!-- Open Graph -->
  <meta property="og:site_name" content="<?php echo htmlspecialchars($browserTitle); ?>">
  <meta property="og:title" content="<?php echo htmlspecialchars($title); ?>">
  <meta property="og:description" content="<?php echo htmlspecialchars($description); ?>">
  <meta property="og:url" content="<?php echo htmlspecialchars($canonicalUrlTemp); ?>">
  <meta property="og:type" content="website">
  <meta property="og:image" content="<?= imgUrl($firstImageName) ?>">
  <meta property="og:image:width" content="1200">
  <meta property="og:image:height" content="630">

  <!-- Schema.org -->
  <meta itemprop="name" content="<?php echo htmlspecialchars($browserTitle); ?>">
  <meta itemprop="url" content="<?php echo htmlspecialchars($canonicalUrlTemp); ?>">

  <!-- Twitter Cards -->
  <meta name="twitter:title" content="<?php echo htmlspecialchars($browserTitle); ?>">
  <meta name="twitter:description" content="<?php echo htmlspecialchars($description); ?>">
  <meta name="twitter:url" content="<?php echo htmlspecialchars($canonicalUrlTemp); ?>">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:image" content="<?= imgUrl($firstImageName) ?>">

  <!-- Pinterest -->
  <meta name="pinterest-rich-pin" content="true">

  <!-- JSON-LD -->
  <script type="application/ld+json">
    {
      "@context": "http://schema.org",
      "@graph": [{
        "@type": "Website",
        "name": <?= json_encode(htmlspecialchars($browserTitle), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
        "image": <?= json_encode((imgUrl($firstImageName)), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
        "url": <?= json_encode(htmlspecialchars($canonicalUrlTemp), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
        "datePublished": "<?= date('c', strtotime($publishedAt)) ?>"
      },
      {
          "@type": "BreadcrumbList",
          "itemListElement": [
            {
              "@type": "ListItem",
              "position": 1,
              "name": "Главная",
              "item": "https://statement-st.ru"
            },
            {
              "@type": "ListItem",
              "position": 2,
              "name": "Журнал | statement™ - стильная фотостудия в центре Санкт-Петербурга",
              "item": "https://statement-st.ru/journal"
            },
            {
              "@type": "ListItem",
              "position": 3,
              "name": <?= json_encode(htmlspecialchars($browserTitle), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
              "item": <?= json_encode(htmlspecialchars($canonicalUrlTemp), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
              
            }
          ]
        }
      ]
    }
  </script>

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

  <?php echo "<?php include '../templates/w-mod.php'; ?>"; ?>



</head>

<body>

  <?php echo "<?php include '../templates/notification.php'; ?>"; ?>

  <?php echo "<?php include '../templates/cookie.php'; ?>"; ?>

  <?php echo "<?php include '../templates/navbar.php'; ?>"; ?>

  <section class="section hero-about-c">
    <div class="w-layout-blockcontainer main-container w-container">
      <div class="about-c-hero-master">
        <div data-w-id="e0c879aa-2eb2-fa46-9e3b-a2af1f518ded" class="about-c-left-image"><img loading="lazy"
            src="<?= img($secondImageName) ?>" alt="" class="image-cover parallax">
          <div style="height:100%; object-position: center center;" class="mask-image"></div>
        </div>
        <div class="about-c-right-block">
          <div data-w-id="b1c804f4-213c-9707-74d3-5a202dd16249" class="about-c-right-image"><img
              sizes="(max-width: 479px) 100vw, (max-width: 767px) 45vw, 31vw" srcset="<?= img($firstImageName) ?>"
              alt="" src="<?= img($firstImageName) ?>" loading="lazy" class="image-cover parallax">
            <div style="height:100%; object-position: center center;" class="mask-image"></div>
          </div>
          <div class="label">(Опубликовано <?php
          $date = new DateTime($publishedAt);
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
          echo htmlspecialchars($date->format('d') . ' ' . $months[$date->format('n') - 1] . ' ' . $date->format('Y') . ' года');
          ?>)</div>
        </div>
      </div>
    </div>
  </section>

  <section class="section about-c-heading-section">
    <div class="w-layout-blockcontainer main-container w-container">
      <div class="heading-about-c">

        <?php
        $words = explode(' ', $title);
        foreach ($words as $word) {
          echo '<div class="hide"><div data-w-id="6d2a4cd8-284e-5596-59e8-0e7e8bf11d8e" style="-webkit-transform:translate3d(0, 200%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-moz-transform:translate3d(0, 200%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);-ms-transform:translate3d(0, 200%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0);transform:translate3d(0, 200%, 0) scale3d(1, 1, 1) rotateX(0) rotateY(0) rotateZ(0) skew(0, 0)" class="text-h1">' . htmlspecialchars($word) . '</div></div>';
        }
        ?>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="w-layout-blockcontainer main-container w-container">
      <div class="w-layout-grid about-c-team-halves">
        <div data-w-id="2ec4ccbf-661b-cb2e-4754-01575ed1910f" class="about-c-team-image"><img
            sizes="(max-width: 479px) 100vw, 40vw" srcset="<?= img($thirdImageName) ?>" alt=""
            src="<?= img($thirdImageName) ?>" loading="lazy" class="image-cover parallax">
          <div style="height:100%; object-position: center center;" class="mask-image"></div>
        </div>
        <div class="team-info-right">
          <h4 class="no-margins"><?php echo $subtitle; ?></h4>
          <p class="text-big" style="margin-top: var(--scaling--48);"><?php echo $article; ?></p>
        </div>
      </div>
    </div>
  </section>

  <section class="section about-c-value-section">
    <div class="w-layout-blockcontainer main-container w-container">
      <div class="section-divider about-c-value-divider"></div>
      <div class="w-layout-grid about-c-value-halves">
        <div id="w-node-decf86f9-b629-24a3-93ab-def262d7cb4c-9d9fac27" class="headline-about-c-value">
          <div class="label">(ЖДЕМ ВАС)</div>
          <h4 class="no-margins"><?php echo $conclusion; ?></h4>
        </div>
        <div class="about-c-value-right">
          <div data-w-id="c6d19233-8260-aeed-8317-825ea17bf91c" class="about-c-value-image"><img sizes="100vw"
              srcset="<?= img($fourthImageName) ?>" alt="" src="<?= img($fourthImageName) ?>" loading="lazy"
              class="image-cover parallax">
            <div style="height:100%; object-position: center center;" class="mask-image"></div>
          </div>
        </div>
      </div>
      <div class="section-divider mobile-only" style="margin-top: var(--scaling--120);"></div>
    </div>
  </section>

  <section class="section cta-section">
    <div data-poster-url="/images/vid-4.png" data-video-urls="/media/vid-4.mp4" data-autoplay data-loop
      data-wf-ignore="true" class="cta-video w-background-video w-background-video-atom">
      <video id="index-cta-section-video" autoplay loop style="background-image: url('/images/vid-4.png');" muted
        playsinline data-wf-ignore="true">
        <source src="/media/vid-4.mp4" data-wf-ignore="true">
        <source src="/media/vid-4.webm" data-wf-ignore="true">
      </video>
      <div class="video-custom-overlay">
      </div>
      <div class="w-layout-blockcontainer main-container w-container">
        <div class="cta-master">
          <div class="cta-top-tile">
            <div class="heading-cta">
              <h2 class="text-h1">Создано для&nbsp;настоящего творчества.</h2>
            </div>
          </div>
          <div class="cta-button-wrap">
            <a href="https://appevent.ru/w/5653" target="_blank" class="cta-secondary light w-inline-block">
              <div>Записаться</div>
              <div class="cta-underline">
                <div class="underline-filled-line"></div>
              </div>
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <?php echo "<?php include '../templates/footer.php'; ?>"; ?>

  <script src="/js/jquery-3.5.1.min.js" type="text/javascript" crossorigin="anonymous"></script>
  <script src="/js/design-lib.js" type="text/javascript"></script>
  <script src="/js/custom.js" type="text/javascript"></script>
</body>

</html>