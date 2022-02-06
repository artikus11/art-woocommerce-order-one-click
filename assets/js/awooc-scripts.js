/**
 * Вывод данных
 *
 * @package art-woocommerce-order-one-click/assets/js
 */

jQuery( function ( $ ) {

	'use strict';

	if ( typeof awooc_scripts_ajax === 'undefined' ) {
		console.warn( 'awooc_scripts_ajax not found' );
		return false;
	}

	if ( typeof awooc_scripts_translate === 'undefined' ) {
		console.warn( 'awooc_scripts_translate not found' );
		return false;
	}

	if ( typeof awooc_scripts_settings === 'undefined' ) {
		console.warn( 'awooc_scripts_settings not found' );
		return false;
	}

	if ( typeof wpcf7 === 'undefined' || wpcf7 === null ) {
		console.warn( 'На странице не существует объекта wpcf7. Что-то не так с темой...' );
		return false;
	}


	const AWOOC = {
		xhr: false,
		$button:      $( '.awooc-button-js' ),
		analyticData: {},

		init: function () {

			$( document.body )
				.on( 'click', '.awooc-button-js', this.popup )

				.on( 'awooc_popup_ajax_trigger', this.removeSkeleton )

				.on( 'click', '.awooc-close, .blockOverlay', this.unBlock )

				.on( 'hide_variation', this.disableButton )

				.on( 'show_variation', this.enableButton )

				.on( 'wpcf7mailsent', this.sendSuccess )

				.on( 'wpcf7invalid', this.sendInvalid )
		},

		disableButton: function ( e ) {
			AWOOC.$button.addClass( 'disabled wc-variation-selection-needed' )
		},

		enableButton: function ( e, variation ) {

			if ( false !== variation.is_in_stock ) {
				AWOOC.$button.removeClass( 'disabled wc-variation-selection-needed' )
			} else {
				AWOOC.$button.addClass( 'disabled wc-variation-is-unavailable' )
			}

			// Если у вариации нет цены или ее нет в наличие то скрываем сообщения.
			if ( awooc_scripts_settings.mode === 'no_stock_no_price' ) {
				if ( false === variation.is_purchasable || false === variation.is_in_stock ) {
					AWOOC.$button.removeClass( 'disabled wc-variation-selection-needed' );
					$( 'body.woocommerce' )
						.find( '.single_variation' )
						.hide();
					$( 'body.woocommerce' )
						.find( '.quantity' )
						.hide();
					$( 'body.woocommerce' )
						.find( '.woocommerce-variation-add-to-cart .single_add_to_cart_button' )
						.hide();

				}
			}
		},

		getProductID: function ( e ) {

			const productVariantId = $( '.variations_form' ).find( 'input[name="variation_id"]' ).val()
			let selectedProductId  = $( e.target ).attr( 'data-value-product-id' );

			// Проверяем ID товара, для вариаций свой, для простых свой.
			if ( 0 !== productVariantId && typeof productVariantId !== 'undefined' ) {
				selectedProductId = productVariantId;
			}

			return selectedProductId
		},

		getQty: function ( e ) {
			return $( '.quantity' ).find( 'input[name="quantity"]' ).val() || 1
		},

		unBlock: function () {
			$.unblockUI()
		},

		removeSkeleton: function () {
			$( '.awooc-popup-inner' )
				.find( '.awooc-popup-item' )
				.each( function ( index, item ) {
					$( item ).removeClass( 'skeleton-loader' )
				} )
		},

		addedToMailData: function ( response ) {

			const toMail = response.data.toMail
			const keys   = Object.keys( toMail );

			let dataToMail = '\n' + awooc_scripts_translate.product_data_title + '\n———\n'

			keys.forEach( key => {
				dataToMail += toMail[ key ] + '\n'
			} );

			return dataToMail
		},

		addedToPopupData: function ( response ) {
			const toPopup = response.data.toPopup
			const keys    = Object.keys( toPopup );

			keys.forEach( key => {
				$( '.awooc-popup-' + key ).html( toPopup[ key ] )
			} );
		},

		sendSuccess: function ( detail ) {

			setTimeout( AWOOC.unBlock, awooc_scripts_settings.popup.mailsent_timeout );

			$( document.body ).trigger(
				'awooc_mail_sent_trigger',
				{
					'selectedProduct': AWOOC.analyticData,
					'mailDetail':      detail
				}
			);
		},

		sendInvalid: function ( event, detail ) {
			$( document.body ).trigger( 'awooc_mail_invalid_trigger', [ event, detail ] );

			setTimeout( function () {
				$( '.awooc-form-custom-order .wpcf7-response-output' ).empty();
				$( '.awooc-form-custom-order .wpcf7-not-valid-tip' ).remove();
			}, awooc_scripts_settings.popup.invalid_timeout );

		},

		initMask: function () {
			const mask_fields = $( '.awooc-form-custom-order .wpcf7-mask' );
			if ( mask_fields.length > 0 ) {
				mask_fields.each( function () {
						let $this     = $( this ),
						    data_mask = $this.data( 'mask' );

						try {
							$this.mask( data_mask );

							if ( data_mask.indexOf( '*' ) === -1 && data_mask.indexOf( 'a' ) === -1 ) {
								$this.attr( {
									'inputmode': 'numeric'
								} );
							}

						} catch ( e ) {
							console.error( 'Error ' + e.name + ':' + e.message + '\n' + e.stack );
						}

					}
				);
			}
		},

		request: function ( e ) {
			let data = {
				id:     AWOOC.getProductID( e ),
				qty:    AWOOC.getQty( e ),
				action: 'awooc_ajax_product_form',
				nonce:  awooc_scripts_ajax.nonce
			};

			AWOOC.xhr = $.ajax( {
				url:      awooc_scripts_ajax.url,
				data:     data,
				type:     'POST',
				dataType: 'json',

				success: function ( response ) {

					AWOOC.addedToPopupData( response );
					AWOOC.analyticData = response.data.toAnalytics;
					AWOOC.initContactForm()
					AWOOC.initMask()

					$( 'textarea.awooc-hidden-data' ).val( AWOOC.addedToMailData( response ) );
					$( '.awooc-hidden-product-id' ).val( AWOOC.getProductID( e ) );
					$( '.awooc-hidden-product-qty' ).val( AWOOC.getQty( e ) );

					$( document.body ).trigger( 'awooc_popup_ajax_trigger', response );
				},

				error: function ( response ) {
					if ( response.responseJSON ) {
						console.error( response.responseJSON.data );
					}

				}
			} )
		},

		initContactForm: function () {

			$( '.awooc-form-custom-order div.wpcf7 > form' ).each( function () {

					let version = $( this ).find( 'input[name="_wpcf7_version"]' ).val();

					if ( (
					     typeof version !== 'undefined' && version !== null
					     ) && version <= '5.4' ) {
						let $form = $( this );

						wpcf7.initForm( $form );

						if ( wpcf7.cached ) {
							wpcf7.refill( $form );
						}

					} else {
						wpcf7.init( this );

					}
				}
			)

		},

		popup: function ( e ) {

			if ( $( this ).is( '.disabled' ) ) {
				e.preventDefault();

				if ( $( this ).is( '.wc-variation-is-unavailable' ) ) {
					window.alert( wc_add_to_cart_variation_params.i18n_unavailable_text );
				} else if ( $( this ).is( '.wc-variation-selection-needed' ) ) {
					window.alert( wc_add_to_cart_variation_params.i18n_make_a_selection_text );
				}

				return false
			}


			$.blockUI( {
					message:          awooc_scripts_settings.template,
					css:              awooc_scripts_settings.popup.css,
					overlayCSS:       awooc_scripts_settings.popup.overlay,
					fadeIn:           awooc_scripts_settings.popup.fadeIn,
					fadeOut:          awooc_scripts_settings.popup.fadeOut,
					focusInput:       awooc_scripts_settings.popup.focusInput,
					bindEvents:       false,
					timeout:          0,
					allowBodyStretch: true,
					centerX:          true,
					centerY:          true,
					blockMsgClass:    'blockMsg blockMsgAwooc',

					onBlock: function () {
						$( document.body ).trigger( 'awooc_popup_open_trigger' );

						AWOOC.request( e );
					},

					onUnblock: function () {
						$( document.body ).trigger( 'awooc_popup_close_trigger' );
					},

					onOverlayClick: function () {
						$( 'html' )
							.css( { 'overflow': 'initial' } );
					}
				}
			);
		}

	}

	AWOOC.init()

} );