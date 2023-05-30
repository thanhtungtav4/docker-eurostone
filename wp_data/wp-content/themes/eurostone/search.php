<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package recruit
 */

get_header();
?>
<div class="wrapper">
  <div class="m-page">
    <h2 class="m-page_title monster-font">CAINZ<span>Inside</span></h2>
    <h3 class="m-page_subtitle monster-font">
      <span class="txt-search">Search</span>
    </h3>
  </div>
  <ul class="m-breadcrumbs">
    <li><a href="<?php echo get_home_url(); ?>/">新卒採用 TOP</a></li>
    <li><a href="<?php echo get_home_url(); ?>/inside/">CAINZ Inside</a></li>
    <li><span><?php echo get_search_query() ?></span></li>
  </ul>
</div>
<div class="inside_inner">
  <div class="inside_state">
    <span class="inside_state-title txt-search"><?php echo get_search_query() ?></span>
  </div>
  <div class="wrapper">
    <article>
      <div class="result">
        <?php if ( have_posts() ) : ?>
          <div class="result_return">
          <a class="btn-return" href="<?php echo get_permalink( get_page_by_path( 'inside' ) ) ?>" tabindex="-1">すべての記事</a>
          </div>
          <ul class="result_list">
            <?php
              while ( have_posts() ) :
                the_post();
                require( get_stylesheet_directory() . '/module/archive-item.php' );
              endwhile;
            ?>
          </ul>
          <?php the_posts_pagination( array(
              'prev_text' => __( '<svg width="16" height="10" viewBox="0 0 26 14" xmlns="http://www.w3.org/2000/svg">
              <g stroke="#999" stroke-width="1.5" fill="none" fill-rule="evenodd">
              <path d="m18 1 6 6-6 6M24 7H0"></path>
              </g>
              </svg>', 'textdomain' ),
              'next_text' => __( '<svg width="16" height="10" viewBox="0 0 26 14" xmlns="http://www.w3.org/2000/svg">
              <g stroke="#fff" stroke-width="1.5" fill="none" fill-rule="evenodd">
              <path d="m18 1 6 6-6 6M24 7H0"></path>
              </g>
              </svg>', 'textdomain' ),
          ) ); ?>
          <?php else : ?>
            <div class="inside_main-content">
            <h2 class="inside_main-title">検索結果は0件です。</h2>
            <div class="inside_main-editer inside_item-content">
              <p>申し訳ありませんが、検索キーワードに一致するものはありません。<br>いくつかの異なるキーワードで再度お試しください。</p>
            </div>
            </div>
            <div class="result_return">
              <a class="btn-return" href="<?php echo get_permalink( get_page_by_path( 'inside' ) ) ?>" tabindex="-1">すべての記事</a>
            </div>
        <?php endif; ?>
      </div>
    </article>
    <?php
      require (get_stylesheet_directory() . '/module/block_aside.php');
    ?>
  </div>
</div>
<?php
get_footer();
