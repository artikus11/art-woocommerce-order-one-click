<?php
/**
 * Front.
 *
 * @see     https://wpruse.ru/my-plugins/art-woocommerce-order-one-click/
 * @package art-woocommerce-order-one-click/classes
 * @version 3.0.0
 */

namespace Art\AWOOC;

use WC_Product;

/**
 * Class Front
 *
 * @author Artem Abramovich
 * @since  1.8.0
 */
class Front {

	/**
	 * @var string Режим работы из опций
	 */
	private $mode;

	protected Main $main;


	public function __construct( Main $main ) {

		$this->main = $main;
		$this->mode = get_option( 'woocommerce_awooc_mode_catalog' );

	}


	/**
	 * Инициализация хуков
	 *
	 * @since 2.3.6
	 */
	public function init_hooks(): void {

		add_filter( 'woocommerce_locate_template', [ $this, 'modify_add_to_cart_button_template' ], 1, 2 );

	}


	public function modify_add_to_cart_button_template( $template, $template_name) {


		if ( 'single-product/add-to-cart/simple.php' === $template_name ) {

			$template = $this->get_template_mode( $template );

		}

		if ( 'single-product/add-to-cart/variation-add-to-cart-button.php' === $template_name ) {

			$template = $this->get_template_mode( $template, 'variable' );

		}

		//@todo сделать отдельные файлы для режимов которые будут подключать кнопку на каталоге, проверить вывод кнопки при выводе товаров блоком
		if ( 'loop/add-to-cart.php' === $template_name ) {

			$template = $this->get_template_mode( $template );

		}


		return $template;
	}


	/**
	 * Метод отключения надписей в цикле товаров для разных режимов
	 *
	 * @since 2.3.6
	 */
	public function disable_loop(): void {

		add_filter( 'woocommerce_product_add_to_cart_text', [ $this, 'disable_text_add_to_cart_to_related' ] );
		add_filter( 'woocommerce_product_add_to_cart_url', [ $this, 'disable_url_add_to_cart_to_related' ] );
		add_filter( 'woocommerce_loop_add_to_cart_args', [ $this, 'disable_ajax_add_to_cart_to_related' ], 10, 2 );
	}


	/**
	 *  Замена урл на кнопках в похожих товарах на страницах товарах
	 *
	 * @param  string $url входящий урл.
	 *
	 * @return string
	 * @since 1.8.0
	 */
	public function disable_url_add_to_cart_to_related( string $url ): string {

		if ( is_product() ) {
			$url = get_permalink();
		}

		return $url;
	}


	/**
	 * Замена текста на кнопках в похожих товарах на страницах товарах
	 *
	 * @param  string $text входящий текст на кнопке.
	 *
	 * @return string
	 * @since 1.8.0
	 */
	public function disable_text_add_to_cart_to_related( string $text ): string {

		if ( is_product() ) {
			$text = __( 'Read more', 'woocommerce' );
		}

		return $text;
	}


	/**
	 * Удаление класса вызова ajax в режиме каталога для похожих товаров
	 *
	 * @param  array       $args    массив аргументов.
	 * @param  \WC_Product $product объект продукта.
	 *
	 * @return array
	 * @since 2.2.5
	 */
	public function disable_ajax_add_to_cart_to_related( array $args, WC_Product $product ): array {

		$search   = 'ajax_add_to_cart';
		$position = strrpos( $args['class'], $search );

		if ( false !== $position && ( 'simple' === $product->get_type() && is_product() ) ) {
			$args['class'] = substr_replace( $args['class'], '', $position, strlen( $search ) );
		}

		return $args;
	}


	/**
	 *
	 * 'dont_show_add_to_card' => __( 'Catalog mode', 'art-woocommerce-order-one-click' )
	 * 'show_add_to_card'      => __( 'Normal mode', 'art-woocommerce-order-one-click' )
	 * 'in_stock_add_to_card'  => __( 'Pre-order mode', 'art-woocommerce-order-one-click' )
	 * 'no_stock_no_price'     => __( 'Special mode', 'art-woocommerce-order-one-click' )
	 *
	 * @param         $template
	 * @param  string $type
	 *
	 * @return mixed
	 */
	protected function get_template_mode( $template, string $type = 'simple' ) {

		$product = wc_get_product();

		if ( 'yes' === $product->get_meta( '_awooc_button', true ) ) {
			return $template;
		}

		foreach ( $this->main->get_modes() as $option => $name ) {
			if ( $option === $this->main->get_mode()->get_mode_value() ) {
				$template = $this->main->get_template( "add-to-cart/$type-$name.php" );
			}
		}

		return $template;
	}


	/**
	 * Инициализация хуков
	 *
	 * @deprecated New architecture.
	 * @since      3.0.0
	 */
	public function legacy_hooks(): void {

		_deprecated_function( __METHOD__, '3.0.0' );

		if ( ! WP_DEBUG_LOG ) {
			return;
		}

		/**
		 * WooCommerce setup_hooks
		 *
		 * deprecated
		 */
		add_filter( 'woocommerce_is_purchasable', [ $this, 'disable_add_to_cart_no_price' ], 10, 2 );
		add_filter( 'woocommerce_product_is_in_stock', [ $this, 'disable_add_to_cart_out_stock' ], 10, 2 );
		add_filter( 'woocommerce_hide_invisible_variations', [ $this, 'hide_variable_add_to_cart' ], 10, 3 );
		add_action( 'woocommerce_after_add_to_cart_button', [ $this, 'add_custom_button' ], 15 );

	}


	/**
	 * Вывод кнопки Заказать в зависимости от настроек
	 *
	 * @since      1.8.0
	 * @since      2.3.6
	 *
	 * @deprecated 3.0.0 New architecture.
	 *
	 * @todo       режим спецзаказа - отключение похожих если нет запасов
	 */
	public function add_custom_button(): void {
		_deprecated_function( __METHOD__, '3.0.0' );

		if ( ! WP_DEBUG_LOG ) {
			return;
		}

		$product = wc_get_product();

		if ( 'yes' === $product->get_meta( '_awooc_button', true ) ) {
			return;
		}

		switch ( $this->mode ) {
			case 'dont_show_add_to_card':
				$this->disable_loop();
				$this->hide_button_add_to_card();
				awooc_html_custom_add_to_cart();
				break;
			case 'no_stock_no_price':

				if ( $product->is_on_backorder() || $product->is_in_stock() ) {
					$this->disable_loop();
				}

				awooc_html_custom_add_to_cart();
				break;
			case 'show_add_to_card':
				awooc_html_custom_add_to_cart();
				break;
			case 'in_stock_add_to_card':
				if ( $product->is_on_backorder() || $product->is_in_stock() || ( 0 === $product->get_price() || empty( $product->get_price() ) ) ) {
					$this->disable_loop();
					$this->hide_button_add_to_card();
					awooc_html_custom_add_to_cart();
				}
				break;
		}
	}


	/**
	 * Включение кнопки Заказать в если нет цены в простых товарах
	 *
	 * @param  bool        $bool    входящее булево значение.
	 * @param  \WC_Product $product объект продукта.
	 *
	 * @return bool
	 *
	 * @deprecated 3.0.0 New architecture.
	 *
	 * @since      2.2.0
	 */
	public function disable_add_to_cart_no_price( $bool, $product ): bool {

		_deprecated_function( __METHOD__, '3.0.0' );

		if ( ! WP_DEBUG_LOG ) {
			return $bool;
		}

		if ( 'variation' === $product->get_type() ) {
			return $bool;
		}

		if ( 'yes' === $product->get_meta( '_awooc_button', true ) ) {
			return $bool;
		}

		if ( 'dont_show_add_to_card' === $this->mode ) {

			if ( is_product() ) {
				$bool = true;
			} else {
				$bool = false;
			}
		}

		if ( 'in_stock_add_to_card' === $this->mode && false === $bool ) {

			if ( is_product() ) {
				$bool = true;
			}

		}

		if ( 'no_stock_no_price' === $this->mode && false === $bool ) {

			if ( is_product() ) {
				$bool = true;
			}

			add_action( 'woocommerce_after_add_to_cart_button', [ $this, 'hide_button_add_to_card' ] );
			add_filter( 'awooc_button_label', [ $this, 'custom_button_label' ] );
		}

		return $bool;
	}


	/**
	 * Включение кнопки Заказать в если нет в наличии в простых товарах
	 *
	 * @param  bool        $status  входящее булево значение.
	 * @param  \WC_Product $product объект продукта.
	 *
	 * @return bool
	 *
	 * @deprecated 3.0.0 New architecture.
	 *
	 * @since      2.2.0
	 */
	public function disable_add_to_cart_out_stock( bool $status, WC_Product $product ): bool {

		_deprecated_function( __METHOD__, '3.0.0' );

		if ( ! WP_DEBUG_LOG ) {
			return $status;
		}

		if ( 'variation' === $product->get_type() ) {
			return $status;
		}

		if ( 'yes' === $product->get_meta( '_awooc_button', true ) ) {
			return $status;
		}

		if ( false === $status ) {
			switch ( $this->main->get_mode() ) {
				case 'dont_show_add_to_card':
					if ( is_product() ) {
						$status = true;
					}

					break;
				case 'in_stock_add_to_card':
					if ( is_product() ) {
						$status = true;
					}

					add_action( 'woocommerce_after_add_to_cart_button', [ $this, 'hide_button_add_to_card' ] );
					break;
				case 'no_stock_no_price':
					if ( is_product() ) {
						$status = true;

						add_filter(
							'woocommerce_get_stock_html',
							function ( $html, $product ) {

								$html = '<p class="stock out-of-stock">Нет наличии</p>';

								return $html;
							},
							10,
							2
						);
					}

					add_action( 'woocommerce_before_add_to_cart_button', [ $this, 'hide_button_add_to_card' ] );
					add_filter( 'awooc_button_label', [ $this, 'custom_button_label' ] );
					break;
			}
		}

		return $status;
	}


	/**
	 * Изменение надписи на кнопке при включении режима реагирования на отсутствие цены и наличия
	 *
	 * @param  string $label входящая строка из опций.
	 *
	 * @return string
	 *
	 * @deprecated 3.0.0 New architecture.
	 *
	 * @since      2.2.0
	 */
	public function custom_button_label( string $label ): string {

		_deprecated_function( __METHOD__, '3.0.0' );

		if ( ! WP_DEBUG_LOG ) {
			return $label;
		}

		$label_button        = esc_html( get_option( 'woocommerce_awooc_title_button' ) );
		$custom_label_button = esc_html( get_option( 'woocommerce_awooc_title_custom' ) );

		return $custom_label_button ? : $label_button;
	}


	/**
	 * Включение кнопки Заказать в если нет цены или наличия в вариаиях
	 *
	 * @param  bool                 $bool       входящее булево значение.
	 * @param  int                  $product_id ID родительского товара.
	 * @param  \WC_Product_Variable $variation  объект вариации.
	 *
	 * @return bool
	 *
	 * @since      2.0.0
	 *
	 * @deprecated 3.0.0 New architecture.
	 *
	 * @todo       странное поведение кнопки для вариации если нет на складе вариации или нет цены
	 */
	public function hide_variable_add_to_cart( $bool, $product_id, $variation ): bool {

		_deprecated_function( __METHOD__, '3.0.0' );

		if ( ! WP_DEBUG_LOG ) {
			return $bool;
		}

		$product = wc_get_product( $product_id );

		if ( 'yes' === $product->get_meta( '_awooc_button', true ) ) {
			return $bool;
		}

		if ( 'no_stock_no_price' === $this->mode || 'dont_show_add_to_card' === $this->mode ) {

			if ( ! $product->get_price() ) {
				$bool = false;
				add_action( 'woocommerce_after_add_to_cart_button', [ $this, 'hide_button_add_to_card' ] );
				remove_filter( 'woocommerce_single_variation', 'woocommerce_single_variation', 10 );

				if ( 'no_stock_no_price' === $this->mode ) {
					add_filter( 'awooc_button_label', [ $this, 'custom_button_label' ] );
				}
			}
		}

		return $bool;

	}


	/**
	 * Скрытие кнопки купить
	 *
	 * @return void
	 * @since      1.8.3
	 *
	 * @deprecated 3.0.0 New architecture.
	 *
	 * @since      1.8.0
	 */
	public function hide_button_add_to_card(): void {

		_deprecated_function( __METHOD__, '3.0.0' );

		if ( ! WP_DEBUG_LOG ) {
			return;
		}

		ob_start();
		?>
		<style>
			.woocommerce-variation-add-to-cart .quantity,
			.woocommerce-variation-add-to-cart .single_add_to_cart_button,
			.woocommerce button.button.single_add_to_cart_button,
			.quantity {
				display: none !important;
			}

			.blockUI.blockOverlay {
				background: rgba(0, 0, 0, 1) !important;
			}
		</style>
		<?php

		$disable_add_to_card = apply_filters( 'awooc_disable_add_to_card_style', ob_get_clean() );
		echo wp_kses( $disable_add_to_card, [ 'style' => [] ] );
	}


	/**
	 * Показ кнопки В корзину
	 *
	 * @return void
	 * @since      1.8.0
	 *
	 * @deprecated 3.0.0 New architecture.
	 *
	 */
	protected function show_button_add_to_card(): void {

		_deprecated_function( __METHOD__, '3.0.0' );

		if ( ! WP_DEBUG_LOG ) {
			return;
		}

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

		echo wp_kses( $enable_add_to_card, [ 'style' => [] ] );
	}

}
