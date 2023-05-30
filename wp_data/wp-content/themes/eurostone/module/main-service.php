<?php if( have_rows('main-service-stone') ): ?>
  <section id="idx_service">
      <div class="bg-wave"></div>
      <div class="inner">
        <h3 class="c-title02">Dịch Vụ Ngành Đá</h3>
        <div class="service__cont">
          <ul class="list-4cols">
          <?php while( have_rows('main-service-stone') ): the_row(); ?>
            <li>
              <a href="<?php the_sub_field('link_to') ?>">
                <?php handle_thumbnail_id(get_sub_field('ms-image'), 'TAX-STONE-THUMB', get_sub_field('ms-name'), true); ?>
                <p><?php the_sub_field('ms-name') ?></p>
              </a>
            </li>
            <?php endwhile; ?>
          </ul>
        </div>
      </div>
      <figure class="illust illust03">
        <img loading="lazy" src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/illust03.svg" alt="illust">
      </figure>
      <figure class="illust illust04">
        <img loading="lazy" src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/illust04.svg" alt="illust">
      </figure>
    </section>
  <?php endif; ?>