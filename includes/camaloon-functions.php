<?php
/**
 * Camaloon functions
 *
 * @package Camaloon
 */

/**
 * Add my new menu to the Admin Control Panel
 */
add_action( 'admin_menu', 'camaloon_add_admin_link' );
global $camaloon_url;
$camaloon_url = 'https://camaloon.com';

/**
 * Returns the selected tab
 *
 * @param string $tab tab selected for the user
 *
 * @return string tab
 */
function camaloon_pod_tabs( $tab ) {
	$sanitized_tab = sanitize_text_field( $tab );
	if ( 'connect' !== $sanitized_tab && 'status' !== $sanitized_tab && 'faq' !== $sanitized_tab && 'disconnect' !== $sanitized_tab ) {
		return 'connect';
	}

	return $sanitized_tab;
}

/**
 * Adds the plugin to the menu
 *
 * @return void tab
 */
function camaloon_add_admin_link() {
	add_menu_page(
		'Camaloon', /* Title of the page */
		'Camaloon', /* Text to show on the menu link */
		'manage_options', /* Capability requirement to see the link  */
		plugin_dir_path( __FILE__ ) . 'templates/camaloon-home.php',
		null, /* The 'slug' - file to display when clicking the link */
		plugins_url( '../assets/images/camaloon-icon.svg', __FILE__ ),
		50
	);
}

add_action( 'admin_enqueue_scripts', 'add_camaloon_script_to_admin_page' );

/**
 * Register styles and javascript
 *
 * @param string $hook register styles and javascript
 *
 * @return string tab
 */
function add_camaloon_script_to_admin_page( $hook ) {
	wp_register_style( 'camaloon-status', plugins_url( '../assets/css/status.css', __FILE__ ), false, '1.0.0' );
	wp_enqueue_style( 'camaloon-status' );

	wp_register_style( 'camaloon-header', plugins_url( '../assets/css/header.css', __FILE__ ), false, '1.0.0' );
	wp_enqueue_style( 'camaloon-header' );

	wp_register_style( 'camaloon-css', plugins_url( '../assets/lib/styles.css', __FILE__ ), false, '1.0.0' );
	wp_enqueue_style( 'camaloon-css' );

	wp_enqueue_script( 'camaloon-faq', plugins_url( '../assets/js/faq.js', __FILE__ ) );
	wp_enqueue_script( 'camaloon-js' );
	wp_register_script( 'camaloon-js', plugins_url( '../assets/lib/index.js', __FILE__), array( 'jquery-core' ), false, true );
}

/**
 * Checks if woocommerce is installed
 *
 * @return boolean
 */
function camaloon_is_woocommerce_ready() {
	return class_exists( 'WooCommerce' );
}

/**
 * Checks if Camaloon integration is installed
 *
 * @return boolean
 */
function camaloon_is_store_installed() {
	global $wpdb;

	$count = $wpdb->get_var( "SELECT COUNT(key_id) FROM {$wpdb->prefix}woocommerce_api_keys WHERE description LIKE 'Camaloon%';" );
	return $count > 0;
}

/**
 * Returns the Woocommerce api key set up for camaloon
 *
 * @return string key
 */
function get_camaloon_key() {
	global $wpdb;

	$key_id = $wpdb->get_var( "SELECT key_id FROM {$wpdb->prefix}woocommerce_api_keys WHERE description LIKE 'Camaloon%';" );
	return $key_id;
}

/**
 * Returns the Woocommerce secret key set up for camaloon
 *
 * @return string secret key
 */
function get_camaloon_consumer_secret() {
	global $wpdb;

	$consumer_secret = $wpdb->get_var( "SELECT consumer_secret FROM {$wpdb->prefix}woocommerce_api_keys WHERE description LIKE 'Camaloon%';" );
	return $consumer_secret;
}

/**
 * Deletes the Woocommerce keys for Camaloon integration
 *
 * @return void
 */
function remove_camaloon_api_key() {
	global $wpdb;
	$key = get_camaloon_key();
	$wpdb->delete( 'wp_woocommerce_api_keys', array( 'key_id' => $key ) );
}

/**
 * Checks if the webhooks are present
 *
 * @return boolean
 */
function camaloon_is_webhook_existed() {
	global $wpdb;

	$count = $wpdb->get_var( "SELECT COUNT(webhook_id) FROM {$wpdb->prefix}wc_webhooks WHERE delivery_url LIKE '%/print_on_demand/woo_commerce/webhooks/receive';" );

	if ( $count > 0 ) {
		return STATUS_OK;
	}

	return STATUS_FAIL;
}

/**
 * Return the ids for the webhooks ids
 *
 * @return array
 */
function camaloon_get_webhooks_ids() {
	global $wpdb;
	$records = $wpdb->get_results( "SELECT webhook_id FROM {$wpdb->prefix}wc_webhooks WHERE delivery_url LIKE '%/print_on_demand/woo_commerce/webhooks/receive';", ARRAY_A );

	return $records;
}

/**
 * Retrieve store status through the camaloon api
 *
 * @param object $client client
 *
 * @return object request response
 */
function camaloon_retrieve_store_status( $client ) {
	return $client->get( '/print_on_demand/api/stores/store_status/' . get_camaloon_consumer_secret() );
}

/**
 * Disconnect the store through the camaloon api
 *
 * @param object $client client
 *
 * @return object request response
 */
function disconnect_camaloon_store( $client ) {
	return $client->put( '/print_on_demand/api/stores/disconnect_store/' . get_camaloon_consumer_secret() );
}

/**
 * This function removes all the local stuff stored to make the connection if we detect that the user deactivates the store at camaloon.com
 *
 * @param object $client client
 *
 * @return void
 */
function camaloon_mark_as_inactive_if_needed( $client ) {
	if ( camaloon_is_store_installed() && camaloon_is_woocommerce_ready() ) {
		if ( 'true' === camaloon_retrieve_store_status( $client )['inactive'] ) {
			camaloon_disconnect_local_values();
		}
	}
}

/**
 * Removes all webhooks from camaloon
 *
 * @return void
 */
function camaloon_remove_webhooks_ids() {
	global $wpdb;
	$post_ids = camaloon_get_webhooks_ids();
	$id1      = array_values( $post_ids[0] )[0];
	$id2      = array_values( $post_ids[1] )[0];
	$wpdb->delete( 'wp_wc_webhooks', array( 'webhook_id' => $id1 ) );
	$wpdb->delete( 'wp_wc_webhooks', array( 'webhook_id' => $id2 ) );
}

/**
 * Removes all the local stuff stored to make the connection. Woocommerce API keys and webhooks
 *
 * @return void
 */
function camaloon_disconnect_local_values() {
	remove_camaloon_api_key();
	camaloon_remove_webhooks_ids();
}

/**
 * Removes all the local stuff stored to make the connection. Woocommerce API keys and webhooks
 *
 * @param object $client
 *
 * @return void
 */
function disconnect_camaloon( $client ) {
	disconnect_camaloon_store( $client );
	camaloon_disconnect_local_values();
}

/**
 * Render php templates
 *
 * @param string $name
 *
 * @return string content
 */
function camaloon_render_php( $name ) {
	$path = plugin_dir_path( __FILE__ ) . 'templates/' . $name . '.php';
	ob_start();
	include( $path );
	$var = ob_get_contents();
	ob_end_clean();
	return $var;
}

const STATUS_OK            = 1;
const STATUS_WARNING       = 0;
const STATUS_FAIL          = -1;
const STATUS_NOT_CONNECTED = 2;
const WEBHOOK_NAME         = 'Camaloon Integration';

/**
 * Check if permalinks are enabled
 *
 * @return boolean
 */
function camaloon_check_permalinks() {
	$permalinks = get_option( 'permalink_structure', false );

	if ( $permalinks && strlen( $permalinks ) > 0 ) {
		return STATUS_OK;
	}

	return STATUS_FAIL;
}

/**
 * Check if WordPress is greater than 5.8.1
 *
 * @return boolean
 */
function camaloon_check_wp_version() {
	$current = get_bloginfo( 'version' );

	if ( version_compare( $current, '5.8.1', '>=' ) ) {
		return STATUS_OK;
	}
	return STATUS_FAIL;
}

/**
 * Check if Woocommerce is greater than 5.7.1
 *
 * @return boolean
 */
function camaloon_check_woocommerce_version() {
	if( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '5.7.1', '>=' ) ) {
		return STATUS_OK;
	}
	return STATUS_FAIL;
}

/**
 * Check if directory is writable
 * @return boolean
 */
function camaloon_check_uploads_write() {
	$upload_dir = wp_upload_dir();
	if ( is_writable( $upload_dir['basedir'] ) ) {
		return STATUS_OK;
	}

	return STATUS_FAIL;
}

/**
 * Check if PHP memory limit is enough
 * @return boolean
 */
function camaloon_check_php_memory_limit() {
	$memory_limit = ini_get( 'memory_limit' );
	if ( preg_match( '/^(\d+)(.)$/', $memory_limit, $matches ) ) {
		if ( 'M' === $matches[2] ) {
			$memory_limit = $matches[1] * 1024 * 1024; /* nnnM -> nnn MB */
		} elseif ( 'K' === $matches[2] ) {
			$memory_limit = $matches[1] * 1024; /* nnnK -> nnn KB */
		}
	}

	$ok = ( $memory_limit >= 128 * 1024 * 1024 ); // at least 128M?

	if ( $ok ) {
		return STATUS_OK;
	}

	return STATUS_FAIL;
}

/**
 * Check if PHP time limit is enough
 * @return boolean
 */
function camaloon_check_php_time_limit() {
	$time_limit = ini_get( 'max_execution_time' );

	if ( ! $time_limit || $time_limit >= 30 ) {
		return STATUS_OK;
	}

	return STATUS_FAIL;
}

/**
 * List of checklist items required for the plugin
 * @return array
 */
function camaloon_get_checklist_items() {
	return array(
		array(
			'name'        => __( 'WordPress Permalinks', 'camaloon' ),
			'description' => __( 'WooCommerce API will not work unless your permalinks in Settings > Permalinks are set up correctly. Make sure you that they are NOT set to "plain".', 'camaloon' ),
			'method'      => 'camaloon_check_permalinks',
		),
		array(
			'name'        => __( 'WordPress version', 'camaloon' ),
			'description' => __( 'WordPress should always be updated to the latest version. Updates can be installed from your WordPress admin dashboard.', 'camaloon' ),
			'method'      => 'camaloon_check_wp_version',
		),
		array(
			'name'        => __( 'WooCommerce version', 'camaloon' ),
			'description' => __( 'Woocommerce should always be version 5.7.1 or later.', 'camaloon' ),
			'method'      => 'camaloon_check_woocommerce_version',
		),
		array(
			'name'        => __( 'WooCommerce Webhooks', 'camaloon' ),
			'description' => __( 'Camaloon requires WooCommerce webhooks to be set up to quickly capture your incoming orders, products updates etc.', 'camaloon' ),
			'method'      => 'camaloon_is_webhook_existed',
		),
		array(
			'name'        => __( 'Write permissions', 'camaloon' ),
			'description' => __( 'Make the uploads directory writable. This is required for mockup generator product push to work correctly. Contact your hosting provider if you need help with this.', 'camaloon' ),
			'method'      => 'camaloon_check_uploads_write',
		),
		array(
			'name'        => __( 'PHP memory limit', 'camaloon' ),
			'description' => __( 'Set PHP allocated memory limit to at least 128mb. Contact your hosting provider if you need help with this.', 'camaloon' ),
			'method'      => 'camaloon_check_php_memory_limit',
		),
		array(
			'name'        => __( 'PHP script time limit', 'camaloon' ),
			'description' => __( 'Set PHP script execution time limit to at least 30 seconds. This is required to successfully push products with many variants. Contact your hosting provider if you need help with this.', 'camaloon' ),
			'method'      => 'camaloon_check_php_time_limit',
		),
	);
}

/**
 * Checks if one checklist failed
 * @return boolean
 */
function camaloon_check_list_failed_without_webhooks() {
	$items = camaloon_get_checklist_items();
	foreach ( $items as $item ) {
		if ( 'WooCommerce Webhooks' === $item['name'] ) {
			continue;
		}
		$item['status'] = $item['method']();
		if ( STATUS_OK !== $item['status'] ) {
			return true;
		}
	}
	return false;
}

/**
 * Returns the language code by the selected language
 * @return string
 */
function camaloon_get_locale_code_by_selected_language() {
	$locale = get_locale();

	switch ( $locale ) {
		case 'es_ES':
			return 'es';
		case 'en_GB':
			return 'en';
		case 'da_DK':
			return 'da';
		case 'de_DE':
			return 'de';
		case 'fr_FR':
			return 'fr';
		case 'it_IT':
			return 'it';
		case 'nl_NL':
			return 'nl';
		case 'nn_NO':
			return 'no';
		case 'pt_PT':
			return 'pt';
		case 'sv_SE':
			return 'sv';
		default:
			return 'en';
	}
}

/**
 * To console log errors
 *
 * @param string $output
 * @param bool $with_script_tags
 *
 * @return void
 */
function camaloon_console_log( $output, $with_script_tags = true ) {
	$js_code = 'console.log(' . wp_json_encode( $output, JSON_HEX_TAG ) . ');';
	if ( $with_script_tags ) {
		echo esc_html( '<script>' . $js_code . '</script>' );
	}
	echo esc_js( $js_code );
}
