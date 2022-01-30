<?php

namespace Art\AWOOC;

class Conditional {

	public function is_mode_special(): bool {

		return 'no_stock_no_price' === $this->get_mode();
	}


	public function is_mode_catalog(): bool {

		return 'dont_show_add_to_card' === $this->get_mode();
	}


	public function is_mode_preorder(): bool {

		return 'in_stock_add_to_card' === $this->get_mode();
	}


	public function is_mode_normal(): bool {

		return 'show_add_to_card' === $this->get_mode();
	}


	/**
	 * Получение режима работы
	 *
	 * 'dont_show_add_to_card' => __( 'Catalog mode', 'art-woocommerce-order-one-click' )
	 * 'show_add_to_card'      => __( 'Normal mode', 'art-woocommerce-order-one-click' )
	 * 'in_stock_add_to_card'  => __( 'Pre-order mode', 'art-woocommerce-order-one-click' )
	 * 'no_stock_no_price'     => __( 'Special mode', 'art-woocommerce-order-one-click' )
	 *
	 * @return false|mixed|void
	 * @since  3.0.0
	 */
	public function get_mode() {

		return get_option( 'woocommerce_awooc_mode_catalog' );
	}

}