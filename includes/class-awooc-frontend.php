<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class AWOOC_Front_End
 *
 * @author Artem Abramovich
 * @since  1.8.0
 */
class AWOOC_Front_End {

	/**
	 * Constructor.
	 *
	 * @since 1.8.0
	 */
	public function __construct() {

		/**
		 * Base hooks
		 */
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_script_style' ), 100 );
		add_action( 'wp_footer', array( $this, 'popup_window_html' ), 30 );

		/**
		 * WooCommerce hooks
		 */
		add_filter( 'woocommerce_is_purchasable', array( $this, 'disable_add_to_cart' ), 10 );
		add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'add_custom_button' ) );

	}


	/**
	 * Подключаем нужные стили и скрипты
	 */
	public function enqueue_script_style() {

		wp_enqueue_script( 'awooc-scripts', AWOOC_PLUGIN_URI . 'assets/js/awooc-scripts.js', array( 'jquery' ), AWOOC_PLUGIN_VER, true );
		wp_enqueue_style( 'awooc-styles', AWOOC_PLUGIN_URI . 'assets/css/awooc-styles.css', array(), AWOOC_PLUGIN_VER );
		wp_localize_script(
			'awooc-scripts',
			'awooc_scrpts',
			array(
				'url'   => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( 'awooc-nonce' ),
			)
		);
	}


	/**
	 * Вывод всплывающего окна
	 *
	 * @since 1.8.0
	 */
	public function popup_window_html() {

		if ( ! is_product() ) {
			return;
		}

		$elements = get_option( 'woocommerce_awooc_select_item' );
		if ( ! is_array( $elements ) ) {
			return;
		}

		include AWOOC_PLUGIN_DIR . 'includes/view/html-popup-window.php';
	}

	/**
	 * Включение режима каталога в зависимости от настроек
	 *
	 * @return bool
	 */
	public function disable_add_to_cart() {

		$mode_catalog = get_option( 'woocommerce_awooc_mode_catalog' );

		if ( 'dont_show_add_to_card' === $mode_catalog ) {
			if ( is_product() ) {
				return true;
			}

			return false;
		} else {
			return true;
		}
	}


	/**
	 * Вывод кнопки Заказать в зависимости от настроек
	 *
	 * @since 1.8.0
	 */
	public function add_custom_button() {

		global $product;

		$show_add_to_card = get_option( 'woocommerce_awooc_mode_catalog' );

		switch ( $show_add_to_card ) {
			case 'dont_show_add_to_card':
				add_filter( 'woocommerce_product_add_to_cart_text', array( $this, 'disable_text_add_to_cart_to_related' ) );
				add_filter( 'woocommerce_product_add_to_cart_url', array( $this, 'disable_url_add_to_cart_to_related' ) );
				$this->hide_button_add_to_card();
				if ( is_product() ) {
					awooc_html_custom_add_to_cart();
				}
				break;
			case 'show_add_to_card':
				$this->show_button_add_to_card();
				if ( is_product() ) {
					awooc_html_custom_add_to_cart();
				}
				break;
			case 'in_stock_add_to_card':
				if ( $product->is_on_backorder() || 0 === $product->get_price() || ! $product->get_price() || ! $product->is_in_stock() ) {
					$this->hide_button_add_to_card();
					awooc_html_custom_add_to_cart();
				}
				break;
		}

	}


	/**
	 * Скрытие кнопки купить
	 *
	 * @since 1.8.0
	 *
	 * @return mixed|void
	 */
	public function hide_button_add_to_card() {

		ob_start();
		?>
		<style>
			.woocommerce button.button.alt,
			.woocommerce-page button.button.alt,
			.woocommerce-variation-add-to-cart .quantity,
			.woocommerce-variation-add-to-cart .single_add_to_cart_button,
			.single_add_to_cart_button,
			input.qty {
				display: none !important;
			}
			.blockUI.blockOverlay {
				background: rgba(0,0,0,1) !important;
			}
		</style>
		<?php

		$disable_add_to_card = apply_filters( 'awooc_disable_add_to_card_style', ob_get_clean() );
		echo $disable_add_to_card;// WPCS: XSS ok.
	}


	/**
	 * Показ кнопки В корзину
	 *
	 * @since 1.8.0
	 *
	 * @return mixed|void
	 */
	public function show_button_add_to_card() {

		ob_start();
		?>
		<style>
			.woocommerce-variation-add-to-cart,
			.single_add_to_cart_button,
			input.qty {
				display: inline-block !important;
			}
			.blockUI.blockOverlay {
				background: rgba(0,0,0,1) !important;
			}
		</style>
		<?php

		$enable_add_to_card = apply_filters( 'awooc_enable_add_to_card_style', ob_get_clean() );
		echo $enable_add_to_card;// WPCS: XSS ok.
	}


	/**
	 *  Замена урл на кнопках в похожих товарах на страницах товарах
	 *
	 *
	 * @since 1.8.0
	 *
	 * @param $url
	 *
	 * @return string
	 */
	public function disable_url_add_to_cart_to_related( $url ) {

		global $product;
		if ( is_product() ) {
			$url = get_permalink( $product->get_id() );
		}

		return $url;
	}

	/**
	 * Замена текста на кнопках в похожих товарах на страницах товарах
	 *
	 * @since 1.8.0
	 *
	 * @param $text
	 *
	 * @return string
	 */
	public function disable_text_add_to_cart_to_related( $text ) {

		if ( is_product() ) {
			$text = __( 'Read more', 'woocommerce' );
		}

		return $text;
	}
}
