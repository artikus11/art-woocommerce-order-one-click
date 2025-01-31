import { ajax } from '../../config';

export default class DataCollector {
	constructor( request ) {
		this.request = request;
		this.defaultAttributes = {};
	}

	collectData( event ) {
		const data = this.getBaseData( event );

		this.setAttributesOnCatalog( event, data );
		this.serializeFormData( event, data );

		return data;
	}

	getBaseData( event ) {
		return {
			id: this.request.getProductID( event ),
			action: 'awooc_ajax_product_form',
			nonce: ajax.nonce,
			attributes: { ...this.defaultAttributes },
		};
	}

	setAttributesOnCatalog( event, data ) {
		data.attributes = { ...this.defaultAttributes };
		const { selectedVariant } = event.target.dataset;

		if ( selectedVariant ) {
			data.attributes = JSON.parse( selectedVariant );
		}
	}

	serializeFormData( event, data ) {
		const form = event.target.closest( '.cart' );

		if ( ! form ) {
			return;
		}

		data.attributes = { ...this.defaultAttributes };

		this.processFormData( new FormData( form ), data );
	}

	processFormData( formData, data ) {
		formData.forEach( ( value, name ) => {
			this.updateDataField( data, name, value );

			if ( name.startsWith( 'attribute_' ) ) {
				this.updateDataField( data.attributes, name, value );
			}
		} );

		delete data[ 'add-to-cart' ];
	}

	updateDataField( dataObject, name, value ) {
		dataObject[ name ] = dataObject[ name ]
			? [].concat( dataObject[ name ], value )
			: value;
	}
}
