<li class="m-contentsRelatedList__item">
<a class="m-contentsRelatedList__item--block" href="<?php the_permalink() ?>">
    <div class="m-contentsRelatedList__item--thumb">
      <?php handle_thumbnail('CONTENT-RELATED') ?>
    </div>
    <div class="m-contentsRelatedList__item--description">
      <div class="m-contentsRelatedList__item--content js-text-length">
        <?php the_title() ?>
      </div>
      <div class="m-contentsRelatedList__item--wrap">
        <div class="m-contentsRelatedList__item--date"><?php echo get_the_date( 'Y.m.d'); ?></div>
        <?php echo get_contents_related_category(get_the_ID()); ?>
      </div>
    </div>
  </a>
</li>