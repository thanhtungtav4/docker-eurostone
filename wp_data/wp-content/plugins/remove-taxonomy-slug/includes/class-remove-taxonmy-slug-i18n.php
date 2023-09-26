<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://wordpress.org/
 * @since      1.0.0
 *
 * @package    Remove_Taxonmy_Slug
 * @subpackage Remove_Taxonmy_Slug/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Remove_Taxonmy_Slug
 * @subpackage Remove_Taxonmy_Slug/includes
 * @author     Akshay <akshay.shah5189@gmail.com>
 */
class Remove_Taxonmy_Slug_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'remove-taxonmy-slug',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
