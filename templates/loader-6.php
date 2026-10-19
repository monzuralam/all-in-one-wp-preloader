<?php
/**
 * Loader 6 template.
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
<div class="preloader ai1wp-style-6">
	<div class="ai1wp-style-6-panel ai1wp-style-6-panel-left"></div>
	<div class="ai1wp-style-6-panel ai1wp-style-6-panel-right"></div>
	<div class="ai1wp-style-6-logo">
		<img src="<?php echo esc_url( $logo ); ?>" alt="Image">
	</div>
</div>
