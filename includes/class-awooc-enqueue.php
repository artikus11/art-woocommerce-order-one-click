<?php
/**
 * Файл обработки скриптов и стилей
 *
 * @see     https://wpruse.ru/my-plugins/art-woocommerce-order-one-click/
 * @package art-woocommerce-order-one-click/includes
 * @version 2.3.6
 */

/**
 * Class AWOOC_Enqueue
 *
 * @author Artem Abramovich
 * @since  2.3.6
 */
class AWOOC_Enqueue {

	public function __construct() {

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_script_style' ), 100 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_script_style' ) );
	}


	/**
	 * Подключаем нужные стили и скрипты
	 */
	public function enqueue_script_style() {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_register_script(
			'awooc-scripts',
			AWOOC_PLUGIN_URI . 'assets/js/awooc-scripts' . $suffix . '.js',
			array( 'jquery', 'jquery-blockui' ),
			AWOOC_PLUGIN_VER,
			false
		);

		wp_register_style(
			'awooc-styles',
			AWOOC_PLUGIN_URI . 'assets/css/awooc-styles' . $suffix . '.css',
			array(),
			AWOOC_PLUGIN_VER
		);

		wp_localize_script(
			'awooc-scripts',
			'awooc_scripts_ajax',
			array(
				'url'   => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( 'awooc-nonce' ),
			)
		);

		wp_localize_script(
			'awooc-scripts',
			'awooc_scripts_translate',
			array(
				'product_qty'        => __( 'Quantity: ', 'art-woocommerce-order-one-click' ),
				'product_title'      => __( 'Title: ', 'art-woocommerce-order-one-click' ),
				'product_price'      => __( 'Price: ', 'art-woocommerce-order-one-click' ),
				'product_sku'        => __( 'SKU: ', 'art-woocommerce-order-one-click' ),
				'product_sum'        => __( 'Amount: ', 'art-woocommerce-order-one-click' ),
				'product_attr'       => __( 'Attributes: ', 'art-woocommerce-order-one-click' ),
				'product_data_title' => __( 'Information about the selected product', 'art-woocommerce-order-one-click' ),
				'product_link'        => __( 'Link to the product: ', 'art-woocommerce-order-one-click' ),
				'title_close'        => __( 'Click to close', 'art-woocommerce-order-one-click' ),
			)
		);

		wp_localize_script(
			'awooc-scripts',
			'awooc_scripts_settings',
			array(
				'mode'  => get_option( 'woocommerce_awooc_mode_catalog' ),
				'popup' => apply_filters(
					'awooc_popup_setting',
					array(
						'css'        => array(
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
						),
						'overlay'    => array(
							'zIndex'          => '100000',
							'backgroundColor' => '#000',
							'opacity'         => 0.6,
							'cursor'          => 'wait',
						),
						'fadeIn'     => '400',
						'fadeOut'    => '400',
						'focusInput' => false,
					)
				),
			)
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
	public function admin_enqueue_script_style() {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_style( 'admin-awooc-styles', AWOOC_PLUGIN_URI . 'assets/css/admin-style' . $suffix . '.css', array(), AWOOC_PLUGIN_VER );

	}
}
