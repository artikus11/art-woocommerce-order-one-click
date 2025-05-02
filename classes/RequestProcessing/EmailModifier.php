<?php
/**
 * Файл обработки писем
 *
 * @see     https://wpruse.ru/my-plugins/art-woocommerce-order-one-click/
 * @package art-woocommerce-order-one-click/classes
 * @version 1.8.0
 */

namespace Art\AWOOC\RequestProcessing;

use Art\AWOOC\RequestHandler;
use WC_Order;
use WPCF7_ContactForm;
use WPCF7_Submission;

/**
 * Class Email
 *
 * @author Artem Abramovich
 * @since  3.0.0
 */
class EmailModifier extends RequestHandler {

	public function init_hooks(): void {

		add_filter( 'wpcf7_form_hidden_fields', [ $this, 'add_hidden_fields' ], 100, 1 );

		add_action( 'awooc_create_order', [ $this, 'change_email_subject' ], 10, 3 );

		add_action( 'wpcf7_before_send_mail', [ $this, 'change_email_template' ], 20, 3 );
	}


	public function add_hidden_fields( $fields ): array {

		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$is_ajax_request = wp_doing_ajax() && isset( $_REQUEST['action'] ) && 'awooc_ajax_product_form' === $_REQUEST['action'];
		// phpcs:enable WordPress.Security.NonceVerification.Recommended
		if ( ! $is_ajax_request ) {
			return $fields;
		}

		$addon_fields = apply_filters(
			'awooc_added_hidden_fields',
			[
				'awooc-hidden-data' => '',
				'awooc_product_id'  => '',
				'awooc_product_qty' => '',
				'awooc_customer_id' => get_current_user_id(),
			]
		);

		if ( class_exists( 'Polylang' ) && ! defined( 'WP_CLI' ) ) {
			$addon_fields['lang'] = pll_current_language();
		}

		if ( defined( 'ICL_LANGUAGE_CODE' ) && ! defined( 'WP_CLI' ) ) {
			$addon_fields['lang'] = ICL_LANGUAGE_CODE;
		}

		return array_merge(
			$fields,
			$addon_fields
		);
	}


	/**
	 * Изменение темы письма
	 *
	 * @param  \WC_Order          $order        объект заказа.
	 * @param  \WPCF7_ContactForm $contact_form объект формы.
	 * @param  array              $posted_data  пересылаемые данные.
	 *
	 * @since 2.2.6
	 */
	public function change_email_subject( WC_Order $order, WPCF7_ContactForm $contact_form, $posted_data ): void {

		if ( 'yes' !== get_option( 'woocommerce_awooc_change_subject' ) ) {
			return;
		}

		$mail = $contact_form->prop( 'mail' );

		$mail['subject'] = $mail['subject'] . ' №' . $order->get_order_number();

		$contact_form->set_properties( [ 'mail' => $mail ] );
	}


	/**
	 * @param  \WPCF7_ContactForm $contact_form
	 * @param  bool               $abort
	 * @param  \WPCF7_Submission  $submission
	 *
	 * @return void
	 */
	public function change_email_template( WPCF7_ContactForm $contact_form, bool $abort, WPCF7_Submission $submission ): void {

		if ( 'yes' !== get_option( 'woocommerce_awooc_enable_letter_template' ) ) {
			return;
		}

		if ( $contact_form->id() !== $this->main->get_selected_form_id() ) {
			return;
		}

		// TODO: костыль, почему-то get_locale() всегда дефолтное значение возвращает
		switch_to_locale( apply_filters( 'awooc_letter_locale', get_option( 'WPLANG' ) ) );

		$mail_body = $submission->get_posted_data();

		[ $posted_text, $posted_email, $posted_tel ] = $this->prepare_posted_data( $mail_body );

		$mail = $contact_form->prop( 'mail' );

		ob_start();

		load_template(
			$this->main->get_template( 'email.php' ),
			true,
			apply_filters(
				'awooc_letter_args',
				[
					'letter_data'  => [
						'name'  => $posted_text,
						'email' => $posted_email,
						'phone' => $posted_tel,
					],
					'letter_meta'  => [
						'ip'   => [
							'label' => esc_html__( 'IP', 'art-woocommerce-order-one-click' ),
							'value' => $submission->get_meta( 'remote_ip' ),
						],
						'time' => [
							'label' => esc_html__( 'Date', 'art-woocommerce-order-one-click' ),
							'value' => $submission->get_meta( 'timestamp' ),
						],
						'url'  => [
							'label' => esc_html__( 'Domain', 'art-woocommerce-order-one-click' ),
							'value' => $submission->get_meta( 'url' ),
						],
					],
					'product_data' => $this->get_parse_mail_body( $mail_body['awooc-hidden-data'] ),
				],
				$this,
				$submission
			)
		);

		$mail['body'] = ob_get_clean();

		$contact_form->set_properties( [ 'mail' => $mail ] );
	}


	public function get_parse_mail_body( $text ): array {

		$result = [];

		$lines = array_filter( array_map( 'trim', explode( "\n", $text ) ) );

		unset( $lines[0], $lines[1] );

		foreach ( $lines as $line ) {

			$parts = explode( ':', $line, 2 );

			$key   = trim( $parts[0] );
			$value = trim( $parts[1] );

			if ( empty( $value ) ) {
				continue;
			}

			$value = html_entity_decode( preg_replace( '/\s+/', ' ', $value ) );

			$result[ $key ] = $value;
		}

		return $result;
	}
}
