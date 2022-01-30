<?php

namespace Art\AWOOC;

class Response_Popup extends Response {

	public function get_response(): array {

		$data = [
			'title' => $this->title(),
			'image' => $this->image(),
			'price' => $this->formatted_price(),
			'sku'   => $this->formatted_sku(),
			'attr'  => $this->formatted_attr(),
			'qty'   => $this->get_qty(),
			'sum'   => $this->formatted_sum(),
			'form'  => $this->select_form(),
		];

		if ( awooc()->conditional->is_mode_catalog() || awooc()->conditional->is_mode_preorder() ) {
			unset( $data['qty'] );
		}

		return $data;
	}


	/**
	 * Получаем цену товара
	 *
	 * @since 3.0.0
	 */
	protected function formatted_price() {

		if ( ! $this->price() ) {
			return '';
		}

		return apply_filters(
			'awooc_popup_price_html',
			sprintf(
				'%s<span class="awooc-price-wrapper">%s</span></div>',
				apply_filters( 'awooc_popup_price_label', __( 'Price: ', 'art-woocommerce-order-one-click' ) ),
				wc_price( $this->price() )
			),
			$this->get_product()
		);

	}


	/**
	 * Форматирование артикула
	 *
	 *
	 * @return string
	 * @since 2.3.2
	 */
	protected function formatted_sku(): string {

		return wp_kses_post(
			apply_filters(
				'awooc_popup_sku_html',
				sprintf(
					'<span class="awooc-sku-wrapper">%s</span><span class="awooc-sku">%s</span>',
					apply_filters( 'awooc_popup_sku_label', __( 'SKU: ', 'art-woocommerce-order-one-click' ) ),
					$this->sku()
				),
				$this->get_product()
			)
		);
	}


	/**
	 * Форматирование атрибутов вариативного товара
	 *
	 *
	 * @return string
	 * @since 2.3.2
	 */
	protected function formatted_attr(): string {

		if ( $this->is_simple() ) {
			return '';
		}

		return sprintf(
			'%s</br><span class="awooc-attr-wrapper"><span>%s</span></span>',
			apply_filters( 'awooc_popup_attr_label', esc_html__( 'Attributes: ', 'art-woocommerce-order-one-click' ) ),
			implode( '; </span><span>', $this->get_attributes_alt_method() ),

		);
	}


	/**
	 * Получаем сумму товара
	 *
	 * @since 3.3.0
	 */
	protected function formatted_sum() {

		if ( ! $this->price() ) {
			return '';
		}

		return apply_filters(
			'awooc_popup_price_html',
			sprintf(
				'%s<span class="awooc-formatted_sum-wrapper">%s</span></div>',
				apply_filters( 'awooc_popup_sum_label', __( 'Amount: ', 'art-woocommerce-order-one-click' ) ),
				wc_price( $this->get_sum() )
			),
			$this->get_product()
		);

	}

}