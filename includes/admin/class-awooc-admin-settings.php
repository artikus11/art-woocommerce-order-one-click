<?php

/**
 * Class AWOOC_Admin_Settings
 *
 * @author Artem Abramovich
 * @since  1.8.0
 *
 * @todo   Сделать настройку для добавления отслеживания метрики или аналитики
 * @todo   Сделать настройку изменения статуса заказа
 * @todo   Сделать проверку на наличие выбранных элементов
 */
class AWOOC_Admin_Settings extends WC_Settings_Page {

	/**
	 * Constructor.
	 *
	 * @since 1.8.0
	 */
	public function __construct() {

		$this->id    = 'awooc_settings';
		$this->label = __( 'One click order', 'art-woocommerce-order-one-click' );

		parent::__construct();

		add_action( 'woocommerce_admin_field_notice', array( $this, 'text_notice' ), 10, 1 );

	}


	public function get_sections() {

		$sections = apply_filters(
			'awooc_settings_sections',
			array(
				'' => __( 'General', 'art-woocommerce-order-one-click' ),
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
					'name' => __( 'General settings', 'art-woocommerce-order-one-click' ),
					'type' => 'title',
					'id'   => 'woocommerce_awooc_settings_catalog_mode',
				),

				array(
					'title'    => __( 'Operating mode', 'art-woocommerce-order-one-click' ),
					'desc'     => __( 'Select the mode of operation and display the Buy button', 'art-woocommerce-order-one-click' ),
					'id'       => 'woocommerce_awooc_mode_catalog',
					'css'      => 'min-width:350px;',
					'class'    => 'wc-enhanced-select',
					'default'  => 'dont_show_add_to_card',
					'type'     => 'select',
					'options'  => array(
						'dont_show_add_to_card' => __( 'Do not show Buy button: catalog mode', 'art-woocommerce-order-one-click' ),
						'show_add_to_card'      => __( 'Show Buy button: normal mode', 'art-woocommerce-order-one-click' ),
						'in_stock_add_to_card'  => __( 'The Order button appears only when inventory management: pre-order mode', 'art-woocommerce-order-one-click' ),
					),
					'desc_tip' => true,
				),

				array(
					'title'    => __( 'Select form', 'art-woocommerce-order-one-click' ),
					'desc'     => __( 'Choose the desired form', 'art-woocommerce-order-one-click' ),
					'id'       => 'woocommerce_awooc_select_form',
					'css'      => 'min-width:350px;',
					'class'    => 'wc-enhanced-select',
					'default'  => __( '-- Select --', 'art-woocommerce-order-one-click' ),
					'type'     => 'select',
					'options'  => $this->select_forms(),
					'desc_tip' => true,
				),

				array(
					'title'    => __( 'Button Label', 'art-woocommerce-order-one-click' ),
					'desc'     => __( 'Specify the desired label on the button', 'art-woocommerce-order-one-click' ),
					'id'       => 'woocommerce_awooc_title_button',
					'css'      => 'min-width:350px;',
					'default'  => __( 'Buy in one click', 'art-woocommerce-order-one-click' ),
					'type'     => 'text',
					'desc_tip' => true,
				),

				array(
					'type' => 'sectionend',
					'id'   => 'woocommerce_awooc_settings_catalog_mode',
				),

				array(
					'name' => __( 'Popup window', 'art-woocommerce-order-one-click' ),
					'type' => 'title',
					'desc' => '',
					'id'   => 'woocommerce_awooc_settings_popup_window',
				),

				array(
					'title'    => __( 'Turn off window elements', 'art-woocommerce-order-one-click' ),
					'desc'     => __( 'Remove items that are NOT needed.', 'art-woocommerce-order-one-click' ),
					'id'       => 'woocommerce_awooc_select_item',
					'css'      => 'min-width:350px;',
					'class'    => 'wc-enhanced-select',
					'type'     => 'multiselect',
					'default'  => awooc_default_elements_item(),
					'options'  => $this->select_elements_item(),
					'desc_tip' => true,
				),

				array(
					'type' => 'sectionend',
					'id'   => 'woocommerce_awooc_settings_popup_window',
				),

				array(
					'name' => __( 'Orders', 'art-woocommerce-order-one-click' ),
					'type' => 'title',
					'id'   => 'woocommerce_awooc_settings_orders',
				),

				array(
					'id'      => 'woocommerce_awooc_created_order_notice',
					'type'    => 'notice',
					'class'   => 'awooc-notice notice-warning',
					'style'   => '',
					'message' => $this->order_setting_notice(),
				),

				array(
					'title'   => __( 'Create orders', 'art-woocommerce-order-one-click' ),
					'desc'    => __(
						'When this setting is enabled, orders will be created in the WooCommerce panel with the status "Pending payment"',
						'art-woocommerce-order-one-click'
					),
					'id'      => 'woocommerce_awooc_created_order',
					'default' => 'no',
					'type'    => 'checkbox',
				),

				/*array(/// @codingStandardsIgnoreLine
					'title'   => 'Отправить письмо пользователю',
					'desc'    => 'Письма пользователю о заказе по умолчанию не отправляются. При включении этой настройки пользователям письма будут приходить',
					'id'      => 'woocommerce_awooc_send_email_customer',
					'default' => 'no',
					'type'    => 'checkbox',
				),*/

				array(
					'type' => 'sectionend',
					'id'   => 'woocommerce_awooc_settings_orders',
				),

			)
		);

		return apply_filters( 'woocommerce_get_settings_' . $this->id, $settings, $current_section );

	}


	/**
	 * @return array
	 *
	 * @since 1.8.0
	 */
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


	/**
	 * @return array
	 */
	public static function select_default_elements_item() {

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
	 * @return array
	 */
	public function select_elements_item() {

		$options = array(
			'title' => __( 'Title', 'art-woocommerce-order-one-click' ),
			'image' => __( 'Image', 'art-woocommerce-order-one-click' ),
			'price' => __( 'Price', 'art-woocommerce-order-one-click' ),
			'sku'   => __( 'SKU', 'art-woocommerce-order-one-click' ),
			'attr'  => __( 'Attributes', 'art-woocommerce-order-one-click' ),
		);

		return $options;
	}


	/**
	 * Message to created orders settings
	 *
	 * @return string
	 *
	 * @since 1.9.0
	 */
	public function order_setting_notice() {

		$message     = '<p>' . __( '<strong>Warning! The functionality is under development. </strong> For the correct operation of this functionality. Requires proper creation of fields in the Contact Form 7 form with the names:', 'art-woocommerce-order-one-click' ) . '</p>';
		$field_name  = __( 'field Name - <code>awooc-text</code>;', 'art-woocommerce-order-one-click' );
		$field_email = __( 'field Email - <code>awooc-email</code>;', 'art-woocommerce-order-one-click' );
		$field_tel   = __( 'field Phone - <code>awooc-tel</code>;', 'art-woocommerce-order-one-click' );

		$message .= '<ul><li>' . $field_name . '</li><li>' . $field_email . '</li><li>' . $field_tel . '</li></ul>';

		return $message;
	}


	/**
	 * @param $value
	 *
	 * @since 1.9.0
	 */
	public function text_notice( $value ) {

		if ( $value['style'] ) {
			$style = 'style="' . $value['style'] . '"';
		} else {
			$style = '';
		}

		?>
		<div id="<?php echo esc_attr( $value['id'] ); ?>" class="<?php echo esc_attr( $value['class'] ); ?>" <?php echo esc_attr( $style ); ?>>
			<?php echo wp_kses_post( wpautop( wptexturize( $value['message'] ) ) ); ?>
		</div>

		<?php

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

new AWOOC_Admin_Settings();
