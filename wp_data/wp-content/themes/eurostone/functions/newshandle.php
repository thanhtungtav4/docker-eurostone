<?php
/**
 * Load search ajax
 */
function searchHandlerData() {
	//initialize && normalized data input
	$page_size = PAGESIZE;
	$paramsKeyword= $_POST['paramsKeyword'] ? normalizeInputString($_POST['paramsKeyword']) : null;
	$paramsYear= $_POST['paramsYear'] ? normalizeInputNumber($_POST['paramsYear']) : null;
	$paramsMonth= $_POST['paramsMonth'] ? normalizeInputNumber($_POST['paramsMonth']) : null;
	$paramsCategory= $_POST['paramsCategory'] ?? null;
	$paged = $_POST['paramsPaged'] ? normalizeInputPaged($_POST['paramsPaged']) : '1';
	//!initialize && normalized data input
	$params = array(
    'post_type' => 'post',
		'post_status' => 'publish',
		'monthnum' => $paramsMonth,
		'year' => $paramsYear,
		's' => $paramsKeyword,
		'orderby' => 'ID',
		'order' => 'desc',
		'paged' => $paged,
		'posts_per_page' => $page_size,
  );
	if(is_array($paramsCategory)){
		$params['tax_query']= array(
			array(
					'taxonomy' => 'category', //double check your taxonomy name in you dd
					'field'    => 'id',
					'terms'    => $paramsCategory,
			),
		);
	}
  $dataPost = new WP_Query($params);
  $results  = array();
  if ( $dataPost->have_posts() ) :
    while ( $dataPost->have_posts() ) : $dataPost->the_post();
      $result  = array(
				'title' => get_the_title(),
				'permalink' => get_permalink(),
				'excerpt' => get_the_excerpt(),
				'images' => get_handle_thumbnail('NEWS-THUMB'),
				'date' => get_the_date('Y.m.d'),
				'category' =>get_html_category(get_the_ID()),
			);
			array_push($results, $result);
    endwhile;
  else  :
     $results  = null;
  endif;
	$total_posts  = $dataPost->found_posts;
	$total_pages = ceil( $total_posts / $page_size );
	$nextpage = $paged+1;
			$prevouspage = $paged-1;
			$total = $dataPost->max_num_pages;
			$pagination_args = array(
			'base'               => '%_%',
			'format'             => '?%#%',
			'total'              => $total,
			'current'            => $paged,
			'show_all'           => false,
			'end_size'           => 1,
			'mid_size'           => 2,
			'prev_next'          => true,
			'prev_text'       => __('<<'),
			'next_text'       => __('>>'),
			'type'               => 'plain',
			'add_args'           => false,
			'add_fragment'       => '',
			'before_page_number' => '',
			'after_page_number'  => ''
	);
	$paginate_links = paginate_links($pagination_args);
	$paginate_links = str_replace('href=', 'href="#" data-paged=', $paginate_links);
	$response = array(
    'total_posts' => $total_posts,
    'total_pages' => $total_pages,
    'current_page' => $paged,
    'posts' => $results,
		'pagination' => $paginate_links,
		'keysearch' => $paramsKeyword
	);
	echo json_encode( $response );
  wp_reset_query();
  exit;
}
add_action('wp_ajax_searchHandlerData', 'searchHandlerData');
add_action('wp_ajax_nopriv_searchHandlerData', 'searchHandlerData');
