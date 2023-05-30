<li class="items">
  <a href="<?php the_permalink()?>">
    <?php handle_thumbnail('NEWS-THUMB', true); ?>
    <div class="sec">
      <h4><?php the_title()?></h4>
      <p class="txt"><?php echo wp_trim_words(get_the_excerpt()) ?></p>
    </div>
  </a>
</li>