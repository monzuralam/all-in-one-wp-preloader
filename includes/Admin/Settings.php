<?php
/**
 * Settings page.
 *
 * @package AI1WP
 */

namespace AI1WP\Admin;

use AI1WP\Options;

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Settings Page
 * React app ( src/ ), built with @wordpress/scripts into build/.
 */
class Settings {

	/**
	 * Page Slug
	 */
	const PAGE = 'ai1wp-settings';

	/**
	 * Register Hooks
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'menu' ) );
	}

	/**
	 * Admin Menu
	 */
	public function menu() {
		$hook = add_menu_page(
			__( 'All-in-One WP Preloader', 'all-in-one-wp-preloader' ),
			__( 'All-in-One WP Preloader', 'all-in-one-wp-preloader' ),
			'manage_options',
			self::PAGE,
			array( $this, 'render' )
		);
		add_action( 'admin_print_scripts-' . $hook, array( $this, 'enqueue' ) );
	}

	/**
	 * Admin Scripts
	 */
	public function enqueue() {
		$asset_file = AI1WP_PATH . '/build/index.asset.php';
		if ( ! file_exists( $asset_file ) ) {
			return;
		}
		$asset = include $asset_file;

		wp_enqueue_media();
		wp_enqueue_script( 'ai1wp-admin', AI1WP_URL . '/build/index.js', $asset['dependencies'], $asset['version'], true );
		wp_enqueue_style( 'ai1wp-admin', AI1WP_URL . '/build/index.css', array( 'wp-components' ), $asset['version'] );
		wp_style_add_data( 'ai1wp-admin', 'rtl', 'replace' );
		wp_set_script_translations( 'ai1wp-admin', 'all-in-one-wp-preloader', AI1WP_PATH . '/languages' );

		wp_add_inline_script( 'ai1wp-admin', 'window.ai1wpAdmin = ' . wp_json_encode( $this->app_data() ) . ';', 'before' );
	}

	/**
	 * Settings App Data
	 */
	private function app_data() {
		$layouts = array();
		foreach ( Options::layouts() as $value => $label ) {
			$layouts[] = array(
				'value' => (string) $value,
				'label' => $label,
			);
		}

		return array(
			'version'      => AI1WP_VERSION,
			'settings'     => Options::all(),
			'layouts'      => $layouts,
			'templates'    => $this->preview_templates(),
			'styleUrl'     => AI1WP_URL . '/assets/css/style.css?ver=' . AI1WP_VERSION,
			'defaultImage' => Options::default_image(),
			'siteTitle'    => get_bloginfo( 'name' ),
			'siteUrl'      => home_url( '/' ),
		);
	}

	/**
	 * Loader Markup for the Live Preview
	 * Same templates as the frontend.
	 */
	private function preview_templates() {
		$templates = array();
		$logo      = Options::default_image();
		foreach ( array_keys( Options::layouts() ) as $value ) {
			ob_start();
			include AI1WP_PATH . '/templates/loader-' . $value . '.php';
			$templates[ (string) $value ] = ob_get_clean();
		}
		return $templates;
	}

	/**
	 * Settings Page Content
	 */
	public function render() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap ai1wp-wrap">
			<h1 class="screen-reader-text"><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<?php if ( ! file_exists( AI1WP_PATH . '/build/index.asset.php' ) ) : ?>
				<div class="notice notice-error"><p><?php esc_html_e( 'Settings app is not built. Run "npm install && npm run build" in the plugin folder.', 'all-in-one-wp-preloader' ); ?></p></div>
			<?php endif; ?>
			<div id="ai1wp-settings-root"></div>
		</div>
		<?php
	}
}
