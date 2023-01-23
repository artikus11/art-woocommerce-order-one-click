/**
 * Вывод данных
 *
 * @package art-woocommerce-order-one-click/assets/js
 */

jQuery( function ( $ ) {

		'use strict';

		const AWOOCADMIN = {
			xhr:          false,
			$selectMode:  $( 'select#woocommerce_awooc_mode_catalog' ),
			analyticData: {},

			init: function () {

				this.getDescription( this.$selectMode, this.$selectMode.val() );

				$( document.body )
					.on( 'change', this.$selectMode, function ( e ) {
						let select_val = $( e.target ).val();
						AWOOCADMIN.getDescription( $( e.target ), select_val );
					} )

			},

			getDescription: function ( el, select_val ) {
				let desc = $( el ).closest( '.forminp-select' ).find( '.description' );

				$( desc ).css( {
					'display':    'block',
					'margin-top': '8px',
					'max-width':  '80%'
				} );

				switch ( select_val ) {
					case 'dont_show_add_to_card':
						$( desc ).text( awooc_admin.mode_catalog );
						break;
					case 'show_add_to_card':
						$( desc ).text( awooc_admin.mode_normal );
						break;
					case 'in_stock_add_to_card':
						$( desc ).text( awooc_admin.mode_in_stock );
						break;
					case 'no_stock_no_price':
						$( desc ).text( awooc_admin.mode_special );
						break;
				}
			}
		}


		AWOOCADMIN.init();

	},
);
