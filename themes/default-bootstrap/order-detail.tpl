{*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{if isset($order)}
<div class="box box-small clearfix">
	{if isset($reorderingAllowed) && $reorderingAllowed}
	<form id="submitReorder" action="{if isset($opc) && $opc}{$link->getPageLink('order-opc', true)}{else}{$link->getPageLink('order', true)}{/if}" method="post" class="submit">
		<input type="hidden" value="{$order->id}" name="id_order"/>
		<input type="hidden" value="" name="submitReorder"/>

		<a href="#" onclick="$(this).closest('form').submit(); return false;" class="button btn btn-default button-medium pull-right"><span>{l s='Reorder'}<i class="icon-chevron-right right"></i></span></a>
	</form>
	{/if}
	<p class="dark">
		<strong>{l s='Order Reference %s - placed on' sprintf=$order->getUniqReference()} {dateFormat date=$order->date_add full=0}</strong>
	</p>
</div>
<div class="info-order box">
	{if $carrier->id}<p><strong class="dark">{l s='Carrier'}</strong> {if $carrier->name == "0"}{$shop_name|escape:'html':'UTF-8'}{else}{$carrier->name|escape:'html':'UTF-8'}{/if}</p>{/if}
	<p><strong class="dark">{l s='Payment method'}</strong> <span class="color-myaccount">{$order->payment|escape:'html':'UTF-8'}</span></p>
	{if $invoice AND $invoiceAllowed}
	<p>
		<i class="icon-file-text"></i>
		<a target="_blank" href="{$link->getPageLink('pdf-invoice', true)}?id_order={$order->id|intval}{if $is_guest}&amp;secure_key={$order->secure_key|escape:'html':'UTF-8'}{/if}">{l s='Download your invoice as a PDF file.'}</a>
	</p>
	{/if}
	{if $order->recyclable}
	<p><i class="icon-repeat"></i>&nbsp;{l s='You have given permission to receive your order in recycled packaging.'}</p>
	{/if}
	{if $order->gift}
		<p><i class="icon-gift"></i>&nbsp;{l s='You have requested gift wrapping for this order.'}</p>
		<p><strong class="dark">{l s='Message'}</strong> {$order->gift_message|nl2br}</p>
	{/if}
</div>

{if count($order_history)}
<h1 class="page-heading">{l s='Follow your order\'s status step-by-step'}</h1>
<div class="table_block">
	<table class="detail_step_by_step table table-bordered">
		<thead>
			<tr>
				<th class="first_item">{l s='Date'}</th>
				<th class="last_item">{l s='Status'}</th>
			</tr>
		</thead>
		<tbody>
		{foreach from=$order_history item=state name="orderStates"}
			<tr class="{if $smarty.foreach.orderStates.first}first_item{elseif $smarty.foreach.orderStates.last}last_item{/if} {if $smarty.foreach.orderStates.index % 2}alternate_item{else}item{/if}">
				<td class="step-by-step-date">{dateFormat date=$state.date_add full=0}</td>
				<td><span{if isset($state.color) && $state.color} style="background-color:{$state.color|escape:'html':'UTF-8'}; border-color:{$state.color|escape:'html':'UTF-8'};"{/if} class="label{if isset($state.color) && Tools::getBrightness($state.color) > 128} dark{/if}">{$state.ostate_name|escape:'html':'UTF-8'}</span></td>
			</tr>
		{/foreach}
		</tbody>
	</table>
</div>
{/if}

{if isset($followup)}
<p class="bold">{l s='Click the following link to track the delivery of your order'}</p>
<a href="{$followup|escape:'html':'UTF-8'}">{$followup|escape:'html':'UTF-8'}</a>
{/if}

<div class="adresses_bloc">
	<div class="row">
		<div class="col-xs-12 col-sm-6"{if $order->isVirtual()} style="display:none;"{/if}>
			<ul class="address alternate_item box">
				<li><h3 class="page-subheading">{l s='Delivery address'} ({$address_delivery->alias})</h3></li>
				{foreach from=$dlv_adr_fields name=dlv_loop item=field_item}
					{if $field_item eq "company" && isset($address_delivery->company)}<li class="address_company">{$address_delivery->company|escape:'html':'UTF-8'}</li>
					{elseif $field_item eq "address2" && $address_delivery->address2}<li class="address_address2">{$address_delivery->address2|escape:'html':'UTF-8'}</li>
					{elseif $field_item eq "phone_mobile" && $address_delivery->phone_mobile}<li class="address_phone_mobile">{$address_delivery->phone_mobile|escape:'html':'UTF-8'}</li>
					{else}
							{assign var=address_words value=" "|explode:$field_item}
							<li>{foreach from=$address_words item=word_item name="word_loop"}{if !$smarty.foreach.word_loop.first} {/if}<span class="address_{$word_item|replace:',':''}">{$deliveryAddressFormatedValues[$word_item|replace:',':'']|escape:'html':'UTF-8'}</span>{/foreach}</li>
					{/if}
				{/foreach}
			</ul>
		</div>
		<div class="col-xs-12 col-sm-6">
			<ul class="address item {if $order->isVirtual()}full_width{/if} box">
				<li><h3 class="page-subheading">{l s='Invoice address'} ({$address_invoice->alias})</h3></li>
				{foreach from=$inv_adr_fields name=inv_loop item=field_item}
					{if $field_item eq "company" && isset($address_invoice->company)}<li class="address_company">{$address_invoice->company|escape:'html':'UTF-8'}</li>
					{elseif $field_item eq "address2" && $address_invoice->address2}<li class="address_address2">{$address_invoice->address2|escape:'html':'UTF-8'}</li>
					{elseif $field_item eq "phone_mobile" && $address_invoice->phone_mobile}<li class="address_phone_mobile">{$address_invoice->phone_mobile|escape:'html':'UTF-8'}</li>
					{else}
							{assign var=address_words value=" "|explode:$field_item}
							<li>{foreach from=$address_words item=word_item name="word_loop"}{if !$smarty.foreach.word_loop.first} {/if}<span class="address_{$word_item|replace:',':''}">{$invoiceAddressFormatedValues[$word_item|replace:',':'']|escape:'html':'UTF-8'}</span>{/foreach}</li>
					{/if}
				{/foreach}
			</ul>
		</div>
	</div>
</div>
{$HOOK_ORDERDETAILDISPLAYED}
{if !$is_guest}<form action="{$link->getPageLink('order-follow', true)|escape:'html':'UTF-8'}" method="post">{/if}
<div id="order-detail-content" class="table_block table-responsive">
	<table class="table table-bordered">
		<thead>
			<tr>
				{if $return_allowed}<th class="first_item"><input type="checkbox" /></th>{/if}
				<th class="{if $return_allowed}item{else}first_item{/if}">{l s='Reference'}</th>
				<th class="item">{l s='Product'}</th>
				<th class="item">{l s='Quantity'}</th>
				{if $order->hasProductReturned()}
					<th class="item">{l s='Returned'}</th>
				{/if}
				<th class="item">{l s='Unit price'}</th>
				<th class="last_item">{l s='Total price'}</th>
			</tr>
		</thead>
		<tfoot>
			{if $priceDisplay && $use_tax}
				<tr class="item">
					<td colspan="{if $return_allowed}2{else}1{/if}">
						<strong>{l s='Items (tax excl.)'}</strong>
					</td>
					<td colspan="{if $order->hasProductReturned()}5{else}4{/if}">
						<span class="price">{displayWtPriceWithCurrency price=$order->getTotalProductsWithoutTaxes() currency=$currency}</span>
					</td>
				</tr>
			{/if}
			<tr class="item">
				<td colspan="{if $return_allowed}2{else}1{/if}">
					<strong>{l s='Items'} {if $use_tax}{l s='(tax incl.)'}{/if} </strong>
				</td>
				<td colspan="{if $order->hasProductReturned()}5{else}4{/if}">
					<span class="price">{displayWtPriceWithCurrency price=$order->getTotalProductsWithTaxes() currency=$currency}</span>
				</td>
			</tr>
			{if $order->total_discounts > 0}
			<tr class="item">
				<td colspan="{if $return_allowed}2{else}1{/if}">
					<strong>{l s='Total vouchers'}</strong>
				</td>
				<td colspan="{if $order->hasProductReturned()}5{else}4{/if}">
					<span class="price-discount">{displayWtPriceWithCurrency price=$order->total_discounts currency=$currency convert=1}</span>
				</td>
			</tr>
			{/if}
			{if $order->total_wrapping > 0}
			<tr class="item">
				<td colspan="{if $return_allowed}2{else}1{/if}">
					<strong>{l s='Total gift wrapping cost'}</strong>
				</td>
				<td colspan="{if $order->hasProductReturned()}5{else}4{/if}">
					<span class="price-wrapping">{displayWtPriceWithCurrency price=$order->total_wrapping currency=$currency}</span>
				</td>
			</tr>
			{/if}
			<tr class="item">
				<td colspan="{if $return_allowed}2{else}1{/if}">
					<strong>{l s='Shipping & handling'} {if $use_tax}{l s='(tax incl.)'}{/if} </strong>
				</td>
				<td colspan="{if $order->hasProductReturned()}5{else}4{/if}">
					<span class="price-shipping">{displayWtPriceWithCurrency price=$order->total_shipping currency=$currency}</span>
				</td>
			</tr>
			<tr class="totalprice item">
				<td colspan="{if $return_allowed}2{else}1{/if}">
					<strong>{l s='Total'}</strong>
				</td>
				<td colspan="{if $order->hasProductReturned()}5{else}4{/if}">
					<span class="price">{displayWtPriceWithCurrency price=$order->total_paid currency=$currency}</span>
				</td>
			</tr>
		</tfoot>
		<tbody>
		{foreach from=$products item=product name=products}
			{if !isset($product.deleted)}
				{assign var='productId' value=$product.product_id}
				{assign var='productAttributeId' value=$product.product_attribute_id}
				{if isset($product.customizedDatas)}
					{assign var='productQuantity' value=$product.product_quantity-$product.customizationQuantityTotal}
				{else}
					{assign var='productQuantity' value=$product.product_quantity}
				{/if}
				<!-- Customized products -->
				{if isset($product.customizedDatas)}
					<tr class="item">
						{if $return_allowed}<td class="order_cb"></td>{/if}
						<td><label for="cb_{$product.id_order_detail|intval}">{if $product.product_reference}{$product.product_reference|escape:'html':'UTF-8'}{else}--{/if}</label></td>
						<td class="bold">
							<label for="cb_{$product.id_order_detail|intval}">{$product.product_name|escape:'html':'UTF-8'}</label>
						</td>
						<td>
						<input class="order_qte_input form-control grey"  name="order_qte_input[{$smarty.foreach.products.index}]" type="text" size="2" value="{$product.customizationQuantityTotal|intval}" />
							<div class="clearfix return_quantity_buttons">
								<a href="#" class="return_quantity_down btn btn-default button-minus"><span><i class="icon-minus"></i></span></a>
								<a href="#" class="return_quantity_up btn btn-default button-plus"><span><i class="icon-plus"></i></span></a>
							</div>
							<label for="cb_{$product.id_order_detail|intval}"><span class="order_qte_span editable">{$product.customizationQuantityTotal|intval}</span></label></td>
						{if $order->hasProductReturned()}
							<td>
								{$product['qty_returned']}
							</td>
						{/if}
						<td>
							<label class="price" for="cb_{$product.id_order_detail|intval}">
								{if $group_use_tax}
									{convertPriceWithCurrency price=$product.unit_price_tax_incl currency=$currency}
								{else}
									{convertPriceWithCurrency price=$product.unit_price_tax_excl currency=$currency}
								{/if}
							</label>
						</td>
						<td>
							<label class="price" for="cb_{$product.id_order_detail|intval}">
								{if isset($customizedDatas.$productId.$productAttributeId)}
									{if $group_use_tax}
										{convertPriceWithCurrency price=$product.total_customization_wt currency=$currency}
									{else}
										{convertPriceWithCurrency price=$product.total_customization currency=$currency}
									{/if}
								{else}
									{if $group_use_tax}
										{convertPriceWithCurrency price=$product.total_price_tax_incl currency=$currency}
									{else}
										{convertPriceWithCurrency price=$product.total_price_tax_excl currency=$currency}
									{/if}
								{/if}
							</label>
						</td>
					</tr>
					{foreach $product.customizedDatas  as $customizationPerAddress}
						{foreach $customizationPerAddress as $customizationId => $customization}
						<tr class="alternate_item">
							{if $return_allowed}<td class="order_cb"><input type="checkbox" id="cb_{$product.id_order_detail|intval}" name="customization_ids[{$product.id_order_detail|intval}][]" value="{$customizationId|intval}" /></td>{/if}
							<td colspan="2">
							{foreach from=$customization.datas key='type' item='datas'}
								{if $type == $CUSTOMIZE_FILE}
								<ul class="customizationUploaded">
									{foreach from=$datas item='data'}
										<li><img src="{$pic_dir}{$data.value}_small" alt="" class="customizationUploaded" /></li>
									{/foreach}
								</ul>
								{elseif $type == $CUSTOMIZE_TEXTFIELD}
								<ul class="typedText">{counter start=0 print=false}
									{foreach from=$datas item='data'}
										{assign var='customizationFieldName' value="Text #"|cat:$data.id_customization_field}
										<li>{$data.name|default:$customizationFieldName} : {$data.value}</li>
									{/foreach}
								</ul>
								{/if}
							{/foreach}
							</td>
							<td>
								<input class="order_qte_input form-control grey" name="customization_qty_input[{$customizationId|intval}]" type="text" size="2" value="{$customization.quantity|intval}" />
								<div class="clearfix return_quantity_buttons">
									<a href="#" class="return_quantity_down btn btn-default button-minus"><span><i class="icon-minus"></i></span></a>
									<a href="#" class="return_quantity_up btn btn-default button-plus"><span><i class="icon-plus"></i></span></a>
								</div>
								<label for="cb_{$product.id_order_detail|intval}"><span class="order_qte_span editable">{$customization.quantity|intval}</span></label>
							</td>
							<td colspan="2"></td>
						</tr>
						{/foreach}
					{/foreach}
				{/if}
				<!-- Classic products -->
				{if $product.product_quantity > $product.customizationQuantityTotal}
					<tr class="item">
						{if $return_allowed}<td class="order_cb"><input type="checkbox" id="cb_{$product.id_order_detail|intval}" name="ids_order_detail[{$product.id_order_detail|intval}]" value="{$product.id_order_detail|intval}" /></td>{/if}
						<td><label for="cb_{$product.id_order_detail|intval}">{if $product.product_reference}{$product.product_reference|escape:'html':'UTF-8'}{else}--{/if}</label></td>
						<td class="bold">
							<label for="cb_{$product.id_order_detail|intval}">
								{if $product.download_hash && $logable && $product.display_filename != '' && $product.product_quantity_refunded == 0 && $product.product_quantity_return == 0}
									{if isset($is_guest) && $is_guest}
									<a href="{$link->getPageLink('get-file', true, NULL, "key={$product.filename|escape:'html':'UTF-8'}-{$product.download_hash|escape:'html':'UTF-8'}&amp;id_order={$order->id}&secure_key={$order->secure_key}")|escape:'html':'UTF-8'}" title="{l s='Download this product'}">
									{else}
										<a href="{$link->getPageLink('get-file', true, NULL, "key={$product.filename|escape:'html':'UTF-8'}-{$product.download_hash|escape:'html':'UTF-8'}")|escape:'html':'UTF-8'}" title="{l s='Download this product'}">
									{/if}
										<img src="{$img_dir}icon/download_product.gif" class="icon" alt="{l s='Download product'}" />
									</a>
									{if isset($is_guest) && $is_guest}
										<a href="{$link->getPageLink('get-file', true, NULL, "key={$product.filename|escape:'html':'UTF-8'}-{$product.download_hash|escape:'html':'UTF-8'}&id_order={$order->id}&secure_key={$order->secure_key}")|escape:'html':'UTF-8'}" title="{l s='Download this product'}"> {$product.product_name|escape:'html':'UTF-8'} 	</a>
									{else}
									<a href="{$link->getPageLink('get-file', true, NULL, "key={$product.filename|escape:'html':'UTF-8'}-{$product.download_hash|escape:'html':'UTF-8'}")|escape:'html':'UTF-8'}" title="{l s='Download this product'}"> {$product.product_name|escape:'html':'UTF-8'} 	</a>
									{/if}
								{else}
									{$product.product_name|escape:'html':'UTF-8'}
								{/if}
							</label>
						</td>
						<td class="return_quantity">
							<input class="order_qte_input form-control grey" name="order_qte_input[{$product.id_order_detail|intval}]" type="text" size="2" value="{$productQuantity|intval}" />
							<div class="clearfix return_quantity_buttons">
								<a href="#" class="return_quantity_down btn btn-default button-minus"><span><i class="icon-minus"></i></span></a>
								<a href="#" class="return_quantity_up btn btn-default button-plus"><span><i class="icon-plus"></i></span></a>
							</div>
							<label for="cb_{$product.id_order_detail|intval}"><span class="order_qte_span editable">{$productQuantity|intval}</span></label></td>
						{if $order->hasProductReturned()}
							<td>
								{$product['qty_returned']}
							</td>
						{/if}
						<td class="price">
							<label for="cb_{$product.id_order_detail|intval}">
							{if $group_use_tax}
								{convertPriceWithCurrency price=$product.unit_price_tax_incl currency=$currency}
							{else}
								{convertPriceWithCurrency price=$product.unit_price_tax_excl currency=$currency}
							{/if}
							</label>
						</td>
						<td class="price">
							<label for="cb_{$product.id_order_detail|intval}">
							{if $group_use_tax}
								{convertPriceWithCurrency price=$product.total_price_tax_incl currency=$currency}
							{else}
								{convertPriceWithCurrency price=$product.total_price_tax_excl currency=$currency}
							{/if}
							</label>
						</td>
					</tr>
				{/if}
			{/if}
		{/foreach}
		{foreach from=$discounts item=discount}
			<tr class="item">
				<td>{$discount.name|escape:'html':'UTF-8'}</td>
				<td>{l s='Voucher'} {$discount.name|escape:'html':'UTF-8'}</td>
				<td><span class="order_qte_span editable">1</span></td>
				<td>&nbsp;</td>
				<td>{if $discount.value != 0.00}-{/if}{convertPriceWithCurrency price=$discount.value currency=$currency}</td>
				{if $return_allowed}
				<td>&nbsp;</td>
				{/if}
			</tr>
		{/foreach}
		</tbody>
	</table>
</div>
{if $return_allowed}
	<div id="returnOrderMessage">
		<h3 class="page-heading bottom-indent">{l s='Merchandise return'}</h3>
		<p>{l s='If you wish to return one or more products, please mark the corresponding boxes and provide an explanation for the return. When complete, click the button below.'}</p>
		<p class="form-group">
			<textarea class="form-control" cols="67" rows="3" name="returnText"></textarea>
		</p>
		<p class="form-group">
			<button type="submit" name="submitReturnMerchandise" class="btn btn-default button button-small"><span>{l s='Make an RMA slip'}<i class="icon-chevron-right right"></i></span></button>
			<input type="hidden" class="hidden" value="{$order->id|intval}" name="id_order" />
		</p>
	</div>
{/if}
{if !$is_guest}</form>{/if}
{assign var='carriers' value=$order->getShipping()}
{if $carriers|count > 0 && isset($carriers.0.carrier_name) && $carriers.0.carrier_name}
	<table class="table table-bordered footab">
		<thead>
			<tr>
				<th class="first_item">{l s='Date'}</th>
				<th class="item" data-sort-ignore="true">{l s='Carrier'}</th>
				<th data-hide="phone" class="item">{l s='Weight'}</th>
				<th data-hide="phone" class="item">{l s='Shipping cost'}</th>
				<th data-hide="phone" class="last_item" data-sort-ignore="true">{l s='Tracking number'}</th>
			</tr>
		</thead>
		<tbody>
			{foreach from=$carriers item=line}
			<tr class="item">
				<td data-value="{$line.date_add|regex_replace:"/[\-\:\ ]/":""}">{dateFormat date=$line.date_add full=0}</td>
				<td>{$line.carrier_name}</td>
				<td data-value="{if $line.weight > 0}{$line.weight|string_format:"%.3f"}{else}0{/if}">{if $line.weight > 0}{$line.weight|string_format:"%.3f"} {Configuration::get('PS_WEIGHT_UNIT')}{else}-{/if}</td>
				<td data-value="{if $order->getTaxCalculationMethod() == $smarty.const.PS_TAX_INC}{$line.shipping_cost_tax_incl}{else}{$line.shipping_cost_tax_excl}{/if}">{if $order->getTaxCalculationMethod() == $smarty.const.PS_TAX_INC}{displayPrice price=$line.shipping_cost_tax_incl currency=$currency->id}{else}{displayPrice price=$line.shipping_cost_tax_excl currency=$currency->id}{/if}</td>
				<td>
					<span class="shipping_number_show">{if $line.tracking_number}{if $line.url && $line.tracking_number}<a href="{$line.url|replace:'@':$line.tracking_number}">{$line.tracking_number}</a>{else}{$line.tracking_number}{/if}{else}-{/if}</span>
				</td>
			</tr>
			{/foreach}
		</tbody>
	</table>
{/if}
{if !$is_guest}
	{if count($messages)}
	<h3 class="page-heading">{l s='Messages'}</h3>
	 <div class="table_block">
		<table class="detail_step_by_step table table-bordered">
			<thead>
				<tr>
					<th class="first_item" style="width:150px;">{l s='From'}</th>
					<th class="last_item">{l s='Message'}</th>
				</tr>
			</thead>
			<tbody>
			{foreach from=$messages item=message name="messageList"}
				<tr class="{if $smarty.foreach.messageList.first}first_item{elseif $smarty.foreach.messageList.last}last_item{/if} {if $smarty.foreach.messageList.index % 2}alternate_item{else}item{/if}">
					<td>
						<strong class="dark">
							{if isset($message.elastname) && $message.elastname}
								{$message.efirstname|escape:'html':'UTF-8'} {$message.elastname|escape:'html':'UTF-8'}
							{elseif $message.clastname}
								{$message.cfirstname|escape:'html':'UTF-8'} {$message.clastname|escape:'html':'UTF-8'}
							{else}
								{$shop_name|escape:'html':'UTF-8'}
							{/if}
						</strong>
						<br />
						{dateFormat date=$message.date_add full=1}
					</td>
					<td>{$message.message|escape:'html':'UTF-8'|nl2br}</td>
				</tr>
			{/foreach}
			</tbody>
		</table>
	</div>
	{/if}
	{if isset($errors) && $errors}
		<div class="alert alert-danger">
			<p>{if $errors|@count > 1}{l s='There are %d errors' sprintf=$errors|@count}{else}{l s='There is %d error' sprintf=$errors|@count}{/if}</p>
			<ol>
			{foreach from=$errors key=k item=error}
				<li>{$error}</li>
			{/foreach}
			</ol>
		</div>
	{/if}
	{if isset($message_confirmation) && $message_confirmation}
	<p class="alert alert-success">
		{l s='Message successfully sent'}
	</p>
	{/if}
	<form action="{$link->getPageLink('order-detail', true)|escape:'html':'UTF-8'}" method="post" class="std" id="sendOrderMessage">
		<h3 class="page-heading bottom-indent">{l s='Add a message'}</h3>
		<p>{l s='If you would like to add a comment about your order, please write it in the field below.'}</p>
		<p class="form-group">
		<label for="id_product">{l s='Product'}</label>
			<select name="id_product" class="form-control">
				<option value="0">{l s='-- Choose --'}</option>
				{foreach from=$products item=product name=products}
					<option value="{$product.product_id}">{$product.product_name}</option>
				{/foreach}
			</select>
		</p>
		<p class="form-group">
			<textarea class="form-control" cols="67" rows="3" name="msgText"></textarea>
		</p>
		<div class="submit">
			<input type="hidden" name="id_order" value="{$order->id|intval}" />
			<input type="submit" class="unvisible" name="submitMessage" value="{l s='Send'}"/>
			<button type="submit" name="submitMessage" class="button btn btn-default button-medium"><span>{l s='Send'}<i class="icon-chevron-right right"></i></span></button>
		</div>
	</form>
{else}
<p class="alert alert-info"><i class="icon-info-sign"></i>{l s='You cannot return merchandise with a guest account'}</p>
{/if}
{/if}
