<?php

/**
 * Fired during plugin activation
 *
 * @link       https://wordpress.org/
 * @since      1.0.0
 *
 * @package    Remove_Taxonmy_Slug
 * @subpackage Remove_Taxonmy_Slug/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Remove_Taxonmy_Slug
 * @subpackage Remove_Taxonmy_Slug/includes
 * @author     Akshay <akshay.shah5189@gmail.com>
 */
class Remove_Taxonmy_Slug_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		//remove_taxnomy_slug
		$data = array();
		update_option('remove_taxnomy_slug', $data);

	}

}
