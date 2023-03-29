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
    <h3 class="c-title02 c-title02--01">Lợi Ích Của Dekton</h3>
    <div class="benefit__block">
      <?php
      $queryArgsWorks = new WP_Query($argsArgsWorks);
      if( $queryArgsWorks->have_posts() ): ?>
        <dl>
          <dt class="active">ƯU ĐIỂM</dt>
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
      <dl> 
        <dt class="active">Phương Án Thiết Kế</dt>
        <dd style="display: none;"> 
          <p>Text text text text text text text text text text text text text text text text text text text text text text text text text text. Text text text text text text text text text text text text text text text text text text text text text text text text text text.</p>
          <p>Text text text text text text text text text text text text text text text text text text text text text text text text text text. Text text text text text text text text text text text text text text text text text text text text text text text text text text.</p>
        </dd>
      </dl>
      <dl> 
        <dt class="active">Cải Tiến</dt>
        <dd style="display: none;"> 
          <p>Text text text text text text text text text text text text text text text text text text text text text text text text text text. Text text text text text text text text text text text text text text text text text text text text text text text text text text.</p>
          <p>Text text text text text text text text text text text text text text text text text text text text text text text text text text. Text text text text text text text text text text text text text text text text text text text text text text text text text text.</p>
        </dd>
      </dl>
    </div>
  </div>
  <figure class="illust illust03"><img loading="lazy" src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/illust03.svg" alt="illust"></figure>
  <figure class="illust illust04"><img loading="lazy" src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/illust04.svg" alt="illust"></figure>
</section>