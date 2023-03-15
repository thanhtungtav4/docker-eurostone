<?php
  $args = array(
    'post_type'		=> 'content',
    'post_status' => 'publish',
    'posts_per_page' => '5',
    'order' => 'DESC',
  );
  $queryContent = new WP_Query( $args );
  if( $queryContent->have_posts() ):
?>
<section class="contents">
  <div class="m-headline t-contents">
    <div class="g-inner">
      <h2 class="m-headline__text">コンテンツ</h2>
    </div>
  </div>
  <div class="contents__main">
    <div class="contents_slider" data-slider="true">
      <div class="swiper-wrapper">
      <?php while ( $queryContent->have_posts() ) : $queryContent->the_post(); ?>
        <div class="swiper-slide">
          <a class="contents_slider__link" href="<?php the_permalink()?>">
            <div class="contents_slider__thumb">
              <?php handle_thumbnail('CONTENT-THUMB') ?>
            </div>
            <div class="contents_slider__detail">
              <span class="contents_slider__detail-date"><?php echo get_the_date('Y-m-d', get_the_ID()); ?></span>
              <?php $term_list = get_the_terms(get_the_ID(), 'category-content');
                    if (!empty($term_list)):
                        foreach($term_list as $item) :
                      ?>
                          <span class="contents_slider__detail-category">
                            <?php ($item && $item->name) ? print $item->name : ''?>
                          </span>
                      <?php  endforeach; ?>
                    <?php endif;?>
              <p class="contents_slider__detail-text js-text-length"><?php the_title() ?></p>
            </div>
          </a>
        </div>
      <?php endwhile; ?>
      </div>
      <div class="pagination"></div>
      <div class="m-btnSecondary">
        <a class="m-btnSecondary__link" href="/contents/">コンテンツ一覧 <svg width="15" height="14" viewbox="0 0 15 14" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
            <defs>
              <path id="9y8pum6u0a" d="M14.167 12.25c.46 0 .833.392.833.875 0 .449-.322.819-.736.87l-.097.005h-10c-.46 0-.834-.392-.834-.875 0-.449.322-.819.736-.87l.098-.005h10zm-13.334 0c.46 0 .834.392.834.875S1.294 14 .833 14C.373 14 0 13.608 0 13.125s.373-.875.833-.875zm13.334-6.125c.46 0 .833.392.833.875 0 .449-.322.819-.736.87l-.097.005h-10c-.46 0-.834-.392-.834-.875 0-.449.322-.819.736-.87l.098-.005h10zm-13.334 0c.46 0 .834.392.834.875s-.373.875-.834.875C.373 7.875 0 7.483 0 7s.373-.875.833-.875zM14.167 0c.46 0 .833.392.833.875 0 .449-.322.819-.736.87l-.097.005h-10c-.46 0-.834-.392-.834-.875 0-.449.322-.819.736-.87L4.167 0h10zM.833 0c.46 0 .834.392.834.875s-.373.875-.834.875C.373 1.75 0 1.358 0 .875S.373 0 .833 0z"></path>
            </defs>
            <g fill="none" fill-rule="evenodd">
              <mask id="ou91ahao1b" fill="#fff">
                <use xlink:href="#9y8pum6u0a"></use>
              </mask>
              <use fill="#333" xlink:href="#9y8pum6u0a"></use>
              <path fill="#212121" mask="url(#ou91ahao1b)" d="M-4.167-5.25h23.333v24.5H-4.167z"></path>
            </g>
          </svg>
        </a>
      </div>
    </div>
  </div>
</section>
<?php wp_reset_query(); ?>
<?php endif; ?>