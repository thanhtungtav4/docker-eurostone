<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://wordpress.org/
 * @since      1.0.0
 *
 * @package    Remove_Taxonmy_Slug
 * @subpackage Remove_Taxonmy_Slug/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Remove_Taxonmy_Slug
 * @subpackage Remove_Taxonmy_Slug/public
 * @author     Akshay <akshay.shah5189@gmail.com>
 */
class Remove_Taxonmy_Slug_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		add_filter( 'request', array( $this, 'remtaxslug_change_term_request'), 1, 1);
		add_filter( 'term_link', array( $this,'remtaxslug_term_permalink' ), 10, 2);
		add_filter( 'get_category_link', array( $this,'remtaxslug_term_permalink' ), 10, 3);
		add_filter( 'get_term_link', array( $this,'remtaxslug_term_permalink' ), 10, 3);
		add_filter( 'category_link', array( $this,'remtaxslug_term_permalink' ), 10, 3);



	}


	/**
	 * Removed the slug from the request filter
	 *
	 * @since    1.0.0
	 */
	public function remtaxslug_change_term_request( $query ){

		// specify you taxonomy name here, it can be also 'category' or 'post_tag'

		$tax_name = get_option('remove_taxnomy_slug', true);
		$tax_name = apply_filters('remove_taxonmy_slug_filter', $tax_name);


		foreach ($tax_name as $key => $tax_name) {
			// Request for child terms differs, we should make an additional check
			if (
				array_key_exists( 'attachment', $query )
				&& $query['attachment']
			){
				$include_children	= true;
				$name	= $query['attachment'];

			}else{
				if ( array_key_exists( 'name', $query ) ) {
					$include_children = false;
					$name = $query['name'];
				}else{
					$include_children = false;
					$name = '';
				}

			}

			// get the current term to make sure it exists
			$term = get_term_by( 'slug', $name, $tax_name);

			// check it here

			if ( isset( $name ) && ! empty( $term ) && ! is_wp_error( $term ) ):

				if ( $include_children ) {
					if ( array_key_exists( 'attachment', $query ) ){
						unset( $query['attachment'] );
					}
					$parent = $term->parent;
					while ( $parent ) {
						$parent_term = get_term($parent, $tax_name);
						$name = $parent_term->slug . '/' . $name;
						$parent = $parent_term->parent;
					}
				} else {
					unset( $query['name'] );
				}

				switch ( $tax_name ):
				case 'category':{
							$query['category_name'] = $name; // for categories
							break;
					}
				case 'post_tag':{
						$query['tag'] = $name; // for post tags
						break;
					}
				default:{
						$query[$tax_name] = $name; // for another taxonomies
						break;
					}
					endswitch;

			endif;

		}

		return $query;
	}

	/**
	 * This will removed the slug form the default wordpress function
	 *
	 * @param [type] $url
	 * @param [type] $term
	 * @param [type] $taxonomy
	 * @since    1.0.0
	 */
	public function remtaxslug_term_permalink($url, $taxonomy){

		$saved_value = get_option( 'remove_taxnomy_slug', true );
		$saved_value = apply_filters( 'remove_taxonmy_slug_filter', $saved_value );


		foreach ( $saved_value as $key => $value) {
			$taxonomy_name = $value;
			$taxonomy_slug = $value;
			// exit the function if taxonomy slug is not in URL
			if (
				strpos($url, $taxonomy_slug) === false
				|| $taxonomy != $taxonomy_name
			) {
				return $url;
			}
			$url = str_replace('/' . $taxonomy_slug, '', $url);
		}
		return $url;

	}

}
