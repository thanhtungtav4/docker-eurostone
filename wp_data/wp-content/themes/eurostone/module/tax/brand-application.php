<?php
   $arrAplication = get_field('application_module', $taxonomy_term);
?>
<section id="silestone_box06">
  <div class="inner">
    <h3 class="c-title02 c-title02--01"> <?php _e('Applications Of', 'eurostone'); ?> <?php single_term_title(); ?> </h3>
      <?php foreach($arrAplication as $key => $itemlist) : ?>
        <div class="app_block">
            <div class="m-block02__cont">
                <?php foreach($itemlist as $value => $item) : 
                if($item[0]) : 
                ?>
                <div class="frame01">
                <figure>
                    <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/construc_img01.jpg" alt="Công Trình Thi Công">
                </figure>
                <div class="block">
                    <h4>TEXT TEXT TEXT TEXT TEXT TEXT TEXT TEXT TEXT TEXT </h4>
                    <div class="sec">
                    <p>TEXT TEXT TEXT TEXT TEXT TEXT TEXT TEXT TEXT TEXT TEXT TEXT TEXT</p>
                    <p>TEXT TEXT TEXT TEXT TEXT TEXT TEXT TEXT TEXT TEXT TEXT TEXT TEXT TEXT TEXT TEXT TEXT</p>
                    </div>
                </div>
                </div>
                <?php endif; ?>
                <div class="frame02">
                <ul class="c-slider01 slider02">
                    <?php var_dump($item); ?>
                    <li class="items">
                    <a href="#">
                        <figure>
                        <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/construc_img02.jpg" alt="Công Trình Thi Công">
                        </figure>
                        <div class="sec">
                        <h4>TEXT TEXT TEXT TEXT TEXT TEXT TEXT TEXT TEXT TEXT </h4>
                        </div>
                    </a>
                    </li>
                    <li class="items">
                    <a href="#">
                        <figure>
                        <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/construc_img02.jpg" alt="Công Trình Thi Công">
                        </figure>
                        <div class="sec">
                        <h4>TEXT TEXT TEXT TEXT TEXT TEXT TEXT TEXT TEXT TEXT </h4>
                        </div>
                    </a>
                    </li>
                    <li class="items">
                    <a href="#">
                        <figure>
                        <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/construc_img02.jpg" alt="Công Trình Thi Công">
                        </figure>
                        <div class="sec">
                        <h4>TEXT TEXT TEXT TEXT TEXT TEXT TEXT TEXT TEXT TEXT </h4>
                        </div>
                    </a>
                    </li>
                    <li class="items">
                    <a href="#">
                        <figure>
                        <img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/construc_img02.jpg" alt="Công Trình Thi Công">
                        </figure>
                        <div class="sec">
                        <h4>TEXT TEXT TEXT TEXT TEXT TEXT TEXT TEXT TEXT TEXT </h4>
                        </div>
                    </a>
                    </li>
                </ul>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
      <?php endforeach; ?>
  </div>
</section>