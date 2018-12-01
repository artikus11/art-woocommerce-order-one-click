jQuery(document).ready(function ($) {

    $('.awooc-custom-order').on('click', function (event) {
        event.preventDefault;

        var prodictSelectedId;
        //debugger;

        var productVariantId = $('.variations_form').find('input[name="variation_id"]').val(),
            productId = $(this).attr('data-value-product-id'),
            productQty = $('.quantity').find('input[name="quantity"]').val();

        var outTitle,
            outPrice,
            outLink,
            outSku,
            outAttr;

        if (productVariantId != 0 && typeof productVariantId !== 'undefined') {
            prodictSelectedId = productVariantId;
        } else {
            prodictSelectedId = productId;
        }

        data = {
            id: prodictSelectedId,
            action: 'awooc_ajax_product_form',
            nonce: awooc_scrpts.nonce
        };

        $.ajax({
            url: awooc_scrpts.url,
            data: data,
            type: 'POST',
            dataType: 'json',
            beforeSend: function (xhr, data) {

                $('.awooc-col.columns-left').block({
                    message: $('<div class="spinner"><div class="double-bounce1"></div><div class="double-bounce2"></div></div>'),
                    overlayCSS: {
                        background: '#fff',
                        opacity: 1,
                        cursor: 'wait',
                        border: 'none',
                    },
                    css: {
                        border: 'none',
                        background: 'transparent',
                        width: '100%',
                        left: '40%',
                    },
                });
            },
            success: function (data) {

                $('.awooc-col.columns-left').unblock();


                if (data.elements === 'full') {

                    outTitle = data.title == false ? '' : '\n' + 'Название: ' + data.title;
                    outAttr = data.attr == false ? '' : '\n' + data.attr;
                    outPrice = data.price == false ? '' : '\n' + 'Цена: ' + data.pricenumber;
                    outSku = data.sku == false ? '' : '\n' + data.sku;
                    outLink = data.link == false ? '' : '\n' + data.link;

                    $('.awooc-form-custom-order-title').text(data.title);
                    $('.awooc-form-custom-order-img').html(data.image);
                    $('.awooc-form-custom-order-price').html(data.price);
                    $('.awooc-form-custom-order-price').after(data.qty);
                    $('.awooc-form-custom-order-qty').text('Количество: ' + productQty);
                    $('.awooc-form-custom-order-sku').html(data.sku);
                    $('.awooc-form-custom-order-attr').html(outAttr);
                }

                var hiddenData = '\n' + 'Данные о выбраном товаре' +
                    '\n' + '-------' +
                    outTitle +
                    '\n' + 'ID: ' + prodictSelectedId +
                    outPrice +
                    outAttr.replace(/(<([^>]+)>)/ig, "") +
                    outSku.replace(/(<([^>]+)>)/ig, "") +
                    '\n' + 'Количество: ' + productQty +
                    outLink;

                console.log(hiddenData);
                $('.awooc-hidden-data').val(hiddenData);

                $('.awooc-hidden-product-id').val(prodictSelectedId);
                $('.awooc-hidden-product-qty').val(productQty);
            }
        });

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
                $('.awooc-form-custom-order-title').empty();
                $('.awooc-form-custom-order-img').empty();
                $('.awooc-form-custom-order-price').empty();
                //$('.awooc-form-custom-order-price').next().remove();
                $('.awooc-form-custom-order-qty').remove();
                $('.awooc-form-custom-order-sku').empty();
                $('.awooc-form-custom-order-attr').empty();
                history.pushState('', document.title, window.location.pathname);

            },
            onOverlayClick: function () {
                $('#awooc-form-custom-order').addClass('awooc-hide');
                $.unblockUI();
                $('.awooc-form-custom-order-title').empty();
                $('.awooc-form-custom-order-img').empty();
                $('.awooc-form-custom-order-price').empty();
                $('.awooc-form-custom-order-qty').remove();
                $('.awooc-form-custom-order-sku').empty();
                $('.awooc-form-custom-order-attr').empty();
                history.pushState('', document.title, window.location.pathname)
            }
        });


        //console.log(productQty * productPriceOut);
        // console.log(productPriceOut);
        //$('.awooc-hidden-data').val('\n' + 'Данные о выбраном товаре' + '\n' + '-------' + '\n' + 'Название: ' + productTitle + '\n' + 'ID: ' + productId + '\n' + productPrice + productSku + '\n' + 'Количество: ' + productQty + '\n' + productLink);
        // $('.awooc-hidden-product-id').val(productId);

    });
    /* $('.blockOverlay').attr('title', 'Ткнуть для закрытия').click(function () {
         $.unblockUI();
         $('#awooc-form-custom-order').addClass('awooc-hide');
     });*/

    $('.awwoc-close').attr('title', 'Ткнуть для закрытия').click(function () {
        $.unblockUI();
        $('#awooc-form-custom-order').addClass('awooc-hide');
        history.pushState('', document.title, window.location.pathname);
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

    function replaceAjaxData(data) {

    }
});