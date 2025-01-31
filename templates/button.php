<?php
/**
 * Button Template
 *
 * This template can be overridden by copying it to yourtheme/art-woocommerce-order-one-click/button.php.
 *
 * @see     https://wpruse.ru/my-plugins/art-woocommerce-order-one-click/
 * @package art-woocommerce-order-one-click/templates
 * @version 3.0.0
 *
 * @global $args
 */

do_action( 'awooc_before_button' );

?>
	<button
		type="button"
		data-value-product-id="<?php echo esc_attr( $args['product_id'] ); ?>"
		class="<?php echo esc_attr( $args['class'] ); ?>"
		id="<?php echo esc_attr( $args['id'] ); ?>"
		<?php do_action( 'awooc_attributes_button' ); ?>><?php echo esc_html( trim( $args['label'] ) ); ?></button>

<?php

do_action( 'awooc_after_button' );

