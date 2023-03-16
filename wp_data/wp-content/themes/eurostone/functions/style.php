<?php
/***
 * add style to  detail post
 */
add_action( 'wp_footer', 'conditionally_enqueue_styles_scripts' );
function conditionally_enqueue_styles_scripts() {
    if(is_front_page()){
        //wp_register_script( 'top-slider', get_stylesheet_directory_uri().'/assets/js/top-slider.js' );
        //wp_enqueue_script('top-slider');
    }
    if(is_singular('post') || is_singular('content')){
      ?>
      <script>
        document.addEventListener('DOMContentLoaded', function() {
          const images = document.querySelectorAll('.wp-block-image img');
            images.forEach(function(image) {
              image.removeAttribute('width');
              image.removeAttribute('height');
            });
        });
      </script>
      <?php
    }
}
