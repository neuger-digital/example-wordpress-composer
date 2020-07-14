<?php
/**
 * Neuger Example WordPress Composer WP Config
 *
 * @package  ExampleWordPressComposer
 * @since   ExampleWordPressComposer 0.1
 */

// Don't show deprecations.
error_reporting( E_ALL ^ E_DEPRECATED );

// Set root path.
$root_path = realpath( __DIR__ . '/..' );

// Include the Composer autoload.
require_once $root_path . '/vendor/autoload.php';

/*
 * Fetch .env
 */
if ( ! isset( $_ENV['PANTHEON_ENVIRONMENT'] ) && file_exists( $root_path . '/.env' ) ) {
	$dotenv = Dotenv\Dotenv::create( $root_path );
	$dotenv->load();
	$dotenv->required(
		array(
			'DB_NAME',
			'DB_USER',
			'DB_HOST',
		)
	)->notEmpty();
}

/**
 * Disallow on server file edits
 */
define( 'DISALLOW_FILE_EDIT', true );
define( 'DISALLOW_FILE_MODS', true );

/**
 * Force SSL
 */
define( 'FORCE_SSL_ADMIN', true );

/**
 * Limit post revisions
 */
define( 'WP_POST_REVISIONS', 3 );

/*
 * If NOT on Pantheon
 */
if ( ! isset( $_ENV['PANTHEON_ENVIRONMENT'] ) ) :
	/**
	 * Define site and home URLs
	 */
	// HTTP is still the default scheme for now.
	$scheme = 'http';
	// If we have detected that the end use is HTTPS, make sure we pass that
	// through here, so <img> tags and the like don't generate mixed-mode
	// content warnings.
	if ( isset( $_SERVER['HTTP_USER_AGENT_HTTPS'] ) && 'ON' === $_SERVER['HTTP_USER_AGENT_HTTPS'] ) {
		$scheme = 'https';
	}
	$site_url = getenv( 'WP_HOME' ) !== false ? getenv( 'WP_HOME' ) : $scheme . '://' . $_SERVER['HTTP_HOST'] . '/';
	define( 'WP_HOME', $site_url );
	define( 'WP_SITEURL', $site_url . 'wp/' );

	/**
	 * Set Database Details
	 */
	define( 'DB_NAME', getenv( 'DB_NAME' ) );
	define( 'DB_USER', getenv( 'DB_USER' ) );
	define( 'DB_PASSWORD', getenv( 'DB_PASSWORD' ) !== false ? getenv( 'DB_PASSWORD' ) : '' );
	define( 'DB_HOST', getenv( 'DB_HOST' ) );

	/**
	 * Set debug modes
	 */
	define( 'WP_DEBUG', getenv( 'WP_DEBUG' ) === 'true' ? true : false );
	define( 'IS_LOCAL', getenv( 'IS_LOCAL' ) !== false ? true : false );

	/**#@+
	 * Authentication Unique Keys and Salts.
	 *
	 * Change these to different unique phrases!
	 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
	 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
	 *
	 * @since 2.6.0
	 */
	define('AUTH_KEY',         '(P0{6bf7uvUxmaRh=(^km{+GL/#9|;/X-O]eF`;oXfV57;L%&jJqr|-b+T?EWph#');
	define('SECURE_AUTH_KEY',  'aH] qAIi]ICsNN$BRQoAV/B:ecRNxRv:tl,dHhD$TUT==_u)Yao_+@g.)nf+U@zf');
	define('LOGGED_IN_KEY',    '|-|c70LC/.A#8,OB%A06/xg${hYgx)s$<7+<VKxXJk^N5xs 8=[.]g(bT>/l#yyq');
	define('NONCE_KEY',        'X2sNCS9.28D|,~ySnA= y%}^sb{O%z_0+O?c_[U(F-w$I{hg`xwk9L/-~|AY-!(B');
	define('AUTH_SALT',        'p(V#)._9[mz#-+~5^Rb#/A8W4HTgA4|@2N@VmvZ+BoZ/ojUrwqSg>KJTQfb:B([N');
	define('SECURE_AUTH_SALT', 'GQ6B,pDPJ&;A=ym`q;m|DK%z>TT ]E ~f:|CJOuVr&<[2!ru_Mn+vS(Op%:`V;4J');
	define('LOGGED_IN_SALT',   '1*h3o)*|t^;)cFh7d4mQUz-q|g||F_M,|Pp ?%95Ao*N#.RAk%I-^#nyTAGfo%kt');
	define('NONCE_SALT',       'q+=^WonA~gJ/Be*6wUvy#QD4)Lr8YC76Efl5kRyJd)ce!ZaVEi{||/rz(UEeLL?J');

endif;

/*
 * If on Pantheon
 */
if ( isset( $_ENV['PANTHEON_ENVIRONMENT'] ) ) :

	// ** MySQL settings - included in the Pantheon Environment ** //
	/** The name of the database for WordPress */
	define( 'DB_NAME', $_ENV['DB_NAME'] );

	/** MySQL database username */
	define( 'DB_USER', $_ENV['DB_USER'] );

	/** MySQL database password */
	define( 'DB_PASSWORD', $_ENV['DB_PASSWORD'] );

	/** MySQL hostname; on Pantheon this includes a specific port number. */
	define( 'DB_HOST', $_ENV['DB_HOST'] . ':' . $_ENV['DB_PORT'] );

	/** Database Charset to use in creating database tables. */
	define( 'DB_CHARSET', 'utf8' );

	/** The Database Collate type. Don't change this if in doubt. */
	define( 'DB_COLLATE', '' );

	/**#@+
	 * Authentication Unique Keys and Salts.
	 *
	 * Change these to different unique phrases!
	 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
	 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
	 *
	 * Pantheon sets these values for you also. If you want to shuffle them you
	 * can do so via your dashboard.
	 *
	 * @since 2.6.0
	 */
	define( 'AUTH_KEY', $_ENV['AUTH_KEY'] );
	define( 'SECURE_AUTH_KEY', $_ENV['SECURE_AUTH_KEY'] );
	define( 'LOGGED_IN_KEY', $_ENV['LOGGED_IN_KEY'] );
	define( 'NONCE_KEY', $_ENV['NONCE_KEY'] );
	define( 'AUTH_SALT', $_ENV['AUTH_SALT'] );
	define( 'SECURE_AUTH_SALT', $_ENV['SECURE_AUTH_SALT'] );
	define( 'LOGGED_IN_SALT', $_ENV['LOGGED_IN_SALT'] );
	define( 'NONCE_SALT', $_ENV['NONCE_SALT'] );
	/**#@-*/

	/** A couple extra tweaks to help things run well on Pantheon. */
	if ( isset( $_SERVER['HTTP_HOST'] ) ) {
		// HTTP is still the default scheme for now.
		$scheme = 'http';
		// If we have detected that the end use is HTTPS, make sure we pass that
		// through here, so <img> tags and the like don't generate mixed-mode
		// content warnings.
		if ( isset( $_SERVER['HTTP_USER_AGENT_HTTPS'] ) && 'ON' === $_SERVER['HTTP_USER_AGENT_HTTPS'] ) {
			$scheme = 'https';
		}
		define( 'WP_HOME', $scheme . '://' . $_SERVER['HTTP_HOST'] );
		define( 'WP_SITEURL', $scheme . '://' . $_SERVER['HTTP_HOST'] . '/wp' );

	}
	// Don't show deprecations; useful under PHP 5.5.
	error_reporting( E_ALL ^ E_DEPRECATED );
	// Force the use of a safe temp directory when in a container.
	if ( defined( 'PANTHEON_BINDING' ) ) :
		define( 'WP_TEMP_DIR', sprintf( '/srv/bindings/%s/tmp', PANTHEON_BINDING ) );
	endif;

	// FS writes aren't permitted in test or live, so we should let WordPress know to disable relevant UI.
	if ( in_array( $_ENV['PANTHEON_ENVIRONMENT'], array( 'test', 'live' ) ) && ! defined( 'DISALLOW_FILE_MODS' ) ) :
		define( 'DISALLOW_FILE_MODS', true );
	endif;

	if ( ! defined( 'WP_DEBUG' ) ) {
		if ( in_array( $_ENV['PANTHEON_ENVIRONMENT'], array( 'test', 'live' ), true ) ) {
			// Test and live.
			define( 'WP_DEBUG', false );
		} else {
			// Dev and local.
			define( 'WP_DEBUG', true );
			define( 'WP_DISABLE_FATAL_ERROR_HANDLER', true );
		}
	}

endif;

/*
* Define wp-content directory outside of WordPress core directory
*/
define( 'WP_CONTENT_DIR', dirname( __FILE__ ) . '/wp-content' );
define( 'WP_CONTENT_URL', WP_HOME . '/wp-content' );

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = getenv( 'DB_PREFIX' ) !== false ? getenv( 'DB_PREFIX' ) : 'wp_';

/* That's all, stop editing! Happy blogging. */

// Redirect the dev site to a custom domain.
// Check to make sure that this work with multi-dev sites.
if ( isset( $_ENV['PANTHEON_ENVIRONMENT'] ) && 'cli' !== php_sapi_name() ) {
	$primary_domain = $_SERVER['HTTP_HOST'];
	// Redirect to https://$primary_domain in the Live environment.
	if ( 'dev' === $_ENV['PANTHEON_ENVIRONMENT'] ) {
		$primary_domain = 'example-wordpress-composer.neuger.site';
		if ( $_SERVER['HTTP_HOST'] !== $primary_domain && isset( $_SERVER['REQUEST_URI'] ) ) {
			header( 'HTTP/1.0 301 Moved Permanently' );
			header( 'Location: https://' . $primary_domain . $_SERVER['REQUEST_URI'] );
			exit();
		}
	}
}

/* Start Custom Redirects */

// Turn to false if you don't want query strings preserved.
$keep_query_strings_after_redirect = true;

// Custom 301 redirects (no trailing slashes).
// This is a key value pair of key = request page, value = redirect page.
// Key (request page): no hashes, no trailing slashes, no query paramemters (query parameters that the user types in will be preserved).
// Value (redirect page): hashes are allowed and no query parameters (we are preserving request query parameters).
$redirect_targets = array(
);

require_once dirname( __FILE__ ) . '/redirects.php';

/* End Custom NCG Settings */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

if ( ! defined( 'WEBPATH' ) ) {
	define( 'WEBPATH', rtrim( dirname( __FILE__ ), 'wp' ) . '/' );
}

// Sets up WordPress vars and included files.
require_once ABSPATH . 'wp-settings.php';
