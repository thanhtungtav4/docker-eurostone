<?php
  $argsShowroom = array(
    'post_type'		=> 'showroom',
    'post_status' => 'publish',
    'posts_per_page' => '4',
    'order' => 'DESC',
  );
  $queryShowroom = new WP_Query($argsShowroom);
  if( $queryShowroom->have_posts() ): ?>
    <section id="idx_system">
      <div class="bg-wave"></div>
      <div class="inner">
        <div class="system__cont">
          <h3 class="c-title02"><?php _e('System', 'eurostone') ?> Showroom</h3>
          <ul class="list-2cols">
            <?php while ( $queryShowroom->have_posts() ) : $queryShowroom->the_post(); ?>
              <?php require( get_stylesheet_directory() . '/module/item/showroomItem.php' ); ?>
            <?php endwhile; ?>
          </ul>
        </div>
      </div>
      <figure class="illust illust01">
        <img loading="lazy" src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/illust01.svg" alt="">
      </figure>
      <figure class="illust illust02">
        <img loading="lazy" src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/illust02.svg" alt="">
      </figure>
    </section>
    <?php wp_reset_query(); ?>
  <?php endif; ?>
