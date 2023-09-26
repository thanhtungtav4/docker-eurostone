<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wordpress.org/
 * @since      1.0.0
 *
 * @package    Remove_Taxonmy_Slug
 * @subpackage Remove_Taxonmy_Slug/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Remove_Taxonmy_Slug
 * @subpackage Remove_Taxonmy_Slug/admin
 * @author     Akshay <akshay.shah5189@gmail.com>
 */
class Remove_Taxonmy_Slug_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		add_action( 'admin_init', array ( $this, 'retaxslug_save_settings') );

	}

	/**
	 * Register the page for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function rtaxslug_register_custom_menu_page(){

		add_menu_page(
			'Remove Taxonomy Slug',
			'Remove Taxonomy Slug Settings',
			'manage_options',
			'remove-taxonomy-slug',
			array( $this, 'retaxslug_admin_menu_template' ),
			'dashicons-tickets',
			6
		);

	}

	/**
	 * Menu page template function
	 *
	 * @since    1.0.0
	 */
	public function retaxslug_admin_menu_template(){

		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/remove-taxonmy-slug-admin-display.php';
	}

	/**
	 * Get list of all the taxonomy for the selection
	 *
	 * @since    1.0.0
	 */
	public function retaxslug_get_list_of_taxonomy(){
		$list_of_taxonomy = array();
		$list_of_taxonomy = get_taxonomies();
		return $list_of_taxonomy;
	}

	/**
	 * Get the listed of save data of removed taxonomy
	 *
	 * @since    1.0.0
	 */
	public function retaxslug_save_list_of_taxonomy(){
		$saved_data = array();

		if( ! empty ( get_option( 'remove_taxnomy_slug', false ) ) )  {
			$saved_data = get_option( 'remove_taxnomy_slug', false );
		}

		return $saved_data;

	}

	/**
	 * Save the settings of taxonomy
	 *
	 * @since    1.0.0
	 */
	public function retaxslug_save_settings(){

		if(
			!empty( (array) $_REQUEST )
			&& array_key_exists( 'remove_taxonomy_submit', $_REQUEST )
			&& $_REQUEST['remove_taxonomy_submit'] != ''
			&& 'Save Changes' === $_REQUEST['remove_taxonomy_submit']
		){
			if (
					! empty ( $_REQUEST['remove_taxnomy_slug_nonce'] )
					|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['remove_taxnomy_slug_nonce'], 'name_of_my_action' ) ) )
				) {

					add_action( 'admin_notices', array( $this, 'remtaxslug_general_admin_notice' ) );

			}

			if(
				array_key_exists( 'remve_slug_selected_taxonomy', $_REQUEST)
				&& ! empty( (array) $_REQUEST['remve_slug_selected_taxonomy'] )
				&& isset( $_REQUEST['remve_slug_selected_taxonomy'])
			){
				$remove_taxonomy_selected = array_map( 'sanitize_text_field', wp_unslash( (array) $_REQUEST['remve_slug_selected_taxonomy'] ) );

				$remove_taxonomy_selected = apply_filters(
					'remove_taxonmy_slug_filter',
					$remove_taxonomy_selected
				);
				update_option(
					'remove_taxnomy_slug',
					$remove_taxonomy_selected,
					true
				);

				add_action( 'admin_notices', array( $this, 'remtaxslug_general_admin_success' ) );
			}else{
				add_action('admin_notices', array($this, 'remtaxslug_save_admin_notice'));

			}
		}
	}

	/**
	 * Admin notice function for nonce
	 *
	 * @since    1.0.0
	 */
	public function remtaxslug_general_admin_notice(){
		global $pagenow;
		if ($pagenow == 'options-general.php') {
			echo '<div class="notice notice-warning is-dismissible">
					<p>' . __("Nonce are not working", "remove-taxonmy-slug") . '</p>
				</div>';
		}

	}

	/**
	 * Admin notice function for selection value
	 *
	 * @since    1.0.0
	 */
	public function remtaxslug_save_admin_notice(){
		global $pagenow;
		if ($pagenow == 'options-general.php') {
			echo '<div class="notice notice-warning is-dismissible">
					<p>' . __("Select at least one option", "remove-taxonmy-slug") . '</p>
				</div>';
		}

	}

	/**
	 * Admnin notice for the success
	 *
	 * @since    1.0.0
	 */
	public function remtaxslug_general_admin_success(){
		global $pagenow;

		if ( 'admin.php' === $pagenow ) {
			echo '<div class="notice notice-warning is-dismissible">
					<p>' . __("Save settings successfully", "remove-taxonmy-slug") . '</p>
				</div>';
		}

	}
}
