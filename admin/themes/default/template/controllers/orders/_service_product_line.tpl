{if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)}
	{assign var=product_price value=($product['unit_price_tax_excl'] + $product['ecotax'])}
{else}
	{assign var=product_price value=$product['unit_price_tax_incl']}
{/if}
<tr class="product-line-row" data-id_product="{$product.id_product}" data-id_order_detail="{$product.id_order_detail}">
	{if $refund_allowed}
		<td class="standard_refund_fields" style="display:none">
			<input type="checkbox" name="id_htl_booking[]" value="{$product.id|escape:'html':'UTF-8'}" {if isset($refundReqBookings) && ($product.id|in_array:$refundReqBookings)}disabled{/if}/>
		</td>
	{/if}
	{* <td class="text-center">
		{$data.room_num}
	</td> *}
	<td class="text-center">
		{if isset($product.image) && $product.image->id}{$product.image_tag}{/if}
	</td>
	<td class="text-center">
		<a href="{$link->getAdminLink('AdminProducts')|escape:'html':'UTF-8'}&amp;id_product={$product['product_id']|intval}&amp;updateproduct&amp;token={getAdminToken tab='AdminProducts'}">
			<span class="productName">{$product['product_name']}</span><br />
			{if $product.product_reference}{l s='Reference number:'} {$product.product_reference}<br />{/if}
			{if $product.product_supplier_reference}{l s='Supplier reference:'} {$product.product_supplier_reference}{/if}
		</a>
		{* <div class="row-editing-warning" style="display:none;">
			<div class="alert alert-warning">
				<strong>{l s='Editing this product line will remove the reduction and base price.'}</strong>
			</div>
		</div> *}
	</td>
    <td class="text-center unit_price_tax_excl">
        <span>{displayPrice price=$product.unit_price_tax_excl currency=$currency->id}</span>
    </td>
    <td class="text-center">
		<span class="product_quantity_show{if (int)$product['product_quantity'] - (int)$product['customized_product_quantity'] > 1} badge{/if}">{(int)$product['product_quantity'] - (int)$product['customized_product_quantity']}</span>
		{if $can_edit}
			<span class="product_quantity_edit" style="display:none;">
				<input type="text" name="product_quantity" class="edit_product_quantity" value="{$product['product_quantity']|htmlentities}"/>
			</span>
		{/if}
	</td>
	<td class="text-center">
        <span class="room_price_show">{displayPrice price=$product_price * ($product['product_quantity'] - $product['customizationQuantityTotal']) currency=$currency->id}</span>
        {* {if $can_edit}
            <div class="room_price_edit" style="display:none;">
                <input type="hidden" name="product_id_order_detail" class="edit_product_id_order_detail" value="{$product['id_order_detail']}" />
                <div class="form-group">
                    <div class="fixed-width-xl">
                        <div class="input-group">
                            {if $currency->format % 2}<div class="input-group-addon">{$currency->sign} {l s='tax excl.'}</div>{/if}
                            <input type="text" name="product_price_tax_excl" class="edit_product_price_tax_excl edit_product_price" value="{Tools::ps_round($product['unit_price_tax_excl'], 2)}"/>
                            {if !($currency->format % 2)}<div class="input-group-addon">{$currency->sign} {l s='tax excl.'}</div>{/if}
                        </div>
                    </div>
                    <br/>
                    <div class="fixed-width-xl">
                        <div class="input-group">
                            {if $currency->format % 2}<div class="input-group-addon">{$currency->sign} {l s='tax incl.'}</div>{/if}
                            <input type="text" name="product_price_tax_incl" class="edit_product_price_tax_incl edit_product_price" value="{Tools::ps_round($product['unit_price_tax_incl'], 2)}"/>
                            {if !($currency->format % 2)}<div class="input-group-addon">{$currency->sign} {l s='tax incl.'}</div>{/if}
                        </div>
                    </div>
                </div>
            </div>
		{/if} *}
	</td>

	{* {if isset($refundReqBookings) && $refundReqBookings}
		<td class="text-center">
			{if isset($data.refund_info) && $data.refund_info}
				<span class="badge" style="background-color:{$data.refund_info.color|escape:'html':'UTF-8'}">{$data.refund_info.name|escape:'html':'UTF-8'}</span>
			{/if}
		</td>
		<td class="text-center">
			{if isset($data.refund_info) && $data.refund_info}
				{convertPriceWithCurrency price=$data.refund_info.refunded_amount currency=$currency->id}
			{/if}
		</td>
	{/if} *}

	{* {if $data.booking_type == 1}
		<td class="text-center">
			{if isset($data.refund_info) && ($data.refund_info.refunded || $data.refund_info.denied)}
				<p class="text-center">--</p>
			{else}
				<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#mySwappigModal" data-id_order="{$order->id}" data-room_num='{$data.room_num}' data-date_from='{$data.date_from}' data-date_to='{$data.date_to}' data-id_room='{$data.id_room}' data-cust_name='{$data.alloted_cust_name}' data-cust_email='{$data.alloted_cust_email}' data-avail_rm_swap='{$data.avail_rooms_to_swap|@json_encode}' data-avail_rm_realloc='{$data.avail_rooms_to_realloc|@json_encode}'>
					{l s='Reallocate Room' mod='hotelreservationsystem'}
				</button>
			{/if}
		</td>
	{/if} *}
	{if ($can_edit && !$order->hasBeenDelivered())}
		<td class="room_invoice" style="display: none;">
		{if sizeof($invoices_collection)}
		<select name="product_invoice" class="edit_product_invoice">
			{foreach from=$invoices_collection item=invoice}
			<option value="{$invoice->id}" {*{if $invoice->id == $product['id_order_invoice']}selected="selected"{/if}*}>
				#{Configuration::get('PS_INVOICE_PREFIX', $current_id_lang, null, $order->id_shop)}{'%06d'|sprintf:$invoice->number}
			</option>
			{/foreach}
		</select>
		{else}
		&nbsp;
		{/if}
		</td>
		<td class="product_action text-right">
			{* edit/delete controls *}
			<div class="btn-group">
                {* <button type="button" class="btn btn-default delete_product_line">
					<i class="icon-trash"></i>
					{l s='Delete'}
				</button> *}
				<button type="button" class="btn btn-default edit_product_change_link">
					<i class="icon-pencil"></i>
					{l s='Edit'}
				</button>
				<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
					<span class="caret"></span>
				</button>
				<ul class="dropdown-menu" role="menu">
					<li>
						<a href="#" class="delete_product_line">
							<i class="icon-trash"></i>
							{l s='Delete'}
						</a>
					</li>
				</ul>
			</div>
			{* Update controls *}
			<button type="button" class="btn btn-default submitProductChange" style="display: none;">
				<i class="icon-ok"></i>
				{l s='Update'}
			</button>
			<button type="button" class="btn btn-default cancel_product_change_link" style="display: none;">
				<i class="icon-remove"></i>
				{l s='Cancel'}
			</button>
		</td>
	{/if}
</tr>