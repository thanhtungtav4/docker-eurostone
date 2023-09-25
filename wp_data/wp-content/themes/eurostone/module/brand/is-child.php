<?php
$term = get_queried_object();
$brand_bg = get_background_taxonomy($term->taxonomy, $term->term_id);
$paged = max(1, get_query_var('pg')); // Use 'pg' for your custom query pagination

$args = array(
    'post_type'      => 'product',
    'post_status'    => 'publish',
    'posts_per_page' => 3,
    'tax_query'      => array(
        array(
            'taxonomy' => 'brand', // Replace with your custom taxonomy name
            'field'    => 'term_id',
            'terms'    => $term->term_id,
        ),
    ),
    'paged'          => $paged,
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
            <ul class="list-3cols">
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
            // Output custom pagination links with SVG icons
            echo get_custom_pagination_links($queryPost, 'pg');
            ?>
        </ul>
    </div>
</section>

<?php
// Custom Pagination Function
function get_custom_pagination_links($query, $query_var)
{
    $big = 999999999; // Need an unlikely integer
    $paginate_links = paginate_links(array(
        'base'      => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
        'format'    => '?'.$query_var.'=%#%',
        'current'   => max(1, get_query_var($query_var)),
        'total'     => $query->max_num_pages,
        'prev_text' => '<svg xmlns="http://www.w3.org/2000/svg" width="40" height="16" viewBox="0 0 40 16" fill="none">...</svg>',
        'next_text' => '<svg width="40" height="15" fill="none" xmlns="http://www.w3.org/2000/svg">...</svg>',
    ));

    return $paginate_links;
}
?>
