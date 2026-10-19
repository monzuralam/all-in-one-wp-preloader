<?php
/**
 * Frontend preloader.
 *
 * @package AI1WP
 */

namespace AI1WP;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Frontend Preloader
 */
class Frontend {

	/**
	 * Register Hooks
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
		add_action( 'wp_body_open', array( $this, 'render' ) );
	}

	/**
	 * Enqueue Scripts & Style
	 */
	public function enqueue() {
		if ( ! Options::get( 'enable' ) ) {
			return;
		}

		wp_enqueue_style( 'ai1wp', AI1WP_URL . '/assets/css/style.css', array(), AI1WP_VERSION, 'all' );
		wp_enqueue_script( 'ai1wp', AI1WP_URL . '/assets/js/main.js', array( 'jquery' ), AI1WP_VERSION, true );

		$css = $this->dynamic_style();
		if ( $css ) {
			wp_add_inline_style( 'ai1wp', $css );
		}
	}

	/**
	 * Preloader Dynamic Style
	 */
	private function dynamic_style() {
		$layout   = Options::get( 'layout' );
		$bg_color = Options::get( 'bg_color' );
		$rules    = '';

		if ( $bg_color ) {
			// Custom property for loaders that color inner panels ( Loader 6 ).
			$rules .= '--ai1wp-bg: ' . $bg_color . ';';
			if ( '6' !== $layout ) {
				$rules .= 'background-color: ' . $bg_color . ';';
			}
		}
		// Loader 2 & 3 show the image as background, others use an <img> tag.
		if ( in_array( $layout, array( '2', '3' ), true ) ) {
			$rules .= 'background-image: url("' . esc_url( $this->logo() ) . '");';
		}

		return $rules ? '.ai1wp-style-' . $layout . '{' . $rules . '}' : '';
	}

	/**
	 * Preloader Content
	 */
	public function render() {
		if ( ! Options::get( 'enable' ) ) {
			return;
		}

		$logo = $this->logo();
		include AI1WP_PATH . '/templates/loader-' . Options::get( 'layout' ) . '.php';
	}

	/**
	 * Preloader Image
	 * If no image select then load default image.
	 */
	private function logo() {
		$image = Options::get( 'image' );
		return $image ? $image : Options::default_image();
	}
}
