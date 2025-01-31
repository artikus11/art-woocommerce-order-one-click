<?php
/**
 * Создание формы при первой установке
 *
 * @see     https://wpruse.ru/my-plugins/art-woocommerce-order-one-click/
 * @package art-woocommerce-order-one-click/classes
 * @version 2.3.1
 */

namespace Art\AWOOC\Admin;

use WPCF7_ContactForm;
use WPCF7_ContactFormTemplate;

/**
 * Class AWOOC_Install_Form
 *
 * @author Artem Abramovich
 * @since  2.3.1
 */
class Create_Form {

	/**
	 * Создание формы при первой активации
	 *
	 * @since 2.3.0
	 */
	public static function install_form() {

		if ( ! class_exists( 'WPCF7_ContactForm' ) ) {
			return;
		}

		if ( get_option( 'woocommerce_awooc_active' ) ) {
			return;
		}

		$mail_form = sprintf(
			'
[text* awooc-text class:input-text placeholder "%1$s"]
[email* awooc-email class:input-text placeholder "%2$s"]
[tel* awooc-tel class:input-text placeholder "%3$s"]
[submit class:wp-element-button "%4$s"]',
			__( 'Your Name', 'art-woocommerce-order-one-click' ),
			__( 'Your Email', 'art-woocommerce-order-one-click' ),
			__( 'Your Phone', 'art-woocommerce-order-one-click' ),
			__( 'Send', 'art-woocommerce-order-one-click' )
		);

		$mail_body = /* translators: %s: [awooc-text] */
			sprintf( __( 'Name: %s', 'art-woocommerce-order-one-click' ), '[awooc-text]' ) . "\n" . /* translators: %s: [awooc-email]' */
			sprintf( __( 'Email: %s', 'art-woocommerce-order-one-click' ), '[awooc-email]' ) . "\n" . /* translators: %s: [awooc-tel] */
			sprintf( __( 'Phone: %s', 'art-woocommerce-order-one-click' ), '[awooc-tel]' ) . "\n\n" . '[awooc-hidden-data]' . "\n\n" . '-- ' . "\n" .
			/* translators: 1: blog name, 2: blog URL */
			sprintf( __( 'This e-mail was sent from a contact form on %1$s (%2$s)', 'contact-form-7' ), get_bloginfo( 'name' ), get_bloginfo( 'url' ) );

		self::created_form( $mail_form, $mail_body );

		$option = [
			'awooc_validate' => [
				'timestamp'     => time(),
				'version'       => AWOOC_PLUGIN_VER,
				'count_valid'   => 1,
				'count_invalid' => 0,
			],
		];

		if ( empty( get_option( 'woocommerce_awooc_active' ) ) ) {
			update_option( 'woocommerce_awooc_active', $option );
		}
	}


	/**
	 * @param  string $mail_form
	 * @param  string $mail_body
	 */
	public static function created_form( string $mail_form, string $mail_body ): void {

		$contact_form = WPCF7_ContactForm::get_template();

		$contact_form->set_properties(
			[
				'form'   => $mail_form,
				'mail'   => [
					'subject'            => /* translators: 1: blog name, 2: blog URL */ sprintf(
						__( 'Order from the site %1$s (%2$s)', 'art-woocommerce-order-one-click' ),
						get_bloginfo( 'name' ),
						get_bloginfo( 'url' )
					),
					'sender'             => sprintf( '%s <%s>', get_bloginfo( 'name' ), WPCF7_ContactFormTemplate::from_email() ),
					'body'               => $mail_body,
					'recipient'          => get_option( 'admin_email' ),
					'additional_headers' => 'Reply-To:[awooc-email]',
					'attachments'        => '',
					'use_html'           => 1,
					'exclude_blank'      => 0,
				],
				'mail_2' => [
					'subject'            => /* translators: 1: blog name, 2: blog URL */ sprintf(
						__( 'Order from the site %1$s (%2$s)', 'art-woocommerce-order-one-click' ),
						get_bloginfo( 'name' ),
						get_bloginfo( 'url' )
					),
					'sender'             => sprintf( '%s <%s>', get_bloginfo( 'name' ), WPCF7_ContactFormTemplate::from_email() ),
					'body'               => $mail_body,
					'recipient'          => get_option( 'admin_email' ),
					'additional_headers' => 'Reply-To:[awooc-email]',
					'attachments'        => '',
					'use_html'           => 1,
					'exclude_blank'      => 0,
				],
			]
		);

		$props = $contact_form->get_properties();

		$post_content = implode( "\n", wpcf7_array_flatten( $props ) );

		$post_id = wp_insert_post(
			[
				'post_type'    => 'wpcf7_contact_form',
				'post_status'  => 'publish',
				'post_title'   => __( 'Order One Click', 'art-woocommerce-order-one-click' ),
				'post_content' => trim( $post_content ),
			]
		);

		if ( $post_id ) {
			foreach ( $props as $prop => $value ) {
				update_post_meta(
					$post_id,
					'_' . $prop,
					wpcf7_normalize_newline_deep( $value )
				);
			}

			add_post_meta( $post_id, '_hash',
				wpcf7_generate_contact_form_hash( $post_id ),
				true
			);
		}
	}
}
