<?php
/**
 * Product quantity inputs
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/global/quantity-input.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.4.0
 *
 * @global     $args
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $args ) ) {
	return;
}

$input_id     = $args['input_id'];
$input_type   = $args['type'];
$readonly     = $args['readonly'];
$classes      = $args['classes'];
$input_name   = $args['input_name'];
$input_value  = $args['input_value'];
$min_value    = $args['min_value'];
$max_value    = $args['max_value'];
$step         = $args['step'];
$placeholder  = $args['placeholder'];
$inputmode    = $args['inputmode'];
$autocomplete = $args['autocomplete'];

/* translators: %s: Quantity. */
$label = ! empty( $args['product_name'] ) ? sprintf( esc_html__( '%s quantity', 'woocommerce' ), wp_strip_all_tags( $args['product_name'] ) )
	: esc_html__( 'Quantity', 'woocommerce' );

?>
	<div class="quantity">
		<?php
		/**
		 * Hook to output something before the quantity input field.
		 *
		 * @since 7.2.0
		 */
		do_action( 'woocommerce_before_quantity_input_field' );
		?>
		<button type="button" class="awooc-popup-input-qty--minus">-</button>
		<label class="screen-reader-text" for="<?php echo esc_attr( $input_id ); ?>"><?php echo esc_attr( $label ); ?></label>
		<input
			type="<?php echo esc_attr( $input_type ); ?>"
			<?php echo $readonly ? 'readonly="readonly"' : ''; ?>
			id="<?php echo esc_attr( $input_id ); ?>"
			class="<?php echo esc_attr( join( ' ', (array) $classes ) ); ?>"
			name="<?php echo esc_attr( $input_name ); ?>"
			value="<?php echo esc_attr( $input_value ); ?>"
			aria-label="<?php esc_attr_e( 'Product quantity', 'woocommerce' ); ?>"
			<?php if ( in_array( $input_type, [ 'text', 'search', 'tel', 'url', 'email', 'password' ], true ) ) : ?>
				size="4"
			<?php endif; ?>
			min="<?php echo esc_attr( $min_value ); ?>"
			max="<?php echo esc_attr( 0 < $max_value ? $max_value : '' ); ?>"
			<?php if ( ! $readonly ) : ?>
				step="<?php echo esc_attr( $step ); ?>"
				placeholder="<?php echo esc_attr( $placeholder ); ?>"
				inputmode="<?php echo esc_attr( $inputmode ); ?>"
				autocomplete="<?php echo esc_attr( $autocomplete ?? 'on' ); ?>"
			<?php endif; ?>
		/>
		<button type="button" class="awooc-popup-input-qty--plus">+</button>
		<?php
		/**
		 * Hook to output something after quantity input field
		 *
		 * @since 3.6.0
		 */
		do_action( 'woocommerce_after_quantity_input_field' );
		?>
	</div>
<?php
