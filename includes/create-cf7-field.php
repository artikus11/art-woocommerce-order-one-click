<?php
/**
 * Создание дополнительных кнопок для CF7
 *
 * @see     https://wpruse.ru/my-plugins/art-woocommerce-order-one-click/
 * @package art-woocommerce-order-one-click/includes/admin
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Contact Form 7 setup_hooks
 */
add_action( 'wpcf7_init', 'awooc_wpcf7_add_form_tag', 10 );
add_filter( 'wpcf7_validate_awooc_hidden', 'awooc_fields_validation_filter', 10, 2 );
add_filter( 'wpcf7_validate_awooc_hidden*', 'awooc_fields_validation_filter', 10, 2 );
add_action( 'wpcf7_admin_init', 'awooc_fields_add_tag_generator_address', 1 );
add_action( 'wpcf7_admin_init', 'awooc_fields_add_tag_generator_address', 1 );

/**
 * Подключаем калбек скрытого поля
 */
function awooc_wpcf7_add_form_tag() {

	wpcf7_add_form_tag( 'awooc_hidden', 'awooc_wpcf7_add_form_tag_callback', true );
}

/**
 * Добавление поля
 *
 * @param  WPCF7_FormTag $tag объект кнопкок.
 *
 * @return string
 */
function awooc_wpcf7_add_form_tag_callback( $tag ) {

	if ( empty( $tag->name ) ) {
		return '';
	}

	$atts          = array();
	$class         = wpcf7_form_controls_class( $tag->type ) . ' awooc-hidden-data';
	$atts['class'] = apply_filters( 'awooc_class_hidden_field', $tag->get_class_option( $class ) );
	$atts['id']    = $tag->get_id_option();
	$value         = (string) reset( $tag->values );
	$value         = $tag->get_default_option( $value );
	$atts['value'] = $value;
	$atts['type']  = 'hidden';
	$atts['name']  = $tag->name;
	$atts          = wpcf7_format_atts( $atts );
	$html          = sprintf( '<span class="wpcf7-form-control-wrap %1$s"><textarea %2$s></textarea></span>', sanitize_html_class( $tag->name ), $atts );

	$html .= sprintf( '<input type="hidden" name="%1$s" value="" class="awooc-hidden-product-id">', 'awooc_product_id' );
	$html .= sprintf( '<input type="hidden" name="%1$s" value="" class="awooc-hidden-product-qty">', 'awooc_product_qty' );

	return $html;
}

/**
 * Проверка формы
 *
 * @param  WPCF7_Validation $result результат валидации.
 * @param  WPCF7_FormTag    $tag    объект кнопкок.
 *
 * @return mixed
 */
function awooc_fields_validation_filter( $result, $tag ) {

	$name = $tag->name;

	// @codingStandardsIgnoreLine
	$value = isset( $_POST[ $name ] ) ? sanitize_text_field( $_POST[ $name ] ) : '';

	if ( $tag->is_required() && '' === $value ) {
		$result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
	}

	return $result;
}

/**
 * Подключаем калбек скрытого поля для сбора данных
 */
function awooc_fields_add_tag_generator_address() {

	$tag_generator = WPCF7_TagGenerator::get_instance();
	$tag_generator->add( 'awooc_hidden', __( 'AWOOC hide field', 'art-woocommerce-order-one-click' ), 'awooc_tag_generator_hidden' );
}

/**
 * Форма окна для заполнения поля
 */
function awooc_tag_generator_hidden() {

	$type = 'awooc_hidden';

	/* translators: %s: window description */
	$description = __( 'Generate a special hidden multi-line field. See %s.', 'art-woocommerce-order-one-click' );

	$desc_link = wpcf7_link(
		__( 'https://wpruse.ru/my-plugins/order-one-click/', 'art-woocommerce-order-one-click' ),
		__( 'the description for details', 'art-woocommerce-order-one-click' )
	);

	?>
	<div class="control-box" style="width: 100%;overflow: initial;">
		<fieldset>
			<legend>
				<?php echo sprintf( esc_html( $description ), wp_kses_post( $desc_link ) ); ?>
			</legend>

			<table class="form-table">
				<tbody>
				<tr>
					<th scope="row"><label for="tag-generator-panel-awooc_hidden-name"><?php echo esc_html( __( 'Name', 'contact-form-7' ) ); ?></label>
					</th>
					<td><input
							type="text"
							name="name"
							class="tg-name oneline"
							id="tag-generator-panel-awooc_hidden-name"/></td>
				</tr>
				</tbody>
			</table>
		</fieldset>
	</div>

	<div class="insert-box" style="overflow: initial;width: 99.5%;height: auto;">
		<label>
			<input
				style=" width: 370px;"
				type="text"
				name="<?php echo esc_attr( $type ); ?>"
				class="tag code"
				readonly="readonly"
				onfocus="this.select()"/>
		</label>

		<div class="submitbox">
			<input
				type="button"
				class="button button-primary insert-tag"
				value="<?php echo esc_attr( __( 'Insert Tag', 'contact-form-7' ) ); ?>"/>
		</div>

		<br class="clear"/>

		<p class="description mail-tag"><label
				for="tag-generator-panel-mailtag">
				<?php

				echo sprintf(
					/* translators: %s: field description */
					esc_html__(
						'To use the value input through this field in a mail field, you need to insert the corresponding mail-tag (%s) into the field on the Mail tab.',
						'contact-form-7'
					),
					'<strong><span class="mail-tag"></span></strong>'
				);
				?>
				<input
					type="text"
					class="mail-tag code hidden"
					readonly="readonly"
					id="tag-generator-panel-mailtag"/></label></p>
	</div>
	<?php
}
