/*global awooc_scripts_ajax */
/*global awooc_scripts_translate */
/*global awooc_scripts_settings */
/*global wc_add_to_cart_variation_params */
/*global wpcf7 */
jQuery( function( $ ) {
	'use strict';

	if ( typeof awooc_scripts_ajax === 'undefined' ) {
		// eslint-disable-next-line no-console
		console.warn( 'awooc_scripts_ajax not found' );
		return false;
	}

	if ( typeof awooc_scripts_translate === 'undefined' ) {
		// eslint-disable-next-line no-console
		console.warn( 'awooc_scripts_translate not found' );
		return false;
	}

	if ( typeof awooc_scripts_settings === 'undefined' ) {
		// eslint-disable-next-line no-console
		console.warn( 'awooc_scripts_settings not found' );
		return false;
	}

	if ( typeof wpcf7 === 'undefined' || wpcf7 === null ) {
		// eslint-disable-next-line no-console
		console.warn( 'На странице не существует объекта wpcf7. Что-то не так с темой...' );
		return false;
	}

	const AWOOC = {
		xhr: false,
		$button: $( '.awooc-button-js' ),
		$cfvswVariationsForm: $( '.cfvsw_variations_form:not(.variation-function-added' ),
		$buttonProduct: $( '.woocommerce-variation-add-to-cart .awooc-button-js' ),
		formId: Number( awooc_scripts_settings.popup.cf7_form_id ),
		analyticData: {},

		init() {
			if ( this.$cfvswVariationsForm !== undefined ) {
				AWOOC.addedToButtonAttributes();
			}

			$( document.body )
				.on( 'click', '.awooc-button-js', this.popup )

				.on( 'awooc_popup_ajax_trigger', this.removeSkeleton )

				.on( 'click', '.awooc-close, .blockOverlay', this.unBlock )

				.on( 'hide_variation', this.disableButton )

				.on( 'show_variation', this.enableButton )

				.on( 'wpcf7mailsent', this.sendSuccess )

				.on( 'wpcf7invalid', this.sendInvalid )

				.on( 'cfvswVariationLoad', this.addedToButtonAttributes )
				.on( 'astraInfinitePaginationLoaded', this.addedToButtonAttributes )
				.on( 'cfvswVariationLoad', this.addedToButtonAttributes )
				.on( 'click', '.cfvsw-swatches-option', function( e ) {
					AWOOC.onClickSwatchesOption( $( e.target ) );
				} );
		},

		addedToButtonAttributes() {
			AWOOC.$cfvswVariationsForm.each(
				function() {
					const thisForm = $( this );

					thisForm.wc_variation_form();
					if ( thisForm.attr( 'data-cfvsw-catalog' ) ) {
						return;
					}

					thisForm.on( 'found_variation', function( e, variation ) {
						AWOOC.updateButtonData( thisForm, variation );
					} );
				},
			);
		},

		updateButtonData( variant ) {
			const select = variant.find( '.variations select' );
			const data = {};
			const button = variant
				.parents( 'li' )
				.find( '.awooc-button-js' );

			select.each( function() {
				const attributeName =
					$( this ).data( 'attribute_name' ) || $( this ).attr( 'name' );

				data[ attributeName ] = $( this ).val() || '';
			} );

			button.addClass( 'cfvsw_variation_found' );
			button.attr( 'data-selected_variant', JSON.stringify( data ) );
		},

		resetButtonData( variant ) {
			const button = variant
				.parents( 'li' )
				.find( '.awooc-button-js' );

			button.html( button.data( 'select_options_text' ) );
			button.removeClass( 'cfvsw_variation_found' );
			button.attr( 'data-selected_variant', '' );
		},

		onClickSwatchesOption( swatch ) {
			if ( swatch.hasClass( 'cfvsw-selected-swatch' ) ) {
				swatch.removeClass( 'cfvsw-selected-swatch' );
				AWOOC.resetButtonData( swatch );
			} else {
				const parent = swatch.parent();
				parent.find( '.cfvsw-swatches-option' ).each( function() {
					$( this ).removeClass( 'cfvsw-selected-swatch' );
				} );

				swatch.addClass( 'cfvsw-selected-swatch' );
			}

			AWOOC.updateSelectOption( swatch );
		},

		updateSelectOption( swatch ) {
			const value = swatch.hasClass( 'cfvsw-selected-swatch' )
				? swatch.data( 'slug' )
				: '';
			const select = swatch
				.closest( '.cfvsw-swatches-container' )
				.prev()
				.find( 'select' );
			select.val( value ).change();
		},

		disableButton() {
			AWOOC.$button.addClass( 'disabled wc-variation-selection-needed' );
		},

		enableButton( e, variation, purchasable ) {
			if ( ! variation.is_in_stock ) {
				AWOOC.$buttonProduct.addClass( 'disabled wc-variation-is-unavailable' );
			} else {
				AWOOC.$buttonProduct.removeClass( 'disabled wc-variation-selection-needed' );
			}

			if ( awooc_scripts_settings.mode === 'dont_show_add_to_card' ) {
				AWOOC.$buttonProduct.removeClass( 'disabled wc-variation-selection-needed' );
			}

			switch ( awooc_scripts_settings.mode ) {
				case 'dont_show_add_to_card': // catalog
					// eslint-disable-next-line no-console
					console.log( variation );
					break;
				case 'show_add_to_card': // normal
					// eslint-disable-next-line no-console
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
					if ( ! purchasable ) {
						AWOOC.$button.removeClass( 'disabled wc-variation-selection-needed' );
						AWOOC.hideAddToCartModule();
					} else {
						AWOOC.showAddToCartModule();
					}
					break;
			}
		},

		hideAddToCartModule() {
			$( 'body.woocommerce' )
				.find( '.woocommerce-variation-add-to-cart .quantity' )
				.addClass( 'awooc-hide' );
			$( 'body.woocommerce' )
				.find( '.woocommerce-variation-add-to-cart .single_add_to_cart_button' )
				.addClass( 'awooc-hide' );
		},

		showAddToCartModule() {
			$( 'body.woocommerce' )
				.find( '.woocommerce-variation-add-to-cart .quantity' )
				.removeClass( 'awooc-hide' );
			$( 'body.woocommerce' )
				.find( '.woocommerce-variation-add-to-cart .single_add_to_cart_button' )
				.removeClass( 'awooc-hide' );
		},

		hideAwoocButton() {
			AWOOC.$button.addClass( 'awooc-hide' );
		},

		showAwoocButton() {
			AWOOC.$button.removeClass( 'awooc-hide' );
		},

		getProductID( e ) {
			const productVariantId = $( '.variations_form' ).find( 'input[name="variation_id"]' ).val();
			let selectedProductId = $( e.target ).attr( 'data-value-product-id' );

			// Проверяем ID товара, для вариаций свой, для простых свой.
			if ( 0 !== productVariantId && typeof productVariantId !== 'undefined' ) {
				selectedProductId = productVariantId;
			}

			return selectedProductId;
		},

		getQty() {
			return $( '.quantity' ).find( 'input[name="quantity"]' ).val() || 1;
		},

		unBlock() {
			$.unblockUI();
		},

		removeSkeleton() {
			$( '.awooc-popup-inner' )
				.find( '.awooc-popup-item' )
				.each( function( index, item ) {
					$( item ).removeClass( 'skeleton-loader' );
				} );
		},

		addedToMailData( toMail ) {
			const keys = Object.keys( toMail );

			let dataToMail = '\n' + awooc_scripts_translate.product_data_title + '\n———\n';

			keys.forEach( function( key ) {
				dataToMail += toMail[ key ] + '\n';
			} );

			return dataToMail;
		},

		addedToPopupData( toPopup ) {
			const keys = Object.keys( toPopup );

			keys.forEach( function( key ) {
				$( '.awooc-popup-' + key ).html( toPopup[ key ] );
			} );
		},

		sendSuccess( event ) {
			setTimeout( AWOOC.unBlock, awooc_scripts_settings.popup.mailsent_timeout );

			if ( AWOOC.formId === event.detail.contactFormId ) {
				$( document.body ).trigger(
					'awooc_mail_sent_trigger',
					{
						selectedProduct: AWOOC.analyticData,
						mailDetail: event.detail,
					},
				);
			}
		},

		sendInvalid( event, detail ) {
			if ( AWOOC.formId === event.detail.contactFormId ) {
				$( document.body ).trigger( 'awooc_mail_invalid_trigger', [ event, detail ] );
			}

			setTimeout( function() {
				$( '.awooc-form-custom-order .wpcf7-response-output' ).empty();
				$( '.awooc-form-custom-order .wpcf7-not-valid-tip' ).remove();
			}, awooc_scripts_settings.popup.invalid_timeout );
		},

		initMask() {
			const maskFields = $( '.awooc-form-custom-order .wpcf7-mask' );

			if ( ! maskFields.length ) {
				return;
			}

			maskFields.each( ( index, field ) => {
				const $this = $( field );
				const dataMask = $this.data( 'mask' );

				if ( ! dataMask ) {
					return;
				}

				try {
					$this.mask( dataMask );

					const hasAsterisk = dataMask.includes( '*' );
					const hasLetterA = dataMask.includes( 'a' );

					if ( ! hasAsterisk && ! hasLetterA ) {
						$this.attr( { inputmode: 'numeric' } );
					}
				} catch ( e ) {
					// eslint-disable-next-line no-console
					console.error( `Error: ${ e.name }: ${ e.message }\n${ e.stack }` );
				}
			} );
		},

		updateAmount( qtyVal, e, toMail ) {
			let priceValue = $( '.awooc-popup-price .woocommerce-Price-currencyValue' ).text();

			if ( priceValue ) {
				priceValue = priceValue.replace( awooc_scripts_settings.popup.price_decimal_sep, '.' );
				priceValue = priceValue.replace( /\s+/g, '' );

				let amount = parseFloat( priceValue.replace( awooc_scripts_settings.popup.price_decimal_sep, '.' ) ) * qtyVal;

				amount = amount
					.toFixed( awooc_scripts_settings.popup.price_num_decimals )
					.replace( '.', awooc_scripts_settings.popup.price_decimal_sep );

				amount = amount
					.toString()
					.replace( /\B(?=(\d{3})+(?!\d))/g, awooc_scripts_settings.popup.price_thousand_sep );

				$( e.target )
					.closest( '.awooc-form-custom-order' )
					.find( '.awooc-popup-sum .woocommerce-Price-currencyValue' )
					.text( amount );

				const currentAmountValue = $( e.target )
					.closest( '.awooc-form-custom-order' )
					.find( '.awooc-popup-sum bdi' )
					.text();

				toMail.sum = awooc_scripts_translate.formatted_sum + currentAmountValue;
			} else {
				delete toMail.sum;
			}
		},

		updateQty( toMail ) {
			$( '.awooc-popup-qty' )
				.on( 'input', 'input.awooc-popup-input-qty', function( e ) {
					const qtyVal = $( e.target ).val();

					toMail.qty = awooc_scripts_translate.product_qty + qtyVal;

					AWOOC.analyticData.qty = qtyVal;
					AWOOC.updateAmount( qtyVal, e, toMail );

					$( 'input[name="awooc-hidden-data"]' ).val( AWOOC.addedToMailData( toMail ) );
					$( 'input[name="awooc_product_qty"]' ).val( qtyVal );
				} );
		},

		request( e ) {
			const data = {
				id: AWOOC.getProductID( e ),
				action: 'awooc_ajax_product_form',
				nonce: awooc_scripts_ajax.nonce,
			};

			if ( $( e.target ).data( 'selected_variant' ) !== undefined ) {
				data.attributes = $( e.target ).data( 'selected_variant' );
			}

			$( e.target ).closest( '.cart' ).serializeArray().forEach( function( { name, value } ) {
				if ( data[ name ] ) {
					if ( ! Array.isArray( data[ name ] ) ) {
						data[ name ] = [ data[ name ] ];
					}
					data[ name ].push( value );
				} else {
					data[ name ] = value;
				}
			} );

			delete data[ 'add-to-cart' ];

			AWOOC.xhr = $.ajax( {
				url: awooc_scripts_ajax.url,
				data,
				type: 'POST',
				dataType: 'json',

				success: ( response ) => {
					const toPopup = response.data.toPopup;
					const toMail = response.data.toMail;

					AWOOC.addedToPopupData( toPopup );
					AWOOC.analyticData = response.data.toAnalytics;

					AWOOC.updateQty( toMail );

					AWOOC.initContactForm();
					AWOOC.initMask();

					$( 'input[name="awooc_product_id"]' ).val( AWOOC.getProductID( e ) );
					$( 'input[name="awooc_product_qty"]' ).val( AWOOC.getQty( e ) );
					$( 'input[name="awooc-hidden-data"]' ).val( AWOOC.addedToMailData( toMail ) );

					if ( $.magnificPopup !== undefined && $.magnificPopup.instance !== undefined ) {
						$.magnificPopup.close();
					}

					$( document.body ).trigger( 'awooc_popup_ajax_trigger', response );
				},

				error: ( response ) => {
					if ( response.responseJSON ) {
						// eslint-disable-next-line no-console
						console.error( response.responseJSON.data );
					}
				},
			} );
		},

		initContactForm() {
			$( '.awooc-form-custom-order div.wpcf7 > form' )
				.each( function() {
					const version = $( this ).find( 'input[name="_wpcf7_version"]' ).val();
					const isOldVersion = version.value && version.value <= '5.4';
					if ( isOldVersion ) {
						const $form = $( this );
						wpcf7.initForm( $form );
						if ( wpcf7.cached ) {
							wpcf7.refill( $form );
						}
					} else {
						wpcf7.init( this );
					}
				} );
		},

		popup( e ) {
			if ( $( this ).is( '.disabled' ) ) {
				e.preventDefault();

				if ( $( this ).is( '.wc-variation-is-unavailable' ) ) {
					// eslint-disable-next-line no-alert
					window.alert( wc_add_to_cart_variation_params.i18n_unavailable_text );
				} else if ( $( this ).is( '.wc-variation-selection-needed' ) ) {
					// eslint-disable-next-line no-alert
					window.alert( wc_add_to_cart_variation_params.i18n_make_a_selection_text );
				}

				return false;
			}

			$.blockUI( {
				message: awooc_scripts_settings.template,
				css: awooc_scripts_settings.popup.css,
				overlayCSS: awooc_scripts_settings.popup.overlay,
				fadeIn: awooc_scripts_settings.popup.fadeIn,
				fadeOut: awooc_scripts_settings.popup.fadeOut,
				focusInput: awooc_scripts_settings.popup.focusInput,
				bindEvents: false,
				timeout: 0,
				allowBodyStretch: true,
				centerX: true,
				centerY: true,
				blockMsgClass: 'blockMsg blockMsgAwooc',

				onBlock() {
					$( document.body ).trigger( 'awooc_popup_open_trigger' );

					AWOOC.request( e );
				},

				onUnblock() {
					$( document.body ).trigger( 'awooc_popup_close_trigger' );
				},

				onOverlayClick() {
					$( 'html' )
						.css( { overflow: 'initial' } );
				},
			} );
		},
	};

	AWOOC.init();
	window.AWOOC = AWOOC;
} );
