<?php if(class_exists('ACF')) : ?>
  <?php
    $argsTax = get_terms( array(
      'taxonomy' => 'stone-cladding',
      'hide_empty' => false,
      'parent' => 0
    ));
    ?>
  <section id="box_tile">
    <div class="inner">
      <h3 class="c-title03"><?php _e('TILING CATEGORY', 'eurostone'); ?></h3>
      <ul class="list-4cols">
        <?php foreach($argsTax as $key => $item) : ?>
          <?php require( get_stylesheet_directory() . '/module/item/taxItem.php' ); ?>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>
<?php endif; ?>
