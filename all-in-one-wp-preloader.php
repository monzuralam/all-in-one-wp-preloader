<?php
/**
 * Plugin Name:       All-in-One WP Preloader
 * Plugin URI:        https://profiles.wordpress.org/monzuralam
 * Description:       All-in-One Preloader helps you to create awesome preloader that easy to customize, works on all major browsers and with any wp theme.
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
define( 'AI1WP_URL', plugins_url( '' , __FILE__ ));

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


/**
 * Enqueue Scripts
 */
if( ! function_exists('ai1wp_scripts') ){
    function ai1wp_scripts(){
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'ai1wp', AI1WP_URL . '/assets/js/main.js', array('jquery'), time(), true );
    }
    add_action('wp_enqueue_scripts','ai1wp_scripts');
}

/**
 * Enqueue Style
 */
if( ! function_exists('ai1wp_style') ){
    function ai1wp_style(){
        wp_enqueue_style('ai1wp', AI1WP_URL . '/assets/css/style.css', null, time(), 'all');
    }
    add_action('wp_enqueue_scripts','ai1wp_style');
}

/*--------------------------------------------------------------
// Carbon fields Inclusion
--------------------------------------------------------------*/
use Carbon_Fields\Container;
use Carbon_Fields\Field;

require_once( AI1WP_PATH . '/admin/carbon-fields/vendor/autoload.php' );

if( ! function_exists('ai1wp_preloader_boot') ){
    function ai1wp_preloader_boot(){
        \Carbon_Fields\Carbon_Fields::boot();
    }
    add_action('plugins_loaded','ai1wp_preloader_boot');
}
