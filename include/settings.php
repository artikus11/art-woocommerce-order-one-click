<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
function awooc_settings_select_elements() {
	$default = array(
		'title' => 'Заголовок',
		'image' => 'Изображение',
		'price' => 'Цена',
		'sku'   => 'Артикул',
		'attr'  => 'Атрибуты',
	);
	
	return $default;
}

function awooc_settings_select_forms() {
	$args     = array(
		'post_type'      => 'wpcf7_contact_form',
		'posts_per_page' => - 1,
	);
	$cf7Forms = get_posts( $args );
	$select   = array();
	foreach ( $cf7Forms as $form ) {
		$select[ esc_attr( $form->ID ) ] = '[contact-form-7 id="' . esc_attr( $form->ID ) . '" title="' .
		                                   esc_html( $form->post_title ) . '"]';
	}
	
	return $select;
}

add_filter( 'woocommerce_general_settings', 'bryce_add_a_setting' );
function bryce_add_a_setting( $settings ) {
	
	$settings[] = array(
		'name' => 'Настройки режима каталога',
		'type' => 'title',
		'desc' => 'Настройки плагина Art WooCommerce Order One Click',
		'id'   => 'woocommerce_awooc_settings',
	);
	$settings[] = array(
		'title'         => 'Режим каталога',
		'desc'          => 'Снимите чекбокс, если хотите чтобы была видно кнопка купить',
		'id'            => 'woocommerce_awooc_mode_catalog',
		'default'       => 'yes',
		'type'          => 'checkbox',
		'checkboxgroup' => 'start',
		'autoload'      => true,
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
		'desc'     => 'Выберите элементы, которые НЕ надо показывать',
		'id'       => 'woocommerce_awooc_select_item',
		'css'      => 'min-width:350px;',
		'class'    => 'wc-enhanced-select',
		'type'     => 'multiselect',
		'options'  => awooc_settings_select_elements(),
		'desc_tip' => true,
	);
	$settings[] = array(
		'type' => 'sectionend',
		'id'   => 'woocommerce_awooc_settings',
	);
	
	return $settings;
}

