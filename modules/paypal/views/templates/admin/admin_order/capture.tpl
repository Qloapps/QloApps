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

{if $smarty.const._PS_VERSION_ >= 1.6}
<div class="row">
	<div class="col-lg-12">
		<div class="panel">
			<div class="panel-heading"><img src="{$base_url|escape:'htmlall':'UTF-8'}modules/{$module_name|escape:'htmlall':'UTF-8'}/{if $order_payment == 'paypal'}logo.gif{else}views/img/braintree.png{/if}" alt="" /> {if $order_payment == 'paypal'}{l s='PayPal Capture' mod='paypal'}{else}{l s='Braintree Capture' mod='paypal'}{/if}</div>
			{if  $list_captures|@count gt 0} 
				<table class="table" width="100%" cellspacing="0" cellpadding="0">
				  <tr>
				    <th>{l s='Capture date' mod='paypal'}</th>
				    <th>{l s='Capture Amount' mod='paypal'}</th> 
				    <th>{l s='Result Capture' mod='paypal'}</th>
				  </tr>
				{foreach from=$list_captures item=list}
				  <tr>
				    <td>{Tools::displayDate($list.date_add, $smarty.const.null,true)|escape:'htmlall':'UTF-8'}</td>
				    <td>{$list.capture_amount|escape:'htmlall':'UTF-8'}</td>
				    <td>{$list.result|escape:'htmlall':'UTF-8'}</td>
				  </tr>
				{/foreach}
				</table>
			{/if}
			<form method="post" action="{$smarty.server.REQUEST_URI|escape:'htmlall':'UTF-8'}">
				<p>{l s='There is still' mod='paypal'} {$rest_to_capture|escape:'htmlall':'UTF-8'} {$id_currency|escape:'htmlall':'UTF-8'} {l s='to capture.' mod='paypal'} {l s='How many do you want to capture :' mod='paypal'}</p>
				<input type="text" onchange="captureEdit();" name="totalCaptureMoney" style="width80%;" placeholder="{l s='Enter the money you want to capture (ex: 200.00)' mod='paypal'}"/>
		
				<input type="hidden" name="id_order" value="{$params.id_order|escape:'htmlall':'UTF-8'}" />
				<p><b>{l s='Information:' mod='paypal'}</b> {l s='Funds ready to be captured before shipping' mod='paypal'}</p>
				<p class="center">
					<button type="submit" class="btn btn-default" name="submitPayPalCapture" onclick="if (!confirm('{l s='Are you sure you want to capture?' mod='paypal'}'))return false;">
						
						{l s='Get the money' mod='paypal'}
					</button>
				</p>
			</form>
		</div>
	</div>
</div>
{else}
<br />
<fieldset {if isset($ps_version) && ($ps_version < '1.5')}style="width: 400px"{/if}>
	<legend><img src="{$base_url|escape:'htmlall':'UTF-8'}modules/{$module_name|escape:'htmlall':'UTF-8'}/{if $order_payment == 'paypal'}logo.gif{else}views/img/braintree.png{/if}" alt="" />{if $order_payment == 'paypal'}{l s='PayPal Capture' mod='paypal'}{else}{l s='Braintree Capture' mod='paypal'}{/if}</legend>
	<p><b>{l s='Information:' mod='paypal'}</b> {l s='Funds ready to be captured before shipping' mod='paypal'}</p>
	{if  $list_captures|@count gt 0} 
 		<table class="table" width="100%" cellspacing="0" cellpadding="0">
		  <tr>
		    <th>{l s='Capture date' mod='paypal'}</th>
		    <th>{l s='Capture Amount' mod='paypal'}</th> 
		    <th>{l s='Result Capture' mod='paypal'}</th>
		  </tr>
		{foreach from=$list_captures item=list}
		  <tr>
		    <td>{Tools::displayDate($list.date_add, $smarty.const.null,true)|escape:'htmlall':'UTF-8'}</td>
		    <td>{$list.capture_amount|escape:'htmlall':'UTF-8'}</td>
		    <td>{$list.result|escape:'htmlall':'UTF-8'}</td>
		  </tr>
		{/foreach}
		</table>
	{/if}
	<form method="post" action="{$smarty.server.REQUEST_URI|escape:'htmlall':'UTF-8'}">
		<p>{l s='There is still' mod='paypal'} {$rest_to_capture|escape:'htmlall':'UTF-8'} {$id_currency|escape:'htmlall':'UTF-8'} {l s='to capture.' mod='paypal'} {l s='How many do you want to capture :' mod='paypal'}</p>
		<input type="text" onchange="captureEdit();" name="totalCaptureMoney" style="width80%;" placeholder="{l s='Enter the money you want to capture (ex: 200.00)' mod='paypal'}"/>
		<input type="hidden" name="id_order" value="{$params.id_order|intval}" />
		<p class="center"><input type="submit" class="button" name="submitPayPalCapture" value="{l s='Get the money' mod='paypal'}" onclick="return confirm('{l s='Are you sure you want to capture?' mod='paypal'}');" /></p>
	</form>
</fieldset>
{literal}
<script>
function captureEdit(){
	var regexp = /^([0-9\s]{0,10})((\.|,)[0-9]{0,2})?$/i;
	if (!regexp.test($("input[name='totalCaptureMoney']").val()))
		alert('Syntax not valid ! ex : 2 000.00');
}
</script>
{/literal}
{/if}
