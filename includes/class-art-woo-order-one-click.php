<?php // @codingStandardsIgnoreLine
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
	 * @var object $instance The instance of ArtWoo_Order_One_Click.
	 */
	private static $instance;

	/**
	 * Added AWOOC_Front_End.
	 *
	 * @since 2.0.0
	 * @var object AWOOC_Front_End $front_end
	 */
	public $front_end;

	/**
	 * Added AWOOC_Ajax.
	 *
	 * @since 2.0.0
	 * @var object AWOOC_Ajax $ajax
	 */
	public $ajax;

	/**
	 * Added AWOOC_Orders.
	 *
	 * @since 2.0.0
	 * @var object AWOOC_Orders $front_end
	 */
	public $orders;

	/**
	 * @since 2.0.0
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

		$this->load_dependencies();

		$this->init();

		$this->load_textdomain();
	}


	/**
	 *
	 * Load plugin parts.
	 *
	 *
	 * @since 2.0.0
	 */
	private function load_dependencies() {

		/**
		 * Helpers
		 */
		require AWOOC_PLUGIN_DIR . '/includes/helpers.php';

		/**
		 * Hiding field to CF7
		 */
		require AWOOC_PLUGIN_DIR . '/includes/admin/added-cf7-field.php';

		/**
		 * Hiding field to CF7
		 */
		require AWOOC_PLUGIN_DIR . '/includes/admin/class-awooc-admin-meta-box.php';

		/**
		 * Front end
		 */
		require AWOOC_PLUGIN_DIR . '/includes/class-awooc-frontend.php';
		$this->front_end = new AWOOC_Front_End();

		/**
		 * Ajax
		 */
		require AWOOC_PLUGIN_DIR . '/includes/class-awooc-ajax.php';
		$this->ajax = new AWOOC_Ajax();

		/**
		 * Создание заказов
		 */
		require AWOOC_PLUGIN_DIR . '/includes/class-awooc-orders.php';
		$this->orders = new AWOOC_Orders();

		/**
		 * Template functions
		 */
		require AWOOC_PLUGIN_DIR . '/includes/awooc-template-functions.php';
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

		add_action( 'admin_init', array( $this, 'check_requirements' ) );
		add_action( 'admin_init', array( $this, 'check_php_version' ) );
		add_action( 'wp_ajax_awooc_rated', array( $this, 'add_rated' ) );
		add_filter( 'plugin_action_links_' . AWOOC_PLUGIN_FILE, array( $this, 'add_plugin_action_links' ), 10, 1 );
		add_filter( 'woocommerce_get_settings_pages', array( $this, 'add_awooc_admin_settings' ), 15 );

		foreach ( $this->required_plugins as $required_plugin ) {
			if ( ! class_exists( $required_plugin['class'] ) ) {
				return;
			}
		}
	}

	/**
	 * Textdomain.
	 *
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
	 * @param array $settings
	 *
	 * @return array
	 *
	 * @since 1.8.0
	 * @since 1.8.5
	 */
	public function add_awooc_admin_settings( $settings ) {

		$settings[] = include __DIR__ . '/admin/class-awooc-admin-settings.php';

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
	 * @param array $links List of existing links.
	 *
	 * @return array List of modified links.
	 */
	public function add_plugin_action_links( $links ) {

		$plugin_links = array(
			'settings' => '<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=awooc_settings' ) ) . '">' . esc_html__( 'Settings', 'art-woocommerce-order-one-click' ) . '</a>',
		);

		return array_merge( $plugin_links, $links );

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
				'%1$s requires PHP version 5.6 or higher. Your current PHP version is %2$s. Please upgrade PHP version to run this plugin.',
				'art-woocommerce-order-one-click'
			),
			esc_html( AWOOC_PLUGIN_NAME ),
			PHP_VERSION
		);

		$this->admin_notice( $message, 'notice notice-error is-dismissible' );

	}


	/**
	 * Check plugin PHP version. If not met, show message and deactivate plugin.
	 *
	 * @since 2.0.0
	 */
	public function check_php_version() {

		if ( version_compare( PHP_VERSION, '5.6', 'lt' ) ) {

			deactivate_plugins( plugin_basename( AWOOC_PLUGIN_FILE ) );

			add_action( 'admin_notices', array( $this, 'php_version_notice' ) );
			add_action( 'admin_notices', array( $this, 'show_deactivate_notice' ) );
		}

	}

	/**
	 * Check plugin requirements. If not met, show message and deactivate plugin.
	 *
	 * @since 2.0.0
	 */
	public function check_requirements() {

		if ( false === $this->requirements() ) {
			add_action( 'admin_notices', array( $this, 'show_plugin_not_found_notice' ) );
			if ( is_plugin_active( AWOOC_PLUGIN_FILE ) ) {

				deactivate_plugins( AWOOC_PLUGIN_FILE );
				// @codingStandardsIgnoreStart
				if ( isset( $_GET['activate'] ) ) {
					unset( $_GET['activate'] );
				}
				// @codingStandardsIgnoreEnd
				add_action( 'admin_notices', array( $this, 'show_deactivate_notice' ) );
			}
		}
	}


	/**
	 * Check if plugin requirements.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
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

		$message_parts = array();

		foreach ( $this->required_plugins as $key => $required_plugin ) {
			if ( ! $required_plugin['active'] ) {
				$href = '/wp-admin/plugin-install.php?tab=plugin-information&plugin=';

				$href .= $required_plugin['slug'] . '&TB_iframe=true&width=640&height=500';

				$message_parts[] = '<strong><em><a href="' . $href . '" class="thickbox">' . $required_plugin['name'] . __( ' version ', 'art-woocommerce-order-one-click' ) . $required_plugin['version'] . '</a>' . __( ' or higher', 'art-woocommerce-order-one-click' ) . '</em></strong>';
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
	 * Show admin notice.
	 *
	 * @since 2.0.0
	 *
	 * @param string $message Message to show.
	 * @param string $class   Message class: notice notice-success notice-error notice-warning notice-info is-dismissible
	 *
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
	 * Deleting settings when uninstalling the plugin
	 *
	 * @since 2.0.0
	 */
	public static function uninstall() {

		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		if ( 'yes' === get_option( 'woocommerce_awooc_not_del_settings' ) ) {
			return;
		}

		$options = apply_filters(
			'awooc_uninstall_options',
			array(
				'woocommerce_awooc_padding',
				'woocommerce_awooc_margin',
				'woocommerce_awooc_mode_catalog',
				'woocommerce_awooc_select_form',
				'woocommerce_awooc_title_button',
				'woocommerce_awooc_select_item',
				'woocommerce_awooc_created_order',
				'woocommerce_awooc_title_custom',
				'woocommerce_awooc_no_price',
				'woocommerce_awooc_text_rated',
				'woocommerce_awooc_сhange_subject',
				'woocommerce_awooc_not_del_settings',
			)
		);

		foreach ( $options as $option ) {
			delete_option( $option );
		}

	}


	/**
	 * Создание формы при первой активации
	 *
	 * @since 2.3.0
	 */
	public static function install_form() {

		if ( get_option( 'awooc_active' ) ) {
			return;
		}

		$mail_form = sprintf(
			'
[text* awooc-text placeholder "%1$s"]
[email* awooc-email placeholder "%2$s"]
[tel* awooc-tel placeholder "%3$s"]
[awooc_hidden awooc-hidden-data]
[submit "%4$s"]',
			__( 'Your Name', 'art-woocommerce-order-one-click' ),
			__( 'Your Email', 'art-woocommerce-order-one-click' ),
			__( 'Your Phone', 'art-woocommerce-order-one-click' ),
			__( 'Send', 'art-woocommerce-order-one-click' )
		);

		$mail_body =
			/* translators: %s: [awooc-text] */
			sprintf( __( 'Name: %s', 'art-woocommerce-order-one-click' ), '[awooc-text]' ) . "\n" .
			/* translators: %s: [awooc-email]' */
			sprintf( __( 'Email: %s', 'art-woocommerce-order-one-click' ), '[awooc-email]' ) . "\n" .
			/* translators: %s: [awooc-tel] */
			sprintf( __( 'Phone: %s', 'art-woocommerce-order-one-click' ), '[awooc-tel]' ) . "\n\n" . '[awooc-hidden-data]' . "\n\n" . '-- ' . "\n" .
			/* translators: 1: blog name, 2: blog URL */
			sprintf( __( 'This e-mail was sent from a contact form on %1$s (%2$s)', 'contact-form-7' ), get_bloginfo( 'name' ), get_bloginfo( 'url' ) );

		$contact_form = WPCF7_ContactForm::get_template();

		$contact_form->set_properties(
			[
				'form'   => $mail_form,
				'mail'   => [
					'subject'            => /* translators: 1: blog name, 2: blog URL */ sprintf(
						__( 'Order from the site %1$s (%2$s)', 'art-woocommerce-order-one-click' ),
						get_bloginfo( 'name' ),
						get_bloginfo( 'url' )
					),
					'sender'             => sprintf( '%s <%s>', get_bloginfo( 'name' ), WPCF7_ContactFormTemplate::from_email() ),
					'body'               => $mail_body,
					'recipient'          => get_option( 'admin_email' ),
					'additional_headers' => '',
					'attachments'        => '',
					'use_html'           => 1,
					'exclude_blank'      => 0,
				],
				'mail_2' => [
					'subject'            => /* translators: 1: blog name, 2: blog URL */ sprintf(
						__( 'Order from the site %1$s (%2$s)', 'art-woocommerce-order-one-click' ),
						get_bloginfo( 'name' ),
						get_bloginfo( 'url' )
					),
					'sender'             => sprintf( '%s <%s>', get_bloginfo( 'name' ), WPCF7_ContactFormTemplate::from_email() ),
					'body'               => $mail_body,
					'recipient'          => get_option( 'admin_email' ),
					'additional_headers' => '',
					'attachments'        => '',
					'use_html'           => 1,
					'exclude_blank'      => 0,
				],
			]
		);

		$props = $contact_form->get_properties();

		$post_content = implode( "\n", wpcf7_array_flatten( $props ) );

		$post_id = wp_insert_post(
			array(
				'post_type'    => 'wpcf7_contact_form',
				'post_status'  => 'publish',
				'post_title'   => __( 'Order One Click', 'art-woocommerce-order-one-click' ),
				'post_content' => trim( $post_content ),
			)
		);

		if ( $post_id ) {
			foreach ( $props as $prop => $value ) {
				update_post_meta(
					$post_id,
					'_' . $prop,
					wpcf7_normalize_newline_deep( $value )
				);
			}
		}

		$option = [
			'awooc_validate' => [
				'timestamp'     => current_time( 'timestamp' ),
				'version'       => AWOOC_PLUGIN_VER,
				'count_valid'   => 1,
				'count_invalid' => 0,
			],
		];

		$awooc_active = get_option( 'awooc_active' );

		if ( false === $awooc_active ) {
			update_option( 'awooc_active', $option );
		}

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
