<?php if(class_exists('ACF') && get_field('tax-block-name', $taxonomy_term)) : ?>
<section id="dekton_box02">
  <div class="bg"></div>
  <div class="dekton_box02__cont">
    <div class="box-left">
      <p class="txt01">
        <img loading="lazy" src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/dekton/text_dekton02.png" alt="dekton" width="102">
      </p>
      <h3><?php the_field('tax-block-name', $taxonomy_term); ?></h3>
    </div>
    <div class="box-right">
      <div class="block">
        <div class="frame frame--01">
          <h4><?php the_field('name_block_1', $taxonomy_term); ?></h4>
          <div class="sec">
           <?php the_field('description_block_1', $taxonomy_term); ?>
          </div>
        </div>
        <div class="frame frame--02">
          <h4><?php the_field('name_block_2', $taxonomy_term); ?></h4>
          <div class="sec">
          <?php the_field('description_block_2', $taxonomy_term); ?>
          </div>
        </div>
      </div>
      <div class="frame frame--03">
        <h4><?php the_field('name_block_3', $taxonomy_term); ?></h4>
        <div class="sec">
          <?php the_field('description_block_3', $taxonomy_term); ?>
        </div>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>