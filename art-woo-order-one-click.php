<?php // @codingStandardsIgnoreLine

/**
 * Plugin Name: Art WooCommerce Order One Click
 * Plugin URI: wpruse.ru/my-plugins/order-one-click/
 * Text Domain: art-woocommerce-order-one-click
 * Domain Path: /languages
 * Description: Плагин под WooCommerce.  Включает режим каталога. Скрываются кнопки купить, появляется кнопка Заказать. Для правильной работы требуются WooCommerce и Contact Form 7
 * Version: 1.9.0
 * Author: Artem Abramovich
 * Author URI: https://wpruse.ru/
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * WC requires at least: 3.3.0
 * WC tested up to: 3.5.2
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
	exit; // Exit if accessed directly
}

$awooc_data = get_file_data(
	__FILE__,
	array(
		'ver'         => 'Version',
		'name'        => 'Plugin Name',
		'text_domain' => 'Text Domain',
	)
);

define( 'AWOOC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'AWOOC_PLUGIN_URI', plugin_dir_url( __FILE__ ) );
define( 'AWOOC_PLUGIN_VER', $awooc_data['ver'] );
define( 'AWOOC_PLUGIN_NAME', $awooc_data['name'] );

register_uninstall_hook( __FILE__, array( 'ArtWoo_Order_One_Click', 'uninstall' ) );

/**
 * Class ArtWoo_Order_One_Click
 *
 * Main AWOOC class, initialized the plugin
 *
 * @class       ArtWoo_Order_One_Click
 * @version     1.8.0
 * @author      Artem Abramovich
 */
class ArtWoo_Order_One_Click {

	/**
	 * Instance of ArtWoo_Order_One_Click.
	 *
	 * @since  1.8.0
	 * @access private
	 * @var object $instance The instance of AWOOS_Custom_Sale.
	 */
	private static $instance;

	/**
	 * Plugin version.
	 *
	 * @since 1.8.0
	 * @var string $version Plugin version number.
	 */
	public $version;

	/**
	 * Plugin name.
	 *
	 * @since 1.8.0
	 * @var string $name Plugin name.
	 */
	public $name;

	/**
	 * @since 1.9.0
	 * @var array Required plugins.
	 */
	protected $required_plugins = array();


	/**
	 * Construct.
	 *
	 * @since 1.8.0
	 *
	 * @see   https://github.com/kagg-design/woof-by-category
	 *
	 */
	public function __construct() {

		$this->required_plugins = array(
			array(
				'plugin'  => 'woocommerce/woocommerce.php',
				'name'    => 'WooCommerce',
				'slug'    => 'woocommerce',
				'class'   => 'WooCommerce',
				'version' => '3.0',
				'active'  => false,
			),
			array(
				'plugin'  => 'contact-form-7/wp-contact-form-7.php',
				'name'    => 'Contact Form 7',
				'slug'    => 'contact-form-7',
				'class'   => 'WPCF7',
				'version' => '5.0',
				'active'  => false,
			),
		);

		$this->init();

		$this->load_textdomain();
	}


	/**
	 * Init.
	 *
	 * Initialize plugin parts.
	 *
	 *
	 * @since 1.8.0
	 */
	public function init() {

		if ( version_compare( PHP_VERSION, '5.6', 'lt' ) ) {

			return add_action( 'admin_notices', array( $this, 'php_version_notice' ) );
		}

		add_action( 'admin_init', array( $this, 'check_requirements' ) );

		foreach ( $this->required_plugins as $required_plugin ) {
			if ( ! class_exists( $required_plugin['class'] ) ) {
				return;
			}
		}

		/**
		 * Hiding field to CF7
		 */
		include 'includes/admin/added-cf7-field.php';

		/**
		 * Front end
		 */
		include 'includes/class-awooc-frontend.php';
		$this->front_end = new AWOOC_Front_End();

		/**
		 * Ajax
		 */
		include 'includes/class-awooc-ajax.php';
		$this->ajax = new AWOOC_Ajax();

		/**
		 * Создание заказов
		 */
		include 'includes/class-awooc-orders.php';
		$this->orders = new AWOOC_Orders();

		/**
		 * Template functions
		 */
		include 'includes/awooc-template-functions.php';

		// Load hooks
		$this->hooks();

		global $pagenow;
		if ( 'plugins.php' === $pagenow ) {
			// Plugins page
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'add_plugin_action_links' ), 10, 2 );
		}

	}


	/**
	 * Hooks.
	 *
	 * Initialize all class hooks.
	 *
	 * @since 1.8.0
	 */
	public function hooks() {

		add_filter( 'woocommerce_get_settings_pages', array( $this, 'add_awooc_admin_settings' ), 15 );

	}


	/**
	 * Textdomain.
	 *
	 * Load the textdomain based on WP language.
	 *
	 * @since 1.9.0
	 */
	public function load_textdomain() {

		$locale = apply_filters( 'plugin_locale', get_locale(), 'art-decoration-shortcode' );

		load_textdomain(
			'art-woocommerce-order-one-click',
			WP_LANG_DIR . '/art-decoration-shortcode/art-decoration-shortcode-' . $locale . '.mo'
		);
		load_plugin_textdomain(
			'art-woocommerce-order-one-click',
			false,
			basename( dirname( __FILE__ ) ) . '/languages'
		);

	}


	/**
	 * Instance.
	 *
	 * An global instance of the class. Used to retrieve the instance
	 * to use on other files/plugins/themes.
	 *
	 * @since 1.8.0
	 * @return object Instance of the class.
	 */
	public static function instance() {

		if ( is_null( self::$instance ) ) :
			self::$instance = new self();
		endif;

		return self::$instance;

	}


	/**
	 * Settings.
	 *
	 * Include the WooCommerce settings class.
	 *
	 * @param WC_Admin_Settings $settings
	 *
	 * @return array
	 *
	 * @since 1.8.0
	 * @since 1.8.5
	 */
	public function add_awooc_admin_settings( $settings ) {

		$settings[] = include 'includes/admin/class-awooc-admin-settings.php';

		return $settings;
	}


	/**
	 * Plugin action links.
	 *
	 * Add links to the plugins.php page below the plugin name
	 * and besides the 'activate', 'edit', 'delete' action links.
	 *
	 * @since 1.8.0
	 *
	 * @param    array  $links List of existing links.
	 * @param    string $file  Name of the current plugin being looped.
	 *
	 * @return    array            List of modified links.
	 */
	public function add_plugin_action_links( $links, $file ) {

		if ( plugin_basename( __FILE__ ) === $file ) {
			$links = array_merge(
				array(
					'<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=awooc_settings' ) ) . '">Настройки</a>',
				),
				$links
			);
		}

		return $links;

	}


	/**
	 * Display PHP 5.6 required notice.
	 *
	 * Display a notice when the required PHP version is not met.
	 *
	 * @since 1.8.0
	 */
	public function php_version_notice() {

		$message = sprintf(
			/* translators: 1: Name plugins, 2:PHP version */
			esc_html__(
				'%1$s requires PHP version 5.6 or higher. Your current PHP version is %2$s. Please upgrade PHP version.',
				'art-woocommerce-order-one-click'
			),
			esc_html( AWOOC_PLUGIN_NAME ),
			PHP_VERSION
		);

		$this->admin_notice( $message, 'notice notice-error is-dismissible' );

	}


	public function check_requirements() {

		if ( ! $this->requirements() ) {
			add_action( 'admin_notices', array( $this, 'show_plugin_not_found_notice' ) );
			if ( is_plugin_active( plugin_basename( AWOOC_PLUGIN_URI ) ) ) {
				deactivate_plugins( plugin_basename( AWOOC_PLUGIN_URI ) );
				// @codingStandardsIgnoreStart
				if ( isset( $_GET['activate'] ) ) {
					unset( $_GET['activate'] );
				}
				// @codingStandardsIgnoreEnd
				add_action( 'admin_notices', array( $this, 'show_deactivate_notice' ) );
			}
		}
	}


	private function requirements() {

		$all_active = true;
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
		foreach ( $this->required_plugins as $key => $required_plugin ) {
			if ( is_plugin_active( $required_plugin['plugin'] ) ) {
				$this->required_plugins[ $key ]['active'] = true;
			} else {
				$all_active = false;
			}
		}

		return $all_active;
	}


	public function show_plugin_not_found_notice() {

		$message = sprintf(
			/* translators: 1: Name author plugin */
			__( 'The %s requires installed and activated plugins: ', 'art-woocommerce-order-one-click' ),
			esc_attr( AWOOC_PLUGIN_NAME )
		);

		$message_parts = array();

		foreach ( $this->required_plugins as $key => $required_plugin ) {
			if ( ! $required_plugin['active'] ) {
				$href = '/wp-admin/plugin-install.php?tab=plugin-information&plugin=';

				$href .= $required_plugin['slug'] . '&TB_iframe=true&width=640&height=500';

				$message_parts[] = '<strong><em><a href="' . $href . '" class="thickbox">' . $required_plugin['name'] . ' version ' . $required_plugin['version'] . '</a> or higher</em></strong>';
			}
		}

		$count = count( $message_parts );
		foreach ( $message_parts as $key => $message_part ) {
			if ( 0 !== $key ) {
				if ( ( ( $count - 1 ) === $key ) ) {
					$message .= ' and ';
				} else {
					$message .= ', ';
				}
			}

			$message .= $message_part;
		}

		$message .= '.';

		$this->admin_notice( $message, 'notice notice-error is-dismissible' );
	}


	/**
	 * @param $message
	 * @param $class
	 */
	private function admin_notice( $message, $class ) {

		?>
		<div class="<?php echo esc_attr( $class ); ?>">
			<p>
				<span>
				<?php echo wp_kses_post( $message ); ?>
				</span>
			</p>
		</div>
		<?php

	}


	/**
	 * Show a notice to inform the user that the plugin has been deactivated.
	 *
	 * @since 1.9.0
	 */
	public function show_deactivate_notice() {

		$message = sprintf(
			/* translators: 1: Name author plugin */
			__( '%s plugin has been deactivated.', 'art-woocommerce-order-one-click' ),
			esc_attr( AWOOC_PLUGIN_NAME )
		);

		$this->admin_notice( $message, 'notice notice-info is-dismissible' );
	}


	/**
	 * Deleting settings when uninstalling the plugin
	 *
	 * @since 1.8.0
	 */
	public function uninstall() {

		delete_option( 'woocommerce_awooc_padding' );
		delete_option( 'woocommerce_awooc_margin' );
		delete_option( 'woocommerce_awooc_mode_catalog' );
		delete_option( 'woocommerce_awooc_select_form' );
		delete_option( 'woocommerce_awooc_title_button' );
		delete_option( 'woocommerce_awooc_select_item' );
		delete_option( 'woocommerce_awooc_created_order' );
	}
}

/**
 * The main function responsible for returning the ArtWoo_Order_One_Click object.
 *
 * Use this function like you would a global variable, except without needing to declare the global.
 *
 * Example: <?php awooc_order_one_click()->method_name(); ?>
 *
 * @since 1.0.0
 *
 * @return object ArtWoo_Order_One_Click class object.
 */
if ( ! function_exists( 'awooc_order_one_click' ) ) {

	function awooc_order_one_click() {

		return ArtWoo_Order_One_Click::instance();
	}
}

$GLOBALS['awooc'] = awooc_order_one_click();
