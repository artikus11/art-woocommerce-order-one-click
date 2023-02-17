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
class Orders extends Ajax {



	public function init_hooks(): void {

		add_action( 'wpcf7_mail_sent', [ $this, 'created_order_mail_send' ], 10, 1 );
	}


	/**
	 * Создание заказа при отправке письма
	 *
	 * @param  WPCF7_ContactForm $contact_form объект формы.
	 *
	 * @since 1.5.0
	 * @since 2.2.6
	 */
	public function created_order_mail_send( WPCF7_ContactForm $contact_form ): void {

		if ( 'yes' !== get_option( 'woocommerce_awooc_created_order' ) ) {
			return;
		}

		if ( $contact_form->id() !== (int) get_option( 'woocommerce_awooc_select_form' ) ) {
			return;
		}

		$posted_data = WPCF7_Submission::get_instance()->get_posted_data();

		[ $posted_data, $posted_text, $posted_email, $posted_tel, $product_id, $product_qty ] = $this->posted_data( $posted_data );

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

		$this->add_order( $order, (int) $product_id, (int) $product_qty, $address );

		do_action( 'awooc_create_order', $order, $contact_form, $posted_data );

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
		$order->add_order_note( __( 'The order was created by using the One-click Order button', 'art-woocommerce-order-one-click' ) );
		$order->calculate_totals();
		$order->update_status( 'pending', __( 'One click order', 'art-woocommerce-order-one-click' ), true );
	}

}
