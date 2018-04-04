jQuery(document).ready(function ($) {
    $('.awooc-custom-order').click(function () {
        var productVariantId = $('.variations_form').find('input[name="variation_id"]').val();
        var productPrice = $('.variations_form').find('.woocommerce-variation-price .price').html();
        var productPriceSimple = $('.type-product').find('.price').html();
        var productPriceVariant = $('.product-type-variable').find('.price').html();
        $('#awooc-form-custom-order').find('.awooc-form-custom-order-price').html(productPrice);
         console.log(productPrice);
        // console.log(productVariantId);
        if (productPrice != undefined) {
            $('#awooc-form-custom-order').find('.awooc-form-custom-order-price').html(productPrice);
        } else {
            $('#awooc-form-custom-order').find('.awooc-form-custom-order-price').html(productPriceSimple);
        }

        if (productVariantId != 0 && productVariantId != undefined) {
            var data = {
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
                    console.log(data);
                    $('.awooc-form-custom-order-attr').text(data);
                }
            });
        }
        $('#awooc-form-custom-order').removeClass('awooc-hide');
        $.blockUI({
            message: $('#awooc-form-custom-order'),
            css: {
                width: '100%',
                maxWidth: '600px',
                top: '10%',
                left: '35%'
            },
            bindEvents: false,
            timeout: 0,
            onOverlayClick: $.unblockUI
        });

        var productVariationsTitle = $('#awooc-form-custom-order').find('.awooc-form-custom-order-title').text();
        $.trim(productVariationsTitle);
        $('.awooc-hidden-title').val(productVariationsTitle);
        $('.blockOverlay').attr('title', 'Click to unblock').click($.unblockUI);

    });
/*    document.addEventListener('wpcf7invalid', function (event) {
        setTimeout(function () {
            $('.wpcf7-form')[0].reset();
            $('.wpcf7-response-output').remove();
        }, 3000);
    }, false);
    document.addEventListener('wpcf7mailsent', function (event) {
        setTimeout($.unblockUI, 1000);
        setTimeout(function () {
            $('.wpcf7-form')[0].reset();
            $('.wpcf7-response-output').remove();
        }, 3000);
    }, false);*/

});
