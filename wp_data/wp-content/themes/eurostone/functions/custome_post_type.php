<?php
function cptui_register_my_cpts() {

	/**
	 * Post Type: Contents.
	 */

	$labels = [
		"name" => esc_html__( "Contents", "Corporate" ),
		"singular_name" => esc_html__( "Contents", "Corporate" ),
	];

	$args = [
		"label" => esc_html__( "Contents", "Corporate" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => false,
		"rewrite" => [ "slug" => "content", "with_front" => false ],
		"query_var" => true,
		"supports" => [ "title", "editor", "thumbnail", "excerpt" ],
		"show_in_graphql" => false,
	];

	register_post_type( "content", $args );
}

add_action( 'init', 'cptui_register_my_cpts' );

function cptui_register_my_taxes_category_content() {

	/**
	 * Taxonomy: Category Content.
	 */

	$labels = [
		"name" => esc_html__( "Category Content", "Corporate" ),
		"singular_name" => esc_html__( "Category Content", "Corporate" ),
	];


	$args = [
		"label" => esc_html__( "Category Content", "Corporate" ),
		"labels" => $labels,
		"public" => false,
		"publicly_queryable" => true,
		"hierarchical" => true,
		"show_ui" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"query_var" => true,
		"rewrite" => [ 'slug' => 'category-content', 'with_front' => true, ],
		"show_admin_column" => true,
		"show_in_rest" => true,
		"show_tagcloud" => true,
		"rest_base" => "category-content",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"rest_namespace" => "wp/v2",
		"show_in_quick_edit" => true,
		"sort" => true,
		"show_in_graphql" => false,
	];
	register_taxonomy( "category-content", [ "content" ], $args );
}
add_action( 'init', 'cptui_register_my_taxes_category_content' );