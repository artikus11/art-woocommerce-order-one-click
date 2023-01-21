<?php
/**
 * Файл получения днныех о товаре в окне
 *
 * @see     https://wpruse.ru/my-plugins/art-woocommerce-order-one-click/
 * @package art-woocommerce-order-one-click/classes
 * @version 1.8.0
 */

namespace Art\AWOOC;

/**
 * Class Ajax
 *
 * @author Artem Abramovich
 * @since  1.8.0
 */
class Ajax {

	/**
	 * Переменная для сверки с настройками
	 *
	 * @since 1.8.0
	 *
	 * @var mixed|void
	 */
	public $elements;

	protected Main $main;


	public function __construct( Main $main ) {

		$this->main = $main;

	}


	public function init_hooks(): void {

		add_action( 'wp_ajax_nopriv_awooc_ajax_product_form', [ $this, 'ajax_callback' ] );
		add_action( 'wp_ajax_awooc_ajax_product_form', [ $this, 'ajax_callback' ] );

		add_action( 'wpcf7_before_send_mail', [ $this, 'email' ], 10, 3 );
	}


	/**
	 * Возвратна функция дл загрузки данных во всплывающем окне
	 */
	public function ajax_callback(): void {

		/**
		 * Если включено кеширование, то нонсу не проверяем.
		 */
		if ( false === defined( 'WP_CACHE' ) ) {
			check_ajax_referer( 'awooc-nonce', 'nonce' );
		}

		if ( empty( $_POST['id'] ) ) {
			wp_send_json_error(
				esc_html__(
					'Something is wrong with sending data. Unable to get product ID. Disable the output in the popup window or contact the developers of the plugin',
					'art-woocommerce-order-one-click'
				),
				403
			);
		}

		$product     = $this->get_product();
		$product_qty = $this->get_qty();

		$data = apply_filters(
			'awooc_data_ajax',
			[
				'elements'    => 'full',
				'toPopup'     => ( new Prepare_Popup( $this->main, $product, $product_qty ) )->get_response(),
				'toMail'      => ( new Prepare_Mail( $this->main, $product, $product_qty ) )->get_response(),
				'toAnalytics' => ( new Prepare_Analytics( $this->main, $product, $product_qty ) )->get_response(),
			],
			$product
		);

		if ( ! empty( $this->elements ) ) {
			$data['elements'] = 'empty';
		}

		wp_send_json_success( $data, 200 );
	}


	/**1643656254
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

		$product_id  = $mail_body['awooc_product_id'];
		$product_qty = $mail_body['awooc_product_qty'];

		$name  = ! empty( $mail_body['awooc-text'] ) ? sanitize_text_field( wp_unslash( $mail_body['awooc-text'] ) ) : '';
		$email = ! empty( $mail_body['awooc-email'] ) ? sanitize_text_field( wp_unslash( $mail_body['awooc-email'] ) ) : '';
		$tel   = ! empty( $mail_body['awooc-tel'] ) ? sanitize_text_field( wp_unslash( $mail_body['awooc-tel'] ) ) : '';

		$mail     = $contact_form->prop( 'mail' );
		$response = $this->response_to_mail( $product_id, $product_qty );

		ob_start();

		load_template(
			awooc()->templater->get_template( 'email.php' ),
			true,
			[
				'letter_data'  => [
					'name'  => $name,
					'email' => $email,
					'phone' => $tel,
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
	 * @param  int $id
	 *
	 * @return false|\WC_Product|null
	 */
	public function get_product( int $id = 0 ) {

		if ( ! empty( $_POST['id'] ) ) {
			$id = (int) $_POST['id'];
		}

		return wc_get_product( sanitize_text_field( wp_unslash( $id ) ) );
	}


	/**
	 * @param  int $qty
	 *
	 * @return int
	 */
	protected function get_qty( int $qty = 1 ): int {

		if ( ! empty( $_POST['qty'] ) ) {
			$qty = (int) sanitize_text_field( wp_unslash( $_POST['qty'] ) );
		}

		return $qty;
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

		return new Prepare_Mail($this->main, $product, $product_qty );
	}

}
