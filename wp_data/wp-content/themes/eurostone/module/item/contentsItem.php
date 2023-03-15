<li class="m-contentsList__item">
  <a class="m-contentsList__item--block" href="<?php the_permalink() ?>">
    <div class="m-contentsList__item--thumb">
      <?php handle_thumbnail('CONTENT-ITEM-THUMB') ?>
    </div>
    <div class="m-contentsList__item--description">
      <div class="m-contentsList__item--wrap">
        <div class="m-contentsList__item--date"><?php echo get_the_date( 'Y.m.d'); ?></div>
        <?php echo get_contents_category(get_the_ID()); ?>
      </div>
      <div class="m-contentsList__item--content js-text-length"><?php the_title() ?></div>
    </div>
  </a>
</li>