<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package recruit
 */
get_header();
$term = get_queried_object();
if(!empty($term) && ($term->parent == 0)){
  include_once(get_stylesheet_directory() .  '/module/brand/is-parent.php');
}
else{
  include_once(get_stylesheet_directory() .  '/module/brand/is-child.php');
}
get_footer();
?>


