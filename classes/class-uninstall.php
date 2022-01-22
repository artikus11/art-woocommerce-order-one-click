<?php
/**
 * Удаление настроек и меты при удалении плагина
 *
 * @see     https://wpruse.ru/my-plugins/art-woocommerce-order-one-click/
 * @package art-woocommerce-order-one-click/classes
 * @version 3.0.0
 */

namespace Art\AWOOC;

class Uninstall {

	/**
	 * Deleting settings when uninstalling the plugin
	 *
	 * @since 2.0.0
	 */
	public static function uninstall(): void {

		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		if ( 'yes' === get_option( 'woocommerce_awooc_not_del_settings' ) ) {
			return;
		}

		self::remove_options();
		self::remove_post_meta();

	}


	protected static function remove_options(): void {


		$options = apply_filters(
			'awooc_uninstall_options',
			[
				'woocommerce_awooc_mode_catalog',
				'woocommerce_awooc_select_form',
				'woocommerce_awooc_title_button',
				'woocommerce_awooc_title_custom',
				'woocommerce_awooc_select_item',
				'woocommerce_awooc_enable_enqueue',
				'woocommerce_awooc_created_order',
				'woocommerce_awooc_change_subject',
				'woocommerce_awooc_settings_others',
				'woocommerce_awooc_not_del_settings',
				'woocommerce_awooc_text_rated',
			]
		);

		foreach ( $options as $option ) {
			delete_option( $option );
		}
	}


	public static function remove_post_meta(): void {

		global $wpdb;

		$wpdb->delete( $wpdb->postmeta, [ 'meta_key' => '_awooc_button' ], [ '%s' ] );
	}

}