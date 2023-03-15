<!DOCTYPE html>
<html <?php language_attributes(); ?>>
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="charset" content="utf-8">
    <meta name="format-detection" content="telephone=no">
    <meta property="og:url" content="<?php echo home_url( $_SERVER['REQUEST_URI'] ); ?>">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?php echo get_bloginfo( 'name' ) ?? '株式会社カインズ 企業サイト' ?>">
    <meta property="og:locale" content="ja_JP">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="format-detection" content="telephone=no">
    <meta name="robots" content="index,follow">
    <link rel="canonical" href="<?php echo home_url( $_SERVER['REQUEST_URI'] ); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;700&amp;display=swap" rel="stylesheet">
    <link rel="icon" href="<?php echo get_stylesheet_directory_uri() ?>/assets/images/favicon.ico">
    <link rel="icon" href="<?php echo get_stylesheet_directory_uri() ?>/assets/images/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" type="image/png" sizes="180x180" href="<?php echo get_stylesheet_directory_uri() ?>/assets/images/apple-touch-icon.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css">
    <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri() ?>/assets/css/style.css">
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-WVB45Z6');</script>
<!-- End Google Tag Manager -->
    <?php wp_head(); ?>
  </head>
  <body <?php echo bodyClass(); ?>>
  <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WVB45Z6"
    width="0" style="display:none;visibility:hidden"></iframe></noscript>
  <!-- End Google Tag Manager (noscript) -->
    <!-- noscript, no-cookie-->
    <noscript>
      <div class="m-boxAtt">
        <p class="m-boxAtt_ttl m-taC">JavaScriptが無効になっています。</p>
        <p class="m-textS m-taC-pc">すべての機能を利用するためには、有効に設定してください。<br class="g-inline-pc">設定方法はお使いのブラウザのヘルプをご確認ください。</p>
      </div>
    </noscript>
    <header class="m-header">
      <div class="m-header__top">
        <div class="m-header__top-inner">
          <nav class="m-header__top-menu">
                <ul>
                  <li><a href="https://www.cainz.com/" target="_blank">オンラインショップ</a>
                  </li>
                  <li><a href="https://reform.cainz.com/" target="_blank">リフォーム</a>
                  </li>
                  <li><a href="https://petsone.cainz.com/" target="_blank">ペッツワン</a>
                  </li>
                  <li><a href="https://reserve.cainz.com/" target="_blank">Reserve</a>
                  </li>
                  <li><a href="https://diy-style.cainz.com/" target="_blank">DIY STYLE</a>
                  </li>
                  <li><a href="https://style-factory.cainz.com/" target="_blank">Style Factory</a>
                  </li>
                  <li><a href="https://www.cainz.co.jp/" target="_blank">企業サイト</a>
                  </li>
                </ul>
          </nav>
        </div>
      </div>
      <div class="m-header__bottom">
        <div class="m-header__bottom-inner">
          <?php if(is_front_page()) : ?>
            <h1 class="m-header__logo">
              <a href="/"><img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/common/logo_header.svg" alt="CAINZ"></a>
            </h1>
          <?php else : ?>
            <div class="m-header__logo">
              <a href="/"><img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/common/logo_header.svg" alt="CAINZ"></a>
            </div>
          <?php endif; ?>
          <nav class="m-header__bottom-menu">
            <ul>
              <li><a href="/corporate/">企業情報</a>
                <div class="m-header__subMenu">
                  <div class="m-header__boxMenu">
                    <div class="m-header__thumbMenu">
                      <div class="m-header__thumbMenu-img"><img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/common/img_header_company.png" alt="企業情報"></div>
                      <div class="m-header__thumbMenu-wrap">
                        <div class="m-header__thumbMenu-japanese">企業情報</div>
                        <div class="m-header__thumbMenu-english">COMPANY INFO</div>
                      </div>
                    </div>
                    <div class="m-header__navMenu">
                          <ul>
                            <li><a href="/corporate/top_message/">トップメッセージ</a>
                            </li>
                            <li><a href="/corporate/philosophy/">企業理念</a>
                            </li>
                            <li><a href="/corporate/policy/">行動方針</a>
                            </li>
                            <li><a href="/corporate/overview/">会社概要</a>
                            </li>
                            <li><a href="/corporate/access/">アクセス</a>
                            </li>
                            <li><a href="/corporate/management/">役員一覧・組織図</a>
                            </li>
                            <li><a href="/corporate/history/">沿革</a>
                            </li>
                            <li><a href="/corporate/group/">ベイシアグループについて</a>
                            </li>
                          </ul>
                    </div>
                    <div class="m-header__btnLink"><a class="m-header__btnLink-text" href="/corporate/">企業情報トップ</a></div>
                  </div>
                </div>
              </li>
              <li><a href="/news/">ニュースリリース</a></li>
              <li><a href="/contents/">コンテンツ</a></li>
              <li><a href="/about/">カインズについて</a>
                <div class="m-header__subMenu">
                  <div class="m-header__boxMenu">
                    <div class="m-header__thumbMenu">
                      <div class="m-header__thumbMenu-img"><img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/common/img_header_aboutus.png" alt="カインズについて"></div>
                      <div class="m-header__thumbMenu-wrap">
                        <div class="m-header__thumbMenu-japanese">カインズについて</div>
                        <div class="m-header__thumbMenu-english">ABOUT CAINZ</div>
                      </div>
                    </div>
                    <div class="m-header__navMenu">
                          <ul>
                            <li><a href="/about/products/">商品開発（SBU戦略）</a>
                            </li>
                            <li><a href="/about/store/">店舗について</a>
                            </li>
                            <li><a href="/about/logistics/">ロジティクス</a>
                            </li>
                            <li><a href="/about/quality/">品質管理</a>
                            </li>
                            <li><a href="/about/digital/">デジタル戦略</a>
                            </li>
                            <li><a href="/about/hr/">人事戦略（DIY HR）</a>
                            </li>
                            <li><a href="/about/kumimachi/">くみまち構想</a>
                            </li>
                          </ul>
                    </div>
                    <div class="m-header__btnLink"><a class="m-header__btnLink-text" href="/about/">カインズについてトップ</a></div>
                  </div>
                </div>
              </li>
              <li><a href="/partner/">ビジネスパートナー募集</a>
                <div class="m-header__subMenu">
                  <div class="m-header__boxMenu">
                    <div class="m-header__thumbMenu">
                      <div class="m-header__thumbMenu-img"><img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/common/img_header_partner.png" alt="ビジネスパートナー募集"></div>
                      <div class="m-header__thumbMenu-wrap">
                        <div class="m-header__thumbMenu-japanese">ビジネスパートナー募集</div>
                        <div class="m-header__thumbMenu-english">Looking for business</div>
                      </div>
                    </div>
                    <div class="m-header__navMenu">
                          <ul>
                            <li><a href="/partner/franchise/">フランチャイズ募集</a>
                            </li>
                            <li><a href="/partner/event/">催事募集</a>
                            </li>
                            <li><a href="/partner/site/">出店用地募集</a>
                            </li>
                            <li><a href="/partner/construction/">施工業者募集</a>
                            </li>
                            <li><a href="/partner/products_partner/">商品関連お取引業者募集</a>
                            </li>
                            <li><a href="/partner/tenant/">テナント募集</a>
                            </li>
                          </ul>
                    </div>
                    <div class="m-header__btnLink"><a class="m-header__btnLink-text" href="/partner/">ビジネスパートナー募集トップ</a></div>
                  </div>
                </div>
              </li>
              <li><a href="https://recruit.cainz.com/" target="_blank">採用情報</a></li>
              <li><a href="https://translate.google.com/translate?hl=&amp;sl=ja&amp;tl=en&amp;u=https://www.cainz.co.jp/" target="_blank">EN</a></li>
            </ul>
          </nav>
          <div class="m-menuToggle">
            <div class="m-menuToggle__hamburger">
              <input class="m-menuToggle__hamburger-button js-humburger-btn" type="checkbox">
              <div class="m-menuToggle__hamburger-line"><span>1</span><span>2</span><span>3</span></div>
            </div>
            <div class="m-menuToggle__content js-menu-content">
              <nav class="m-menuToggle__content-item">
                <h2 class="m-menuToggle__content-item--title">最新情報</h2>
                <ul class="m-menuToggle__content-item--list">
                  <li><a href="/news/">ニュースリリース</a></li>
                  <li><a href="/contents/">コンテンツ</a></li>
                </ul>
              </nav>
              <nav class="m-menuToggle__content-item">
                <h2 class="m-menuToggle__content-item--title">総合情報</h2>
                <ul class="m-menuToggle__content-item--list">
                  <li>
                    <div class="m-menuToggle__according">
                      <input type="checkbox"><a class="m-menuToggle__according-text" href="/corporate/">企業情報</a>
                      <div class="m-menuToggle__according-content">
                            <ul>
                              <li><a href="/corporate/top_message/">トップメッセージ</a>
                              </li>
                              <li><a href="/corporate/philosophy/">企業理念</a>
                              </li>
                              <li><a href="/corporate/policy/">行動方針</a>
                              </li>
                              <li><a href="/corporate/overview/">会社概要</a>
                              </li>
                              <li><a href="/corporate/access/">アクセス</a>
                              </li>
                              <li><a href="/corporate/management/">役員一覧・組織図</a>
                              </li>
                              <li><a href="/corporate/history/">沿革</a>
                              </li>
                              <li><a href="/corporate/group/">ベイシアグループについて</a>
                              </li>
                            </ul>
                        <div class="m-header__btnLink"><a class="m-header__btnLink-text" href="/corporate/">企業情報トップ</a></div>
                      </div>
                      <div class="m-menuToggle__according-icon"></div>
                    </div>
                  </li>
                  <li>
                    <div class="m-menuToggle__according">
                      <input type="checkbox"><a class="m-menuToggle__according-text" href="/about/">カインズについて</a>
                      <div class="m-menuToggle__according-content">
                            <ul>
                              <li><a href="/about/products/">商品開発（SBU戦略）</a>
                              </li>
                              <li><a href="/about/store/">店舗について</a>
                              </li>
                              <li><a href="/about/logistics/">ロジティクス</a>
                              </li>
                              <li><a href="/about/quality/">品質管理</a>
                              </li>
                              <li><a href="/about/digital/">デジタル戦略</a>
                              </li>
                              <li><a href="/about/hr/">人事戦略（DIY HR）</a>
                              </li>
                              <li><a href="/about/kumimachi/">くみまち構想</a>
                              </li>
                            </ul>
                        <div class="m-header__btnLink"><a class="m-header__btnLink-text" href="/about/">カインズについてトップ</a></div>
                      </div>
                      <div class="m-menuToggle__according-icon"></div>
                    </div>
                  </li>
                  <li>
                    <div class="m-menuToggle__according">
                      <input type="checkbox"><a class="m-menuToggle__according-text" href="/partner/">ビジネスパートナー募集</a>
                      <div class="m-menuToggle__according-content">
                            <ul>
                              <li><a href="/partner/franchise/">フランチャイズ募集</a>
                              </li>
                              <li><a href="/partner/event/">催事募集</a>
                              </li>
                              <li><a href="/partner/site/">出店用地募集</a>
                              </li>
                              <li><a href="/partner/construction/">施工業者募集</a>
                              </li>
                              <li><a href="/partner/products_partner/">商品関連お取引業者募集</a>
                              </li>
                              <li><a href="/partner/tenant/">テナント募集</a>
                              </li>
                            </ul>
                        <div class="m-header__btnLink"><a class="m-header__btnLink-text" href="/partner/">ビジネスパートナー募集トップ</a></div>
                      </div>
                      <div class="m-menuToggle__according-icon"></div>
                    </div>
                  </li>
                  <li><a href="https://recruit.cainz.com/" target="_blank">採用情報</a></li>
                </ul>
              </nav>
              <nav class="m-menuToggle__globalMenu">
                <ul class="m-menuToggle__globalMenu-list">
                  <li><a href="https://www.cainz.com/" target="_blank">オンラインショップ</a></li>
                  <li><a href="https://reform.cainz.com/" target="_blank">リフォーム</a></li>
                  <li><a href="https://reserve.cainz.com/" target="_blank">Reserve</a></li>
                  <li><a href="https://petsone.cainz.com/" target="_blank">ペッツワン</a></li>
                  <li><a href="https://diy-style.cainz.com/" target="_blank">DIY STYLE</a></li>
                  <li><a href="https://style-factory.cainz.com/" target="_blank">Style Factory</a></li>
                  <li><a href="https://translate.google.com/translate?hl=&amp;sl=ja&amp;tl=en&amp;u=https://www.cainz.co.jp/" target="_blank">EN</a></li>
                </ul>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </header>
    <main class="main">