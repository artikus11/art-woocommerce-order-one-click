<?php

/**
 * Class AWOOC_Admin_Meta_Box
 *
 * @author Artem Abramovich
 * @since  2.3.0
 *
 */
class AWOOC_Admin_Meta_Box {

	/**
	 * Инициализация класса
	 *
	 * @since 2.3.0
	 */
	public static function init() {

		self::hooks();
	}


	/**
	 * Подключение хуков
	 *
	 * @since 2.3.0
	 */
	public static function hooks() {

		add_filter( 'product_type_options', array( __CLASS__, 'meta_box' ), 10, 1 );
		add_action( 'woocommerce_process_product_meta_simple', array( __CLASS__, 'save_meta_box' ), 10, 1 );
		add_action( 'woocommerce_process_product_meta_variable', array( __CLASS__, 'save_meta_box' ), 10, 1 );
	}


	/**
	 * Добавляем дополнительные элементы
	 *
	 * @param $options
	 *
	 * @return array
	 *
	 * @since 2.3.0
	 */
	public static function meta_box( $options ) {

		$options = array_slice( $options, 0, 0 ) + [
				'awooc_button' => [
					'id'            => '_awooc_button',
					'wrapper_class' => 'show_if_simple show_if_variable',
					'label'         => __( 'Disable Order One Click Button', 'art-woocommerce-order-one-click' ),
					'description'   => __( 'If enabled, then on this product the Order button will not be visible. Product will return to its original condition.', 'art-woocommerce-order-one-click' ),
					'default'       => 'no',
				],
			] + $options;

		return $options;
	}


	/**
	 * Сохраняем данные
	 *
	 * @param $post_id
	 *
	 * @since 2.3.0
	 */
	public static function save_meta_box( $post_id ) {

		$product = wc_get_product( $post_id );
		$button  = isset( $_POST['_awooc_button'] ) ? 'yes' : 'no';

		$product->update_meta_data( '_awooc_button', $button );

		$product->save();

	}

}

AWOOC_Admin_Meta_Box::init();
