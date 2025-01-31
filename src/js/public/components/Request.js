import { ajax, translate } from '../config';
import UpdateQuantity from './RequestProcessing/UpdateQuantity';
import DataCollector from './RequestProcessing/DataCollector';

export default class Request {
	constructor( app ) {
		this.app = app;
		this.$ = this.app.$;
		this.dataCollector = new DataCollector( this );
	}

	sendRequest( e ) {
		const data = this.dataCollector.collectData( e );

		this.app.xhr = this.$.ajax( {
			url: ajax.url,
			data,
			type: 'POST',
			dataType: 'json',
			success: ( response ) => this.handleSuccessResponse( response, e ),
			error: ( response ) => this.handleErrorResponse( response ),
		} );
	}

	getProductID( e ) {
		const variationsForm = e.target.closest( '.variations_form' );
		const variationId = variationsForm?.querySelector( 'input[name="variation_id"]' )?.value;

		return variationId ?? e.target.dataset.valueProductId;
	}

	handleSuccessResponse( response, e ) {
		this.removeSkeleton();
		const { toMail, toPopup, toAnalytics } = response.data;

		this.fillDataToPopup( toPopup );
		this.fillHiddenFormFields( toMail, e );
		new UpdateQuantity( toMail, this, e ).bindEvent();

		this.app.analyticData = toAnalytics;

		this.initUI();

		this.closeMagnificPopup();

		this.app.events.trigger( 'awooc_popup_ajax_trigger', response );
	}

	handleErrorResponse( response ) {
		if ( response.responseJSON ) {
			// eslint-disable-next-line no-console
			console.error( response.responseJSON.data );
		}
	}

	fillDataToMail( data ) {
		return `\n${ translate.product_data_title }\n———\n${ Object.values( data ).join( '\n' ) }`;
	}

	fillDataToPopup( data ) {
		Object.entries( data ).forEach( ( [ key, value ] ) => {
			const element = document.querySelector( `.awooc-popup-${ key }` );
			if ( element ) {
				element.innerHTML = value;
			}
		} );
	}

	fillHiddenFormFields( data, e ) {
		this.updateField( 'awooc_product_id', this.getProductID( e ) );
		this.updateField( 'awooc_product_qty', this.getQty() );
		this.updateField( 'awooc-hidden-data', this.fillDataToMail( data ) );
	}

	updateField( name, value ) {
		const field = document.querySelector( `input[name="${ name }"]` );
		if ( field ) {
			field.value = value;
		}
	}

	initUI() {
		this.app.form.initContactForm();
		this.app.form.initMask();
	}

	removeSkeleton() {
		document.querySelectorAll( '.awooc-popup-inner .awooc-popup-item' ).forEach( ( item ) => {
			item.classList.remove( 'skeleton-loader' );
		} );
	}

	closeMagnificPopup() {
		if ( this.$.magnificPopup?.instance ) {
			this.$.magnificPopup.close();
		}
	}

	getQty() {
		const qty = document.querySelector( '.quantity input[name="quantity"]' );
		if ( qty ) {
			return qty.value;
		}
		return 1;
	}
}
