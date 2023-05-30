<li>
  <a href="<?php the_permalink() ?>">
    <?php handle_thumbnail('FEATURES-THUMB', true); ?>
    <div class="sec">
      <h4 class="ttl"><?php echo wp_trim_words(get_the_title(), 12, '...'); ?></h4>
      <p class="txt"><?php echo wp_trim_words(get_the_excerpt(), 15, '...'); ?></p>
      <p class="link"><?php _e('View details', 'eurostone') ?></p>
    </div>
  </a>
</li>