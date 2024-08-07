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


	/**
	 * @var \WC_Order|\WP_Error
	 */
	public $order;


	public function __construct( Main $main ) {

		$this->main = $main;
	}


	public function init_hooks(): void {

		add_action( 'wp_ajax_nopriv_awooc_ajax_product_form', [ $this, 'ajax_callback' ] );
		add_action( 'wp_ajax_awooc_ajax_product_form', [ $this, 'ajax_callback' ] );
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

		$send_data = [
			'main'        => $this->main,
			'product'     => $product,
			'product_qty' => $product_qty,
		];

		if ( ! empty( $_POST['attributes'] ) ) {
			$send_data['attributes'] = $this->sanitize_attribute();
		}

		$data = apply_filters(
			'awooc_data_ajax',
			[
				'elements'    => 'full',
				'toPopup'     => ( new Prepare_Popup( $send_data ) )->get_response(),
				'toMail'      => ( new Prepare_Mail( $send_data ) )->get_response(),
				'toAnalytics' => ( new Prepare_Analytics( $send_data ) )->get_response(),
			],
			$product
		);

		if ( ! empty( $this->elements ) ) {
			$data['elements'] = 'empty';
		}

		wp_send_json_success( $data, 200 );
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

		if ( ! empty( $_POST['quantity'] ) ) {
			$qty = (int) sanitize_text_field( wp_unslash( $_POST['quantity'] ) );
		}

		return $qty;
	}


	/**
	 * @param $posted_data
	 *
	 * @return array
	 */
	protected function prepare_posted_data( $posted_data ): array {

		$posted_data = array_map( [ $this, 'sanitize_field' ], $posted_data );

		$posted_text  = $posted_data['awooc-text'] ?? '';
		$posted_email = $posted_data['awooc-email'] ?? '';
		$posted_tel   = $posted_data['awooc-tel'] ?? '';

		$product_id  = $posted_data['awooc_product_id'] ?? 0;
		$product_qty = $posted_data['awooc_product_qty'] ?? 1;
		$customer_id = $posted_data['awooc_customer_id'] ?? 0;

		return [ $posted_text, $posted_email, $posted_tel, $product_id, $product_qty, $customer_id ];
	}


	/**
	 * @param $field
	 *
	 * @return string
	 */
	protected function sanitize_field( $field ): string {

		return sanitize_text_field( wp_unslash( $field ) );
	}


	protected function sanitize_attribute(): array {

		$attributes = $_POST['attributes'];

		if ( empty( $attributes ) ) {
			return [];
		}

		$attr = [];

		foreach ( $attributes as $key => $val ) {
			$attr[ str_replace( 'attribute_', '', $key ) ] = $this->sanitize_field( $val );
		}

		return $attr;
	}
}
