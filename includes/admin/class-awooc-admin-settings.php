<?php
/**
 * Настройки
 *
 * @see     https://wpruse.ru/my-plugins/art-woocommerce-order-one-click/
 * @package art-woocommerce-order-one-click/includes/admin
 * @version 1.8.0
 */

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

		add_action( 'woocommerce_admin_field_metabox_open', array( __CLASS__, 'metabox_open' ), 10, 1 );
		add_action( 'woocommerce_admin_field_metabox_close', array( __CLASS__, 'metabox_close' ), 10, 1 );

		add_action( 'woocommerce_admin_field_wrap_open', array( __CLASS__, 'wrap_open' ), 10, 1 );
		add_action( 'woocommerce_admin_field_wrap_close', array( __CLASS__, 'wrap_close' ), 10, 1 );

		add_action( 'woocommerce_admin_field_main_open', array( __CLASS__, 'main_open' ), 10, 1 );
		add_action( 'woocommerce_admin_field_main_close', array( __CLASS__, 'main_close' ), 10, 1 );

		add_action( 'woocommerce_admin_field_post_box', array( __CLASS__, 'post_box' ), 10, 1 );
		add_action( 'woocommerce_admin_field_notice', array( __CLASS__, 'text_notice' ), 10, 1 );
		add_action( 'woocommerce_admin_field_group_input', array( __CLASS__, 'group_input' ), 15, 1 );

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_script_style' ) );

	}


	/**
	 * Дефолтные элементы
	 *
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
	 * Обработка селекта Да/Нет
	 *
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
	 * Произвольное поле для сообщений
	 *
	 * @param  array $value массив аргументов поля.
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
	 * Открывающий тег сайдбара
	 *
	 * @param  array $value массив аргументов поля.
	 *
	 * @since 2.2.6
	 */
	public static function metabox_open( $value ) {

		if ( ! empty( $value['id'] ) ) {
			?>
			<div id="postbox-container-1" class="postbox-container">
				<div class="meta-box-sortables">
			<?php

		}

	}


	/**
	 * Закрывающий тег сайдбара
	 *
	 * @param  array $value массив аргументов поля.
	 *
	 * @since 2.2.6
	 */
	public static function metabox_close( $value ) {

		if ( ! empty( $value['id'] ) ) {
			?>
				</div>
			</div>
			<?php

		}

	}


	/**
	 * Открывающий тег обертки всей страницы настроек
	 *
	 * @param  array $value массив аргументов поля.
	 *
	 * @since 2.2.6
	 */
	public static function wrap_open( $value ) {

		if ( ! empty( $value['id'] ) ) {
			?>
			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-2">
					<div class="inside">
			<?php

		}

	}


	/**
	 * Закрывающий тег обертки всей страницы настроек
	 *
	 * @param  array $value массив аргументов поля.
	 *
	 * @since 2.2.6
	 */
	public static function wrap_close( $value ) {

		if ( ! empty( $value['id'] ) ) {
			?>
					</div>
				</div>
			</div>
			<br class="clear">
			<?php

		}

	}


	/**
	 * Открывающий тег основного контента
	 *
	 * @param  array $value массив аргументов поля.
	 *
	 * @since 2.2.6
	 */
	public static function main_open( $value ) {

		if ( ! empty( $value['id'] ) ) {
			echo '<div id="post-body-content">';
		}

	}


	/**
	 * Закрывающий тег основного контента
	 *
	 * @param  array $value массив аргументов поля.
	 *
	 * @since 2.2.6
	 */
	public static function main_close( $value ) {

		if ( ! empty( $value['id'] ) ) {
			echo '</div>';
		}

	}


	/**
	 * Произвольная группа полей
	 *
	 * @param  array $value массив аргументов поля.
	 *
	 * @since 2.1.4
	 */
	public static function group_input( $value ) {

		$option_value       = WC_Admin_Settings::get_option( $value['id'], $value['default'] );
		$field_desc_tooltip = WC_Admin_Settings::get_field_description( $value );

		?>
		<tr>
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $value['id'] ); ?>">
					<?php echo esc_html( $value['title'] ); ?>
					<?php echo $field_desc_tooltip['tooltip_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</label>
			</th>
			<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
				<?php echo $field_desc_tooltip['description']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
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
								placeholder="<?php echo esc_attr( $val['label'] ); ?>"
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
	 * Обертка для сгруппированных опций
	 *
	 * @param  string $option название опции.
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
	 * Значение по умолчанию для группы полей
	 *
	 * @return array
	 *
	 * @since 2.1.4
	 */
	public static function group_fields_default() {

		return array(
			'id'    => '',
			'type'  => '',
			'label' => '',
		);

	}


	/**
	 * Произвольный метабокс в сайдбаре
	 *
	 * @param  array $value массив аргументов поля.
	 *
	 * @since 2.2.6
	 */
	public static function post_box( $value ) {

		if ( $value['style'] ) {
			$style = 'style="' . $value['style'] . '"';
		} else {
			$style = '';
		}

		if ( $value['title'] ) {
			$title = '<h2><span>' . $value['title'] . '</span></h2>';
		} else {
			$title = '';
		}

		?>
		<div id="<?php echo esc_attr( $value['id'] ); ?>" class="<?php echo esc_attr( $value['class'] ); ?> postbox" <?php echo esc_attr( $style ); ?>>
			<?php echo wp_kses_post( $title ); ?>
			<div class="inside">
				<?php echo wp_kses_post( wpautop( wptexturize( $value['message'] ) ) ); ?>
			</div>
		</div>
		<?php

	}


	/**
	 * Отдельная секция во вкладке
	 *
	 * @return array|mixed|void
	 */
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


	/**
	 * Настройки
	 *
	 * @param  string $current_section название входящей секции.
	 *
	 * @return array|mixed|void
	 */
	public function get_settings( $current_section = '' ) {

		$settings = apply_filters(
			'awooc_settings_section_main',
			array(
				array(
					'type' => 'wrap_open',
					'id'   => 'woocommerce_awooc_wrap_open',
				),

				array(
					'type' => 'main_open',
					'id'   => 'woocommerce_awooc_main_open',
				),

				array(
					'name' => __( 'General settings', 'art-woocommerce-order-one-click' ),
					'type' => 'title',
					'id'   => 'woocommerce_awooc_settings_catalog_mode',
				),

				array(
					'title'   => __( 'Operating mode', 'art-woocommerce-order-one-click' ),
					'desc'    => __( 'Select the mode of operation and display the Buy button', 'art-woocommerce-order-one-click' ),
					'id'      => 'woocommerce_awooc_mode_catalog',
					'css'     => 'min-width:350px;',
					'class'   => 'wc-enhanced-select',
					'default' => 'dont_show_add_to_card',
					'type'    => 'select',
					'options' => self::select_operating_mode(),
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
					'title'    => __( 'Special label', 'art-woocommerce-order-one-click' ),
					'desc'     => __(
						'Special field for the Order button. Allows you to change the label on the button when turning on the Price and Stocks mode.',
						'art-woocommerce-order-one-click'
					),
					'id'       => 'woocommerce_awooc_title_custom',
					'css'      => 'min-width:350px;',
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
					'title'   => __( 'Styles and scripts globally', 'art-woocommerce-order-one-click' ),
					'desc'    => __(
						'If you enable this setting, then the files of forces and scripts will be connected globally on all pages of the site. This setting will be useful for displaying the One-click Order button on the list of products and when using quick view',
						'art-woocommerce-order-one-click'
					),
					'id'      => 'woocommerce_awooc_enable_enqueue',
					'default' => 'no',
					'type'    => 'checkbox',
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

				array(
					'title'   => __( 'Change subject', 'art-woocommerce-order-one-click' ),
					'desc'    => __(
						'If you enable this setting, then the order number will be added to the subject of the letter in the format of the "Letter Subject No. 112233"',
						'art-woocommerce-order-one-click'
					),
					'id'      => 'woocommerce_awooc_сhange_subject',
					'default' => 'no',
					'type'    => 'checkbox',
				),

				array(
					'type' => 'sectionend',
					'id'   => 'woocommerce_awooc_settings_orders',
				),
				array(
					'name' => __( 'Others', 'art-woocommerce-order-one-click' ),
					'type' => 'title',
					'desc' => '',
					'id'   => 'woocommerce_awooc_settings_others',
				),

				array(
					'title'   => __( 'Do not delete settings', 'art-woocommerce-order-one-click' ),
					'desc'    => __(
						'If enabled, then the settings when you remove the plugin will NOT be deleted',
						'art-woocommerce-order-one-click'
					),
					'id'      => 'woocommerce_awooc_not_del_settings',
					'default' => 'no',
					'type'    => 'checkbox',
				),

				array(
					'type' => 'sectionend',
					'id'   => 'woocommerce_awooc_settings_others',
				),

				array(
					'type' => 'main_close',
					'id'   => 'woocommerce_awooc_main_close',
				),
				array(
					'id'   => 'woocommerce_awooc_metabox_open',
					'type' => 'metabox_open',
				),

				array(
					'id'      => 'woocommerce_awooc_call_to_rate',
					'type'    => 'post_box',
					'class'   => '',
					'style'   => '',
					'title'   => '',
					'message' => self::call_to_rate(),
				),

				array(
					'id'      => 'woocommerce_awooc_guide',
					'type'    => 'post_box',
					'class'   => '',
					'style'   => '',
					'title'   => __( 'Useful links', 'art-woocommerce-order-one-click' ),
					'message' => self::guide_link(),
				),

				array(
					'id'      => 'woocommerce_awooc_call_to_donate',
					'type'    => 'post_box',
					'class'   => '',
					'style'   => '',
					'title'   => __( 'Donate', 'art-woocommerce-order-one-click' ),
					'message' => self::call_to_donate(),
				),

				array(
					'type' => 'metabox_close',
					'id'   => 'woocommerce_awooc_column_close',
				),

				array(
					'type' => 'wrap_close',
					'id'   => 'woocommerce_awooc_wrap_close',
				),
			)
		);

		return apply_filters( 'woocommerce_get_settings_' . $this->id, $settings, $current_section );

	}


	/**
	 * Дефолтные значения селекта выбора режима
	 *
	 * @return array
	 *
	 * @since 2.0.0
	 */
	public static function select_operating_mode() {

		$options = apply_filters(
			'awooc_select_operating_mode',
			array(
				'dont_show_add_to_card' => __( 'Catalog mode', 'art-woocommerce-order-one-click' ),
				'show_add_to_card'      => __( 'Normal mode', 'art-woocommerce-order-one-click' ),
				'in_stock_add_to_card'  => __( 'Pre-order mode', 'art-woocommerce-order-one-click' ),
				'no_stock_no_price'     => __( 'Special mode', 'art-woocommerce-order-one-click' ),
			)
		);

		return $options;
	}


	/**
	 * Выбор нужной формы
	 *
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
	 * Дефолтные значения селекта опции окна
	 *
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
			'sum'   => __( 'Amount', 'art-woocommerce-order-one-click' ),
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
	 * Призыв поставить рейтинг
	 *
	 * @return string
	 * @since  2.2.6
	 */
	public static function call_to_rate() {

		$message = '';

		if ( ! current_user_can( 'manage_woocommerce' ) || ! function_exists( 'wc_get_screen_ids' ) || ! function_exists( 'get_current_screen' ) ) {
			return $message;
		}

		$current_screen = get_current_screen();
		$wc_pages       = wc_get_screen_ids();

		$wc_pages = array_diff( $wc_pages, array( 'profile', 'user-edit' ) );

		if ( isset( $current_screen->id ) && in_array( $current_screen->id, $wc_pages, true ) ) {
			if ( ! get_option( 'woocommerce_awooc_text_rated' ) ) {
				$message = sprintf(
					/* translators: 1: Art WooCommerce Order One Click 2:: five stars */
					esc_html__( 'If you like the plugin %1$s please leave us a %2$s rating. A huge thanks in advance!', 'art-woocommerce-order-one-click' ),
					sprintf( '<strong>%s</strong>', esc_html__( 'Art WooCommerce Order One Click', 'art-woocommerce-order-one-click' ) ),
					'<a href="https://wordpress.org/support/plugin/art-woocommerce-order-one-click/reviews?rate=5#new-post" target="_blank" class="awooc-rating-link" aria-label="' .
					esc_attr__( 'five star', 'art-woocommerce-order-one-click' ) . '" data-rated="' . esc_attr__( 'Thanks :)', 'art-woocommerce-order-one-click' ) .
					'">&#9733;&#9733;&#9733;&#9733;&#9733;</a>'
				);
				wc_enqueue_js(
					"jQuery( 'a.awooc-rating-link' ).click( function() {
						jQuery.post( '" . WC()->ajax_url() . "', { action: 'awooc_rated', resp: 'yes' } );
						jQuery( this ).parent().text( jQuery( this ).data( 'rated' ) );
					});"
				);
			} else {
				$message = __( 'Thank you for using the plugin!', 'art-woocommerce-order-one-click' );
			}
		}

		return $message;
	}


	/**
	 * Призыв поддержать проект
	 *
	 * @return string
	 * @since  2.2.6
	 */
	public static function call_to_donate() {

		$message = '';

		if ( ! current_user_can( 'manage_woocommerce' ) || ! function_exists( 'wc_get_screen_ids' ) ) {
			return $message;
		}

		$payments = array(
			'pp'     => array(
				'title' => __( 'PayPal', 'art-woocommerce-order-one-click' ),
				'desc'  => __( 'Make a donation through PayPal', 'art-woocommerce-order-one-click' ),
				'link'  => 'https://www.paypal.me/artabr',
			),
			'yd'     => array(
				'title' => __( 'Yandex Money', 'art-woocommerce-order-one-click' ),
				'desc'  => __( 'Make a donation through the Yandex Money system. You can use bank cards', 'art-woocommerce-order-one-click' ),
				'link'  => 'https://money.yandex.ru/to/41001551911515',
			),
			'wpruse' => array(
				'title' => __( 'WPRUSe', 'art-woocommerce-order-one-click' ),
				'desc'  => __( 'WPRUSe project site', 'art-woocommerce-order-one-click' ),
				'link'  => 'https://wpruse.ru/donat/',
			),
		);

		$message = sprintf(
			/* translators: 1: Art WooCommerce Order One Click  */
			esc_html__( 'You can make a donation to make the plugin %1$s even better!', 'art-woocommerce-order-one-click' ),
			sprintf( '<strong>%s</strong>', esc_html__( 'Art WooCommerce Order One Click', 'art-woocommerce-order-one-click' ) )
		);

		foreach ( $payments as $key => $payment ) {
			$message .= '<p><span class="woocommerce-help-tip" data-tip="' . $payment['desc'] . '"></span><strong>';
			$message .= '<a href="' . $payment['link'] . '" target="_blank" class="awooc-donate-link">' . $payment['title'] . '</a>';
			$message .= '</strong><p>';
		}

		return $message;
	}


	/**
	 * Ссылка на инструкцию
	 *
	 * @return string
	 * @since  2.2.6
	 */
	public static function guide_link() {

		$message = '';

		if ( ! current_user_can( 'manage_woocommerce' ) || ! function_exists( 'wc_get_screen_ids' ) ) {
			return $message;
		}

		$message = __( 'Detailed step by step instructions for setting up the plugin (in Russian)', 'art-woocommerce-order-one-click' );

		$message .= '<p><a href="https://wpruse.ru/my-plugins/art-woocommerce-order-one-click/" target="_blank" class="awooc-tutorial-link">Read more...</a></p>';
		$message .= __( 'Plugin on GitHub, you can write there suggestions, wishes or participate in the development', 'art-woocommerce-order-one-click' );

		$message .= '<p><a href="https://github.com/artikus11/art-woocommerce-order-one-click" target="_blank" class="awooc-tutorial-link">Plugin on GitHub</a></p>';

		return $message;
	}


	/**
	 * Подключаем дополниетльный скрипт в админке для управления описанием
	 *
	 * @since  2.2.1
	 */
	public function admin_enqueue_script_style() {

		wp_enqueue_script( 'admin-awooc-script', AWOOC_PLUGIN_URI . 'assets/js/admin-script.js', array(), AWOOC_PLUGIN_VER, false );
		wp_localize_script(
			'admin-awooc-script',
			'awooc_admin',
			array(
				'mode_catalog'  => __(
					'On the pages of the categories and the store itself, the Add to Cart buttons are disabled. On the product page, the "Add to cart" button is hidden and the "Order" button appears.',
					'art-woocommerce-order-one-click'
				),
				'mode_normal'   => __(
					'The button "Add to cart" works in the normal mode, that is, goods can be added to the cart and at the same time ordered in one click',
					'art-woocommerce-order-one-click'
				),
				'mode_in_stock' => __(
					'The Order button will appear automatically if: Price not available;  stock status "In Unfulfilled Order"; stock status "Out of stock"; inventory management is enabled at item level and preorders allowed',
					'art-woocommerce-order-one-click'
				),
				'mode_special'  => __(
					'When turned on, it works the same way as normal mode. But if the goods have no price or the product out of stock, then only the Order button will appear.',
					'art-woocommerce-order-one-click'
				),
			)
		);

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
