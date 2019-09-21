<?php
/**
 * Файл вспомогательных функций
 *
 * @see     https://wpruse.ru/my-plugins/art-woocommerce-order-one-click/
 * @package art-woocommerce-order-one-click/includes
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Комментарии перед кнопкой
 *
 * @see awooc_html_comments()
 *
 * @since 2.1.1
 */
add_action( 'awooc_before_button', 'awooc_html_comments', 10 );

/**
 * Элементы настроек по умолчанию
 *
 * @since 2.0.0
 */
function awooc_default_elements_item() {

	$default = array(
		'title',
		'image',
		'price',
		'sku',
		'attr',
		'qty',
	);

	return $default;
}

/**
 * Обработка класов окна
 *
 * @param  array $elements массив элементов.
 *
 * @since 2.1.0
 */
function awooc_class_full( $elements ) {

	$class_full = '';
	if ( ! $elements || ( in_array( 'title', $elements, true ) && count( $elements ) <= 1 ) ) {
		$class_full = 'awooc-col-full';
	}

	echo esc_html( $class_full );
}

/**
 * Вспомогательная функция, для осуществления поддержки плагина
 *
 * @since 2.1.1
 */
function awooc_html_comments() {

	if ( apply_filters( 'awooc_html_comments', true ) ) {
		?>
		<!-- plugin version: <?php echo esc_html( AWOOC_PLUGIN_VER ); ?>; mode: <?php echo esc_html( get_option( 'woocommerce_awooc_mode_catalog' ) ); ?>-->
		<?php

	}
}
