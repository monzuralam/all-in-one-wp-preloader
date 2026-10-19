<?php
/**
 * Update runner.
 *
 * @package AI1WP
 */

namespace AI1WP\Update;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Update Runner
 * Runs pending update files in version order. Each file returns a callable.
 * To add an update: create Update-x.y.z.php and register it in $updates.
 */
class Update {

	/**
	 * Installed Version Option
	 */
	const VERSION_OPTION = 'ai1wp_version';

	/**
	 * Update Files
	 * version => file
	 *
	 * @var array
	 */
	private static $updates = array(
		'1.0.1' => 'Update-1.0.1.php',
	);

	/**
	 * Register Hooks
	 * Run on init (not only admin) so the frontend never reads unmigrated settings after an auto-update.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'maybe_update' ), 5 );
	}

	/**
	 * Run Pending Updates
	 */
	public static function maybe_update() {
		$installed = get_option( self::VERSION_OPTION );

		if ( ! $installed ) {
			// Fresh install, nothing to migrate.
			if ( ! self::is_legacy_install() ) {
				update_option( self::VERSION_OPTION, AI1WP_VERSION );
				return;
			}
			// 1.0.0 never stored its version.
			$installed = '1.0.0';
		}

		if ( version_compare( $installed, AI1WP_VERSION, '>=' ) ) {
			return;
		}

		foreach ( self::$updates as $version => $file ) {
			if ( version_compare( $installed, $version, '<' ) ) {
				$callback = include __DIR__ . '/' . $file;
				if ( is_callable( $callback ) ) {
					call_user_func( $callback );
				}
				// Save progress after each step.
				update_option( self::VERSION_OPTION, $version );
			}
		}

		update_option( self::VERSION_OPTION, AI1WP_VERSION );
	}

	/**
	 * Check 1.0.0 (Carbon Fields) Settings
	 */
	private static function is_legacy_install() {
		foreach ( array( '_ai1wp_enable', '_ai1wp_img', '_ai1wp_bg_color', '_ai1wp_layout_style' ) as $option ) {
			if ( false !== get_option( $option ) ) {
				return true;
			}
		}
		return false;
	}
}
