<?php if(class_exists('ACF') && get_field('name-intro', $taxonomy_term)) : ?>
<section id="dekton_box01">
  <div class="inner">
    <div class="m-block03">
      <div class="m-block03__cont">
        <h3 class="c-title03"><?php the_field('name-intro', $taxonomy_term) ?></h3>
        <div class="sec">
          <?php the_field('description-intro', $taxonomy_term) ?>
        </div>
      </div>
      <div class="m-block03__img">
        <picture>
          <source media="(max-width:768px)" srcset="<?php echo wp_get_attachment_image_url(get_field('image-intro', $taxonomy_term), 'INTRO-TAX-THUMBNAIL' ) ?>">
          <source media="(min-width:768px)" srcset="<?php echo wp_get_attachment_image_url(get_field('image-intro', $taxonomy_term), 'INTRO-TAX-THUMBNAIL-SP' ) ?>">
          <?php
            handle_thumbnail_id(get_field('image-intro', $taxonomy_term), 'INTRO-TAX-THUMBNAIL', get_field('name-intro', $taxonomy_term) );
          ?>
        </picture>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>