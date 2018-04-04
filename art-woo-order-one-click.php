<?php
/**
 * Plugin Name:       Art WooCommerce Order One Click
 * Plugin URI:        #
 * Description:
 * Version:           1.1
 * Author:            Artem Abramovich
 * Author URI:
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woo-seo-addon
 * Domain Path:       /languages
 *
 * WC requires at least: 3.3.0
 * WC tested up to: 3.3.4
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
define( 'AWOOC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'AWOOC_PLUGIN_URI', plugin_dir_url( __FILE__ ) );
define( 'AWOOC_PLUGIN_VER', '1.0' );
require_once 'include/helpers.php';

if (main_is_plugin_active( 'woocommerce/woocommerce.php' ) || class_exists( 'WooCommerce' ) ) {
	require_once 'include/functions.php';
} elseif ( is_admin() ) {
	add_action( 'admin_notices', function(){
		echo '<div id="message" class="error notice"><p>Для работы плагина Art WooCommerce Order One Click нужен плагин <a href="//wordpress.org/plugins/woocommerce/" target="_blank">WooCommerce</a> </p></div>';
	} );
}