<?php
$term = get_queried_object();
$brand_bg = get_background_taxonomy($term->taxonomy, $term->term_id);
$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1; // Use 'pg' for your custom query pagination

$args = array(
    'post_type'      => 'product',
    'post_status'    => 'publish',
    'posts_per_page' => 9,
    'paged'          => $paged,
    'tax_query'      => array(
        array(
            'taxonomy' => 'brand', // Replace with your custom taxonomy name
            'field'    => 'term_id',
            'terms'    => $term->term_id,
        ),
    ),
);

$queryPost = new WP_Query($args);
?>

<section id="kitchen_mainv">
    <div class="m-mainv01" style="<?php echo $brand_bg; ?>">
        <h2><?php single_term_title(); ?></h2>
    </div>
</section>

<section id="kitchen_box06">
    <div class="inner">
        <div class="m-block04">
            <p class="txt01"><?php echo category_description(); ?></p>
            <ul class="list-3cols" id="ajax-content">
                <?php
                while ($queryPost->have_posts()) : $queryPost->the_post();
                    require(get_stylesheet_directory() . '/module/item/categoryItem.php');
                endwhile;
                wp_reset_postdata();
                ?>
            </ul>
        </div>
    </div>
    <figure class="illust illust04">
        <img loading="lazy" src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/illust04.svg" alt="illust">
    </figure>

    <!-- Custom Pagination -->
    <div class="pagination">
        <ul>
            <?php
            $GLOBALS['wp_query']->max_num_pages = $queryPost->max_num_pages;
                the_posts_pagination(
                    array(
                        'prev_text' => __( '<<', 'textdomain' ),
                        'next_text' => __( '>>', 'textdomain' ),
                        'current' => max(
                            1,
                            get_query_var('paged')
                        ),
                    )
                );
            ?>
        </ul>
    </div>
</section>
