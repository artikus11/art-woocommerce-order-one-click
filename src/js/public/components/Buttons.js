import { settings } from '../config.js';

/*global wc_add_to_cart_variation_params */
export default class Buttons {
	constructor( app ) {
		this.app = app;
		this.button = document.querySelectorAll( 'form.cart .awooc-button-js' );
		this.addToCartElements = document.querySelectorAll( '.woocommerce-variation-add-to-cart .quantity, .woocommerce-variation-add-to-cart .single_add_to_cart_button' );
		this.disableClasses = [ 'disabled', 'wc-variation-selection-needed' ];
		this.disableClassesOutStock = [ 'disabled', 'wc-variation-is-unavailable' ];
	}

	init() {
		document.addEventListener( 'click', ( e ) => this.handleShowPopup( e ) );

		jQuery( document.body )
			.on( 'hide_variation', () => this.toggleButtonClasses( this.button, 'add', this.disableClasses ) )
			.on( 'show_variation', ( event, variation, purchasable ) => this.updateButtonState( variation, purchasable ) );
	}

	handleShowPopup( e ) {
		const button = e.target.closest( '.awooc-button-js' );
		if ( ! button ) {
			return;
		}

		if ( button.classList.contains( 'disabled' ) ) {
			e.preventDefault();
			const message = button.classList.contains( 'wc-variation-is-unavailable' )
				? wc_add_to_cart_variation_params.i18n_unavailable_text
				: wc_add_to_cart_variation_params.i18n_make_a_selection_text;
			// eslint-disable-next-line no-alert
			window.alert( message );
			return false;
		}

		this.app.popup.showPopup( e );
	}

	updateButtonState( variation, purchasable, button = null ) {
		const targetButton = button || this.button;

		switch ( settings.mode ) {
			case 'in_stock_add_to_card': // preload
				this.handlePreloadMode( variation, targetButton );
				break;

			case 'no_stock_no_price': // special
				this.handleSpecialMode( variation, targetButton );
				break;

			default:
				this.handleDefaultMode( purchasable, targetButton );
				break;
		}
	}

	handlePreloadMode( variation, button = null ) {
		const targetButton = button || this.button;

		if ( variation.backorders_allowed || ! variation.is_in_stock ) {
			this.hideAddToCartModule();
			this.toggleButtonClasses( targetButton, 'remove', this.disableClassesOutStock );
		} else {
			this.showAddToCartModule();
			this.toggleButtonClasses( targetButton, 'add', this.disableClasses );
		}
	}

	handleSpecialMode( variation, button = null ) {
		const targetButton = button || this.button;

		if ( variation.is_purchasable && ! variation.is_in_stock ) {
			this.hideAddToCartModule();
			this.toggleButtonClasses( targetButton, 'remove', this.disableClasses );
		} else if ( variation.is_purchasable && variation.is_in_stock ) {
			this.showAddToCartModule();
			this.toggleButtonClasses( targetButton, 'remove', this.disableClasses );
		} else {
			this.showAddToCartModule();
			this.toggleButtonClasses( targetButton, 'add', this.disableClasses );
		}
	}

	handleDefaultMode( purchasable, button = null ) {
		const targetButton = button || this.button;

		this.toggleButtonClasses( targetButton, purchasable ? 'remove' : 'add', this.disableClassesOutStock );
	}

	toggleButtonClasses( buttons, action, classes ) {
		if ( ! buttons ) {
			buttons = this.button;
		}

		if ( buttons instanceof NodeList ) {
			buttons = Array.from( buttons );
		}

		if ( ! Array.isArray( buttons ) ) {
			buttons = [ buttons ];
		}

		buttons.forEach( ( btn ) => {
			if ( btn && btn.classList ) {
				classes.forEach( ( cls ) => {
					if ( typeof cls === 'string' ) {
						btn.classList[ action ]( cls );
					}
				} );
			}
		} );
	}

	toggleModuleVisibility( action ) {
		this.addToCartElements.forEach( ( el ) => el.classList[ action ]( 'awooc-hide' ) );
		this.button.forEach( ( btn ) => btn.classList[ action ]( 'no-margin' ) );
	}

	hideAddToCartModule() {
		this.toggleModuleVisibility( 'add' );
	}

	showAddToCartModule() {
		this.toggleModuleVisibility( 'remove' );
	}
}
