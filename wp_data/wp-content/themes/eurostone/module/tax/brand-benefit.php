<?php
  $postIn = get_field('tax_advantage', $taxonomy_term) ? array_filter(get_field('tax_advantage', $taxonomy_term)) : '' ;
  $argsArgsWorks = array(
    'post_type'		=> array('post', 'product', 'construction_works'),
    'post_status' => 'publish',
    'posts_per_page' => '8',
    'order' => 'DESC',
    'post__in' => $postIn
  );
?>
<section id="dekton_box03">
  <div class="inner">
    <h3 class="c-title02 c-title02--01"><?php _e('Benefits of', 'eurostone') ?> <?php single_term_title(); ?></h3>
    <div class="benefit__block">
      <?php
      $queryArgsWorks = new WP_Query($argsArgsWorks);
      if( $queryArgsWorks->have_posts() ): ?>
        <dl>
          <dt class="active"><?php _e('ADVANTAGE', 'eurostone') ?></dt>
          <dd>
            <ul class="c-slider01 slider02">
              <?php while ( $queryArgsWorks->have_posts() ) : $queryArgsWorks->the_post(); ?>
                <?php require( get_stylesheet_directory() . '/module/item/taxnewsItem.php' ); ?>
              <?php endwhile; ?>
            </ul>
          </dd>
        </dl>
      <?php wp_reset_query(); ?>
      <?php endif; ?>
      <?php if(get_field('tax_design_options', $taxonomy_term)) : ?>
        <dl>
          <dt class=""><?php _e('Design options', 'eurostone') ?></dt>
          <dd class="plr-15" style="display: none;">
            <?php the_field('tax_design_options', $taxonomy_term); ?>
          </dd>
        </dl>
      <?php endif; ?>
      <?php if(get_field('tax_improve', $taxonomy_term)) : ?>
      <dl>
        <dt class=""><?php _e('Improve', 'eurostone') ?></dt>
        <dd class="plr-15" style="display: none;">
          <?php the_field('tax_improve', $taxonomy_term); ?>
        </dd>
      </dl>
      <?php endif; ?>
    </div>
  </div>
  <figure class="illust illust03"><img loading="lazy" src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/illust03.svg" alt="illust"></figure>
  <figure class="illust illust04"><img loading="lazy" src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/illust04.svg" alt="illust"></figure>
</section>