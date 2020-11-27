<?php
/**
 * Файл обработки заказов
 *
 * @see     https://wpruse.ru/my-plugins/art-woocommerce-order-one-click/
 * @package art-woocommerce-order-one-click/includes
 * @version 1.8.2
 */

/**
 * Class AWOOC_Orders
 *
 * @author Artem Abramovich
 * @since  1.8.2
 */
class AWOOC_Orders {

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

		/**
		 * Contact Form 7 setup_hooks
		 */
		add_action( 'wpcf7_before_send_mail', array( $this, 'created_order_mail_send' ), 10, 1 );
	}


	/**
	 * Создание заказа при отправке письма
	 *
	 * @param  WPCF7_ContactForm $contact_form объект формы.
	 *
	 * @throws WC_Data_Exception Exception.
	 * @since 1.5.0
	 * @since 2.2.6
	 */
	public function created_order_mail_send( $contact_form ) {

		if ( 'yes' !== get_option( 'woocommerce_awooc_created_order' ) ) {
			return;
		}

		// @codingStandardsIgnoreStart
		if ( ! isset( $_POST['_wpcf7'] ) || sanitize_text_field( wp_unslash( $_POST['_wpcf7'] ) ) !== $this->select_form ) {
			return;
		}

		$user_passed_text = '';
		$user_passed_email = '';
		$user_passed_tel = '';

		if ( isset( $_POST['awooc-text'] ) && ! empty( $_POST['awooc-text'] ) ) {
			$user_passed_text = sanitize_text_field( wp_unslash( $_POST['awooc-text'] ) );
		}

		if ( isset( $_POST['awooc-email'] ) && ! empty( $_POST['awooc-email'] ) ) {
			$user_passed_email = sanitize_text_field( wp_unslash( $_POST['awooc-email'] ) );
		}

		if ( isset( $_POST['awooc-tel'] ) && ! empty( $_POST['awooc-tel'] ) ) {
			$user_passed_tel = sanitize_text_field( wp_unslash( $_POST['awooc-tel'] ) );
		}

		$product_id  = isset( $_POST['awooc_product_id'] ) ? sanitize_text_field( wp_unslash( $_POST['awooc_product_id'] ) ) : null;
		$product_qty = isset( $_POST['awooc_product_qty'] ) ? sanitize_text_field( wp_unslash( $_POST['awooc_product_qty'] ) ) : null;
		// @codingStandardsIgnoreEnd

		$address = apply_filters(
			'awooc_order_address_arg',
			array(
				'first_name' => $user_passed_text,
				'email'      => $user_passed_email,
				'phone'      => $user_passed_tel,
			)
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
	 * @throws WC_Data_Exception Exception.
	 *
	 * @since 2.2.6
	 */
	public function add_order( $order, $product_id, $product_qty, $address ) {

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
	public function change_subject( $contact_form, $order ) {

		if ( 'yes' !== get_option( 'woocommerce_awooc_сhange_subject' ) ) {
			return;
		}

		$mail = $contact_form->prop( 'mail' );

		$mail['subject'] = $mail['subject'] . ' №' . $order->get_order_number();

		$contact_form->set_properties( array( 'mail' => $mail ) );

	}

}
