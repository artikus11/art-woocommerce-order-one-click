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

		if ('woocommerce_page_wc-settings' !== get_current_screen()->id && $_GET['tab'] !== 'awooc_settings'){
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

		[ $has_product_page, $has_wc_blocks, $has_wc_shortcode_products ] = $this->has_product();

		if ( $has_product_page || $has_wc_blocks || $has_wc_shortcode_products ) {

			wp_localize_script(
				'awooc-scripts',
				'awooc_scripts_ajax',
				[
					'url'   => admin_url( 'admin-ajax.php' ),
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
					'custom_label' => awooc_custom_button_label(), //TODO не работает при выводе товаров шорткодом или блоком. Сделать проверку на шорткод?
					'popup'        => apply_filters(
						'awooc_popup_setting',
						[
							'mailsent_timeout' => 3000,
							'invalid_timeout'  => 5000,
							'cf7_form_id' => get_option( 'woocommerce_awooc_select_form' ),
							'css'              => [
								'width'        => '100%',
								'maxWidth'     => '600px',
								'maxHeight'    => '600px',
								'top'          => '50%',
								'left'         => '50%',
								'border'       => '4px',
								'borderRadius' => '4px',
								'cursor'       => 'default',
								'overflowY'    => 'auto',
								'boxShadow'    => '0px 0px 3px 0px rgba(0, 0, 0, 0.2)',
								'zIndex'       => '1000000',
								'transform'    => 'translate(-50%, -50%)',
							],
							'overlay'          => [
								'zIndex'          => '100000',
								'backgroundColor' => '#000',
								'opacity'         => 0.6,
								'cursor'          => 'wait',
							],
							'fadeIn'           => '400',
							'fadeOut'          => '400',
							'focusInput'       => false,
						]
					),
				]
			);
		}

	}


	/**
	 * @return array
	 */
	protected function has_product(): array {

		$has_product_page = is_shop() || is_product_category() || is_product_tag() || is_product();

		global $post;

		if ( ! $post ) {
			return [ false, false, false ];
		}

		$parse_content  = parse_blocks( $post->post_content );
		$blocks_name    = array_filter( wp_list_pluck( $parse_content, 'blockName' ) );
		$wc_blocks_name = [];

		foreach ( $blocks_name as $block_name ) {
			if ( false !== strpos( $block_name, 'woocommerce' ) ) {
				$wc_blocks_name[] = $block_name;
			}
		}

		$has_wc_blocks = false;

		if ( ! empty( $wc_blocks_name ) ) {
			$has_wc_blocks = true;
		}

		$has_wc_shortcode_products = false;

		if ( has_shortcode( $post->post_content, 'products' ) ) {
			$has_wc_shortcode_products = true;
		}

		return [ $has_product_page, $has_wc_blocks, $has_wc_shortcode_products ];
	}

}
