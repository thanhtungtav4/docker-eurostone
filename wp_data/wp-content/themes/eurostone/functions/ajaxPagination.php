<?php

function custom_pagination() {
    if (isset($_GET['action']) && $_GET['action'] == 'custom_pagination' && isset($_GET['page']) && isset($_GET['terms'])) {
        $page = intval($_GET['page']); // Get the page number from the AJAX request
        $terms = $_GET['terms'];
        // Define your custom query args based on the page number
        $args = array(
            'post_type'      => 'product',
            'post_status'    => 'publish',
            'paged'          => $page, // Use the page number from the AJAX request
            'tax_query'      => array(
                array(
                    'taxonomy' => 'brand', // Replace with your custom taxonomy name
                    'field'    => 'term_id',
                    'terms'    => $terms,
                ),
            ),
        );

        $queryPost = new WP_Query($args);

        // The Loop to display your posts
        if ($queryPost->have_posts()) :
            while ($queryPost->have_posts()) : $queryPost->the_post();
                require(get_stylesheet_directory() . '/module/item/categoryItem.php');
            endwhile;
            wp_reset_postdata();
        endif;

        die(); // Terminate the AJAX request
    }
  }

  add_action('wp_ajax_custom_pagination', 'custom_pagination');
  add_action('wp_ajax_nopriv_custom_pagination', 'custom_pagination');

