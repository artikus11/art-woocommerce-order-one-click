/**
 * Вывод данных
 *
 * @package art-woocommerce-order-one-click/assets/js
 */

jQuery( document )
	.ready(
		function( $ )
		{

			$( document )
				.on(
					'hide_variation',
					function( event, variation )
					{
						$( '.awooc-custom-order.button' )
							.addClass( 'disabled wc-variation-selection-needed' );
					},
				);

			$( document )
				.on(
					'show_variation',
					function( event, variation )
					{
						$( '.awooc-custom-order.button' )
							.removeClass(
								'disabled wc-variation-selection-needed wc-variation-is-unavailable' );

						// Если у вариации нет цены или ее нет в наличие то скрываем сообщения.
						if ( awooc_scripts.mode === 'no_stock_no_price' ) {
							if ( ! variation.is_purchasable || ! variation.is_in_stock ) {
								$( 'body.woocommerce' )
									.find( '.single_variation' )
									.hide();
							}
						}
					},
				);

			var orderTitle = $( '.awooc-form-custom-order-title' ),
			    orderImg   = $( '.awooc-form-custom-order-img' ),
			    orderPrice = $( '.awooc-form-custom-order-price' ),
			    orderQty   = $( '.awooc-form-custom-order-qty' ),
			    orderSku   = $( '.awooc-form-custom-order-sku' ),
			    orderAttr  = $( '.awooc-form-custom-order-attr' ),
			    preload    = '<div class="cssload-container"><div class="cssload-speeding-wheel"></div></div>';

			$( document )
				.on(
					'click',
					'.awooc-custom-order.button',
					function( event )
					{
						if ( $( this )
							.is( '.disabled' ) ) {
							event.preventDefault();

							if ( $( this )
								.is( '.wc-variation-is-unavailable' ) ) {
								window.alert( wc_add_to_cart_variation_params.i18n_unavailable_text );
							} else if ( $( this )
								.is( '.wc-variation-selection-needed' ) ) {
								window.alert( wc_add_to_cart_variation_params.i18n_make_a_selection_text );
							}
							return false;
						}

						// Задаем переменные.
						let prodictSelectedId,
							 productVariantId = $( '.variations_form' ).find( 'input[name="variation_id"]' ).val(),
							 productId = $( this ).attr( 'data-value-product-id' ),
							 productQty = $( '.quantity' ).find( 'input[name="quantity"]' ).val(),
							 dataOut = {};

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
						$.ajax(
							{
								url: awooc_scripts.url,
								data: data,
								type: 'POST',
								dataType: 'json',
								beforeSend: function( xhr, data )
								{
									// Вызываем прелоадер.
									$( event.currentTarget )
										.block(
											{
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
								success: function( data )
								{

									// Отключаем прелоадер.
									$( event.currentTarget )
										.unblock();
									$( event.currentTarget )
										.find( '.cssload-container' )
										.remove();

									// Добавляем тайтл к кнопке закрытия окнa.
									$( '.awooc-close' )
										.attr( 'title', awooc_scripts.title_close );

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
									orderTitle
										.text( data.title );
									orderImg
										.html( data.image );
									orderPrice
										.html( data.price );
									orderPrice
										.after( data.qty );
									orderQty
										.text( awooc_scripts.product_qty + productQty );
									orderSku
										.html( data.sku );
									orderAttr
										.html( data.attr );

									// Загружаем форму.
									$( '.awooc-col.columns-right' )
										.html( data.form );

									// Инициализация формы.
									awoocInitContactForm();

									// Собираем данные для письма.
									var hiddenData = awoocHiddenDataToMail( dataOut );

									// Записываем данные с скрытое поле для отправки письма.
									$( '.awooc-hidden-data' )
										.val( hiddenData );

									// Записываем id товара и количество в скрытое поле.
									$( '.awooc-hidden-product-id' )
										.val( prodictSelectedId );
									$( '.awooc-hidden-product-qty' )
										.val( productQty );

									// Проверка на наличчие маски телефона.
									awooMaskField();

									// Выводим всплывающее окно.
									awoocPopupWindow();
								},
							},
						);

						return false;
					},
				);

			$( document )
				.on(
					'click',
					'.awooc-close',
					function()
					{
						// При клику на оверлей закрываем окно.
						$.unblockUI();
						// При клику на оверлей добавлем нужный класс.
						$( '#awooc-form-custom-order' )
							.addClass( 'awooc-hide' );
					},
				);

			// Если ошибка отправки письма то через 5сек очищаем сообщения.
			document.addEventListener(
				'wpcf7invalid',
				function( event )
				{
					setTimeout(
						function()
						{
							$( '.wpcf7-response-output' )
								.remove();
							$( '.wpcf7-not-valid-tip' )
								.remove();
						},
						5000,
					);
				},
				false,
			);

			// После отправки письма то через 3сек очищаем форму и закрываем окно.
			document.addEventListener(
				'wpcf7mailsent',
				function( event )
				{
					setTimeout(
						$.unblockUI,
						3000,
					);
					setTimeout(
						function()
						{
							$( '.wpcf7-form' )[0].reset();
							$( '.wpcf7-response-output' )
								.remove();
						},
						3000,
					);
				},
				false,
			);


			function awoocInitContactForm() {
				$( 'div.wpcf7 > form' )
					.each(
						function()
						{
							var $form = $( this );
							wpcf7.initForm( $form );
							if ( wpcf7.cached ) {
								wpcf7.refill( $form );
							}
						},
					);
			}


			function awoocHiddenDataToMail( dataOut ) {
				return '\n' + awooc_scripts.product_data_title +
						'\n &mdash;&mdash;&mdash;' +
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
				$.blockUI(
					{
						message: $( '#awooc-form-custom-order' ),
						css: {
							width: '100%',
							maxWidth: '600px',
							maxHeight: '600px',
							top: '10%',
							left: 'calc(50% - 300px)',
							border: 'none',
							cursor: 'default',
							overflowY: 'auto',
							boxShadow: '0px 0px 3px 0px rgba(0, 0, 0, 0.2)',
							zIndex: '1000000',
						},
						overlayCSS: {
							zIndex: '1000000',
							backgroundColor: '#000',
							opacity: 0.6,
							cursor: 'wait',
						},
						bindEvents: true,
						timeout: 0,
						fadeIn: 400,
						fadeOut: 400,
						allowBodyStretch: true,
						centerX: true, // <-- only effects element blocking (page block
						// controlled via css above)
						centerY: true,
						onBlock: function()
						{
							$( '#awooc-form-custom-order' )
								.removeClass( 'awooc-hide' );

							// Если окно меньше 480px то меняем стили окна.
							if ( window.innerWidth < 480 ) {
								$( '.blockUI.blockPage' )
									.css(
										{
											'left': '2%',
											'top': 'calc(30% - 160px)',
											'height': 'auto',
											'overflow-y': 'scroll',
											'width': '95%',
										},
									);
							} else if ( window.innerWidth < 569 || window.innerWidth < 669 ) {
								$( '.blockUI.blockPage' )
									.css(
										{
											'left': '2%',
											'top': '2%',
											'height': '95%',
											'overflow-y': 'scroll',
											'width': '95%',
										},
									);
							} else if ( window.innerWidth < 769 ) {
								$( '.blockUI.blockPage' )
									.css(
										{
											'height': 'auto',
											'overflow-y': 'scroll',
											'width': '95%',
										},
									);
							}
						},
						onUnblock: function()
						{
							// При закрытии окна добавлем нужный класс.
							$( '#awooc-form-custom-order' )
								.addClass( 'awooc-hide' );

							// При закрытии окна очищаем данные.
							awoocFormDataEmpty();

						},
						onOverlayClick: function()
						{

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
				orderTitle
					.empty();
				orderImg
					.empty();
				orderPrice
					.empty();
				orderQty
					.empty();
				orderSku
					.empty();
				orderAttr
					.empty();
			}


			function awooMaskFieldItem() {
				let $this = $( this ), data_mask = $this.data( 'mask' );

				try {

					$this.mask( data_mask );

					if ( data_mask.indexOf( '*' ) === -1 && data_mask.indexOf( 'a' ) === -1 ) {
						$this
							.attr(
								{
									'inputmode': 'numeric',
								},
							);
					}

				} catch ( e ) {

					console.log( 'Ошибка ' + e.name + ':' + e.message + '\n' + e.stack );

				}

			}


			function awooMaskField() {
				let $mask_fields = $( '.wpcf7-mask' );
				if ( $mask_fields.length > 0 ) {
					$mask_fields
						.each(
							awooMaskFieldItem(),
						);
				}
			}

		},
	);
