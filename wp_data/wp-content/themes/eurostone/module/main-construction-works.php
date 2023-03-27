<?php
  $argsWorks = array(
    'post_type'		=> 'construction_works',
    'post_status' => 'publish',
    'posts_per_page' => '1',
    'order' => 'DESC',
  );
  $argsWorksList = array(
    'post_type'		=> 'construction_works',
    'post_status' => 'publish',
    'posts_per_page' => '6',
    'order' => 'DESC',
    'offset' => '-1'
  );
  $queryWorks = new WP_Query($argsWorks);
  if( $queryWorks->have_posts() ): ?>
  <section id="idx_construction">
    <div class="inner">
      <h3 class="c-title02"><?php _e('Construction Works', 'eurostone') ?></h3>
      <div class="viewall-link">
        <a href="#"><?php _e('View all', 'eurostone') ?></a>
      </div>
      <div class="m-block02">
        <div class="m-block02__cont">
        <?php while ( $queryWorks->have_posts() ) : $queryWorks->the_post(); ?>
          <div class="frame01">
              <?php handle_thumbnail('WORKS-THUMB', true); ?>
            <div class="block">
              <h4><?php the_title() ?></h4>
              <div class="sec">
                <?php echo wp_trim_words(get_the_excerpt()) ?>
              </div>
              <div class="link-detail">
                <a href="<?php the_permalink()?>">Chi tiết...</a>
              </div>
            </div>
          </div>
        <?php
          endwhile;
          wp_reset_postdata();
          $queryWorksList = new WP_Query($argsWorksList);
          if( $queryWorksList->have_posts() ):
        ?>
          <div class="frame02">
            <ul class="c-slider01 slider02">
            <?php while ( $queryWorksList->have_posts() ) : $queryWorksList->the_post();
            ?>
              <li class="items">
                <a href="<?php the_permalink()?>">
                  <?php handle_thumbnail('NEWS-THUMB', true); ?>
                  <div class="sec">
                    <?php $terms = get_the_terms(get_the_ID(), 'category-construction');
                      foreach($terms as $term){
                        echo '<p class="tag">'.$term->name.'</p>';
                      }
                    ?>
                    <h4><?php the_title() ?></h4>
                    <p class="txt"><?php echo wp_trim_words(get_the_excerpt()) ?></p>
                  </div>
                </a>
              </li>
            <?php
              endwhile;
              wp_reset_postdata();
            ?>
            </ul>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>
<?php wp_reset_query(); ?>
<?php endif; ?>