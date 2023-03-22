<section id="idx_intro">
  <figure class="illust illust03">
    <img loading="lazy" src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/illust03.svg" alt="illust">
  </figure>
  <figure class="illust illust04">
    <img loading="lazy" src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/illust04.svg" alt="illust">
  </figure>
  <div class="inner">
    <div class="m-block01">
        <picture>
          <source media="(max-width:768px)" srcset="<?php echo wp_get_attachment_image_url(get_field('image-intro'), 'SLIDE-THUMBSP' ) ?>">
          <source media="(min-width:768px)" srcset="<?php echo wp_get_attachment_image_url(get_field('image-intro'), 'SLIDE-THUMBPC' ) ?>">
          <?php
            handle_thumbnail_id(get_field('image-intro'), 'INTRO-THUMBPC', get_field('name-intro') );
          ?>
        </picture>
      <div class="m-block01__cont">
        <h3 class="c-title01">
          <?php get_field('name-intro') ? the_field('name-intro') : ''?>
        </h3>
        <div class="section">
          <?php get_field('description-intro') ? the_field('description-intro') : ''?>
        </div>
        <div class="c-btn01">
          <a href="<?php the_field('link_to-intro')?>">XEM THÊM</a>
        </div>
      </div>
    </div>
  </div>
</section>