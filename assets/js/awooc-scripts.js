jQuery(document).ready(function ($) {
    $('.awooc-custom-order').click(function (e) {
        e.preventDefault();
        //debugger;
        var productPriceHidden,
            productPriceVariation,
            productPriceSimple;
        var productVariantId = $('.variations_form').find('input[name="variation_id"]').val();
        if ($('.woocommerce-variation-price').find('.price ins').length) {
            productPriceVariation = $('.woocommerce-variation-price').find('.price ins').text();
        } else {
            productPriceVariation = $('.woocommerce-variation-price').find('.price').text();
        }

        if ($('.summary').find('.price ins').length) {
            productPriceSimple = $('.summary').find('.price ins').text();
        } else {
            productPriceSimple = $('.summary').find('.price').text();
        }
        var productPriceSku = $('.product_meta').find('.sku').text();
        var productId = $(this).attr('data-value-product-id');


        if (typeof productPriceSku !== 'undefined') {
            $('#awooc-form-custom-order').find('.awooc-form-custom-order-sku').html(productPriceSku);
        }
        if (productPriceVariation === '') {
            $('#awooc-form-custom-order').find('.awooc-form-custom-order-price').html(productPriceSimple);
            productPriceHidden = 'Цена: ' + productPriceSimple;
        }
        if (productPriceVariation !== '') {
            $('#awooc-form-custom-order').find('.awooc-form-custom-order-price').html(productPriceVariation);
            productPriceHidden = 'Цена: ' + productPriceVariation;
        }

        //  console.log(productVariantId);

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
                success: function (data) {
                    $('.awooc-form-custom-order-attr').text(data);
                    var productVariationsTitle = $('#awooc-form-custom-order').find('.awooc-form-custom-order-title').text();
                    $.trim(productVariationsTitle);
                    var productVariationsSku = $('#awooc-form-custom-order').find('.awooc-form-custom-order-sku').text();
                    $.trim(productVariationsTitle);
                    if (typeof productPriceSku !== 'undefined') {
                        $('.awooc-hidden-data').val('Товар: ' + productVariationsTitle + '\n' + 'Атрибуты: ' + data + '\n' + 'Артикул: ' + productVariationsSku + '\n' + productPriceHidden);
                    } else {
                        $('.awooc-hidden-data').val('Товар: ' + productVariationsTitle + '\n' + 'Атрибуты: ' + data + '\n' + productPriceHidden);
                    }

                }
            });
        }

        $.blockUI({
            message: $('#awooc-form-custom-order'),
            css: {
                width: '100%',
                maxWidth: '600px',
                top: '10%',
                left: '32%',
                border: 'none',
                cursor: 'default'
            },
            bindEvents: false,
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
            },
            onOverlayClick: function () {
                $('#awooc-form-custom-order').addClass('awooc-hide');
            }
        });
        $('.blockOverlay').attr('title', 'Ткнуть для закрытия').click(function () {
            $.unblockUI();
        });
        $('.awwoc-close').attr('title', 'Ткнуть для закрытия').click(function () {
            $.unblockUI();
        });
        var productVariationsTitle = $('#awooc-form-custom-order').find('.awooc-form-custom-order-title').text();
        $.trim(productVariationsTitle);
        if (typeof productPriceSku !== 'undefined') {
            $('.awooc-hidden-data').val('Товар: ' + productVariationsTitle + '\n' + 'Артикул: ' + productPriceSku + '\n' + productPriceHidden);
        } else {
            $('.awooc-hidden-data').val('Товар: ' + productVariationsTitle + '\n' + productPriceHidden);
        }
        $('.awooc-hidden-product-id').val(productId);

    });
    /*    $(document).on('wpcf7invalid', function (e) {
            console.log(event.detail);
            setTimeout($.unblockUI, 5000);
        });*/
    document.addEventListener('wpcf7invalid', function (event) {
        // console.log(event.detail);
        setTimeout($.unblockUI, 5000);
        setTimeout(function () {
            $('.wpcf7-form')[0].reset();
            $('.wpcf7-response-output').remove();
        }, 10000);
    }, false);
    document.addEventListener('wpcf7mailsent', function (event) {
        setTimeout($.unblockUI, 3000);
        setTimeout(function () {
            $('.wpcf7-form')[0].reset();
            $('.wpcf7-response-output').remove();
        }, 3000);
    }, false);

});
