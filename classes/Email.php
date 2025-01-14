<?php
/**
 * Файл обработки писем
 *
 * @see     https://wpruse.ru/my-plugins/art-woocommerce-order-one-click/
 * @package art-woocommerce-order-one-click/classes
 * @version 1.8.0
 */

namespace Art\AWOOC;

use Art\AWOOC\Prepare\Mail;
use WC_Order;
use WPCF7_ContactForm;
use WPCF7_Submission;

/**
 * Class Email
 *
 * @author Artem Abramovich
 * @since  3.0.0
 */
class Email extends Ajax {

	public function init_hooks(): void {

		add_filter( 'wpcf7_form_hidden_fields', [ $this, 'add_hidden_fields' ], 100, 1 );

		add_action( 'awooc_create_order', [ $this, 'change_email_subject' ], 10, 3 );

		add_action( 'wpcf7_before_send_mail', [ $this, 'change_email_template' ], 20, 3 );
	}


	public function add_hidden_fields( $fields ): array {

		$form_id     = WPCF7_ContactForm::get_current()->id();
		$select_form = $this->main->get_selected_form_id();

		if ( $select_form !== $form_id ) {
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
	 * @todo не переводятся строки в письме, разобраться почему
	 */
	public function change_email_template( WPCF7_ContactForm $contact_form, bool $abort, WPCF7_Submission $submission ): void {

		if ( 'yes' !== get_option( 'woocommerce_awooc_enable_letter_template' ) ) {
			return;
		}

		if ( (int) $contact_form->id() !== $this->main->get_selected_form_id() ) {
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
	 * @param  int $product_id
	 * @param  int $product_qty
	 *
	 * @return \Art\AWOOC\Prepare\Mail
	 */
	public function response_to_mail( int $product_id, int $product_qty ): Mail {

		return new Mail( [
			'main'        => $this->main,
			'product'     => $this->get_product( $product_id ),
			'product_qty' => $this->get_qty( $product_qty ),
		] );
	}
}
