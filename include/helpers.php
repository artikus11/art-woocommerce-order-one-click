<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Показ кнопки В корзину
 */
function awooc_enable_add_to_card() {
	?>
	<style>
		.single_variation_wrap {
			display: block !important;
		}
		
		.single_add_to_cart_button,
		input.qty {
			display: inline-block !important;
		}
	</style>
	<?php
}

/**
 * Скрытие кнопки купить
 */
function awooc_disable_add_to_card() {
	?>
	<style>
		.single_variation_wrap,
		.single_add_to_cart_button,
		input.qty {
			display: none !important;
		}
	</style>
	<?php
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
	echo apply_filters( 'awooc_html_add_to_cart', sprintf( '<a href="%s" data-value-product-id="%s" class="%s">%s</a>', esc_url( '#awooc-form-custom-order' ), esc_attr( $product->get_id() ), esc_attr( 'awooc-custom-order button alt' ), esc_html( get_option( 'woocommerce_awooc_title_button' ) ) ), $product );
}