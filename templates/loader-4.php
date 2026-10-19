<?php
/**
 * Loader 4 template.
 *
 * @package AI1WP
 *
 * @var string $logo Preloader image URL.
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class="preloader ai1wp-style-4">
	<img src="<?php echo esc_url( $logo ); ?>" alt="Image">
	<div class="ai1wp-style-4-dots">
		<span></span>
		<span></span>
		<span></span>
	</div>
</div>
