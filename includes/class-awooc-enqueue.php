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
				'product_attr'       => __( 'Attributes: ', 'art-woocommerce-order-one-click' ),
				'product_data_title' => __( 'Information about the selected product', 'art-woocommerce-order-one-click' ),
				'title_close'        => __( 'Click to close', 'art-woocommerce-order-one-click' ),
			)
		);

		wp_localize_script(
			'awooc-scripts',
			'awooc_scripts_settings',
			array(
				'mode'    => get_option( 'woocommerce_awooc_mode_catalog' ),
				'fadeIn'  => '400',
				'fadeOut' => '400',
			)
		);
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
