<?php
/**
 * Uninstall
 * Remove plugin options, only when "Delete data on uninstall" is enabled.
 *
 * @package AI1WP
 */

// If uninstall not called from WordPress, abort.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
 * Delete plugin data for the current site, if the site enabled "Delete data on uninstall".
 * Plugin classes are not loaded during uninstall, so the option is read directly.
 */
function ai1wp_uninstall_site() {
	$settings = get_option( 'ai1wp_settings' );
	if ( ! is_array( $settings ) || empty( $settings['delete_data'] ) ) {
		return;
	}

	$options = array(
		'ai1wp_settings',
		'ai1wp_version',
		// 1.0.0 (Carbon Fields) options.
		'_ai1wp_enable',
		'_ai1wp_img',
		'_ai1wp_bg_color',
		'_ai1wp_layout_style',
	);

	foreach ( $options as $option ) {
		delete_option( $option );
	}
}

// Each site has its own settings on multisite.
if ( is_multisite() ) {
	$ai1wp_site_ids = get_sites(
		array(
			'fields' => 'ids',
			'number' => 0,
		)
	);
	foreach ( $ai1wp_site_ids as $ai1wp_site_id ) {
		switch_to_blog( $ai1wp_site_id );
		ai1wp_uninstall_site();
		restore_current_blog();
	}
} else {
	ai1wp_uninstall_site();
}
