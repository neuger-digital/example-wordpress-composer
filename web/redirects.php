<?php
/**
 * Neuger Redirects
 *
 * @package WordPress
 * @subpackage Neuger
 */

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

// URI structure: [protocol]:[http_host][request_uri (contains query_string)][hash].

// Remove query string from request URI (for now); this will be added to the.
if ( isset( $_SERVER['QUERY_STRING'] ) && isset( $_SERVER['REQUEST_URI'] ) ) {
	$request_page = str_replace( '?' . $_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI'] );

	// Trim slash at end of URI (for now).
	$request_page = rtrim( $request_page, '/' );

	// Check to see whether the request page is in the $redirect_targets array.
	if ( isset( $redirect_targets[ $request_page ] ) && 'cli' !== php_sapi_name() ) {

		// Get target redirect page URI.
		$redirect_uri = $redirect_targets[ $request_page ];

		// Find and remove redirect URI hash (for now)
		$hash = '';
		$hash_pos = strpos( $redirect_uri, '#' );

		if ( false !== $hash_pos ) {
			$hash = substr( $redirect_uri, $hash_pos );

			if ( $hash ) {
				$redirect_uri = str_replace( $hash, '', $redirect_uri );
			}
		}

		// Add slash back to end of URI.
		$redirect_uri = rtrim( $redirect_uri, '/' ) . '/';

		// Add request query string back to end of URI.
		if ( $keep_query_strings_after_redirect && ! empty( $_SERVER['QUERY_STRING'] ) ) {
			$redirect_uri .= '?' . $_SERVER['QUERY_STRING'];
		}

		// Add hash to end of URI.
		if ( ! empty( $hash ) ) {
			$redirect_uri .= $hash;
		}

		// Redirect to new location.
		header( 'HTTP/1.0 301 Moved Permanently' );
		header( 'Location: https://' . $_SERVER['HTTP_HOST'] . $redirect_uri );

		if ( extension_loaded( 'newrelic' ) ) {
			newrelic_name_transaction( 'redirect' );
		}
		exit();
	}

	if ( isset( $_ENV['PANTHEON_ENVIRONMENT'] ) && php_sapi_name() !== 'cli' ) {
		// Redirect to https://$primary_domain in the Live environment.
		if ( 'live' === $_ENV['PANTHEON_ENVIRONMENT'] ) {
			/** Replace www.example.com with your registered domain name */
			$primary_domain = 'example-wordpress-composer.com';
		} else {
			// Redirect to HTTPS on every Pantheon environment.
			$primary_domain = $_SERVER['HTTP_HOST'];
		}

		if ( $primary_domain !== $_SERVER['HTTP_HOST']
				|| ! isset( $_SERVER['HTTP_USER_AGENT_HTTPS'] )
				|| 'ON' !== $_SERVER['HTTP_USER_AGENT_HTTPS'] ) {

			// Name transaction "redirect" in New Relic for improved reporting (optional).
			if ( extension_loaded( 'newrelic' ) ) {
				newrelic_name_transaction( 'redirect' );
			}

			header( 'HTTP/1.0 301 Moved Permanently' );
			header( 'Location: https://' . $primary_domain . $_SERVER['REQUEST_URI'] );
			exit();
		}
	}
}
