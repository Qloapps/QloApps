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
*  @license	http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{if $Braintree_Configured}
<span style="color:#008000;">
{if $PayPal_sandbox_mode}
	{l s='Your Braintree account is configured in sandbox mode. You can join the Braintree support on 08 05 54 27 14' mod='paypal'}
{else}
	{l s='Your Braintree account is configured in live mode. You can sell on Euro only. You can join the Braintree support on 08 05 54 27 14' mod='paypal'}
{/if}
</span>
<p>{l s='Activate 3D Secure?' mod='paypal'}</p>
<input type="radio" id="threedsecure_on" name="use_threedsecure" value="1"{if $PayPal_check3Dsecure == 1} checked="checked"{/if}/> <label for="threedsecure_on">{l s='Yes' mod='paypal'}</label><br />
<input type="radio" id="threedsecure_off" name="use_threedsecure" value="0"{if $PayPal_check3Dsecure == 0} checked="checked"{/if}/> <label for="threedsecure_off">{l s='No' mod='paypal'}</label>
{else}
<div id="button_braintree">
</div>
<script src="https://assets.braintreegateway.com/v1/braintree-oauth-connect.js"></script>
<script>
	$(document).ready(function(){
		$.get('{$Proxy_Host|escape:'htmlall':'UTF-8'}prestashop/getUrlConnect', {
			user_country: '{$User_Country|escape:'htmlall':'UTF-8'}',
			user_email:'{$User_Mail|escape:'htmlall':'UTF-8'}',
			business_name: '{$Business_Name|escape:'htmlall':'UTF-8'}',
			redirect_url: '{$Braintree_Redirect_Url|escape:'javascript':'UTF-8'}'
		}).done(function(data){
			//console.log(data);

			var partner = new BraintreeOAuthConnect({
				connectUrl : data.data.url_connect,
				container: 'button_braintree',
                environment: {if $PayPal_sandbox_mode}'sandbox'{else}'production'{/if},
                onError: function (errorObject) {
                    console.warn(errorObject.message);
                }
			});
		});
	});
</script>
{/if}