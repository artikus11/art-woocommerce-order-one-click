export default class Woodmart {
	constructor( app ) {
		this.app = app;
	}

	init() {
		jQuery( document.body ).on( 'woodmart-quick-view-displayed', () => {
			const button = document.querySelector( '.product.quick-shop-loaded form.cart .awooc-button-js' );

			if ( ! button ) {
				return;
			}

			const $product = jQuery( '.product.quick-shop-loaded' );
			$product.find( '.variations_form' )
				.on( 'hide_variation', () => this.app.buttons.toggleButtonClasses( [ button ], 'add', this.app.buttons.disableClasses ) )
				.on( 'show_variation', ( event, variation, purchasable ) => this.app.buttons.updateButtonState( variation, purchasable, [ button ] ) );
		} );
	}
}
