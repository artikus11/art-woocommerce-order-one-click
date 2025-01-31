<?php
/**
 * Popup window Template
 *
 * This template can be overridden by copying it to yourtheme/art-woocommerce-order-one-click/popup.php.
 *
 * @see     https://wpruse.ru/my-plugins/art-woocommerce-order-one-click/
 * @package art-woocommerce-order-one-click/templates
 * @version 3.0.0
 *
 * @global $args
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$product  = $args['product'];
$elements = $args['elements'] ? : [];

if ( is_null( $product ) ) {
	$product = wc_get_product();
}

?>

<div id="awooc-form-custom-order" class="awooc-form-custom-order awooc-popup-wrapper">
	<div class="awooc-close">&#215;</div>
	<div class="awooc-custom-order-wrap awooc-popup-inner">
		<?php

		/**
		 * Hook: awooc_popup_before_column
		 *
		 * @hooked awooc_popup_title - 10
		 */
		do_action( 'awooc_popup_before_column', $elements, $product );

		?>
		<div class="awooc-col-wrap awooc-row">
			<div class="awooc-col columns-left <?php awooc_class_full( $elements ); ?>">

				<?php

				/**
				 * Hook: awooc_popup_column_left
				 *
				 * @hooked awooc_popup_window_image - 10
				 * @hooked awooc_popup_window_price - 20
				 * @hooked awooc_popup_window_sku - 30
				 * @hooked awooc_popup_window_attr - 40
				 * @hooked awooc_popup_window_qty - 50
				 */
				do_action( 'awooc_popup_column_left', $elements, $product );

				?>

			</div>
			<div class="awooc-col columns-right <?php awooc_class_full( $elements ); ?>">

				<?php

				/**
				 * Hook: awooc_popup_column_right
				 */
				do_action( 'awooc_popup_column_right', $elements, $product );

				?>

			</div>
		</div>
		<?php

		/**
		 * Hook: awooc_popup_before_column
		 */
		do_action( 'awooc_popup_after_column', $elements, $product );

		?>

	</div>
</div>
