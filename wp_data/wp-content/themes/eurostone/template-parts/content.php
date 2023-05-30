<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package recruit
 */

?>
<?php
	echo get_breadcrumbs();
	$post_id = get_the_ID();
?>
<section class="newsDetail postDetail">
	<h1 class="newsDetail__title"><?php the_title() ?></h1>
	<div class="newsDetail__date"><?php the_date('Y.m.d') ?></div>
	<?php the_content(); ?>
</section>
