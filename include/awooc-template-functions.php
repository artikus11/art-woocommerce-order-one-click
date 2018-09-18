<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Подключаем нужные стили и скрипты
 */
function awooc_enqueue_script_style() {
	wp_enqueue_script( 'awooc-scripts', AWOOC_PLUGIN_URI . 'assets/js/awooc-scripts.js', array( 'jquery' ), AWOOC_PLUGIN_VER, true );
	wp_enqueue_style( 'awooc-styles', AWOOC_PLUGIN_URI . 'assets/css/awooc-styles.css', array(), AWOOC_PLUGIN_VER );
	wp_localize_script( 'awooc-scripts', 'awooc_scrpts', array(
		'url'   => admin_url( 'admin-ajax.php' ),
		'nonce' => wp_create_nonce( 'awooc-nonce' ),
	) );
}

if ( ! function_exists( 'awooc_enable_add_to_card' ) ) {
	/**
	 * Показ кнопки В корзину
	 *
	 * @return mixed|void
	 */
	function awooc_enable_add_to_card() {
		ob_start();
		?>
		<style>
			.woocommerce-variation-add-to-cart,
			.single_add_to_cart_button,
			input.qty {
				display: inline-block !important;
			}
		</style>
		<?php
		$enable_add_to_card = apply_filters( 'awooc_enable_add_to_card_style', ob_get_clean() );
		echo $enable_add_to_card;
	}
}

if ( ! function_exists( 'awooc_disable_add_to_card' ) ) {
	/**
	 * Скрытие кнопки купить
	 *
	 * @return mixed|void
	 */
	function awooc_disable_add_to_card() {
		ob_start();
		?>
		<style>
			.woocommerce button.button.alt,
			.woocommerce-page button.button.alt,
			.woocommerce-variation-add-to-cart .quantity,
			.woocommerce-variation-add-to-cart .single_add_to_cart_button,
			.single_add_to_cart_button,
			input.qty {
				display: none !important;
			}
		</style>
		<?php
		$disable_add_to_card = apply_filters( 'awooc_disable_add_to_card_style', ob_get_clean() );
		echo $disable_add_to_card;
	}
}

if ( ! function_exists( 'awooc_disable_url_add_to_cart_to_related' ) ) {
	/**
	 *  Замена урл на кнопках в похожих товарах на страницах товарах
	 *
	 * @param $url
	 *
	 * @return string
	 */
	function awooc_disable_url_add_to_cart_to_related( $url ) {
		global $product;
		if ( is_product() ) {
			$url = get_permalink( $product->get_id() );
		}
		
		return $url;
	}
}

if ( ! function_exists( 'awooc_disable_text_add_to_cart_to_related' ) ) {
	/**
	 * Замена текста на кнопках в похожих товарах на страницах товарах
	 *
	 * @param $text
	 *
	 * @return string
	 */
	function awooc_disable_text_add_to_cart_to_related( $text ) {
		if ( is_product() ) {
			$text = __( 'Read more', 'woocommerce' );
		}
		
		return $text;
	}
}

if ( ! function_exists( 'awooc_html_custom_add_to_cart' ) ) {
	
	/**
	 * Вывод html кнопки Заказать
	 */
	function awooc_html_custom_add_to_cart() {
		global $product;
		echo apply_filters( 'awooc_html_add_to_cart',
			sprintf( '<a href="%s" data-value-product-id="%s" class="%s">%s</a>',
				esc_url( '#awooc-form-custom-order' ),
				esc_attr( $product->get_id() ),
				apply_filters( 'awooc_classes_button', esc_attr( 'awooc-custom-order button alt' ) ),
				esc_html( get_option( 'woocommerce_awooc_title_button' ) ) ),
			$product );
	}
}

if ( ! function_exists( 'awooc_popup_window_title' ) ) {
	/**
	 * Вывод заголовка товара в модальном окне
	 *
	 * @param $elements
	 * @param $product
	 */
	function awooc_popup_window_title( $elements, $product ) {
		if ( in_array( 'title', $elements ) ) {
			echo apply_filters( 'awooc_popup_title_html',
				sprintf( '<h2 class="%s">%s</h2>',
					esc_attr( 'awooc-form-custom-order-title' ),
					esc_html( $product->get_title() ) ),
				$product );
		}
	}
}

if ( ! function_exists( 'awooc_popup_window_image' ) ) {
	/**
	 *
	 * Вывод миниатюры товара в модальном окне
	 *
	 * @param $elements
	 * @param $product
	 */
	function awooc_popup_window_image( $elements, $product ) {
		if ( in_array( 'image', $elements ) ) {
			$post_thumbnail_id = get_post_thumbnail_id( $product->get_id() );
			$full_size_image   = wp_get_attachment_image_src( $post_thumbnail_id, apply_filters( 'awooc_thumbnail_name', 'shop_single' ) );
			
			echo '<div class="awooc-form-custom-order-img">';
			
			do_action( 'awooc_popup_before_image' );
			
			echo apply_filters( 'awooc_popup_image_html',
				sprintf( '<img src="%s" alt="%s" class="%s" width="%s" height="%s">',
					esc_url( $full_size_image[0] ),
					apply_filters( 'awooc_popup_image_alt', '' ),
					apply_filters( 'awooc_popup_image_classes', esc_attr( 'awooc-form-custom-order-img' ) ),
					esc_attr( $full_size_image[1] ) ,
					esc_attr( $full_size_image[2] ),
					$product ) );
			
			do_action( 'awooc_popup_after_image' );
			
			echo '</div>';
		}
	}
}

if ( ! function_exists( 'awooc_popup_window_price' ) ) {
	/**
	 * Вывод цены товара в модальном окне
	 *
	 * @param $elements
	 * @param $product
	 */
	function awooc_popup_window_price( $elements, $product ) {
		if ( in_array( 'price', $elements ) ) {
			
			echo apply_filters( 'awooc_popup_price_html',
				sprintf( '<div class="awooc-form-custom-order-price">%s<span class="awooc-price-wrapper">%s</span></div>',
					apply_filters( 'awooc_popup_price_label', 'Цена: ' ),
					wc_price( $product->get_price() ),
				$product ) );

		}
		
	}
}

if ( ! function_exists( 'awooc_popup_window_sku' ) ) {
	/**
	 * Вывод артикула товара в модальном окне
	 *
	 * @param $elements
	 * @param $product
	 */
	function awooc_popup_window_sku( $elements, $product ) {

		if ( in_array( 'sku', $elements ) ) {
			if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) {
				$sku = $product->get_sku() ? $product->get_sku() : 'N/A';
				
				echo '<div class="awooc-form-custom-order-sku">';
			
				echo apply_filters( 'awooc_popup_sku_html',
					sprintf( '<span class="awooc-sku-wrapper">%s</span><span class="awooc-sku">%s</span>',
						apply_filters( 'awooc_popup_sku_label', 'Артикул: ' ),
						$sku,
						$product ) );
				
				echo '</div>';
			}
		}
		
	}
}

if ( ! function_exists( 'awooc_popup_window_attr' ) ) {
	/**
	 * Вывод атрибутов вариативного товара в модальном окне
	 *
	 * @param $elements
	 * @param $product
	 */
	function awooc_popup_window_attr( $elements, $product ) {
		
		if ( in_array( 'attr', $elements ) ) {
			if ( $product->is_type( 'variable' ) )  {
			
				printf( '<div class="awooc-form-custom-order-attr">%s<span class="awooc-attr-wrapper"></span></div>',
					apply_filters( 'awooc_popup_attr_label', 'Атрибуты: ' ));

			}
		}
		
	}
}

if ( ! function_exists( 'awooc_popup_window_link' ) ) {
	/**
	 * Ссылка на товар в модальном окне
	 *
	 * @param $elements
	 * @param $product
	 */
	function awooc_popup_window_link( $elements, $product ) {
		printf( '<span class="awooc-form-custom-order-link awooc-hide">Ссылка на товар: %s</span>',
			esc_url(get_permalink( $product->get_id() )));
		
	}
}

if ( ! function_exists( 'awooc_popup_window_select_form' ) ) {
	/**
	 * Вывод формы обратной связи в модальном окне
	 *
	 * @param $elements
	 * @param $product
	 */
	function awooc_popup_window_select_form() {
		$select_form = get_option( 'woocommerce_awooc_select_form' );
		if ( $select_form ) {
			do_action( 'awooc_popup_before_form' );
			
			echo do_shortcode( '[contact-form-7 id="' . esc_attr( $select_form ) . '"]' );
			
			do_action( 'awooc_popup_after_form' );
		}
		
		
	}
}
/**
 * Возвратная функция для ajax запросов
 */
function awooc_ajax_scripts_callback() {
	
	if ( ! wp_verify_nonce( $_POST['nonce'], 'awooc-nonce' ) ) {
		wp_die( 'Данные отправлены с левого адреса' );
	}
	
	$product_var_id = $_POST['id'] ? esc_attr( $_POST['id'] ) : 0;
	if ( 0 == $product_var_id ) {
		wp_die( $product_var_id );
	}
	
	$product          = wc_get_product( $product_var_id );
	$attributes       = $product->get_attributes();
	$product_variable = new WC_Product_Variable( $product->get_parent_id() );
	$variations       = $product_variable->get_variation_attributes();
	$price            = wc_price( $product->get_price() );
	$attr_name        = array();

	foreach ( $attributes as $attr => $value ) {
		$attr_label = wc_attribute_label( $attr );
		$meta       = get_post_meta( $product_var_id, wc_variation_attribute_name( $attr ), true );
		$term       = get_term_by( 'slug', $meta, $attr );
		if ( false != $term ) {
			$attr_name[] = $attr_label . ': ' . $term->name;
		}
	}
	
	if ( empty( $attr_name ) ) {
		foreach ( $variations as $key => $item ) {
			$attr_name[] = $key . ': ' . implode( array_intersect( $item, $attributes ) );
		}
	}
	
	$product_var_attr = esc_html( implode( '; ', $attr_name ) );

	$product_variant = array(
		'attr' => $product_var_attr,
		'price' => $price,
	);
	wp_send_json( $product_variant );
	wp_die();
}

if ( ! function_exists( 'awooc_disable_add_to_cart' ) ) {
	/**
	 * Включение режима каталога в зависимости от настроек
	 *
	 * @return bool
	 */
	function awooc_disable_add_to_cart() {
		$mode_catalog = get_option( 'woocommerce_awooc_mode_catalog' );
		switch ( $mode_catalog ) {
			
			case 'dont_show_add_to_card':
				if ( is_product() ) {
					return true;
				}
				
				return false;
				break;
			case 'show_add_to_card':
			case 'in_stock_add_to_card':
				return true;
				break;
		}
		
		return true;
	}
}

if ( ! function_exists( 'awooc_add_custom_button' ) ) {
	/**
	 * Вывод кнопки Заказать в зависимости от настроек
	 */
	function awooc_add_custom_button() {
		global $product;
		$show_add_to_card = get_option( 'woocommerce_awooc_mode_catalog' );
		if ( 'dont_show_add_to_card' == $show_add_to_card ) {
			add_filter( 'woocommerce_product_add_to_cart_text', 'awooc_disable_text_add_to_cart_to_related' );
			add_filter( 'woocommerce_product_add_to_cart_url', 'awooc_disable_url_add_to_cart_to_related' );
			awooc_disable_add_to_card();
			awooc_html_custom_add_to_cart();
		} elseif ( 'show_add_to_card' == $show_add_to_card ) {
			awooc_enable_add_to_card();
			awooc_html_custom_add_to_cart();
		} elseif ( 'in_stock_add_to_card' == $show_add_to_card ) {
			if ( $product->is_on_backorder() || 0 == $product->get_price() ) {
				awooc_disable_add_to_card();
				awooc_html_custom_add_to_cart();
			}
		}
		
	}
}

if ( ! function_exists( 'awooc_add_custom_button_out_stock' ) ) {
	/**
	 * Вывод кнопки Заказать если товара нет в наличии
	 */
	function awooc_add_custom_button_out_stock() {
		global $product;
		if ( 'in_stock_add_to_card' == get_option( 'woocommerce_awooc_mode_catalog' ) ) {
			if ( ! $product->is_in_stock() ) {
				awooc_html_custom_add_to_cart();
			}
		}
	}
}

if ( ! function_exists( 'awooc_popup_window_html' ) ) {
	function awooc_popup_window_html() {
		include AWOOC_PLUGIN_DIR . 'templates/popup-window.php';
	}
}

if ( ! function_exists( 'awooc_form_product_page_custom_order' ) ) {
	/**
	 * Вывод всплывающего окна
	 */
	function awooc_form_product_page_custom_order() {
		global $product;
		if ( ! is_product() ) {
			return;
		}
		
		$elements = get_option( 'woocommerce_awooc_select_item' );
		if ( ! is_array( $elements ) ) {
			return;
		}
		
		awooc_popup_window_html();

	}
}

if ( ! function_exists( 'awooc_created_order_after_mail_send' ) ) {
	/**
	 * Создание заказа при отправке письма
	 *
	 * @param $contact_data
	 *
	 * @throws WC_Data_Exception
	 */
	function awooc_created_order_after_mail_send( $contact_data ) {
		if ( 'yes' == get_option( 'woocommerce_awooc_created_order' ) ) {
			$user_passed_text  = esc_attr( $_POST['awooc-text'] );
			$user_passed_email = esc_attr( $_POST['awooc-email'] );
			$user_passed_tel   = esc_attr( $_POST['awooc-tel'] );
			$address           = array(
				'first_name' => $user_passed_text,
				'email'      => $user_passed_email,
				'phone'      => $user_passed_tel,
			);
			$order             = wc_create_order();
			$order->add_product( wc_get_product( $_POST['awooc_product_id'] ), 1 );
			$order->set_address( $address, 'billing' );
			$order->calculate_totals();
			$order->update_status( 'Completed', 'Order created dynamically - ', true );
		}
	}
}