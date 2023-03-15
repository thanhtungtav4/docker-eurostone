<?php
  $args = array(
    'post_type'		=> 'post',
    'post_status' => 'publish',
    'posts_per_page' => '4',
    'order' => 'DESC',
    'category__not_in' => array(get_cat_ID ( '重要なお知らせ' ))
  );
  $argsImpomation = array(
    'post_type'		=> 'post',
    'post_status' => 'publish',
    'posts_per_page' => '3',
    'order' => 'DESC',
    'category_name' => 'important-notices',
  );
  $queryNews = new WP_Query( $args );
  $queryImpomation = new WP_Query( $argsImpomation );
  if( $queryNews->have_posts() ||  $queryImpomation->have_posts()): ?>
    <section class="news">
        <?php if( $queryNews->have_posts() ): ?>
          <div class="m-headline t-news">
            <div class="g-inner">
              <h2 class="m-headline__text">ニュースリリース</h2>
            </div>
          </div>
          <div class="g-inner lg">
            <ul class="news__list">
              <?php while ( $queryNews->have_posts() ) : $queryNews->the_post(); ?>
                <li class="news__list-item">
                  <a class="news__list-item--wrap" href="<?php the_permalink(); ?>">
                    <div class="news__list-item--thumb">
                      <?php handle_thumbnail('NEWS-THUMB'); ?>
                    </div>
                    <div class="news__list-item--box">
                      <span class="news__list-item--date"><?php the_date( 'Y-m-d'); ?></span>
                      <?php $category = get_the_category() ;
                        foreach($category as $item) :
                      ?>
                        <span class="news__list-item--category">
                          <?php ($item && $item->name) ? print $item->name : '' ?>
                        </span>
                      <?php  endforeach; ?>
                      <p class="news__list-item--content"><?php the_title(); ?></p>
                    </div>
                  </a>
                </li>
              <?php endwhile; ?>
              <?php wp_reset_query(); ?>
            </ul>
            <div class="m-btnSecondary"><a class="m-btnSecondary__link" href="/news/">ニュースリリース一覧
                <svg width="15" height="14" viewBox="0 0 15 14" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
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
                </svg></a></div>
          </div>
        <?php endif; ?>
        <?php if( $queryImpomation->have_posts() ): ?>
          <div class="news-notice">
            <div class="g-inner">
              <h3 class="news-notice__title">重要なお知らせ</h3>
              <ul class="news-notice__list">
                <?php while ( $queryImpomation->have_posts() ) : $queryImpomation->the_post(); ?>
                  <li class="news-notice__item">
                    <a class="news-notice__item--link" href="<?php the_permalink()?>">
                      <span class="news-notice__item--date"><?php echo get_the_date('Y-m-d'); ?></span>
                      <p class="news-notice__item--text"><?php the_title()?></p>
                    </a>
                  </li>
                <?php endwhile; ?>
                <?php wp_reset_query(); ?>
              </ul>
              <div class="m-btnSecondary">
                <a class="m-btnSecondary__link" href="/news/?category=重要なお知らせ">お知らせ一覧
                  <svg width="15" height="14" viewBox="0 0 15 14" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
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
        <?php endif; ?>
    </section>
  <?php endif; ?>