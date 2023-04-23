<?php
  $postFeatures = get_field('module_features', $taxonomy_term) ? array_filter(get_field('module_features', $taxonomy_term)) : '' ;
  $argsFeatures = array(
    'post_type'		=> array('post', 'product', 'construction_works'),
    'post_status' => 'publish',
    'posts_per_page' => '3',
    'order' => 'DESC',
    'post__in' => $postFeatures
  );
  $queryFeatures = new WP_Query($argsFeatures);
  if( $queryFeatures->have_posts() ): ?>
<section id="granite_box07">
  <div class="inner">
    <div class="m-block04">
      <h3 class="c-title02 c-title02--01"><?php _e('Superior Features', 'eurostone') ?></h3>
      <p class="txt01">Hơn 130 lựa chọn màu sắc thỏa mãn mọi nhu cầu của bạn</p>
      <p class="txt02">Với các tính năng và màu sắc đa dạng phong phú, sản phẩm đá DEKTON thích hợp với mọi ứng dụng nội thất cho căn nhà của bạn</p>
      <ul class="list-3cols">
        <?php while ( $queryFeatures->have_posts() ) : $queryFeatures->the_post(); ?>
          <li>
            <a href="<?php the_permalink()?>">
              <?php handle_thumbnail('FEATURES-THUMB', true); ?>
              <div class="sec">
                <h4 class="ttl"><?php the_title()?></h4>
                <p class="txt"><?php echo wp_trim_words(get_the_excerpt(), 20, '...') ?></p>
                <p class="link"><?php _e('Design now', 'eurostone') ?></p>
              </div>
            </a>
          </li>
        <?php endwhile; ?>
      </ul>
    </div>
  </div>
</section>
<?php wp_reset_query(); ?>
<?php endif; ?>