<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
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
 * @see   awooc_popup_window_select_form()
 *
 * @since 1.8.0
 */
add_action( 'awooc_popup_before_column', 'awooc_popup_window_title', 10, 2 );

add_action( 'awooc_popup_column_left', 'awooc_popup_window_image', 10, 2 );
add_action( 'awooc_popup_column_left', 'awooc_popup_window_price', 20, 2 );
add_action( 'awooc_popup_column_left', 'awooc_popup_window_sku', 30, 2 );
add_action( 'awooc_popup_column_left', 'awooc_popup_window_attr', 40, 2 );

add_action( 'awooc_popup_column_right', 'awooc_popup_window_select_form', 20 );


if ( ! function_exists( 'awooc_html_custom_add_to_cart' ) ) {

	/**
	 * Displaying the button add to card in product page
	 *
	 * @since 1.5.0
	 * @since 1.8.0
	 *
	 * @param array $args
	 * @param null  $product
	 *
	 * @todo  сделать выключение кнопки в Быстром просмотре
	 */
	function awooc_html_custom_add_to_cart( $args = array(), $product = null ) {

		if ( is_null( $product ) ) {
			$product = $GLOBALS['product'];
		}

		$defaults = array(
			'href'       => '#awooc-form-custom-order',
			'product_id' => $product->get_id(),
			'class'      => apply_filters( 'awooc_classes_button', 'awooc-custom-order button alt' ),
			'id'         => apply_filters( 'awooc_id_button', 'awooc-custom-order-bnt' ),
			'label'      => get_option( 'woocommerce_awooc_title_button' ),
		);

		$args = apply_filters( 'awooc_button_args', wp_parse_args( $args, $defaults ), $product );

		ob_start();

		do_action( 'awooc_before_button' );

		?>
		<a
			href="<?php echo esc_url( $args['href'] ); ?>"
			data-value-product-id="<?php echo esc_attr( $args['product_id'] ); ?>"
			class="<?php echo esc_attr( $args['class'] ); ?>"
			id="<?php echo esc_attr( $args['id'] ); ?>"
			<?php do_action( 'awooc_attributes_button' ); ?>>
			<?php echo esc_html( $args['label'] ); ?>
		</a>

		<?php

		do_action( 'awooc_after_button' );

		if ( is_product() ) {
			echo apply_filters( 'awooc_html_add_to_cart', ob_get_clean() );// WPCS: XSS ok.
		}
	}
}

if ( ! function_exists( 'awooc_popup_window_title' ) ) {
	/**
	 * Displaying the product header in a popup window
	 *
	 * @param $elements
	 * @param $product
	 *
	 * @since 1.5.0
	 * @since 1.8.0
	 */
	function awooc_popup_window_title( $elements, $product ) {

		if ( in_array( 'title', $elements, true ) ) {
			echo apply_filters(// WPCS: XSS ok.
				'awooc_popup_title_html',
				sprintf(
					'<h2 class="%s"></h2>',
					esc_attr( 'awooc-form-custom-order-title' )
				),
				$product
			);
		}
	}
}

if ( ! function_exists( 'awooc_popup_window_image' ) ) {
	/**
	 * Output of a product thumbnail in a popup window
	 *
	 * @param $elements
	 *
	 * @since 1.5.0
	 * @since 1.8.0
	 */
	function awooc_popup_window_image( $elements ) {

		if ( in_array( 'image', $elements, true ) ) {

			echo '<div class="awooc-form-custom-order-img"></div>';

		}
	}
}

if ( ! function_exists( 'awooc_popup_window_price' ) ) {
	/**
	 * Output of a product price in a popup window
	 *
	 * @param $elements
	 * @param $product
	 *
	 * @since 1.5.0
	 * @since 1.8.0
	 */
	function awooc_popup_window_price( $elements, $product ) {

		if ( in_array( 'price', $elements, true ) ) {

			echo apply_filters(// WPCS: XSS ok.
				'awooc_popup_price_html',
				'<div class="awooc-form-custom-order-price"></div>',
				$product
			);

		}

	}
}

if ( ! function_exists( 'awooc_popup_window_sku' ) ) {
	/**
	 * Output of a product sku in a popup window
	 *
	 * @param $elements
	 *
	 * @since 1.5.0
	 * @since 1.5.0
	 */
	function awooc_popup_window_sku( $elements ) {

		if ( in_array( 'sku', $elements, true ) ) {

			echo '<div class="awooc-form-custom-order-sku"></div>';

		}

	}
}

if ( ! function_exists( 'awooc_popup_window_attr' ) ) {
	/**
	 * Output of a product attributes in a popup window
	 *
	 * @param $elements
	 * @param $product
	 *
	 * @since 1.5.0
	 * @since 1.8.0
	 */
	function awooc_popup_window_attr( $elements, $product ) {

		if ( in_array( 'attr', $elements, true ) ) {
			if ( $product->is_type( 'variable' ) ) {

				echo '<div class="awooc-form-custom-order-attr"></div>';

			}
		}

	}
}

if ( ! function_exists( 'awooc_popup_window_select_form' ) ) {
	/**
	 * Output form in a popup window
	 *
	 * @since 1.5.0
	 * @since 1.8.0
	 *
	 * @todo  Сделать загруку форму через ajax для предотврщения дублирования
	 */
	function awooc_popup_window_select_form() {

		$select_form = get_option( 'woocommerce_awooc_select_form' );
		if ( $select_form ) {
			do_action( 'awooc_popup_before_form' );

			if ( apply_filters( 'awooc_using_cf7', true ) ) {
				echo do_shortcode( '[contact-form-7 id="' . esc_attr( $select_form ) . '"]' );
			}

			do_action( 'awooc_popup_after_form' );
		}

	}
}
