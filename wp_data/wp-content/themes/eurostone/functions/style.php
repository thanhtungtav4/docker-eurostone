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
    if(is_page('products_partner')){
      ?>
      <script>
        //- Fucntion handle loadmore table client side
        var rowsToShow = 10;
        $('.m-tableProductsPartner table tbody tr:lt('+rowsToShow+')').addClass('is-active');
        $('.m-tableProductsPartner table tr:eq('+rowsToShow+')').addClass('is-hide');
        $('#load-products').on('click',function(e){
          e.preventDefault();
          var productRows = $('.m-tableProductsPartner table tbody tr').length;                        
          $('.m-tableProductsPartner table tr:eq('+rowsToShow+')').removeClass('is-hide');
          $('.m-tableProductsPartner table tbody tr:lt('+productRows+')').addClass('is-active');
          $(this).hide();
        })
      </script>
      <?php
    }
    if(is_page('news')){
      wp_register_script( 'news', get_stylesheet_directory_uri().'/assets/js/news.js' );
      wp_enqueue_script('news');
    }
    if(is_page('contents')){
      wp_register_script( 'contents', get_stylesheet_directory_uri().'/assets/js/contents.js' );
      wp_enqueue_script('contents');
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
