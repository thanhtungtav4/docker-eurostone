<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package xxxxx
 */

get_header();
?>
	<section id="kitchen_mainv">
		<div class="m-mainv01">
			<h2>
				<?php
					printf( esc_html__( 'Search Results for: %s', 'eurostone' ), '<span>' . get_search_query() . '</span>' );
				?>
			</h2>
		</div>
	</section>

<section id="kitchen_box06">
    <div class="inner">
        <div class="m-block04">
            <ul class="list-3cols">
			<?php
			/* Start the Loop */
			while ( have_posts() ) :
				the_post();

				/**
				 * Run the loop for the search to output the results.
				 * If you want to overload this in a child theme then include a file
				 * called content-search.php and that will be used instead.
				 */
				get_template_part( 'template-parts/content', 'search' );

			endwhile;
		?>
            </ul>
        </div>
    </div>
    <figure class="illust illust04">
        <img loading="lazy" src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/illust04.svg" alt="illust">
    </figure>

    <!-- Custom Pagination -->
    <div class="pagination">
        <ul id="pagination-container">
        <?php
            the_posts_pagination( array(
                'prev_text' => __( '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="16" viewBox="0 0 40 16" fill="none">
                            <path d="M0.301853 8.85204L7.80048 15.4115C8.15676 15.7234 8.68753 15.6741 8.98601 15.3019C9.2845 14.9297 9.23737 14.3751 8.88109 14.0633L3.15823 9.05721H39.1584C39.623 9.05721 40 8.66329 40 8.17792C40 7.69255 39.623 7.29863 39.1584 7.29863H3.15823L8.88109 2.29255C9.23737 1.9807 9.2845 1.42616 8.98601 1.05393C8.81938 0.84642 8.58092 0.739147 8.34023 0.739147C8.14946 0.739147 7.95814 0.80656 7.80048 0.944315L0.301296 7.5038C0.110534 7.67086 0 7.91765 0 8.17792C0 8.43819 0.11053 8.68498 0.301853 8.85204Z" fill="#000936"/>
                            </svg>', 'textdomain' ),
                'next_text' => __( '<svg width="40" height="15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M39.698 8.18L32.2 14.793a.812.812 0 01-1.186-.11.92.92 0 01.105-1.25l5.723-5.046h-36C.377 8.387 0 7.989 0 7.5c0-.49.377-.887.842-.887h36l-5.723-5.047a.92.92 0 01-.105-1.249.813.813 0 011.186-.11l7.499 6.613c.19.169.301.418.301.68 0 .262-.11.511-.302.68z" fill="#000936"/></svg>', 'textdomain' ),
            ) );
        ?>
        </ul>
    </div>
</section>
<?php
get_footer();
