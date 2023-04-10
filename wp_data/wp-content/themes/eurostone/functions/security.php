<?php
	// Close comments on the front-end
	add_filter('comments_open', '__return_false', 20, 2);
	add_filter('pings_open', '__return_false', 20, 2);

	// Disable WordPress XMLRPC.php
	add_filter( 'xmlrpc_enabled', '__return_false' );

	// redirect your xmlrpc.php request to 404
	add_filter('xmlrpc_enabled', '__return_false');

	add_action ('init', function () {
		global $pagenow; // get current page
		if (!empty($pagenow) && ( $pagenow === 'xmlrpc.php' || $pagenow === 'wp-trackback.php' || $pagenow === 'wp-links-opml.php' || $pagenow === 'license.txt' || $pagenow === 'readme.html')) {
				get_template_part(404);
				exit();
			}
				return;
		});
	add_filter( 'wp_headers', 'yourprefix_remove_x_pingback' );
	function yourprefix_remove_x_pingback( $headers )
	{
		unset( $headers['X-Pingback'] );
		return $headers;
	}

	function disable_xmlrpc_ping ($methods) {
		unset( $methods['pingback.ping'] );
		return $methods;
	}
	add_filter( 'xmlrpc_methods', 'disable_xmlrpc_ping');

	// !redirect your xmlrpc.php request to 404

	// Disable /users rest routes
		add_filter('rest_endpoints', function( $endpoints ) {
			if ( isset( $endpoints['/wp/v2/users'] ) ) {
					unset( $endpoints['/wp/v2/users'] );
			}
			if ( isset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] ) ) {
					unset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
			}
			return $endpoints;
		});
	// !Disable /users rest routes
	function redirect_readme_html() {
		$request_uri = $_SERVER['REQUEST_URI'];
		$readme_path = ABSPATH . 'readme.html';

		if ( $request_uri === '/readme.html' && file_exists( $readme_path ) ) {
				header( 'HTTP/1.0 404 Not Found' );
				include( get_404_template() );
				exit();
		}
	}

	add_action( 'template_redirect', 'redirect_readme_html' );
