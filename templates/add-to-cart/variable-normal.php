<?php
/**
 * Single variation cart button in Normal mode
 *
 * This template can be overridden by copying it to yourtheme/art-woocommerce-order-one-click/add-to-cart/variable-normal.php.
 *
 * @see     https://wpruse.ru/my-plugins/art-woocommerce-order-one-click/
 * @package art-woocommerce-order-one-click/includes/view
 * @version 3.0.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

awooc()->get_front()->disable_loop();

?>
<div class="woocommerce-variation-add-to-cart variations_button">
	<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

	<?php
	do_action( 'woocommerce_before_add_to_cart_quantity' );

	woocommerce_quantity_input(
		[
			'min_value' => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
			'max_value' => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
			'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : $product->get_min_purchase_quantity(),
			// WPCS: CSRF ok, input var ok.
		]
	);

	do_action( 'woocommerce_after_add_to_cart_quantity' );
	?>

	<button type="submit"
	        class="single_add_to_cart_button button alt<?php echo esc_attr(
		        wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : ''
	        ); ?>"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>

	<?php awooc_html_custom_add_to_cart(); ?>

	<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>

	<input type="hidden" name="add-to-cart" value="<?php echo absint( $product->get_id() ); ?>"/>
	<input type="hidden" name="product_id" value="<?php echo absint( $product->get_id() ); ?>"/>
	<input type="hidden" name="variation_id" class="variation_id" value="0"/>
</div>
