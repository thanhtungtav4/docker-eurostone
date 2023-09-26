<?php
/**
 * recruit functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package recruit
 */
require_once( get_stylesheet_directory() . '/functions/init.php' );
require_once( get_stylesheet_directory() . '/functions/security.php' );
require_once( get_stylesheet_directory() . '/functions/optimize.php' );
require_once( get_stylesheet_directory() . '/functions/slug.php' );
require_once( get_stylesheet_directory() . '/functions/bodyClass.php' );
require_once( get_stylesheet_directory() . '/functions/breadcrumbs.php' );
require_once( get_stylesheet_directory() . '/functions/schema.php' );
require_once( get_stylesheet_directory() . '/functions/custome_post_type.php' );
require_once( get_stylesheet_directory() . '/functions/page.php' );
require_once( get_stylesheet_directory() . '/functions/metaTag.php' );
require_once( get_stylesheet_directory() . '/functions/style.php' );
require_once( get_stylesheet_directory() . '/functions/pagenavi.php' );
if ( function_exists( 'pll_count_posts' ) ) {
  include_once(get_stylesheet_directory() .  '/functions/polylang-share-slug.php');
}
include_once(get_stylesheet_directory() .  '/functions/yoast.php');
include_once(get_stylesheet_directory() .  '/functions/product.php');
include_once(get_stylesheet_directory() .  '/functions/smtp.php');
include_once(get_stylesheet_directory() .  '/functions/content_crawler_gallery.php');

function custom_pagination() {
  if (isset($_GET['action']) && $_GET['action'] == 'custom_pagination' && isset($_GET['page']) && isset($_GET['terms'])) {
      $page = intval($_GET['page']); // Get the page number from the AJAX request
      $terms = $_GET['terms'];
      // Define your custom query args based on the page number
      $args = array(
          'post_type'      => 'product',
          'post_status'    => 'publish',
          'posts_per_page' => 3,
          'paged'          => $page, // Use the page number from the AJAX request
          'tax_query'      => array(
              array(
                  'taxonomy' => 'brand', // Replace with your custom taxonomy name
                  'field'    => 'term_id',
                  'terms'    => $terms,
              ),
          ),
      );

      $queryPost = new WP_Query($args);

      // The Loop to display your posts
      if ($queryPost->have_posts()) :
          while ($queryPost->have_posts()) : $queryPost->the_post();
              require(get_stylesheet_directory() . '/module/item/categoryItem.php');
          endwhile;
          wp_reset_postdata();
      endif;

      die(); // Terminate the AJAX request
  }
}

add_action('wp_ajax_custom_pagination', 'custom_pagination');
add_action('wp_ajax_nopriv_custom_pagination', 'custom_pagination');

