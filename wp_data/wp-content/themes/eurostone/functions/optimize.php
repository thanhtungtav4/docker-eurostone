<?php
// Remove Query Strings
function remove_cssjs_ver( $src ) {
	if( strpos( $src, '?ver=' ) )
	 $src = remove_query_arg( 'ver', $src );
	return $src;
}
add_filter( 'style_loader_src', 'remove_cssjs_ver', 10, 2 );
add_filter( 'script_loader_src', 'remove_cssjs_ver', 10, 2 );
// ! Remove Query Strings
// Remove RSD Links
remove_action( 'wp_head', 'rsd_link' ) ;
// ! Remove RSD Links
#Disable Self Pingbacks in WordPress Using Plugins
function no_self_ping( &$links ) {
	$home = get_option( 'home' );
	foreach ( $links as $l => $link )
		if ( 0 === strpos( $link, $home ) )
			unset($links[$l]);
}
add_action( 'pre_ping', 'no_self_ping' );
add_filter('xmlrpc_enabled', '__return_false');
#!Disable Self Pingbacks in WordPress Using Plugins
/***
  * DISABLE EMOJIS IN WORDPRESS
*/
function disable_emojis() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	// Remove from TinyMCE
	add_filter( 'tiny_mce_plugins', 'disable_emojis_tinymce' );
	wp_dequeue_script( 'wp-embed' );
}
add_action( 'init', 'disable_emojis' );
/**
 * Filter out the tinymce emoji plugin.
 */
function disable_emoji_feature() {
	// Prevent Emoji from loading on the front-end
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	// Remove from admin area also
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	// Remove from RSS feeds also
	remove_filter( 'the_content_feed', 'wp_staticize_emoji');
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji');
	// Remove from Embeds
	remove_filter( 'embed_head', 'print_emoji_detection_script' );
	// Remove from emails
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	// Disable from TinyMCE editor. Currently disabled in block editor by default
	add_filter( 'tiny_mce_plugins', 'disable_emojis_tinymce' );
	remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
	/** Finally, prevent character conversion too
         ** without this, emojis still work
         ** if it is available on the user's device
	 */
	add_filter( 'option_use_smilies', '__return_false' );
}
function disable_emojis_tinymce( $plugins ) {
	if( is_array($plugins) ) {
		$plugins = array_diff( $plugins, array( 'wpemoji' ) );
	}
	return $plugins;
}
add_action('init', 'disable_emoji_feature');
add_filter( 'option_use_smilies', '__return_false' );
/***
  * !DISABLE EMOJIS IN WORDPRESS
*/
// Hide WordPress Version
remove_action( 'wp_head', 'wp_generator' ) ;
// ! Hide WordPress Version
// Remove WLManifest Link
remove_action( 'wp_head', 'wlwmanifest_link' ) ;
// !Remove WLManifest Link

// Disable Heartbeat
add_action( 'init', 'stop_heartbeat', 1 );
function stop_heartbeat() {
	wp_deregister_script('heartbeat');
}
// ! Disable Heartbeat
//Disable Dashicons on Front-end
function wpdocs_dequeue_dashicon() {
	if (current_user_can( 'update_core' )) {
			return;
	}
	wp_deregister_style('dashicons');
}
add_action( 'wp_enqueue_scripts', 'wpdocs_dequeue_dashicon' );
// !Disable Dashicons on Front-end
// Disable Contact Form 7 CSS/JS on Every Page
add_filter( 'wpcf7_load_js', '__return_false' );
add_filter( 'wpcf7_load_css', '__return_false' );
// !Disable Contact Form 7 CSS/JS on Every Page
// Disable Unnecessary Dashboard Widgets
function remove_dashboard_widgets() {
	global $wp_meta_boxes;
	unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
  unset($wp_meta_boxes['dashboard']['normal']['core']['normal-sortables']);
  unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_site_health']);
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_drafts']);
	unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
	unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
	unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
	unset($wp_meta_boxes['dashboard']['normal']);
}
add_action('wp_dashboard_setup', 'remove_dashboard_widgets' );
// ! Disable Unnecessary Dashboard Widgets

