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
                $('#awooc-custom-order-button').prepend('<div class="cssload-container"><div class="cssload-speeding-wheel"></div></div>');
            },
            success: function (data) {
                $('#awooc-custom-order-button').find('.cssload-container').remove();

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

                //console.log(hiddenData);
                $('.awooc-hidden-data').val(hiddenData);

                $('.awooc-hidden-product-id').val(prodictSelectedId);
                $('.awooc-hidden-product-qty').val(productQty);

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
            }
        });

    });

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

});