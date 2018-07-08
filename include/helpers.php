<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Показ кнопки В корзину
 *
 * @return mixed|void
 */
function awooc_enable_add_to_card() {
	ob_start();
	?>
	<style>
		.woocommerce-variation-add-to-cart,
		.single_add_to_cart_button,
		input.qty {
			display: inline-block !important;
		}
	</style>
	<?php
	$enable_add_to_card = apply_filters( 'awooc_enable_add_to_card_style', ob_get_clean() );
	echo $enable_add_to_card;
}

/**
 * Скрытие кнопки купить
 *
 * @return mixed|void
 */
function awooc_disable_add_to_card() {
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
	</style>
	<?php
	$disable_add_to_card = apply_filters( 'awooc_disable_add_to_card_style', ob_get_clean() );
	echo $disable_add_to_card;
}

/**
 *  Замена урл на кнопках в похожих товарах на страницах товарах
 *
 * @param $url
 *
 * @return string
 */
function awooc_disable_url_add_to_cart_to_related( $url ) {
	global $product;
	if ( is_product() ) {
		$url = get_permalink( $product->get_id() );
	}
	
	return $url;
}

/**
 * Замена текста на кнопках в похожих товарах на страницах товарах
 *
 * @param $text
 *
 * @return string
 */
function awooc_disable_text_add_to_cart_to_related( $text ) {
	if ( is_product() ) {
		$text = __( 'Read more', 'woocommerce' );
	}
	
	return $text;
}

/**
 * Вывод html кнопки Заказать
 */
function awooc_html_custom_add_to_cart() {
	global $product;
	echo apply_filters( 'awooc_html_add_to_cart', sprintf( '<a href="%s" data-value-product-id="%s" class="%s">%s</a>', esc_url( '#awooc-form-custom-order' ), esc_attr( $product->get_id() ), apply_filters( 'awooc_classes_button', esc_attr( 'awooc-custom-order button alt' ) ), esc_html( get_option( 'woocommerce_awooc_title_button' ) ) ), $product );
}