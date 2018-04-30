<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
add_action( 'wpcf7_init', 'awooc_wpcf7_add_form_tag', 10 );
function awooc_wpcf7_add_form_tag() {
	wpcf7_add_form_tag( 'awooc_hidden', 'awooc_wpcf7_add_form_tag_callback', true );
}

function awooc_wpcf7_add_form_tag_callback( $tag ) {
	if ( empty( $tag->name ) ) {
		return '';
	}

	$atts = array();
	$class         = wpcf7_form_controls_class( $tag->type ) . ' awooc-hidden-data';
	$atts['class'] = apply_filters( 'awooc_class_hidden_field', $tag->get_class_option( $class ) );
	$atts['id']    = $tag->get_id_option();
	$value         = (string) reset( $tag->values );
	$value         = $tag->get_default_option( $value );
	$atts['value'] = $value;
	$atts['type'] = 'hidden';
	$atts['name'] = $tag->name;
	$atts         = wpcf7_format_atts( $atts );
	$html = sprintf(
		'<span class="wpcf7-form-control-wrap %1$s"><textarea %2$s></textarea></span>',
		sanitize_html_class( $tag->name ), $atts);
	$html .= sprintf('<input type="hidden" name="%1$s" value="" class="awooc-hidden-product-id">',
		'awooc_product_id');
	
	return $html;
}

add_filter( 'wpcf7_validate_awooc_hidden', 'awooc_fields_validation_filter', 10, 2 );
add_filter( 'wpcf7_validate_awooc_hidden*', 'awooc_fields_validation_filter', 10, 2 );
function awooc_fields_validation_filter( $result, $tag ) {
	$name = $tag->name;
	$value = isset( $_POST[ $name ] ) ? sanitize_text_field( $_POST[ $name ] ) : '';
	if ( $tag->is_required() && '' == $value ) {
		$result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
	}
	
	return $result;
}

add_action( 'wpcf7_admin_init', 'awooc_fields_add_tag_generator_address', 1 );
function awooc_fields_add_tag_generator_address() {
	$tag_generator = WPCF7_TagGenerator::get_instance();
	$tag_generator->add( 'awooc_hidden', 'AWOOC Скрытое поле', 'awooc_tag_generator_hidden' );
}

function awooc_tag_generator_hidden( $contact_form, $args = '' ) {
	$args = wp_parse_args( $args, array() );
	$type = 'awooc_hidden';
	?>
	<div class="control-box">
		<fieldset>
			<legend><?php esc_html_e( 'Генерация поля, в которое будет записываться нужные значения для отправки по почте', 'art-woo-order-one-click' ); ?></legend>
			
			<table class="form-table">
				<tbody>
				
				
				<tr>
					<th scope="row"><label for="<?php echo esc_attr( $args['content'] .
					                                                 '-name' ); ?>"><?php echo esc_html( __( 'Name', 'contact-form-7' ) ); ?></label>
					</th>
					<td><input type="text" name="name" class="tg-name oneline"
							id="<?php echo esc_attr( $args['content'] . '-name' ); ?>"/></td>
				</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	
	<div class="insert-box">
		<input type="text" name="<?php echo $type; ?>" class="tag code" readonly="readonly" onfocus="this.select()"/>
		
		<div class="submitbox">
			<input type="button" class="button button-primary insert-tag"
				value="<?php echo esc_attr( __( 'Insert Tag', 'contact-form-7' ) ); ?>"/>
		</div>
		
		<br class="clear"/>
		
		<p class="description mail-tag"><label for="<?php echo esc_attr( $args['content'] .
		                                                                 '-mailtag' ); ?>"><?php echo sprintf( esc_html( __( "To use the value input through this field in a mail field, you need to insert the corresponding mail-tag (%s) into the field on the Mail tab.", 'contact-form-7' ) ), '<strong><span class="mail-tag"></span></strong>' ); ?>
				<input type="text" class="mail-tag code hidden" readonly="readonly"
					id="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>"/></label></p>
	</div>
	<?php
}
