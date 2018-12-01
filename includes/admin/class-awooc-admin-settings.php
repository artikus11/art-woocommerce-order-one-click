<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class AWOOC_Admin_Settings
 *
 * @author Artem Abramovich
 * @since  1.8.0
 *
 * @todo   Сделать настройку для добавления отслеживания метрики или аналитики
 * @todo   Сделать настройку изменения статуса заказа
 */
class AWOOC_Admin_Settings extends WC_Settings_Page {

	/**
	 * Constructor.
	 *
	 * @since 1.8.0
	 */
	public function __construct() {

		$this->id    = 'awooc_settings';
		$this->label = 'Заказ в один клик';

		parent::__construct();

	}


	public function get_sections() {

		$sections = apply_filters(
			'awooc_settings_sections',
			array(
				'' => 'Оcновные',
			)
		);

		return apply_filters( 'woocommerce_get_sections_' . $this->id, $sections );
	}


	/**
	 * Output the settings.
	 */
	public function output() {

		global $current_section;

		$settings = $this->get_settings( $current_section );

		WC_Admin_Settings::output_fields( $settings );
	}


	public function get_settings( $current_section = '' ) {

			$settings = apply_filters(
				'awooc_settings_section_main',
				array(

					array(
						'name' => 'Основные настройки',
						'type' => 'title',
						'id'   => 'woocommerce_awooc_settings_catalog_mode',
					),

					array(
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
					),
					array(
						'title'    => 'Выбор формы',
						'desc'     => 'Выберите нужную форму',
						'id'       => 'woocommerce_awooc_select_form',
						'css'      => 'min-width:350px;',
						'class'    => 'wc-enhanced-select',
						'default'  => '-- Выбрать --',
						'type'     => 'select',
						'options'  => $this->select_forms(),
						'desc_tip' => true,
					),
					array(
						'title'    => 'Надпись на кнопке',
						'desc'     => 'Укажите нужную надпись на кнопке',
						'id'       => 'woocommerce_awooc_title_button',
						'css'      => 'min-width:350px;',
						'default'  => 'Заказать',
						'type'     => 'text',
						'desc_tip' => true,
					),

					array(
						'type' => 'sectionend',
						'id'   => 'woocommerce_awooc_settings_catalog_mode',
					),

					array(
						'name' => 'Всплывающее окно',
						'type' => 'title',
						'desc' => '',
						'id'   => 'woocommerce_awooc_settings_popup_window',
					),

					array(
						'title'    => 'Выключить элементы окна',
						'desc'     => 'Уберите элементы, которые НЕ нужны',
						'id'       => 'woocommerce_awooc_select_item',
						'css'      => 'min-width:350px;',
						'class'    => 'wc-enhanced-select',
						'type'     => 'multiselect',
						'default'  => $this->select_default_elements_item(),
						'options'  => $this->select_elements_item(),
						'desc_tip' => true,
					),
					array(
						'type' => 'sectionend',
						'id'   => 'woocommerce_awooc_settings_popup_window',
					),

					array(
						'name' => 'Заказы',
						'type' => 'title',
						'desc' => '<div class="updated notice error inline"><p><strong>Внимание! Функционал находится в стадии разработки.</strong> Для корректной работы данного функционала,
						требуется правильное создание полей в форме Contact Form 7 c именами:</p>
					<ul>
						<li>поле имени - <code>awooc-text</code>;</li>
						<li>поле email - <code>awooc-email</code>;</li>
						<li>поле телефона - <code>awooc-tel</code>.</li>
					</ul>
				</div>',
						'id'   => 'woocommerce_awooc_settings_orders',
					),


					array(
						'id'   => 'woocommerce_awooc_created_order_notice',
						'type' => 'text_notice',
					),

					array(
						'title'   => 'Включить создание заказов',
						'desc'    => 'Заказы создаются со статусом "Выполнено"',
						'id'      => 'woocommerce_awooc_created_order',
						'default' => 'no',
						'type'    => 'checkbox',
					),

					array(
						'title'   => 'Отправить письмо пользователю',
						'desc'    => 'Письма пользователю о заказе по умолчанию не отправляются. При включении этой настройки пользователям письма будут приходить',
						'id'      => 'woocommerce_awooc_send_email_customer',
						'default' => 'no',
						'type'    => 'checkbox',
					),

					array(
						'type' => 'sectionend',
						'id'   => 'woocommerce_awooc_settings_orders',
					),

				)
			);


		return apply_filters( 'woocommerce_get_settings_' . $this->id, $settings, $current_section );

	}


	public function select_forms() {

		$args = array(
			'post_type'      => 'wpcf7_contact_form',
			'posts_per_page' => 20,
		);

		$cf7_forms = get_posts( $args );
		$select    = array();
		foreach ( $cf7_forms as $form ) {
			$select[ esc_attr( $form->ID ) ] = '[contact-form-7 id="' . esc_attr( $form->ID ) . '" title="' . esc_html( $form->post_title ) . '"]';
		}

		return $select;
	}


	public function select_default_elements_item() {

		$default = array(
			'title',
			'image',
			'price',
			'sku',
			'attr',
		);

		return $default;
	}


	public function select_elements_item() {

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


	/**
	 * Save settings.
	 */
	public function save() {

		global $current_section;
		$settings = $this->get_settings( $current_section );
		WC_Admin_Settings::save_fields( $settings );
		if ( $current_section ) {
			do_action( 'woocommerce_update_options_' . $this->id . '_' . $current_section );
		}
	}

}