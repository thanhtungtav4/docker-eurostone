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
if( class_exists('acf') ) {
  // include_once(get_stylesheet_directory() .  '/functions/acf-gallery/acf-gallery.php');
  // include_once(get_stylesheet_directory() .  '/functions/acf-repeater/acf-repeater.php');
}
if ( function_exists( 'pll_count_posts' ) ) {
  include_once(get_stylesheet_directory() .  '/functions/polylang-share-slug.php');
}
include_once(get_stylesheet_directory() .  '/functions/yoast.php');
include_once(get_stylesheet_directory() .  '/functions/product.php');