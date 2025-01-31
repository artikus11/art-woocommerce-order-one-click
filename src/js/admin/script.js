jQuery( ( $ ) => {
	'use strict';
	/*global awooc_admin */
	const AWOOCADMIN = {
		xhr: false,
		$selectMode: $( '#woocommerce_awooc_mode_catalog' ),
		analyticData: {},

		init() {
			this.getDescription( this.$selectMode, this.$selectMode.val() );

			$( document ).on( 'change', '#woocommerce_awooc_mode_catalog', ( event ) => {
				const selectedValue = $( event.target ).val();
				this.getDescription( $( event.target ), selectedValue );
			} );
		},
		// eslint-disable-next-line camelcase
		getDescription( $element, selectedValue ) {
			const $description = $element.closest( '.forminp-select' ).find( '.description' );

			$description.css( {
				display: 'block',
				marginTop: '8px',
				maxWidth: '80%',
			} );
			/* eslint-disable camelcase */
			const descriptions = {
				dont_show_add_to_card: awooc_admin.mode_catalog,
				show_add_to_card: awooc_admin.mode_normal,
				in_stock_add_to_card: awooc_admin.mode_in_stock,
				no_stock_no_price: awooc_admin.mode_special,
			};
			/* eslint-enable camelcase */
			$description.text( descriptions[ selectedValue ] || '' );
		},
	};

	AWOOCADMIN.init();
} );
