<?php
/**
 * Plugin settings.
 *
 * @package AI1WP
 */

namespace AI1WP;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Plugin Settings
 * All settings are stored in a single option.
 */
class Options {

	/**
	 * Option Name
	 */
	const OPTION = 'ai1wp_settings';

	/**
	 * Preloader Design Options
	 */
	public static function layouts() {
		return array(
			'1' => __( 'Loader 1', 'all-in-one-wp-preloader' ),
			'2' => __( 'Loader 2', 'all-in-one-wp-preloader' ),
			'3' => __( 'Loader 3', 'all-in-one-wp-preloader' ),
			'4' => __( 'Loader 4', 'all-in-one-wp-preloader' ),
			'5' => __( 'Loader 5', 'all-in-one-wp-preloader' ),
			'6' => __( 'Loader 6', 'all-in-one-wp-preloader' ),
			'7' => __( 'Loader 7', 'all-in-one-wp-preloader' ),
		);
	}

	/**
	 * Default Settings
	 */
	public static function defaults() {
		return array(
			'enable'      => false,
			'image'       => '',
			'bg_color'    => '',
			'layout'      => '1',
			'delete_data' => false,
		);
	}

	/**
	 * Register Setting
	 * Runs on init so it is available to the REST API ( /wp/v2/settings ) used by the settings app.
	 */
	public static function register() {
		register_setting(
			'ai1wp_settings',
			self::OPTION,
			array(
				'type'              => 'object',
				'sanitize_callback' => array( __CLASS__, 'sanitize' ),
				'default'           => self::defaults(),
				'show_in_rest'      => array(
					'schema' => array(
						'type'                 => 'object',
						'additionalProperties' => false,
						'properties'           => array(
							'enable'      => array( 'type' => 'boolean' ),
							'image'       => array( 'type' => 'string' ),
							'bg_color'    => array( 'type' => 'string' ),
							'layout'      => array(
								'type' => 'string',
								'enum' => array_map( 'strval', array_keys( self::layouts() ) ),
							),
							'delete_data' => array( 'type' => 'boolean' ),
						),
					),
				),
			)
		);
	}

	/**
	 * Default Preloader Image
	 */
	public static function default_image() {
		return AI1WP_URL . '/assets/images/ai1wp.png';
	}

	/**
	 * Get All Settings
	 * Sanitized on read too, so stored values are always safe to output.
	 */
	public static function all() {
		$settings = get_option( self::OPTION, array() );
		return self::sanitize( wp_parse_args( is_array( $settings ) ? $settings : array(), self::defaults() ) );
	}

	/**
	 * Get Single Setting
	 *
	 * @param string $key Setting key.
	 * @return mixed
	 */
	public static function get( $key ) {
		$settings = self::all();
		return isset( $settings[ $key ] ) ? $settings[ $key ] : null;
	}

	/**
	 * Sanitize Settings
	 *
	 * @param mixed $input Raw settings.
	 * @return array
	 */
	public static function sanitize( $input ) {
		$input   = is_array( $input ) ? $input : array();
		$layouts = self::layouts();
		$layout  = isset( $input['layout'] ) ? (string) $input['layout'] : '';
		$color   = isset( $input['bg_color'] ) ? sanitize_hex_color( $input['bg_color'] ) : '';

		return array(
			'enable'      => ! empty( $input['enable'] ),
			'image'       => isset( $input['image'] ) ? esc_url_raw( $input['image'] ) : '',
			'bg_color'    => $color ? $color : '',
			'layout'      => isset( $layouts[ $layout ] ) ? $layout : '1',
			'delete_data' => ! empty( $input['delete_data'] ),
		);
	}
}
