<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package recruit
 */
$primary_category = get_primary_taxonomy_term( get_the_ID(), 'brand' );
$brand = $primary_category ? $primary_category['title'] : '';
$brand_url = $primary_category ? $primary_category['slug'] : '';
$brand_bg = get_background_taxonomy('brand', $primary_category['id']);
$image_gallery = get_field('product_image_gallery');
$sku = get_field('product_code');
?>
      <section id="detail_mainv">
        <div class="m-mainv01" style="<?php echo $brand_bg; ?>">
          <h2><?php echo $brand ?></h2>
        </div>
        <ul class="breadcrumb">
          <li>
            <a href="<?php echo home_url(); ?>"><?php _e('Home', 'eurostone'); ?></a>
          </li>
          <li>
            <a href="<?php echo $brand_url ?>"><?php echo $brand ?></a>
          </li>
          <li><?php the_title(); ?></li>
        </ul>
      </section>
      <section id="detail_box01">
        <div class="inner">
          <div class="m-blockdetail">
            <div class="detail-slider">
              <ul class="slider-for">
                <li class="items">
                  <?php handle_thumbnail('FEATURES-PRODUCT-THUMB') ?>
                </li>
                <?php
                if($image_gallery) :
                  foreach( $image_gallery as $image ):
                  ?>
                  <li class="items">
                    <?php  handle_thumbnail_id($image['ID'], 'FEATURES-PRODUCT-THUMB', get_the_title() ); ?>
                  </li>
                <?php
                  endforeach;
                endif;
                ?>
              </ul>
              <ul class="slider-nav">
                <li class="items">
                  <?php handle_thumbnail('NEWS-THUMBNAIL') ?>
                </li>
                <?php
                if($image_gallery) :
                  foreach( $image_gallery as $image ):
                  ?>
                  <li class="items">
                    <?php  handle_thumbnail_id($image['ID'], 'NEWS-THUMBNAIL', get_the_title() ); ?>
                  </li>
                <?php
                  endforeach;
                endif;
                ?>
              </ul>
            </div>
            <div class="m-blockdetail__cont">
              <div class="block-top">
                <h4><?php the_title(); ?></h4>
                <ul class="list-tag">
                  <li><?php _e('Product SKU : ', 'eurostone'); ?><span class="bold"><?php echo $sku ?></span>
                  </li>
                  <li>Dekton - Onirika</li>
                </ul>
                <div class="m-blockdetail__tab">
                  <ul class="tab-ttl">
                    <li class="active"><?php _e('Description', 'eurostone'); ?></li>
                    <li><?php _e('Specifications', 'eurostone'); ?></li>
                  </ul>
                  <div class="tab-cont">
                    <div class="tab-frame active">
                      <div class="sec">
                        <p><?php echo get_the_excerpt(); ?></p>
                      </div>
                    </div>
                    <div class="tab-frame">
                      <div class="tab-list">
                        <dl>
                          <dt>Kích thước tiêu chuẩn (normal size)</dt>
                          <dd>303cm x 143cm (119” x 56”) 303cm x 143cm (119” x 56”)303cm x 143cm (119” x 56”)303cm x 143cm (119” x 56”)</dd>
                        </dl>
                        <dl>
                          <dt>Kích thước khổ lớn (jumbo size) </dt>
                          <dd>330cm x 165cm (130” x 65”)</dd>
                        </dl>
                        <dl>
                          <dt>ĐỘ DÀY TIÊU CHUẨN</dt>
                          <dd>20mm (3/4”) <br>30mm (1 1/6”) </dd>
                        </dl>
                        <dl>
                          <dt>CÁC LOẠI BỀ MẶT</dt>
                          <dd>Polished</dd>
                        </dl>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="gr_btn">
                <div class="c-btn01 c-btn01--01">
                  <a href="#">LIÊN HỆ NGAY</a>
                </div>
                <div class="c-btn01">
                  <a href="#">CỬA HÀNG</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
      <section id="detail_box02">
        <div class="inner">
          <ul class="list-fea">
            <li>
              <figure>
                <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/detail/icon01.png" alt="chống trầy">
              </figure>
              <p>chống trầy</p>
            </li>
            <li>
              <figure>
                <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/detail/icon02.png" alt="siêu bền">
              </figure>
              <p>siêu bền</p>
            </li>
            <li>
              <figure>
                <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/detail/icon03.png" alt="độ cứng  Cao">
              </figure>
              <p>độ cứng Cao</p>
            </li>
            <li>
              <figure>
                <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/detail/icon04.png" alt="Chống thấm">
              </figure>
              <p>Chống thấm</p>
            </li>
          </ul>
          <div class="reference_block">
            <div class="reference_cont">
              <h3>BẠN CẦN THAM KHẢO GIÁ?</h3>
              <p>Liên hệ ngay với chúng tôi để nhận báo giá chi tiết nhất phù hợp với nhu cầu của quý khách.</p>
              <div class="c-btn01 c-btn01--01">
                <a href="#">NHẬN BÁO GIÁ</a>
              </div>
            </div>
            <figure class="reference_img">
              <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/detail/detail_img01.jpg" alt="NHẬN BÁO GIÁ">
            </figure>
          </div>
          <div class="viewmore_block">
            <div class="viewmore_cont">
              <p>text text text </p>
              <p>text text text</p>
            </div>
            <div class="viewmore_btn">
              <span>XEM THÊM</span>
            </div>
          </div>
        </div>
      </section>
      <section id="detail_box03">
        <div class="bg-wave"></div>
        <div class="inner">
          <h3 class="c-title02">Sản Phẩm Tương Tự</h3>
          <ul class="c-slider01 slider01">
            <li class="items">
              <a href="#">
                <figure>
                  <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/stone_img01.jpg" alt="Đá Marble Xanh (Green)">
                </figure>
                <div class="sec">
                  <h4>Đá Marble Xanh (Green)</h4>
                  <ul class="list-tag">
                    <li>India</li>
                    <li>Lớn</li>
                    <li>1.6 - 1.8cm</li>
                  </ul>
                </div>
              </a>
            </li>
            <li class="items">
              <a href="#">
                <figure>
                  <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/stone_img01.jpg" alt="Đá Marble Xanh (Green)">
                </figure>
                <div class="sec">
                  <h4>Đá Marble Xanh (Green)</h4>
                  <ul class="list-tag">
                    <li>India</li>
                    <li>Lớn</li>
                    <li>1.6 - 1.8cmf</li>
                  </ul>
                </div>
              </a>
            </li>
            <li class="items">
              <a href="#">
                <figure>
                  <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/stone_img01.jpg" alt="Đá Marble Xanh (Green)">
                </figure>
                <div class="sec">
                  <h4>Đá Marble Xanh (Green)</h4>
                  <ul class="list-tag">
                    <li>India</li>
                    <li>Lớn</li>
                    <li>1.6 - 1.8cm</li>
                  </ul>
                </div>
              </a>
            </li>
            <li class="items">
              <a href="#">
                <figure>
                  <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/stone_img01.jpg" alt="Đá Marble Xanh (Green)">
                </figure>
                <div class="sec">
                  <h4>Đá Marble Xanh (Green)</h4>
                  <ul class="list-tag">
                    <li>India</li>
                    <li>Lớn</li>
                    <li>1.6 - 1.8cm</li>
                  </ul>
                </div>
              </a>
            </li>
            <li class="items">
              <a href="#">
                <figure>
                  <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/stone_img01.jpg" alt="Đá Marble Xanh (Green)">
                </figure>
                <div class="sec">
                  <h4>Đá Marble Xanh (Green)</h4>
                  <ul class="list-tag">
                    <li>India</li>
                    <li>Lớn</li>
                    <li>1.6 - 1.8cm</li>
                  </ul>
                </div>
              </a>
            </li>
            <li class="items">
              <a href="#">
                <figure>
                  <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/stone_img01.jpg" alt="Đá Marble Xanh (Green)">
                </figure>
                <div class="sec">
                  <h4>Đá Marble Xanh (Green)</h4>
                  <ul class="list-tag">
                    <li>India</li>
                    <li>Lớn</li>
                    <li>1.6 - 1.8cm</li>
                  </ul>
                </div>
              </a>
            </li>
          </ul>
        </div>
      </section>
      <section id="detail_box04">
        <div class="inner">
          <div class="block">
            <h3 class="c-title03">Bồn rửa chén</h3>
            <ul class="detail_list01">
              <li>
                <a href="#">
                  <figure>
                    <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/detail/products_img01.png" alt="Đối xứng">
                  </figure>
                  <p>Đối xứng</p>
                </a>
              </li>
              <li>
                <a href="#">
                  <figure>
                    <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/detail/products_img02.png" alt="Cân bằng">
                  </figure>
                  <p>Cân bằng</p>
                </a>
              </li>
              <li>
                <a href="#">
                  <figure>
                    <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/detail/products_img03.png" alt="Phản chiếu">
                  </figure>
                  <p>Phản chiếu</p>
                </a>
              </li>
            </ul>
          </div>
          <div class="block">
            <h3 class="c-title03">Bồn rửa chén</h3>
            <ul class="detail_list01">
              <li>
                <a href="#">
                  <figure>
                    <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/detail/products_img04.png" alt="Bubble">
                  </figure>
                  <p>Bubble</p>
                </a>
              </li>
              <li>
                <a href="#">
                  <figure>
                    <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/detail/products_img05.png" alt="Exelis">
                  </figure>
                  <p>Exelis</p>
                </a>
              </li>
              <li>
                <a href="#">
                  <figure>
                    <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/detail/products_img06.png" alt="Freccia">
                  </figure>
                  <p>Freccia</p>
                </a>
              </li>
            </ul>
          </div>
          <div class="block">
            <h3 class="c-title03">Bồn rửa chén</h3>
            <ul class="detail_list01 detail_list01--01">
              <li>
                <a href="#">
                  <figure>
                    <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/detail/cornor_img01.png" alt="Căn bản">
                  </figure>
                  <p>Căn bản</p>
                </a>
              </li>
              <li>
                <a href="#">
                  <figure>
                    <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/detail/cornor_img02.png" alt="Bán gờ tròn">
                  </figure>
                  <p>Bán gờ tròn</p>
                </a>
              </li>
              <li>
                <a href="#">
                  <figure>
                    <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/detail/cornor_img03.png" alt="Thác nước / Góc tròn">
                  </figure>
                  <p>Thác nước / Góc tròn</p>
                </a>
              </li>
              <li>
                <a href="#">
                  <figure>
                    <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/detail/cornor_img01.png" alt="Bậc bán gờ tròn">
                  </figure>
                  <p>Bậc bán gờ tròn</p>
                </a>
              </li>
              <li>
                <a href="#">
                  <figure>
                    <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/detail/cornor_img05.png" alt="Vát cạnh">
                  </figure>
                  <p>Vát cạnh</p>
                </a>
              </li>
              <li>
                <a href="#">
                  <figure>
                    <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/detail/cornor_img06.png" alt="Gờ tròn">
                  </figure>
                  <p>Gờ tròn </p>
                </a>
              </li>
              <li>
                <a href="#">
                  <figure>
                    <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/detail/cornor_img07.png" alt="Hình cung nhọn">
                  </figure>
                  <p>Hình cung nhọn</p>
                </a>
              </li>
              <li>
                <a href="#">
                  <figure>
                    <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/detail/cornor_img08.png" alt="Đỉnh tròn">
                  </figure>
                  <p>Đỉnh tròn</p>
                </a>
              </li>
            </ul>
          </div>
        </div>
      </section>
      <section id="detail_box05">
        <div class="inner">
          <div class="m-block04">
            <h3 class="c-title02 c-title02--01">Tính Năng Ưu Việt</h3>
            <p class="txt01">Hơn 130 lựa chọn màu sắc thỏa mãn mọi nhu cầu của bạn</p>
            <p class="txt02">Với các tính năng và màu sắc đa dạng phong phú, sản phẩm đá DEKTON thích hợp với mọi ứng dụng nội thất cho căn nhà của bạn</p>
            <ul class="list-3cols">
              <li>
                <a href="#">
                  <figure>
                    <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/dekton/fea_img01.jpg" alt="GIẢ LẬP KHÔNG GIAN">
                  </figure>
                  <div class="sec">
                    <h4 class="ttl">GIẢ LẬP KHÔNG GIAN</h4>
                    <p class="txt">Lựa chọn một căn phòng và giả lập căn nhà mơ ước bằng cách chọn bề mặt và thiết kế mà bạn thích.</p>
                    <p class="link">Thiết kế ngay </p>
                  </div>
                </a>
              </li>
              <li>
                <a href="#">
                  <figure>
                    <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/dekton/fea_img01.jpg" alt="GIẢ LẬP KHÔNG GIAN">
                  </figure>
                  <div class="sec">
                    <h4 class="ttl">GIẢ LẬP KHÔNG GIAN</h4>
                    <p class="txt">Lựa chọn một căn phòng và giả lập căn nhà mơ ước bằng cách chọn bề mặt và thiết kế mà bạn thích.</p>
                    <p class="link">Thiết kế ngay </p>
                  </div>
                </a>
              </li>
              <li>
                <a href="#">
                  <figure>
                    <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/dekton/fea_img01.jpg" alt="GIẢ LẬP KHÔNG GIAN">
                  </figure>
                  <div class="sec">
                    <h4 class="ttl">GIẢ LẬP KHÔNG GIAN</h4>
                    <p class="txt">Lựa chọn một căn phòng và giả lập căn nhà mơ ước bằng cách chọn bề mặt và thiết kế mà bạn thích.</p>
                    <p class="link">Thiết kế ngay </p>
                  </div>
                </a>
              </li>
            </ul>
          </div>
        </div>
        <figure class="illust illust04">
          <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/illust04.svg" alt="illust">
        </figure>
      </section>