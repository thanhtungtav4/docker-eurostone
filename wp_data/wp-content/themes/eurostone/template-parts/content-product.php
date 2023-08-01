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
                if(!empty($image_gallery)) :
                  foreach( $image_gallery as $image ):
                  ?>
                  <li class="items">
                    <?php  handle_thumbnail_id($image, 'FEATURES-PRODUCT-THUMB', get_the_title() ); ?>
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
                    <?php  handle_thumbnail_id($image, 'NEWS-THUMBNAIL', get_the_title() ); ?>
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
              <?php handle_thumbnail('FEATURES-PRODUCT-THUMB') ?>
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
      <?php
      $argsRelated = array(
        'post_type'		=> 'product',
        'post_status' => 'publish',
        'posts_per_page' => '5',
        'order' => 'DESC',
      );
      $queryRelated = new WP_Query($argsRelated);
      if( $queryRelated->have_posts() ): ?>
      <section id="detail_box03">
        <div class="bg-wave"></div>
        <div class="inner">
          <h3 class="c-title02"><?php _e('Related Products', 'eurostone'); ?></h3>
          <ul class="c-slider01 slider01">
            <?php while ( $queryRelated->have_posts() ) : $queryRelated->the_post(); ?>
              <?php require( get_stylesheet_directory() . '/module/item/productItem.php' ); ?>
            <?php endwhile; ?>
          </ul>
        </div>
      </section>
      <?php wp_reset_query(); ?>
      <?php endif; ?>
      <?php  if( have_rows('product_module') ): ?>
      <section id="detail_box04">
        <div class="inner">
          <?php  while( have_rows('product_module') ) : the_row(); ?>
          <div class="block">
            <h3 class="c-title03"><?php the_sub_field('product-category-name'); ?></h3>
            <ul class="detail_list01">
              <?php
                $productList = get_sub_field('product-list');
                if($productList) :
                foreach ($productList as $item) :
              ?>
              <li>
                  <?php
                  if($item && $item['image']) :
                    handle_thumbnail_id($item['image'], 'SUB-PRODUCT-THUMB', $item['Image-product-name'], true) ;
                  endif;
                  ?>
                  <p><?php ($item && $item['Image-product-name']) ? print $item['Image-product-name'] : '' ?></p>
              </li>
              <?php
                endforeach;
                endif;
              ?>
            </ul>
          <?php endwhile; ?>
          </div>
        </div>
      </section>
      <?php endif; ?>
      <?php require( get_stylesheet_directory() . '/module/module-features.php' ); ?>