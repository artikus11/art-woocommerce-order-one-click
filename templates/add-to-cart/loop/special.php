<?php
/**
 * Loop Add to Cart. Simple product add to cart in Special mode
 *
 * This template can be overridden by copying it to yourtheme/art-woocommerce-order-one-click/add-to-cart/simple-catalog.php.
 *
 * @see         https://woocommerce.com/document/template-structure/
 * @see         https://wpruse.ru/my-plugins/art-woocommerce-order-one-click/
 * @package     art-woocommerce-order-one-click/templates
 * @version     3.1.0
 * @var $args
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

do_action( 'awooc_before_loop_add_to_cart_link', $product, $args );

if ('simple' === $product->get_type() || ($product->is_in_stock() && $product->get_price() > 0 )) :
	awooc_loop_add_to_cart_link( $product, $args );
endif;

if ( 'variable' !== $product->get_type() || class_exists( 'CFVSW\Plugin_Loader' ) ) :
	awooc_html_custom_add_to_cart();
endif;

do_action( 'awooc_after_loop_add_to_cart_link', $product, $args );
