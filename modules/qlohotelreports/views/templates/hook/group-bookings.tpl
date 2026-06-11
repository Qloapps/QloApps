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

<div class="qlo-report-group-bookings">

    <h3 class="qlo-report-heading">{$report_label|escape:'html':'UTF-8'}</h3>

    {* ── Filter bar ──────────────────────────────────────────────────── *}
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
            <div class="row">
                <label class="col-xs-3">{l s='Room Type' mod='qlohotelreports'}</label>
                <div class="col-xs-9">
                    <select name="id_product" class="form-control">
                        <option value="0">{l s='All' mod='qlohotelreports'}</option>
                        {foreach $room_types as $roomType}
                        <option value="{$roomType.id_product|intval}"{if $filter_id_product == $roomType.id_product}selected="selected"{/if}>
                            {$roomType.room_type_name|escape:'html':'UTF-8'}
                        </option>
                        {/foreach}
                    </select>
                </div>
            </div>
            {if $active_report == 'reservation'}
            <div class="row">
                <label class="col-xs-3">{l s='Status' mod='qlohotelreports'}</label>
                <div class="col-xs-9">
                    <select name="booking_status" class="form-control">
                        <option value="0">{l s='All' mod='qlohotelreports'}</option>
                        {foreach $booking_statuses as $statusId => $statusLabel}
                        <option value="{$statusId|intval}"{if $filter_booking_status == $statusId}selected="selected"{/if}>{$statusLabel}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
            <div class="row">
                <label class="col-xs-3">{l s='Source' mod='qlohotelreports'}</label>
                <div class="col-xs-9">
                    <select name="booking_type" class="form-control">
                        <option value="0">{l s='All' mod='qlohotelreports'}</option>
                        {foreach $booking_sources as $sourceId => $sourceLabel}
                        <option value="{$sourceId|intval}"{if $filter_booking_type == $sourceId}selected="selected"{/if}>{$sourceLabel}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
            {/if}
            <div class="actions">
                <hr>
                <span class="pull-right">
                    <button type="submit" class="btn btn-default btn-sm">
                        <i class="icon-filter"></i> {l s='Apply' mod='qlohotelreports'}
                    </button>
                </span>
            </div>
        </div>
    </form>

    {* ── Reservation Report ──────────────────────────────────────────── *}
    {if $active_report == 'reservation'}
        <div class="row">
        <div class="col-lg-12">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>{l s='Reservation ID' mod='qlohotelreports'}</th>
                        <th>{l s='Guest Name' mod='qlohotelreports'}</th>
                        <th>{l s='Guest Contact' mod='qlohotelreports'}</th>
                        <th>{l s='Room Type' mod='qlohotelreports'}</th>
                        <th>{l s='Room No.' mod='qlohotelreports'}</th>
                        <th>{l s='Check-in Date' mod='qlohotelreports'}</th>
                        <th>{l s='Check-out Date' mod='qlohotelreports'}</th>
                        <th class="text-right">{l s='Nights' mod='qlohotelreports'}</th>
                        <th class="text-right">{l s='Adults' mod='qlohotelreports'}</th>
                        <th class="text-right">{l s='Children' mod='qlohotelreports'}</th>
                        <th class="text-right">{l s='Rate Per Night' mod='qlohotelreports'}</th>
                        <th>{l s='Booking Source' mod='qlohotelreports'}</th>
                        <th>{l s='Booking Status' mod='qlohotelreports'}</th>
                        <th class="text-right">{l s='Total (excl. Tax)' mod='qlohotelreports'}</th>
                        <th class="text-right">{l s='Tax Amount' mod='qlohotelreports'}</th>
                        <th class="text-right">{l s='Grand Total' mod='qlohotelreports'}</th>
                        <th class="text-right">{l s='Balance Due' mod='qlohotelreports'}</th>
                        <th>{l s='Payment Status' mod='qlohotelreports'}</th>
                        <th>{l s='Created By' mod='qlohotelreports'}</th>
                        <th>{l s='Booking Date' mod='qlohotelreports'}</th>
                    </tr>
                </thead>
                <tbody>
                    {if $reservations}
                        {foreach $reservations as $reservation}
                            <tr>
                                <td>{$reservation.id_order|intval}</td>
                                <td>{$reservation.customer_name|escape:'html':'UTF-8'}</td>
                                <td>{if $reservation.phone}{$reservation.phone|escape:'html':'UTF-8'}{else}<span class="text-muted">—</span>{/if}</td>
                                <td>{$reservation.room_type_name|escape:'html':'UTF-8'}</td>
                                <td>{$reservation.room_num|escape:'html':'UTF-8'}</td>
                                <td>{$reservation.hotel_check_in|escape:'html':'UTF-8'}</td>
                                <td>{$reservation.hotel_check_out|escape:'html':'UTF-8'}</td>
                                <td class="text-right">{$reservation.nights|intval}</td>
                                <td class="text-right">{$reservation.adults|intval}</td>
                                <td class="text-right">{$reservation.children|intval}</td>
                                <td class="text-right">{$reservation.currency_sign|escape:'html':'UTF-8'}{$reservation.unit_price_tax_excl|string_format:"%.2f"}</td>
                                <td>
                                    {if isset($booking_sources[$reservation.booking_type])}{$booking_sources[$reservation.booking_type]|escape:'html':'UTF-8'}{else}{l s='Other' mod='qlohotelreports'}{/if}
                                </td>
                                <td>
                                    {if isset($booking_statuses[$reservation.id_status])}{$booking_statuses[$reservation.id_status]|escape:'html':'UTF-8'}{else}{$reservation.id_status|intval}{/if}
                                </td>
                                <td class="text-right">{$reservation.currency_sign|escape:'html':'UTF-8'}{$reservation.total_price_tax_excl|string_format:"%.2f"}</td>
                                <td class="text-right">{$reservation.currency_sign|escape:'html':'UTF-8'}{$reservation.tax_amount|string_format:"%.2f"}</td>
                                <td class="text-right">{$reservation.currency_sign|escape:'html':'UTF-8'}{$reservation.total_price_tax_incl|string_format:"%.2f"}</td>
                                <td class="text-right">{$reservation.currency_sign|escape:'html':'UTF-8'}{if $reservation.balance_due > 0}{$reservation.balance_due|string_format:"%.2f"}{else}0.00{/if}</td>
                                <td>
                                    {if $reservation.order_paid <= 0}{l s='Pending' mod='qlohotelreports'}
                                    {elseif $reservation.balance_due > 0}{l s='Partial' mod='qlohotelreports'}
                                    {else}{l s='Paid' mod='qlohotelreports'}{/if}
                                </td>
                                <td>{if $reservation.created_by}{$reservation.created_by|escape:'html':'UTF-8'}{else}<span class="text-muted">—</span>{/if}</td>
                                <td>{$reservation.date_add|escape:'html':'UTF-8'}</td>
                            </tr>
                        {/foreach}
                    {else}
                        <tr>
                            <td class="list-empty" colspan="20">
                                <div class="list-empty-msg">
                                    <i class="icon-warning-sign list-empty-icon"></i>
                                    {l s='No reservations found for the selected date range and filters.' mod='qlohotelreports'}
                                </div>
                            </td>
                        </tr>
                    {/if}
                </tbody>
                {if $reservations}
                <tfoot>
                    <tr class="qlo-report-totals">
                        <td colspan="7"><strong>{l s='Totals' mod='qlohotelreports'}</strong></td>
                        <td class="text-right"><strong>{$reservation_totals.nights}</strong></td>
                        <td class="text-right"><strong>{$reservation_totals.adults}</strong></td>
                        <td class="text-right"><strong>{$reservation_totals.children}</strong></td>
                        <td></td>
                        <td colspan="2"></td>
                        <td class="text-right"><strong>{$currency_sign|escape:'html':'UTF-8'}{$reservation_totals.total_price_tax_excl|string_format:"%.2f"}</strong></td>
                        <td class="text-right"><strong>{$currency_sign|escape:'html':'UTF-8'}{$reservation_totals.tax_amount|string_format:"%.2f"}</strong></td>
                        <td class="text-right"><strong>{$currency_sign|escape:'html':'UTF-8'}{$reservation_totals.total_price_tax_incl|string_format:"%.2f"}</strong></td>
                        <td class="text-right"><strong>{$currency_sign|escape:'html':'UTF-8'}{$reservation_totals.balance_due|string_format:"%.2f"}</strong></td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
                {/if}
            </table>
        </div>
        </div>
        </div>
    {/if}

    {* ── Cancellation Report ─────────────────────────────────────────── *}
    {if $active_report == 'cancellation'}
        <div class="row">
        <div class="col-lg-12">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>{l s='Booking ID' mod='qlohotelreports'}</th>
                        <th>{l s='Guest Name' mod='qlohotelreports'}</th>
                        <th>{l s='Room Type' mod='qlohotelreports'}</th>
                        <th>{l s='Room No.' mod='qlohotelreports'}</th>
                        <th>{l s='Check-in Date' mod='qlohotelreports'}</th>
                        <th>{l s='Cancellation Date' mod='qlohotelreports'}</th>
                        <th>{l s='Cancellation Reason' mod='qlohotelreports'}</th>
                        <th>{l s='Cancellation Remark' mod='qlohotelreports'}</th>
                        <th class="text-right">{l s='Refund Amount' mod='qlohotelreports'}</th>
                        <th>{l s='Refund Status' mod='qlohotelreports'}</th>
                        <th>{l s='Booking Date' mod='qlohotelreports'}</th>
                    </tr>
                </thead>
                <tbody>
                    {if $cancellations}
                        {foreach $cancellations as $cancellation}
                            <tr>
                                <td>{$cancellation.id_order|intval}</td>
                                <td>{$cancellation.customer_name|escape:'html':'UTF-8'}</td>
                                <td>{$cancellation.room_type_name|escape:'html':'UTF-8'}</td>
                                <td>{$cancellation.room_num|escape:'html':'UTF-8'}</td>
                                <td>{$cancellation.hotel_check_in|escape:'html':'UTF-8'}</td>
                                <td>{if $cancellation.cancellation_date}{$cancellation.cancellation_date|date_format:'%d-%m-%Y'}{else}<span class="text-muted">—</span>{/if}</td>
                                <td>{if $cancellation.cancellation_reason}{$cancellation.cancellation_reason|escape:'html':'UTF-8'}{else}<span class="text-muted">—</span>{/if}</td>
                                <td><span class="text-muted">—</span></td>
                                <td class="text-right">{$cancellation.currency_sign|escape:'html':'UTF-8'}{$cancellation.refunded_amount|string_format:"%.2f"}</td>
                                <td>{if $cancellation.refund_status}{$cancellation.refund_status|escape:'html':'UTF-8'}{else}<span class="text-muted">—</span>{/if}</td>
                                <td>{if $cancellation.booking_date}{$cancellation.booking_date|escape:'html':'UTF-8'}{else}<span class="text-muted">—</span>{/if}</td>
                            </tr>
                        {/foreach}
                    {else}
                        <tr>
                            <td class="list-empty" colspan="11">
                                <div class="list-empty-msg">
                                    <i class="icon-warning-sign list-empty-icon"></i>
                                    {l s='No cancellations found for the selected date range.' mod='qlohotelreports'}
                                </div>
                            </td>
                        </tr>
                    {/if}
                </tbody>
                {if $cancellations}
                <tfoot>
                    <tr class="qlo-report-totals">
                        <td colspan="8"><strong>{l s='Totals' mod='qlohotelreports'}</strong></td>
                        <td class="text-right"><strong>{$total_refunded|string_format:"%.2f"}</strong></td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
                {/if}
            </table>
        </div>
        </div>
        </div>
    {/if}

    {* ── No-Show Report ──────────────────────────────────────────────── *}
    {if $active_report == 'no-show'}
        <div class="row">
        <div class="col-lg-12">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>{l s='Booking ID' mod='qlohotelreports'}</th>
                        <th>{l s='Guest Name' mod='qlohotelreports'}</th>
                        <th>{l s='Room Type' mod='qlohotelreports'}</th>
                        <th>{l s='Room No.' mod='qlohotelreports'}</th>
                        <th>{l s='Check-in Date' mod='qlohotelreports'}</th>
                        <th class="text-right">{l s='Total Amount' mod='qlohotelreports'}</th>
                        <th class="text-right">{l s='Penalty Charged' mod='qlohotelreports'}</th>
                    </tr>
                </thead>
                <tbody>
                    {if $no_shows}
                        {foreach $no_shows as $noShow}
                            <tr>
                                <td>{$noShow.id_order|intval}</td>
                                <td>{$noShow.customer_name|escape:'html':'UTF-8'}</td>
                                <td>{$noShow.room_type_name|escape:'html':'UTF-8'}</td>
                                <td>{$noShow.room_num|escape:'html':'UTF-8'}</td>
                                <td>{$noShow.actual_checkin|escape:'html':'UTF-8'}</td>
                                <td class="text-right">{$noShow.currency_sign|escape:'html':'UTF-8'}{$noShow.total_price_tax_incl|string_format:"%.2f"}</td>
                                <td class="text-right"><span class="text-muted">—</span></td>
                            </tr>
                        {/foreach}
                    {else}
                        <tr>
                            <td class="list-empty" colspan="7">
                                <div class="list-empty-msg">
                                    <i class="icon-warning-sign list-empty-icon"></i>
                                    {l s='No no-shows found for the selected date range.' mod='qlohotelreports'}
                                </div>
                            </td>
                        </tr>
                    {/if}
                </tbody>
                {if $no_shows}
                <tfoot>
                    <tr class="qlo-report-totals">
                        <td colspan="5"><strong>{l s='Totals' mod='qlohotelreports'}</strong></td>
                        <td class="text-right"><strong>{$currency_sign|escape:'html':'UTF-8'}{$no_show_totals.total_price_tax_incl|string_format:"%.2f"}</strong></td>
                        <td></td>
                    </tr>
                </tfoot>
                {/if}
            </table>
        </div>
        </div>
        </div>
    {/if}

    {* ── Arrivals Report ─────────────────────────────────────────────── *}
    {if $active_report == 'arrivals'}
        <div class="row">
        <div class="col-lg-12">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>{l s='Order ID' mod='qlohotelreports'}</th>
                        <th>{l s='Guest' mod='qlohotelreports'}</th>
                        <th>{l s='Hotel' mod='qlohotelreports'}</th>
                        <th>{l s='Room Type' mod='qlohotelreports'}</th>
                        <th>{l s='Room No.' mod='qlohotelreports'}</th>
                        <th>{l s='Check-in' mod='qlohotelreports'}</th>
                        <th>{l s='Check-out' mod='qlohotelreports'}</th>
                        <th class="text-right">{l s='Nights' mod='qlohotelreports'}</th>
                        <th class="text-right">{l s='Adults' mod='qlohotelreports'}</th>
                        <th class="text-right">{l s='Children' mod='qlohotelreports'}</th>
                        <th class="text-right">{l s='Total (incl. Tax)' mod='qlohotelreports'}</th>
                    </tr>
                </thead>
                <tbody>
                    {if $arrivals}
                        {foreach $arrivals as $arrival}
                            <tr>
                                <td>{$arrival.id_order|intval}</td>
                                <td>{$arrival.customer_name|escape:'html':'UTF-8'}</td>
                                <td>{$arrival.hotel_name|escape:'html':'UTF-8'}</td>
                                <td>{$arrival.room_type_name|escape:'html':'UTF-8'}</td>
                                <td>{$arrival.room_num|escape:'html':'UTF-8'}</td>
                                <td>{$arrival.actual_checkin|escape:'html':'UTF-8'}</td>
                                <td>{$arrival.actual_checkout|escape:'html':'UTF-8'}</td>
                                <td class="text-right">{$arrival.los|intval}</td>
                                <td class="text-right">{$arrival.adults|intval}</td>
                                <td class="text-right">{$arrival.children|intval}</td>
                                <td class="text-right">{$arrival.currency_sign|escape:'html':'UTF-8'}{$arrival.total_price_tax_incl|string_format:"%.2f"}</td>
                            </tr>
                        {/foreach}
                    {else}
                        <tr>
                            <td class="list-empty" colspan="11">
                                <div class="list-empty-msg">
                                    <i class="icon-warning-sign list-empty-icon"></i>
                                    {l s='No arrivals found for the selected date range.' mod='qlohotelreports'}
                                </div>
                            </td>
                        </tr>
                    {/if}
                </tbody>
                {if $arrivals}
                <tfoot>
                    <tr class="qlo-report-totals">
                        <td colspan="7"><strong>{l s='Totals' mod='qlohotelreports'}</strong></td>
                        <td class="text-right"><strong>{$arrival_totals.los}</strong></td>
                        <td class="text-right"><strong>{$arrival_totals.adults}</strong></td>
                        <td class="text-right"><strong>{$arrival_totals.children}</strong></td>
                        <td class="text-right"><strong>{$currency_sign|escape:'html':'UTF-8'}{$arrival_totals.total_price_tax_incl|string_format:"%.2f"}</strong></td>
                    </tr>
                </tfoot>
                {/if}
            </table>
        </div>
        </div>
        </div>
    {/if}

    {* ── Departures Report ───────────────────────────────────────────── *}
    {if $active_report == 'departures'}
        <div class="row">
        <div class="col-lg-12">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>{l s='Order ID' mod='qlohotelreports'}</th>
                        <th>{l s='Guest' mod='qlohotelreports'}</th>
                        <th>{l s='Hotel' mod='qlohotelreports'}</th>
                        <th>{l s='Room Type' mod='qlohotelreports'}</th>
                        <th>{l s='Room No.' mod='qlohotelreports'}</th>
                        <th>{l s='Check-in' mod='qlohotelreports'}</th>
                        <th>{l s='Check-out' mod='qlohotelreports'}</th>
                        <th class="text-right">{l s='Nights' mod='qlohotelreports'}</th>
                        <th class="text-right">{l s='Adults' mod='qlohotelreports'}</th>
                        <th class="text-right">{l s='Children' mod='qlohotelreports'}</th>
                        <th class="text-right">{l s='Total (incl. Tax)' mod='qlohotelreports'}</th>
                    </tr>
                </thead>
                <tbody>
                    {if $departures}
                        {foreach $departures as $departure}
                            <tr>
                                <td>{$departure.id_order|intval}</td>
                                <td>{$departure.customer_name|escape:'html':'UTF-8'}</td>
                                <td>{$departure.hotel_name|escape:'html':'UTF-8'}</td>
                                <td>{$departure.room_type_name|escape:'html':'UTF-8'}</td>
                                <td>{$departure.room_num|escape:'html':'UTF-8'}</td>
                                <td>{$departure.actual_checkin|escape:'html':'UTF-8'}</td>
                                <td>{$departure.actual_checkout|escape:'html':'UTF-8'}</td>
                                <td class="text-right">{$departure.los|intval}</td>
                                <td class="text-right">{$departure.adults|intval}</td>
                                <td class="text-right">{$departure.children|intval}</td>
                                <td class="text-right">{$departure.currency_sign|escape:'html':'UTF-8'}{$departure.total_price_tax_incl|string_format:"%.2f"}</td>
                            </tr>
                        {/foreach}
                    {else}
                        <tr>
                            <td class="list-empty" colspan="11">
                                <div class="list-empty-msg">
                                    <i class="icon-warning-sign list-empty-icon"></i>
                                    {l s='No departures found for the selected date range.' mod='qlohotelreports'}
                                </div>
                            </td>
                        </tr>
                    {/if}
                </tbody>
                {if $departures}
                <tfoot>
                    <tr class="qlo-report-totals">
                        <td colspan="7"><strong>{l s='Totals' mod='qlohotelreports'}</strong></td>
                        <td class="text-right"><strong>{$departure_totals.los}</strong></td>
                        <td class="text-right"><strong>{$departure_totals.adults}</strong></td>
                        <td class="text-right"><strong>{$departure_totals.children}</strong></td>
                        <td class="text-right"><strong>{$currency_sign|escape:'html':'UTF-8'}{$departure_totals.total_price_tax_incl|string_format:"%.2f"}</strong></td>
                    </tr>
                </tfoot>
                {/if}
            </table>
        </div>
        </div>
        </div>
    {/if}

    {* ── In-House Report ─────────────────────────────────────────────── *}
    {if $active_report == 'in-house'}
        <div class="alert alert-info">
            {l s='Showing guests who were in-house at any point during the selected date range.' mod='qlohotelreports'}
        </div>
        <div class="row">
        <div class="col-lg-12">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>{l s='Order ID' mod='qlohotelreports'}</th>
                        <th>{l s='Guest' mod='qlohotelreports'}</th>
                        <th>{l s='Hotel' mod='qlohotelreports'}</th>
                        <th>{l s='Room Type' mod='qlohotelreports'}</th>
                        <th>{l s='Room No.' mod='qlohotelreports'}</th>
                        <th>{l s='Check-in' mod='qlohotelreports'}</th>
                        <th>{l s='Check-out' mod='qlohotelreports'}</th>
                        <th class="text-right">{l s='Nights' mod='qlohotelreports'}</th>
                        <th class="text-right">{l s='Adults' mod='qlohotelreports'}</th>
                        <th class="text-right">{l s='Children' mod='qlohotelreports'}</th>
                        <th class="text-right">{l s='Total (incl. Tax)' mod='qlohotelreports'}</th>
                    </tr>
                </thead>
                <tbody>
                    {if $in_house}
                        {foreach $in_house as $inHouseGuest}
                            <tr>
                                <td>{$inHouseGuest.id_order|intval}</td>
                                <td>{$inHouseGuest.customer_name|escape:'html':'UTF-8'}</td>
                                <td>{$inHouseGuest.hotel_name|escape:'html':'UTF-8'}</td>
                                <td>{$inHouseGuest.room_type_name|escape:'html':'UTF-8'}</td>
                                <td>{$inHouseGuest.room_num|escape:'html':'UTF-8'}</td>
                                <td>{$inHouseGuest.actual_checkin|escape:'html':'UTF-8'}</td>
                                <td>{$inHouseGuest.actual_checkout|escape:'html':'UTF-8'}</td>
                                <td class="text-right">{$inHouseGuest.los|intval}</td>
                                <td class="text-right">{$inHouseGuest.adults|intval}</td>
                                <td class="text-right">{$inHouseGuest.children|intval}</td>
                                <td class="text-right">{$inHouseGuest.currency_sign|escape:'html':'UTF-8'}{$inHouseGuest.total_price_tax_incl|string_format:"%.2f"}</td>
                            </tr>
                        {/foreach}
                    {else}
                        <tr>
                            <td class="list-empty" colspan="11">
                                <div class="list-empty-msg">
                                    <i class="icon-warning-sign list-empty-icon"></i>
                                    {l s='No guests currently in-house.' mod='qlohotelreports'}
                                </div>
                            </td>
                        </tr>
                    {/if}
                </tbody>
                {if $in_house}
                <tfoot>
                    <tr class="qlo-report-totals">
                        <td colspan="7"><strong>{l s='Totals' mod='qlohotelreports'}</strong></td>
                        <td class="text-right"><strong>{$in_house_totals.los}</strong></td>
                        <td class="text-right"><strong>{$in_house_totals.adults}</strong></td>
                        <td class="text-right"><strong>{$in_house_totals.children}</strong></td>
                        <td class="text-right"><strong>{$currency_sign|escape:'html':'UTF-8'}{$in_house_totals.total_price_tax_incl|string_format:"%.2f"}</strong></td>
                    </tr>
                </tfoot>
                {/if}
            </table>
        </div>
        </div>
        </div>
        {if $in_house}
        <p class="text-muted small">
            {l s='%d room(s) currently in-house.' sprintf=[$in_house|count] mod='qlohotelreports'}
        </p>
        {/if}
    {/if}

    <a class="btn btn-default export-csv" href="{$export_url|escape:'html':'UTF-8'}">
        <i class="icon-cloud-download"></i> {l s='CSV Export' mod='qlohotelreports'}
    </a>

</div>
