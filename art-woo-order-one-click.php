<?php
/**
 * Plugin Name:       Art WooCommerce Order One Click
 * Plugin URI:        wpruse.ru/my-plugins/order-one-click/
 * Description: Плагин под WooCommerce.  Включает режим каталога. Скрываются кнопки купить, появляется кнопка Заказать. Для правильной работы требуются WooCommerce и Contact Form 7
 * Version:           1.6.8
 * Author:            Artem Abramovich
 * Author URI:        https://wpruse.ru/
 * License:           GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt Text Domain: Domain Path:
 *
 * WC requires at least: 3.3.0
 * WC tested up to: 3.4
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
if ( ! function_exists( 'get_plugin_data' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}
define( 'AWOOC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'AWOOC_PLUGIN_URI', plugin_dir_url( __FILE__ ) );

$awooc_data = get_file_data( __FILE__, array(
	'awooc_ver'  => 'Version',
	'awooc_name' => 'Plugin Name',
) );

define( 'AWOOC_PLUGIN_VER', $awooc_data['awooc_ver'] );
define( 'AWOOC_PLUGIN_NAME', $awooc_data['awooc_name'] );

add_action( 'plugins_loaded', 'awooc_check_php_and_wp_version' );
add_action( 'admin_notices', 'awooc_show_notices' );
function awooc_check_php_and_wp_version() {
	global $wp_version;
	$php       = 5.6;
	$wp        = 4.8;
	$php_check = version_compare( PHP_VERSION, $php, '<' );
	$wp_check  = version_compare( $wp_version, $wp, '<' );
	
	if ( $php_check ) {
		$flag = 'PHP';
	} elseif ( $wp_check ) {
		$flag = 'WordPress';
	}
	
	if ( $php_check || $wp_check ) {
		$version = 'PHP' == $flag ? $php : $wp;
		if ( ! function_exists( 'deactivate_plugins' ) ) {
			require_once ABSPATH . '/wp-admin/includes/plugin.php';
		}
		
		deactivate_plugins( plugin_basename( __FILE__ ) );
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
		
		$error_text = sprintf( 'Для корректной работы плагин требует версию <strong>%s %s</strong> или выше.', $flag, $version );
		set_transient( 'awooc_activation_error_message', $error_text, 60 );
		
	} elseif ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
		
		if ( ! function_exists( 'deactivate_plugins' ) ) {
			require_once ABSPATH . '/wp-admin/includes/plugin.php';
		}
		
		deactivate_plugins( plugin_basename( __FILE__ ) );
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
		
		$error_text = sprintf( 'Для работы плагина ' . AWOOC_PLUGIN_NAME .
		                       ' требуется плагин <strong><a href="//wordpress.org/plugins/woocommerce/" target="_blank">%s %s</a></strong> или выше.', 'WooCommerce', '3.0' );
		set_transient( 'awooc_activation_error_message', $error_text, 60 );
		
	} elseif ( ! is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) {
		
		if ( ! function_exists( 'deactivate_plugins' ) ) {
			require_once ABSPATH . '/wp-admin/includes/plugin.php';
		}
		
		deactivate_plugins( plugin_basename( __FILE__ ) );
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
		
		$error_text = sprintf( 'Для работы плагина ' . AWOOC_PLUGIN_NAME .
		                       ' требуется плагин <strong><a href="//wordpress.org/plugins/contact-form-7/" target="_blank">%s</a></strong> или выше.', 'Contact Form 7' );
		set_transient( 'awooc_activation_error_message', $error_text, 60 );
		
	} else {
		awooc_activate_plugin();
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'awooc_plugin_add_settings_link' );
		add_filter( 'plugin_row_meta', 'awooc_plugin_add_settings_link', 10, 4 );
	}
}

function awooc_show_notices() {
	$message = get_transient( 'awooc_activation_error_message' );
	if ( ! empty( $message ) ) {
		echo '<div class="notice notice-error">
            <p><strong>Плагин ' . AWOOC_PLUGIN_NAME . ' не активирован!</strong> ' . $message . '</p>
        </div>';
		delete_transient( 'awooc_activation_error_message' );
	}
}

function awooc_activate_plugin() {
	/**
	 * Подключение файла вспомогательных функций
	 */
	require_once 'include/helpers.php';
	/**
	 * Подключение файла основных функций
	 */
	require_once 'include/template-functions.php';
	/**
	 * Подключение файла создания настроек
	 */
	require_once 'include/settings.php';
	/**
	 * Подключение файла создания доплнительного поля для CF7
	 */
	require_once 'include/fields.php';
}

register_uninstall_hook( __FILE__, 'awooc_uninstall' );
/**
 * Удаление значений настроек при деактивации плагина
 */
function awooc_uninstall() {
	delete_option( 'woocommerce_awooc_mode_catalog' );
	delete_option( 'woocommerce_awooc_select_form' );
	delete_option( 'woocommerce_awooc_title_button' );
	delete_option( 'woocommerce_awooc_select_item' );
	delete_option( 'woocommerce_awooc_created_order' );
}

/**
 * Add Settings link in pligins list
 */

function awooc_plugin_add_settings_link( $links ) {
	$settings_link = '<a href="admin.php?page=wc-settings">Настройки</a>';
	array_push( $links, $settings_link );
	
	return $links;
}
