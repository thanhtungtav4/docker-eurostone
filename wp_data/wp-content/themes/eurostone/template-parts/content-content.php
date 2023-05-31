<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package recruit
 */

?>
<article class="m-article">
	<?php
		if ( function_exists('yoast_breadcrumb') ) {
			yoast_breadcrumb( '<div class="breadcrumb">','</div>' );
		}
	?>
	<div class="inner">
		<h1 class="c-title02 c-title02--01"><?php the_title(); ?></h1>
		<div class="m-article--content">
			<?php the_content(); ?>
		</div>
	</div>
</article>
