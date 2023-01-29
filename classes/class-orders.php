<?php
/**
 * Файл обработки заказов
 *
 * @see     https://wpruse.ru/my-plugins/art-woocommerce-order-one-click/
 * @package art-woocommerce-order-one-click/classes
 * @version 1.8.2
 */

namespace Art\AWOOC;

use WC_Order;
use WPCF7_ContactForm;
use WPCF7_Submission;

/**
 * Class AWOOC_Orders
 *
 * @author Artem Abramovich
 * @since  1.8.2
 */
class Orders {

	/**
	 * Выбранная форма из настроек
	 *
	 * @var string ID формы.
	 */
	public $select_form;


	/**
	 * Constructor.
	 *
	 * @since 1.8.0
	 */
	public function __construct() {

		$this->select_form = get_option( 'woocommerce_awooc_select_form' );
	}


	public function init_hooks(): void {

		add_action( 'wpcf7_before_send_mail', [ $this, 'created_order_mail_send' ], 10, 3 );
	}


	/**
	 * Создание заказа при отправке письма
	 *
	 * @param  WPCF7_ContactForm $contact_form объект формы.
	 *
	 * @since 1.5.0
	 * @since 2.2.6
	 */
	public function created_order_mail_send( WPCF7_ContactForm $contact_form, $abort, WPCF7_Submission $submission ): void {

		if ( 'yes' !== get_option( 'woocommerce_awooc_created_order' ) ) {
			return;
		}

		if ( $contact_form->id() !== (int) $this->select_form ) {
			return;
		}

		$posted_data = $submission->get_posted_data();
		$posted_data = array_map( [ $this, 'sanitize_field' ], $posted_data );

		$posted_text  = $posted_data['awooc-text'] ?? '';
		$posted_email = $posted_data['awooc-email'] ?? '';
		$posted_tel   = $posted_data['awooc-tel'] ?? '';

		$product_id  = $posted_data['awooc_product_id'] ?? 0;
		$product_qty = $posted_data['awooc_product_qty'] ?? 1;

		$address = apply_filters(
			'awooc_order_address_arg',
			[
				'first_name' => $posted_text,
				'email'      => $posted_email,
				'phone'      => $posted_tel,
			]
		);

		$order = wc_create_order();

		do_action( 'awooc_after_created_order', $product_id, $order, $address, $product_qty );

		$this->add_order( $order, $product_id, $product_qty, $address );

		$this->change_subject( $contact_form, $order );

		do_action( 'awooc_after_mail_send', $product_id, $order->get_id() );
	}


	/**
	 * Добавление в заказ данных товара
	 *
	 * @param  WC_Order $order       объект заказа.
	 * @param  int      $product_id  ID продкта.
	 * @param  int      $product_qty количество продукта.
	 * @param  array    $address     адрес для заказа.
	 *
	 *
	 * @since 2.2.6
	 */
	public function add_order( WC_Order $order, int $product_id, int $product_qty, array $address ): void {

		$order->add_product( wc_get_product( $product_id ), $product_qty );
		$order->set_address( $address, 'billing' );
		$order->set_address( $address, 'shipping' );
		$order->calculate_totals();
		$order->update_status( 'pending', __( 'One click order', 'art-woocommerce-order-one-click' ), true );
	}


	/**
	 * Изменение темы письма
	 *
	 * @param  WPCF7_ContactForm $contact_form объект формы.
	 * @param  WC_Order          $order        объект заказа.
	 *
	 * @since 2.2.6
	 */
	public function change_subject( WPCF7_ContactForm $contact_form, WC_Order $order ): void {

		if ( 'yes' !== get_option( 'woocommerce_awooc_change_subject' ) ) {
			return;
		}

		$mail = $contact_form->prop( 'mail' );

		$mail['subject'] = $mail['subject'] . ' №' . $order->get_order_number();

		$contact_form->set_properties( [ 'mail' => $mail ] );
	}


	/**
	 * @param $field
	 *
	 * @return string
	 */
	protected function sanitize_field( $field ): string {

		return sanitize_text_field( wp_unslash( $field ) );
	}

}
