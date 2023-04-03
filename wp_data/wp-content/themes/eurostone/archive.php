<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package recruit
 */
get_header();
?>
	<?php if (is_category()) { ?>
    <?php get_template_part( 'template-parts/content', 'category' ); ?>
	<?php } elseif (is_tag()) { ?>
		<?php get_template_part( 'template-parts/content', 'tag' ); ?>
	<?php } elseif (!is_tax('brand')) { ?>
		<?php get_template_part( 'template-parts/content', 'taxonomy' ); ?>
	<?php } ?>
<?php
get_footer();
