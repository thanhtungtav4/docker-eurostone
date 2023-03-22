<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package storefront
 */

  get_header();
  require( get_stylesheet_directory() . '/module/main-slider.php' );
  require( get_stylesheet_directory() . '/module/main-intro.php' );
  require( get_stylesheet_directory() . '/module/main-showroom.php' );
  require( get_stylesheet_directory() . '/module/main-stone-tax.php' );
  require( get_stylesheet_directory() . '/module/main-stone.php' );
  require( get_stylesheet_directory() . '/module/main-service.php' );
  require( get_stylesheet_directory() . '/module/main-construction-works.php' );
  require( get_stylesheet_directory() . '/module/main-news.php' );
  require( get_stylesheet_directory() . '/module/main-contact.php' );
?>
<?php get_footer();
