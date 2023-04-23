<?php
  $postIn = get_field('product_brand_color_top', $taxonomy_term) ? array_filter(get_field('product_brand_color_top', $taxonomy_term)) : '' ;
  $argsColor = array(
    'post_type'		=> array('product'),
    'post_status' => 'publish',
    'posts_per_page' => '8',
    'order' => 'DESC',
    'post__in' => $postIn
  );
?>
<section id="dekton_box04">
  <div class="m-block05">
    <div class="inner"> 
      <h3 class="c-title02"><?php _e('Featured Colors', 'eurostone') ?></h3>
      <ul class="c-slider01 slider01">
        <?php 
          $queryColor = new WP_Query($argsColor);
          if( $queryColor->have_posts() ): 
        ?>
        <?php while ( $queryColor->have_posts() ) : $queryColor->the_post(); ?>
            <?php require( get_stylesheet_directory() . '/module/item/productItem.php' ); ?>
          <?php endwhile; ?>
        <?php wp_reset_query(); ?>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</section>