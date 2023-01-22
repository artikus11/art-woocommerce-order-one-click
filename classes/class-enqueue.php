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

		if ( get_option( 'woocommerce_awooc_enable_enqueue' ) && is_woocommerce() ) {
			wp_enqueue_script( 'awooc-scripts' );
			wp_enqueue_style( 'awooc-styles' );
		}

	}


	/**
	 * Подключаем нужные стили и скрипты
	 *
	 * @since  2.0.0
	 */
	public function admin_enqueue(): void {

		wp_enqueue_style(
			'admin-awooc-styles',
			AWOOC_PLUGIN_URI . 'assets/css/admin-style' . $this->suffix . '.css',
			[],
			AWOOC_PLUGIN_VER
		);

	}


	/**
	 * @since 3.0.0
	 */
	public function localize(): void {

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
				'mode'     => $this->main->get_mode()->get_mode_value(),
				'template' => awooc_popup(),
				'custom_label' => awooc_custom_button_label( ),
				'popup'    => apply_filters(
					'awooc_popup_setting',
					[
						'mailsent_timeout' => 3000,
						'invalid_timeout'  => 5000,
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
