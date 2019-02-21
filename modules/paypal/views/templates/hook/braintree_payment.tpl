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
<div class="row">
	<div class="col-xs-12 col-md-6">
		<div class="braintree-row-payment">
			<p class="payment_module">
			{if $error_msg != ''}<p class="braintree_error">{$error_msg|escape:'htmlall':'UTF-8'}<p>{/if}
                <label class="paypal_title_pay_card">{l s='Pay with your card' mod='paypal'}</label><div class="paypal_clear"></div>
                <img src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/paypal/views/img/logos/braintree_cards.png" alt="">
				<form action="{$braintreeSubmitUrl}" id="braintree-form" method="post">
					<div id="block-card-number" class="block_field">
						<div id="card-number" class="hosted_field"></div>
					</div>

					<div id="block-expiration-date" class="block_field half_block_field">
						<div id="expiration-date" class="hosted_field"></div>
					</div>

					<div id="block-cvv" class="block_field half_block_field">
						<div id="cvv" class="hosted_field"></div>
					</div>

					<input type="hidden" name="deviceData" id="deviceData"/>
					<input type="hidden" name="client_token" value="{$braintreeToken}">
					<input type="hidden" name="liabilityShifted" id="liabilityShifted"/>
					<input type="hidden" name="liabilityShiftPossible" id="liabilityShiftPossible"/>
					<input type="hidden" name="payment_method_nonce" id="payment_method_nonce"/>
					<input type="hidden" name="card_type" id="braintree_card_type"/>
                <div class="paypal_clear"></div>
				<input type="submit" value="{l s='Pay' mod='paypal'}"  id="braintree_submit" disabled="disabled"/>
				</form>
			</p>
		</div>
	</div>
</div>


 	{if !$opc}
	<script src="https://js.braintreegateway.com/web/3.9.0/js/client.min.js"></script>
	<script src="https://js.braintreegateway.com/web/3.9.0/js/hosted-fields.min.js"></script>
	<script src="https://js.braintreegateway.com/web/3.9.0/js/data-collector.min.js"></script>
	<script src="https://js.braintreegateway.com/web/3.9.0/js/three-d-secure.min.js"></script>
 	{/if}
{literal}
	<script>
		var authorization = '{/literal}{$braintreeToken}{literal}';
		var submit = document.querySelector('#braintree_submit');
		var form = document.querySelector('#braintree-form');

		braintree.client.create({
			authorization: authorization
		}, function (clientErr, clientInstance) {
			if (clientErr) {
				$.fancybox.open([
					{
						type: 'inline',
						autoScale: true,
						minHeight: 30,
						content: '<p class="braintree-error">{/literal}{l s='Error create Client' mod='paypal'}{literal}</p>'
					}
				]);
				return;
			}

			braintree.hostedFields.create({
				client: clientInstance,
				styles: {
					'input': {
						'color': '#999999',
						'font-size': '14px',
						'font-family': 'PayPal Forward, sans-serif'
					}
				},
				fields: {
					number: {
						selector: "#card-number",
						placeholder: '{/literal}{l s='Card number' mod='paypal'}{literal}'
					},
					cvv: {
						selector: "#cvv",
						placeholder: '{/literal}{l s='CVC' mod='paypal'}{literal}'
					},
					expirationDate: {
						selector: "#expiration-date",
						placeholder: '{/literal}{l s='MM/YY' mod='paypal'}{literal}'
					}
				}
			},function (hostedFieldsErr, hostedFieldsInstance) {
				if (hostedFieldsErr) {
					$.fancybox.open([
						{
							type: 'inline',
							autoScale: true,
							minHeight: 30,
							content: '<p class="braintree-error">{/literal}{l s='Error create Hosted fields' mod='paypal'}{literal}</p>'
						}
					]);
					return;
				}

				submit.removeAttribute('disabled');

				form.addEventListener('submit', function (event) {
					event.preventDefault();
					hostedFieldsInstance.tokenize(function (tokenizeErr, payload) {
						if (tokenizeErr) {
							var popup_message = '';
							switch (tokenizeErr.code) {
								case 'HOSTED_FIELDS_FIELDS_EMPTY':
									popup_message = "{/literal}{l s='All fields are empty! Please fill out the form.' mod='paypal'}{literal}";
									break;
								case 'HOSTED_FIELDS_FIELDS_INVALID':
									popup_message = "{/literal}{l s='Some fields are invalid :' mod='paypal'}{literal} "+tokenizeErr.details.invalidFieldKeys;
									break;
								case 'HOSTED_FIELDS_FAILED_TOKENIZATION':
									popup_message = "{/literal}{l s='Tokenization failed server side. Is the card valid?' mod='paypal'}{literal}";
									break;
								case 'HOSTED_FIELDS_TOKENIZATION_NETWORK_ERROR':
									popup_message = "{/literal}{l s='Network error occurred when tokenizing.' mod='paypal'}{literal}";
									break;
								default:
									popup_message = "{/literal}{l s='Tokenize failed' mod='paypal'}{literal}";
							}
							$.fancybox.open([
								{
									type: 'inline',
									autoScale: true,
									minHeight: 30,
									content: '<p class="braintree-error">'+popup_message+'</p>'
								}
							]);
							return false;
						}
						{/literal}{if $check3Dsecure}{literal}
						braintree.threeDSecure.create({
							client: clientInstance
						}, function (ThreeDSecureerror,threeDSecure) {

							if(ThreeDSecureerror)
							{
								switch (ThreeDSecureerror.code) {
									case 'THREEDS_HTTPS_REQUIRED':
										popup_message = "{/literal}{l s='3D Secure requires HTTPS.' mod='paypal'}{literal}";
										break;
									default:
										popup_message = "{/literal}{l s='Load 3D Secure Failed' mod='paypal'}{literal}";
								}
								$.fancybox.open([
									{
										type: 'inline',
										autoScale: true,
										minHeight: 30,
										content: '<p class="braintree-error">'+popup_message+'</p>'
									}
								]);
								return false;
							}
							threeDSecure.verifyCard({
								nonce: payload.nonce,
								amount: {/literal}{$braintreeAmount}{literal},
								addFrame: function (err, iframe) {
									$.fancybox.open([
										{
											type: 'inline',
											autoScale: true,
											minHeight: 30,
											content: '<p class="braintree-iframe">'+iframe.outerHTML+'</p>'
										}
									]);
								},
								removeFrame: function () {

								}
							}, function (err, three_d_secure_response) {
								if (err) {
									var popup_message = '';
									switch (err.code) {
										case 'CLIENT_REQUEST_ERROR':
											popup_message = "{/literal}{l s='There was a problem with your request.' mod='paypal'}{literal}";
											break;
										default:
											popup_message = "{/literal}{l s='3D Secure Failed' mod='paypal'}{literal}";
									}
									$.fancybox.open([
										{
											type: 'inline',
											autoScale: true,
											minHeight: 30,
											content: '<p class="braintree-error">'+popup_message+'</p>'
										}
									]);
									return false;
								}
								if(three_d_secure_response.liabilityShifted)
								{
									document.querySelector('input[name="liabilityShifted"]').value = three_d_secure_response.liabilityShifted;
								}
								else
								{
									document.querySelector('input[name="liabilityShifted"]').value = false;
								}

								if(three_d_secure_response.liabilityShiftPossible)
								{
									document.querySelector('input[name="liabilityShiftPossible"]').value = three_d_secure_response.liabilityShiftPossible;
								}
								else
								{
									document.querySelector('input[name="liabilityShiftPossible"]').value = false;
								}
								document.querySelector('input[name="payment_method_nonce"]').value = three_d_secure_response.nonce;
								document.querySelector('input[name="card_type"]').value = payload.details.cardType;
								form.submit()

							});
						});


						{/literal}{else}{literal}

						document.querySelector('input[name="payment_method_nonce"]').value = payload.nonce;

						form.submit();

						{/literal}{/if}{literal}

					});
				},true);
			});
		});
	</script>

{/literal}
