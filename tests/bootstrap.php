<?php
/**
 * PHPUnit bootstrap file
 *
 * @since 2.4.4.1
 *
 * @link https://github.com/JDGrimes/wpppb
 *
 * @package all-in-one-seo-pack
 */
/**
 * Bootstrap Configs
 */
// Full path, no trailing slash.
$wp_develop_dir   = 'C:/path-to-location/wordpress-develop';
$wp_content       = 'C:/path-to-location/wp-content';
$mu_plugin_dir    = 'C:/path-to-location/mu-plugins';
$plugin_dir       = 'C:/path-to-location/plugins';
$wp_default_theme = 'default';// Default: twentyseventeen.

// Disable xdebug backtrace. Why? Does it cause issues?
// @link https://xdebug.org/docs/all_functions
if ( function_exists( 'xdebug_disable' ) ) {
	xdebug_disable();
}

/**
 * WordPress Directory
 */
if ( false !== getenv( 'WP_DEVELOP_DIR' ) ) {
	$wp_develop_dir = getenv( 'WP_DEVELOP_DIR' );
} elseif ( empty( $wp_develop_dir ) || 'C:/path-to-location/wordpress-develop' === $wp_develop_dir ) {
	die( 'WP_DEVELOP_DIR isn\'t being set correctly in all-in-one-seo-pack/tests/bootstrap.php' );
}

/**
 * (MU) Plugin Directory
 *
 * 1. Manually set Directory (Configured above).
 * 2. Set Environment Var in IDE.
 * 3. Force directory to plugin's root folder.
 *
 * OPTION 2 DETAILS
 *
 * In PHPStorm, settings are located in Run > Edit Configurations.
 * Add PHPUnit as a Run Environment.
 * From there, you will need to...
 *   1. Give the configuration a name, for example: PHP Unit.
 *   2. Check the option ‘defined in the configuration file’.
 *   3. Check ‘use alternative configuration file’ and set the path to the phpunit.xml file.
 *   4. Set environment variables, you can do this by clicking the ‘…’ after the input field. You’ll get the next screen:
 * In Step 4, you'll add the Environment Variables.
 */

// WP Content Directory.
if ( false !== getenv( 'WP_CONTENT' ) ) {
	// Opt 2.
	$wp_content = getenv( 'WP_CONTENT' );
} elseif ( ! isset( $wp_content ) || empty( $wp_content ) || 'C:/path-to-location/wp-content' === $wp_content ) {
	// Opt 3.
	$wp_content = $wp_develop_dir . '/src/wp-content';
}

// MU Plugins Directory.
if ( false !== getenv( 'WPMU_PLUGIN_DIR' ) ) {
	// Opt 2.
	$mu_plugin_dir = getenv( 'WPMU_PLUGIN_DIR' );
} elseif ( ! isset( $mu_plugin_dir ) || empty( $mu_plugin_dir ) || 'C:/path-to-location/mu-plugins' === $mu_plugin_dir ) {
	// Opt 3.
	$mu_plugin_dir = $wp_content . '/mu-plugins';
}

// Plugins Directory.
if ( false !== getenv( 'WP_PLUGIN_DIR' ) ) {
	// Opt 2.
	$plugin_dir = getenv( 'WP_PLUGIN_DIR' );
} elseif ( ! isset( $plugin_dir ) || empty( $plugin_dir ) || 'C:/path-to-location/plugins' === $plugin_dir ) {
	// Opt 3.
	$plugin_dir = $wp_content . '/plugins';
}

/**
 * Theme Default
 *
 * ToDo Add set Theme directory.
 */
if ( false !== getenv( 'WP_DEFAULT_THEME' ) ) {
	$wp_default_theme = getenv( 'WP_DEFAULT_THEME' );
}

/**
 * CONSTANTS
 */
define( 'WP_DEVELOP_DIR', $wp_develop_dir );
define( 'WP_CORE_DIR', $wp_develop_dir . '/src' );
define( 'WP_TESTS_DIR', $wp_develop_dir . '/tests/phpunit' );

define( 'AIOSEOP_ROOT_DIR', dirname( dirname( __DIR__ ) ) );
define( 'AIOSEOP_CORE_DIR', dirname( __DIR__ ) );
define( 'AIOSEOP_TESTS_DIR', __DIR__ );

define( 'WP_USE_THEMES', false );
define( 'WP_TESTS_FORCE_KNOWN_BUGS', true );
define( 'AIOSEOP_UNIT_TESTING', true );
define( 'AIOSEOP_UNIT_TESTING_DIR', dirname( __FILE__ ) );

// WP Constants Pre-Defined Configurations.
if ( isset( $wp_content ) && ! empty( $wp_content ) ) {
	define( 'WP_CONTENT', $wp_content );
}
if ( isset( $mu_plugin_dir ) && ! empty( $mu_plugin_dir ) ) {
	define( 'WPMU_PLUGIN_DIR', $mu_plugin_dir );
}
if ( isset( $plugin_dir ) && ! empty( $plugin_dir ) ) {
	define( 'WP_PLUGIN_DIR', $plugin_dir );
}
//if ( isset( $wp_default_theme ) && ! empty( $wp_default_theme ) ) {
//	define( 'WP_DEFAULT_THEME', $wp_default_theme );
//}

/**
 * Manually Activate Plugin
 *
 * This conciders WP's plugin environment, and using WP's structure of the Plugins folder.
 *
 * NOTE: Last checked at WP 4.9.
 * It is not possible to isolate individual plugin files & folders, and preserve some of the initial operations.
 * This is due to the way WP checks for plugin folders that exist in the `WP_PLUGIN_DIR`,
 * as well as basing the activation off that directory. There is no available hook that would allow modifying that
 * differation between individual plugins.
 *
 * Either have to force load it using `_manually_load_plugin` with any directory,
 * or base the plugin(s) in `WP_PLUGIN_DIR` and use `$GLOBALS['wp_tests_options']`.
 */
$GLOBALS['wp_tests_options'] = array(
	'active_plugins' => array( basename( dirname( __DIR__ ) ) . '/all_in_one_seo_pack.php' ),
	'template'       => 'twentyeleven',
);
//function _manually_activate_plugin() {
//	activate_plugin( basename( dirname( __DIR__ ) ) . '/all_in_one_seo_pack.php' );
//}
// Give access to tests_add_filter() function.
//require_once WP_DEVELOP_DIR . '/tests/phpunit/includes/functions.php';
//tests_add_filter( 'plugins_loaded', '_manually_activate_plugin' );

/**
 * Manually load the plugin being tested.
 *
 * Manually load AIOSEOP when WP's Bootstrap loads wp-settings.php
 *
 * @link https://core.trac.wordpress.org/ticket/23690
 * @link https://wordpress.stackexchange.com/questions/145281/phpunit-testing-wordpress-plugin
 */
function _manually_load_plugin() {
	require dirname( dirname( __FILE__ ) ) . '/all_in_one_seo_pack.php';
}
// Give access to tests_add_filter() function.
//require_once WP_DEVELOP_DIR . '/tests/phpunit/includes/functions.php';
//tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

// Start up the WP testing environment.
require( WP_DEVELOP_DIR . '/tests/phpunit/includes/bootstrap.php' );

/**
 * Common Environment Variables
 */
global $current_user;
$current_user = new WP_User( 1 );
$current_user->set_role( 'administrator' );
wp_update_user( array( 'ID' => 1, 'first_name' => 'Admin', 'last_name' => 'User' ) );
