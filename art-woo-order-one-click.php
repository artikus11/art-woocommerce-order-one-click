<?php
/**
 * Plugin Name: Art WooCommerce Order One Click
 * Plugin URI: wpruse.ru/my-plugins/order-one-click/
 * Text Domain: art-woocommerce-order-one-click
 * Domain Path: /languages
 * Description: Plugin for WooCommerce. It includes the catalog mode in the store (there are no prices and the Buy button) and can turn on the Buy/Order button in one click. WooCommerce and Contact Form 7 are required for proper operation.
 * Version: 3.0.1
 * Author: Artem Abramovich
 * Author URI: https://wpruse.ru/
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * WC requires at least: 3.3.0
 * WC tested up to: 5.0
 *
 * Requires PHP: 7.4
 * Requires WP:5.5
 *
 * Copyright Artem Abramovich
 *
 *     This file is part of Art WooCommerce Order One Click,
 *     a plugin for WordPress.
 *
 *     Art WooCommerce Order One Click is free software:
 *     You can redistribute it and/or modify it under the terms of the
 *     GNU General Public License as published by the Free Software
 *     Foundation, either version 3 of the License, or (at your option)
 *     any later version.
 *
 *     Art WooCommerce Order One Click is distributed in the hope that
 *     it will be useful, but WITHOUT ANY WARRANTY; without even the
 *     implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR
 *     PURPOSE. See the GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with WordPress. If not, see <http://www.gnu.org/licenses/>.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$plugin_data = get_file_data(
	__FILE__,
	[
		'ver'  => 'Version',
		'name' => 'Plugin Name',
	]
);

const AWOOC_PLUGIN_DIR = __DIR__;
define( 'AWOOC_PLUGIN_URI', plugin_dir_url( __FILE__ ) );
define( 'AWOOC_PLUGIN_FILE', plugin_basename( __FILE__ ) );

define( 'AWOOC_PLUGIN_VER', $plugin_data['ver'] );
define( 'AWOOC_PLUGIN_NAME', $plugin_data['name'] );

require AWOOC_PLUGIN_DIR . '/vendor/autoload.php';
require_once AWOOC_PLUGIN_DIR . '/includes/create-cf7-field.php';
require_once AWOOC_PLUGIN_DIR . '/includes/helpers.php';
require_once AWOOC_PLUGIN_DIR . '/includes/template-functions.php';

register_uninstall_hook( __FILE__, [ Art\AWOOC\Uninstall::class, 'uninstall' ] );
register_activation_hook( __FILE__, [ Art\AWOOC\Admin\Create_Form::class, 'install_form' ] );

if ( ! function_exists( 'awooc' ) ) {
	/**
	 * The main function responsible for returning the AWOOC object.
	 *
	 * Use this function like you would a global variable, except without needing to declare the global.
	 *
	 * Example: <?php awooc()->method_name(); ?>
	 *
	 * @return object AWOOC class object.
	 * @since 1.0.0
	 */
	function awooc(): object {

		return \Art\AWOOC\Main::instance();
	}
}

awooc();
