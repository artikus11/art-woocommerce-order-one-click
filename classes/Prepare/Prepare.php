<?php
/**
 * Файл обработки ответов
 *
 * @see     https://wpruse.ru/my-plugins/art-woocommerce-order-one-click/
 * @package art-woocommerce-order-one-click/classes
 * @version 3.0.0
 */

namespace Art\AWOOC\Prepare;

use Art\AWOOC\Main;
use WC_Product;
use WC_Product_Variable;

/**
 * Class Prepare
 *
 * @author Artem Abramovich
 * @since  3.0.0
 */
abstract class Prepare {

	/**
	 * @var \WC_Product
	 */
	protected WC_Product $product;


	/**
	 * @var int
	 */
	protected int $qty;


	protected Main $main;


	/**
	 * @var array|mixed
	 */
	protected $attr;


	/**
	 * @param  array $data
	 */
	public function __construct( array $data ) {

		$this->main    = $data['main'];
		$this->product = $data['product'];
		$this->qty     = $data['product_qty'];
		$this->attr    = empty( $data['attributes'] ) ? [] : $data['attributes'];
	}


	/**
	 * Метод обработки ответа
	 *
	 * @return array
	 * @since  3.0.0
	 */
	abstract public function get_response(): array;


	/**
	 * Объект товара
	 *
	 * @return \WC_Product
	 * @since  3.0.0
	 */
	public function get_product(): WC_Product {

		return $this->product;
	}


	/**
	 * Количество товара
	 *
	 * @return int
	 * @since  3.0.0
	 */
	public function get_qty(): int {

		return $this->qty;
	}


	/**
	 * ID товара
	 *
	 * @return int
	 * @since  3.0.0
	 */
	public function id(): int {

		return $this->product->get_id();
	}


	/**
	 * Получение родительского ID
	 *
	 * @return int
	 * @since  3.0.0
	 */
	public function parent_id(): int {

		$product_id = $this->product->get_parent_id();

		if ( $this->is_simple() ) {
			$product_id = $this->product->get_id();
		}

		return $product_id;
	}


	/**
	 * Цена товара
	 *
	 * @since  3.0.0
	 */
	public function price(): string {

		return $this->product->get_price();
	}


	/**
	 * Получаем артикул товара
	 *
	 * @return string
	 * @since  3.0.0
	 */
	public function sku(): string {

		$sku = $this->product->get_sku();

		if ( ! $sku ) {
			$sku = __( 'N/A', 'woocommerce' );
		}

		return $sku;
	}


	/**
	 * Получение заголовка товара
	 *
	 * @return string
	 * @since  3.0.0
	 */
	public function title(): string {

		return html_entity_decode( $this->product->get_name() );
	}


	/**
	 * Получаем изображение товара
	 *
	 * @return string
	 * @since  3.0.0
	 */
	public function image(): string {

		$image = '';

		$post_thumbnail_id = get_post_thumbnail_id( $this->id() );

		if ( ! $post_thumbnail_id ) {
			$post_thumbnail_id = get_post_thumbnail_id( $this->parent_id() );
		}

		if ( ! $post_thumbnail_id ) {
			$post_thumbnail_id = get_option( 'woocommerce_placeholder_image', 0 );
		}

		$full_size_image = wp_get_attachment_image_src( $post_thumbnail_id, apply_filters( 'awooc_thumbnail_name', 'shop_single' ) );

		if ( $full_size_image ) {
			$image = apply_filters(
				'awooc_popup_image_html',
				sprintf(
					'<img src="%s" alt="%s" class="%s" width="%s" height="%s">',
					esc_url( $full_size_image[0] ),
					apply_filters( 'awooc_popup_image_alt', '' ),
					apply_filters( 'awooc_popup_image_classes', esc_attr( 'awooc-form-custom-order-img' ) ),
					esc_attr( $full_size_image[1] ),
					esc_attr( $full_size_image[2] )
				),
				$this->product
			);
		}

		return $image;
	}


	/**
	 * Получение атрибутов вариативного товара
	 *
	 * @return string
	 * @since 3.0.0
	 */
	public function attributes(): string {

		if ( $this->is_simple() ) {
			return '';
		}

		return implode( '; ', $this->get_attributes_alt_method() );
	}


	/**
	 * Получение атрибутов, если не сработает получение из ядра WC
	 *
	 * @return array
	 * @since 2.4.0
	 * @since 3.0.0
	 */
	public function get_attributes_alt_method(): array {

		if ( empty( $this->attr ) ) {
			$attributes       = $this->product->get_attributes();
			$product_variable = new WC_Product_Variable( $this->parent_id() );
			$variations       = $product_variable->get_variation_attributes();
		} else {
			$attributes = $this->attr;
		}

		$attr_name = [];

		foreach ( $attributes as $attr => $value ) {

			$attr_label = wc_attribute_label( $attr, $this->product );
			$meta       = is_object( $value ) ? get_post_meta( $this->id(), wc_variation_attribute_name( $attr ), true ) : $value;
			$term       = get_term_by( 'slug', $meta, $attr );

			if ( false !== $term ) {
				$attr_name[] = sprintf( '%s: %s', $attr_label, $term->name );
			} elseif ( $value && ! is_object( $value ) ) {
				$attr_name[] = sprintf( '%s: %s', $attr_label, $value );
			}
		}

		if ( empty( $attr_name ) && isset( $variations ) ) {
			foreach ( $variations as $key => $item ) {

				$attr_name[] = sprintf( '%s &mdash; %s', wc_attribute_label( $key ), implode( array_intersect( $item, $attributes ) ) );
			}
		}

		return $attr_name;
	}


	/**
	 * Получаем сумму товара
	 *
	 * @return float|int
	 * @since 3.0.0
	 */
	public function get_sum() {

		if ( ! $this->price() ) {
			return 0;
		}

		return $this->price() * $this->qty;
	}


	/**
	 * Получаем ссылку
	 *
	 * @return string
	 * @since 3.0.0
	 */
	public function link(): string {

		return esc_url( get_permalink( $this->parent_id() ) );
	}


	/**
	 * Массив категорий товара
	 *
	 * @return array
	 * @since 3.0.0
	 */
	public function category_list(): array {

		$current_terms = get_the_terms( $this->parent_id(), 'product_cat' );

		$terms = [];

		if ( is_array( $current_terms ) ) {
			foreach ( $current_terms as $term ) {
				$terms[] = $term->name;
			}
		}

		return $terms;
	}


	/**
	 * Output form in a popup window
	 *
	 * @return string
	 * @since 3.0.0
	 */
	public function select_form(): string {

		$select_form = $this->main->get_selected_form_id();

		if ( ! $select_form ) {
			return '';
		}

		return wpcf7_contact_form_tag_func( [ 'id' => esc_attr( $select_form ) ], null, 'contact-form-7' );
	}


	/**
	 * Проверка на простой товар
	 *
	 * @return bool
	 */
	public function is_simple(): bool {

		return $this->product->is_type( 'simple' );
	}
}
