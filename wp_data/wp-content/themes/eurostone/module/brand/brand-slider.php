<?php if(class_exists('ACF')) : ?>
  <section id="dekton_mainv">
    <div class="m-mainv">
      <div class="inner">
        <div class="m-mainv__cont">
          <h2><?php get_field('name-slider', $taxonomy_term) ? the_field('name-slider', $taxonomy_term) : ''?></h2>
          <p><?php get_field('description-slider', $taxonomy_term) ? the_field('description-slider', $taxonomy_term) : ''?></p>
        </div>
        <div class="m-mainv__slider">
          <?php if( have_rows('images-slider', $taxonomy_term) ): ?>
            <ul class="mainv-slider">
              <?php while( have_rows('images-slider', $taxonomy_term) ): the_row();
                $image = get_sub_field('image', $taxonomy_term);
              ?>
                <li class="items">
                  <picture>
                    <source media="(max-width:768px)" srcset="<?php echo wp_get_attachment_image_url($image, 'SLIDE-THUMBSP' ) ?>">
                    <source media="(min-width:768px)" srcset="<?php echo wp_get_attachment_image_url($image, 'SLIDE-THUMBPC' ) ?>">
                    <img loading="lazy" src="<?php echo wp_get_attachment_image_url($image, 'SLIDE-THUMBPC' ) ?>" alt="<?php the_field('name-slider', $taxonomy_term) ?>">
                  </picture>
                </li>
              <?php endwhile; ?>
            </ul>
          <?php endif; ?>
        </div>
      </div>
      <figure loading="lazy" class="illust illust05"><img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/illust05.svg" alt="illust"></figure>
      <figure loading="lazy" class="illust illust06"><img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/illust06.svg" alt="illust"></figure>
    </div>
  </section>
<?php endif; ?>
