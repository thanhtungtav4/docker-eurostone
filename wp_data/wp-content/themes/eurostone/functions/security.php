<?php
	// redirect your xmlrpc.php request to 404
	add_filter('xmlrpc_enabled', '__return_false');

	add_action ('init', function () {
		global $pagenow; // get current page
			if (!empty($pagenow) && 'xmlrpc.php' === $pagenow) {
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