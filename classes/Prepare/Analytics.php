<?php

namespace Art\AWOOC\Prepare;

class Analytics extends Prepare {

	public function get_response(): array {

		$data = [
			'id'       => $this->parent_id(),
			'title'    => $this->title(),
			'sku'      => $this->sku(),
			'price'    => $this->price(),
			'attr'     => $this->attributes(),
			'qty'      => $this->get_qty(),
			'category' => $this->product_category(),
		];

		if ( $this->main->get_mode()->is_mode_catalog() ) {
			unset( $data['qty'] );
		}

		return $data;
	}


	/**
	 * Получаем первый термин для аналитики
	 *
	 * @return bool|string
	 * @since 3.0.0
	 */
	protected function product_category() {

		$terms = get_the_terms( $this->parent_id(), 'product_cat' );

		if ( ! $terms || is_wp_error( $terms ) ) {
			return false;
		}

		return array_shift( $terms )->name;
	}
}
