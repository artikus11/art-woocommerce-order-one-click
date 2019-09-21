/**
 * Вывод данных
 *
 * @package art-woocommerce-order-one-click/assets/js
 */

jQuery(
	function( $ )
	{

		$( 'select#woocommerce_awooc_mode_catalog' )
			.change(
				function()
				{

					let select_val = $( this )
						.val();

					$( this )
						.next()
						.next( '.description' )
						.css(
							{
								'display': 'block',
								'margin-top': '8px',
								'max-width': '80%',
							},
						);

					if ( 'dont_show_add_to_card' === select_val ) {
						$( this )
							.next()
							.next( '.description' )
							.text( awooc_admin.mode_catalog );
					} else if ( 'show_add_to_card' === select_val ) {
						$( this )
							.next()
							.next( '.description' )
							.text( awooc_admin.mode_normal );
					} else if ( 'in_stock_add_to_card' === select_val ) {
						$( this )
							.next()
							.next( '.description' )
							.text( awooc_admin.mode_in_stock );
					} else if ( 'no_stock_no_price' === select_val ) {
						$( this )
							.next()
							.next( '.description' )
							.text( awooc_admin.mode_special );
					}

				},
			)
			.change();
	},
);
