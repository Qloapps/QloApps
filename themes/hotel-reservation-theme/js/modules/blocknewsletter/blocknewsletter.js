/*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
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
