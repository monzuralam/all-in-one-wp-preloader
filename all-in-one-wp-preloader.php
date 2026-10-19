<?php
/**
 * Plugin Name:       All-in-One WP Preloader
 * Plugin URI:        https://wordpress.org/plugins/all-in-one-wp-preloader
 * Description:       All-in-One Preloader helps you to create awesome preloader that easy to customize, works on all major browsers and with any wp theme.
 * Version:           1.0.1
 * Requires at least: 6.6
 * Requires PHP:      5.6
 * Author:            Monzur Alam
 * Author URI:        https://profiles.wordpress.org/monzuralam
 * Text Domain:       all-in-one-wp-preloader
 * Domain Path:       /languages
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * @package AI1WP
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Plugin Version.
define( 'AI1WP_VERSION', '1.0.1' );

// Plugin File.
define( 'AI1WP_FILE', __FILE__ );

// Plugin Basename.
define( 'AI1WP_PLUGIN_BASENAME', basename( __DIR__ ) );

// Plugin Path.
define( 'AI1WP_PATH', __DIR__ );

// Plugin URL.
define( 'AI1WP_URL', plugins_url( '', __FILE__ ) );

/**
 * Autoloader
 * AI1WP\Admin\Settings => includes/Admin/Settings.php
 */
spl_autoload_register(
	function ( $class_name ) {
		$prefix = 'AI1WP\\';
		if ( 0 !== strpos( $class_name, $prefix ) ) {
			return;
		}
		$file = AI1WP_PATH . '/includes/' . str_replace( '\\', '/', substr( $class_name, strlen( $prefix ) ) ) . '.php';
		if ( is_readable( $file ) ) {
			require $file;
		}
	}
);

// Run pending updates on activation too.
register_activation_hook( __FILE__, array( 'AI1WP\Update\Update', 'maybe_update' ) );

// Boot Plugin.
AI1WP\Plugin::instance();
