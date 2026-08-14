{*
* 2007-2017 PrestaShop
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
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<tr class="product-line-row" data-id_htl_booking="{$data.id|escape:'html':'UTF-8'}" data-id_room="{$data.id_room}" data-id_product="{$data.id_product}" data-id_hotel="{$data.id_hotel}" data-date_from="{$data.date_from}" data-date_to="{$data.date_to}" data-product_price="{$data.unit_amt_tax_incl}" data-id_order_detail="{$data.id_order_detail}">
	<td class="center">
        <p>{$data.room_num}</p>
        <p><a href="{$link->getAdminLink('AdminProducts')|escape:'html':'UTF-8'}&amp;id_product={$data.id_product}&amp;updateproduct" target="_blank"><span>{$data.room_type}</span></a></p>
        {if $data.is_back_order}
            <span class="overbooked_room">{l s='overbooked'}</span>
        {/if}
		{if isset($data['connected_rooms'][$data.id_room])}
			{include file="controllers/products/connected-rooms.tpl" htl_connected_rooms=$data['connected_rooms'][$data.id_room]}
		{/if}
    </td>
	<td class="center"><img width="80px" src="{$data.image_link}" title="{l s='Room image'}"></td>
	<td class="center">
		{assign var="is_full_date" value=($show_full_date && ($data['date_from']|date_format:'%D' == $data['date_to']|date_format:'%D'))}
		<p>{dateFormat date=$data.date_from full=$is_full_date}</p>
		<p>{l s='to'}</p>
		<p>{dateFormat date=$data.date_to full=$is_full_date}</p>
	</td>
	<td class="center">
		<p>{if $data['adults']}{$data['adults']}{/if} {if $data['adults'] > 1}{l s='Adults'}{else}{l s='Adult'}{/if}</p>
		{if $data['children']}
			<div class="dropdown">
				<a data-toggle="dropdown"><span>{$data['children']} {if $data['children'] > 1}{l s='Children'}{else}{l s='Child'}{/if}</span></a>
				<div class="dropdown-menu well well-sm">
					<label>{l s='Children Ages'}</label>
					{if isset($data['child_ages']) && $data['child_ages']}
						{foreach $data['child_ages'] as $childAge}
							<p class="">
								{if $childAge == 0}
                                    {l s='Child %s : Under 1'  sprintf=[$childAge@iteration]}
                                {else}
                                    {l s='Child %s : %s' sprintf=[$childAge@iteration, $childAge]} {if $childAge > 1}{l s='years'}{else}{l s='year'}{/if}
                                {/if}
							</p>
						{/foreach}
					{/if}
				</div>
			</div>
		{/if}
	</td>
	<td class="center">
		<p>
			{if ($data['id_status'] == $hotel_order_status['STATUS_CHECKED_IN']['id_status']) || ($data['id_status'] == $hotel_order_status['STATUS_CHECKED_OUT']['id_status'])}
				<span class="text-danger room_status">{dateFormat date=$data['check_in'] full=1}</span>
			{else}
				--
			{/if}
		</p>
		<p>
			{if $data['id_status'] == $hotel_order_status['STATUS_CHECKED_OUT']['id_status']}
				<span class="text-success room_status">{dateFormat date=$data['check_out'] full=1}</span>
			{else}
				--
			{/if}
		</p>
	</td>
	<td class="center">
		{if $data['booking_type'] == $ALLOTMENT_MANUAL}
			{l s='Manual'} &nbsp;{if $data['comment'] != ''}<a class="manual_allotment_comment" href="#" data-id_hotel_booking_detail="{$data['id']}"><i class="icon-info-circle"></i></a>{/if}
		{else}
			{l s='Auto'}
		{/if}
	</td>
	<td class="center">
		<p>
			<span class="room_type_current_price">
				{convertPriceWithCurrency price=$data['total_price_tax_excl'] currency=$currency->id}
			</span>
		</p>
			<p class="help-block">{convertPriceWithCurrency price=$data['paid_unit_price_tax_excl'] currency=$currency->id}/{l s='day'}</p>

	</td>
	<td class="center">
		{convertPriceWithCurrency price=($data['extra_demands_price_te'] + $data['additional_services_price_te'] + $data['convenience_fee_te'] + $data['additional_services_price_auto_add_te']) currency=$currency->id}
		{if $data['extra_demands']|count || $data['additional_services']|count}
			<a class="open_room_extra_services" href="#" date_from="{$data['date_from']}" date_to="{$data['date_to']}" id_product="{$data['id_product']}" id_room="{$data['id_room']}" id_order="{$order->id}" id_htl_booking="{$data['id']}">
				<i class="icon icon-lg icon-info-circle"></i>
			</a>
		{/if}
	</td>
	<td class="center">
		{convertPriceWithCurrency price=($data['total_room_tax']) currency=$currency->id}
	</td>
	<td class="center">
		{convertPriceWithCurrency price=($data['total_room_price_ti']) currency=$currency->id}
	</td>
	{if (isset($refundReqBookings) && $refundReqBookings)}
		<td class="center">
			<p>
	            {if $data.id|in_array:$refundReqBookings}
				    {if $data.is_cancelled}
					    <span class="badge badge-danger">{l s='Cancelled'}</span>
				    {elseif isset($data.refund_info) && (!$data.refund_info.refunded || $data.refund_info.id_customization)}
					    <span class="badge" style="background-color:{$data.refund_info.color|escape:'html':'UTF-8'}">{$data.refund_info.name|escape:'html':'UTF-8'}</span>
	                {else}
		    			<span>--</span>
	                {/if}
				{else}
	                <span>--</span>
	            {/if}
			</p>
				{if $data.is_refunded && isset($data.refund_info) && $data.refund_info}
					<p class="badge badge-success refunded_amount">
						{convertPriceWithCurrency price=$data.refund_info.refunded_amount currency=$currency->id}
					</p>
				{/if}
		</td>
	{/if}
	{if ($can_edit && !$order->hasBeenDelivered())}
		<td class="product_action center">
            <div class="btn-group pull-right">
                {if isset($refundReqBookings) && $refundReqBookings && $data.id|in_array:$refundReqBookings && $data.is_cancelled}
                    <button href="#" class="btn btn-default delete_room_line">
                        <i class="icon-trash"></i>
                        {l s='Delete'}
                    </button>
                {else}
                    <button href="#" class="btn btn-default edit_room_change_link" data-product_line_data="{$data|json_encode|escape}">
                        <i class="icon-pencil"></i>
                        {l s='Edit'}
                    </button>
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        {if isset($refundReqBookings) && $refundReqBookings && $data.id|in_array:$refundReqBookings && $data.is_refunded}
                        {else}
                            <li>
                                <a href="#" class="open_room_status_form" data-id_hotel_booking_detail="{$data['id']}" data-id_order="{$data['id_order']}" data-id_status="{$data['id_status']}" data-id_room="{$data['id_room']}" data-date_from="{$data['date_from']|date_format:"%Y-%m-%d"}" data-date_to="{$data['date_to']|date_format:"%Y-%m-%d"}" data-check_in_time="{$data['check_in_time']}" data-check_out_time="{$data['check_out_time']}" data-check_in="{$data['check_in']}" data-check_out="{$data['check_out']}">
                                    <i class="icon-clock-o"></i>
                                    {l s='Room Status'}
                                </a>
                            </li>
                        {/if}
                        <li>
                            <a href="#" onclick="BookingDocumentsModal.init({$data.id|intval}, this); return false;">
                                <i class="icon-file-text"></i>
                                {l s='Documents'}
                                <span class="badge badge-info">{if $data.num_checkin_documents > 0}{$data.num_checkin_documents}{else}0{/if}</span>
                            </a>
                        </li>
                        <li>
                            <a title="{l s='Guest Registration Card'}" href="{$link->getAdminLink('AdminPdf')|escape:'html':'UTF-8'}&amp;submitAction=generateGuestRegistrationFormPDF&amp;id_hotel_booking_detail={$data['id']}" target="_blank">
                                <i class="icon-download"></i> {l s='GRC'}
                            </a>
                        </li>
                        <li>
                            <a href="#" class="room_reallocate_swap" id="reallocate_room_{$data['id']}" data-room_type_name="{$data['room_type_name']}" data-toggle="modal" data-target="#mySwappigModal" data-id_htl_booking="{$data['id']}" data-id_order="{$data['id_order']}" data-room_num='{$data.room_num}' data-id_room_type='{$data.id_product}' data-cust_name='{$data.alloted_cust_name}' data-cust_email='{$data.alloted_cust_email}' data-avail_rm_swap='{$data.avail_rooms_to_swap|@json_encode}' data-avail_realloc_room_types='{$data.avail_room_types_to_realloc|@json_encode}' data-allotment_type='{$data.booking_type}' data-allotment_type_label='{if $data.booking_type == $ALLOTMENT_MANUAL}{l s='Manual'}{else}{l s='Auto'}{/if}' data-comment='{$data.comment}'>
                                <i class="icon-refresh"></i>
                                {l s='Reallocate/Swap Room'}
                            </a>
                        </li>
                        <li>
                            <a href="#" class="delete_room_line">
                                <i class="icon-trash"></i>
                                {l s='Delete'}
                            </a>
                        </li>
                    </ul>
                {/if}
			</div>
		</td>
	{/if}
</tr>