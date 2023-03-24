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
//var_dump(single_term_title("", false));
require( get_stylesheet_directory() . '/module/brand/brand-slider.php' );
if(!empty($intro_style) || $intro_style != 0 ){
  require( get_stylesheet_directory() . '/module/tax/brand-intro-style-'.$intro_style.'.php' );
}
else{
  require( get_stylesheet_directory() . '/module/tax/brand-intro.php' );
}
?>

  <section id="dekton_box02">
    <div class="bg"></div>
    <div class="dekton_box02__cont">
      <div class="box-left">
        <p class="txt01"><img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/dekton/text_dekton02.png" alt="dekton" width="102"></p>
        <h3>The technical ultracompact stone</h3>
      </div>
      <div class="box-right">
        <div class="block">
          <div class="frame frame--01">
            <h4>Dekton is stone</h4>
            <div class="sec">
              <p>Dekton is a sophisticated mixture of more than 20 minerals extracted from nature.</p>
              <p>Dekton uses in its manufacturing the exclusive TSP technology, able to sinter mineral particles making them bond with each other.</p>
            </div>
          </div>
          <div class="frame frame--02">
            <h4>Dekton is technique</h4>
            <div class="sec">
              <p>Dekton is a sophisticated mixture of more than 20 minerals extracted from nature.</p>
              <p>Dekton uses in its manufacturing the exclusive TSP technology, able to sinter mineral particles making them bond with each other.</p>
            </div>
          </div>
        </div>
        <div class="frame frame--03">
          <h4>Dekton is ultracompact</h4>
          <div class="sec">
            <p>Dekton is a sophisticated mixture of more than 20 minerals extracted from nature.</p>
            <p>Dekton uses in its manufacturing the exclusive TSP technology, able to sinter mineral particles making them bond with each other.</p>
          </div>
        </div>
      </div>
    </div>
  </section>
<?php
get_footer();

