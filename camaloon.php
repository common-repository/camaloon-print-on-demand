<?php
/**
 * Camaloon
 *
 * @package Camaloon
 * Plugin Name: Camaloon Print on Demand
 * Plugin URI: http://camaloon.com/wordpress-plugin/
 * Description: Grow your brand with the Camaloon’s print-on-demand dropshipping plugin
 * Author: Camaloon
 * Version: 1.0.0
 * Text Domain: camaloon
 * Domain Path: /languages
 * Author URI: http://camaloon.com/
 */

/**
 * Returns the plugin text domain
 *
 * @return void assets path
 */
function camaloon_load_textdomain() {
	load_plugin_textdomain( 'camaloon', false, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'init', 'camaloon_load_textdomain' );
require_once plugin_dir_path( __FILE__ ) . 'includes/camaloon-functions.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-camaloon-client.php';

/**
 * Returns the path of the assets's plugin
 *
 * @return string assets path
 */
function camaloon_get_asset_url() {
	return trailingslashit( plugin_dir_url( __FILE__ ) ) . 'assets/';
}
