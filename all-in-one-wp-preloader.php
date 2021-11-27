<?php
/**
 * Plugin Name:       All-in-one WP Preloader
 * Plugin URI:        https://profiles.wordpress.org/monzuralam
 * Description:       Best wp preloader for your website.
 * Version:           1.0.0
 * Author:            Monzur Alam
 * Author URI:        https://profiles.wordpress.org/monzuralam
 * Text Domain:       all-in-one-wp-preloader
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * GitHub Plugin URI: https://github.com/all-in-one-wp-preloader
 */

// If this file is called directly, abort.
if( ! defined('ABSPATH') ){
    exit; // Exit if accessed directly.
}

// Plugin Basename 
define( 'AI1WP_PLUGIN_BASENAME', basename( dirname( __FILE__ ) ));

// Plugin Path
define( 'AI1WP_PATH', dirname(__FILE__) );

// Plugin URL
define( 'AI1WP_URL', plugins_url( '' ));

/**
 * Load Textdomain
 * Load Plugin Localization files.
 */
if( ! function_exists('ai1wp_i10n') ){
    function ai1wp_i10n(){
        load_plugin_textdomain( 'all-in-one-wp-preloader', false, AI1WP_PLUGIN_BASENAME . '/languages');
    }
    add_action('plugins_loaded', 'ai1wp_i10n');
}