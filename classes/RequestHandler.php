<?php
/**
 * Файл получения днныех о товаре в окне
 *
 * @see     https://wpruse.ru/my-plugins/art-woocommerce-order-one-click/
 * @package art-woocommerce-order-one-click/classes
 * @version 1.8.0
 */

namespace Art\AWOOC;

use Art\AWOOC\Prepare\Analytics;
use Art\AWOOC\Prepare\Mail;
use Art\AWOOC\Prepare\Popup;
use WC_Product;

/**
 * Class Ajax
 *
 * @author Artem Abramovich
 * @since  1.8.0
 */
class RequestHandler {

	/**
	 * Переменная для сверки с настройками
	 *
	 * @since 1.8.0
	 *
	 * @var mixed|void
	 */
	public $elements;


	protected Main $main;


	protected int $product_id;


	protected int $product_qty;


	protected array $attributes;


	public function __construct( Main $main ) {

		$this->main = $main;

		$this->init_post_data();
	}


	protected function init_post_data(): void {

		// phpcs:disable WordPress.Security.NonceVerification.Missing
		$this->product_id  = sanitize_text_field( wp_unslash( $_POST['id'] ?? 0 ) );
		$this->product_qty = sanitize_text_field( wp_unslash( $_POST['quantity'] ?? 1 ) );
		$this->attributes  = $this->sanitize_attributes( map_deep( wp_unslash( $_POST['attributes'] ?? [] ), 'sanitize_text_field' ) );
		// phpcs:enable WordPress.Security.NonceVerification.Missing
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
		if ( ! defined( 'WP_CACHE' ) ) {
			check_ajax_referer( 'awooc-nonce', 'nonce' );
		}

		if ( empty( $this->product_id ) ) {
			wp_send_json_error(
				esc_html__(
					'Something is wrong with sending data. Unable to get product ID. Disable the output in the popup window or contact the developers of the plugin',
					'art-woocommerce-order-one-click'
				),
				403
			);
		}

		$product = $this->get_product();

		$send_data = $this->prepare_send_data( $product );

		$response_data = $this->prepare_response_data( $send_data, $product );

		wp_send_json_success( $response_data, 200 );
	}


	protected function prepare_send_data( WC_Product $product ): array {

		$send_data = [
			'main'        => $this->main,
			'product'     => $product,
			'product_qty' => $this->product_qty,
		];

		if ( ! empty( $this->attributes ) ) {
			$send_data['attributes'] = $this->attributes;
		}

		return $send_data;
	}


	protected function prepare_response_data( array $send_data, WC_Product $product ): array {

		$data = apply_filters(
			'awooc_data_ajax',
			[
				'elements'    => 'full',
				'productId'   => $product->get_id(),
				'productQty'  => $send_data['product_qty'],
				'toPopup'     => ( new Popup( $send_data ) )->get_response(),
				'toMail'      => ( new Mail( $send_data ) )->get_response(),
				'toAnalytics' => ( new Analytics( $send_data ) )->get_response(),
			],
			$product
		);

		if ( ! empty( $this->elements ) ) {
			$data['elements'] = 'empty';
		}

		return $data;
	}


	/**
	 *
	 * @param  int $id
	 *
	 * @return WC_Product
	 */
	public function get_product( int $id = 0 ): WC_Product {

		if ( ! empty( $this->product_id ) ) {
			$id = $this->product_id;
		}

		return wc_get_product( $id );
	}


	/**
	 * @param  int $qty
	 *
	 * @return int
	 */
	protected function get_qty( int $qty = 1 ): int {

		if ( ! empty( $this->product_qty ) ) {
			$qty = $this->product_qty;
		}

		return $qty;
	}


	/**
	 * @param  array $posted_data
	 *
	 * @return array
	 */
	protected function prepare_posted_data( array $posted_data ): array {

		$posted_data = map_deep( wp_unslash( $posted_data ), 'sanitize_text_field' );

		return [
			$posted_data['awooc-text'] ?? '',
			$posted_data['awooc-email'] ?? '',
			$posted_data['awooc-tel'] ?? '',
			(int) ( $posted_data['awooc_product_id'] ?? 0 ),
			(float) ( $posted_data['awooc_product_qty'] ?? 1 ),
			(int) ( $posted_data['awooc_customer_id'] ?? 0 ),
		];
	}


	/**
	 * @param  string $field
	 *
	 * @return string
	 */
	protected function sanitize_field( string $field ): string {

		return sanitize_text_field( wp_unslash( $field ) );
	}


	protected function sanitize_attributes( $attributes ): array {

		if ( empty( $attributes ) ) {
			return [];
		}

		$attr = [];

		foreach ( $attributes as $key => $val ) {
			$attr[ str_replace( 'attribute_', '', $key ) ] = sanitize_text_field( wp_unslash( $val ) );
		}

		return $attr;
	}
}
