<?php
/**
 * Simple product add to cart in Special mode
 *
 * This template can be overridden by copying it to yourtheme/art-woocommerce-order-one-click/add-to-cart/simple-special.php.
 *
 * @see     https://wpruse.ru/my-plugins/art-woocommerce-order-one-click/
 * @package art-woocommerce-order-one-click/includes/view
 * @version 3.0.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

echo wc_get_stock_html( $product ); // WPCS: XSS ok.

if ( $product->is_on_backorder() || $product->is_in_stock() ) {
	awooc()->get_front()->disable_loop();
}

?>

<?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>

<form
	class="cart"
	action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>"
	method="post"
	enctype='multipart/form-data'>

	<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

	<?php
	do_action( 'woocommerce_before_add_to_cart_quantity' );

	woocommerce_quantity_input(
		[
			'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
			'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
			'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : $product->get_min_purchase_quantity(),
			// WPCS: CSRF ok, input var ok.
		]
	);

	do_action( 'woocommerce_after_add_to_cart_quantity' );
	?>

	<?php if ( $product->is_in_stock() && $product->is_purchasable() ): ?>
		<button type="submit"
		        name="add-to-cart"
		        value="<?php echo esc_attr( $product->get_id() ); ?>"
		        class="single_add_to_cart_button button alt<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>
	<?php endif; ?>

	<?php awooc_html_custom_add_to_cart(); ?>

	<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>

</form>

<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>


