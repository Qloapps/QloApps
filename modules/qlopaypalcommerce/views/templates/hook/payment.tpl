{*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/licenses/afl-3.0.php
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
* @license https://opensource.org/licenses/afl-3.0.php Academic Free License 3.0
*}

<div class="row">
    <div class="col-xs-12">
        <p class="payment_module">
            <a id="qlo_paypal_payment_option" class="qlo_paypal_payment" title="{l s='Pay using Paypal payment options.' mod='qlopaypalcommerce'}">
                {l s='Pay using Paypal' mod='qlopaypalcommerce'}
            </a>
        </p>
    </div>
</div>

<div id="paypal-button-container" style="display:none; padding: 15px; border: 1px solid #d6d4d4; border-top: none; background: #FBFBFB; max-width: 100%;"></div>

<div id="wk-overlay" style="display:none;">
    <div class="wk-spinner"></div><br/>
    {l s='Please wait...' mod='qlopaypalcommerce'}
</div>

<script>
(function ($) {
    var paypalButtonsRendered = false;

    function submitForm(url, data) {
        let form = document.createElement('form'),
            input = document.createElement('input');
        input.name = data.key;
        input.value = data.value;
        form.appendChild(input);
        form.style.visibility = 'hidden';
        form.method = 'POST';
        form.action = url;
        document.body.appendChild(form);

        form.submit();
    }

    function getPaypalErrorField(details, field) {
        if (details && details.details && details.details[0] && details.details[0][field]) {
            return details.details[0][field];
        }
        return '';
    }

    function redirectToErrorPage(details) {
        window.location.replace(
            error_order
            + '?err_name=' + encodeURIComponent(getPaypalErrorField(details, 'issue'))
            + '&err_msg=' + encodeURIComponent(getPaypalErrorField(details, 'description'))
        );
    }

    function renderPaypalButtons() {
        if (paypalButtonsRendered) {
            return;
        }
        paypalButtonsRendered = true;

        paypal.Buttons({
            env: pp_environment,
            style: {
                layout: 'vertical',   // horizontal | vertical
                size:   'responsive',   // medium | large | responsive
                shape:  'rect',         // pill | rect
                color:  'gold'         // gold | blue | silver | black,
            },
            commit: false,
            createOrder: function() {
                return fetch(create_order, {
                    method: 'POST',
                    body: JSON.stringify({
                        'update': null,
                        'flow': 'shortcut'
                    }),
                }).then(function(response) {
                    return response.json();
                }).then(function(dataJson) {
                    return dataJson.data.id;
                });
            },
            onApprove: function(data, actions) {
                $('#wk-overlay').show();

                return fetch(capture_order, {
                    headers: {
                        'content-type': 'application/json'
                    },
                    body: JSON.stringify({
                        orderID: data.orderID,
                        getOrderData: true,
                    }),
                    method: "POST",
                }).then(function(res) {
                    return res.json();
                }).then(function(details) {
                    if (details.id) {
                        const postData = {
                            key: "order_id",
                            value: details.id
                        };
                        submitForm(capture_order, postData);
                    } else if (getPaypalErrorField(details, 'issue') === 'INSTRUMENT_DECLINED') {
                        return actions.restart();
                    } else {
                        redirectToErrorPage(details);
                    }
                });
            },
            onCancel: function(data) {
                let url = cancel_order,
                    postData = {
                        key: "error",
                        value: JSON.stringify(data)
                    };
                submitForm(url, postData);
            },
            onError: function (error) {
                redirectToErrorPage(error.data);
            }
        }).render('#paypal-button-container');
    }

    $(document).ready(function() {
        $('#qlo_paypal_payment_option').click(function(e) {
            e.preventDefault();
            $('#paypal-button-container').slideToggle();
            renderPaypalButtons();
        });
    });
})(jQuery);
</script>