<?php

namespace Art\AWOOC\Admin;

class Settings_Fields {

	public function init_hooks(): void {

		add_action( 'woocommerce_admin_field_metabox_open', [ __CLASS__, 'metabox_open' ], 10, 1 );
		add_action( 'woocommerce_admin_field_metabox_close', [ __CLASS__, 'metabox_close' ], 10, 1 );

		add_action( 'woocommerce_admin_field_wrap_open', [ __CLASS__, 'wrap_open' ], 10, 1 );
		add_action( 'woocommerce_admin_field_wrap_close', [ __CLASS__, 'wrap_close' ], 10, 1 );

		add_action( 'woocommerce_admin_field_main_open', [ __CLASS__, 'main_open' ], 10, 1 );
		add_action( 'woocommerce_admin_field_main_close', [ __CLASS__, 'main_close' ], 10, 1 );

		add_action( 'woocommerce_admin_field_post_box', [ __CLASS__, 'post_box' ], 10, 1 );
		add_action( 'woocommerce_admin_field_notice', [ __CLASS__, 'text_notice' ], 10, 1 );
		add_action( 'woocommerce_admin_field_group_input', [ __CLASS__, 'group_input' ], 15, 1 );
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
	 * Обертка для сгруппированных опций
	 *
	 * @param  string $option название опции.
	 *
	 * @return array
	 *
	 * @since 2.1.4
	 */
	public static function group_fields( string $option ): array {

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
	public static function group_fields_default(): array {

		return [
			'id'    => '',
			'type'  => '',
			'label' => '',
		];
	}


	/**
	 * Произвольное поле для сообщений
	 *
	 * @param  array $value массив аргументов поля.
	 *
	 * @since 2.0.0
	 */
	public static function text_notice( array $value ): void {

		$style = '';

		if ( $value['style'] ) {
			$style = 'style="' . $value['style'] . '"';
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
	public static function metabox_open( array $value ): void {

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
	public static function metabox_close( array $value ): void {

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
	public static function wrap_open( array $value ): void {

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
	public static function wrap_close( array $value ): void {

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
	public static function main_open( array $value ): void {

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
	public static function main_close( array $value ): void {

		if ( ! empty( $value['id'] ) ) {
			echo '</div>';
		}
	}
}
