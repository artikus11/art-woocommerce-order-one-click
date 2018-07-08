<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
function awooc_settings_multiselect_select_elements() {
	$default = array(
		'title' => 'Заголовок',
		'image' => 'Изображение',
		'price' => 'Цена',
		'sku'   => 'Артикул',
		'attr'  => 'Атрибуты',
	);
	$options = get_option( 'woocommerce_awooc_select_item' );
	
	return wp_parse_args( $options, $default );
}

function awooc_settings_multiselect_default_elements() {
	$default = array(
		'title',
		'image',
		'price',
		'sku',
		'attr',
	);
	
	return $default;
}

function awooc_settings_select_forms() {
	$args     = array(
		'post_type'      => 'wpcf7_contact_form',
		'posts_per_page' => 20,
	);
	$cf7Forms = get_posts( $args );
	$select   = array();
	foreach ( $cf7Forms as $form ) {
		$select[ esc_attr( $form->ID ) ] = '[contact-form-7 id="' . esc_attr( $form->ID ) . '" title="' . esc_html( $form->post_title ) . '"]';
	}
	
	return $select;
}

add_filter( 'woocommerce_general_settings', 'awooc_add_setting' );
function awooc_add_setting( $settings ) {
	
	$settings[] = array(
		'name' => 'Настройки режима каталога',
		'type' => 'title',
		'desc' => 'Настройки плагина Art WooCommerce Order One Click',
		'id'   => 'woocommerce_awooc_settings',
	);
	$settings[] = array(
		'title'    => 'Режим работы',
		'desc'     => 'Выберите режим работы и показа кнопки Купить',
		'id'       => 'woocommerce_awooc_mode_catalog',
		'css'      => 'min-width:350px;',
		'class'    => 'wc-enhanced-select',
		'default'  => 'dont_show_add_to_card',
		'type'     => 'select',
		'options'  => array(
			'dont_show_add_to_card' => 'Не показывать кнопку Купить: режим каталога',
			'show_add_to_card'      => 'Показывать кнопку Купить: штатный режим',
			'in_stock_add_to_card'  => 'Кнопка Заказать появиться только при управлении запасами: режим предзаказа',
		),
		'desc_tip' => true,
	);
	$settings[] = array(
		'title'    => 'Выбор формы',
		'desc'     => 'Выберите нужную форму',
		'id'       => 'woocommerce_awooc_select_form',
		'css'      => 'min-width:350px;',
		'class'    => 'wc-enhanced-select',
		'default'  => '-- Выбрать --',
		'type'     => 'select',
		'options'  => awooc_settings_select_forms(),
		'desc_tip' => true,
	);
	$settings[] = array(
		'title'    => 'Надпись на кнопке',
		'desc'     => 'Укажите нужную надпись на кнопке',
		'id'       => 'woocommerce_awooc_title_button',
		'css'      => 'min-width:350px;',
		'default'  => 'Заказать',
		'type'     => 'text',
		'desc_tip' => true,
	);
	$settings[] = array(
		'title'    => 'Выключить элементы окна',
		'desc'     => 'Уберите элементы, которые НЕ нужны',
		'id'       => 'woocommerce_awooc_select_item',
		'css'      => 'min-width:350px;',
		'class'    => 'wc-enhanced-select',
		'type'     => 'multiselect',
		'default'  => awooc_settings_multiselect_default_elements(),
		'options'  => awooc_settings_multiselect_select_elements(),
		'desc_tip' => true,
	);
	$settings[] = array(
		'title'   => 'Включить создание заказов',
		'desc'    => '<div class="updated notice error inline"><p><strong>Внимание! Функционал находится в стадии разработки.</strong> Для корректной работы данного функционала, требуется правильное создание полей в форме Contact Form 7 c именами:</p> <ul><li>поле имени - <code>awooc-text</code>;</li> <li>поле email - <code>awooc-email</code>;</li> <li>поле телефона - <code>awooc-tel</code>.</li></ul></div>',
		'id'      => 'woocommerce_awooc_created_order',
		'default' => 'no',
		'type'    => 'checkbox',
	);
	$settings[] = array(
		'type' => 'sectionend',
		'id'   => 'woocommerce_awooc_settings',
	);
	
	return $settings;
}

