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


	/**
	 * Constructor.
	 *
	 * @since 1.8.0
	 */
	public function __construct() {

		add_action( 'wp_ajax_nopriv_awooc_ajax_product_form', [ $this, 'ajax_scripts_callback' ] );
		add_action( 'wp_ajax_awooc_ajax_product_form', [ $this, 'ajax_scripts_callback' ] );
	}


	/**
	 * Возвратна функция дл загрузки данных во всплывающем окне
	 */
	public function ajax_scripts_callback(): void {

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
				), 403
			);

		}

		$product     = $this->get_product();
		$product_qty = $this->get_qty();

		$to_popup     = new Response_Popup( $product, $product_qty );
		$to_mail      = new Response_Mail( $product, $product_qty );
		$to_analytics = new Response_Analytics( $product, $product_qty );

		$data = apply_filters(
			'awooc_data_ajax',
			[
				'elements'     => 'full',
				'toPopup'     => $to_popup->get_response(),
				'toMail'      => $to_mail->get_response(),
				'toAnalytics' => $to_analytics->get_response(),
			],
			$product
		);

		if ( ! empty( $this->elements ) ) {
			$data['elements'] = 'empty';
		}

		wp_send_json_success( $data, 200 );
	}


	/**
	 * @return false|\WC_Product|null
	 */
	public function get_product() {

		return wc_get_product( sanitize_text_field( wp_unslash( $_POST['id'] ) ) );
	}


	/**
	 * @return int
	 */
	protected function get_qty(): int {

		return $_POST['qty'] ? (int) sanitize_text_field( wp_unslash( $_POST['qty'] ) ) : 1;
	}

}
