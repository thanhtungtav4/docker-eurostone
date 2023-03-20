<li>
  <a href="<?php echo get_term_link($item->term_id, 'stone-cladding') ?>">
    <?php
    $img = get_field('stone_cladding_category_image', 'stone-cladding_' . $item->term_id, );
    handle_thumbnail_id($img, 'TAX-STONE-THUMB', $item->name, true); ?>
    <p><?php echo $item->name ?></p>
  </a>
</li>