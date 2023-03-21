<?php
  $terms_slug = 'da-marble';
  $taxonomy_type = 'type-stone';
  $taxonomyName = get_term_by('slug',  $terms_slug , $taxonomy_type);
  $taxonomySlug = get_term_link($terms_slug, $taxonomy_type);
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
          <h3 class="c-title03"><?php echo $taxonomyName->name ?></h3>
          <ul class="list-cate">
            <li>
              <a href="#">Subcate</a>
            </li>
            <li>
              <a href="#">Subcate</a>
            </li>
            <li>
              <a href="#">Subcate</a>
            </li>
          </ul>
        </div>
        <div class="viewall-link">
          <a href="<?php echo $taxonomySlug ?>" title="<?php echo $taxonomyName->name ?>">Xem tất cả </a>
        </div>
        <ul class="c-slider01 slider01">
          <li class="items">
            <a href="#">
              <figure>
                <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/stone_img01.jpg" alt="Đá Marble Xanh (Green)">
              </figure>
              <div class="sec">
                <h4>Đá Marble Xanh (Green) Đá Marble Xanh (Green) Đá Marble Xanh (Green)</h4>
                <ul class="list-tag">
                  <li>India</li>
                  <li>Lớn</li>
                  <li>1.6 - 1.8cm</li>
                </ul>
              </div>
            </a>
          </li>
          <li class="items">
            <a href="#">
              <figure>
                <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/stone_img01.jpg" alt="Đá Marble Xanh (Green)">
              </figure>
              <div class="sec">
                <h4>Đá Marble Xanh (Green)</h4>
                <ul class="list-tag">
                  <li>India</li>
                  <li>Lớn</li>
                  <li>1.6 - 1.8cmf</li>
                </ul>
              </div>
            </a>
          </li>
          <li class="items">
            <a href="#">
              <figure>
                <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/stone_img01.jpg" alt="Đá Marble Xanh (Green)">
              </figure>
              <div class="sec">
                <h4>Đá Marble Xanh (Green)</h4>
                <ul class="list-tag">
                  <li>India</li>
                  <li>Lớn</li>
                  <li>1.6 - 1.8cm</li>
                </ul>
              </div>
            </a>
          </li>
          <li class="items">
            <a href="#">
              <figure>
                <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/stone_img01.jpg" alt="Đá Marble Xanh (Green)">
              </figure>
              <div class="sec">
                <h4>Đá Marble Xanh (Green)</h4>
                <ul class="list-tag">
                  <li>India</li>
                  <li>Lớn</li>
                  <li>1.6 - 1.8cm</li>
                </ul>
              </div>
            </a>
          </li>
          <li class="items">
            <a href="#">
              <figure>
                <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/stone_img01.jpg" alt="Đá Marble Xanh (Green)">
              </figure>
              <div class="sec">
                <h4>Đá Marble Xanh (Green)</h4>
                <ul class="list-tag">
                  <li>India</li>
                  <li>Lớn</li>
                  <li>1.6 - 1.8cm</li>
                </ul>
              </div>
            </a>
          </li>
          <li class="items">
            <a href="#">
              <figure>
                <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/stone_img01.jpg" alt="Đá Marble Xanh (Green)">
              </figure>
              <div class="sec">
                <h4>Đá Marble Xanh (Green)</h4>
                <ul class="list-tag">
                  <li>India</li>
                  <li>Lớn</li>
                  <li>1.6 - 1.8cm</li>
                </ul>
              </div>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </section>
<?php wp_reset_query(); ?>
<?php endif; ?>



