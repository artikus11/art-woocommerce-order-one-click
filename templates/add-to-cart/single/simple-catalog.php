<?php
/**
 * Simple product add to cart in Catalog mode
 *
 * This template can be overridden by copying it to yourtheme/art-woocommerce-order-one-click/add-to-cart/simple-catalog.php.
 *
 * @see     https://wpruse.ru/my-plugins/art-woocommerce-order-one-click/
 * @package art-woocommerce-order-one-click/includes/view
 * @version 3.0.0
 */

defined( 'ABSPATH' ) || exit;

awooc()->get_front()->disable_loop();
?>

<?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>

<form class="cart">
	<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

	<?php awooc_html_custom_add_to_cart(); ?>

	<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
</form>

<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>
