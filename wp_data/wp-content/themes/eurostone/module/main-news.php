<?php
$argsNews = array(
  'post_type'		=> 'post',
  'post_status' => 'publish',
  'posts_per_page' => '4',
  'order' => 'DESC',
  'offset' => '-1'
);
$queryNews = new WP_Query($argsNews);
if( $queryNews->have_posts() ): ?>
<section id="idx_news">
  <div class="inner">
    <h3 class="c-title02"><?php _e('News', 'eurostone') ?></h3>
    <div class="viewall-link">
      <a href="#"><?php _e('View all', 'eurostone') ?></a>
    </div>
    <div class="news__cont">
      <ul class="news__list">
        <?php while ( $queryNews->have_posts() ) : $queryNews->the_post(); ?>
          <?php require( get_stylesheet_directory() . '/module/item/newslistItem.php' ); ?>
        <?php endwhile; ?>
      </ul>
    </div>
  </div>
</section>
<?php wp_reset_query(); ?>
<?php endif; ?>