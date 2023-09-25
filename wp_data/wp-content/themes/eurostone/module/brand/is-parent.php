<?php
$taxonomy_object = get_queried_object();
$taxonomy_id = $taxonomy_object->term_id;
$taxonomy_term = $taxonomy_object->taxonomy . '_' . $taxonomy_object->term_id;
$intro_style = get_field('style_intro', $taxonomy_term);
$show_tax_intro = get_field('show_tax_intro', $taxonomy_term);
$show_tax_bebefit = get_field('show_tax_bebefit', $taxonomy_term);
$show_tax_top_colors = get_field('show_tax_color_top', $taxonomy_term);
$show_tax_block_info = get_field('show_tax_block_info', $taxonomy_term);
$show_tax_application_module = get_field('show_tax_application_module', $taxonomy_term);
require( get_stylesheet_directory() . '/module/brand/brand-slider.php' );
if($show_tax_intro == true){
  if(!empty($intro_style) || $intro_style != 0 ){
    require( get_stylesheet_directory() . '/module/tax/brand-intro-style-'.$intro_style.'.php' );
  }
  else{
    require( get_stylesheet_directory() . '/module/tax/brand-intro.php' );
  }
}
if($show_tax_block_info == true){
  require( get_stylesheet_directory() . '/module/tax/brand-block-info.php' );
}
if($show_tax_bebefit == true){
  require( get_stylesheet_directory() . '/module/tax/brand-benefit.php' );
}
require( get_stylesheet_directory() . '/module/tax/brand-faq.php' );
if($show_tax_top_colors == true){
  require( get_stylesheet_directory() . '/module/tax/top_colors.php' );
}
if($show_tax_application_module == true){
  require( get_stylesheet_directory() . '/module/tax/brand-application.php' );
}
require( get_stylesheet_directory() . '/module/tax/brand-structure.php' );
require( get_stylesheet_directory() . '/module/module-features.php' );