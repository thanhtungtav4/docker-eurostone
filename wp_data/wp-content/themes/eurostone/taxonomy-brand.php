<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package recruit
 */
get_header();
$taxonomy_object = get_queried_object();
$taxonomy_id = $taxonomy_object->term_id;
$taxonomy_term = $taxonomy_object->taxonomy . '_' . $taxonomy_object->term_id;
$intro_style = get_field('style_intro', $taxonomy_term);
require( get_stylesheet_directory() . '/module/brand/brand-slider.php' );
if(!empty($intro_style) || $intro_style != 0 ){
  require( get_stylesheet_directory() . '/module/tax/brand-intro-style-'.$intro_style.'.php' );
}
else{
  require( get_stylesheet_directory() . '/module/tax/brand-intro.php' );
}
require( get_stylesheet_directory() . '/module/tax/brand-block-info.php' );
require( get_stylesheet_directory() . '/module/tax/brand-benefit.php' );
require( get_stylesheet_directory() . '/module/tax/brand-faq.php' );
require( get_stylesheet_directory() . '/module/tax/top_colors.php' );
require( get_stylesheet_directory() . '/module/tax/brand-application.php' );
require( get_stylesheet_directory() . '/module/tax/brand-structure.php' );
require( get_stylesheet_directory() . '/module/module-features.php' );
?>
<?php
get_footer();

