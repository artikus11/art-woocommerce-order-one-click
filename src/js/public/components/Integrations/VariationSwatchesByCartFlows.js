export default class VariationSwatchesByCartFlows {
	static CONFIG = {
		selectors: {
			forms: '.cfvsw_variations_form, form.variations_form',
			button: '.awooc-button-js',
			selects: '.variations select',
		},
		classes: {
			variationFound: 'cfvsw_variation_found',
		},
		events: {
			variationLoad: 'cfvswVariationLoad',
			paginationLoaded: 'astraInfinitePaginationLoaded',
			foundVariation: 'found_variation',
			resetData: 'reset_data',
			hideVariation: 'hide_variation',
		},
		ns: '.awooc',
	};

	constructor( app ) {
		this.app = app;
		this.variationForms = [];
	}

	init() {
		this.refreshFormsAndButtons();
		this.bindGlobalEvents();
	}

	/**
	 * Находит актуальные формы на странице и сбрасывает состояние кнопок.
	 */
	refreshFormsAndButtons() {
		const { selectors } = VariationSwatchesByCartFlows.CONFIG;
		this.variationForms = document.querySelectorAll( selectors.forms );

		if ( this.variationForms.length === 0 ) {
			return;
		}

		this.resetAllButtons();
		this.attachFormEvents();
	}

	/**
	 * Кастомные события CartFlows / Astra на ленивую загрузку
	 */
	bindGlobalEvents() {
		const { events } = VariationSwatchesByCartFlows.CONFIG;

		document.addEventListener( events.variationLoad, () => this.refreshFormsAndButtons() );
		document.addEventListener( events.paginationLoaded, () => this.refreshFormsAndButtons() );
	}

	/**
	 * Подписка на события изменения состояния вариаций WooCommerce
	 */
	attachFormEvents() {
		const {
			events,
			ns,
		} = VariationSwatchesByCartFlows.CONFIG;

		this.variationForms.forEach( ( form ) => {
			if ( ! form.dataset.awoocInitialized ) {
				jQuery( form ).wc_variation_form();
				form.dataset.awoocInitialized = '1';
			}

			if ( form.dataset.cfvswCatalog ) {
				return;
			}

			jQuery( form )
				.off( events.foundVariation + ns )
				.on( events.foundVariation + ns, ( event, variation ) => this.onVariationFound( form, variation ) );

			jQuery( form )
				.off( events.resetData + ns )
				.on( events.resetData + ns, () => this.onVariationReset( form ) );

			jQuery( form )
				.off( events.hideVariation + ns )
				.on( events.hideVariation + ns, () => this.onVariationReset( form ) );
		} );
	}

	onVariationFound( form, variation ) {
		const {
			selectors,
			classes,
		} = VariationSwatchesByCartFlows.CONFIG;
		const button = form.closest( 'li' )?.querySelector( selectors.button );

		if ( ! button ) {
			return;
		}

		const canOrder = variation && variation.is_purchasable && variation.variation_is_visible;

		if ( canOrder ) {
			const selectElements = form.querySelectorAll( selectors.selects );
			const data = {};

			selectElements.forEach( ( selectElement ) => {
				const attributeName = selectElement.dataset.attributeName || selectElement.name;
				data[ attributeName ] = selectElement.value || '';
			} );

			button.disabled = false;
			button.classList.add( classes.variationFound );

			button.dataset.selectedVariant = JSON.stringify( data );
		} else {
			this.clearButtonData( button );
		}
	}

	onVariationReset( form ) {
		const { selectors } = VariationSwatchesByCartFlows.CONFIG;
		const button = form.closest( 'li' )?.querySelector( selectors.button );

		if ( button ) {
			this.clearButtonData( button );
		}
	}

	clearButtonData( button ) {
		const { classes } = VariationSwatchesByCartFlows.CONFIG;
		button.disabled = true;
		button.classList.remove( classes.variationFound );
		button.dataset.selectedVariant = '';
	}

	resetAllButtons() {
		const { selectors } = VariationSwatchesByCartFlows.CONFIG;
		this.variationForms.forEach( ( form ) => {
			const button = form.closest( 'li' )?.querySelector( selectors.button );
			if ( button ) {
				this.clearButtonData( button );
			}
		} );
	}
}
