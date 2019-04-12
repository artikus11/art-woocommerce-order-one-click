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

		add_action( 'woocommerce_admin_field_notice', array( __CLASS__, 'text_notice' ), 10, 1 );
		add_action( 'woocommerce_admin_field_group_input', array( __CLASS__, 'group_input' ), 15, 1 );
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
					'options'  => self::select_operating_mode(),
					'desc_tip' => true,
				),

				array(
					'title'    => __( 'Price & Stocks', 'art-woocommerce-order-one-click' ),
					'desc'     => __(
						'The inclusion of the button "Order in one click" in the absence of price and availability of goods. Works with simple and variable and goods. The button "Add to cart" will be hidden and only the button "Order in one click" will be visible.',
						'art-woocommerce-order-one-click'
					),
					'id'       => 'woocommerce_awooc_no_price',
					'css'      => 'min-width:350px;',
					'class'    => 'wc-enhanced-select',
					'default'  => 'off',
					'type'     => 'select',
					'options'  => self::select_on_off(),
					'desc_tip' => true,
				),

				array(
					'title'    => __( 'Special label', 'art-woocommerce-order-one-click' ),
					'desc'     => __( 'Special field for the Order button. Allows you to change the label on the button when turning on the Price and Stocks mode.', 'art-woocommerce-order-one-click' ),
					'id'       => 'woocommerce_awooc_title_custom',
					'css'      => 'min-width:350px;',
					'type'     => 'text',
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
					'default'  => esc_html__( 'Buy in one click', 'art-woocommerce-order-one-click' ),
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
					'options'  => self::select_elements_item(),
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
					'message' => self::order_setting_notice(),
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
			'qty',
		);

		return $default;
	}


	/**
	 * @return array
	 *
	 * @since 2.0.0
	 */
	public static function select_operating_mode() {

		$options = apply_filters(
			'awooc_select_operating_mode',
			array(
				'dont_show_add_to_card' => __( 'Do not show Buy button: catalog mode', 'art-woocommerce-order-one-click' ),
				'show_add_to_card'      => __( 'Show Buy button: normal mode', 'art-woocommerce-order-one-click' ),
				'in_stock_add_to_card'  => __( 'The Order button appears only when inventory management: pre-order mode', 'art-woocommerce-order-one-click' ),
			)
		);

		return $options;
	}

	/**
	 * @return array
	 *
	 * @since 2.0.0
	 */
	public static function select_on_off() {

		return array(
			'off' => __( 'Off', 'art-woocommerce-order-one-click' ),
			'on'  => __( 'On', 'art-woocommerce-order-one-click' ),
		);
	}

	/**
	 * @return array
	 */
	public static function select_elements_item() {

		$options = array(
			'title' => __( 'Title', 'art-woocommerce-order-one-click' ),
			'image' => __( 'Image', 'art-woocommerce-order-one-click' ),
			'price' => __( 'Price', 'art-woocommerce-order-one-click' ),
			'sku'   => __( 'SKU', 'art-woocommerce-order-one-click' ),
			'attr'  => __( 'Attributes', 'art-woocommerce-order-one-click' ),
			'qty'   => __( 'Quantity', 'art-woocommerce-order-one-click' ),
		);

		return $options;
	}


	/**
	 * Message to created orders settings
	 *
	 * @return string
	 *
	 * @since 2.0.0
	 */
	public static function order_setting_notice() {

		$message     = '<p>' . __( '<strong>Warning! The functionality is under development. </strong> For the correct operation of this functionality. Requires proper creation of fields in the Contact Form 7 form with the names:', 'art-woocommerce-order-one-click' ) . '</p>';
		$field_name  = __( 'field Name - <code>awooc-text</code>;', 'art-woocommerce-order-one-click' );
		$field_email = __( 'field Email - <code>awooc-email</code>;', 'art-woocommerce-order-one-click' );
		$field_tel   = __( 'field Phone - <code>awooc-tel</code>;', 'art-woocommerce-order-one-click' );

		$message .= '<ul><li>' . $field_name . '</li><li>' . $field_email . '</li><li>' . $field_tel . '</li></ul>';

		return $message;
	}


	/**
	 * Произвольное поле для сообщений
	 *
	 * @param $value
	 *
	 * @since 2.0.0
	 */
	public static function text_notice( $value ) {

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
	 * Произвольная группа полей
	 *
	 * @param $value
	 *
	 * @since 2.1.4
	 */
	public static function group_input( $value ) {

		$option_value       = WC_Admin_Settings::get_option( $value['id'], $value['default'] );
		$field_desc_tooltip = WC_Admin_Settings::get_field_description( $value );

		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $value['id'] ); ?>">
					<?php echo esc_html( $value['title'] ); ?>
					<?php echo $field_desc_tooltip['tooltip_html']; // WPCS: XSS ok. ?>
				</label>
			</th>
			<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
					<?php echo $field_desc_tooltip['description']; // WPCS: XSS ok. ?>
					<div class="awooc-row" style="<?php echo esc_attr( $value['css'] ); ?>">
						<?php

						foreach ( $value['fields'] as $key => $val ) :
							if ( ! isset( $val['id'] ) ) {
								$val['id'] = '';
							}

							if ( ! isset( $val['class'] ) ) {
								$val['class'] = '';
							}

							if ( ! isset( $val['label'] ) ) {
								$val['label'] = '';
							}

							if ( ! isset( $val['type'] ) ) {
								$val['type'] = 'text';
							}

							if ( ! isset( $val['css'] ) || empty( $val['css'] ) ) {
								$val['css'] = 'width: 100%';
							}

							?>
							<div class="awooc-column">

								<input
									name="<?php echo esc_attr( $value['id'] ) . '[' . esc_attr( $val['id'] ) . ']'; ?>"
									value="<?php echo esc_attr( $option_value[ $val['id'] ] ); ?>"
									type="<?php echo esc_attr( $val['type'] ); ?>"
									class="<?php echo esc_attr( $val['class'] ); ?>"
									style="<?php echo esc_attr( $val['css'] ); ?>"
									placeholder="<?php echo esc_attr( $option_value[ $val['label'] ] ); ?>"
									data-tip="<?php echo esc_attr( $key ); ?>"
								/>
								<label for="<?php echo esc_attr( $value['id'] ) . '[' . esc_attr( $val['id'] ) . ']'; ?>">

									<em>
										<small><?php echo esc_html( $val['label'] ); ?></small>
									</em>
								</label>
							</div>
						<?php endforeach; ?>
					</div>

			</td>
		</tr>

		<?php

	}


	/**
	 * Значение по умолчанию для группы полей
	 *
	 * @return array
	 *
	 * @since 2.1.4
	 */
	public static function group_fields_default() {

		$default = array(
			'id'    => '',
			'type'  => '',
			'label' => '',
		);

		return $default;
	}


	/**
	 * @param $option
	 *
	 * @return array
	 *
	 * @since 2.1.4
	 */
	public static function group_fields( $option ) {

		$options = get_option( $option );

		return wp_parse_args( $options, self::group_fields_default() );
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
