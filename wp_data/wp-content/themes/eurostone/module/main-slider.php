<?php if(class_exists('ACF')) : ?>
  <section id="idx_main">
    <div class="m-mainv">
      <div class="inner">
        <div class="m-mainv__cont">
          <h2><?php get_field('name-slider') ? the_field('name-slider') : ''?></h2>
          <p><?php get_field('description-slider') ? the_field('description-slider') : ''?></p>
        </div>
        <div class="m-mainv__slider">
          <?php if( have_rows('images-slider') ): ?>
            <ul class="mainv-slider">
              <?php while( have_rows('images-slider') ): the_row();
                $image = get_sub_field('image');
              ?>
                <li class="items">
                  <picture>
                    <source media="(max-width:768px)" srcset="<?php echo wp_get_attachment_image_url($image, 'SLIDE-THUMBSP' ) ?>">
                    <source media="(min-width:768px)" srcset="<?php echo wp_get_attachment_image_url($image, 'SLIDE-THUMBPC' ) ?>">
                    <img src="<?php echo wp_get_attachment_image_url($image, 'SLIDE-THUMBPC' ) ?>" alt="<?php the_field('name-slider') ?>">
                  </picture>
                </li>
              <?php endwhile; ?>
            </ul>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>
<?php endif; ?>
