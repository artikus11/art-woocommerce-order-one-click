<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
add_action( 'wp_enqueue_scripts', 'awooc_enqueue_script_style', 100 );
/**
 * Пожключаем нужные стили и скрипты
 */
function awooc_enqueue_script_style() {
	wp_enqueue_script( 'awooc-scripts', AWOOC_PLUGIN_URI .
	                                    'assets/js/awooc-scripts.js', array( 'jquery' ), AWOOC_PLUGIN_VER, true );
	wp_enqueue_style( 'awooc-styles', AWOOC_PLUGIN_URI . 'assets/css/awooc-styles.css', array(), AWOOC_PLUGIN_VER );
	wp_localize_script( 'awooc-scripts', 'awooc_scrpts', array(
		'url'   => admin_url( 'admin-ajax.php' ),
		'nonce' => wp_create_nonce( 'awooc-nonce' ),
	) );
}

add_action( 'wp_ajax_awooc_ajax_variant_order', 'awooc_ajax_scripts_callback' );
add_action( 'wp_ajax_nopriv_awooc_ajax_variant_order', 'awooc_ajax_scrpts_callback' );
/**
 * Возвратная функция для ajax запросов
 */
function awooc_ajax_scripts_callback() {
	
	if ( ! wp_verify_nonce( $_POST['nonce'], 'awooc-nonce' ) ) {
		wp_die( 'Данные отправлены с левого адреса' );
	}
	$product_var_id = $_POST['id'] ? esc_attr( $_POST['id'] ) : 0;
	if ( 0 == $product_var_id ) {
		wp_die();
	}
	$product          = wc_get_product( $product_var_id );
	$attributes       = $product->get_attributes();
	$product_variable = new WC_Product_Variable( $product->get_parent_id() );
	$variations       = $product_variable->get_variation_attributes();
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
	wp_send_json( $product_var_attr );
	wp_die();
}

add_filter( 'woocommerce_is_purchasable', 'awooc_disable_add_to_cart', 10 );
/**
 * Включение режима каталога в зависимости от настроек
 *
 * @return bool
 */
function awooc_disable_add_to_cart() {
	$mode_catalog = get_option( 'woocommerce_awooc_mode_catalog' );
	switch ( $mode_catalog ) {
		case 'dont_show_add_to_card':
		case 'show_add_to_card':
			if ( is_product() ) {
				return true;
			}
			
			return false;
			break;
		case 'in_stock_add_to_card':
			return true;
			break;
	}
	
	return true;
}



add_action( 'woocommerce_after_add_to_cart_button', 'awooc_add_custom_button' );
/**
 * Вывод кнопки Заказать в зависимости от настроек
 */
function awooc_add_custom_button() {
	global $product;
	$show_add_to_card = get_option( 'woocommerce_awooc_mode_catalog' );
	if ('dont_show_add_to_card' == $show_add_to_card) {
		awooc_disable_add_to_card();
		add_filter( 'woocommerce_product_add_to_cart_text', 'awooc_disable_text_add_to_cart_to_related' );
		add_filter( 'woocommerce_product_add_to_cart_url', 'awooc_disable_url_add_to_cart_to_related' );
		awooc_html_custom_add_to_cart();
	} elseif ('show_add_to_card' == $show_add_to_card) {
		awooc_enable_add_to_card();
		awooc_html_custom_add_to_cart();
	} elseif ('in_stock_add_to_card' == $show_add_to_card ){
		if ($product->is_purchasable() || !$product->is_in_stock() || $product->backorders_allowed() || $product->is_on_backorder()  ){
			awooc_disable_add_to_card();
			awooc_html_custom_add_to_cart();
		}
	}

}

add_action( 'wp_footer', 'awooc_form_custom_order' );
/**
 * Вывод всплывающего окна
 */
function awooc_form_custom_order() {
	global $product;
	if ( ! is_product() ) {
		return;
	}
	$elements = get_option( 'woocommerce_awooc_select_item' );
	if (!is_array($elements)){
		return;
	}
	?>
	<div id="awooc-form-custom-order" class="awooc-form-custom-order awooc-hide">
		<div class="awwoc-close">&#215;</div>
		<div class="awooc-custom-order-wrap">
			<?php if ( in_array( 'title', $elements ) ): ?>
				<h2 class="awooc-form-custom-order-title"><?php echo esc_html( $product->get_title() ); ?></h2>
			<?php endif; ?>
			<div class="awooc-col">
				<?php
				if ( in_array( 'image', $elements ) ):
					$post_thumbnail_id = get_post_thumbnail_id( $product->get_id() );
					$full_size_image = wp_get_attachment_image_src( $post_thumbnail_id, 'shop_single' );
					?>
					<div class="awooc-form-custom-order-img">
						<img src="<?php echo esc_url( $full_size_image[0] ) ?>" alt="">
					</div>
				<?php endif;
				if ( in_array( 'price', $elements ) ):?>
					<div class="awooc-form-custom-order-price"></div>
				<?php endif;
				if ( in_array( 'sku', $elements ) ):?>
					<div class="awooc-form-custom-order-sku"></div>
				<?php endif; ?>
			</div>
			<div class="awooc-col">
				<?php if ( in_array( 'attr', $elements ) ):?>
					<div class="awooc-form-custom-order-attr"></div>
				<?php endif;
				if ( ! empty( get_option( 'woocommerce_awooc_select_form' ) ) ) :
					echo do_shortcode( '[contact-form-7 id="' .
					                   esc_attr( get_option( 'woocommerce_awooc_select_form' ) ) . '"]' );
				endif; ?>
			</div>
		</div>
	</div>
	<?php
}

add_action( 'wpcf7_mail_sent', 'awooc_created_order_after_mail_send', 10, 1 );
/**
 * Создание заказа при отправке письма
 *
 * @param $contact_data
 *
 * @throws WC_Data_Exception
 */
function awooc_created_order_after_mail_send( $contact_data ) {
	if ( 'yes' == get_option( 'woocommerce_awooc_mode_catalog' ) ) {
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
