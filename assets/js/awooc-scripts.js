jQuery(document).ready(function ($) {
    //debugger;
    $(document).on('hide_variation', function (event) {
        $('#awooc-custom-order-button').addClass( 'disabled wc-variation-selection-needed' );
    });

    $(document).on('show_variation', function (event) {
        $('#awooc-custom-order-button').removeClass('disabled wc-variation-selection-needed wc-variation-is-unavailable');
    });

    $('.awooc-custom-order').on('click', function (event) {
        event.preventDefault;

        if ( $( this ).is('.disabled') ) {
            event.preventDefault();

            if ( $( this ).is('.wc-variation-is-unavailable') ) {
                window.alert( wc_add_to_cart_variation_params.i18n_unavailable_text );
            } else if ( $( this ).is('.wc-variation-selection-needed') ) {
                window.alert( wc_add_to_cart_variation_params.i18n_make_a_selection_text );
            }
            return false;
        }
        // Задаем переменные
        var prodictSelectedId;
        var productVariantId = $('.variations_form').find('input[name="variation_id"]').val(),
            productId = $(this).attr('data-value-product-id'),
            productQty = $('.quantity').find('input[name="quantity"]').val();
        var outTitle,
            outPrice,
            outLink,
            outSku,
            outAttr;

        // Проверяем ID товара, для вариаций свой, для простых свой
        if (productVariantId != 0 && typeof productVariantId !== 'undefined') {
            prodictSelectedId = productVariantId;
        } else {
            prodictSelectedId = productId;
        }

        // Собираем данные для отправки
        data = {
            id: prodictSelectedId,
            action: 'awooc_ajax_product_form',
            nonce: awooc_scrpts.nonce
        };

        // Отправляем запрос
        $.ajax({
            url: awooc_scrpts.url,
            data: data,
            type: 'POST',
            dataType: 'json',
            beforeSend: function (xhr, data) {
                // Вызываем прелоадер
                $('#awooc-custom-order-button').prepend('<div class="cssload-container"><div class="cssload-speeding-wheel"></div></div>');
            },
            success: function (data) {
                // Отключаем прелоадер
                $('#awooc-custom-order-button').find('.cssload-container').remove();

                // Проверяем данные после аяксаи формируем нужные строки
                outTitle = data.title == false ? '' : '\n' + 'Название: ' + data.title;
                outAttr = data.attr == false ? '' : '\n' + data.attr;
                outPrice = data.price == false ? '' : '\n' + 'Цена: ' + data.pricenumber;
                outSku = data.sku == false ? '' : '\n' + data.sku;
                outLink = data.link == false ? '' : '\n' + data.link;

                // Проверяем что все элементы на месте
                if (data.elements === 'full') {
                    // Формируем данные
                    $('.awooc-form-custom-order-title').text(data.title);
                    $('.awooc-form-custom-order-img').html(data.image);
                    $('.awooc-form-custom-order-price').html(data.price);
                    $('.awooc-form-custom-order-price').after(data.qty);
                    $('.awooc-form-custom-order-qty').text('Количество: ' + productQty);
                    $('.awooc-form-custom-order-sku').html(data.sku);
                    $('.awooc-form-custom-order-attr').html(outAttr);

                    // Загружаем форму
                    $('.awooc-col.columns-right').html(data.form);
                    // Инициализируем форму
                    wpcf7.initForm('.wpcf7-form');
                    if (wpcf7.cached) {
                        wpcf7.refill('.wpcf7-form');
                    }

                    // Собираем данные для письма
                    var hiddenData = '\n' + 'Данные о выбраном товаре' +
                        '\n' + '-------' +
                        outTitle +
                        '\n' + 'ID: ' + prodictSelectedId +
                        outPrice +
                        outAttr.replace(/(<([^>]+)>)/ig, "") +
                        outSku.replace(/(<([^>]+)>)/ig, "") +
                        '\n' + 'Количество: ' + productQty +
                        outLink;

                } else {
                    // Если нет элеентов то просто выводим форму и инициализируем ее
                    $('.awooc-custom-order-wrap').html(data.form);
                    wpcf7.initForm('.wpcf7-form');
                    if (wpcf7.cached) {
                        wpcf7.refill('.wpcf7-form');
                    }

                    // Собираем данные для письма
                    var hiddenData = '\n' + 'Данные о выбраном товаре' +
                        '\n' + '-------' +
                        outTitle +
                        '\n' + 'ID: ' + prodictSelectedId +
                        outPrice +
                        outAttr +
                        outSku +
                        '\n' + 'Количество: ' + productQty +
                        outLink;
                }


                // console.log(hiddenData);
                // Записываем данные с скрытое поле для отправки письма
                $('.awooc-hidden-data').val(hiddenData);

                // Записываем id товара и количество в скрытое поле
                $('.awooc-hidden-product-id').val(prodictSelectedId);
                $('.awooc-hidden-product-qty').val(productQty);

                // Проверка на наличчие маски телефона
                var $mask_fields = $('.wpcf7-mask');
                if ($mask_fields.length > 0) {
                    $mask_fields.each(function () {
                        var $this = $(this), data_mask = $this.data('mask');

                        $this.mask(data_mask);

                        if (data_mask.indexOf('*') == -1 && data_mask.indexOf('a') == -1) {
                            $this.attr({
                                'inputmode': 'numeric'
                            });
                        }

                    });
                }

                //console.log($('#awooc-form-custom-order').find('.wpcf7-recaptcha').attr('data-sitekey'));

                // Выводим всплывающее окно
                $.blockUI({
                    message: $('#awooc-form-custom-order'),
                    css: {
                        width: '100%',
                        maxWidth: '600px',
                        maxHeight: '600px',
                        top: '10%',
                        left: '32%',
                        border: 'none',
                        cursor: 'default',
                        overflowY: 'auto',
                        boxShadow: '0px 0px 3px 0px rgba(0, 0, 0, 0.2)',
                        zIndex: '1000000'
                    },
                    overlayCSS: {
                        zIndex: '1000000',
                        backgroundColor: '#000',
                        opacity: 0.6,
                        cursor: 'wait'
                    },
                    bindEvents: true,
                    timeout: 0,
                    fadeIn: 400,
                    fadeOut: 400,
                    allowBodyStretch: true,
                    onBlock: function () {
                        $('#awooc-form-custom-order').removeClass('awooc-hide');

                        // Если окно меньше 480px то меняем стили окна
                        if (window.innerWidth < 480) {
                            $('.blockUI.blockPage').css({
                                'left': '2%',
                                'top': '5%',
                                'height': '95%',
                                'overflow-y': 'scroll',
                                'width': '95%',
                            });
                        }
                    },
                    onUnblock: function () {
                        // При закрытии окна добавлем нужный класс
                        $('#awooc-form-custom-order').addClass('awooc-hide');
                        // При закрытии окна очищаем данные
                        $('.awooc-form-custom-order-title').empty();
                        $('.awooc-form-custom-order-img').empty();
                        $('.awooc-form-custom-order-price').empty();
                        //$('.awooc-form-custom-order-price').next().remove();
                        $('.awooc-form-custom-order-qty').remove();
                        $('.awooc-form-custom-order-sku').empty();
                        $('.awooc-form-custom-order-attr').empty();
                        // При закрытии окна очищаем урл
                        history.pushState('', document.title, window.location.pathname);

                    },
                    onOverlayClick: function () {
                        // При закрытии окна добавлем нужный класс
                        $('#awooc-form-custom-order').addClass('awooc-hide');
                        $.unblockUI();
                        // При закрытии окна очищаем данные
                        $('.awooc-form-custom-order-title').empty();
                        $('.awooc-form-custom-order-img').empty();
                        $('.awooc-form-custom-order-price').empty();
                        $('.awooc-form-custom-order-qty').remove();
                        $('.awooc-form-custom-order-sku').empty();
                        $('.awooc-form-custom-order-attr').empty();
                        // При закрытии окна очищаем урл
                        history.pushState('', document.title, window.location.pathname)
                    }
                });
            }
        });

    });

    $('.awwoc-close').attr('title', 'Ткнуть для закрытия').click(function () {
        // При клику на оверлей закрываем окно
        $.unblockUI();
        // При клику на оверлей добавлем нужный класс
        $('#awooc-form-custom-order').addClass('awooc-hide');
        // При клику на оверлей очищаем урл
        history.pushState('', document.title, window.location.pathname);
    });

    // Если ошибка отправки письма то через 5сек очищаем сообщения
    document.addEventListener('wpcf7invalid', function (event) {
        setTimeout(function () {
            $('.wpcf7-response-output').remove();
            $('.wpcf7-not-valid-tip').remove();
        }, 5000);
    }, false);

    // После отправки письма то через 3сек очищаем форму и закрываем окно
    document.addEventListener('wpcf7mailsent', function (event) {
        setTimeout($.unblockUI, 3000);
        setTimeout(function () {
            $('.wpcf7-form')[0].reset();
            $('.wpcf7-response-output').remove();
        }, 3000);
    }, false);

});