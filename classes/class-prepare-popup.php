<?php

namespace Art\AWOOC;

class Prepare_Popup extends Prepare {

	public function get_response(): array {

		$data = [
			'title' => $this->title(),
			'image' => $this->image(),
			'price' => $this->formatted_price(),
			'sku'   => $this->formatted_sku(),
			'attr'  => $this->formatted_attr(),
			'qty'   => $this->formatted_qty(),
			'sum'   => $this->formatted_sum(),
			'form'  => $this->select_form(),
		];

		if ( $this->main->get_mode()->is_mode_catalog() || $this->main->get_mode()->is_mode_preorder() ) {
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

		add_filter( 'wc_price', [ $this, 'formatted_wc_price' ], 10, 5 );

		return apply_filters(
			'awooc_popup_price_html',
			sprintf(
				'<span class="awooc-price-label">%s</span><span class="awooc-price-value">%s</span>',
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
					'<span class="awooc-sku-label">%s</span><span class="awooc-sku-value">%s</span>',
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
			'<span class="awooc-attr-label">%s</span></br><span class="awooc-attr-value"><span>%s</span></span>',
			apply_filters( 'awooc_popup_attr_label', esc_html__( 'Attributes: ', 'art-woocommerce-order-one-click' ) ),
			implode( '; </span><span>', $this->get_attributes_alt_method() ),

		);
	}


	/**
	 * Форматирование количества
	 *
	 *
	 * @return string
	 * @since 3.0.0
	 */
	protected function formatted_qty(): string {

		$allowed_html = [
			'div'   => [
				'class' => 'quantity',

			],
			'span'  => [
				'class' => [],

			],
			'label' => [ 'class' => [] ],
			'input' => [
				'type'         => [],
				'id'           => [],
				'class'        => [],
				'name'         => [],
				'value'        => [],
				'title'        => [],
				'size'         => [],
				'min'          => [],
				'max'          => [],
				'step'         => [],
				'placeholder'  => [],
				'inputmode'    => [],
				'autocomplete' => [],
			],
		];

		return wp_kses(
			apply_filters(
				'awooc_popup_qty_html',
				sprintf(
					'<span class="awooc-qty-label">%s</span>%s',
					apply_filters( 'awooc_popup_qty_label', __( 'Quantity: ', 'art-woocommerce-order-one-click' ) ),

					woocommerce_quantity_input(
						[
							'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $this->get_product()->get_min_purchase_quantity(), $this->get_product() ),
							'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $this->get_product()->get_max_purchase_quantity(), $this->get_product() ),
							'input_value' => $this->get_qty() !== null ? wc_stock_amount( wp_unslash( $this->get_qty() ) ) : $this->get_product()->get_min_purchase_quantity(),
							'classes'     => [ 'input-text', 'qty', 'text', 'awooc-popup-input-qty' ],
						],
						$this->get_product(),
						false
					)

				),
				$this->get_product()
			), $allowed_html
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

		add_filter( 'wc_price', [ $this, 'formatted_wc_price' ], 10, 5 );

		return apply_filters(
			'awooc_popup_price_html',
			sprintf(
				'<span class="awooc-sum-label">%s</span><span class="awooc-sum-value">%s</span>',
				apply_filters( 'awooc_popup_sum_label', __( 'Amount: ', 'art-woocommerce-order-one-click' ) ),
				wc_price( $this->get_sum() )
			),
			$this->get_product()
		);

	}


	public function formatted_wc_price( $return, $price, $args, $unformatted_price, $original_price ): string {

		$negative        = $price < 0;
		$formatted_price = ( $negative ? '-' : '' ) . sprintf(
				$args['price_format'],
				'<span class="woocommerce-Price-currencySymbol">' . get_woocommerce_currency_symbol( $args['currency'] ) . '</span>',
				'<span class="woocommerce-Price-currencyValue">' . $price . '</span>'
			);

		return '<span class="woocommerce-Price-amount amount"><bdi>' . $formatted_price . '</bdi></span>';
	}

}