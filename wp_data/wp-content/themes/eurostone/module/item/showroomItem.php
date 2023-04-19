<li class="system__card">
  <a href="<?php //the_permalink()?>">
    <?php handle_thumbnail('SHOWROOM-THUMBPC', true); ?>
    <div class="system__card--sec">
      <h4><?php the_title() ?></h4>
      <p><?php echo wp_trim_words(get_the_excerpt()) ?></p>
    </div>
  </a>
</li>