<?php

/**
 * Class AWOOC_Ajax
 *
 * @author Artem Abramovich
 * @since  1.8.0
 */
class AWOOC_Ajax {

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

		add_action( 'wp_ajax_nopriv_awooc_ajax_product_form', array( $this, 'ajax_scripts_callback' ) );
		add_action( 'wp_ajax_awooc_ajax_product_form', array( $this, 'ajax_scripts_callback' ) );
	}


	/**
	 * Возвратна функция дл загрузки данных во всплывающем окне
	 */
	public function ajax_scripts_callback() {

		/**
		 * Если включено кеширование, то нонсу не проверяем.
		 */
		if ( false === defined( 'WP_CACHE' ) ) {
			if ( ! wp_verify_nonce( $_POST['nonce'], 'awooc-nonce' ) ) {
				wp_die( esc_html__( 'Oops ... Data sent from unknown address', 'art-woocommerce-order-one-click' ) );
			}
		}

		if ( ! isset( $_POST['id'] ) || empty( $_POST['id'] ) ) {
			wp_die(
				esc_html__(
					'Something is wrong with sending data. Unable to get product ID. Disable the output in the popup window or contact the developers of the plugin',
					'art-woocommerce-order-one-click'
				)
			);
		}

		$product = wc_get_product( esc_attr( $_POST['id'] ) );

		$data = array(
			'elements'    => 'full',
			'title'       => $this->product_title( $product ),
			'image'       => $this->product_image( $product ),
			'link'        => esc_url( get_permalink( $this->product_id( $product ) ) ),
			'sku'         => $this->product_sku( $product ),
			'attr'        => $this->product_attr( $product ),
			'price'       => $this->product_price( $product ),
			'pricenumber' => $product->get_price(),
			'qty'         => '',
			'form'        => $this->select_form(),
			'cat'         => $this->product_cat( $product ),
		);

		// проверяем на включенный режим, если включен режим любой кроме шатного, то удаляем количество
		if ( 'dont_show_add_to_card' === get_option( 'woocommerce_awooc_mode_catalog' ) || 'in_stock_add_to_card' === get_option( 'woocommerce_awooc_mode_catalog' ) ) {
			unset( $data['qty'] );
		}

		if ( ! $product->get_price() ) {
			$data['qty']   = false;
			$data['price'] = false;
		}

		if ( empty( $this->elements ) || ! isset( $this->elements ) ) {
			$data['elements'] = 'empty';
		}

		wp_send_json( $data );

		wp_die();
	}


	/**
	 * Получение заголовка товара
	 *
	 * @param WC_Product $product
	 *
	 * @return bool|string
	 * @since 1.8.0
	 *
	 */
	public function product_title( $product ) {

		return $product->get_title();
	}


	/**
	 * Получаем изображение товара
	 *
	 * @param WC_Product $product
	 *
	 * @return bool|mixed|string
	 * @since 1.8.0
	 *
	 */
	public function product_image( $product ) {

		$image = '';

		$post_thumbnail_id = get_post_thumbnail_id( $product->get_id() );

		if ( ! $post_thumbnail_id ) {
			$post_thumbnail_id = get_post_thumbnail_id( $product->get_parent_id() );
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
				$product
			);
		}

		return $image;
	}


	/**
	 * Вспомогательная функция для проверки типа товара
	 *
	 * @param WC_Product $product
	 *
	 * @return mixed
	 * @since 1.8.0
	 *
	 */
	public function product_id( $product ) {

		if ( 'simple' === $product->get_type() ) {
			$product_id = $product->get_id();
		} else {
			$product_id = $product->get_parent_id();
		}

		return $product_id;
	}


	/**
	 * Получаем артикул товара
	 *
	 * @param WC_Product $product
	 *
	 * @return bool|mixed
	 * @since 1.8.0
	 *
	 */
	public function product_sku( $product ) {

		if ( ! wc_product_sku_enabled() && ( ! $product->get_sku() || ! $product->is_type( 'variable' ) ) ) {
			return false;
		}

		$sku = $product->get_sku() ? $product->get_sku() : 'N/A';

		return wp_kses_post(
			apply_filters(
				'awooc_popup_sku_html',
				sprintf(
					'<span class="awooc-sku-wrapper">%s</span><span class="awooc-sku">%s</span>',
					apply_filters( 'awooc_popup_sku_label', __( 'SKU: ', 'art-woocommerce-order-one-click' ) ),
					$sku
				),
				$product
			)
		);
	}


	/**
	 * Получение атрибутов вариативного товара
	 *
	 * @param WC_Product $product
	 *
	 * @return bool|string
	 * @since 1.8.0
	 *
	 */
	public function product_attr( $product ) {

		if ( $product->is_type( 'simple' ) ) {
			return false;
		}

		$attributes       = $product->get_attributes();
		$product_variable = new WC_Product_Variable( $product->get_parent_id() );
		$variations       = $product_variable->get_variation_attributes();
		$attr_name        = array();

		foreach ( $attributes as $attr => $value ) {

			$attr_label = wc_attribute_label( $attr );
			$meta       = get_post_meta( $product->get_id(), wc_variation_attribute_name( $attr ), true );
			$term       = get_term_by( 'slug', $meta, $attr );

			if ( false !== $term ) {
				$attr_name[] = $attr_label . ': ' . $term->name;
			}
		}

		if ( empty( $attr_name ) && isset( $variations ) ) {
			foreach ( $variations as $key => $item ) {

				$attr_name[] = wc_attribute_label( $key ) . ' &mdash; ' . implode( array_intersect( $item, $attributes ) );
			}
		}

		$allowed_html = array(
			'br'   => array(),
			'span' => array(),
		);

		$product_var_attr = wp_kses( implode( '; </span><span>', $attr_name ), $allowed_html );

		if ( ! isset( $variations ) ) {
			return false;
		}

		$attr_json = sprintf(
			'%s</br><span class="awooc-attr-wrapper"><span>%s</span></span>',
			apply_filters( 'awooc_popup_attr_label', esc_html__( 'Attributes: ', 'art-woocommerce-order-one-click' ) ),
			$product_var_attr
		);

		return $attr_json;

	}


	/**
	 * Получаем цену товара
	 *
	 * @param WC_Product $product
	 *
	 * @return bool|mixed
	 * @since 1.8.0
	 *
	 */
	public function product_price( $product ) {

		if ( ! $product->get_price() ) {
			return false;
		}

		return apply_filters(
			'awooc_popup_price_html',
			sprintf(
				'%s<span class="awooc-price-wrapper">%s</span></div>',
				apply_filters( 'awooc_popup_price_label', __( 'Price: ', 'art-woocommerce-order-one-click' ) ),
				wc_price( $product->get_price() )
			),
			$product
		);

	}


	/**
	 * Output form in a popup window
	 *
	 * @return bool|string
	 * @since 2.1.5
	 * @since 1.8.1
	 */
	public function select_form() {

		$select_form = apply_filters( 'awooc_selected_form_id', get_option( 'woocommerce_awooc_select_form' ) );

		if ( ! $select_form ) {
			return false;
		}

		$form = wpcf7_contact_form_tag_func( array( 'id' => esc_attr( $select_form ) ), null, 'contact-form-7' );

		if ( ! $form ) {
			return false;
		}

		return $form;
	}


	/**
	 * Получаем категории товара
	 *
	 * @param WC_Product $product
	 *
	 * @return string
	 * @since 2.1.0
	 *
	 */
	public function product_cat( $product ) {

		return wc_get_product_category_list(
			$this->product_id( $product ),
			', ',
			'<span class="posted_in">' . _n( 'Category:', 'Categories:', count( $product->get_category_ids() ), 'art-woocommerce-order-one-click' ) . ' ',
			'</span>'
		);

	}


	/**
	 * Получаем ссылку на товар
	 *
	 * @param WC_Product $product
	 *
	 * @return string
	 * @since 1.8.0
	 *
	 */
	public function product_link( $product ) {

		return sprintf(
			'<span class="awooc-form-custom-order-link awooc-hide">Ссылка на товар: %s</span>',
			esc_url( get_permalink( $this->product_id( $product ) ) )
		);

	}

}
