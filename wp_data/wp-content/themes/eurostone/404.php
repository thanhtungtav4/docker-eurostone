<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package recruit
 */

get_header();
?>
  <section class="m-notfound">
    <div class="wrapper">
      <h2 class="brand-vision_title monster-font">404<span>Not Found</span></h2>
      <div class="m-notfound_box">
        <p>ページが見つかりません。</p>
        <a class="btn-primary" href="<?php echo home_url() ?>/">新卒採用 TOPへ</a>
      </div>
    </div>
  </section>
<?php
get_footer();
