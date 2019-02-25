<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Элементы настроек по умолчанию
 *
 * @since 2.0.0
 *
 */
function awooc_default_elements_item() {

	$default = array(
		'title',
		'image',
		'price',
		'sku',
		'attr',
	);

	return $default;
}

/**
 * Обработка класов окна
 *
 * @since 2.1.0
 *
 * @param array $elements
 */
function awooc_class_full( $elements ) {

	$class_full = '';
	if ( ! $elements || ( in_array( 'title', $elements, true ) && count( $elements ) <= 1 ) ) {
		$class_full = 'awooc-col-full';
	}

	echo esc_html( $class_full );
}
