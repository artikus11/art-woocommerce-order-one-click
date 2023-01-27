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
		xhr:          false,
		$button:      $( '.awooc-button-js' ),
		formId:       Number( awooc_scripts_settings.popup.cf7_form_id ),
		analyticData: {},

		init: function () {

			$( document.body )
				.on( 'click', '.awooc-button-js', this.popup )

				.on( 'awooc_popup_ajax_trigger', this.removeSkeleton )

				.on( 'click', '.awooc-close, .blockOverlay', this.unBlock )

				.on( 'wc_variation_form', this.wc_variation_form )

				.on( 'hide_variation', this.disableButton )

				.on( 'show_variation', this.enableButton )

				.on( 'wpcf7mailsent', this.sendSuccess )

				.on( 'wpcf7invalid', this.sendInvalid )
		},

		wc_variation_form: function ( e ) {
			console.log( e );
			if ( awooc_scripts_settings.mode === 'in_stock_add_to_card' ) {
				//AWOOC.hideAwoocButton();
			}
		},

		disableButton: function () {
			AWOOC.$button.addClass( 'disabled wc-variation-selection-needed' )
		},

		enableButton: function ( e, variation, purchasable ) {

			if ( ! variation.is_in_stock ) {
				AWOOC.$button.addClass( 'disabled wc-variation-is-unavailable' )
			} else {
				AWOOC.$button.removeClass( 'disabled wc-variation-selection-needed' )
			}

			if ( awooc_scripts_settings.mode === 'dont_show_add_to_card' ) {
				AWOOC.$button.removeClass( 'disabled wc-variation-selection-needed' )
			}

			switch ( awooc_scripts_settings.mode ) {
				case 'dont_show_add_to_card': // catalog
					console.log( variation );
					break;
				case 'show_add_to_card': // normal
					console.log( variation );
					break;
				case 'in_stock_add_to_card':// preload
					if ( variation.backorders_allowed || ! variation.is_in_stock ) {
						AWOOC.$button.removeClass( 'disabled wc-variation-selection-needed' );
						AWOOC.hideAddToCartModule();
						AWOOC.showAwoocButton();
					} else {
						AWOOC.showAddToCartModule();
						AWOOC.hideAwoocButton();
					}
					break;
				case 'no_stock_no_price': // special
					console.log( purchasable );
					console.log( variation );
					console.log( variation.is_in_stock );
					if ( ! purchasable ) {
						console.log( 'hideAddToCartModule max_qty' )
						AWOOC.$button.removeClass( 'disabled wc-variation-selection-needed' );
						AWOOC.hideAddToCartModule();
					} else {
						console.log( 'showAddToCartModule max_qty' )
						AWOOC.showAddToCartModule();
					}
					/*if ( ! variation.is_in_stock ) {
					 console.log( variation );
					 console.log( 'hideAddToCartModule is_in_stock' )
					 AWOOC.$button.removeClass( 'disabled wc-variation-selection-needed' );
					 AWOOC.hideAddToCartModule();
					 } else {
					 console.log( 'showAddToCartModule is_in_stock' )
					 AWOOC.showAddToCartModule();
					 }*/
					break;
			}
		},

		hideAddToCartModule: function () {

			/*$( 'body.woocommerce' )
			 .find( '.single_variation' )
			 .addClass( 'awooc-hide'
			 )*/
			/*$( 'body.woocommerce' )
			 .find( '.quantity' ).addClass( 'awooc-hide' )*/
			$( 'body.woocommerce' )
				.find( '.woocommerce-variation-add-to-cart .quantity' )
				.addClass( 'awooc-hide' )
			$( 'body.woocommerce' )
				.find( '.woocommerce-variation-add-to-cart .single_add_to_cart_button' )
				.addClass( 'awooc-hide' )
		},

		showAddToCartModule: function () {

			/*$( 'body.woocommerce' )
			 .find( '.single_variation' )
			 .removeClass( 'awooc-hide' )*/
			$( 'body.woocommerce' )
				.find( '.woocommerce-variation-add-to-cart .quantity' )
				.removeClass( 'awooc-hide' )
			$( 'body.woocommerce' )
				.find( '.woocommerce-variation-add-to-cart .single_add_to_cart_button' )
				.removeClass( 'awooc-hide' )
		},

		hideAwoocButton: function ( e ) {
			AWOOC.$button.addClass( 'awooc-hide' )
		},

		showAwoocButton: function ( e ) {
			AWOOC.$button.removeClass( 'awooc-hide' )
		},

		getProductID: function ( e ) {

			const productVariantId = $( '.variations_form' ).find( 'input[name="variation_id"]' ).val()
			let selectedProductId  = $( e.target ).attr( 'data-value-product-id' );

			// Проверяем ID товара, для вариаций свой, для простых свой.
			if ( 0 !== productVariantId && typeof productVariantId !== 'undefined' ) {
				selectedProductId = productVariantId;
			}

			return selectedProductId;
		},

		getQty: function () {
			return $( '.quantity' ).find( 'input[name="quantity"]' ).val() || 1;
		},

		unBlock: function () {
			$.unblockUI();
		},

		removeSkeleton: function () {
			$( '.awooc-popup-inner' )
				.find( '.awooc-popup-item' )
				.each( function ( index, item ) {
					$( item ).removeClass( 'skeleton-loader' )
				} );
		},

		addedToMailData: function ( response ) {

			const toMail = response.data.toMail;
			const keys   = Object.keys( toMail );

			let dataToMail = '\n' + awooc_scripts_translate.product_data_title + '\n———\n';

			keys.forEach( function ( key ) {
				dataToMail += toMail[ key ] + '\n';
			} );

			return dataToMail;
		},

		addedToPopupData: function ( response ) {
			const toPopup = response.data.toPopup;
			const keys    = Object.keys( toPopup );

			keys.forEach( function ( key ) {
				$( '.awooc-popup-' + key ).html( toPopup[ key ] );
			} );
		},

		sendSuccess: function ( event ) {

			setTimeout( AWOOC.unBlock, awooc_scripts_settings.popup.mailsent_timeout );

			if ( AWOOC.formId === event.detail.contactFormId ) {

				$( document.body ).trigger(
					'awooc_mail_sent_trigger',
					{
						'selectedProduct': AWOOC.analyticData,
						'mailDetail':      event.detail
					}
				);
			}

		},

		sendInvalid: function ( event, detail ) {
			if ( AWOOC.formId === event.detail.contactFormId ) {
				$( document.body ).trigger( 'awooc_mail_invalid_trigger', [ event, detail ] );
			}

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
					AWOOC.initContactForm();
					AWOOC.initMask();

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
					     )
					     && version <= '5.4' ) {

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

				return false;
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

	AWOOC.init();

} );