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

