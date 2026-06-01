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
            <a id="qlo_duitku_payment_option" href="javascript:void(0)" class="qlo_duitku_payment" title="{l s='Pay using Duitku payment options.' mod='qloduitkupayment'}">
                {l s='Pay using Duitku' mod='qloduitkupayment'}
                {if isset($duitkuunavailable) && $duitkuunavailable == 1}
                    <span class="err_duitku_unavalible">{l s='(currently unavailable)' mod='qloduitkupayment'}</span>
                {/if}
            </a>
        </p>
    </div>

    <div class="duitku_loading_overlay">
        <img src="{$module_dir}views/img/ajax-loader.gif" class="duitku_loading_img" alt="{l s='Loading Duitku payment methods...' mod='qloduitkupayment'}"/>
    </div>
</div>
<script>
$(document).ready(function() {
    $("#qlo_duitku_payment_option").on("click", function(e) {
        e.preventDefault();
        $(".duitku_loading_overlay").show();
        $.ajax({
            url: qdp_duitku_validate_url,
            type: "POST",
            dataType: "JSON",
            cache: false,
            data: {
                ajax: true,
                action: "getDuitkuTransactionToken",
            },
            success: function(jsonResponse) {
                $(".duitku_loading_overlay").hide();
                if (jsonResponse.status) {
                    window.location.href = jsonResponse.response.paymentUrl;
                } else {
                    $.growl.error({
                        title: "",
                        message: jsonResponse.message,
                    });
                }
            }
        });
    });
});
</script>