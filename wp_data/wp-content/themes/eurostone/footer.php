<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package recruit
 */

?>
    </main>
    <footer class="m-footer">
      <div class="m-footer__inner">
        <div class="m-footer__logo">
          <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/common/logo_footer.svg" alt="CAINZ">
        </div>
        <div class="m-footer__sitemap">
          <nav class="m-footer__sitemap-item">
                <ul>
                  <li><a href="/corporate/">企業情報</a>
                  </li>
                  <li><a href="/about/">カインズについて</a>
                  </li>
                  <li><a href="/news/">ニュースリリース</a>
                  </li>
                  <li><a href="/contents/">コンテンツ</a>
                  </li>
                  <li><a href="/partner/">ビジネスパートナー募集</a>
                  </li>
                </ul>
          </nav>
          <nav class="m-footer__sitemap-item">
                <ul>
                  <li><a href="https://recruit.cainz.com/" target="_blank">採用情報</a>
                  </li>
                  <li><a href="https://www.cainz.com/" target="_blank">オンラインストア</a>
                  </li>
                  <li><a href="https://map.cainz.com/" target="_blank">店舗検索</a>
                  </li>
                  <li><a href="http://www.21zaidan.or.jp/" target="_blank">ベイシア21世紀財団</a>
                  </li>
                  <li><a href="https://www.thespa.co.jp/" target="_blank">ザスパクサツ群馬<br>オフィシャルサイト</a>
                  </li>
                </ul>
          </nav>
          <div class="m-footer__sitemap-sns">
            <div class="m-footer__sitemap-sns--title">公式SNSアカウント</div>
            <ul class="m-footer__sitemap-sns--list">
                  <li>
                    <a href="https://www.facebook.com/cainzfun/" target="_blank">
                      <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/common/icon_facebook.svg" alt="CAINZ Facebook">
                    </a>
                  </li>
                  <li>
                    <a href="https://twitter.com/cainz_official/" target="_blank">
                      <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/common/icon_twitter.svg" alt="CAINZ Twitter">
                    </a>
                  </li>
                  <li>
                    <a href="https://www.instagram.com/cainz_official/" target="_blank">
                      <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/common/icon_instagram.svg" alt="CAINZ Instagram">
                    </a>
                  </li>
                  <li>
                    <a href="https://www.youtube.com/@cainztv/" target="_blank">
                      <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/common/icon_youtube.svg" alt="CAINZ Youtube">
                    </a>
                  </li>
            </ul>
            <a class="m-footer__sitemap-policyLink" href="https://policies.cainz.com/privacy/">プライバシーポリシー</a>
          </div>
        </div>
        <div class="m-footer__logoSp">
          <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/common/logo_header.svg" alt="CAINZ">
        </div>
        <div class="m-menuToggle">
          <div class="m-menuToggle__content">
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
                <li><a href="https://translate.google.com/translate?hl=&amp;sl=ja&amp;tl=en&amp;u=https://www.cainz.co.jp/" target="_blank">EN</a></li>
              </ul>
            </nav>
          </div>
        </div>
        <div class="m-footer__wrap">
          <?php if(is_front_page()) : ?>
            <div class="m-cart-button-block js-cart-btn">
              <a class="m-btn m-btnCart" href="https://www.cainz.com/" target="_blank">オンラインショップ<span class="m-icon__cart">
                <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/common/icon_shopping_cart.svg" alt="オンラインショップ"></span>
              </a>
              <a class="m-btn m-btnShop" href="https://map.cainz.com/" target="_blank">店舗検索<span class="m-icon__shop">
                <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/common/icon_shop.svg" alt="店舗検索"></span>
              </a>
            </div>
           <?php endif; ?>
          <button class="m-btnBackTop js-backtop-btn" type="button">ページの先頭に戻る<img class="m-btnBackTop__icon" src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/common/btn_backtotop.svg" alt="ページの先頭に戻る"></button>
          <div class="m-footer__snsMobile">
            <div class="m-footer__snsMobile-title">公式SNSアカウント</div>
            <ul class="m-footer__snsMobile-list">
                  <li><a href="https://www.facebook.com/cainzfun/" target="_blank"><img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/common/icon_facebook.svg" alt="CAINZ Facebook"></a></li>
                  <li><a href="https://twitter.com/cainz_official/" target="_blank"><img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/common/icon_twitter.svg" alt="CAINZ Twitter"></a></li>
                  <li><a href="https://www.instagram.com/cainz_official/" target="_blank"><img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/common/icon_instagram.svg" alt="CAINZ Instagram"></a></li>
                  <li><a href="https://www.youtube.com/@cainztv/" target="_blank"><img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/common/icon_youtube.svg" alt="CAINZ Youtube"></a></li>
            </ul><a class="m-footer__snsMobile-policyLink" href="https://policies.cainz.com/privacy/">プライバシーポリシー</a>
          </div>
        </div>
      </div>
      <div class="m-footer__copyright">
        <p class="m-footer__copyright-text">Copyright © CAINZ All rights reserved.</p>
      </div>
    </footer>
    <script src="<?php echo get_stylesheet_directory_uri() ?>/assets/js/libs/lib.js"></script>
    <script src="<?php echo get_stylesheet_directory_uri() ?>/assets/js/main.js"></script>
    <?php
    wp_footer();
    echo generateSchemaFormat();
    ?>
  </body>
</html>