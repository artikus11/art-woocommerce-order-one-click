<?php

namespace Art\AWOOC;

class Prepare_Mail extends Prepare {

	public function get_response(): array {

		$data = [
			'id'         => $this->caption_id(),
			'title'      => $this->caption_title(),
			'sku'        => $this->caption_sku(),
			'price'      => $this->caption_price(),
			'attr'       => $this->caption_attributes(),
			'qty'        => $this->caption_qty(),
			'sum'        => $this->caption_amount(),
			'categories' => $this->formatted_category_list(),
			'link'       => $this->caption_link(),
		];

		if ( $this->main->get_mode()->is_mode_catalog() ) {
			unset( $data['qty'] );
		}

		return $data;
	}


	protected function caption_title(): string {

		return sprintf(
			__( 'Title: %s', 'art-woocommerce-order-one-click' ),
			$this->title()
		);
	}


	protected function caption_id(): string {

		return sprintf(
			'ID: %s',
			$this->id()
		);
	}


	protected function caption_price(): string {

		return sprintf(
			__( 'Price: %s', 'art-woocommerce-order-one-click' ),
			wp_filter_nohtml_kses( wc_price( $this->price() ) )
		);
	}


	protected function caption_sku(): string {

		return sprintf(
			__( 'SKU: %s', 'art-woocommerce-order-one-click' ),
			$this->sku()
		);
	}


	protected function caption_attributes(): string {

		return sprintf(
			__( 'Attributes: %s', 'art-woocommerce-order-one-click' ),
			$this->attributes()
		);
	}


	protected function caption_qty(): string {

		return sprintf(
			__( 'Quantity: %s', 'art-woocommerce-order-one-click' ),
			$this->get_qty()
		);
	}


	protected function caption_amount(): string {

		return sprintf(
			__( 'Amount: %s', 'art-woocommerce-order-one-click' ),
			wp_filter_nohtml_kses( wc_price( $this->get_sum() ) )
		);
	}


	/**
	 * Получаем ссылку на товар
	 *
	 *
	 * @return string
	 * @since 3.0.0
	 */
	protected function caption_link(): string {

		return sprintf(
			__( 'Link to the product: %s', 'art-woocommerce-order-one-click' ),
			$this->link()
		);
	}


	/**
	 * Форматированные категории товара
	 *
	 * @return string
	 * @since 3.0.0
	 */
	protected function formatted_category_list(): string {

		$categories_list = $this->category_list();

		return sprintf(
			'%s%s',
			_n(
				'Category: ', 'Categories: ',
				count( $categories_list ),
				'art-woocommerce-order-one-click'
			),
			implode( ', ', $categories_list )
		);
	}
}
