<?php
/**
 * Plugin Name:       Art WooCommerce Order One Click
 * Plugin URI:        #
 * Description: Плагин под WooCommerce.  Включает режим каталога. Скрываются кнопки купить, появляется кнопка
 * Заказать. Для правильной работы требуются WooCommerce и Contact Form 7
 * Version:           1.5.2
 * Author:            Artem Abramovich
 * Author URI:        https://wpruse.ru/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:
 * Domain Path:
 *
 * WC requires at least: 3.3.0
 * WC tested up to: 3.3.4
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
define( 'AWOOC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'AWOOC_PLUGIN_URI', plugin_dir_url( __FILE__ ) );
define( 'AWOOC_PLUGIN_VER', '1.5.2' );
add_action( 'admin_init', 'awooc_check_activate_plugins' );
function awooc_check_activate_plugins() {
	if ( is_admin() && current_user_can( 'activate_plugins' ) ) {
		if ( ! is_plugin_active( 'contact-form-7/wp-contact-form-7.php' ) ) {
			add_action( 'admin_notices', function () {
				echo '<div id="message" class="error notice"><p>Для корректной работы плагина Art WooCommerce Order One Click нужен плагин <a href="//wordpress.org/plugins/contact-form-7/" target="_blank">Contact Form 7</a> </p></div>';
			} );
			deactivate_plugins( plugin_basename( __FILE__ ) );
			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
			}
		} elseif ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			add_action( 'admin_notices', function () {
				echo '<div id="message" class="error notice"><p>Для работы плагина Art WooCommerce Order One Click нужен плагин <a href="//wordpress.org/plugins/woocommerce/" target="_blank">WooCommerce</a> </p></div>';
			} );
			deactivate_plugins( plugin_basename( __FILE__ ) );
			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
			}
		}
	}
}

require_once 'include/helpers.php';
require_once 'include/functions.php';
require_once 'include/settings.php';
require_once 'include/fields.php';

register_uninstall_hook( __FILE__, 'awooc_uninstall' );
function awooc_uninstall() {
	delete_option( 'woocommerce_awooc_mode_catalog' );
	delete_option( 'woocommerce_awooc_select_form' );
	delete_option( 'woocommerce_awooc_title_button' );
	delete_option( 'woocommerce_awooc_select_item' );
}
