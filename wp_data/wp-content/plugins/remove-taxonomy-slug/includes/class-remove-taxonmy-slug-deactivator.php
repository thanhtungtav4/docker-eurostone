<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://wordpress.org/
 * @since      1.0.0
 *
 * @package    Remove_Taxonmy_Slug
 * @subpackage Remove_Taxonmy_Slug/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Remove_Taxonmy_Slug
 * @subpackage Remove_Taxonmy_Slug/includes
 * @author     Akshay <akshay.shah5189@gmail.com>
 */
class Remove_Taxonmy_Slug_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		delete_option( 'remove_taxnomy_slug');
	}

}
