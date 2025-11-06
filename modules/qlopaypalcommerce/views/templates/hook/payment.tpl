{*
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
*}

<div id="paypal-button-container"></div>

<div id="wk-overlay" style="display:none;">
    <div class="wk-spinner"></div><br/>
    {l s='Please wait...' mod='qlopaypalcommerce'}
</div>

<script>
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
                } else if (details.details[0].issue === 'INSTRUMENT_DECLINED') {
                    return actions.restart();
                } else {
                    console.log(details);
                    window.location.replace(error_order + '?err_name=' + details.details[0].issue + '&err_msg=' + details.details[0].description);
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
            console.log(error.data.details[0].issue + ' : ' + error.data.details[0].description);
            window.location.replace(error_order + '?err_name=' + error.data.details[0].issue + '&err_msg=' + error.data.details[0].description);
        }
    }).render('#paypal-button-container');

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
</script>