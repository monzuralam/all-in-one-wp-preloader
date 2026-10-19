<?php
/**
 * Loader 3 template.
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
<div class="loader-wrap">
	<div class="ai1wp-style-3">
		<div class="ai1wp-style-3-close"><?php esc_html_e( 'Preloader Close', 'all-in-one-wp-preloader' ); ?></div>
	</div>
	<div class="layer layer-one"><span class="overlay"></span></div>
	<div class="layer layer-two"><span class="overlay"></span></div>
	<div class="layer layer-three"><span class="overlay"></span></div>
</div>
