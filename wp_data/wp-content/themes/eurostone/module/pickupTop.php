<?php
  $argsPickup = array(
    'post_type'		=> array('post','content'),
    'post_status' => 'publish',
    'posts_per_page' => '10',
    'order' => 'DESC',
    'meta_query'  => array(
      array(
        'key'     => 'pickup_notices',
        'value'   => '1',
        'compare' => '='
      )
    )
  );
  $queryPickup = new WP_Query( $argsPickup );
  if( $queryPickup->have_posts() ): ?>
    <section class="pickup">
      <div class="g-inner">
        <ul class="pickup-box">
          <?php while ( $queryPickup->have_posts() ) : $queryPickup->the_post(); ?>
              <li><a class="pickup-text" href="<?php the_permalink()?>"><?php the_title() ?></a></li>
          <?php endwhile; ?>
        </ul>
      </div>
    </section>
    <?php wp_reset_query(); ?>
  <?php endif; ?>