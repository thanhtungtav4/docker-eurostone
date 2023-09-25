<?php
function cptui_register_my_cpts() {

	/**
	 * Post Type: Sản Phẩm.
	 */

	$labels = [
		"name" => esc_html__( "Sản Phẩm", "eurostone" ),
		"singular_name" => esc_html__( "Sản Phẩm", "eurostone" ),
	];

	$args = [
		"label" => esc_html__( "Sản Phẩm", "eurostone" ),
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
		"can_export" => true,
		"rewrite" => [ "slug" => "product", "with_front" => false ],
		"query_var" => true,
		"menu_icon" => "dashicons-admin-collapse",
		"supports" => [ "title", "editor", "thumbnail", "excerpt", "custom-fields", "page-attributes" ],
		"taxonomies" => [ "post_tag" ],
		"show_in_graphql" => false,
	];

	register_post_type( "product", $args );

	/**
	 * Post Type: ShowRoom.
	 */

	$labels = [
		"name" => esc_html__( "ShowRoom", "eurostone" ),
		"singular_name" => esc_html__( "ShowRoom", "eurostone" ),
	];

	$args = [
		"label" => esc_html__( "ShowRoom", "eurostone" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => false,
		"show_ui" => true,
		"show_in_rest" => false,
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
		"can_export" => true,
		"rewrite" => [ "slug" => "showroom", "with_front" => false ],
		"query_var" => true,
		"menu_icon" => "dashicons-admin-multisite",
		"supports" => [ "title", "editor", "thumbnail", "excerpt" ],
		"show_in_graphql" => false,
	];

	register_post_type( "showroom", $args );

	/**
	 * Post Type: Công trình xây dựng.
	 */

	$labels = [
		"name" => esc_html__( "Công trình xây dựng", "eurostone" ),
		"singular_name" => esc_html__( "Công trình xây dựng", "eurostone" ),
	];

	$args = [
		"label" => esc_html__( "Công trình xây dựng", "eurostone" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => true,
		"can_export" => true,
		"rewrite" => [ "slug" => "case-studies", "with_front" => false ],
		"query_var" => true,
		"menu_icon" => "dashicons-admin-generic",
		"supports" => [ "title", "editor", "thumbnail", "excerpt", "page-attributes" ],
		"show_in_graphql" => false,
	];

	register_post_type( "construction_works", $args );
}

add_action( 'init', 'cptui_register_my_cpts' );
function cptui_register_my_taxes() {

	/**
	 * Taxonomy: Thương Hiệu.
	 */

	$labels = [
		"name" => esc_html__( "Thương Hiệu", "eurostone" ),
		"singular_name" => esc_html__( "Thương Hiệu", "eurostone" ),
	];

	
	$args = [
		"label" => esc_html__( "Thương Hiệu", "eurostone" ),
		"labels" => $labels,
		"public" => true,
		"publicly_queryable" => true,
		"hierarchical" => true,
		"show_ui" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"query_var" => true,
		"rewrite" => [ 'slug' => 'brand', 'with_front' => true,  'hierarchical' => true, ],
		"show_admin_column" => false,
		"show_in_rest" => true,
		"show_tagcloud" => false,
		"rest_base" => "brand",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"rest_namespace" => "wp/v2",
		"show_in_quick_edit" => true,
		"sort" => true,
		"show_in_graphql" => false,
	];
	register_taxonomy( "brand", [ "product" ], $args );

	/**
	 * Taxonomy: Hạng mục ốp đá.
	 */

	$labels = [
		"name" => esc_html__( "Hạng mục ốp đá", "eurostone" ),
		"singular_name" => esc_html__( "Hạng mục ốp đá", "eurostone" ),
	];

	
	$args = [
		"label" => esc_html__( "Hạng mục ốp đá", "eurostone" ),
		"labels" => $labels,
		"public" => true,
		"publicly_queryable" => true,
		"hierarchical" => true,
		"show_ui" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"query_var" => true,
		"rewrite" => [ 'slug' => 'stone-cladding', 'with_front' => false,  'hierarchical' => true, ],
		"show_admin_column" => false,
		"show_in_rest" => true,
		"show_tagcloud" => false,
		"rest_base" => "stone-cladding",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"rest_namespace" => "wp/v2",
		"show_in_quick_edit" => true,
		"sort" => true,
		"show_in_graphql" => false,
	];
	register_taxonomy( "stone-cladding", [ "product" ], $args );

	/**
	 * Taxonomy: Doanh mục thi công.
	 */

	$labels = [
		"name" => esc_html__( "Doanh mục thi công", "eurostone" ),
		"singular_name" => esc_html__( "Doanh mục thi công", "eurostone" ),
	];

	
	$args = [
		"label" => esc_html__( "Doanh mục thi công", "eurostone" ),
		"labels" => $labels,
		"public" => true,
		"publicly_queryable" => true,
		"hierarchical" => true,
		"show_ui" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"query_var" => true,
		"rewrite" => [ 'slug' => 'category-construction', 'with_front' => false,  'hierarchical' => true, ],
		"show_admin_column" => false,
		"show_in_rest" => true,
		"show_tagcloud" => false,
		"rest_base" => "category-construction",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"rest_namespace" => "wp/v2",
		"show_in_quick_edit" => true,
		"sort" => true,
		"show_in_graphql" => false,
	];
	register_taxonomy( "category-construction", [ "construction_works" ], $args );

	/**
	 * Taxonomy: Màu sắc.
	 */

	$labels = [
		"name" => esc_html__( "Màu sắc", "eurostone" ),
		"singular_name" => esc_html__( "Màu sắc", "eurostone" ),
	];

	
	$args = [
		"label" => esc_html__( "Màu sắc", "eurostone" ),
		"labels" => $labels,
		"public" => true,
		"publicly_queryable" => true,
		"hierarchical" => true,
		"show_ui" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"query_var" => true,
		"rewrite" => [ 'slug' => 'colors', 'with_front' => true, ],
		"show_admin_column" => false,
		"show_in_rest" => true,
		"show_tagcloud" => false,
		"rest_base" => "colors",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"rest_namespace" => "wp/v2",
		"show_in_quick_edit" => false,
		"sort" => false,
		"show_in_graphql" => false,
	];
	register_taxonomy( "colors", [ "product" ], $args );
}
add_action( 'init', 'cptui_register_my_taxes' );