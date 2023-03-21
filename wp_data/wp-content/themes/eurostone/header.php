<!DOCTYPE html>
<html <?php language_attributes(); ?>>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Trang chủ</title>
    <link rel="preload" href="<?php echo get_stylesheet_directory_uri() ?>/assets/css/slick.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
      <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri() ?>/assets/css/slick.css">
    </noscript>
    <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri() ?>/assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&amp;display=swap" rel="stylesheet">
    <link as="image" rel="preload" href="<?php echo get_stylesheet_directory_uri() ?>/assets/images/logo.svg" fetchpriority="high">
    <!-- Primary Meta Tags-->
    <meta name="title" content="Trang chủ test">
    <meta name="description" content="Trang chủ - Spiderum trang tin tức">
    <!-- Open Graph / Facebook-->
    <meta property="og:type" content="website">
    <meta property="og:url" content="">
    <meta property="og:title" content="Trang chủ test">
    <meta property="og:description" content="Trang chủ - Spiderum trang tin tức">
    <meta property="og:image" content="https://images.unsplash.com/photo-1629927506216-fcdf656d74de?ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&amp;ixlib=rb-1.2.1&amp;auto=format&amp;fit=crop&amp;w=1500&amp;q=80">
    <!-- Twitter-->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="">
    <meta property="twitter:title" content="Trang chủ test">
    <meta property="twitter:description" content="Trang chủ - Spiderum trang tin tức">
    <meta property="twitter:image" content="https://images.unsplash.com/photo-1629927506216-fcdf656d74de?ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&amp;ixlib=rb-1.2.1&amp;auto=format&amp;fit=crop&amp;w=1500&amp;q=80"> <?php wp_head(); ?>
  </head>
  <body <?php echo bodyClass(); ?>>
  <header class="m-header">
      <div class="inner">
        <div class="m-header__top">
          <div class="m-header__logo"><a href="/"><img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/logo.svg" alt="EuroStrone" width="280" height="62"></a></div>
          <div class="m-header__boxright">
            <div class="m-header__search">
              <input type="text" placeholder="tìm kiếm">
              <p class="btn_search"></p>
            </div>
            <div class="m-header__phone"><a href="tel:0903930126"><span>0903 930 126</span></a></div>
            <div class="m-header__mail"> <a href="mailto:info@eurostone.vn"><span>info@eurostone.vn</span></a></div>
            <ul>
              <?php
              if ( function_exists('pll_the_languages') ) {
                  pll_the_languages(array('show_flags'=>1,'show_names'=>0));
                }
              ?>
            </ul>
          </div>
        </div>
        <div class="m-header__bottom">
          <ul class="m-header__bottom--menu">
            <li><a href="#">Giới thiệu</a></li>
            <li><a href="#">Màu Sắc</a></li>
            <li><a href="#">Giới thiệu</a></li>
            <li><a href="#">Brands</a></li>
            <li class="sub"><span>Hạng Mục Ốp Đá</span>
              <div class="m-header__submenu">
                <div class="inner">
                  <ul class="menu-slider">
                    <li class="items"><a href="#">
                            <figure> <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/menu_img01.jpg" alt="Phòng bếp test test test test test">
                              <figcaption>Phòng bếp test test test test test</figcaption>
                            </figure>
                            <p>text text text text test test test test test</p></a>
                    </li>
                    <li class="items"><a href="#">
                            <figure> <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/menu_img02.jpg" alt="Phòng bếp">
                              <figcaption>Phòng bếp</figcaption>
                            </figure></a>
                    </li>
                    <li class="items"><a href="#">
                            <figure> <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/menu_img03.jpg" alt="Phòng bếp">
                              <figcaption>Phòng bếp</figcaption>
                            </figure></a>
                    </li>
                    <li class="items"><a href="#">
                            <figure> <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/menu_img04.jpg" alt="Phòng bếp">
                              <figcaption>Phòng bếp</figcaption>
                            </figure></a>
                    </li>
                    <li class="items"><a href="#">
                            <figure> <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/menu_img04.jpg" alt="Phòng bếp">
                              <figcaption>Phòng bếp</figcaption>
                            </figure></a>
                    </li>
                  </ul>
                </div>
              </div>
            </li>
            <li class="sub"><span>Giả Lập Không Gian</span>
              <div class="m-header__submenu">
                <ul class="submenu submenu--01">
                  <li><a href="#">
                          <figure> <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/menu_img05.jpg" alt="Hình dung trực tuyến test test">
                            <figcaption>Hình dung trực tuyến test test</figcaption>
                          </figure></a>
                  </li>
                  <li><a href="#">
                          <figure> <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/menu_img06.jpg" alt="Nhà bếp 3D">
                            <figcaption>Nhà bếp 3D</figcaption>
                          </figure></a>
                  </li>
                  <li><a href="#">
                          <figure> <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/menu_img07.jpg" alt="Bảng thiết kế">
                            <figcaption>Bảng thiết kế</figcaption>
                          </figure></a>
                  </li>
                </ul>
              </div>
            </li>
            <li class="sub"><span>Tin Tức</span>
              <div class="m-header__submenu">
                <ul class="submenu submenu--02">
                  <li><a href="#">
                          <figure> <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/menu_img08.jpg" alt="Blog &amp; Sự kiện">
                            <figcaption>Blog &amp; Sự kiện</figcaption>
                          </figure></a>
                  </li>
                  <li><a href="#">
                          <figure> <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/menu_img09.jpg" alt="Mẫu nhà đẹp">
                            <figcaption>Mẫu nhà đẹp</figcaption>
                          </figure></a>
                  </li>
                  <li><a href="#">
                          <figure> <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/menu_img10.jpg" alt="Bảng thiết kế">
                            <figcaption>Bảng thiết kế</figcaption>
                          </figure></a>
                  </li>
                  <li><a href="#">
                          <figure> <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/menu_img12.jpg" alt="Bảng thiết kế">
                            <figcaption>Bảng thiết kế</figcaption>
                          </figure></a>
                  </li>
                  <li><a href="#">
                          <figure> <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/menu_img11.jpg" alt="Bảng thiết kế">
                            <figcaption>Bảng thiết kế</figcaption>
                          </figure></a>
                  </li>
                </ul>
              </div>
            </li>
            <li><a href="#">Cosentino City</a></li>
            <li><a href="#">Professionals</a></li>
          </ul>
        </div>
        <div class="hamburger"><span> </span><span></span><span></span></div>
      </div>
    </header>
    <main class="l-main">