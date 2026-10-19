<?php
/**
 * Loader 1 template.
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
<div class="preloader ai1wp-style-1">
	<figure>
		<img src="<?php echo esc_url( $logo ); ?>" alt="Image">
	</figure>
</div>
