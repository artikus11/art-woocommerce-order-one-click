<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Основные хуки
 */
add_action( 'wp_enqueue_scripts', 'awooc_enqueue_script_style', 100 );
add_action( 'wp_ajax_nopriv_awooc_ajax_variant_order', 'awooc_ajax_scripts_callback' );
add_action( 'wp_ajax_awooc_ajax_variant_order', 'awooc_ajax_scripts_callback' );
add_action( 'wp_footer', 'awooc_form_product_page_custom_order' );

/**
 * Хуки Вукомерса
 */
add_filter( 'woocommerce_is_purchasable', 'awooc_disable_add_to_cart', 10 );
add_filter( 'woocommerce_is_purchasable', 'awooc_disable_add_to_cart', 10 );
add_action( 'woocommerce_after_add_to_cart_button', 'awooc_add_custom_button' );
add_action( 'woocommerce_single_product_summary', 'awooc_add_custom_button_out_stock', 35 );


/**
 * Хуки Contact Form 7
 */
add_action( 'wpcf7_mail_sent', 'awooc_created_order_after_mail_send', 10, 1 );


/**
 * Модальное окно
 *
 * @see awooc_popup_window_title()
 * @see awooc_popup_window_image()
 * @see awooc_popup_window_price()
 * @see awooc_popup_window_sku()
 * @see awooc_popup_window_sku()
 * @see awooc_popup_window_attr()
 * @see awooc_popup_window_select_form()
 */
add_action( 'awooc_popup_before_column', 'awooc_popup_window_title', 10,  2);

add_action( 'awooc_popup_column_left', 'awooc_popup_window_image', 10,  2);
add_action( 'awooc_popup_column_left', 'awooc_popup_window_price', 20,  2);
add_action( 'awooc_popup_column_left', 'awooc_popup_window_sku', 30,  2);
add_action( 'awooc_popup_column_left', 'awooc_popup_window_attr', 40,  2);
add_action( 'awooc_popup_column_left', 'awooc_popup_window_link', 50,  2);

add_action( 'awooc_popup_column_right', 'awooc_popup_window_select_form', 20);
