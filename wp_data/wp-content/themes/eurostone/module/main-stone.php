<?php
  $terms_slug = 'da-marble';
  $taxonomy_type = 'type-stone';
  $taxonomyParent = get_term_by('slug',  $terms_slug , $taxonomy_type);
  $taxonomySlug = get_term_link($terms_slug, $taxonomy_type);
  $termchildren = get_term_children($taxonomyParent->term_id, $taxonomy_type );
  $argsStone = array(
    'post_type'		=> 'product',
    'post_status' => 'publish',
    'posts_per_page' => '8',
    'order' => 'DESC',
    'tax_query' => array(
      'relation' => 'IN',
      array(
          'taxonomy' => $taxonomy_type,
          'field'    => 'slug',
          'terms'    =>  $terms_slug,
      ),
    ),
  );
  $queryStone = new WP_Query($argsStone);
  if( $queryStone->have_posts() ): ?>
  <section id="box_stone">
    <div class="inner">
      <div class="block block--01">
        <div class="box-ttl">
          <h3 class="c-title03"><?php echo $taxonomyParent->name ?></h3>
          <?php if($termchildren) :?>
          <ul class="list-cate">
            <?php foreach($termchildren as $child) :
              $term = get_term_by( 'id', $child, $taxonomy_type );
              ?>
            <li>
              <a href="<?php echo get_term_link( $child, $taxonomy_type ); ?>"><?php echo $term->name ?></a>
            </li>
            <?php endforeach; ?>
          </ul>
          <?php endif; ?>
        </div>
        <div class="viewall-link">
          <a href="<?php echo $taxonomySlug ?>" title="<?php echo $taxonomyParent->name ?>"><?php _e('View all', 'eurostone') ?></a>
        </div>
        <ul class="c-slider01 slider01">
          <?php while ( $queryStone->have_posts() ) : $queryStone->the_post(); ?>
            <?php require( get_stylesheet_directory() . '/module/item/sliderItem.php' ); ?>
          <?php endwhile; ?>
        </ul>
      </div>
    </div>
  </section>
<?php wp_reset_query(); ?>
<?php endif; ?>



