<?php

namespace Art\AWOOC;

class Response_Mail extends Response {

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

		if ( awooc()->conditional->is_mode_catalog() || awooc()->conditional->is_mode_preorder() ) {
			unset( $data['qty'] );
		}

		return $data;
	}


	protected function caption_title(): string {

		return sprintf(
			'%s%s',
			__( 'Title: ', 'art-woocommerce-order-one-click' ),
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
			'%s%s',
			__( 'Price: ', 'art-woocommerce-order-one-click' ),
			$this->price()
		);
	}


	protected function caption_sku(): string {

		return sprintf(
			'%s%s',
			__( 'SKU: ', 'art-woocommerce-order-one-click' ),
			$this->sku()
		);
	}


	protected function caption_attributes(): string {

		return sprintf(
			'%s%s',
			__( 'Attributes: ', 'art-woocommerce-order-one-click' ),
			$this->attributes()
		);
	}


	protected function caption_qty(): string {

		return sprintf(
			'%s%s',
			__( 'Quantity: ', 'art-woocommerce-order-one-click' ),
			$this->get_qty()
		);
	}


	protected function caption_amount(): string {

		return sprintf(
			'%s%s',
			__( 'Amount: ', 'art-woocommerce-order-one-click' ),
			$this->get_sum()
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
			'%s%s',
			__( 'Link to the product: ', 'art-woocommerce-order-one-click' ),
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