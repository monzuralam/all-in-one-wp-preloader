<?php
/**
 * Update 1.0.1
 * Carbon Fields removed. Move separate Carbon Fields options into the single "ai1wp_settings" option.
 * Safe to run more than once.
 *
 * @package AI1WP
 */

use AI1WP\Options;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

return function () {
	// New key => Carbon Fields option.
	$legacy = array(
		'enable'   => '_ai1wp_enable',
		'image'    => '_ai1wp_img',
		'bg_color' => '_ai1wp_bg_color',
		'layout'   => '_ai1wp_layout_style',
	);

	if ( false === get_option( Options::OPTION ) ) {
		$values = array();
		foreach ( $legacy as $key => $option ) {
			$values[ $key ] = get_option( $option, '' );
		}
		// Carbon Fields checkbox stores "yes" or "".
		$values['enable'] = ( 'yes' === $values['enable'] );

		update_option( Options::OPTION, Options::sanitize( $values ) );
	}

	foreach ( $legacy as $option ) {
		delete_option( $option );
	}
};
