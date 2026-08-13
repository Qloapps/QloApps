{**
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

{assign var=pdf_style_template value=$smarty.const._PS_PDF_DIR_|cat:'invoice.style-tab.tpl'}
{include file=$pdf_style_template}

<table width="100%" id="booking-body" border="0" cellpadding="0" cellspacing="0" style="margin:0;">

    <tr>
        <td colspan="12">
            <table class="bordered-table" width="100%" cellpadding="5" cellspacing="0" nobr="true">
                <tbody>
                    <tr>
                        <th colspan="6" class="header-left small">{l s='BOOKING INFORMATION' pdf='true'}</th>
                    </tr>
                    <tr>
                        <td class="small white"><span class="bold">{l s='Booking Reference' pdf='true'}</span></td>
                        <td class="small white">{$order->reference|escape:'html':'UTF-8'}</td>
                        <td class="small white"><span class="bold">{l s='Booking Date' pdf='true'}</span></td>
                        <td class="small white">{$booking_date|escape:'html':'UTF-8'}</td>
                        <td class="small white"><span class="bold">{l s='Voucher Issue Date' pdf='true'}</span></td>
                        <td class="small white">{$issue_date|escape:'html':'UTF-8'}</td>
                    </tr>
                    <tr>
                        <td class="small white"><span class="bold">{l s='Status' pdf='true'}</span></td>
                        <td class="small white">{if $order_state && isset($order_state.name)}{$order_state.name|escape:'html':'UTF-8'}{/if}</td>
                        <td class="small white"><span class="bold">{l s='Total Rooms' pdf='true'}</span></td>
                        <td class="small white">{$total_rooms|intval}</td>
                        <td class="small white"><span class="bold">{l s='Total Nights' pdf='true'}</span></td>
                        <td class="small white">{$total_room_nights|intval}</td>
                    </tr>
                </tbody>
            </table>
        </td>
    </tr>

    <tr>
        <td colspan="12" height="12">&nbsp;</td>
    </tr>

    <tr nobr="true">
        <td colspan="6" class="left" valign="top">
            <table class="bordered-table" width="100%" cellpadding="5" cellspacing="0" nobr="true">
                <tbody>
                    <tr>
                        <th colspan="2" class="header-left small">{l s='PROPERTY DETAILS' pdf='true'}</th>
                    </tr>
                    <tr>
                        <td class="small white" width="30%"><span class="bold">{l s='Property' pdf='true'}</span></td>
                        <td class="small white" width="70%">{if $hotel}{$hotel->hotel_name|escape:'html':'UTF-8'}{/if}</td>
                    </tr>
                    <tr>
                        <td class="small white" width="30%"><span class="bold">{l s='Email' pdf='true'}</span></td>
                        <td class="small white" width="70%">{if $hotel}{$hotel->email|escape:'html':'UTF-8'}{/if}</td>
                    </tr>
                    <tr>
                        <td class="small white" width="30%"><span class="bold">{l s='Phone' pdf='true'}</span></td>
                        <td class="small white" width="70%">{if $hotel}{$hotel->phone|escape:'html':'UTF-8'}{/if}</td>
                    </tr>
                    <tr>
                        <td class="small white" width="30%"><span class="bold">{l s='Address' pdf='true'}</span></td>
                        <td class="small white" width="70%">{if $hotel_address}{$hotel_address nofilter}{else}--{/if}</td>
                    </tr>
                </tbody>
            </table>
        </td>
        {* <td colspan="1">&nbsp;</td> *}
        <td colspan="6" class="left" valign="top">
            <table class="bordered-table" width="100%" cellpadding="5" cellspacing="0" nobr="true">
                <tbody>
                    <tr>
                        <th colspan="2" class="header-left small">{l s='GUEST INFORMATION' pdf='true'}</th>
                    </tr>
                    <tr>
                        <td class="small white" width="35%"><span class="bold">{l s='Primary Guest' pdf='true'}</span></td>
                        <td class="small white" width="65%">{$guest->firstname|escape:'html':'UTF-8'} {$guest->lastname|escape:'html':'UTF-8'}</td>
                    </tr>
                    <tr>
                        <td class="small white" width="35%"><span class="bold">{l s='Email' pdf='true'}</span></td>
                        <td class="small white" width="65%">{$guest->email|escape:'html':'UTF-8'}</td>
                    </tr>
                    <tr>
                        <td class="small white" width="35%"><span class="bold">{l s='Phone' pdf='true'}</span></td>
                        <td class="small white" width="65%">{$guest->phone|escape:'html':'UTF-8'}</td>
                    </tr>
                    <tr>
                        <td class="small white" width="35%"><span class="bold">{l s='Address' pdf='true'}</span></td>
                        <td class="small white" width="65%">{if $guest_address|strip_tags|trim}{$guest_address nofilter}{else}--{/if}</td>
                    </tr>
                    <tr>
                        <td class="small white" width="35%"><span class="bold">{l s='Total Guests' pdf='true'}</span></td>
                        <td class="small white" width="65%">{$total_guests|intval}</td>
                    </tr>
                </tbody>
            </table>
        </td>
    </tr>

    <tr>
        <td colspan="12" height="12">&nbsp;</td>
    </tr>

    <tr>
        <td colspan="12">
            <table class="bordered-table" width="100%" cellpadding="4" cellspacing="0" nobr="true">
                <thead>
                    <tr>
                        <th colspan="7" class="header-left small">{l s='ROOM DETAILS' pdf='true'}</th>
                    </tr>
                    <tr>
                        <th class="header-left small" width="24%">{l s='Room Description' pdf='true'}</th>
                        <th class="header-left small" width="14%">{l s='Tax Rate(s)' pdf='true'}</th>
                        <th class="header-left small" width="14%">{l s='Unit Price' pdf='true'}<br />{l s='(Tax excl.)' pdf='true'}</th>
                        <th class="header-left small" width="14%">{l s='Rooms' pdf='true'}</th>
                        <th class="header-left small" width="11%">{l s='Check-in Date' pdf='true'}</th>
                        <th class="header-left small" width="11%">{l s='Check-out Date' pdf='true'}</th>
                        <th class="header-left small" width="12%">{l s='Total' pdf='true'}<br />{l s='(Tax excl.)' pdf='true'}</th>
                    </tr>
                </thead>
                <tbody>
                    {if $room_type_rows}
                        {foreach from=$room_type_rows item=room_type_row}
                            {cycle values="color_line_even,color_line_odd" assign=bgcolor_class}
                            <tr class="{$bgcolor_class}">
                                <td class="small white left" width="24%">{$room_type_row.room_type_name|escape:'html':'UTF-8'}</td>
                                <td class="small white center" width="14%">{$room_type_row.order_detail_tax_label}</td>
                                <td class="small white center" width="14%">{displayPrice currency=$order->id_currency price=$room_type_row.unit_price_tax_excl}</td>
                                <td class="small white left" width="14%">
                                    {if $room_type_row.adults <= 9}0{/if}{$room_type_row.adults|intval} {if $room_type_row.adults > 1}{l s='Adults' pdf='true'}{else}{l s='Adult' pdf='true'}{/if}{if $room_type_row.children}, {if $room_type_row.children <= 9}0{/if}{$room_type_row.children|intval} {if $room_type_row.children > 1}{l s='Children' pdf='true'}{else}{l s='Child' pdf='true'}{/if}{/if}<br />{if $room_type_row.rooms <= 9}0{/if}{$room_type_row.rooms|intval} {if $room_type_row.rooms > 1}{l s='Rooms' pdf='true'}{else}{l s='Room' pdf='true'}{/if}
                                </td>
                                <td class="small white center" width="11%" nowrap="nowrap">{$room_type_row.check_in_date|escape:'html':'UTF-8'}</td>
                                <td class="small white center" width="11%" nowrap="nowrap">{$room_type_row.check_out_date|escape:'html':'UTF-8'}</td>
                                <td class="small white center" width="12%">{displayPrice currency=$order->id_currency price=$room_type_row.total_price_tax_excl}</td>
                            </tr>
                        {/foreach}
                    {else}
                        <tr>
                            <td class="white center" colspan="7">{l s='No room details available.' pdf='true'}</td>
                        </tr>
                    {/if}
                </tbody>
            </table>
        </td>
    </tr>

    {if $service_groups}
        <tr>
            <td colspan="12" height="12">&nbsp;</td>
        </tr>
        <tr>
            <td colspan="12">
                <table class="bordered-table" width="100%" cellpadding="5" cellspacing="0">
                    <tbody>
                        <tr>
                            <th colspan="3" class="header-left small">{l s='SERVICES DETAILS' pdf='true'}</th>
                        </tr>
                        <tr>
                            <th class="header-left small" width="35%">{l s='Room Type' pdf='true'}</th>
                            <th class="header-left small" width="45%">{l s='Service Name' pdf='true'}</th>
                            <th class="header-left small" width="20%">{l s='Qty' pdf='true'}</th>
                        </tr>
                        {foreach from=$service_groups item=group}
                            {foreach from=$group.services item=service name=service_loop}
                                <tr>
                                    {if $smarty.foreach.service_loop.first}
                                        <td class="small white" width="35%" rowspan="{$group.services|@count}">{$group.room_type_name|escape:'html':'UTF-8'}<br />
                                            {$group.check_in_date|escape:'html':'UTF-8'} - {$group.check_out_date|escape:'html':'UTF-8'}<br />
                                            {l s='Room' pdf='true'} - {$group.room_count|intval}
                                        </td>
                                    {/if}
                                    <td class="small white" width="45%">{$service.name|escape:'html':'UTF-8'}</td>
                                    <td class="small white" width="20%">{$service.quantity|intval}</td>
                                </tr>
                            {/foreach}
                        {/foreach}
                    </tbody>
                </table>
            </td>
        </tr>
    {/if}

</table>

<table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin:0;"><tr><td height="12">&nbsp;</td></tr></table>

<table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin:0;" nobr="true">
    <tr>
        <td width="50%" class="left" valign="top">
            <table class="bordered-table" width="100%" cellpadding="5" cellspacing="0">
                <tbody>
                    <tr>
                        <th colspan="2" class="header-left small">{l s='PAYMENT INFORMATION' pdf='true'}</th>
                    </tr>
                    <tr>
                        <td class="grey left small bold" width="40%">{l s='Payment Method' pdf='true'}</td>
                        <td class="white left small" width="60%">{$payment_method nofilter}</td>
                    </tr>
                    <tr>
                        <td class="grey left small bold" width="40%">{l s='Payment Status' pdf='true'}</td>
                        <td class="white left small" width="60%">{if $order_state && isset($order_state.name)}{$order_state.name|escape:'html':'UTF-8'}{/if}</td>
                    </tr>
                </tbody>
            </table>
        </td>
        <td width="50%" class="left" valign="top">
            <table class="bordered-table" width="100%" cellpadding="5" cellspacing="0">
                <tbody>
                    <tr>
                        <th colspan="2" class="header-left small">{l s='TOTAL AMOUNT' pdf='true'}</th>
                    </tr>
                    {if isset($footer.room_price_tax_excl) && $footer.room_price_tax_excl}
                        <tr>
                            <td class="grey small" width="70%">{l s='Total Rooms Cost (tax excl.)' pdf='true'}</td>
                            <td class="white right small" width="30%">{displayPrice currency=$order->id_currency price=$footer.room_price_tax_excl}</td>
                        </tr>
                    {/if}
                    {if isset($footer.service_products_price_tax_excl) && $footer.service_products_price_tax_excl}
                        <tr>
                            <td class="grey small" width="70%">{l s='Total Products cost (tax excl.)' pdf='true'}</td>
                            <td class="white right small" width="30%">{displayPrice currency=$order->id_currency price=$footer.service_products_price_tax_excl}</td>
                        </tr>
                    {/if}
                    {if isset($footer.additional_service_price_tax_excl) && $footer.additional_service_price_tax_excl}
                        <tr>
                            <td class="grey small" width="70%">{l s='Extra Services Cost (tax excl.)' pdf='true'}</td>
                            <td class="white right small" width="30%">{displayPrice currency=$order->id_currency price=$footer.additional_service_price_tax_excl}</td>
                        </tr>
                    {/if}
                    {if isset($footer.total_convenience_fee_te) && $footer.total_convenience_fee_te}
                        <tr>
                            <td class="grey small" width="70%">{l s='Convenience Fee (tax excl.)' pdf='true'}</td>
                            <td class="white right small" width="30%">{displayPrice currency=$order->id_currency price=$footer.total_convenience_fee_te}</td>
                        </tr>
                    {/if}
                    <tr>
                        <td class="grey small bold" width="70%">{l s='Total (Tax excl.)' pdf='true'}</td>
                        <td class="white right small bold" width="30%">{displayPrice currency=$order->id_currency price=$footer.total_without_discount_te}</td>
                    </tr>
                    {if $footer.total_tax_without_discount > 0}
                        <tr>
                            <td class="grey small bold" width="70%">{l s='Total Tax' pdf='true'}</td>
                            <td class="white right small bold" width="30%">{displayPrice currency=$order->id_currency price=$footer.total_tax_without_discount}</td>
                        </tr>
                    {/if}
                    {if isset($footer.product_discounts_tax_incl) && $footer.product_discounts_tax_incl > 0}
                        <tr>
                            <td class="grey small bold" width="70%">{l s='Total Discounts' pdf='true'}</td>
                            <td class="white right small bold" width="30%">- {displayPrice currency=$order->id_currency price=$footer.product_discounts_tax_incl}</td>
                        </tr>
                    {/if}
                    <tr>
                        <td class="grey small bold big" width="70%">{l s='Final Booking Amount' pdf='true'}</td>
                        <td class="white right small bold big" width="30%">{displayPrice currency=$order->id_currency price=$footer.total_paid_tax_incl}</td>
                    </tr>
                    {if $footer.amount_due > 0}
                        <tr>
                            <td class="grey small bold big" width="70%">{l s='Due Amount' pdf='true'}</td>
                            <td class="white right small bold big" width="30%">{displayPrice currency=$order->id_currency price=$footer.amount_due}</td>
                        </tr>
                    {/if}
                </tbody>
            </table>
        </td>
    </tr>
</table>

{if $cancellation_policy}
    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin:0;"><tr><td height="12">&nbsp;</td></tr></table>
    <table class="bordered-table" width="100%" cellpadding="5" cellspacing="0" nobr="true">
        <tbody>
            <tr>
                <th colspan="2" class="header-left small">{l s='CANCELLATION POLICY' pdf='true'}</th>
            </tr>
            <tr>
                <td class="small white" colspan="2">{$cancellation_policy nofilter}</td>
            </tr>
        </tbody>
    </table>
{/if}

<table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin:0;"><tr><td height="12">&nbsp;</td></tr></table>

<table class="bordered-table" width="100%" cellpadding="5" cellspacing="0" nobr="true">
    <tbody>
        <tr>
            <th class="header-left small">{l s='PROPERTY POLICIES' pdf='true'}</th>
        </tr>
        <tr>
            <td class="small white">
                <span class="bold">{l s='Check-in Time:' pdf='true'}</span> 
                {if $hotel && $hotel->check_in && $hotel->check_in != '00:00:00'}{$hotel->check_in|escape:'html':'UTF-8'}{else}__________{/if}
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span class="bold">{l s='Check-out Time:' pdf='true'}</span> 
                {if $hotel && $hotel->check_out && $hotel->check_out != '00:00:00'}{$hotel->check_out|escape:'html':'UTF-8'}{else}__________{/if}
            </td>
        </tr>
        <tr>
            <td class="small white">
                <span class="bold">{l s='Hotel Policies:' pdf='true'}</span><br />
                <br />
                {if isset($payment_policy) && $payment_policy|strip_tags|trim}
                    {$payment_policy nofilter}
                {else}
                    ______________________________<br />
                    ______________________________<br />
                    ______________________________
                {/if}
            </td>
        </tr>
    </tbody>
</table>

{if isset($HOOK_DISPLAY_PDF) && $HOOK_DISPLAY_PDF|strip_tags|trim}
    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin:0;"><tr><td height="10">&nbsp;</td></tr></table>
    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="margin:0;">
        <tr>
            <td>{$HOOK_DISPLAY_PDF}</td>
        </tr>
    </table>
{/if}
