<?php
/**
 * Main plugin class.
 *
 * @package AI1WP
 */

namespace AI1WP;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Main Plugin Class
 */
final class Plugin {

	/**
	 * Single instance.
	 *
	 * @var Plugin
	 */
	private static $instance = null;

	/**
	 * Get Instance
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Register Hooks
	 */
	private function __construct() {
		add_action( 'init', array( 'AI1WP\Options', 'register' ) );

		Update\Update::init();
		new Frontend();

		if ( is_admin() ) {
			new Admin\Settings();
		}
	}
}
