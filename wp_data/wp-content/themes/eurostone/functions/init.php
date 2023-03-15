<?php
if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}
define('PAGESIZE', 10);
define( 'PLACEHOLDER-THUMB', get_stylesheet_directory_uri() . '/assets/images/placeholder/img_noimage_440x260.png' );
define( 'PLACEHOLDER-NEWS-THUMB', get_stylesheet_directory_uri() . '/assets/images/placeholder/img_noimage_440x260.png' );
define( 'PLACEHOLDER-NEWS-ITEM-THUMB', get_stylesheet_directory_uri() . '/assets/images/placeholder/img_noimage_300x186.png' );
define( 'PLACEHOLDER-CONTENT-THUMB', get_stylesheet_directory_uri() . '/assets/images/placeholder/img_noimage_800x448.png' );
define( 'PLACEHOLDER-CONTENT-ITEM-THUMB', get_stylesheet_directory_uri() . '/assets/images/placeholder/img_noimage_708x492.png' );
define( 'PLACEHOLDER-CONTENT-DETAIL-THUMB', get_stylesheet_directory_uri() . '/assets/images/img_noimage_800x448.png' );
///placeholder-content-thumb placeholder-news-thumb placeholder-thumb
// using in meta data if null
define('POST_TYPES', ['page', 'post', 'content']);
define('PLACEHOLDER_IMAGE_META', get_stylesheet_directory_uri() . '/assets/images/placeholder/img_noimage_800x448.png');
define('PLACEHOLDER_DESCRIPTION_META', 'オリジナル商品を開発中！ホームセンター「カインズ」の企業サイトです。');
define('PLACEHOLDER_DESCRIPTION_META_POST', 'カインズのニュースリリース・プレスリリースをご紹介します。ホームセンターのCAINZでおなじみ、株式会社カインズの公式企業サイトです。');
define('PLACEHOLDER_DESCRIPTION_META_CONTENT', 'カインズの取り組みを、深堀りしてご紹介しています。ホームセンターのCAINZでおなじみ、株式会社カインズの公式企業サイトです。');
define('PLACEHOLDER_KEYWORDS_META', 'カインズ,カインズホーム,cainz,採用,採用情報,新卒採用,パート,アルバイト,専任社員,中途採用,薬剤師,登録販売者');
define('PLACEHOLDER_KEYWORDS_META_POST', 'カインズ,CAINZ,ホームセンター,カインズホーム,ニュースリリース,プレスリリース');
define('PLACEHOLDER_KEYWORDS_META_CONTENT', 'カインズ,CAINZ,ホームセンター,カインズホーム,コンテンツ,取り組み,深堀り');
define('PLACEHOLDER_IMAGE_OGP', get_stylesheet_directory_uri() . '/assets/images/placeholder/og-image_400x248.png');
// setup
function corporate_setup() {
	add_theme_support( 'post-thumbnails' );
}
add_action( 'after_setup_theme', 'corporate_setup' );

// load style css
function corporate_enqueue_style() {
	wp_enqueue_style( 'corporate-style', get_stylesheet_uri() );
}
add_action( 'wp_enqueue_scripts', 'corporate_enqueue_style' );

/**
 * Adds custom image sizes to the current theme.
 */
add_action( 'after_setup_theme', 'corporate_theme_setup' );
function corporate_theme_setup() {
	add_image_size( 'NEWS-THUMB', 440, 260, true );
	add_image_size( 'CONTENT-RELATED', 664, 400, true );
	add_image_size( 'NEWS-ITEM-THUMB', 300, 186, true );
	add_image_size( 'SLIDE-THUMBPC', 1140, 500, true );
	add_image_size( 'SLIDE-THUMBSP', 456, 557, true );
	add_image_size( 'CONTENT-THUMB', 1100, 652, true );
	add_image_size( 'TENANT-ITEM-THUMB', 456, 304, true );
	add_image_size( 'CONTENT-ITEM-THUMB', 708, 492, true );
	add_image_size( 'NEWS-DETAIL-THUMB', 1450, 896, true );
	add_image_size( 'CONTENT-DETAIL-THUMB', 800, 448, true );
}
/**
 * This function takes a string `$data` as its input and performs the following operations:
 * - Trim whitespaces using the PHP `trim` function.
 * - Convert the string to lowercase using the PHP `strtolower` function.
 * - Remove any malicious characters using the PHP `filter_var` function with the `FILTER_SANITIZE_STRING` filter.
 * - Return the sanitized and normalized string.
 *
 * @param string $data The input string to sanitize and normalize.
 * @return string The sanitized and normalized string.
 */

 function normalizeInputString($data) {
	// Trim whitespaces
	$data = trim($data);
	// Convert to lowercase
	$data = strtolower($data);
	// Remove any malicious characters
	$data = filter_var($data, FILTER_SANITIZE_STRING);
	return $data;
}
/**
* This function takes a string `$data` as its input and performs the following operations:
* - Trim whitespaces using the PHP `trim` function.
* - Convert the string to lowercase using the PHP `strtolower` function.
* - Remove any non-numeric characters using the PHP `filter_var` function with the `FILTER_SANITIZE_NUMBER_INT` filter.
* - Convert the sanitized string to an integer using the PHP `intval` function.
* - Return the sanitized and normalized integer.
*
* @param string $data The input string to sanitize and normalize.
* @return int The sanitized and normalized integer.
*/
function normalizeInputNumber($data) {
	// Trim whitespaces
	$data = trim($data);
	// Convert to lowercase
	$data = strtolower($data);
	// Remove any malicious characters
	$data = filter_var($data, FILTER_SANITIZE_NUMBER_INT);
	// Initialize data type
	$data = intval($data);
	return $data;
}
/**
* This function takes a string `$data` as its input and performs the following operations:
* - Trim whitespaces using the PHP `trim` function.
* - Convert the string to lowercase using the PHP `strtolower` function.
* - Remove any question mark character '?' using the PHP `str_replace` function.
* - Remove any non-numeric characters using the PHP `filter_var` function with the `FILTER_SANITIZE_NUMBER_INT` filter.
* - Convert the sanitized string to an integer using the PHP `intval` function.
* - Return the sanitized and normalized integer.
*
* @param string $data The input string to sanitize and normalize.
* @return int The sanitized and normalized integer.
*/
function normalizeInputPaged($data){
	// Trim whitespaces
	$data = trim($data);
	// Convert to lowercase
	$data = strtolower($data);
	// remote ?
	$data = str_replace('?', '', $data);
	// Remove any malicious characters
	$data = filter_var($data, FILTER_SANITIZE_NUMBER_INT);
	// Initialize data type
	$data = intval($data);
	return $data;
}
/**
 * Disables the block editor for all posts.
 *
 * @param bool $use_block_editor Whether to use the block editor for the post. Default true.
 * @return bool Whether to use the block editor for the post. Always false.
 */
 //add_filter('use_block_editor_for_post', '__return_false', 10);


/**
 * Retrieves the URL for a category based on its slug.
 *
 * @param string $category_slug The slug of the category to retrieve the URL for.
 * @return string|false The URL of the category, or false if the category cannot be found.
 */
 function get_category_url_by_slug( $category_slug ) {
	// Retrieve the category object based on its slug.
	$category = get_category_by_slug( $category_slug );

	// If the category object exists, retrieve the URL for the category based on its term ID.
	if ( $category ) {
		return get_category_link( $category->term_id );
	} else {
		// If the category object does not exist, return false.
		return false;
	}
}

/**
 * Display an image slide with picture element and responsive sources.
 *
 * @param string $linkto The link to the slide.
 * @param int $imgsp The ID of the small image.
 * @param int $imgpc The ID of the large image.
 * @param string $alt The alt text for the image.
 * @return string The HTML for the image slide.
 */
function show_picture_slide($linkto, $imgsp, $imgpc, $alt) {
	$picture = ''; // Initialize the HTML output.
	// Get the URLs for the small and large images.
	$sourcesp = wp_get_attachment_image_url($imgsp, 'slide-thumbsp');
	$sourcepc = wp_get_attachment_image_url($imgpc, 'slide-thumbpc');
	// Only display the slide if there is a URL for the large image.
	if ($sourcepc) {
		$picture .= '<div class="swiper-slide">';
		$picture .= '<a href="' . $linkto . '"> <picture>';
		$picture .= $sourcesp ? '<source media="(max-width: 767px)" srcset="' . $sourcesp . '">' : '';
		if ($sourcepc) {
			$picture .= '<source media="(min-width: 768px)" srcset="' . $sourcepc . '">';
			$picture .= '<img src="' . $sourcepc . '" alt="' . $alt . '">';
		}
		$picture .= '</picture></a></div>';
	}

	return $picture;
}

/**
 * Generate HTML for post thumbnails
 *
 * @param string $size The thumbnail size to use
 * @return string The HTML for the post thumbnail image
 */
function get_handle_thumbnail($size){
	$placeholder = defined("PLACEHOLDER-".$size) ? constant("PLACEHOLDER-".$size) : null;
	$images = '';
	if ( has_post_thumbnail() ) {
	  $images = '<img src="'. get_the_post_thumbnail_url(null, $size).'" alt="'. get_the_title() .'" loading="lazy">';
	}
	elseif(!empty($placeholder)){
		$images = '<img src="'. $placeholder . '" alt="'. get_the_title() .'" loading="lazy">';
	}
	else{
		$images = '<img src="'. constant('PLACEHOLDER-THUMB') . '" alt="'. get_the_title() .'" loading="lazy">';
	}
	return $images;
}


/**
 * Generate HTML for displaying post thumbnails
 *
 * @param string $size The thumbnail size to use
 * @return string The HTML for the post thumbnail image
 */
function handle_thumbnail($size){
	$placeholder = defined("PLACEHOLDER-".$size) ? constant("PLACEHOLDER-".$size) : null;
	$images = '';
	if ( has_post_thumbnail() ) {
		$images = the_post_thumbnail($size, array('loading' => 'lazy', 'alt'   => get_the_title() ) );
	}
	elseif(!empty($placeholder)){
		$images = '<img src="'. $placeholder . '" alt="'. get_the_title() .'" loading="lazy">';
	}
	else{
		$images = '<img src="'. constant('PLACEHOLDER-THUMB') . '" alt="'. get_the_title() .'" loading="lazy">';
	}
	return print $images;
}

/**
 * Generate HTML for displaying thumbnails by id
 *
 * @param int $id  attachment image
 * @param string $size The thumbnail size to use
 * @param string $alt alt image
 * @return string The HTML for the post thumbnail image
 */
function handle_thumbnail_id($id, $size = 'NEWS-THUMB', $alt = ''){
	$placeholder = defined("PLACEHOLDER-".$size) ? constant("PLACEHOLDER-".$size) : null;
	$images = wp_get_attachment_image_url($id, $size);
	if ($images) {
	  $images = '<figure><img src="'. wp_get_attachment_image_url($id, $size).'" alt="'. $alt .'" loading="lazy"></figure>';
	}
	elseif(!empty($placeholder)){
		$images = '<img src="'. $placeholder . '" alt="'. alt() .'" loading="lazy">';
	}
	else{
		$images = '<img src="'. constant('PLACEHOLDER-THUMB') . '" alt="'. $alt .'" loading="lazy">';
	}
	return print $images;
}

// REMOVE WP EMOJI
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );

// Disable global-styles-inline-css
add_action( 'wp_enqueue_scripts', 'remove_global_styles' );
function remove_global_styles(){
    wp_dequeue_style( 'global-styles' );
		wp_dequeue_style( 'classic-theme-styles' );
		wp_dequeue_style( 'wp-block-library' );
}

function get_id_by_slug($page_slug) {
	// $page_slug = "parent-page"; in case of parent page
	// $page_slug = "parent-page/sub-page"; in case of inner page
	$page = get_page_by_path($page_slug);
	if ($page) {
			return $page->ID;
	} else {
			return null;
	}
}
// hide admin bar
//add_filter('show_admin_bar', '__return_false');

/**
 * Get the slug of a category by its name.
 *
 * @param string $category_name The name of the category.
 *
 * @return string|null The slug of the category, or null if it does not exist.
 */
function get_category_slug_by_name( $category_name, $taxonomy = 'category' ) {
	$category = get_term_by( 'name', $category_name, $taxonomy );
	if ( $category ) {
			return $category->slug;
	} else {
			return null;
	}
}
function show_category_by_tems($terms = 'category', $param = ''){
	$termData = get_terms($terms, array('hide_empty' => false));
	if(!empty($termData)){
		$categoryHtml = '<ul class="m-list-checkbox">';
		$categoryHtml .= '<li class="m-list-checkbox__item">
												<div class="m-checkbox">
													<label>
														<input type="checkbox" id="selectAll"><span class="m-checkbox_label">すべて</span>
													</label>
												</div>
											</li>';
		foreach($termData as $item){
			$is_check = ($item->name == $param) ? 'checked' : false;
			$categoryHtml .= '<li class="m-list-checkbox__item"><div class="m-checkbox"><label>';
			$categoryHtml .= '<input '. $is_check .' name="category" class="checkbox" type="checkbox" value-name="'.'&nbsp;&nbsp;'. $item->name .'" value="'. $item->term_id .'"><span class="m-checkbox_label">'. $item->name .'</span>';
			$categoryHtml .= '</label></div>';
		}
		$categoryHtml .= '</ul>';
		return $categoryHtml;
	}
	return '';
}

	// remove width & height attributes from images
	function remove_img_attr ($html)
	{
		return preg_replace('/(width|height)="\d+"\s/', "", $html);
	}
	add_filter( 'post_thumbnail_html', 'remove_img_attr' );

	add_filter( 'jetpack_enable_open_graph', '__return_false' );

	// Get all years posts publish;
	function get_posts_years_array( $post_types = 'post' ) {
		global $wpdb;

		if ( ! is_array( $post_types ) ) {
			$post_types = array( $post_types );
		}

		$prefix = $wpdb->prefix;
		$types_placeholder = implode( ', ', array_fill( 0, count( $post_types ), '%s' ) );
		$query = $wpdb->prepare(
			"SELECT DISTINCT YEAR(post_date) AS year FROM {$prefix}posts WHERE post_type IN ({$types_placeholder}) AND post_status = 'publish' ORDER BY post_date DESC",
			$post_types
		);
		$results = $wpdb->get_results( $query, OBJECT_K );

		if ( empty( $results ) ) {
			return '';
		}

		$year_html = '';
		foreach ( $results as $year => $data ) {
			$year_html .= sprintf(
				'<li class="swiper-slide" data-box="%1$s年" value="%2$s">%1$s年</li>',
				esc_attr( $year ),
				$year
			);
		}

		return $year_html;
	}