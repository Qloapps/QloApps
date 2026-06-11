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
        {if $room_types}
        <div class="row">
            <label class="col-xs-3">{l s='Room Type' mod='qlohotelreports'}</label>
            <div class="col-xs-9">
                <select name="id_product" class="form-control">
                    <option value="0">{l s='All' mod='qlohotelreports'}</option>
                    {foreach $room_types as $roomType}
                    <option value="{$roomType.id_product|intval}"{if $filter_id_product == $roomType.id_product} selected="selected"{/if}>
                        {$roomType.room_type_name|escape:'html':'UTF-8'}
                    </option>
                    {/foreach}
                </select>
            </div>
        </div>
        {/if}
        {if $active_report == 'room-status' && $available_floors}
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
   OCCUPANCY
   ═══════════════════════════════════════════════════════════════════════════ *}
{if $active_report == 'occupancy'}

    {* Daily table *}
    <div class="row">
    <div class="col-lg-12">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>{l s='Date' mod='qlohotelreports'}</th>
                    <th class="text-center">{l s='Total Rooms (Inventory)' mod='qlohotelreports'}</th>
                    <th class="text-center">{l s='Available Rooms' mod='qlohotelreports'}</th>
                    <th class="text-center">{l s='Rooms Booked' mod='qlohotelreports'}</th>
                    <th class="text-center">{l s='Rooms Occupied' mod='qlohotelreports'}</th>
                    <th class="text-center">{l s='Out of Order' mod='qlohotelreports'}</th>
                    <th class="text-center">{l s='Complimentary' mod='qlohotelreports'}</th>
                    <th class="text-center">{l s='Occupancy Rate %' mod='qlohotelreports'}</th>
                    <th class="text-right">{l s='ADR (Avg. Daily Rate)' mod='qlohotelreports'}</th>
                    <th class="text-right">{l s='RevPAR (Rev. Per Avail. Room)' mod='qlohotelreports'}</th>
                    <th class="text-right">{l s='Total Room Revenue' mod='qlohotelreports'}</th>
                </tr>
            </thead>
            <tbody>
                {if $daily_rows}
                    {foreach $daily_rows as $occupancyRow}
                    <tr>
                        <td>{$occupancyRow.date|escape:'html':'UTF-8'}</td>
                        <td class="text-center">{$occupancyRow.total|intval}</td>
                        <td class="text-center">{$occupancyRow.available|intval}</td>
                        <td class="text-center">{$occupancyRow.rooms_booked|intval}</td>
                        <td class="text-center">{$occupancyRow.rooms_occupied|intval}</td>
                        <td class="text-center">{$occupancyRow.out_of_order|intval}</td>
                        <td class="text-center">&mdash;</td>
                        <td class="text-center">{$occupancyRow.occupancy_pct|string_format:'%.1f'}%</td>
                        <td class="text-right">{$currency_sign|escape:'html':'UTF-8'}{$occupancyRow.adr|string_format:'%.2f'}</td>
                        <td class="text-right">{$currency_sign|escape:'html':'UTF-8'}{$occupancyRow.revpar|string_format:'%.2f'}</td>
                        <td class="text-right">{$currency_sign|escape:'html':'UTF-8'}{$occupancyRow.total_room_revenue|string_format:'%.2f'}</td>
                    </tr>
                    {/foreach}
                {else}
                    <tr>
                        <td class="list-empty" colspan="11">
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
   AVAILABILITY
   ═══════════════════════════════════════════════════════════════════════════ *}
{elseif $active_report == 'availability'}

    <div class="row">
    <div class="col-lg-12">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>{l s='Date' mod='qlohotelreports'}</th>
                    <th>{l s='Room Type' mod='qlohotelreports'}</th>
                    <th class="text-center">{l s='Total Rooms (Inventory)' mod='qlohotelreports'}</th>
                    <th class="text-center">{l s='Rooms Booked' mod='qlohotelreports'}</th>
                    <th class="text-center">{l s='Out of Order / Maintenance' mod='qlohotelreports'}</th>
                    <th class="text-center">{l s='Rooms Available' mod='qlohotelreports'}</th>
                    <th class="text-right">{l s='Rate' mod='qlohotelreports'}</th>
                    <th class="text-center">{l s='Occupancy %' mod='qlohotelreports'}</th>
                </tr>
            </thead>
            <tbody>
                {if $availability_rows}
                    {assign var="prev_date" value=''}
                    {assign var="date_total" value=0}
                    {assign var="date_booked" value=0}
                    {assign var="date_ooo" value=0}
                    {assign var="date_avail" value=0}
                    {foreach $availability_rows as $availabilityRow}
                        {if $prev_date != '' && $prev_date != $availabilityRow.date}
                        <tr class="qlo-subtotal-row">
                            <td><strong>{$prev_date|escape:'html':'UTF-8'} {l s='Total' mod='qlohotelreports'}</strong></td>
                            <td class="text-center">&mdash;</td>
                            <td class="text-center"><strong>{$date_total|intval}</strong></td>
                            <td class="text-center"><strong>{$date_booked|intval}</strong></td>
                            <td class="text-center"><strong>{$date_ooo|intval}</strong></td>
                            <td class="text-center"><strong>{$date_avail|intval}</strong></td>
                            <td class="text-center">&mdash;</td>
                            <td class="text-center">&mdash;</td>
                        </tr>
                        {assign var="date_total" value=0}
                        {assign var="date_booked" value=0}
                        {assign var="date_ooo" value=0}
                        {assign var="date_avail" value=0}
                        {/if}
                        {assign var="prev_date" value=$availabilityRow.date}
                        {assign var="date_total" value=$date_total+$availabilityRow.total_rooms}
                        {assign var="date_booked" value=$date_booked+$availabilityRow.rooms_booked}
                        {assign var="date_ooo" value=$date_ooo+$availabilityRow.out_of_order}
                        {assign var="date_avail" value=$date_avail+$availabilityRow.available}
                    <tr>
                        <td>{$availabilityRow.date|escape:'html':'UTF-8'}</td>
                        <td>{$availabilityRow.room_type_name|escape:'html':'UTF-8'}</td>
                        <td class="text-center">{$availabilityRow.total_rooms|intval}</td>
                        <td class="text-center">{$availabilityRow.rooms_booked|intval}</td>
                        <td class="text-center">{$availabilityRow.out_of_order|intval}</td>
                        <td class="text-center">{$availabilityRow.available|intval}</td>
                        <td class="text-center">&mdash;</td>
                        <td class="text-center">{$availabilityRow.occupancy_pct|string_format:'%.1f'}%</td>
                    </tr>
                    {/foreach}
                    {if $prev_date != ''}
                    <tr class="qlo-subtotal-row">
                        <td><strong>{$prev_date|escape:'html':'UTF-8'} {l s='Total' mod='qlohotelreports'}</strong></td>
                        <td>&mdash;</td>
                        <td class="text-center"><strong>{$date_total|intval}</strong></td>
                        <td class="text-center"><strong>{$date_booked|intval}</strong></td>
                        <td class="text-center"><strong>{$date_ooo|intval}</strong></td>
                        <td class="text-center"><strong>{$date_avail|intval}</strong></td>
                        <td>&mdash;</td>
                        <td>&mdash;</td>
                    </tr>
                    {/if}
                {else}
                    <tr>
                        <td class="list-empty" colspan="8">
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
   ROOM STATUS
   ═══════════════════════════════════════════════════════════════════════════ *}
{elseif $active_report == 'room-status'}

    <div class="alert alert-info">
        {l s='Showing room status as of the filter start date.' mod='qlohotelreports'}
    </div>

    {if $rooms}
    {assign var="cnt_total" value=0}
    {assign var="cnt_available" value=0}
    {assign var="cnt_occupied" value=0}
    {assign var="cnt_ooo" value=0}
    {foreach $rooms as $room}
        {assign var="cnt_total" value=$cnt_total+1}
        {if $room.id_order}
            {assign var="cnt_occupied" value=$cnt_occupied+1}
        {elseif isset($room_statuses[$room.id_status])}
            {assign var="cnt_ooo" value=$cnt_ooo+1}
        {else}
            {assign var="cnt_available" value=$cnt_available+1}
        {/if}
    {/foreach}
    <div class="row qlo-kpi-row">
        <div class="col-md-3 col-sm-6">
            <div class="qlo-kpi-card">
                <div class="qlo-kpi-label">{l s='Total Rooms' mod='qlohotelreports'}</div>
                <div class="qlo-kpi-value">{$cnt_total|intval}</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="qlo-kpi-card qlo-kpi-card--success">
                <div class="qlo-kpi-label">{l s='Available' mod='qlohotelreports'}</div>
                <div class="qlo-kpi-value">{$cnt_available|intval}</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="qlo-kpi-card qlo-kpi-card--danger">
                <div class="qlo-kpi-label">{l s='Occupied' mod='qlohotelreports'}</div>
                <div class="qlo-kpi-value">{$cnt_occupied|intval}</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="qlo-kpi-card qlo-kpi-card--warning">
                <div class="qlo-kpi-label">{l s='Out of Order' mod='qlohotelreports'}</div>
                <div class="qlo-kpi-value">{$cnt_ooo|intval}</div>
            </div>
        </div>
    </div>
    {/if}

    <div class="row">
    <div class="col-lg-12">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>{l s='Room No.' mod='qlohotelreports'}</th>
                    <th>{l s='Room Type' mod='qlohotelreports'}</th>
                    <th>{l s='Floor' mod='qlohotelreports'}</th>
                    <th class="text-center">{l s='Status' mod='qlohotelreports'}</th>
                    <th class="text-center">{l s='Housekeeping Status' mod='qlohotelreports'}</th>
                    <th>{l s='Current Guest' mod='qlohotelreports'}</th>
                    <th>{l s='Check-out Date' mod='qlohotelreports'}</th>
                </tr>
            </thead>
            <tbody>
                {if $rooms}
                    {foreach $rooms as $room}
                    <tr>
                        <td>{$room.room_num|escape:'html':'UTF-8'}</td>
                        <td>{$room.room_type_name|escape:'html':'UTF-8'}</td>
                        <td>{if $room.floor}{$room.floor|escape:'html':'UTF-8'}{else}&mdash;{/if}</td>
                        <td class="text-center">
                            {if $room.id_order}
                                <span class="label label-danger">{l s='Occupied' mod='qlohotelreports'}</span>
                            {elseif isset($room_statuses[$room.id_status])}
                                <span class="label {$room_statuses[$room.id_status].class|escape:'html':'UTF-8'}">{$room_statuses[$room.id_status].label|escape:'html':'UTF-8'}</span>
                            {else}
                                <span class="label label-success">{l s='Vacant' mod='qlohotelreports'}</span>
                            {/if}
                        </td>
                        <td class="text-center">&mdash;</td>
                        <td>{if $room.guest_name}{$room.guest_name|escape:'html':'UTF-8'}{else}&mdash;{/if}</td>
                        <td>{if $room.date_to}{$room.date_to|escape:'html':'UTF-8'}{else}&mdash;{/if}</td>
                    </tr>
                    {/foreach}
                {else}
                    <tr>
                        <td class="list-empty" colspan="7">
                            <div class="list-empty-msg">
                                <i class="icon-warning-sign list-empty-icon"></i>
                                {l s='No rooms found.' mod='qlohotelreports'}
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
   ROOM TYPE PERFORMANCE
   ═══════════════════════════════════════════════════════════════════════════ *}
{elseif $active_report == 'room-perf'}

    <div class="row">
    <div class="col-lg-12">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>{l s='Room Type' mod='qlohotelreports'}</th>
                    <th>{l s='Hotel' mod='qlohotelreports'}</th>
                    <th class="text-center">{l s='Total Rooms' mod='qlohotelreports'}</th>
                    <th class="text-center">{l s='Total Room Nights Available' mod='qlohotelreports'}</th>
                    <th class="text-center">{l s='Total Room Nights Sold' mod='qlohotelreports'}</th>
                    <th class="text-center">{l s='Occupancy Rate %' mod='qlohotelreports'}</th>
                    <th class="text-right">{l s='Total Price (excl. Tax)' mod='qlohotelreports'}</th>
                    <th class="text-right">{l s='Tax Amount' mod='qlohotelreports'}</th>
                    <th class="text-right">{l s='Total Revenue' mod='qlohotelreports'}</th>
                    <th class="text-right">{l s='ADR (Avg. Daily Rate)' mod='qlohotelreports'}</th>
                    <th class="text-right">{l s='RevPAR (Rev. Per Avail. Room)' mod='qlohotelreports'}</th>
                    <th class="text-center">{l s='Cancellation Count' mod='qlohotelreports'}</th>
                    <th class="text-center">{l s='No-Show Count' mod='qlohotelreports'}</th>
                    <th class="text-center">{l s='Avg. Length of Stay' mod='qlohotelreports'}</th>
                </tr>
            </thead>
            <tbody>
                {if $roomTypePerformance_rows}
                    {foreach $roomTypePerformance_rows as $perfRow}
                    <tr>
                        <td>{$perfRow.room_type_name|escape:'html':'UTF-8'}</td>
                        <td>{$perfRow.hotel_name|escape:'html':'UTF-8'}</td>
                        <td class="text-center">{$perfRow.total_rooms|intval}</td>
                        <td class="text-center">{$perfRow.total_nights_available|intval}</td>
                        <td class="text-center">{$perfRow.room_nights|intval}</td>
                        <td class="text-center">{$perfRow.occupancy_pct|string_format:'%.1f'}%</td>
                        <td class="text-right">{$currency_sign|escape:'html':'UTF-8'}{$perfRow.room_revenue|string_format:'%.2f'}</td>
                        <td class="text-right">{$currency_sign|escape:'html':'UTF-8'}{$perfRow.tax_amount|string_format:'%.2f'}</td>
                        <td class="text-right">{$currency_sign|escape:'html':'UTF-8'}{$perfRow.total_revenue|string_format:'%.2f'}</td>
                        <td class="text-right">{$currency_sign|escape:'html':'UTF-8'}{$perfRow.adr|string_format:'%.2f'}</td>
                        <td class="text-right">{$currency_sign|escape:'html':'UTF-8'}{$perfRow.revpar|string_format:'%.2f'}</td>
                        <td class="text-center">{$perfRow.cancel_count|intval}</td>
                        <td class="text-center">{$perfRow.no_show_count|intval}</td>
                        <td class="text-center">{$perfRow.avg_los|string_format:'%.1f'}</td>
                    </tr>
                    {/foreach}
                {else}
                    <tr>
                        <td class="list-empty" colspan="14">
                            <div class="list-empty-msg">
                                <i class="icon-warning-sign list-empty-icon"></i>
                                {l s='No data for the selected period.' mod='qlohotelreports'}
                            </div>
                        </td>
                    </tr>
                {/if}
            </tbody>
            {if $roomTypePerformance_rows}
            <tfoot class="qlo-report-totals">
                {assign var="tot_rooms" value=0}
                {assign var="tot_nights_avail" value=0}
                {assign var="tot_nights_sold" value=0}
                {assign var="tot_price_excl" value=0}
                {assign var="tot_tax" value=0}
                {assign var="tot_total_rev" value=0}
                {assign var="tot_cancels" value=0}
                {assign var="tot_noshows" value=0}
                {foreach $roomTypePerformance_rows as $perfRow}
                    {assign var="tot_rooms" value=$tot_rooms+$perfRow.total_rooms}
                    {assign var="tot_nights_avail" value=$tot_nights_avail+$perfRow.total_nights_available}
                    {assign var="tot_nights_sold" value=$tot_nights_sold+$perfRow.room_nights}
                    {assign var="tot_price_excl" value=$tot_price_excl+$perfRow.room_revenue}
                    {assign var="tot_tax" value=$tot_tax+$perfRow.tax_amount}
                    {assign var="tot_total_rev" value=$tot_total_rev+$perfRow.total_revenue}
                    {assign var="tot_cancels" value=$tot_cancels+$perfRow.cancel_count}
                    {assign var="tot_noshows" value=$tot_noshows+$perfRow.no_show_count}
                {/foreach}
                <tr>
                    <td colspan="2"><strong>{l s='Total' mod='qlohotelreports'}</strong></td>
                    <td class="text-center"><strong>{$tot_rooms|intval}</strong></td>
                    <td class="text-center"><strong>{$tot_nights_avail|intval}</strong></td>
                    <td class="text-center"><strong>{$tot_nights_sold|intval}</strong></td>
                    <td class="text-center">&mdash;</td>
                    <td class="text-right"><strong>{$currency_sign|escape:'html':'UTF-8'}{$tot_price_excl|string_format:'%.2f'}</strong></td>
                    <td class="text-right"><strong>{$currency_sign|escape:'html':'UTF-8'}{$tot_tax|string_format:'%.2f'}</strong></td>
                    <td class="text-right"><strong>{$currency_sign|escape:'html':'UTF-8'}{$tot_total_rev|string_format:'%.2f'}</strong></td>
                    <td class="text-right">&mdash;</td>
                    <td class="text-right">&mdash;</td>
                    <td class="text-center"><strong>{$tot_cancels|intval}</strong></td>
                    <td class="text-center"><strong>{$tot_noshows|intval}</strong></td>
                    <td class="text-center">&mdash;</td>
                </tr>
            </tfoot>
            {/if}
        </table>
    </div>
    </div>
    </div>

{/if}

<a class="btn btn-default export-csv" href="{$export_url|escape:'html':'UTF-8'}">
    <i class="icon-cloud-download"></i> {l s='CSV Export' mod='qlohotelreports'}
</a>
