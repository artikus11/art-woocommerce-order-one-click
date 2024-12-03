<?php
/**
 * Файл обработки заказов
 *
 * @see     https://wpruse.ru/my-plugins/art-woocommerce-order-one-click/
 * @package art-woocommerce-order-one-click/classes
 * @version 1.8.2
 */

namespace Art\AWOOC;

use WC_Data_Exception;
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

	/**
	 * @var false|mixed|null
	 */
	protected $has_create_order;


	public function init_hooks(): void {

		$this->has_create_order = get_option( 'woocommerce_awooc_created_order' );

		/**
		 * Хук wpcf7_before_send_mail используется для создания заказов, а не wpcf7_mail_sent, потому что только на этом хуке возможно изменять данные письма до его отправки. На хуке wpcf7_mail_sent ничего изменить не получиться, письмо уже ушло
		 */
		add_action( 'wpcf7_before_send_mail', [ $this, 'created_order_mail_send' ], 10, 3 );
	}


	/**
	 * Создание заказа при отправке письма
	 *
	 * @param  \WPCF7_ContactForm $contact_form
	 * @param  bool               $abort
	 * @param  \WPCF7_Submission  $submission
	 *
	 * @return void
	 * @since 2.2.6
	 * @since 1.5.0
	 */
	public function created_order_mail_send( WPCF7_ContactForm $contact_form, $abort, WPCF7_Submission $submission ): void {

		if ( 'yes' !== $this->has_create_order ) {
			return;
		}

		if ( $contact_form->id() !== $this->main->get_selected_form_id() ) {
			return;
		}

		$posted_data = $submission->get_posted_data();

		[ $posted_text, $posted_email, $posted_tel, $product_id, $product_qty, $customer_id ] = $this->prepare_posted_data( $posted_data );

		$address = apply_filters(
			'awooc_order_address_arg',
			[
				'first_name' => $posted_text,
				'email'      => $posted_email,
				'phone'      => $posted_tel,
			],
			$posted_data
		);

		$this->order = wc_create_order();

		do_action( 'awooc_after_created_order', $product_id, $this->order, $address, $product_qty );

		$this->add_order( (int) $product_id, (int) $product_qty, $address, (int) $customer_id );

		do_action( 'awooc_create_order', $this->order, $contact_form, $posted_data );

		do_action( 'awooc_after_mail_send', $product_id, $this->order->get_id() );
	}


	/**
	 * Добавление в заказ данных товара
	 *
	 * @param  int   $product_id  ID продкта.
	 * @param  int   $product_qty количество продукта.
	 * @param  array $address     адрес для заказа.
	 *
	 * @param  int   $customer_id
	 *
	 * @since 2.2.6
	 */
	public function add_order( int $product_id, int $product_qty, array $address, int $customer_id ): void {

		$this->order->add_product( wc_get_product( $product_id ), $product_qty );
		$this->order->set_address( $address, 'billing' );
		$this->order->set_address( $address, 'shipping' );

		if ( 0 !== $customer_id ) {
			try {
				$this->order->set_customer_id( $customer_id );
			} catch ( WC_Data_Exception $exception ) {
				wc_get_logger()->error(
					$exception->getMessage(),
					[
						'source'    => 'awooc',
						'backtrace' => true,
					]
				);
			}
		}

		$this->order->update_meta_data( '_awooc_order', true );
		$this->order->add_order_note( __( 'The order was created by using the One-click Order button', 'art-woocommerce-order-one-click' ) );
		$this->order->calculate_totals();
		$this->order->update_status( 'pending', __( 'One click order', 'art-woocommerce-order-one-click' ), true );
	}
}
