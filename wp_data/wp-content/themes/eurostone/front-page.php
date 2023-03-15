<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package storefront
 */

get_header(); ?>
    <?php
      require( get_stylesheet_directory() . '/module/sliderTop.php' );
      require( get_stylesheet_directory() . '/module/pickupTop.php' );
      require( get_stylesheet_directory() . '/module/newsTop.php' );
      require( get_stylesheet_directory() . '/module/contentTop.php' );
    ?>
      <section class="aboutUs">
        <div class="m-headline t-aboutUs">
          <div class="g-inner">
            <h2 class="m-headline__text">カインズについて</h2>
          </div>
        </div>
        <div class="g-inner">
          <ul class="aboutUs__list">
            <li class="aboutUs__list-item"><a href="/corporate/top_message/">
                <picture>
                  <source media="(min-width: 1025px)" srcset="<?php echo get_stylesheet_directory_uri() ?>/assets/images/top/img-about01.png">
                  <source media="(max-width: 1024px)" srcset="<?php echo get_stylesheet_directory_uri() ?>/assets/images/top/img-about01_sp.png"><img class="m-aboutUs__list-thumb" src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/top/img-about01.png" alt="トップメッセージ">
                </picture><span class="aboutUs__list-text">トップメッセージ</span></a></li>
            <li class="aboutUs__list-item"><a href="/corporate/philosophy/">
                <picture>
                  <source media="(min-width: 1025px)" srcset="<?php echo get_stylesheet_directory_uri() ?>/assets/images/top/img-about02.png">
                  <source media="(max-width: 1024px)" srcset="<?php echo get_stylesheet_directory_uri() ?>/assets/images/top/img-about02_sp.png"><img class="m-aboutUs__list-thumb" src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/top/img-about02.png" alt="企業理念">
                </picture><span class="aboutUs__list-text">企業理念</span></a></li>
            <li class="aboutUs__list-item"><a href="/corporate/policy/">
                <picture>
                  <source media="(min-width: 1025px)" srcset="<?php echo get_stylesheet_directory_uri() ?>/assets/images/top/img-about03.png">
                  <source media="(max-width: 1024px)" srcset="<?php echo get_stylesheet_directory_uri() ?>/assets/images/top/img-about03_sp.png"><img class="m-aboutUs__list-thumb" src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/top/img-about03.png" alt="行動方針">
                </picture><span class="aboutUs__list-text">行動方針</span></a></li>
            <li class="aboutUs__list-item"><a href="/corporate/outline/">
                <picture>
                  <source media="(min-width: 1025px)" srcset="<?php echo get_stylesheet_directory_uri() ?>/assets/images/top/img-about04.png">
                  <source media="(max-width: 1024px)" srcset="<?php echo get_stylesheet_directory_uri() ?>/assets/images/top/img-about04_sp.png"><img class="m-aboutUs__list-thumb" src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/top/img-about04.png" alt="会社概要">
                </picture><span class="aboutUs__list-text">会社概要</span></a></li>
            <li class="aboutUs__list-item"><a href="/corporate/access/">
                <picture>
                  <source media="(min-width: 1025px)" srcset="<?php echo get_stylesheet_directory_uri() ?>/assets/images/top/img-about05.png">
                  <source media="(max-width: 1024px)" srcset="<?php echo get_stylesheet_directory_uri() ?>/assets/images/top/img-about05_sp.png"><img class="m-aboutUs__list-thumb" src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/top/img-about05.png" alt="アクセス">
                </picture><span class="aboutUs__list-text">アクセス</span></a></li>
            <li class="aboutUs__list-item"><a href="/corporate/management/">
                <picture>
                  <source media="(min-width: 1025px)" srcset="<?php echo get_stylesheet_directory_uri() ?>/assets/images/top/img-about06.png">
                  <source media="(max-width: 1024px)" srcset="<?php echo get_stylesheet_directory_uri() ?>/assets/images/top/img-about06_sp.png"><img class="m-aboutUs__list-thumb" src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/top/img-about06.png" alt="役員一覧・組織図">
                </picture><span class="aboutUs__list-text">役員一覧・組織図</span></a></li>
            <li class="aboutUs__list-item"><a href="/corporate/history/">
                <picture>
                  <source media="(min-width: 1025px)" srcset="<?php echo get_stylesheet_directory_uri() ?>/assets/images/top/img-about07.png">
                  <source media="(max-width: 1024px)" srcset="<?php echo get_stylesheet_directory_uri() ?>/assets/images/top/img-about07_sp.png"><img class="m-aboutUs__list-thumb" src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/top/img-about07.png" alt="沿革">
                </picture><span class="aboutUs__list-text">沿革</span></a></li>
            <li class="aboutUs__list-item"><a href="/corporate/aboutbeisia/">
                <picture>
                  <source media="(min-width: 1025px)" srcset="<?php echo get_stylesheet_directory_uri() ?>/assets/images/top/img-about08.png">
                  <source media="(max-width: 1024px)" srcset="<?php echo get_stylesheet_directory_uri() ?>/assets/images/top/img-about08_sp.png"><img class="m-aboutUs__list-thumb" src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/top/img-about08.png" alt="ベイシアグループについて">
                </picture><span class="aboutUs__list-text">ベイシアグループについて</span></a></li>
          </ul>
          <ul class="aboutUs__systemList">
            <li class="aboutUs__systemList-item">
              <div class="m-btnPrimary"><a class="m-btnPrimary__link js-link-btn" href="/about/product/">商品開発</a></div>
            </li>
            <li class="aboutUs__systemList-item">
              <div class="m-btnPrimary"><a class="m-btnPrimary__link js-link-btn" href="/about/store/">店舗について</a></div>
            </li>
            <li class="aboutUs__systemList-item">
              <div class="m-btnPrimary"><a class="m-btnPrimary__link js-link-btn" href="/about/diyhr/">人事戦略</a></div>
            </li>
            <li class="aboutUs__systemList-item">
              <div class="m-btnPrimary"><a class="m-btnPrimary__link js-link-btn" href="/about/logistics/">ロジティクス</a></div>
            </li>
            <li class="aboutUs__systemList-item">
              <div class="m-btnPrimary"><a class="m-btnPrimary__link js-link-btn" href="/about/quality/">品質管理</a></div>
            </li>
            <li class="aboutUs__systemList-item">
              <div class="m-btnPrimary"><a class="m-btnPrimary__link js-link-btn" href="/about/digital/">デジタル戦略</a></div>
            </li>
            <li class="aboutUs__systemList-item">
              <div class="m-btnPrimary"><a class="m-btnPrimary__link js-link-btn" href="/about/kumimachi/">くみまち構想</a></div>
            </li>
            <li class="aboutUs__systemList-item">
              <div class="m-btnPrimary"><a class="m-btnPrimary__link js-link-btn" href="https://recruit.cainz.com/">採用</a></div>
            </li>
            <li class="aboutUs__systemList-item">
              <div class="m-btnPrimary"><a class="m-btnPrimary__link js-link-btn" href="/partners/">ビジネスパートナー募集</a></div>
            </li>
          </ul>
          <div class="aboutUs__logoBanner">
            <div class="aboutUs__logoBanner-headline">
              <h3 class="aboutUs__logoBanner-headline--title">関連サービス</h3>
            </div>
            <ul class="aboutUs__logoBanner__list">
              <li class="aboutUs__logoBanner__list-item"><a href="https://diy-style.cainz.com/" target="_blank"><img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/top/img-service01.png" alt="CAINZ DIY Style"></a></li>
              <li class="aboutUs__logoBanner__list-item"><a href="https://reserve.cainz.com/" target="_blank"><img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/top/img-service02.png" alt="CAINZ Reserve"></a></li>
              <li class="aboutUs__logoBanner__list-item"><a href="https://reform.cainz.com/" target="_blank"><img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/top/img-service03.png" alt="CAINZ Reform"></a></li>
              <li class="aboutUs__logoBanner__list-item"><a href="https://petsone.cainz.com/" target="_blank"><img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/top/img-service04.png" alt="Pet's One ペッツワン"></a></li>
              <li class="aboutUs__logoBanner__list-item"><a href="https://img.cainz.com/jp/cooking_fun/index.html" target="_blank"><img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/top/img-service05.png" alt="cooking Fun"></a></li>
              <li class="aboutUs__logoBanner__list-item"><a href="https://czpro.jp/" target="_blank"><img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/top/img-service06.png" alt="C'z PRO"></a></li>
              <li class="aboutUs__logoBanner__list-item"><a href="https://style-factory.cainz.com/" target="_blank"><img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/top/img-service07.png" alt="CAINZ Style Factory"></a></li>
            </ul>
          </div>
          <div class="aboutUs__logoBanner">
            <div class="aboutUs__logoBanner-headline">
              <h3 class="aboutUs__logoBanner-headline--title">メディア</h3>
            </div>
            <ul class="aboutUs__logoBanner__list">
              <li class="aboutUs__logoBanner__list-item"><a href="https://magazine.cainz.com/" target="_blank"><img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/top/img-media01.png" alt="となりのカインズさん"></a></li>
              <li class="aboutUs__logoBanner__list-item"><a href="https://www.youtube.com/@cainztv" target="_blank"><img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/top/img-media02.png" alt="CAINZ TV"></a></li>
              <li class="aboutUs__logoBanner__list-item"><a href="https://www.wanqol.com/" target="_blank"><img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/top/img-media03.png" alt="WanQol"></a></li>
            </ul>
          </div>
          <div class="aboutUs__logoBanner">
            <div class="aboutUs__logoBanner-headline">
              <h3 class="aboutUs__logoBanner-headline--title">グループ企業</h3>
            </div>
            <ul class="aboutUs__logoBanner__list">
              <li class="aboutUs__logoBanner__list-item"><a href="https://www.beisia.co.jp/" target="_blank"><img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/top/img-group01.png" alt="Beisia"></a></li>
              <li class="aboutUs__logoBanner__list-item"><a href="https://www.workman.co.jp/" target="_blank"><img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/top/img-group02.png" alt="WORKMAN"></a></li>
              <li class="aboutUs__logoBanner__list-item"><a href="https://hands.net/" target="_blank"><img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/top/img-group03.png" alt="HANDS"></a></li>
              <li class="aboutUs__logoBanner__list-item"><a href="https://www.saveon.co.jp/" target="_blank"><img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/top/img-group04.png" alt="SAVE ON"></a></li>
              <li class="aboutUs__logoBanner__list-item"><a href="https://www.autors.co.jp/" target="_blank"><img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/top/img-group05.png" alt="AUTER's"></a></li>
              <li class="aboutUs__logoBanner__list-item"><a href="https://beisiadenki.jp/" target="_blank"><img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/top/img-group06.png" alt="ベイシア電器"></a></li>
              <li class="aboutUs__logoBanner__list-item"><a href="https://www.seikando-b.co.jp/" target="_blank"><img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/top/img-group07.png" alt="清閑堂"></a></li>
              <li class="aboutUs__logoBanner__list-item"><a href="https://www.cainz.co.jp/" target="_blank"><img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/top/img-group08.png" alt="ベイシアスポーツクラブ"></a></li>
              <li class="aboutUs__logoBanner__list-item"><a href="https://cainztravel.jp/" target="_blank"><img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/top/img-group09.png" alt="CAINZ Travel"></a></li>
              <li class="aboutUs__logoBanner__list-item"><a href="https://be-rri.jp/" target="_blank"><img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/top/img-group10.png" alt="Beisia 流通技術研究所"></a></li>
            </ul>
          </div>
        </div>
      </section>
<?php get_footer();
