jQuery(document).ready(function ($) {

    $('.awooc-custom-order').on('click', function(event){
        event.preventDefault;

        //debugger;
        var productVariantId = $('.variations_form').find('input[name="variation_id"]').val(),
            productId = $(this).attr('data-value-product-id'),
            productTitle = $('#awooc-form-custom-order').find('.awooc-form-custom-order-title').text(),
            productSku = '\n' + $('.awooc-form-custom-order-sku').text(),
            productLink = $('.awooc-form-custom-order-link').text(),
            productPrice = $('.awooc-form-custom-order-price').text();

        if ($('.awooc-form-custom-order-attr').find('.awooc-attr-wrapper').length < 0) {
            $('.awooc-form-custom-order-attr ').empty();
        }

        if (productVariantId != 0 && typeof productVariantId !== 'undefined') {
            data = {
                id: productVariantId,
                action: 'awooc_ajax_variant_order',
                nonce: awooc_scrpts.nonce
            };
            $.ajax({
                url: awooc_scrpts.url,
                data: data,
                type: 'POST',
                dataType: 'json',
                beforeSend: function (xhr) {
                    $('.awooc-col.columns-left').block({
                        message: null,
                        overlayCSS: {
                            background: '#fff',
                            opacity: 0.6
                        }
                    })
                },
                success: function (data) {

                    $('.awooc-attr-wrapper').text(data.attr);
                    $('.awooc-price-wrapper').html(data.price);
                    var productVariantPrice = $('.awooc-form-custom-order-price').text();
                    $('.awooc-col.columns-left').unblock();
                    $('.awooc-hidden-data').val('\n' +'Данные о выбраном товаре' + '\n' + '-------' + '\n' + 'Название: ' + productTitle + '\n' + 'ID: ' + productVariantId  + '\n' + productVariantPrice  + '\n' + 'Атрибуты: ' + data.attr +  productSku + '\n' + productLink);
                }
            });
        }

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
            bindEvents: true,
            timeout: 0,
            allowBodyStretch: true,
            onBlock: function () {
                $('#awooc-form-custom-order').removeClass('awooc-hide');
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
                $('#awooc-form-custom-order').addClass('awooc-hide');
                history.pushState('',document.title,window.location.pathname);

            },
            onOverlayClick: function () {
                $('#awooc-form-custom-order').addClass('awooc-hide');
                history.pushState('',document.title,window.location.pathname)
            }
        });

        $('.blockOverlay').attr('title', 'Ткнуть для закрытия').click(function () {
            $.unblockUI();
        });

        $('.awwoc-close').attr('title', 'Ткнуть для закрытия').click(function () {
            $.unblockUI();
            history.pushState('',document.title,window.location.pathname);
        });


        $('.awooc-hidden-data').val('\n' + 'Данные о выбраном товаре' + '\n' + '-------' + '\n' + 'Название: ' + productTitle  + '\n' + 'ID: ' + productId + '\n' + productPrice  +  productSku + '\n' + productLink);
        $('.awooc-hidden-product-id').val(productId);

    });

    document.addEventListener('wpcf7invalid', function (event) {
        setTimeout(function () {
            $('.wpcf7-response-output').remove();
            $('.wpcf7-not-valid-tip').remove();
        }, 5000);
    }, false);

    document.addEventListener('wpcf7mailsent', function (event) {
        setTimeout($.unblockUI, 3000);
        setTimeout(function () {
            $('.wpcf7-form')[0].reset();
            $('.wpcf7-response-output').remove();
        }, 3000);
    }, false);

});