<?php
/**
 * Подключение файлов
 *
 * @see     https://wpruse.ru/my-plugins/art-woocommerce-order-one-click/
 * @package art-woocommerce-order-one-click/classes
 * @version 3.0.0
 */

namespace Art\AWOOC;

class Templater {

	/**
	 * @param  string $template_name
	 *
	 * @return string
	 */
	public function get_template( string $template_name ): string {

		$template_path = locate_template( $this->template_path() . $template_name );

		if ( ! $template_path ) {
			$template_path = sprintf( '%s/templates/%s', $this->plugin_path(), $template_name );
		}

		return apply_filters( 'awooc_locate_template', $template_path );
	}


	/**
	 * @return string
	 */
	public function template_path(): string {

		return apply_filters( 'awooc_template_path', 'art-woocommerce-order-one-click/' );
	}


	/**
	 * @return string
	 */
	public function plugin_path(): string {

		return untrailingslashit( AWOOC_PLUGIN_DIR );
	}


	/**
	 * @return string
	 */
	public function plugin_url(): string {

		return untrailingslashit( plugins_url( '/', AWOOC_PLUGIN_FILE ) );
	}

}