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
<div style="display:block; width:100%; ">
<div id="ppplus" style="display:block; width:100%;"></div>
</div>

{literal}
    <script type="application/javascript">

        // presta 160
        $("input[name=payment_option]").on("click", function(){


             $("#confirmOrder").show();
             ppp.deselectPaymentMethod();

             //$("payment_option_form #ppplus").hide();

        });

        // presta 161
        $(".payment_module").on("click", function(){

             $("#confirmOrder").show();
             ppp.deselectPaymentMethod();


            if($(this).next().first().children().attr("id") == "ppplusDiv"){
                $("#ppplus").show();
            }else{
                $("#ppplus").hide();
            }

        });



        var ppp = PAYPAL.apps.PPP({
            "approvalUrl": "{/literal}{$approval_url|escape:'UTF-8'}{literal}",
            "placeholder": "ppplus",
            "mode": "{/literal}{$mode|escape:'htmlall':'UTF-8'}{literal}",
            {/literal}{if $mode == 'sandbox'}"showPuiOnSandbox": "true",{/if}{literal}

            "language": "{/literal}{$language|escape:'htmlall':'UTF-8'}{literal}",
            "country": "{/literal}{$country|escape:'htmlall':'UTF-8'}{literal}",
            "buttonLocation": "inside",

            "enableContinue": function (){
                $("#confirmOrder").hide();
            },

            "disableContinue": function (){
                $("#confirmOrder").show();
            },

            "onContinue" : function () {


                // eu-legal
                if($("#cgv-legal").length != 0){


                    if($("#cgv-legal").is(":checked")){

                $('#ppplus iframe').slideUp();
                $('#ppplus').html('<img style="display:block;margin:15px auto;" src="{/literal}{$img_loader|escape:'htmlall':'UTF-8'}{literal}"/>');
                doPatch(ppp);

                    }else{

                        alert("Bitte akzeptieren Sie die Allgemeinen Geschäftsbedingungen");
                    }

                }else if($("#cgv").length != 0){ // advanced eu 161


                    if($("#cgv").is(":checked")){

                        $('#ppplus iframe').slideUp();
                        $('#ppplus').html('<img style="display:block;margin:15px auto;" src="{/literal}{$img_loader|escape:'htmlall':'UTF-8'}{literal}"/>');
                        doPatch(ppp);

                    }else{

                        alert("Bitte akzeptieren Sie die Allgemeinen Geschäftsbedingungen");
                    }

                }else{
                    $('#ppplus iframe').slideUp();
                    $('#ppplus').html('<img style="display:block;margin:15px auto;" src="{/literal}{$img_loader|escape:'htmlall':'UTF-8'}{literal}"/>');
                    doPatch(ppp);
                }


            },

            "onLoad": function(){
                //deselect payment methods
                ppp.deselectPaymentMethod();
            }
        });

        function doPatch(ppp) {
            jQuery.ajax({
                url : "{/literal}{$ajaxUrl}{literal}",
                success: function(){
                   ppp.doCheckout();
               }

            });
        }
    </script>
{/literal}

