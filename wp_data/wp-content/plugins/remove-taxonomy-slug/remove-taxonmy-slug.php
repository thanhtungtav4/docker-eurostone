<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://wordpress.org/
 * @since             1.0.1
 * @package           Remove_Taxonmy_Slug
 *
 * @wordpress-plugin
 * Plugin Name:       Remove Taxonomy Slug
 * Plugin URI:        https://wordpress.org/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.1
 * Author:            Akshay Shah
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       remove-taxonmy-slug
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'REMOVE_TAXONMY_SLUG_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-remove-taxonmy-slug-activator.php
 */
function activate_remove_taxonmy_slug() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-remove-taxonmy-slug-activator.php';
	Remove_Taxonmy_Slug_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-remove-taxonmy-slug-deactivator.php
 */
function deactivate_remove_taxonmy_slug() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-remove-taxonmy-slug-deactivator.php';
	Remove_Taxonmy_Slug_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_remove_taxonmy_slug' );
register_deactivation_hook( __FILE__, 'deactivate_remove_taxonmy_slug' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-remove-taxonmy-slug.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_remove_taxonmy_slug() {

	$plugin = new Remove_Taxonmy_Slug();
	$plugin->run();

}
run_remove_taxonmy_slug();


