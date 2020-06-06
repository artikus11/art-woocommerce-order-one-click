/**
 * Вывод данных
 *
 * @package art-woocommerce-order-one-click/assets/js
 */

jQuery( function( $ ) {

	if ( typeof awooc_scripts === 'undefined' ) {
		console.log( 'awooc_scripts not found' );
		return false;
	}

	// Задаем переменные.
	const awoocBtn   = $( '.awooc-custom-order.button' ),
			orderTitle = $( '.awooc-form-custom-order-title' ),
			orderImg   = $( '.awooc-form-custom-order-img' ),
			orderPrice = $( '.awooc-form-custom-order-price' ),
			orderQty   = $( '.awooc-form-custom-order-qty' ),
			orderSku   = $( '.awooc-form-custom-order-sku' ),
			orderAttr  = $( '.awooc-form-custom-order-attr' ),
			preload    = '<div class="cssload-container"><div class="cssload-speeding-wheel"></div></div>';

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
			if ( awooc_scripts.mode === 'no_stock_no_price' ) {
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
				 productQty       = $( '.quantity' ).find( 'input[name="quantity"]' ).val(),
				 dataOut          = {};

			// Проверяем ID товара, для вариаций свой, для простых свой.
			if ( 0 !== productVariantId && typeof productVariantId !== 'undefined' ) {
				prodictSelectedId = productVariantId;
			} else {
				prodictSelectedId = productId;
			}

			// Собираем данные для отправки.
			let data = {
				id: prodictSelectedId,
				action: 'awooc_ajax_product_form',
				nonce: awooc_scripts.nonce,
			};

			// Отправляем запрос.
			let request = $.ajax( {
					url: awooc_scripts.url,
					data: data,
					type: 'POST',
					dataType: 'json',
					beforeSend: function( xhr, data ) {
						// Вызываем прелоадер.
						$( event.currentTarget ).block( {
								message: null,
								overlayCSS: {
									opacity: 0.6,
								},
							},
						);
						$( event.currentTarget )
							.fadeIn( 200 )
							.prepend( preload );
					},
					success: function( data ) {

						// Отключаем прелоадер.
						$( event.currentTarget ).unblock();
						$( event.currentTarget )
							.find( '.cssload-container' )
							.remove();

						// Добавляем тайтл к кнопке закрытия окнa.
						$( '.awooc-close' ).attr( 'title', awooc_scripts.title_close );

						// Проверяем данные после аякса и формируем нужные строки.
						dataOut = {
							outID: 'ID: ' + prodictSelectedId,
							outTitle: data.title === false
								? ''
								: '\n' + awooc_scripts.product_title + data.title,
							outAttr: data.attr === false ? '' : '\n' + data.attr,
							outPrice: data.price === false
								? ''
								: '\n' + awooc_scripts.product_price + data.pricenumber,
							outSku: data.sku === false ? '' : '\n' + data.sku,
							outCat: data.cat === false ? '' : '\n' + data.cat,
							outLink: data.link === false ? '' : '\n' + data.link,
							outQty: data.qty === false
								? ''
								: '\n' + awooc_scripts.product_qty + productQty,
						};

						// Формируем данные.
						orderTitle.text( data.title );
						orderImg.html( data.image );
						orderPrice.html( data.price );
						orderPrice.after( data.qty );
						orderQty.text( awooc_scripts.product_qty + productQty );
						orderSku.html( data.sku );
						orderAttr.html( data.attr );

						// Загружаем форму.
						$( '.awooc-col.columns-right' ).html( data.form );

						// Инициализация формы.
						awoocInitContactForm();

						// Собираем данные для письма.
						let hiddenData = awoocHiddenDataToMail( dataOut );

						// Записываем данные с скрытое поле для отправки письма.
						$( '.awooc-hidden-data' ).val( hiddenData );

						// Записываем id товара и количество в скрытое поле.
						$( '.awooc-hidden-product-id' ).val( prodictSelectedId );
						$( '.awooc-hidden-product-qty' ).val( productQty );

						// Проверка на наличчие маски телефона.
						awoocMaskField();

						// Выводим всплывающее окно.
						awoocPopupWindow();

						// Данные для аналитики
						selectedProduct = {
							productId: prodictSelectedId,
							productName: data.title,
							productSku: data.productSku,
							productQty: productQty,
							productPrice: data.pricenumber,
							productCat: data.productCat,
							productAttr: data.productAttr ? data.productAttr.replace( /<\/?[^>]+>/g, '' ) : '',
						};

						// Событитие открытия окна.
						$( document.body ).trigger( 'awooc_popup_open_trigger', [ data ] );

						return data;
					},

				},
			);

			return false;
		} )

		.on( 'click', '.awooc-close', function() {
			// При клику на оверлей закрываем окно.
			$.unblockUI();

			// При клику на оверлей добавлем нужный класс.
			$( '#awooc-form-custom-order' ).addClass( 'awooc-hide' );
		} )

		.on( 'wpcf7mailsent', function( detail ) {

			$( document.body ).trigger(
				'awooc_mail_sent_trigger',
				{
					'selectedProduct': selectedProduct,
					'mailDetail': detail,
				},
			);

			setTimeout( $.unblockUI, 3000 );

			setTimeout( function() {
					$( '.awooc-form-custom-order .wpcf7-form' )[0].reset();
					$( '.awooc-form-custom-order .wpcf7-response-output' ).remove();
				}, 3000,
			);
		} )

		.on( 'wpcf7invalid', function( event, detail ) {

			$( document.body ).trigger( 'awooc_mail_invalid_trigger', [ event, detail ] );

			setTimeout( function() {
				$( '.awooc-form-custom-order .wpcf7-response-output' ).remove();
				$( '.awooc-form-custom-order .wpcf7-not-valid-tip' ).remove();
			}, 5000 );

		} );


	function awoocInitContactForm() {
		$( '.awooc-form-custom-order div.wpcf7 > form' )
			.each( function() {
					let $form = $( this );

					if ( typeof wpcf7 === 'undefined' || wpcf7 === null ) {
						console.log( 'На странице не существует объекта wpcf7. Что-то не так с темой...' );
					} else {
						wpcf7.initForm( $form );
						if ( wpcf7.cached ) {
							wpcf7.refill( $form );
						}
					}

				},
			);
	}


	function awoocHiddenDataToMail( dataOut ) {
		return '\n' + awooc_scripts.product_data_title +
				 '\n ———' +
				 dataOut.outTitle +
				 '\n' + dataOut.outID +
				 dataOut.outCat.replace( /(<([^>]+)>)/ig, '' ) +
				 dataOut.outPrice +
				 dataOut.outAttr.replace( /(<([^>]+)>)/ig, '' ) +
				 dataOut.outSku.replace( /(<([^>]+)>)/ig, '' ) +
				 dataOut.outQty +
				 dataOut.outLink;
	}


	function awoocPopupWindow() {
		$.blockUI( {
				message: $( '#awooc-form-custom-order' ),
				css: {
					width: '100%',
					maxWidth: '600px',
					maxHeight: '600px',
					top: '50%',
					left: '50%',
					border: '4px',
					borderRadius: '4px',
					cursor: 'default',
					overflowY: 'auto',
					boxShadow: '0px 0px 3px 0px rgba(0, 0, 0, 0.2)',
					zIndex: '1000000',
					transform: 'translate(-50%, -50%)',
				},
				overlayCSS: {
					zIndex: '1000000',
					backgroundColor: '#000',
					opacity: 0.6,
					cursor: 'wait',
				},
				bindEvents: true,
				timeout: 0,
				fadeIn: awooc_scripts.fadeIn,
				fadeOut: awooc_scripts.fadeOut,
				allowBodyStretch: true,
				focusInput: false,
				centerX: true,
				centerY: true,
				onBlock: function() {

					$( '#awooc-form-custom-order' ).removeClass( 'awooc-hide' );

				},
				onUnblock: function() {
					// При закрытии окна добавлем нужный класс.
					$( '#awooc-form-custom-order' )
						.addClass( 'awooc-hide' );

					// При закрытии окна очищаем данные.
					awoocFormDataEmpty();

					// Событие закрытия окна.
					$( document.body ).trigger( 'awooc_popup_close_trigger' );

				},
				onOverlayClick: function() {

					// При закрытии окна добавлем нужный класс.
					$( '#awooc-form-custom-order' )
						.addClass( 'awooc-hide' );
					$( 'html' )
						.css( { 'overflow': 'initial' } );

					$.unblockUI();

					// При закрытии окна очищаем данные.
					awoocFormDataEmpty();

				},
			},
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
							'inputmode': 'numeric',
						} );
					}

				} catch ( e ) {

					console.log( 'Error ' + e.name + ':' + e.message + '\n' + e.stack );

				}

			} );
		}
	}

} );