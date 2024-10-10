/**
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to support@qloapps.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to https://store.webkul.com/customisation-guidelines for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
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
