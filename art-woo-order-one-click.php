<?php
/**
 * Plugin Name:       Art WooCommerce Order One Click
 * Plugin URI:        #
 * Description:
 * Version:           1.0
 * Author:            Artem Abramovich
 * Author URI:
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       woo-seo-addon
 * Domain Path:       /languages
 *
 * WC requires at least: 3.3.0
 * WC tested up to: 3.3.4
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
define( 'AWOOC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'AWOOC_PLUGIN_URI', plugin_dir_url( __FILE__ ) );
define( 'AWOOC_PLUGIN_VER', '1.0' );
add_action( 'wp_enqueue_scripts', 'awooc_enqueue_script_style', 100 );
function awooc_enqueue_script_style() {
	wp_enqueue_script( 'awooc-scrpts', AWOOC_PLUGIN_URI . 'assets/js/awooc-scrpts.js', array( 'jquery' ), AWOOC_PLUGIN_VER, true );
	wp_enqueue_style( 'awooc-styles', AWOOC_PLUGIN_URI . 'assets/css/awooc-styles.css', array(), AWOOC_PLUGIN_VER );
	wp_localize_script( 'awooc-scrpts', 'awooc_scrpts', array(
		'url'   => admin_url( 'admin-ajax.php' ),
		'nonce' => wp_create_nonce( 'awooc-nonce' ),
	) );
}

add_action( 'wp_ajax_awooc_ajax_variant_order', 'awooc_ajax_scrpts_callback' );
add_action( 'wp_ajax_nopriv_awooc_ajax_variant_order', 'awooc_ajax_scrpts_callback' );
function awooc_ajax_scrpts_callback() {
	
	if ( ! wp_verify_nonce( $_POST['nonce'], 'awooc-nonce' ) ) {
		wp_die( 'Данные отправлены с левого адреса' );
	}
	$product_var_id    = esc_attr( $_POST['id'] );
	$product           = wc_get_product( $product_var_id );
	$product_var_attrs = $product->get_attributes();
	$attr_name         = array();
	foreach ( $product_var_attrs as $attr => $value ) {
		$meta        = get_post_meta( $product_var_id, 'attribute_' . $attr, true );
		$term        = get_term_by( 'slug', $meta, $attr );
		$attr_name[] = $term->name;
	}
	$product_var_attr = implode( ',', $attr_name );
	wp_send_json( $product_var_attr );
	wp_die();
}

add_action( 'woocommerce_after_add_to_cart_button', 'awooc_add_custom_button' );
function awooc_add_custom_button() {
	global $product;
	?>
	<a href="#awooc-form-custom-order" data-value-product-id="<?php echo esc_attr( $product->get_id() ); ?>"
		class="awooc-custom-order button alt">Заказать</a>
	<?php
}

add_action( 'wp_footer', 'awooc_form_custom_order' );
function awooc_form_custom_order() {
	global $product;
	if ( ! is_product() ) {
		return;
	}
	?>
	<div id="awooc-form-custom-order" class="awooc-form-custom-order awooc-hide">
		<div class="awooc-custom-order-wrap">
			<div class="awooc-col">
				<?php
				$post_thumbnail_id = get_post_thumbnail_id( $product->get_id() );
				$full_size_image   = wp_get_attachment_image_src( $post_thumbnail_id, 'shop_single' );
				?>
				<div class="awooc-form-custom-order-img">
					<img src="<?php echo $full_size_image[0]; ?>" alt="">
				</div>
				<div class="awooc-form-custom-order-price"></div>
			</div>
			<div class="awooc-col">
				<h2 class="awooc-form-custom-order-title"><?php echo $product->get_title(); ?></h2>
				<div class="awooc-form-custom-order-attr"></div>
				<?php echo do_shortcode( '[contact-form-7 id="19" title="Страница контактов"]' ); ?>
			</div>
		</div>
	</div>
	<?php
}