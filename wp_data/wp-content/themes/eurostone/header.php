<!DOCTYPE html>
<html <?php language_attributes(); ?>>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="preload" href="<?php echo get_stylesheet_directory_uri() ?>/assets/css/slick.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
      <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri() ?>/assets/css/slick.css">
    </noscript>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500;600;700&amp;display=swap" rel="stylesheet">
    <link as="image" rel="preload" href="<?php echo get_stylesheet_directory_uri() ?>/assets/images/logo.svg" fetchpriority="high">
    <?php wp_head(); ?>
  </head>
  <body <?php echo bodyClass(); ?>>
  <header class="m-header">
    <div class="inner">
      <div class="m-header__top">
        <div class="m-header__logo">
          <?php if(is_front_page()) : ?>
            <h1>
              <a href="<?php echo home_url() ?>">
                <img loading="lazy"  src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/logo.svg" alt="EuroStrone" width="280" height="62">
              </a>
            </h1>
          <?php  else : ?>
            <a href="<?php echo home_url() ?>">
              <img loading="lazy"  src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/logo.svg" alt="EuroStrone" width="280" height="62">
            </a>
          <?php endif; ?>
        </div>
        <div class="m-header__boxright">
          <form action="/" method="get" class="m-header__search">
            <input type="text" id="s" name="s" placeholder="Tìm kiếm" aria-label="Tìm kiếm">
            <button type="submit" name="submit" class="btn_search" aria-label="Tìm kiếm"></button>
          </form>
          <div class="m-header__phone"><a href="tel:0903930126"><span>0903 930 126</span></a></div>
          <div class="m-header__mail"> <a href="mailto:info@eurostone.vn"><span>info@eurostone.vn</span></a></div>
          <ul class="translate">
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
          <li><a href="#">Giới Thiệu</a></li>
          <li><a href="<?php echo home_url('/colors/'); ?>">Màu Sắc</a></li>
          <li class="sub sub_tab"><span>Thương Hiệu</span>
            <div class="m-header__submenu">
              <ul class="list_tab only_pc">
                <li class="active">Đá Dekton</li>
                <li>Đá Silestone</li>
                <li>Đá Marble - Cẩm thạch</li>
              </ul>
              <div class="inner">
                <div class="menu_tab active">
                  <p class="ttl only_sp">Silestone</p>
                  <div class="tab_cont">
                    <div class="box-left">
                      <figure> <img loading="lazy"  src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/menu_silestone.jpg" alt="silestone"></figure>
                      <div class="sec">
                        <p class="ttl">Dekton</p>
                        <p class="txt">text</p>
                      </div>
                    </div>
                    <div class="box-right">
                      <div class="tab_link">
                        <dl>
                          <dt>Dekton Information</dt>
                          <dd>
                            <p> <a href="<?php echo home_url('/da-nung-ket-dekton/ban-lavabo/'); ?>">Bàn lavabo dekton</a></p>
                            <p> <a href="<?php echo home_url('/da-nung-ket-dekton/khay-tam/'); ?>">Khay tắm dekton</a></p>
                            <p> <a href="<?php echo home_url('/da-nung-ket-dekton/lat-nen/'); ?>">Lát nền Dekton</a></p>
                            <p> <a href="<?php echo home_url('/da-nung-ket-dekton/mat-ban-bep-ngoai-troi/'); ?>">Mặt bàn bếp ngoài trời dekton</a></p>
                            
                          </dd>
                        </dl>
                      </div>
                      <div class="tab_link">
                        <dl>
                          <dt>Dekton Information</dt>
                          <dd>
                           
                            <p> <a href="<?php echo home_url('/da-nung-ket-dekton/op-cau-thang/'); ?>">Ốp cầu thang dekton</a></p>
                            <p> <a href="<?php echo home_url('/da-nung-ket-dekton/op-tuong/'); ?>">Ốp tường dekton</a></p>
                            <p> <a href="<?php echo home_url('/da-nung-ket-dekton/op-tu-bep/'); ?>">Ốp tủ bếp dekton</a></p>
                          </dd>
                        </dl>
                      </div>
                      <div class="tab_link">
                        <dl>
                          <dt>Dekton Information</dt>
                          <dd>
                            <p> <a href="<?php echo home_url('/da-nung-ket-dekton/mat-ban-bep/'); ?>">Mặt bàn bếp Dekton</a></p>
                            <p> <a href="<?php echo home_url('/da-nung-ket-dekton/noi-that/'); ?>">Nội thất Dekton</a></p>
                            <p> <a href="<?php echo home_url('/da-nung-ket-dekton/mat-ban-phong-tam/'); ?>">Mặt bàn phòng tắm dekton</a></p>
                          </dd>
                        </dl>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="menu_tab">
                  <p class="ttl only_sp">Silestone</p>
                  <div class="tab_cont">
                    <div class="box-left">
                      <figure> <img loading="lazy"  src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/menu_silestone.jpg" alt="silestone"></figure>
                      <div class="sec">
                        <p class="ttl">SILESTONE</p>
                        <p class="txt">text</p>
                      </div>
                    </div>
                    <div class="box-right">
                      <div class="tab_link">
                        <dl>
                          <dt>Silestone Information</dt>
                          <dd>
                            <p> <a href="<?php echo home_url('da-thach-anh-nhan-tao-silestone/ban-lavabo/'); ?>">Bàn Lavabo Silestone</a></p>
                            <p> <a href="<?php echo home_url('/da-thach-anh-nhan-tao-silestone/da-silestone-mau-den/'); ?>">Bồn rửa bếp Silestone</a></p>
                            <p> <a href="<?php echo home_url('/da-thach-anh-nhan-tao-silestone/khay-tam/'); ?>">Khay tắm Đá Silestone</a></p>
                            <p> <a href="<?php echo home_url('/da-thach-anh-nhan-tao-silestone/lat-nen/'); ?>">Lát nền Đá Silestone</a></p>
                          </dd>
                        </dl>
                      </div>
                      <div class="tab_link">
                        <dl>
                          <dt>Silestone Information</dt>
                          <dd>
                            <p> <a href="<?php echo home_url('/da-thach-anh-nhan-tao-silestone/mat-ban-bep/'); ?>">Mặt bàn bếp Silestone</a></p>
                            <p> <a href="<?php echo home_url('/da-thach-anh-nhan-tao-silestone/mat-ban-phong-tam/'); ?>">Mặt bàn phòng tắm Silestone</a></p>
                            <p> <a href="<?php echo home_url('/da-thach-anh-nhan-tao-silestone/noi-that/'); ?>">Nội thất Đá Silestone</a></p>
                            <p> <a href="<?php echo home_url('/da-thach-anh-nhan-tao-silestone/op-tuong/'); ?>">Ốp tường Đá Silestone</a></p>
                          </dd>
                        </dl>
                      </div>
                      <div class="tab_link">
                        <dl>
                          <dt>Silestone Information</dt>
                          <dd>
                            <p> <a href="<?php echo home_url('/da-thach-anh-nhan-tao-silestone/huong-dan-ve-sinh/'); ?>">Hướng dẫn vệ sinh Silestone</a></p>
                            <p> <a href="<?php echo home_url('/da-thach-anh-nhan-tao-silestone/chinh-sach-bao-hanh/'); ?>">Chính sách bảo hành Silestone</a></p>
                          </dd>
                        </dl>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="menu_tab">
                  <p class="ttl only_sp">Đá Marble - Cẩm thạch</p>
                  <div class="tab_cont">
                    <div class="box-left">
                      <figure> <img loading="lazy"  src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/menu_silestone.jpg" alt="silestone"></figure>
                      <div class="sec">
                        <p class="ttl">Đá Marble - Cẩm thạch</p>
                        <p class="txt">text</p>
                      </div>
                    </div>
                    <div class="box-right">
                      <div class="tab_link">
                        <dl>
                          <dt>Marble Information</dt>
                          <dd>
                            <p> <a href="<?php echo home_url('/da-marble-cam-thach/op-cau-thang/'); ?>">Cầu thang Đá Marble</a></p>
                            <p> <a href="<?php echo home_url('/da-marble-cam-thach/lat-nen/'); ?>">Lát nền Đá Marble</a></p>
                            <p> <a href="<?php echo home_url('/da-marble-cam-thach/mat-ban/'); ?>">Mặt bàn Đá Marble</a></p>
                          </dd>
                        </dl>
                      </div>
                      <div class="tab_link">
                        <dl>
                          <dt>Marble Information</dt>
                          <dd>
                            <p> <a href="<?php echo home_url('/da-marble-cam-thach/noi-that/'); ?>">Nội thất Đá Marble</a></p>
                            <p> <a href="<?php echo home_url('/da-marble-cam-thach/op-mat-tien/'); ?>">Ốp mặt tiền Đá Marble</a></p>
                            <p> <a href="<?php echo home_url('/da-marble-cam-thach/op-tuong/'); ?>">Ốp tường Đá Marble</a></p>
                          </dd>
                        </dl>
                      </div>
                      <div class="tab_link">
                        <dl>
                          <dt>Marble Information</dt>
                          <dd>
                            <p> <a href="<?php echo home_url('/da-marble-cam-thach/op-phong-tam/'); ?>">Phòng tắm ốp Đá Marble</a></p>
                            <p> <a href="<?php echo home_url('/da-marble-cam-thach/op-thang-may/'); ?>">Thang máy Đá Marble</a></p>
                            <p> <a href="<?php echo home_url('/da-marble-cam-thach/op-vach-tivi/'); ?>">Vách tivi Đá Marble</a></p>
                          </dd>
                        </dl>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </li>
          <li class="sub"><span>Hạng Mục Ốp Đá</span>
            <div class="m-header__submenu">
              <div class="inner">
                <ul class="menu-slider">
                  <li class="items">
                    <a href="<?php echo home_url('/ban-an-mat-da/'); ?>">
                      <figure> 
                        <img loading="lazy"  src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/menu_img01.jpg" alt="Bàn ăn mặt đá">
                        <figcaption>Bàn ăn mặt đá</figcaption>
                      </figure>
                      <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
                    </a>
                  </li>
                  <li class="items">
                    <a href="<?php echo home_url('/ban-da-lavabo/'); ?>">
                      <figure> 
                        <img loading="lazy"  src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/menu_img02.jpg" alt="Bàn đá lavabo">
                        <figcaption>Bàn đá lavabo</figcaption>
                      </figure>
                    </a>
                  </li>
                  <li class="items">
                    <a href="<?php echo home_url('/da-lat-nen/'); ?>">
                      <figure> 
                        <img loading="lazy"  src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/menu_img03.jpg" alt="Đá lát nền">
                        <figcaption>Đá lát nền</figcaption>
                      </figure>
                    </a>
                  </li>
                  <li class="items">
                    <a href="<?php echo home_url('/da-lat-san-vuon/'); ?>">
                      <figure> 
                        <img loading="lazy"  src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/menu_img04.jpg" alt="Đá lát sân vườn">
                        <figcaption>Đá lát sân vườn</figcaption>
                      </figure>
                    </a>
                  </li>
                  <li class="items">
                    <a href="<?php echo home_url('/da-op-bep/'); ?>">
                      <figure> 
                        <img loading="lazy"  src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/menu_img04.jpg" alt="Đá Ốp Bếp">
                        <figcaption>Đá Ốp Bếp</figcaption>
                      </figure>
                    </a>
                  </li>
                  <li class="items">
                    <a href="<?php echo home_url('/da-op-cau-thang/'); ?>">
                      <figure> 
                        <img loading="lazy"  src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/menu_img04.jpg" alt="Đá ốp cầu thang">
                        <figcaption>Đá ốp cầu thang</figcaption>
                      </figure>
                    </a>
                  </li>
                  <li class="items">
                    <a href="<?php echo home_url('/da-op-cot/'); ?>">
                      <figure> 
                        <img loading="lazy"  src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/menu_img04.jpg" alt="Đá ốp cột">
                        <figcaption>Đá ốp cột</figcaption>
                      </figure>
                    </a>
                  </li>
                  <li class="items">
                    <a href="<?php echo home_url('/da-op-mat-tien/'); ?>">
                      <figure> 
                        <img loading="lazy"  src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/menu_img04.jpg" alt="Đá ốp mặt tiền">
                        <figcaption>Đá ốp mặt tiền</figcaption>
                      </figure>
                    </a>
                  </li>
                  <li class="items">
                    <a href="<?php echo home_url('/da-op-phong-tam/'); ?>">
                      <figure> 
                        <img loading="lazy"  src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/menu_img04.jpg" alt="Đá ốp phòng tắm">
                        <figcaption>Đá ốp phòng tắm</figcaption>
                      </figure>
                    </a>
                  </li>
                  <li class="items">
                    <a href="<?php echo home_url('/	da-op-tam-cap/'); ?>">
                      <figure> 
                        <img loading="lazy"  src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/menu_img04.jpg" alt="Đá ốp tam cấp">
                        <figcaption>Đá ốp tam cấp</figcaption>
                      </figure>
                    </a>
                  </li>
                  <li class="items">
                    <a href="<?php echo home_url('/da-op-thang-may/'); ?>">
                      <figure> 
                        <img loading="lazy"  src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/menu_img04.jpg" alt="Đá ốp thang máy">
                        <figcaption>Đá ốp thang máy</figcaption>
                      </figure>
                    </a>
                  </li>
                  <li class="items">
                    <a href="<?php echo home_url('/da-op-tuong/'); ?>">
                      <figure> 
                        <img loading="lazy"  src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/menu_img04.jpg" alt="Đá ốp tường">
                        <figcaption>Đá ốp tường</figcaption>
                      </figure>
                    </a>
                  </li>
                  <li class="items">
                    <a href="<?php echo home_url('/da-op-vach-tivi/'); ?>">
                      <figure> 
                        <img loading="lazy"  src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/menu_img04.jpg" alt="Đá ốp vách tivi">
                        <figcaption>Đá ốp vách tivi</figcaption>
                      </figure>
                    </a>
                  </li>
                </ul>
              </div>
            </div>
          </li>
          <li class="sub"><span>Giả Lập Không Gian</span>
            <div class="m-header__submenu">
              <ul class="submenu submenu--01">
                <li>
                  <a href="#">
                    <figure> 
                      <img loading="lazy"  src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/menu_img05.jpg" alt="Hình dung trực tuyến test test">
                      <figcaption>Hình dung trực tuyến test test</figcaption>
                    </figure>
                  </a>
                </li>
                <li>
                  <a href="#">
                    <figure> 
                      <img loading="lazy" src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/menu_img06.jpg" alt="Nhà bếp 3D">
                      <figcaption>Nhà bếp 3D</figcaption>
                    </figure>
                  </a>
                </li>
                <li>
                  <a href="#">
                    <figure>
                      <img loading="lazy"  src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/menu_img07.jpg" alt="Bảng thiết kế">
                      <figcaption>Bảng thiết kế</figcaption>
                    </figure>
                  </a>
                </li>
              </ul>
            </div>
          </li>
          <li class="sub"><span>Tin Tức</span>
            <div class="m-header__submenu">
              <ul class="submenu submenu--02">
                <li>
                  <a href="<?php echo home_url('/tin-tuc/'); ?>">
                    <figure> 
                      <img loading="lazy"  src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/menu_img08.jpg" alt="Blog &amp; Sự kiện">
                      <figcaption>Blog &amp; Sự kiện</figcaption>
                    </figure>
                  </a>
                </li>
                <li>
                  <a href="<?php echo home_url('/eurostone/'); ?>">
                    <figure> 
                      <img loading="lazy"  src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/menu_img09.jpg" alt="Tin tức Eurostone">
                      <figcaption>Tin tức Eurostone</figcaption>
                    </figure>
                  </a>
                </li>
                <li>
                  <a href="<?php echo home_url('/tuyen-dung/'); ?>">
                    <figure> 
                      <img loading="lazy"  src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/menu_img10.jpg" alt="Tuyển Dụng">
                      <figcaption>Tuyển Dụng</figcaption>
                    </figure>
                  </a>
                </li>
                <li>
                  <a href="<?php echo home_url('/wiki/'); ?>">
                    <figure> 
                      <img loading="lazy"  src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/menu_img12.jpg" alt="wiki">
                      <figcaption>Wiki</figcaption>
                    </figure>
                  </a>
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