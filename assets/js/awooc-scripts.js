/**
 * Вывод данных
 *
 * @package art-woocommerce-order-one-click/assets/js
 */

jQuery( function( $ ) {

	'use strict';

	if ( typeof awooc_scripts_ajax === 'undefined' ) {
		console.log( 'awooc_scripts_ajax not found' );
		return false;
	}

	if ( typeof awooc_scripts_translate === 'undefined' ) {
		console.log( 'awooc_scripts_translate not found' );
		return false;
	}

	if ( typeof awooc_scripts_settings === 'undefined' ) {
		console.log( 'awooc_scripts_settings not found' );
		return false;
	}

	if ( typeof wpcf7 === 'undefined' || wpcf7 === null ) {
		console.log( 'На странице не существует объекта wpcf7. Что-то не так с темой...' );
		return false;
	}

	// Задаем переменные.
	const awoocBtn   = $( '.awooc-custom-order-button' ),
	      awoocPopup = $( '.awooc-form-custom-order' ),
	      orderTitle = $( '.awooc-form-custom-order-title' ),
	      orderImg   = $( '.awooc-form-custom-order-img' ),
	      orderPrice = $( '.awooc-form-custom-order-price' ),
	      orderQty   = $( '.awooc-form-custom-order-qty' ),
	      orderSku   = $( '.awooc-form-custom-order-sku' ),
	      orderAttr  = $( '.awooc-form-custom-order-attr' ),
	      orderSum  = $( '.awooc-form-custom-order-sum' ),
	      preload    = '<div class="awooc-preload-container"><div class="awooc-ajax-loader"></div></div>';

	let selectedProduct;

	$( document )

		.on( 'hide_variation', function( event ) {
			awoocBtn.addClass( 'disabled wc-variation-selection-needed' );

		} )

		.on( 'show_variation', function( event, variation ) {
			if ( false !== variation.is_in_stock ) {
				awoocBtn.removeClass( 'disabled wc-variation-selection-needed' );
			} else {
				awoocBtn.addClass( 'disabled wc-variation-is-unavailable' );
			}

			// Если у вариации нет цены или ее нет в наличие то скрываем сообщения.
			if ( awooc_scripts_settings.mode === 'no_stock_no_price' ) {
				if ( false === variation.is_purchasable || false === variation.is_in_stock ) {
					awoocBtn.removeClass( 'disabled wc-variation-selection-needed' );
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
		} )

		.on( 'click', '.awooc-custom-order-button', function( event ) {
			if ( $( this ).is( '.disabled' ) ) {
				event.preventDefault();

				if ( $( this ).is( '.wc-variation-is-unavailable' ) ) {
					window.alert( wc_add_to_cart_variation_params.i18n_unavailable_text );
				} else if ( $( this ).is( '.wc-variation-selection-needed' ) ) {
					window.alert( wc_add_to_cart_variation_params.i18n_make_a_selection_text );
				}

				return false;
			}

			let prodictSelectedId,
			    productVariantId = $( '.variations_form' ).find( 'input[name="variation_id"]' ).val(),
			    productId        = $( this ).attr( 'data-value-product-id' ),
			    productQty       = $( '.quantity' ).find( 'input[name="quantity"]' ).val() || 1,
			    dataOutForMail   = {};

			// Проверяем ID товара, для вариаций свой, для простых свой.
			if ( 0 !== productVariantId && typeof productVariantId !== 'undefined' ) {
				prodictSelectedId = productVariantId;
			} else {
				prodictSelectedId = productId;
			}

			// Собираем данные для отправки.
			let data = {
				id: prodictSelectedId,
				qty: productQty,
				action: 'awooc_ajax_product_form',
				nonce: awooc_scripts_ajax.nonce
			};

			// Отправляем запрос.
			$.ajax( {
					url: awooc_scripts_ajax.url,
					data: data,
					type: 'POST',
					dataType: 'json',
					beforeSend: function( xhr, data ) {
						// Вызываем прелоадер.
						$( event.currentTarget )
							.fadeIn( 200 )
							.prepend( preload );
					},
					success: function( data ) {

						// Отключаем прелоадер.
						$( event.currentTarget )
							.find( '.awooc-preload-container' )
							.remove();

						// Добавляем тайтл к кнопке закрытия окнa.
						$( '.awooc-close' ).attr( 'title', awooc_scripts_translate.title_close );

						// Проверяем данные после аякса и формируем нужные строки.
						dataOutForMail = {
							outID: 'ID: ' + prodictSelectedId,
							outTitle: data.title === false
								? ''
								: '\n' + awooc_scripts_translate.product_title + data.title,
							outAttr: data.attr === false ? '' : '\n' + data.attr,
							outSku: data.sku === false ? '' : '\n' + data.sku,
							outCat: data.cat === false ? '' : '\n' + data.cat,
							outLink: data.link === false ? '' : '\n' + awooc_scripts_translate.product_link + data.link,
							outPrice: data.price === false
								? ''
								: '\n' + awooc_scripts_translate.product_price + data.pricenumber,
							outQty: data.qty === false
								? ''
								: '\n' + awooc_scripts_translate.product_qty + productQty,
							outSum: data.sum === false
								? ''
								: '\n' + awooc_scripts_translate.product_sum + data.sumnumber
						};

						// Формируем данные.
						orderTitle.text( data.title );
						orderImg.html( data.image );
						orderPrice.html( data.price );
						orderQty.text( awooc_scripts_translate.product_qty + productQty );
						orderSku.html( data.sku );
						orderAttr.html( data.attr );
						orderSum.html( data.sum );

						// Загружаем форму.
						$( '.awooc-col.columns-right' ).html( data.form );

						// Инициализация формы.
						awoocInitContactForm();

						// Собираем данные для письма.
						let hiddenData = awoocHiddenDataToMail( dataOutForMail );

						// Записываем данные с скрытое поле для отправки письма.
						$( '.awooc-hidden-data' ).val( hiddenData );

						// Записываем id товара и количество в скрытое поле.
						$( '.awooc-hidden-product-id' ).val( prodictSelectedId );
						$( '.awooc-hidden-product-qty' ).val( productQty );

						// Проверка на наличчие маски телефона.
						awoocMaskField();

						// Выводим всплывающее окно.
						awoocPopupWindow( awoocPopup );

						// Данные для аналитики
						selectedProduct = {
							productId: prodictSelectedId,
							productName: data.title,
							productSku: data.productSku,
							productQty: productQty,
							productPrice: data.pricenumber,
							productCat: data.productCat,
							productAttr: data.productAttr ? data.productAttr.replace( /<\/?[^>]+>/g, '' ) : ''
						};

						// Событитие открытия окна.
						$( document.body ).trigger( 'awooc_popup_open_trigger', [ data ] );

						return data;
					}

				}
			);

			return false;
		} )

		.on( 'click', '.awooc-close', function() {
			// При клику на оверлей закрываем окно.
			$.unblockUI();

			// При клику на оверлей добавлем нужный класс.
			awoocPopup.hide();
		} )

		.on( 'click', '.blockOverlay', function() {
			// При клику на оверлей закрываем окно.
			$.unblockUI();

			// При клику на оверлей добавлем нужный класс.
			awoocPopup.hide();
		} )

		.on( 'wpcf7mailsent', function( detail ) {

			$( document.body ).trigger(
				'awooc_mail_sent_trigger',
				{
					'selectedProduct': selectedProduct,
					'mailDetail': detail
				}
			);

			setTimeout( $.unblockUI, 3000 );

			setTimeout( function() {
					$( '.awooc-form-custom-order .wpcf7-form' )[0].reset();
					$( '.awooc-form-custom-order .wpcf7-response-output' ).remove();
				}, 3000
			);
		} )

		.on( 'wpcf7invalid', function( event, detail ) {

			$( document.body ).trigger( 'awooc_mail_invalid_trigger', [ event, detail ] );

			setTimeout( function() {
				$( '.awooc-form-custom-order .wpcf7-response-output' ).empty();
				$( '.awooc-form-custom-order .wpcf7-not-valid-tip' ).remove();
			}, 5000 );

		} );


	function awoocInitContactForm() {

		$( '.awooc-form-custom-order div.wpcf7 > form' ).each( function() {

				let version = $( this ).find( 'input[name="_wpcf7_version"]' ).val();

				if ( ( typeof version !== 'undefined' && version !== null ) && version <= '5.4' ) {
					let $form = $( this );

					wpcf7.initForm( $form );

					if ( wpcf7.cached ) {
						wpcf7.refill( $form );
					}

				} else {
					wpcf7.init( this );

				}
			}
		);

	}


	function awoocHiddenDataToMail( dataOut ) {
		return '\n' + awooc_scripts_translate.product_data_title +
		       '\n ———' +
		       dataOut.outTitle +
		       '\n' + dataOut.outID +
		       dataOut.outCat.replace( /(<([^>]+)>)/ig, '' ) +
		       dataOut.outAttr.replace( /(<([^>]+)>)/ig, '' ) +
		       dataOut.outSku.replace( /(<([^>]+)>)/ig, '' ) +
		       dataOut.outPrice +
		       dataOut.outQty +
		       dataOut.outSum +
		       dataOut.outLink;
	}


	function awoocPopupWindow( popUp ) {
		$.blockUI( {
				message: popUp,
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
				onBlock: function() {
					popUp.show();
				},
				onUnblock: function() {
					// При закрытии окна добавлем нужный класс.
					popUp.hide();

					// При закрытии окна очищаем данные.
					awoocFormDataEmpty();

					// Событие закрытия окна.
					$( document.body ).trigger( 'awooc_popup_close_trigger' );

				},
				onOverlayClick: function() {

					// При закрытии окна добавлем нужный класс.
					popUp.hide();
					$( 'html' )
						.css( { 'overflow': 'initial' } );

					$.unblockUI();

					// При закрытии окна очищаем данные.
					awoocFormDataEmpty();

				}
			}
		);
	}


	function awoocFormDataEmpty() {
		orderTitle.empty();
		orderImg.empty();
		orderPrice.empty();
		orderQty.empty();
		orderSku.empty();
		orderAttr.empty();
	}


	function awoocMaskField() {
		let mask_fields = $( '.awooc-form-custom-order .wpcf7-mask' );
		if ( mask_fields.length > 0 ) {
			mask_fields.each( function() {
					let $this     = $( this ),
					    data_mask = $this.data( 'mask' );

					//Если ошибка определения, то выводим в консоль сообщение и продолжаем
					try {

						$this.mask( data_mask );

						if ( data_mask.indexOf( '*' ) === -1 && data_mask.indexOf( 'a' ) === -1 ) {
							$this.attr( {
								'inputmode': 'numeric'
							} );
						}

					} catch ( e ) {

						console.log( 'Error ' + e.name + ':' + e.message + '\n' + e.stack );

					}

				}
			);
		}
	}

} );