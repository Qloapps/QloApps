/**
 * Copyright since 2010 Webkul.
 *
 * NOTICE OF LICENSE
 *
 * All right is reserved,
 * Please go through LICENSE.txt file inside our module
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please refer to CustomizationPolicy.txt file inside our module for more information.
 *
 * @author Webkul IN
 * @copyright since 2010 Webkul IN
 * @license LICENSE.txt
 */

$(document).on('click', '#blocknewsletter .newsletter-btn', function (e) {
    e.preventDefault();

    $.ajax({
        url: url_newsletter_subscription,
        type: 'POST',
        dataType: 'JSON',
        cache: false,
        data: $(this).closest('form').serialize(),
        beforeSend: function () {
            let messageBlock = $('#blocknewsletter .message-block');
            $(messageBlock).fadeOut('fast');

            $('#blocknewsletter .loader').show();
        },
        success: function (response) {
            let messageBlock = $('#blocknewsletter .message-block');
            $(messageBlock).html(response.message_html).stop(true, true).fadeIn('fast');
        },
        error: function () {
            if (!onlineFlag) {
                showErrorMessage(no_internet_txt);
            }
        },
        complete: function () {
            $('#blocknewsletter .loader').hide();
        }
    });
});
