<li class="m-newsList__item">
  <a class="m-newsList__item--block" href="<?php the_permalink() ?>">
    <div class="m-newsList__item--thumb">
      <?php handle_thumbnail('NEWS-ITEM-THUMB') ?>
    </div>
    <div class="m-newsList__item--description">
      <div class="m-newsList__item--content js-text-length">
        <?php the_title() ?>
      </div>
      <div class="m-newsList__item--wrap">
        <div class="m-newsList__item--date"><?php echo get_the_date( 'Y.m.d'); ?></div>
        <?php echo get_html_category(get_the_ID()); ?>
      </div>
    </div>
  </a>
</li>