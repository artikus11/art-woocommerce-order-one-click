<?php // @codingStandardsIgnoreLine

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
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_script_style' ) );
		add_action( 'wp_footer', array( $this, 'popup_window_html' ), 30 );

		/**
		 * WooCommerce hooks
		 */
		add_filter( 'woocommerce_is_purchasable', array( $this, 'disable_add_to_cart_no_price' ), 10, 2 );
		//add_filter( 'woocommerce_product_is_in_stock', array( $this, 'disable_add_to_cart_out_stock' ), 10, 2 );
		add_filter( 'woocommerce_hide_invisible_variations', array( $this, 'hide_variable_add_to_cart' ), 10, 3 );
		add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'add_custom_button' ) );


	}


	/**
	 * Подключаем нужные стили и скрипты
	 */
	public function enqueue_script_style() {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		/*
		 * @todo Кнопка не работает на темах Divi и Phlox
		 */
		wp_enqueue_script( 'awooc-scripts', AWOOC_PLUGIN_URI . 'assets/js/awooc-scripts' . $suffix . '.js', array( 'jquery', 'jquery-blockui' ), AWOOC_PLUGIN_VER, false );
		wp_enqueue_style( 'awooc-styles', AWOOC_PLUGIN_URI . 'assets/css/awooc-styles' . $suffix . '.css', array(), AWOOC_PLUGIN_VER );
		wp_localize_script(
			'awooc-scripts',
			'awooc_scripts',
			array(
				'url'                => admin_url( 'admin-ajax.php' ),
				'nonce'              => wp_create_nonce( 'awooc-nonce' ),
				'product_qty'        => __( 'Quantity: ', 'art-woocommerce-order-one-click' ),
				'product_title'      => __( 'Title: ', 'art-woocommerce-order-one-click' ),
				'product_price'      => __( 'Price: ', 'art-woocommerce-order-one-click' ),
				'product_sku'        => __( 'SKU: ', 'art-woocommerce-order-one-click' ),
				'product_attr'       => __( 'Attributes: ', 'art-woocommerce-order-one-click' ),
				'product_data_title' => __( 'Information about the selected product', 'art-woocommerce-order-one-click' ),
				'title_close'        => __( 'Click to close', 'art-woocommerce-order-one-click' ),
				'is_price_stock'     => get_option( 'woocommerce_awooc_no_price' ),
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


	/**
	 * Вывод всплывающего окна
	 *
	 * @since 1.8.0
	 */
	public function popup_window_html() {

		$elements = get_option( 'woocommerce_awooc_select_item' );
		if ( ! is_array( $elements ) ) {
			return;
		}

		include AWOOC_PLUGIN_DIR . '/includes/view/html-popup-window.php';
	}


	/**
	 * Включение кнопки Заказать в если нет цены в простых товарах
	 *
	 * @param bool       $bool
	 * @param WC_Product $product
	 *
	 * @return bool
	 * @todo Переписать функцию с проверкой входящего значения
	 */
	public function disable_add_to_cart_no_price( $bool, $product ) {

		if ( 'variation' === $product->get_type() ) {
			return $bool;
		}

		$mode_catalog = get_option( 'woocommerce_awooc_mode_catalog' );

		if ( 'dont_show_add_to_card' === $mode_catalog ) {
			if ( is_product() ) {
				add_filter( 'woocommerce_product_add_to_cart_text', array( $this, 'disable_text_add_to_cart_to_related' ) );
				add_filter( 'woocommerce_product_add_to_cart_url', array( $this, 'disable_url_add_to_cart_to_related' ) );

				return true;
			}

			return false;
		} else {
			return true;
		}

	}

	/**
	 * Включение кнопки Заказать в если товара нет в наличие в простых товарах
	 *
	 * @param string       $status
	 * @param WC_Product $product
	 *
	 * @return bool
	 */
	public function disable_add_to_cart_out_stock( $status, $product ) {

		$mode_catalog = get_option( 'woocommerce_awooc_mode_catalog' );

		if ( 'instock' !== $product->get_stock_status() ) {
			return true;
		} elseif ( '' === $product->get_price() && $status ) {
			$this->hide_button_add_to_card();

			return true;
		}

		return false;
	}


	/**
	 * Изменение надписи на кнопке при включении режима реагирования на отсутствие цены и наличия
	 *
	 * @param $label
	 *
	 * @return string
	 *
	 * @since 2.2.0
	 */
	public function custom_button_label( $label ) {

		$label = get_option( 'woocommerce_awooc_title_custom' ) ? esc_html( get_option( 'woocommerce_awooc_title_custom' ) ) : '';

		return $label;
	}


	/**
	 * Включение кнопки Заказать в если нет цены или наличия в вариаиях
	 *
	 * @param bool                $bool
	 * @param int                 $product_id
	 * @param WC_Product_Variable $variation
	 *
	 * @return bool
	 *
	 * @since 2.0.0
	 */
	public function hide_variable_add_to_cart( $bool, $product_id, $variation ) {

		if ( 'no_stock_no_price' === get_option( 'woocommerce_awooc_mode_catalog' ) ) {
			if ( ! $variation->is_purchasable() || ! $variation->is_in_stock() ) {

				add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'hide_button_add_to_card' ) );
				add_filter( 'awooc_button_label', array( $this, 'custom_button_label' ) );

				$bool = false;
			}
		}

		return $bool;

	}


	/**
	 * Вывод кнопки Заказать в зависимости от настроек
	 *
	 * @since 1.8.0
	 *
	 */
	public function add_custom_button() {

		global $product;

		$show_add_to_card = get_option( 'woocommerce_awooc_mode_catalog' );

		switch ( $show_add_to_card ) {
			case 'dont_show_add_to_card':
				add_filter( 'woocommerce_product_add_to_cart_text', array( $this, 'disable_text_add_to_cart_to_related' ) );
				add_filter( 'woocommerce_product_add_to_cart_url', array( $this, 'disable_url_add_to_cart_to_related' ) );

				$this->hide_button_add_to_card();
				awooc_html_custom_add_to_cart();
				break;
			case 'no_stock_no_price':
			case 'show_add_to_card':
				awooc_html_custom_add_to_cart();
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
	 * @since 1.8.3
	 *
	 * @return mixed|void
	 */
	public function hide_button_add_to_card() {

		ob_start();
		?>
		<style>
			.woocommerce button.btn,
			.woocommerce button.button.alt,
			.woocommerce-page button.button.alt,
			.woocommerce-variation-add-to-cart .quantity,
			.woocommerce-variation-add-to-cart .single_add_to_cart_button,
			.single_add_to_cart_button,
			.quantity {
				display: none !important;
			}

			.blockUI.blockOverlay {
				background: rgba(0, 0, 0, 1) !important;
			}
		</style>
		<?php

		$disable_add_to_card = apply_filters( 'awooc_disable_add_to_card_style', ob_get_clean() );
		echo wp_kses( $disable_add_to_card, array( 'style' => array() ) );
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
			.qty {
				display: inline-block !important;
			}

			.blockUI.blockOverlay {
				background: rgba(0, 0, 0, 1) !important;
			}
		</style>
		<?php

		$enable_add_to_card = apply_filters( 'awooc_enable_add_to_card_style', ob_get_clean() );
		echo wp_kses( $enable_add_to_card, array( 'style' => array() ) );
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

		$product = wc_get_product();
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
