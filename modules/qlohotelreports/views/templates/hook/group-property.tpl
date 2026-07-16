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

<h3 class="qlo-report-heading">{$report_label|escape:'html':'UTF-8'}</h3>

{* ── Filters ─────────────────────────────────────────────────────────────── *}
<form method="get" action="{$filter_base_url|escape:'html':'UTF-8'}" class="form-horizontal clearfix list_action_wrapper">
    <input type="hidden" name="controller" value="AdminStats">
    <input type="hidden" name="module" value="qlohotelreports">
    <input type="hidden" name="tab" value="{$active_report|escape:'html':'UTF-8'}">
    {if isset($smarty.get.token)}<input type="hidden" name="token" value="{$smarty.get.token|escape:'html':'UTF-8'}">{/if}
    <div class="list_filters">
        {if $hotels|count > 1}
        <div class="row">
            <label class="col-xs-3">{l s='Hotel' mod='qlohotelreports'}</label>
            <div class="col-xs-9">
                <select name="id_hotel" class="form-control">
                    <option value="0"{if !$id_hotel} selected="selected"{/if}>{l s='All Hotels' mod='qlohotelreports'}</option>
                    {foreach $hotels as $hotel}
                    <option value="{$hotel.id|intval}"{if $id_hotel == $hotel.id} selected="selected"{/if}>{$hotel.hotel_name|escape:'html':'UTF-8'}</option>
                    {/foreach}
                </select>
            </div>
        </div>
        {/if}
        {if $active_report == 'out-of-order'}
        <div class="row">
            <label class="col-xs-3">{l s='Room Type' mod='qlohotelreports'}</label>
            <div class="col-xs-9">
                <select name="id_product" class="form-control">
                    <option value="0"{if !$filter_id_product} selected="selected"{/if}>{l s='All' mod='qlohotelreports'}</option>
                    {foreach $room_types as $roomType}
                    <option value="{$roomType.id_product|intval}"{if $filter_id_product == $roomType.id_product} selected="selected"{/if}>
                        {$roomType.room_type_name|escape:'html':'UTF-8'}
                    </option>
                    {/foreach}
                </select>
            </div>
        </div>
        {if $available_floors}
        <div class="row">
            <label class="col-xs-3">{l s='Floor' mod='qlohotelreports'}</label>
            <div class="col-xs-9">
                <select name="floor" class="form-control">
                    <option value=""{if !$filter_floor} selected="selected"{/if}>{l s='All Floors' mod='qlohotelreports'}</option>
                    {foreach $available_floors as $fl}
                    <option value="{$fl.floor|escape:'html':'UTF-8'}"{if $filter_floor == $fl.floor} selected="selected"{/if}>{$fl.floor|escape:'html':'UTF-8'}</option>
                    {/foreach}
                </select>
            </div>
        </div>
        {/if}
        {/if}
        <div class="actions">
            <hr>
            <span class="pull-right">
                <button type="submit" class="btn btn-sm btn-default">
                    <i class="icon-filter"></i> {l s='Apply' mod='qlohotelreports'}
                </button>
            </span>
        </div>
    </div>
</form>

{* ═══════════════════════════════════════════════════════════════════════════
   DAILY SUMMARY
   ═══════════════════════════════════════════════════════════════════════════ *}
{if $active_report == 'daily-summary'}

    <div class="row">
    <div class="col-lg-12">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>{l s='Date' mod='qlohotelreports'}</th>
                    <th class="text-center">{l s='Total Rooms' mod='qlohotelreports'}</th>
                    <th class="text-center">{l s='Rooms Sold' mod='qlohotelreports'}</th>
                    <th class="text-center">{l s='Occupancy %' mod='qlohotelreports'}</th>
                    <th class="text-right">{l s='ADR (Avg. Daily Rate)' mod='qlohotelreports'}</th>
                    <th class="text-right">{l s='RevPAR (Rev. Per Avail. Room)' mod='qlohotelreports'}</th>
                    <th class="text-right">{l s='Total Revenue' mod='qlohotelreports'}</th>
                    <th class="text-center">{l s='Arrivals' mod='qlohotelreports'}</th>
                    <th class="text-center">{l s='Departures' mod='qlohotelreports'}</th>
                    <th class="text-center">{l s='In-house Guests' mod='qlohotelreports'}</th>
                    <th class="text-center">{l s='Cancellations' mod='qlohotelreports'}</th>
                    <th class="text-center">{l s='No-Shows' mod='qlohotelreports'}</th>
                </tr>
            </thead>
            <tbody>
                {if $daily_rows}
                    {foreach $daily_rows as $dailySummaryRow}
                    <tr>
                        <td>{$dailySummaryRow.date|escape:'html':'UTF-8'}</td>
                        <td class="text-center">{$dailySummaryRow.total_rooms|intval}</td>
                        <td class="text-center">{$dailySummaryRow.rooms_sold|intval}</td>
                        <td class="text-center">{$dailySummaryRow.occupancy|string_format:'%.1f'}%</td>
                        <td class="text-right">{displayPrice price=$dailySummaryRow.adr currency=$id_currency}</td>
                        <td class="text-right">{displayPrice price=$dailySummaryRow.revpar currency=$id_currency}</td>
                        <td class="text-right">{displayPrice price=$dailySummaryRow.revenue currency=$id_currency}</td>
                        <td class="text-center">{$dailySummaryRow.arrivals|intval}</td>
                        <td class="text-center">{$dailySummaryRow.departures|intval}</td>
                        <td class="text-center">{$dailySummaryRow.inhouse_guests|intval}</td>
                        <td class="text-center">{$dailySummaryRow.cancels|intval}</td>
                        <td class="text-center">&mdash;</td>
                    </tr>
                    {/foreach}
                {else}
                    <tr>
                        <td class="list-empty" colspan="12">
                            <div class="list-empty-msg">
                                <i class="icon-warning-sign list-empty-icon"></i>
                                {l s='No data for the selected period.' mod='qlohotelreports'}
                            </div>
                        </td>
                    </tr>
                {/if}
            </tbody>
        </table>
    </div>
    </div>
    </div>

{* ═══════════════════════════════════════════════════════════════════════════
   HOTEL COMPARISON
   ═══════════════════════════════════════════════════════════════════════════ *}
{elseif $active_report == 'hotel-comparison'}

    <div class="row">
    <div class="col-lg-12">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>{l s='Hotel Name' mod='qlohotelreports'}</th>
                    <th class="text-center">{l s='Total Rooms' mod='qlohotelreports'}</th>
                    <th class="text-center">{l s='Rooms Sold' mod='qlohotelreports'}</th>
                    <th class="text-center">{l s='Occupancy %' mod='qlohotelreports'}</th>
                    <th class="text-right">{l s='Room Revenue' mod='qlohotelreports'}</th>
                    <th class="text-right">{l s='Extra Service Revenue' mod='qlohotelreports'}</th>
                    <th class="text-right">{l s='Gross Revenue' mod='qlohotelreports'}</th>
                    <th class="text-right">{l s='ADR (Avg. Daily Rate)' mod='qlohotelreports'}</th>
                    <th class="text-right">{l s='RevPAR (Rev. Per Avail. Room)' mod='qlohotelreports'}</th>
                    <th class="text-center">{l s='Total Bookings' mod='qlohotelreports'}</th>
                    <th class="text-center">{l s='Total Cancellations' mod='qlohotelreports'}</th>
                    <th class="text-center">{l s='Cancel Rate %' mod='qlohotelreports'}</th>
                    <th class="text-center">{l s='No-Shows' mod='qlohotelreports'}</th>
                    <th class="text-center">{l s='Avg LOS' mod='qlohotelreports'}</th>
                    <th class="text-right">{l s='Outstanding Balance' mod='qlohotelreports'}</th>
                </tr>
            </thead>
            <tbody>
                {if $hotel_rows}
                    {foreach $hotel_rows as $hotelRow}
                    <tr>
                        <td>{$hotelRow.hotel_name|escape:'html':'UTF-8'}</td>
                        <td class="text-center">{$hotelRow.total_rooms|intval}</td>
                        <td class="text-center">{$hotelRow.rooms_sold|intval}</td>
                        <td class="text-center">{$hotelRow.occupancy|string_format:'%.1f'}%</td>
                        <td class="text-right">{displayPrice price=$hotelRow.room_revenue currency=$id_currency}</td>
                        <td class="text-right">{displayPrice price=$hotelRow.extra_service_rev currency=$id_currency}</td>
                        <td class="text-right">{displayPrice price=$hotelRow.gross_revenue currency=$id_currency}</td>
                        <td class="text-right">{displayPrice price=$hotelRow.adr currency=$id_currency}</td>
                        <td class="text-right">{displayPrice price=$hotelRow.revpar currency=$id_currency}</td>
                        <td class="text-center">{$hotelRow.bookings|intval}</td>
                        <td class="text-center">{$hotelRow.cancellations|intval}</td>
                        <td class="text-center">{$hotelRow.cancel_rate_pct|string_format:'%.1f'}%</td>
                        <td class="text-center">{$hotelRow.no_shows|intval}</td>
                        <td class="text-center">{$hotelRow.avg_los|string_format:'%.1f'}</td>
                        <td class="text-right">{displayPrice price=$hotelRow.outstanding_balance currency=$id_currency}</td>
                    </tr>
                    {/foreach}
                {else}
                    <tr>
                        <td class="list-empty" colspan="15">
                            <div class="list-empty-msg">
                                <i class="icon-warning-sign list-empty-icon"></i>
                                {l s='No hotels found.' mod='qlohotelreports'}
                            </div>
                        </td>
                    </tr>
                {/if}
            </tbody>
        </table>
    </div>
    </div>
    </div>

{* ═══════════════════════════════════════════════════════════════════════════
   OUT OF ORDER ROOMS
   ═══════════════════════════════════════════════════════════════════════════ *}
{elseif $active_report == 'out-of-order'}

    <div class="row">
    <div class="col-lg-12">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>{l s='Room No.' mod='qlohotelreports'}</th>
                    <th>{l s='Floor' mod='qlohotelreports'}</th>
                    <th>{l s='Room Type' mod='qlohotelreports'}</th>
                    <th class="text-center">{l s='OOO Status' mod='qlohotelreports'}</th>
                    <th>{l s='Reason' mod='qlohotelreports'}</th>
                    <th>{l s='Start Date' mod='qlohotelreports'}</th>
                    <th>{l s='Expected End Date' mod='qlohotelreports'}</th>
                    <th>{l s='Actual End Date' mod='qlohotelreports'}</th>
                    <th class="text-center">{l s='Duration (Days)' mod='qlohotelreports'}</th>
                    <th class="text-center">{l s='Current Status' mod='qlohotelreports'}</th>
                    <th>{l s='Marked By' mod='qlohotelreports'}</th>
                    <th>{l s='Resolved By' mod='qlohotelreports'}</th>
                    <th class="text-right">{l s='Est. Revenue Loss' mod='qlohotelreports'}</th>
                </tr>
            </thead>
            <tbody>
                {if $outOfOrder_rows}
                    {assign var="today" value=$smarty.now|date_format:'%Y-%m-%d'}
                    {foreach $outOfOrder_rows as $outOfOrderRow}
                    <tr>
                        <td>{$outOfOrderRow.room_num|escape:'html':'UTF-8'}</td>
                        <td>{if $outOfOrderRow.floor}{$outOfOrderRow.floor|escape:'html':'UTF-8'}{else}&mdash;{/if}</td>
                        <td>{$outOfOrderRow.room_type_name|escape:'html':'UTF-8'}</td>
                        <td class="text-center">
                            {if isset($room_statuses[$outOfOrderRow.id_status])}
                                <span class="label {$room_statuses[$outOfOrderRow.id_status].class|escape:'html':'UTF-8'}">{$room_statuses[$outOfOrderRow.id_status].label|escape:'html':'UTF-8'}</span>
                            {/if}
                        </td>
                        <td>{if $outOfOrderRow.reason}{$outOfOrderRow.reason|escape:'html':'UTF-8'}{else}&mdash;{/if}</td>
                        <td>{if $outOfOrderRow.disabled_from}{$outOfOrderRow.disabled_from|escape:'html':'UTF-8'}{else}&mdash;{/if}</td>
                        <td>{if $outOfOrderRow.disabled_to}{$outOfOrderRow.disabled_to|escape:'html':'UTF-8'}{else}&mdash;{/if}</td>
                        <td>&mdash;</td>
                        <td class="text-center">{if $outOfOrderRow.disabled_days}{$outOfOrderRow.disabled_days|intval}{else}&mdash;{/if}</td>
                        <td class="text-center">
                            {if !$outOfOrderRow.disabled_from}
                                <span class="label label-success">{l s='Active' mod='qlohotelreports'}</span>
                            {else}
                                <span class="label label-danger">{l s='Inactive' mod='qlohotelreports'}</span>
                            {/if}
                        </td>
                        <td>&mdash;</td>
                        <td>&mdash;</td>
                        <td class="text-right">&mdash;</td>
                    </tr>
                    {/foreach}
                {else}
                    <tr>
                        <td class="list-empty" colspan="13">
                            <div class="list-empty-msg">
                                <i class="icon-warning-sign list-empty-icon"></i>
                                {l s='No out of order rooms for the selected period.' mod='qlohotelreports'}
                            </div>
                        </td>
                    </tr>
                {/if}
            </tbody>
        </table>
    </div>
    </div>
    </div>

{/if}

<a class="btn btn-default export-csv" href="{$export_url|escape:'html':'UTF-8'}">
    <i class="icon-cloud-download"></i> {l s='CSV Export' mod='qlohotelreports'}
</a>
