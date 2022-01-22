<?php

namespace Art\AWOOC;

/**
 * Class AWOOC
 * Main AWOOC class, initialized the plugin
 *
 * @class       AWOOC
 * @version     1.8.0
 * @author      Artem Abramovich
 */
class Main {

	/**
	 * Instance of ArtWoo_Order_One_Click.
	 *
	 * @since  1.8.0
	 * @access private
	 * @var object $instance The instance of ArtWoo_Order_One_Click.
	 */
	private static $instance;

	/**
	 * @since 2.0.0
	 * @var object Front $front_end
	 */
	public $front;

	/**
	 * @since 2.3.6
	 * @var object Enqueue $enqueue
	 */
	public $enqueue;

	/**
	 * @since 2.0.0
	 * @var object Ajax $ajax
	 */
	public $ajax;

	/**
	 * Added Orders.
	 *
	 * @since 2.0.0
	 * @var object Orders $orders
	 */
	public $orders;

	/**
	 * @since 3.0.0
	 * @var object Templater $templater
	 */
	public $templater;

	/**
	 * Required plugins
	 *
	 * @since 2.0.0
	 * @var array Required plugins.
	 */
	protected $required_plugins = [];


	/**
	 * Construct.
	 *
	 * @since 1.8.0
	 * @see   https://github.com/kagg-design/woof-by-category
	 */
	public function __construct() {

		$this->required_plugins = [
			[
				'plugin'  => 'woocommerce/woocommerce.php',
				'name'    => 'WooCommerce',
				'slug'    => 'woocommerce',
				'class'   => 'WooCommerce',
				'version' => '3.0',
				'active'  => false,
			],
			[
				'plugin'  => 'contact-form-7/wp-contact-form-7.php',
				'name'    => 'Contact Form 7',
				'slug'    => 'contact-form-7',
				'class'   => 'WPCF7',
				'version' => '5.0',
				'active'  => false,
			],
		];

		$this->includes();

		$this->init();

		$this->load_textdomain();

	}


	/**
	 * Load plugin parts.
	 *
	 * @since 2.0.0
	 */
	private function includes() {


		require AWOOC_PLUGIN_DIR . '/includes/helpers.php';

		require AWOOC_PLUGIN_DIR . '/includes/create-cf7-field.php';

		require AWOOC_PLUGIN_DIR . '/includes/template-functions.php';

		require AWOOC_PLUGIN_DIR . '/classes/class-setup-form.php';

		require AWOOC_PLUGIN_DIR . '/classes/class-product-meta.php';

		require AWOOC_PLUGIN_DIR . '/classes/class-enqueue.php';
		$this->enqueue = new Enqueue();

		require AWOOC_PLUGIN_DIR . '/classes/class-templater.php';
		$this->templater = new Templater();

		require AWOOC_PLUGIN_DIR . '/classes/class-front.php';
		$this->front = new Front();

		require AWOOC_PLUGIN_DIR . '/classes/class-ajax.php';
		$this->ajax = new Ajax();

		require AWOOC_PLUGIN_DIR . '/classes/class-orders.php';
		$this->orders = new Orders();

	}


	/**
	 * Init.
	 * Initialize plugin parts.
	 *
	 * @since 1.8.0
	 */
	public function init() {

		add_action( 'admin_init', [ $this, 'check_requirements' ] );
		add_action( 'admin_init', [ $this, 'check_php_version' ] );

		add_action( 'wp_ajax_awooc_rated', [ $this, 'add_rated' ] );

		add_filter( 'plugin_action_links_' . AWOOC_PLUGIN_FILE, [ $this, 'add_plugin_action_links' ], 10, 1 );

		add_filter( 'woocommerce_get_settings_pages', [ $this, 'add_awooc_admin_settings' ], 15 );

		foreach ( $this->required_plugins as $required_plugin ) {
			if ( ! class_exists( $required_plugin['class'] ) ) {
				return;
			}
		}

	}


	/**
	 * Textdomain.
	 * Load the textdomain based on WP language.
	 *
	 * @since 2.0.0
	 */
	public function load_textdomain() {

		load_plugin_textdomain(
			'art-woocommerce-order-one-click',
			false,
			dirname( AWOOC_PLUGIN_FILE ) . '/languages/'
		);

	}


	/**
	 * Instance.
	 * A global instance of the class. Used to retrieve the instance
	 * to use on other files/plugins/themes.
	 *
	 * @return object Instance of the class.
	 * @since 1.8.0
	 */
	public static function instance() {

		if ( is_null( self::$instance ) ) :
			self::$instance = new self();
		endif;

		return self::$instance;

	}


	/**
	 * Settings.
	 * Include the WooCommerce settings class.
	 *
	 * @param  array $settings приходит массив настроек.
	 *
	 * @return array
	 * @since 1.8.0
	 * @since 1.8.5
	 */
	public function add_awooc_admin_settings( $settings ) {

		$settings[] = include __DIR__ . '/class-settings.php';

		return $settings;
	}


	/**
	 * Plugin action links.
	 * Add links to the plugins.php page below the plugin name
	 * and besides the 'activate', 'edit', 'delete' action links.
	 *
	 * @param  array $links List of existing links.
	 *
	 * @return array List of modified links.
	 * @since 1.8.0
	 */
	public function add_plugin_action_links( $links ) {

		$plugin_links = [
			'settings' => sprintf(
				'<a href="%s">%s</a>',
				esc_url( admin_url( 'admin.php?page=wc-settings&tab=awooc_settings' ) ),
				esc_html__( 'Settings', 'art-woocommerce-order-one-click' )
			),
		];

		return array_merge( $plugin_links, $links );

	}


	/**
	 * Display PHP 5.6 required notice.
	 * Display a notice when the required PHP version is not met.
	 *
	 * @since 1.8.0
	 */
	public function php_version_notice() {

		$message = sprintf(
		/* translators: 1: Name plugins, 2:PHP version */
			esc_html__(
				'%1$s requires PHP version 7.3 or higher. Your current PHP version is %2$s. Please upgrade PHP version to run this plugin.',
				'art-woocommerce-order-one-click'
			),
			esc_html( AWOOC_PLUGIN_NAME ),
			PHP_VERSION
		);

		$this->admin_notice( $message, 'notice notice-error is-dismissible' );

	}


	/**
	 * Show admin notice.
	 *
	 * @param  string $message Message to show.
	 * @param  string $class   Message class: notice notice-success notice-error notice-warning notice-info is-dismissible.
	 *
	 * @since 2.0.0
	 */
	private function admin_notice( $message, $class ) {

		printf(
			'<div class="%1$s"><p><span>%2$s</span></p></div>',
			esc_attr( $class ),
			wp_kses_post( $message )
		);

	}


	/**
	 * Check plugin PHP version. If not met, show message and deactivate plugin.
	 *
	 * @since 2.0.0
	 */
	public function check_php_version() {

		if ( PHP_VERSION_ID < 50600 ) {

			deactivate_plugins( plugin_basename( AWOOC_PLUGIN_FILE ) );

			add_action( 'admin_notices', [ $this, 'php_version_notice' ] );
			add_action( 'admin_notices', [ $this, 'show_deactivate_notice' ] );
		}

	}


	/**
	 * Check plugin requirements. If not met, show message and deactivate plugin.
	 *
	 * @since 2.0.0
	 */
	public function check_requirements() {

		if ( false === $this->requirements() ) {
			$this->deactivation_plugin();
		}
	}


	/**
	 * Check if plugin requirements.
	 *
	 * @return bool
	 * @since 2.0.0
	 */
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


	/**
	 * Сообщение при деактивации плагина
	 */
	public function deactivation_plugin() {

		add_action( 'admin_notices', [ $this, 'show_plugin_not_found_notice' ] );
		if ( is_plugin_active( AWOOC_PLUGIN_FILE ) ) {

			deactivate_plugins( AWOOC_PLUGIN_FILE );
			// @codingStandardsIgnoreStart
			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
			}
			// @codingStandardsIgnoreEnd
			add_action( 'admin_notices', [ $this, 'show_deactivate_notice' ] );
		}
	}


	/**
	 * Show required plugins not found message.
	 *
	 * @since 2.0.0
	 */
	public function show_plugin_not_found_notice() {

		$message = sprintf(
		/* translators: 1: Name author plugin */
			__( 'The %s requires installed and activated plugins: ', 'art-woocommerce-order-one-click' ),
			esc_attr( AWOOC_PLUGIN_NAME )
		);

		$message_parts = [];

		foreach ( $this->required_plugins as $key => $required_plugin ) {
			if ( ! $required_plugin['active'] ) {
				$href = '/wp-admin/plugin-install.php?tab=plugin-information&plugin=';

				$href .= sprintf( '%s&TB_iframe=true&width=640&height=500', $required_plugin['slug'] );

				$message_parts[] = sprintf(
					'<strong><em><a href="%s" class="thickbox">%s%s%s</a>%s</em></strong>',
					$href,
					$required_plugin['name'],
					__( ' version ', 'art-woocommerce-order-one-click' ),
					$required_plugin['version'],
					__( ' or higher', 'art-woocommerce-order-one-click' )
				);
			}
		}

		$count = count( $message_parts );

		foreach ( $message_parts as $key => $message_part ) {
			if ( 0 !== $key ) {
				if ( ( ( $count - 1 ) === $key ) ) {
					$message .= __( ' and ', 'art-woocommerce-order-one-click' );
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
	 * Show a notice to inform the user that the plugin has been deactivated.
	 *
	 * @since 2.0.0
	 */
	public function show_deactivate_notice() {

		$message = sprintf(
		/* translators: 1: Name author plugin */
			__( '%s plugin has been deactivated.', 'art-woocommerce-order-one-click' ),
			esc_attr( AWOOC_PLUGIN_NAME )
		);

		$this->admin_notice( $message, 'notice notice-warning is-dismissible' );
	}


	/**
	 * Приглашение поставить оценку
	 */
	public function add_rated() {

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( - 1 );
		}

		update_option( 'woocommerce_awooc_text_rated', 1 );

		wp_die();
	}

}
