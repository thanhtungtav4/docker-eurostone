<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package recruit
 */
// Get the current post ID
$post_id = get_the_ID();

// Get the terms for the 'brand' taxonomy for the current post
$terms = get_the_terms( $post_id, 'brand' );
var_dump($terms);
// Get the parent and child taxonomy names
$parent_name = '';
$child_names = array();
if ( $terms && ! is_wp_error( $terms ) ) {
    $term = current( $terms );
    if ( $term->parent != 0 ) {
        $parent = get_term_by( 'id', $term->parent, 'brand' );
        $parent_name = $parent->name;
    }
    if ( $term->children ) {
        $children = get_term_children( $term->term_id, 'brand' );
        foreach ( $children as $child ) {
            $child_term = get_term_by( 'id', $child, 'brand' );
            $child_names[] = $child_term->name;
        }
    }
}

// Build the breadcrumbs string
$breadcrumbs = '';
if ( ! empty( $parent_name ) ) {
    $breadcrumbs .= '<a href="' . get_term_link( $parent->term_id, 'brand' ) . '">' . $parent_name . '</a> &gt; ';
}
if ( ! empty( $child_names ) ) {
    $breadcrumbs .= '<a href="' . get_term_link( $term->term_id, 'brand' ) . '">' . $term->name . '</a> &gt; ';
}
$breadcrumbs .= get_the_title();

// Display the breadcrumbs
echo '<div class="breadcrumbs">' . $breadcrumbs . '</div>';
?>
      <section id="detail_mainv">
        <div class="m-mainv01">
          <h2>MARBLE</h2>
        </div>
        <ul class="breadcrumb">
          <li>
            <a href="#">Trang chủ</a>
          </li>
          <li>
            <a href="#">Đá Marble - Cẩm Thạch</a>
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
                  <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/marble/marble_img01.jpg" alt="Đá Marble Trắng Vân Mây (Volakas)">
                </li>
                <li class="items">
                  <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/marble/marble_img02.jpg" alt="Đá Marble Trắng Vân Mây (Volakas)">
                </li>
                <li class="items">
                  <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/marble/marble_img03.jpg" alt="Đá Marble Trắng Vân Mây (Volakas)">
                </li>
                <li class="items">
                  <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/marble/marble_img04.jpg" alt="Đá Marble Trắng Vân Mây (Volakas)">
                </li>
                <li class="items">
                  <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/marble/marble_img04.jpg" alt="Đá Marble Trắng Vân Mây (Volakas)">
                </li>
              </ul>
              <ul class="slider-nav">
                <li class="items">
                  <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/marble/marble_img01.jpg" alt="Đá Marble Trắng Vân Mây (Volakas)">
                </li>
                <li class="items">
                  <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/marble/marble_img02.jpg" alt="Đá Marble Trắng Vân Mây (Volakas)">
                </li>
                <li class="items">
                  <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/marble/marble_img03.jpg" alt="Đá Marble Trắng Vân Mây (Volakas)">
                </li>
                <li class="items">
                  <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/marble/marble_img04.jpg" alt="Đá Marble Trắng Vân Mây (Volakas)">
                </li>
                <li class="items">
                  <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/marble/marble_img04.jpg" alt="Đá Marble Trắng Vân Mây (Volakas)">
                </li>
              </ul>
            </div>
            <div class="m-blockdetail__cont">
              <div class="block-top">
                <h4><?php the_title(); ?></h4>
                <ul class="list-tag">
                  <li>Mã sản phẩm : <span class="bold">EWH11014</span>
                  </li>
                  <li>Dekton - Onirika</li>
                </ul>
                <div class="m-blockdetail__tab">
                  <ul class="tab-ttl">
                    <li class="active">Mô tả</li>
                    <li>Thông số kỹ Thuật</li>
                  </ul>
                  <div class="tab-cont">
                    <div class="tab-frame active">
                      <div class="sec">
                        <p>Đá Marble trắng vân mây đến từ Hy Lạp với màu đá trắng chủ đạo cùng với các đường vân màu xám, nâu, tím, hồng..., phù hợp ốp tường trang trí, hoặc các tiện ích khác góp phần tạo nên sự sang trọng cho ngôi nhà của bạn.</p>
                        <p>Với công nghệ hiện đại đến từ Châu Âu, đá Marble vân mây siêu bền, siêu chắc chắn với thời gian, sẽ đem đến cho bạn một không gian thoải mái, tuyệt vời.</p>
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