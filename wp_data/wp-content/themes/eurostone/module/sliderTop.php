<?php
  if(class_exists('Smart_Custom_Fields') && is_array(SCF::get('SlideTop')) && !empty(SCF::get('SlideTop'))) :
?>
  <section class="slider">
    <div class="slider__main">
      <div class="top_slider" data-slider="true">
        <div class="swiper-wrapper">
          <?php
            $dataSlider = SCF::get('SlideTop');
            if($dataSlider) :
            usort($dataSlider, function ($sorta, $sortb) {
                return $sorta["slide_sort"] - $sortb["slide_sort"];
            });
            foreach($dataSlider as $key => $slider) :
                $imgsp = $slider["slide_image_mobile"] ? $slider["slide_image_mobile"] : null;
                $imgpc = $slider["slide_image_pc"] ? $slider["slide_image_pc"] : null;
                $linkto = $slider["link_to"] ? $slider["link_to"] : '#';
                $alt = $slider['slide_image_alt'] ? $slider['slide_image_alt'] : null;
              ?>
                <?php
                  echo show_picture_slide($linkto, $imgsp, $imgpc, $alt);
                ?>
            <?php
              endforeach;
            endif;
            ?>
        </div>
        <div class="pagination"></div>
      </div>
    </div>
  </section>
<?php endif; ?>