export default class VariationSwatchesByCartFlows {
	constructor( app ) {
		this.app = app;
		this.variationForms = document.querySelectorAll( '.cfvsw_variations_form, form.variations_form' );
		this.buttons = this.getButtons();
	}

	getButtons() {
		return Array.from( this.variationForms )
			.map( ( form ) => form.closest( 'li' ) )
			.filter( ( li ) => li !== null )
			.flatMap( ( li ) => Array.from( li.querySelectorAll( '.awooc-button-js' ) ) );
	}

	init() {
		if ( this.variationForms.length < 0 ) {
			return;
		}

		this.addedToButtonAttributes();
		this.disableButtons();

		this.bindEvents();
	}

	bindEvents() {
		document.addEventListener( 'cfvswVariationLoad', () => this.addedToButtonAttributes() );
		document.addEventListener( 'astraInfinitePaginationLoaded', () => this.addedToButtonAttributes() );

		const swatchesOptions = document.querySelectorAll( '.cfvsw-swatches-option' );

		swatchesOptions.forEach( ( swatch ) => {
			swatch.addEventListener( 'click', ( e ) => this.onClickSwatchesOption( e ) );
		} );
	}

	addedToButtonAttributes() {
		this.variationForms.forEach( ( form ) => {
			jQuery( form ).wc_variation_form();

			if ( form.dataset.cfvswCatalog ) {
				return;
			}

			jQuery( form ).on( 'found_variation', () => this.updateButtonData( form ) );
		} );
	}

	updateButtonData( variant ) {
		const selectElements = variant.querySelectorAll( '.variations select' );
		const data = {};
		const button = variant.closest( 'li' )?.querySelector( '.awooc-button-js' );

		selectElements.forEach( ( selectElement ) => {
			const attributeName = selectElement.dataset.attributeName || selectElement.name;
			data[ attributeName ] = selectElement.value || '';
		} );

		if ( button ) {
			button.disabled = false;
			button.classList.add( 'cfvsw_variation_found' );
			button.dataset.selectedVariant = JSON.stringify( data );
		}
	}

	onClickSwatchesOption( e ) {
		const swatch = e.target;

		if ( this.isSwatchSelected( swatch ) ) {
			this.deselectSwatch( swatch );
			this.resetButtonData( swatch );
		} else {
			this.deselectAllSwatches( swatch );
			this.selectSwatch( swatch );
		}

		this.updateSelectOption( swatch );
	}

	resetButtonData( swatch ) {
		const button = swatch.closest( 'li' )?.querySelector( '.awooc-button-js' );

		if ( ! button ) {
			return;
		}

		button.disabled = true;
		button.classList.remove( 'cfvsw_variation_found' );
		button.dataset.selectedVariant = '';
	}

	updateSelectOption( swatch ) {
		const value = this.getSwatchValue( swatch );
		const select = this.getSelectElement( swatch );

		if ( select ) {
			select.value = value;
			select.dispatchEvent( new Event( 'change' ) );
		}
	}

	isSwatchSelected( swatch ) {
		return (
			( ! swatch.classList.contains( 'cfvsw-swatches-disabled' ) || ! swatch.classList.contains( 'cfvsw-swatches-out-of-stock' ) ) &&
			swatch.classList.contains( 'cfvsw-selected-swatch' )
		);
	}

	deselectSwatch( swatch ) {
		swatch.classList.remove( 'cfvsw-selected-swatch' );
	}

	deselectAllSwatches( swatch ) {
		swatch.parentElement
			.querySelectorAll( '.cfvsw-swatches-option' )
			.forEach( ( option ) => option.classList.remove( 'cfvsw-selected-swatch' ) );
	}

	selectSwatch( swatch ) {
		swatch.classList.add( 'cfvsw-selected-swatch' );
	}

	getSwatchValue( swatch ) {
		return this.isSwatchSelected( swatch ) ? swatch.dataset.slug : '';
	}

	getSelectElement( swatch ) {
		return swatch.closest( '.cfvsw-swatches-container' )?.previousElementSibling?.querySelector( 'select' );
	}

	disableButtons() {
		this.buttons.forEach( ( button ) => ( button.disabled = true ) );
	}
}
