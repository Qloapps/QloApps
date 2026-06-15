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
<table id="addresses-tab" cellspacing="0" cellpadding="0">
	<tr>
		<!-- <td width="33%"><span class="bold"> </span><br/><br/>
			{if isset($order_invoice)}{$order_invoice->shop_address}{/if}
		</td> -->
		<!-- <td width="33%">{if $delivery_address}<span class="bold">{l s='Delivery Address' pdf='true'}</span><br/><br/>
				{$delivery_address}
			{/if}
		</td> -->
		<td width="33%">
			{if !empty($hotel_address)}
				<span class="bold">{l s='Hotel Detail' pdf='true'}</span><br/><br/>
				{$hotel_address}
			{/if}
		</td>
		<td  width="33%"></td>
		<td width="33%">
			<span class="bold">{l s='Customer Detail' pdf='true'}</span><br/><br/>
			{if $invoice_address}
				{$invoice_address}
			{else}
				{$customer->firstname} {$customer->lastname}
				<br>
				{$customer->phone}
			{/if}
		</td>
	</tr>
</table>