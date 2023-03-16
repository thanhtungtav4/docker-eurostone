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
<footer class="c-footer">
  <div class="inner">
    <div class="footer__cont">
      <div class="footer__block">
        <div class="footer__frame">
          <div class="f_ttl">THÔNG TIN CÔNG TY</div>
          <div class="f_txt01">CÔNG TY CỔ PHẦN ĐÁ HOA CƯƠNG CHÂU ÂU - EUROSTONE</div>
          <ul class="f_list">
            <li>MST: 0309720941</li>
            <li>Người đại diện: Text Text Text</li>
            <li>Ngày cấp: Text Text Text</li>
            <li>Nơi cấp: Text Text Text</li>
          </ul>
        </div>
        <div class="footer__frame">
          <div class="f_ttl">KẾT NỐI VỚI EUROSTONE</div>
          <ul class="list-social">
            <li>
              <a href="#" target="_blank">
                <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/facebook.png" alt="facebook" width="30">
              </a>
            </li>
            <li>
              <a href="#">
                <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/pinterest.png" alt="pinterest" width="30">
              </a>
            </li>
            <li>
              <a href="#" target="_blank">
                <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/youtube.png" alt="youtube" width="30">
              </a>
            </li>
            <li>
              <a href="#" target="_blank">
                <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/zalo.png" alt="zalo" width="30">
              </a>
            </li>
            <li>
              <a href="#" target="_blank">
                <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/messenger.png" alt="messenger" width="30">
              </a>
            </li>
          </ul>
        </div>
      </div>
      <div class="footer__block">
        <div class="footer__frame">
          <div class="f_ttl">VỀ EUROSTONE</div>
          <p class="f_txt02 icon01">Showroom</p>
          <ul class="f_list">
            <li>số 120 đường số 7, KDC Cityland - Center Hill, P.7, Q. Gò Vấp</li>
            <li>số 25 đường số 10, KĐT Sala, P. An Lợi Đông, Q.2, TP. Thủ Đức</li>
          </ul>
          <p class="f_txt02 icon02">Nhà máy</p>
          <ul class="f_list">
            <li>số 120 đường số 7, KDC Cityland - Center Hill, P.7, Q. Gò Vấp</li>
            <li>số 25 đường số 10, KĐT Sala, P. An Lợi Đông, Q.2, TP. Thủ Đức</li>
          </ul>
          <p class="f_txt02 phone">Hotline : <a href="tel:0903930126">0903 930 126</a>
            <a href="tel:0903598407">0903 598 407</a>
          </p>
          <p class="f_txt02 mail">Email : <a href="mailto:info@eurostone.vn">info@eurostone.vn</a>
          </p>
        </div>
      </div>
      <div class="footer__block">
        <div class="footer__frame">
          <p class="f_ttl">CHÍNH SÁCH CHUNG</p>
          <ul class="f_list">
            <li>Chính sách vận chuyển</li>
            <li>Chính sách thi công</li>
            <li>Chính sách bảo hành</li>
          </ul>
        </div>
        <div class="footer__contact">
          <p>Đăng ký Email ngay để nhận ưu đãi mới nhất!</p>
          <div class="register">
            <p class="cus_input">
              <input type="text" placeholder="Nhập Email">
            </p>
            <p class="btn_submit">
              <input type="submit" value="">
            </p>
          </div>
          <p class="f_logo">
            <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/f_logo.png" alt="facebook" width="137">
          </p>
        </div>
      </div>
    </div>
  </div>
  <address>&copy;2016 EuroStone All Rights Reserved.</address>
</footer>
<script src="<?php echo get_stylesheet_directory_uri() ?>/assets/js/jquery.js"></script>
<script src="<?php echo get_stylesheet_directory_uri() ?>/assets/js/slick.min.js"></script>
<script src="<?php echo get_stylesheet_directory_uri() ?>/assets/js/jquery.matchHeight.js"></script>
<script src="<?php echo get_stylesheet_directory_uri() ?>/assets/js/top.js"></script>
 <?php wp_footer(); ?>
</body>
</html>