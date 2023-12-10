<?php
/**
 * Файл функций вывода окна и кнопки
 *
 * @see     https://wpruse.ru/my-plugins/art-woocommerce-order-one-click/
 * @package art-woocommerce-order-one-click/includes
 * @version 1.8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Модальное окно
 *
 * @see   awooc_popup_window_title()
 * @see   awooc_popup_window_image()
 * @see   awooc_popup_window_price()
 * @see   awooc_popup_window_sku()
 * @see   awooc_popup_window_sku()
 * @see   awooc_popup_window_attr()
 * @see   awooc_popup_window_qty()
 *
 * @since 1.8.0
 * @since 2.1.0
 */
add_action( 'awooc_popup_before_column', 'awooc_popup_window_title', 10, 2 );

add_action( 'awooc_popup_column_left', 'awooc_popup_window_image', 10, 2 );
add_action( 'awooc_popup_column_left', 'awooc_popup_window_sku', 20, 2 );
add_action( 'awooc_popup_column_left', 'awooc_popup_window_attr', 30, 2 );
add_action( 'awooc_popup_column_left', 'awooc_popup_window_price', 40, 2 );
add_action( 'awooc_popup_column_left', 'awooc_popup_window_qty', 50, 2 );
add_action( 'awooc_popup_column_left', 'awooc_popup_window_sum', 60, 2 );

add_action( 'awooc_popup_column_right', 'awooc_popup_window_form', 10 );

if ( ! function_exists( 'awooc_mode_classes' ) ) {
	/**
	 * Вспомогательная функция вывода класса в зависимости от режима работы
	 *
	 * @param  \WC_Product $product
	 *
	 * @return string[]
	 * @since 2.1.4
	 */
	function awooc_mode_classes( WC_Product $product ): array {

		$mode_classes = [
			'awooc-custom-order',
			'button',
			'alt',
			'awooc-button-js',
			'awooc-custom-order-button',
			'wp-element-button',
		];

		$mode = get_option( 'woocommerce_awooc_mode_catalog' );

		switch ( $mode ) {
			case 'dont_show_add_to_card':
				$mode_classes[] = 'dont-show-add-to-card';
				break;
			case 'show_add_to_card':
				$mode_classes[] = 'show-add-to-card';
				break;
			case 'in_stock_add_to_card':
				$mode_classes[] = 'in-stock-add-to-card';
				break;
			case 'no_stock_no_price':
				$mode_classes[] = 'no-stock-no-price';

				if ( 'onbackorder' === $product->get_stock_status() || 'outofstock' === $product->get_stock_status() ) {
					$mode_classes[] = 'no-margin';
				}

				break;
		}

		return apply_filters( 'awooc_mode_classes_button', $mode_classes );
	}
}

if ( ! function_exists( 'awooc_html_custom_add_to_cart' ) ) {

	/**
	 * Displaying the button add to card in product page
	 *
	 * @param  array|string    $args    массив параметров.
	 * @param  WC_Product|null $product объект продукта.
	 *
	 * @since 1.5.0
	 * @since 3.0.0
	 */
	function awooc_html_custom_add_to_cart( $args = [], WC_Product $product = null ) {

		if ( is_null( $product ) ) {
			$product = $GLOBALS['product'];
		}

		if ( 'yes' === $product->get_meta( '_awooc_button' ) ) {
			return;
		}

		if ( function_exists( 'rocket_init' ) && function_exists( 'wpcf7_enqueue_scripts' ) ) {
			wpcf7_enqueue_scripts();
		}

		if ( function_exists( 'rocket_init' ) && function_exists( 'wpcf7_enqueue_styles' ) ) {
			wpcf7_enqueue_styles();
		}


		wp_enqueue_script( 'awooc-scripts' );
		wp_enqueue_style( 'awooc-styles' );

		$defaults = [
			'product_id' => $product->get_id(),
			'class'      => apply_filters( 'awooc_classes_button', implode( ' ', awooc_mode_classes( $product ) ) ),
			'id'         => apply_filters( 'awooc_id_button', 'awooc-custom-order-button-' . $product->get_id() ),
			'label'      => apply_filters( 'awooc_button_label', awooc_custom_button_label( $product ) ),
			'loop'       => false,
		];

		$args = apply_filters(
			'awooc_button_args',
			wp_parse_args( $args, $defaults ),
			$product
		);

		load_template(
			awooc()->get_template( 'button.php' ),
			$args['loop'],
			[
				'product_id' => $args['product_id'],
				'class'      => $args['class'],
				'id'         => $args['id'],
				'label'      => $args['label'],
			]
		);
	}
}

if ( ! function_exists( 'awooc_popup' ) ) {
	/**
	 * Вывод всплывающего окна
	 *
	 * @since 3.0.0
	 */
	function awooc_popup() {

		$elements = get_option( 'woocommerce_awooc_select_item' );
		$product  = wc_get_product();

		if ( ! is_array( $elements ) ) {
			return null;
		}

		ob_start();

		load_template(
			awooc()->get_template( 'popup.php' ),
			true,
			[
				'elements' => $elements,
				'product'  => $product,
			]
		);

		return ob_get_clean();
	}
}

if ( ! function_exists( 'awooc_custom_button_label' ) ) {
	/**
	 * @param  \WC_Product|null $product
	 *
	 * @return string
	 */
	function awooc_custom_button_label( WC_Product $product = null ): string {

		if ( is_null( $product ) ) {
			$product = wc_get_product();
		}

		$label_button        = esc_html( get_option( 'woocommerce_awooc_title_button' ) );
		$custom_label_button = esc_html( get_option( 'woocommerce_awooc_title_custom' ) );

		if ( empty( $product ) ) {
			return $label_button;
		}

		if ( awooc()->get_mode()->is_mode_special() && ( ( $product->get_price() <= 0 || '' === $product->get_price() ) || ! $product->is_in_stock() ) ) {

			return $custom_label_button ? : $label_button;
		}

		return $label_button;
	}
}

if ( ! function_exists( 'awooc_popup_window_title' ) ) {
	/**
	 * Displaying the product header in a popup window
	 *
	 * @param  array       $elements массив настроек элементов окна.
	 * @param  \WC_Product $product  объект продукта.
	 *
	 * @todo  проблема с объектов товара всех страницах кроме страниц товара и архивов, если ставить тип, то выбивает фатал
	 *
	 * @since 1.5.0
	 * @since 1.8.10
	 */
	function awooc_popup_window_title( array $elements, $product ) {

		if ( in_array( 'title', $elements, true ) ) {
			echo wp_kses_post(
				apply_filters(
					'awooc_popup_title_html',
					sprintf(
						'<h2 class="%s"></h2>',
						esc_attr( 'awooc-form-custom-order-title awooc-popup-item awooc-popup-title skeleton-loader' )
					),
					$product
				)
			);
		}
	}
}

if ( ! function_exists( 'awooc_popup_window_image' ) ) {
	/**
	 * Output of a product thumbnail in a popup window
	 *
	 * @param  array $elements массив настроек элементов окна.
	 *
	 * @since 1.5.0
	 * @since 1.8.0
	 */
	function awooc_popup_window_image( array $elements ) {

		if ( in_array( 'image', $elements, true ) ) {

			echo '<div class="awooc-form-custom-order-img awooc-popup-item awooc-popup-image skeleton-loader"></div>';

		}
	}
}

if ( ! function_exists( 'awooc_popup_window_price' ) ) {
	/**
	 * Output of a product price in a popup window
	 *
	 * @param  array       $elements массив настроек элементов окна.
	 * @param  \WC_Product $product  объект продукта.
	 *
	 * @since 1.5.0
	 * @since 1.8.10
	 */
	function awooc_popup_window_price( array $elements, $product ) {

		if ( in_array( 'price', $elements, true ) ) {

			echo wp_kses_post(
				apply_filters(
					'awooc_popup_price_html',
					'<div class="awooc-form-custom-order-price awooc-popup-item awooc-popup-price skeleton-loader"></div>',
					$product
				)
			);

		}

	}
}

if ( ! function_exists( 'awooc_popup_window_sum' ) ) {
	/**
	 * Output of a product price in a popup window
	 *
	 * @param  array      $elements массив настроек элементов окна.
	 * @param  WC_Product $product  объект продукта.
	 *
	 * @since 1.5.0
	 * @since 2.4.0
	 */
	function awooc_popup_window_sum( array $elements, $product ) {

		if ( in_array( 'sum', $elements, true ) ) {

			echo wp_kses_post(
				apply_filters(
					'awooc_popup_price_html',
					'<div class="awooc-form-custom-order-sum awooc-popup-item awooc-popup-sum skeleton-loader"></div>',
					$product
				)
			);

		}

	}
}

if ( ! function_exists( 'awooc_popup_window_sku' ) ) {
	/**
	 * Output of a product sku in a popup window
	 *
	 * @param  array $elements массив настроек элементов окна.
	 *
	 * @since 1.5.0
	 * @since 1.5.0
	 */
	function awooc_popup_window_sku( array $elements ) {

		if ( in_array( 'sku', $elements, true ) ) {

			echo '<div class="awooc-form-custom-order-sku awooc-popup-item awooc-popup-sku skeleton-loader"></div>';

		}

	}
}

if ( ! function_exists( 'awooc_popup_window_qty' ) ) {
	/**
	 * Output of a product quantity in a popup window
	 *
	 * @param  array $elements массив настроек элементов окна.
	 *
	 * @since 2.1.0
	 */
	function awooc_popup_window_qty( array $elements ) {

		if ( in_array( 'qty', $elements, true ) ) {

			echo '<div class="awooc-form-custom-order-qty awooc-popup-item awooc-popup-qty skeleton-loader"></div>';

		}

	}
}

if ( ! function_exists( 'awooc_popup_window_attr' ) ) {
	/**
	 * Output of a product attributes in a popup window
	 *
	 * @param  array $elements массив настроек элементов окна.
	 *
	 * @since 1.5.0
	 * @since 1.8.9
	 */
	function awooc_popup_window_attr( array $elements ) {

		if ( in_array( 'attr', $elements, true ) ) {
			echo '<div class="awooc-form-custom-order-attr awooc-popup-item awooc-popup-attr skeleton-loader"></div>';
		}

	}
}

if ( ! function_exists( 'awooc_popup_window_form' ) ) {
	/**
	 * Output of a product attributes in a popup window
	 *
	 * @since 1.5.0
	 * @since 1.8.9
	 */
	function awooc_popup_window_form() {

		echo '<div class="awooc-form-custom-order-form awooc-popup-item awooc-popup-form skeleton-loader"></div>';
	}
}

if ( ! function_exists( 'awooc_popup_window_select_form' ) ) {
	/**
	 * Output form in a popup window
	 *
	 * @since 1.5.0
	 * @since 1.8.0
	 */
	function awooc_popup_window_select_form() {

		$select_form = awooc()->get_selected_form_id();

		if ( $select_form ) {
			do_action( 'awooc_popup_before_form' );

			if ( apply_filters( 'awooc_using_cf7', true ) ) {
				echo do_shortcode( '[contact-form-7 id="' . esc_attr( $select_form ) . '"]' );
			}

			do_action( 'awooc_popup_after_form' );
		}

	}
}
