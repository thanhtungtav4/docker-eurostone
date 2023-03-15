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
<section class="contentsDetail postDetail">
	<div class="g-inner">
		<div class="contentsDetail__main">
			<div class="contentsDetail__main-thumb">
				<?php
					if (has_post_thumbnail()) {
						handle_thumbnail('CONTENT-DETAIL-THUMB');
					}
				?>
			</div>
			<div class="contentsDetail__main-description">
				<h1 class="contentsDetail__main-title js-text-length"><?php the_title(); ?></h1>
				<dl>
					<dt><?php the_date('Y.m.d') ?></dt>
					<?php echo get_detail_contents_category_name(get_the_ID()) ?>
				</dl>
			</div>
		</div>
	</div>
	<div class="contentsDetail__inner">
		<?php the_content(); ?>
		<dl class="contentsDetail__categoryTag">
			<dt>カテゴリー：</dt>
			<?php echo get_detail_contents_category(get_the_ID())?>
		</dl>
		<div class="contentsDetail__sns">
			<p>このプレスリリースをシェアする</p>
			<ul>
				<li>
					<a href="https://twitter.com/share?url=<?php echo get_the_permalink();?>&amp;text=<?php echo get_the_title();?>">
						<img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/common/icon_twitter2.svg" alt="twitter"></a>
				</li>
				<li>
					<a href="http://www.facebook.com/share.php?u=<?php echo get_the_permalink(); ?>">
						<img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/common/icon_facebook2.svg" alt="facebook"></a>
				</li>
			</ul>
		</div>
	</div>
</section>

<?php get_contents_related_from_category($post_id, 'post', 'category'); ?>

<?php get_contents_related_same_taxonomy($post_id, 'content', 'category-content'); ?>