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
	<div class="newsDetail__inner">
		<div class="newsDetail__main">
			<div class="newsDetail__main-head">
				<?php echo get_html_category_detail($post_id) ?>
				<h1 class="newsDetail__title"><?php the_title() ?></h1>
				<div class="newsDetail__date"><?php the_date('Y.m.d') ?></div>
			</div>
			<?php if ( has_post_thumbnail() ) : ?>
			<div class="newsDetail__thumbMain">
				<?php the_post_thumbnail('NEWS-DETAIL-THUMB', array('loading' => 'lazy', 'alt'   => get_the_title() ) )?>
			</div>
			<?php endif; ?>
		</div>
		<?php the_content(); ?>
		<div class="newsDetail__wrap">
			<div class="newsDetail__sns">
				<p>このプレスリリースをシェアする</p>
				<ul>
					<li>
						<a class="sns__twitter" href="https://twitter.com/share?url=<?php echo get_the_permalink();?>&amp;text=<?php echo get_the_title();?>" target="_blank" rel="nofollow noopener"><img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/common/icon_twitter2.svg" alt="twitter"></a></li>
					<li>
						<a class="sns__facebook" href="http://www.facebook.com/share.php?u=<?php echo get_the_permalink(); ?>" target="_blank" rel="nofollow noopener"><img src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/common/icon_facebook2.svg" alt="facebook"></a></li>
				</ul>
			</div>
			<?php  if(class_exists('Smart_Custom_Fields') && !empty(SCF::get('pdf'))) : ?>
				<div class="newsDetail__download wp-block-button">
					<a class="wp-block-button__link m-btnDownload" download="<?php echo get_attached_file(SCF::get('pdf')) ?>" href="<?php echo get_attached_file(SCF::get('pdf')) ?>">本プレスリリースのPDFはこちら</a>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>

<?php get_contents_related_from_category($post_id, 'content', 'category-content'); ?>

<?php get_contents_related_same_taxonomy($post_id, 'post', 'category'); ?>