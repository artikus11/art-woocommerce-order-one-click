<?php
/**
 * @see         https://wpruse.ru/my-plugins/art-woocommerce-order-one-click/
 * @package     art-woocommerce-order-one-click/classes
 * @author      Artem Abramovich
 */

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
	 */
	private static ?Main $instance = null;

	/**
	 * @since 2.0.0
	 * @var Front $front_end
	 */
	private Front $front;

	/**
	 * @since 2.3.6
	 * @var Enqueue $enqueue
	 */
	public Enqueue $enqueue;

	/**
	 * @since 2.0.0
	 * @var Ajax $ajax
	 */
	public Ajax $ajax;

	/**
	 * Added Orders.
	 *
	 * @since 2.0.0
	 * @var Orders $orders
	 */
	public Orders $orders;

	/**
	 * @since 3.0.0
	 * @var Templater $templater
	 */
	public Templater $templater;

	/**
	 * @since 3.0.0
	 * @var Mode
	 */
	public Mode $mode;


	/**
	 * Construct.
	 *
	 * @since 1.8.0
	 * @see   https://github.com/kagg-design/woof-by-category
	 */
	public function __construct() {

		$this->includes();

		$this->init();

		$this->load_textdomain();

	}


	/**
	 * Load plugin parts.
	 *
	 * @since 2.0.0
	 */
	private function includes(): void {

		require AWOOC_PLUGIN_DIR . '/classes/class-requirements.php';
		( new Requirements() )->init();

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

		require AWOOC_PLUGIN_DIR . '/classes/class-response.php';
		require AWOOC_PLUGIN_DIR . '/classes/class-response-popup.php';
		require AWOOC_PLUGIN_DIR . '/classes/class-response-mail.php';
		require AWOOC_PLUGIN_DIR . '/classes/class-response-analytics.php';

		require AWOOC_PLUGIN_DIR . '/classes/class-mode.php';
		$this->mode = new Mode();

		require AWOOC_PLUGIN_DIR . '/classes/class-orders.php';
		$this->orders = new Orders();

	}


	/**
	 * Init.
	 * Initialize plugin parts.
	 *
	 * @since 1.8.0
	 */
	public function init(): void {

		add_action( 'wp_ajax_awooc_rated', [ $this, 'add_rated' ] );

		add_filter( 'plugin_action_links_' . AWOOC_PLUGIN_FILE, [ $this, 'add_plugin_action_links' ], 10, 1 );

		add_filter( 'woocommerce_get_settings_pages', [ $this, 'add_awooc_admin_settings' ], 15 );

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
	public function add_plugin_action_links( array $links ): array {

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
	 * Settings.
	 * Include the WooCommerce settings class.
	 *
	 * @param  array $settings приходит массив настроек.
	 *
	 * @return array
	 * @since 1.8.0
	 * @since 1.8.5
	 */
	public function add_awooc_admin_settings( array $settings ): array {

		$settings[] = include __DIR__ . '/class-settings.php';

		return $settings;
	}


	/**
	 * Textdomain.
	 * Load the textdomain based on WP language.
	 *
	 * @since 2.0.0
	 */
	public function load_textdomain(): void {

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
	 * Приглашение поставить оценку
	 */
	public function add_rated(): void {

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( - 1 );
		}

		update_option( 'woocommerce_awooc_text_rated', 1 );

		wp_die();
	}


	/**
	 * @return \Art\AWOOC\Front
	 */
	public function get_front(): Front {

		return $this->front;
	}


	/**
	 * Проверка на простой товар
	 *
	 * @return bool
	 */
	public function is_simple(): bool {

		$product = wc_get_product();

		if ( is_null( $product ) ) {
			$product = $GLOBALS['product'];
		}

		return $product->is_type( 'simple' );

	}
}
