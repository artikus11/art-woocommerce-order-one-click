<?php
/**
 * @see         https://wpruse.ru/my-plugins/art-woocommerce-order-one-click/
 * @package     art-woocommerce-order-one-click/classes
 * @author      Artem Abramovich
 */

namespace Art\AWOOC;

use Art\AWOOC\Admin\Settings;
use Art\AWOOC\Admin\Settings_Fields;
use Art\AWOOC\Product\Meta;
use Art\AWOOC\RequestProcessing\EmailModifier;
use Art\AWOOC\RequestProcessing\OrderCreator;

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
	 * @var \Art\AWOOC\Main|null
	 */
	private static ?Main $instance = null;


	/**
	 * @since 2.0.0
	 * @var Front $front_end
	 */
	protected Front $front;


	/**
	 * @since 2.3.6
	 * @var Enqueue $enqueue
	 */
	protected Enqueue $enqueue;


	/**
	 * @since 3.1.0
	 * @var RequestHandler $ajax
	 */
	protected RequestHandler $ajax;


	/**
	 * Added Orders.
	 *
	 * @since 2.0.0
	 * @var OrderCreator $orders
	 */
	protected OrderCreator $orders;


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

		( new Requirements() )->init_hooks();
		( new Enqueue( $this ) )->init_hooks();
		( new RequestHandler( $this ) )->init_hooks();
		( new OrderCreator( $this ) )->init_hooks();
		( new EmailModifier( $this ) )->init_hooks();

		$this->front = new Front( $this );
		$this->front->init_hooks();

		Meta::init_hooks();

		$this->templater = new Templater();
		$this->mode      = new Mode();

		$this->init_hooks();
	}


	/**
	 * Init.
	 * Initialize plugin parts.
	 *
	 * @since 1.8.0
	 */
	public function init_hooks(): void {

		add_action( 'init', [ $this, 'load_textdomain' ] );

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

		$settings[] = include __DIR__ . '/Admin/Settings.php';

		new Settings();
		( new Settings_Fields() )->init_hooks();

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

		if ( ! current_user_can( 'manage_woocommerce' ) ) { // phpcs:ignore WordPress.WP.Capabilities.Unknown
			wp_die( - 1 );
		}

		update_option( 'woocommerce_awooc_text_rated', 1 );

		wp_die();
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


	/**
	 * @return \Art\AWOOC\Front
	 */
	public function get_front(): Front {

		return $this->front;
	}


	/**
	 * @return \Art\AWOOC\Templater
	 */
	public function get_templater(): Templater {

		return $this->templater;
	}


	/**
	 * @param  string $template_name
	 *
	 * @return string
	 */
	public function get_template( string $template_name ): string {

		return $this->get_templater()->get_template( $template_name );
	}


	/**
	 * @return \Art\AWOOC\Mode
	 */
	public function get_mode(): Mode {

		return $this->mode;
	}


	/**
	 * @return string[]
	 */
	public function get_modes(): array {

		return $this->mode->get_modes();
	}


	/**
	 * @return int
	 * @since 3.0.0
	 */
	public function get_selected_form_id(): int {

		return (int) apply_filters( 'awooc_selected_form_id', get_option( 'woocommerce_awooc_select_form' ) );
	}


	/**
	 * @return string
	 * @since 3.0.0
	 */
	public function get_ajax_url(): string {

		$url = 'admin-ajax.php';

		if ( class_exists( 'Polylang' ) && ! defined( 'WP_CLI' ) ) {
			$url = add_query_arg( [ 'lang' => pll_current_language() ], $url );
		}

		if ( defined( 'ICL_LANGUAGE_CODE' ) && ! defined( 'WP_CLI' ) ) {
			$url = add_query_arg( [ 'lang' => ICL_LANGUAGE_CODE ], $url );
		}

		return $url;
	}
}
