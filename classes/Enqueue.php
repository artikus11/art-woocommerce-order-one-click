<?php
/**
 * Файл обработки скриптов и стилей
 *
 * @see     https://wpruse.ru/my-plugins/art-woocommerce-order-one-click/
 * @package art-woocommerce-order-one-click/classes
 *
 * @version 3.0.0
 */

namespace Art\AWOOC;

/**
 * Class Enqueue
 *
 * @author Artem Abramovich
 * @since  2.3.6
 * @since  3.0.0
 */
class Enqueue {

	protected string $suffix;


	protected Main $main;


	public function __construct( $main ) {

		$this->main   = $main;
		$this->suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	}


	public function init_hooks(): void {

		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue' ], 100 );
		add_action( 'wp_enqueue_scripts', [ $this, 'localize' ], 110 );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue' ] );
	}


	/**
	 * Подключаем нужные стили и скрипты
	 */
	public function enqueue(): void {

		wp_register_script(
			'awooc-scripts',
			AWOOC_PLUGIN_URI . 'assets/js/awooc-scripts' . $this->suffix . '.js',
			[ 'jquery', 'jquery-blockui', 'woocommerce', 'wc-add-to-cart-variation' ],
			AWOOC_PLUGIN_VER,
			false
		);

		wp_register_style(
			'awooc-styles',
			AWOOC_PLUGIN_URI . 'assets/css/awooc-styles' . $this->suffix . '.css',
			[],
			AWOOC_PLUGIN_VER
		);
	}


	/**
	 * Подключаем нужные стили и скрипты
	 *
	 * @since  2.0.0
	 */
	public function admin_enqueue(): void {

		//phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ( isset( $_GET['tab'] ) && 'awooc_settings' !== $_GET['tab'] ) && 'woocommerce_page_wc-settings' !== get_current_screen()->id ) {
			return;
		}

		wp_enqueue_style(
			'admin-awooc-styles',
			AWOOC_PLUGIN_URI . 'assets/css/admin-style' . $this->suffix . '.css',
			[],
			AWOOC_PLUGIN_VER
		);

		wp_enqueue_script(
			'admin-awooc-script',
			AWOOC_PLUGIN_URI . 'assets/js/admin-script.js',
			[ 'jquery' ],
			AWOOC_PLUGIN_VER,
			false
		);

		wp_localize_script(
			'admin-awooc-script',
			'awooc_admin',
			[
				'mode_catalog'  => __(
					'On the pages of the categories and the store itself, the Add to Cart buttons are disabled. On the product page, the "Add to cart" button is hidden and the "Order" button appears.',
					'art-woocommerce-order-one-click'
				),
				'mode_normal'   => __(
					'The button "Add to cart" works in the normal mode, that is, goods can be added to the cart and at the same time ordered in one click',
					'art-woocommerce-order-one-click'
				),
				'mode_in_stock' => __(
					'The Order button will appear automatically if: Price not available;  stock status "In Unfulfilled Order"; stock status "Out of stock"; inventory management is enabled at item level and preorders allowed',
					'art-woocommerce-order-one-click'
				),
				'mode_special'  => __(
					'When turned on, it works the same way as normal mode. But if the goods have no price or the product out of stock, then only the Order button will appear.',
					'art-woocommerce-order-one-click'
				),
			]
		);
	}


	/**
	 * @since 3.0.0
	 */
	public function localize(): void {

		if ( ! class_exists( 'Woocommerce' ) ) {
			return;
		}

		if ( is_admin() ) {
			return;
		}

		wp_localize_script(
			'awooc-scripts',
			'awooc_scripts_ajax',
			[
				'url'   => admin_url( $this->main->get_ajax_url() ),
				'nonce' => wp_create_nonce( 'awooc-nonce' ),
			]
		);

		wp_localize_script(
			'awooc-scripts',
			'awooc_scripts_translate',
			[
				'product_qty'        => __( 'Quantity: ', 'art-woocommerce-order-one-click' ),
				'title'              => __( 'Title: ', 'art-woocommerce-order-one-click' ),
				'price'              => __( 'Price: ', 'art-woocommerce-order-one-click' ),
				'sku'                => __( 'SKU: ', 'art-woocommerce-order-one-click' ),
				'formatted_sum'      => __( 'Amount: ', 'art-woocommerce-order-one-click' ),
				'attributes_list'    => __( 'Attributes: ', 'art-woocommerce-order-one-click' ),
				'product_data_title' => __( 'Information about the selected product', 'art-woocommerce-order-one-click' ),
				'product_link'       => __( 'Link to the product: ', 'art-woocommerce-order-one-click' ),
				'title_close'        => __( 'Click to close', 'art-woocommerce-order-one-click' ),
			]
		);

		wp_localize_script(
			'awooc-scripts',
			'awooc_scripts_settings',
			[
				'mode'         => $this->main->get_mode()->get_mode_value(),
				'template'     => awooc_popup(),
				'custom_label' => awooc_custom_button_label(),
				'popup'        => apply_filters(
					'awooc_popup_setting',
					[
						'mailsent_timeout'   => 3000,
						'invalid_timeout'    => 5000,
						'cf7_form_id'        => $this->main->get_selected_form_id(),
						'price_decimal_sep'  => get_option( 'woocommerce_price_decimal_sep' ), // Десятичный разделитель
						'price_num_decimals' => get_option( 'woocommerce_price_num_decimals' ), // Число дробных знаков
						'price_thousand_sep' => get_option( 'woocommerce_price_thousand_sep' ), // Разделитель тысяч
						'css'                => [
							'width'               => 'calc(100vw - 1rem)',
							'maxWidth'            => '600px',
							'maxHeight'           => 'calc(100vh - 1rem)',
							'top'                 => '50%',
							'left'                => '50%',
							'border'              => '4px',
							'borderRadius'        => '4px',
							'cursor'              => 'default',
							'overflowY'           => 'auto',
							'boxShadow'           => '0px 0px 3px 0px rgba(0, 0, 0, 0.2)',
							'zIndex'              => '1000000',
							'transform'           => 'translate(-50%, -50%)',
							'overscroll-behavior' => 'contain',
						],
						'overlay'            => [
							'zIndex'          => '100000',
							'backgroundColor' => '#000',
							'opacity'         => 0.6,
							'cursor'          => 'wait',
						],
						'fadeIn'             => '400',
						'fadeOut'            => '400',
						'focusInput'         => false,
					]
				),
			]
		);
	}
}
