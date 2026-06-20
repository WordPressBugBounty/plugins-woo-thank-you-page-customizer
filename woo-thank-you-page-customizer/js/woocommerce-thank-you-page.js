jQuery(document).ready(function ($) {
    'use strict';
    $('.woocommerce-thank-you-page-coupon__code-code').focus(function () {
        $(this).select();
    });
    $('.woocommerce-thank-you-page-coupon__code-copy-code').on('click', function () {
        $(this).parent().parent().find('.woocommerce-thank-you-page-coupon__code-code').select();
        document.execCommand("copy");
        show_message(woocommerce_thank_you_page_customizer_params.copied_message)
    });
    sendCouponButton();

    function sendCouponButton() {
        $('.woocommerce-thank-you-page-coupon__code-mail-me').on('click', function () {
            let button = $(this);
            button.unbind().addClass('wtypc-sending-email');
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: woocommerce_thank_you_page_customizer_params.url,
                data: {
                    action: woocommerce_thank_you_page_customizer_params.action,
                    shortcodes: woocommerce_thank_you_page_customizer_params.shortcodes,
                    coupon_code: button.parent().parent().find('.woocommerce-thank-you-page-coupon__code-code').val(),
                    nonce: woocommerce_thank_you_page_customizer_params.nonce,
                },
                success: function (response) {
                    button.removeClass('wtypc-sending-email');
                    sendCouponButton();
                    if (response.hasOwnProperty('message') && response.message) {
                        show_message(response.message);
                    }
                },
                error: function (err) {
                    button.removeClass('wtypc-sending-email');
                    sendCouponButton();
                    console.log(err);
                }
            })
        })
    }
    function show_message(message) {
        if (!$('.vi-wcaio-warning-wrap').length) {
            $('body').append('<div class="vi-wcaio-warning-wrap vi-wcaio-warning-wrap-open"><div>' + message + '</div></div>');
        } else {
            $('.vi-wcaio-warning-wrap').removeClass('vi-wcaio-warning-wrap-close').addClass('vi-wcaio-warning-wrap-open');
            $('.vi-wcaio-warning-wrap > div').html(message);
        }
        setTimeout(function () {
            $('.vi-wcaio-sidebar-cart-wrap').addClass('vi-wcaio-sidebar-cart-wrap-warning');
        }, 1000);
        setTimeout(function () {
            hide_message();
        }, 15000);
    }

    function hide_message() {
        $('.vi-wcaio-warning-wrap').addClass('vi-wcaio-warning-wrap-close').removeClass('vi-wcaio-warning-wrap-open');
    }
});
