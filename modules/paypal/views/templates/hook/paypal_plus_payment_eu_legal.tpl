{*
* 2007-2018 PrestaShop
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2018 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{*Displaying a button or the iframe*}
{if $advancedeucompliance_active}
    <form onsubmit="doPatch(ppp); return false;"></form>
{/if}
<div id="ppplusDiv" style="display:block; width:100%; ">
<div id="ppplus" style="display:block; width:100%;"></div>
</div>

{literal}
<script type="application/javascript">

    var ppp = PAYPAL.apps.PPP({
        "approvalUrl": "{/literal}{$approval_url|escape:'javascript':'UTF-8'}{literal}",
        "placeholder": "ppplus",
        "mode": "{/literal}{$mode|escape:'htmlall':'UTF-8'}{literal}",
            {/literal}{if $mode == 'sandbox'}"showPuiOnSandbox": "true",{/if}{literal}

        "language": "{/literal}{$language|escape:'htmlall':'UTF-8'}{literal}",
        "country": "{/literal}{$country|escape:'htmlall':'UTF-8'}{literal}",
        "buttonLocation": "outside",
        "onLoad": function(){
            //deselect payment methods
            ppp.deselectPaymentMethod();
        }
    });

    function doPatch(ppp) {

        $('#ppplus iframe').slideUp();
        $('#ppplus').html('<img style="display:block;margin:15px auto;" src="{/literal}{$img_loader|escape:'htmlall':'UTF-8'}{literal}"/>');

        jQuery.ajax({
            url : "{/literal}{$ajaxUrl|escape:'javascript':'UTF-8'}{literal}",
            success: function(){
                ppp.doCheckout();
            }

        });
    }

    // ADD for module eu_legal
    $('#paypal_payment form').on('submit',function(event){
        event.preventDefault();
        doPatch(ppp);
        return false;
    });

    // Display Paypal payment options :
    $(document).on('click', '.payment_module', function(e) {
        var ppp_el = $(this).parent().find('#ppplusDiv');
        if (ppp_el.length) {               // Clicked element has a #ppplusDiv child.
            ppp_el.parent().stop().show(); // Display paypal options.
        } else {                           // Clicked element doesn't contain #ppplusDiv child,
            $(document).find('#ppplusDiv').parent().stop().hide(); // thus we can hide paypal options.
        }
    });

</script>
{/literal}
