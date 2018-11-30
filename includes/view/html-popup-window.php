<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

$elements = get_option( 'woocommerce_awooc_select_item' );

?>

<div id="awooc-form-custom-order" class="awooc-form-custom-order awooc-hide">
	<div class="awwoc-close">&#215;</div>
	<div class="awooc-custom-order-wrap">
		<?php

		/**
		 * Hook: awooc_popup_before_column
		 *
		 * @hooked awooc_popup_title - 10
		 */
		do_action( 'awooc_popup_before_column', $elements, $product );

		?>
		<div class="awooc-col columns-left">

			<?php

			/**
			 * Hook: awooc_popup_column_left
			 *
			 * @hooked awooc_popup_window_image - 10
			 * @hooked awooc_popup_window_price - 20
			 * @hooked awooc_popup_window_sku - 30
			 */
			do_action( 'awooc_popup_column_left', $elements, $product );

			?>

		</div>
		<div class="awooc-col columns-right">

			<?php

			/**
			 * Hook: awooc_popup_column_right
			 *
			 * @hooked awooc_popup_window_select_form - 20
			 */
			do_action( 'awooc_popup_column_right', $elements, $product );

			?>

		</div>

		<?php

		/**
		 * Hook: awooc_popup_before_column
		 *
		 */
		do_action( 'awooc_popup_after_column', $elements, $product );

		?>

	</div>
</div>
