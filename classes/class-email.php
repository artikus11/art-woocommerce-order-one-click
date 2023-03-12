<?php
/**
 * Файл обработки писем
 *
 * @see     https://wpruse.ru/my-plugins/art-woocommerce-order-one-click/
 * @package art-woocommerce-order-one-click/classes
 * @version 1.8.0
 */

namespace Art\AWOOC;

use WC_Order;
use WPCF7_ContactForm;

/**
 * Class Email
 *
 * @author Artem Abramovich
 * @since  3.0.0
 */
class Email extends Ajax {

	public function init_hooks(): void {

		add_filter( 'wpcf7_form_hidden_fields', [ $this, 'add_hidden_fields' ], 100, 1 );
		add_action( 'awooc_create_order', [ $this, 'change_subject' ], 100, 2 );
		add_action( 'wpcf7_before_send_mail', [ $this, 'email' ], 10, 3 );
	}


	public function add_hidden_fields( $fields ): array {

		$form_id = WPCF7_ContactForm::get_current()->id();

		if ( (int) get_option( 'woocommerce_awooc_select_form' ) !== $form_id ) {
			return $fields;
		}

		return array_merge(
			$fields,
			[
				'awooc-hidden-data' => '',
				'awooc_product_id'  => '',
				'awooc_product_qty' => '',
				'awooc_customer_id' => get_current_user_id(),
			]
		);
	}


	/**
	 * Изменение темы письма
	 *
	 * @param  WPCF7_ContactForm $contact_form объект формы.
	 * @param  WC_Order          $order        объект заказа.
	 *
	 * @since 2.2.6
	 */
	public function change_subject( WC_Order $order, WPCF7_ContactForm $contact_form ): void {

		if ( 'yes' !== get_option( 'woocommerce_awooc_change_subject' ) ) {
			return;
		}

		$mail = $contact_form->prop( 'mail' );

		$mail['subject'] = $mail['subject'] . ' №' . $order->get_order_number();

		$contact_form->set_properties( [ 'mail' => $mail ] );
	}


	/**
	 * @param $contact_form
	 * @param $abort
	 * @param $submission
	 *
	 * @return void
	 */
	public function email( $contact_form, $abort, $submission ): void {

		if ( 'yes' !== get_option( 'woocommerce_awooc_enable_letter_template' ) ) {
			return;
		}

		if ( (int) $contact_form->id() !== (int) get_option( 'woocommerce_awooc_select_form' ) ) {
			return;
		}

		$mail_body = $submission->get_posted_data();

		[ $posted_text, $posted_email, $posted_tel, $product_id, $product_qty ] = $this->prepare_posted_data( $mail_body );

		$mail     = $contact_form->prop( 'mail' );
		$response = $this->response_to_mail( (int) $product_id, (int) $product_qty );

		ob_start();

		load_template(
			$this->main->get_template( 'email.php' ),
			true,
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
				'product_data' => $response->get_response(),
			]
		);

		$mail['body'] = ob_get_clean();

		$contact_form->set_properties( [ 'mail' => $mail ] );

	}


	/**
	 * @param $product_id
	 * @param $product_qty
	 *
	 * @return \Art\AWOOC\Prepare_Mail
	 */
	public function response_to_mail( $product_id, $product_qty ): Prepare_Mail {

		$product     = $this->get_product( $product_id );
		$product_qty = $this->get_qty( $product_qty );

		return new Prepare_Mail( $this->main, $product, $product_qty );
	}

}