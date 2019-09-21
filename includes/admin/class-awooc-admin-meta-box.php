<?php
/**
 * Метабокс в товарах
 *
 * @see     https://wpruse.ru/my-plugins/art-woocommerce-order-one-click/
 * @package art-woocommerce-order-one-click/includes/admin
 * @version 2.3.0
 */

/**
 * Class AWOOC_Admin_Meta_Box
 *
 * @author Artem Abramovich
 * @since  2.3.0
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
	 * @param  array $options входящиий массив опций.
	 *
	 * @return array
	 *
	 * @since 2.3.0
	 */
	public static function meta_box( $options ) {

		$new_option['awooc_button'] = array(
			'id'            => '_awooc_button',
			'wrapper_class' => 'show_if_simple show_if_variable',
			'label'         => __( 'Disable Order One Click Button', 'art-woocommerce-order-one-click' ),
			'description'   => __(
				'If enabled, then on this product the Order button will not be visible. Product will return to its original condition.',
				'art-woocommerce-order-one-click'
			),
			'default'       => 'no',
		);

		return array_slice( $options, 0, 0 ) + $new_option + $options;
	}


	/**
	 * Сохраняем данные
	 *
	 * @param int $post_id ID продукта.
	 *
	 * @since 2.3.0
	 */
	public static function save_meta_box( $post_id ) {

		$product = wc_get_product( $post_id );

		// @codingStandardsIgnoreStart
		$button = isset( $_POST['_awooc_button'] ) ? 'yes' : 'no';
		// @codingStandardsIgnoreEnd

		$product->update_meta_data( '_awooc_button', $button );

		$product->save();

	}

}

AWOOC_Admin_Meta_Box::init();
