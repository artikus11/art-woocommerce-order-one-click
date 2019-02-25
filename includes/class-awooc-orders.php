<?php

/**
 * Class AWOOC_Orders
 *
 * @author Artem Abramovich
 * @since  1.8.2
 */
class AWOOC_Orders {

	/**
	 * Constructor.
	 *
	 * @since 1.8.0
	 */
	public function __construct() {

		/**
		 * Contact Form 7 hooks
		 */
		add_action( 'wpcf7_mail_sent', array( $this, 'created_order_after_mail_send' ), 10, 1 );
	}


	/**
	 * Создание заказа при отправке письма
	 *
	 *
	 * @throws WC_Data_Exception
	 *
	 * @since 1.5.0
	 * @since 1.8.0
	 */
	public function created_order_after_mail_send() {

		if ( 'yes' !== get_option( 'woocommerce_awooc_created_order' ) ) {
			return;
		}

		$select_form = get_option( 'woocommerce_awooc_select_form' );

		// @codingStandardsIgnoreStart
		if ( $select_form !== sanitize_text_field( $_POST['_wpcf7'] ) ) {
			return;
		}

		$user_passed_text  = $_POST['awooc-text'] ? sanitize_text_field( $_POST['awooc-text'] ) : '';
		$user_passed_email = $_POST['awooc-email'] ? sanitize_text_field( $_POST['awooc-email'] ) : '';
		$user_passed_tel   = $_POST['awooc-tel'] ? sanitize_text_field( $_POST['awooc-tel'] ) : '';
		// @codingStandardsIgnoreEnd

		$product_id  = sanitize_text_field( $_POST['awooc_product_id'] );
		$product_qty = sanitize_text_field( $_POST['awooc_product_qty'] );

		$address = array(
			'first_name' => $user_passed_text,
			'email'      => $user_passed_email,
			'phone'      => $user_passed_tel,
		);

		$order = wc_create_order();

		/*if ( 'yes' !== get_option( 'woocommerce_awooc_send_email_customer' ) ) {// @codingStandardsIgnoreLine
			add_filter( 'woocommerce_email_enabled_customer_completed_order', '__return_false' );
		}*/

		$order->add_product( wc_get_product( $product_id ), $product_qty );
		$order->set_address( $address, 'billing' );
		$order->calculate_totals();
		$order->update_status( 'pending', __( 'One click order', 'art-woocommerce-order-one-click' ), true );

		do_action( 'awooc_after_mail_send', $product_id, $order->get_id() );
	}

}
