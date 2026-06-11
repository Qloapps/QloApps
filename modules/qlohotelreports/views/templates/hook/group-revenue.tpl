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

<div class="qlo-report-group-revenue">

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
            {if $active_report == 'refund'}
            <div class="row">
                <label class="col-xs-3">{l s='Refund Status' mod='qlohotelreports'}</label>
                <div class="col-xs-9">
                    <select name="refund_status" class="form-control">
                        <option value="0"{if !$filter_refund_status} selected="selected"{/if}>{l s='All' mod='qlohotelreports'}</option>
                        {foreach $refund_states as $refundState}
                        <option value="{$refundState.id_order_return_state|intval}"{if $filter_refund_status == $refundState.id_order_return_state} selected="selected"{/if}>
                            {$refundState.name|escape:'html':'UTF-8'}
                        </option>
                        {/foreach}
                    </select>
                </div>
            </div>
            {/if}
            {if $active_report == 'outstanding'}
            <div class="row">
                <label class="col-xs-3">{l s='Booking Status' mod='qlohotelreports'}</label>
                <div class="col-xs-9">
                    <select name="outstanding_status" class="form-control">
                        <option value="0"{if !$filter_outstanding_status} selected="selected"{/if}>{l s='All' mod='qlohotelreports'}</option>
                        <option value="2"{if $filter_outstanding_status == 2} selected="selected"{/if}>{l s='Checked-in' mod='qlohotelreports'}</option>
                        <option value="3"{if $filter_outstanding_status == 3} selected="selected"{/if}>{l s='Checked-out' mod='qlohotelreports'}</option>
                    </select>
                </div>
            </div>
            {/if}
            {if $active_report == 'payment' && $payment_methods}
            <div class="row">
                <label class="col-xs-3">{l s='Payment Method' mod='qlohotelreports'}</label>
                <div class="col-xs-9">
                    <select name="payment_method" class="form-control">
                        <option value=""{if !$filter_payment_method} selected="selected"{/if}>{l s='All' mod='qlohotelreports'}</option>
                        {foreach $payment_methods as $pm}
                        <option value="{$pm.payment_method|escape:'html':'UTF-8'}"{if $filter_payment_method == $pm.payment_method} selected="selected"{/if}>
                            {$pm.payment_method|escape:'html':'UTF-8'}
                        </option>
                        {/foreach}
                    </select>
                </div>
            </div>
            {/if}
            {if $active_report == 'tax'}
            <div class="row">
                <label class="col-xs-3">{l s='Revenue Source' mod='qlohotelreports'}</label>
                <div class="col-xs-9">
                    <select name="revenue_source" class="form-control">
                        <option value="all"{if $filter_revenue_source == 'all'} selected="selected"{/if}>{l s='All' mod='qlohotelreports'}</option>
                        <option value="room"{if $filter_revenue_source == 'room'} selected="selected"{/if}>{l s='Room Charges' mod='qlohotelreports'}</option>
                        <option value="service"{if $filter_revenue_source == 'service'} selected="selected"{/if}>{l s='Service Charges' mod='qlohotelreports'}</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <label class="col-xs-3">{l s='Tax Name' mod='qlohotelreports'}</label>
                <div class="col-xs-9">
                    <select name="id_tax" class="form-control">
                        <option value="0"{if !$filter_id_tax} selected="selected"{/if}>{l s='All' mod='qlohotelreports'}</option>
                        {foreach $tax_names as $tax}
                        <option value="{$tax.id_tax|intval}"{if $filter_id_tax == $tax.id_tax} selected="selected"{/if}>
                            {$tax.name|escape:'html':'UTF-8'}{if $tax.rate} ({$tax.rate|string_format:'%.2f'}%){/if}
                        </option>
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

    {* ── Revenue Report ──────────────────────────────────────────────── *}
    {if $active_report == 'revenue'}
        <div class="row">
        <div class="col-lg-12">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>{l s='Date / Period' mod='qlohotelreports'}</th>
                        <th class="text-right">{l s='Rooms Sold' mod='qlohotelreports'}</th>
                        <th class="text-right">{l s='Total Bookings' mod='qlohotelreports'}</th>
                        <th class="text-right">{l s='Room Revenue (excl. Tax)' mod='qlohotelreports'}</th>
                        <th class="text-right">{l s='Extra Services Revenue' mod='qlohotelreports'}</th>
                        <th class="text-right">{l s='Discount Amount' mod='qlohotelreports'}</th>
                        <th class="text-right">{l s='Tax Amount' mod='qlohotelreports'}</th>
                        <th class="text-right">{l s='Refund Amount' mod='qlohotelreports'}</th>
                        <th class="text-right">{l s='Total Collection' mod='qlohotelreports'}</th>
                        <th class="text-right">{l s='Net Revenue' mod='qlohotelreports'}</th>
                        <th class="text-right">{l s='ADR (Avg. Daily Rate)' mod='qlohotelreports'}</th>
                        <th class="text-right">{l s='RevPAR (Rev. Per Avail. Room)' mod='qlohotelreports'}</th>
                        <th class="text-right">{l s='Occupancy %' mod='qlohotelreports'}</th>
                    </tr>
                </thead>
                <tbody>
                    {if $daily_rows}
                        {foreach $daily_rows as $revenueRow}
                            <tr>
                                <td>{$revenueRow.date|escape:'html':'UTF-8'}</td>
                                <td class="text-right">{$revenueRow.rooms_sold|intval}</td>
                                <td class="text-right">{$revenueRow.bookings|intval}</td>
                                <td class="text-right">{$currency_sign|escape:'html':'UTF-8'}{$revenueRow.room_revenue|string_format:"%.2f"}</td>
                                <td class="text-right">{$currency_sign|escape:'html':'UTF-8'}{$revenueRow.service_revenue|string_format:"%.2f"}</td>
                                <td class="text-right">{$currency_sign|escape:'html':'UTF-8'}{$revenueRow.discounts|string_format:"%.2f"}</td>
                                <td class="text-right">{$currency_sign|escape:'html':'UTF-8'}{$revenueRow.tax_amount|string_format:"%.2f"}</td>
                                <td class="text-right">{$currency_sign|escape:'html':'UTF-8'}0.00</td>
                                <td class="text-right">{$currency_sign|escape:'html':'UTF-8'}{$revenueRow.total_collection|string_format:"%.2f"}</td>
                                <td class="text-right">{$currency_sign|escape:'html':'UTF-8'}{$revenueRow.net_revenue|string_format:"%.2f"}</td>
                                <td class="text-right">{$currency_sign|escape:'html':'UTF-8'}{$revenueRow.adr|string_format:"%.2f"}</td>
                                <td class="text-right">{$currency_sign|escape:'html':'UTF-8'}{$revenueRow.revpar|string_format:"%.2f"}</td>
                                <td class="text-right">{$revenueRow.occupancy_pct|string_format:"%.1f"}%</td>
                            </tr>
                        {/foreach}
                    {else}
                        <tr>
                            <td class="list-empty" colspan="13">
                                <div class="list-empty-msg">
                                    <i class="icon-warning-sign list-empty-icon"></i>
                                    {l s='No revenue data found for the selected date range.' mod='qlohotelreports'}
                                </div>
                            </td>
                        </tr>
                    {/if}
                </tbody>
                {if $daily_rows}
                <tfoot>
                    <tr class="qlo-report-totals">
                        <td><strong>{l s='Total' mod='qlohotelreports'}</strong></td>
                        <td class="text-right"><strong>{$revenue_totals.rooms_sold}</strong></td>
                        <td class="text-right"><strong>{$revenue_totals.bookings}</strong></td>
                        <td class="text-right"><strong>{$currency_sign|escape:'html':'UTF-8'}{$revenue_totals.room_revenue|string_format:"%.2f"}</strong></td>
                        <td class="text-right"><strong>{$currency_sign|escape:'html':'UTF-8'}{$revenue_totals.service_revenue|string_format:"%.2f"}</strong></td>
                        <td class="text-right"><strong>{$currency_sign|escape:'html':'UTF-8'}{$revenue_totals.discounts|string_format:"%.2f"}</strong></td>
                        <td class="text-right"><strong>{$currency_sign|escape:'html':'UTF-8'}{$revenue_totals.tax_amount|string_format:"%.2f"}</strong></td>
                        <td class="text-right"><strong>{$currency_sign|escape:'html':'UTF-8'}0.00</strong></td>
                        <td class="text-right"><strong>{$currency_sign|escape:'html':'UTF-8'}{$revenue_totals.total_collection|string_format:"%.2f"}</strong></td>
                        <td class="text-right"><strong>{$currency_sign|escape:'html':'UTF-8'}{$revenue_totals.net_revenue|string_format:"%.2f"}</strong></td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
                {/if}
            </table>
        </div>
        </div>
        </div>
    {/if}

    {* ── Refund Report ───────────────────────────────────────────────── *}
    {if $active_report == 'refund'}
        <div class="row">
        <div class="col-lg-12">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>{l s='Refund Date' mod='qlohotelreports'}</th>
                        <th>{l s='Refund ID' mod='qlohotelreports'}</th>
                        <th>{l s='Booking ID' mod='qlohotelreports'}</th>
                        <th>{l s='Guest Name' mod='qlohotelreports'}</th>
                        <th class="text-right">{l s='Original Booking Amount' mod='qlohotelreports'}</th>
                        <th class="text-right">{l s='Refund Amount' mod='qlohotelreports'}</th>
                        <th>{l s='Refund Method' mod='qlohotelreports'}</th>
                        <th>{l s='Refund Status' mod='qlohotelreports'}</th>
                        <th>{l s='Processed Date' mod='qlohotelreports'}</th>
                        <th>{l s='Processed By' mod='qlohotelreports'}</th>
                        <th>{l s='Refund Reason' mod='qlohotelreports'}</th>
                        <th>{l s='Remarks' mod='qlohotelreports'}</th>
                    </tr>
                </thead>
                <tbody>
                    {if $refunds}
                        {foreach $refunds as $refund}
                            <tr>
                                <td>{if $refund.cancellation_date}{$refund.cancellation_date|date_format:'%d-%m-%Y'}{else}<span class="text-muted">—</span>{/if}</td>
                                <td>{$refund.id_order_return|intval}</td>
                                <td>{$refund.id_order|intval}</td>
                                <td>{$refund.customer_name|escape:'html':'UTF-8'}</td>
                                <td class="text-right">{$refund.currency_sign|escape:'html':'UTF-8'}{$refund.total_price_tax_incl|string_format:"%.2f"}</td>
                                <td class="text-right">{$refund.currency_sign|escape:'html':'UTF-8'}{$refund.refunded_amount|string_format:"%.2f"}</td>
                                <td>{if $refund.refund_method}{$refund.refund_method|escape:'html':'UTF-8'}{else}<span class="text-muted">—</span>{/if}</td>
                                <td>{if $refund.refund_status}{$refund.refund_status|escape:'html':'UTF-8'}{else}<span class="text-muted">—</span>{/if}</td>
                                <td><span class="text-muted">—</span></td>
                                <td><span class="text-muted">—</span></td>
                                <td>{if $refund.cancellation_reason}{$refund.cancellation_reason|escape:'html':'UTF-8'}{else}<span class="text-muted">—</span>{/if}</td>
                                <td><span class="text-muted">—</span></td>
                            </tr>
                        {/foreach}
                    {else}
                        <tr>
                            <td class="list-empty" colspan="12">
                                <div class="list-empty-msg">
                                    <i class="icon-warning-sign list-empty-icon"></i>
                                    {l s='No refunds found for the selected date range.' mod='qlohotelreports'}
                                </div>
                            </td>
                        </tr>
                    {/if}
                </tbody>
                {if $refunds}
                <tfoot>
                    <tr class="qlo-report-totals">
                        <td colspan="5"><strong>{l s='Total Refunded' mod='qlohotelreports'}</strong></td>
                        <td class="text-right"><strong>{$total_refunded|string_format:"%.2f"}</strong></td>
                        <td colspan="6"></td>
                    </tr>
                </tfoot>
                {/if}
            </table>
        </div>
        </div>
        </div>
    {/if}

    {* ── Payment / Collection Report ────────────────────────────────── *}
    {if $active_report == 'payment'}
        <div class="row">
        <div class="col-lg-12">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>{l s='Payment Date' mod='qlohotelreports'}</th>
                        <th>{l s='Payment ID' mod='qlohotelreports'}</th>
                        <th>{l s='Booking ID' mod='qlohotelreports'}</th>
                        <th>{l s='Booking Ref.' mod='qlohotelreports'}</th>
                        <th>{l s='Guest Name' mod='qlohotelreports'}</th>
                        <th>{l s='Payment Method' mod='qlohotelreports'}</th>
                        <th>{l s='Payment Type' mod='qlohotelreports'}</th>
                        <th class="text-right">{l s='Amount' mod='qlohotelreports'}</th>
                        <th>{l s='Payment Status' mod='qlohotelreports'}</th>
                        <th>{l s='Transaction Reference' mod='qlohotelreports'}</th>
                        <th>{l s='Received By' mod='qlohotelreports'}</th>
                    </tr>
                </thead>
                <tbody>
                    {if $payments}
                        {foreach $payments as $payment}
                            <tr>
                                <td>{$payment.date_add|escape:'html':'UTF-8'}</td>
                                <td>{$payment.id_order_payment|intval}</td>
                                <td>{$payment.id_order|intval}</td>
                                <td>{$payment.reference|escape:'html':'UTF-8'}</td>
                                <td>{$payment.customer_name|escape:'html':'UTF-8'}</td>
                                <td>{$payment.payment_method|escape:'html':'UTF-8'}</td>
                                <td>
                                    {if isset($payment_types[$payment.payment_type])}{$payment_types[$payment.payment_type]|escape:'html':'UTF-8'}{else}<span class="text-muted">—</span>{/if}
                                </td>
                                <td class="text-right">{if $payment.currency_sign}{$payment.currency_sign|escape:'html':'UTF-8'}{else}{$currency_sign|escape:'html':'UTF-8'}{/if}{$payment.amount|string_format:"%.2f"}</td>
                                <td>{l s='Success' mod='qlohotelreports'}</td>
                                <td>{if $payment.transaction_id}{$payment.transaction_id|escape:'html':'UTF-8'}{else}<span class="text-muted">—</span>{/if}</td>
                                <td><span class="text-muted">—</span></td>
                            </tr>
                        {/foreach}
                    {else}
                        <tr>
                            <td class="list-empty" colspan="11">
                                <div class="list-empty-msg">
                                    <i class="icon-warning-sign list-empty-icon"></i>
                                    {l s='No payments found for the selected date range.' mod='qlohotelreports'}
                                </div>
                            </td>
                        </tr>
                    {/if}
                </tbody>
                {if $payments}
                <tfoot>
                    <tr class="qlo-report-totals">
                        <td colspan="7"><strong>{l s='Total Collected' mod='qlohotelreports'}</strong></td>
                        <td class="text-right"><strong>{$currency_sign|escape:'html':'UTF-8'}{$total_payments|string_format:"%.2f"}</strong></td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
                {/if}
            </table>
        </div>
        </div>
        </div>
    {/if}

    {* ── Tax Report ──────────────────────────────────────────────────── *}
    {if $active_report == 'tax'}
        <div class="row">
        <div class="col-lg-12">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>{l s='Booking Date' mod='qlohotelreports'}</th>
                        <th>{l s='Booking Ref.' mod='qlohotelreports'}</th>
                        <th>{l s='Guest Name' mod='qlohotelreports'}</th>
                        <th>{l s='Revenue Source' mod='qlohotelreports'}</th>
                        <th>{l s='Room Type' mod='qlohotelreports'}</th>
                        <th class="text-right">{l s='Taxable Amount' mod='qlohotelreports'}</th>
                        <th>{l s='Tax Name' mod='qlohotelreports'}</th>
                        <th class="text-right">{l s='Tax Rate %' mod='qlohotelreports'}</th>
                        <th class="text-right">{l s='Tax Amount' mod='qlohotelreports'}</th>
                    </tr>
                </thead>
                <tbody>
                    {if $tax_rows}
                        {foreach $tax_rows as $taxRow}
                            <tr>
                                <td>{$taxRow.date_add|date_format:'%d-%m-%Y'}</td>
                                <td>{$taxRow.reference|escape:'html':'UTF-8'}</td>
                                <td>{$taxRow.customer_name|escape:'html':'UTF-8'}</td>
                                <td>{if $taxRow.revenue_source == 'service'}{l s='Service Charge' mod='qlohotelreports'}{else}{l s='Room Charge' mod='qlohotelreports'}{/if}</td>
                                <td>{$taxRow.room_type_name|escape:'html':'UTF-8'}</td>
                                <td class="text-right">{$currency_sign|escape:'html':'UTF-8'}{$taxRow.taxable_amount|string_format:"%.2f"}</td>
                                <td>{if $taxRow.tax_name}{$taxRow.tax_name|escape:'html':'UTF-8'}{else}<span class="text-muted">—</span>{/if}</td>
                                <td class="text-right">{$taxRow.tax_rate|string_format:"%.2f"}%</td>
                                <td class="text-right">{$currency_sign|escape:'html':'UTF-8'}{$taxRow.tax_amount|string_format:"%.2f"}</td>
                            </tr>
                        {/foreach}
                    {else}
                        <tr>
                            <td class="list-empty" colspan="9">
                                <div class="list-empty-msg">
                                    <i class="icon-warning-sign list-empty-icon"></i>
                                    {l s='No tax data found for the selected date range.' mod='qlohotelreports'}
                                </div>
                            </td>
                        </tr>
                    {/if}
                </tbody>
                {if $tax_rows}
                <tfoot>
                    <tr class="qlo-report-totals">
                        <td colspan="5"><strong>{l s='Totals' mod='qlohotelreports'}</strong></td>
                        <td class="text-right"><strong>{$currency_sign|escape:'html':'UTF-8'}{$tax_totals.taxable_amount|string_format:"%.2f"}</strong></td>
                        <td></td>
                        <td></td>
                        <td class="text-right"><strong>{$currency_sign|escape:'html':'UTF-8'}{$tax_totals.tax_amount|string_format:"%.2f"}</strong></td>
                    </tr>
                </tfoot>
                {/if}
            </table>
        </div>
        </div>
        </div>

        {if $tax_by_name}
        <h4 class="qlo-section-heading" style="margin-top:20px">{l s='Tax Summary by Type' mod='qlohotelreports'}</h4>
        <div class="row">
        <div class="col-lg-12">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>{l s='Tax Name' mod='qlohotelreports'}</th>
                        <th class="text-right">{l s='Tax Rate %' mod='qlohotelreports'}</th>
                        <th class="text-right">{l s='Total Taxable Amount' mod='qlohotelreports'}</th>
                        <th class="text-right">{l s='Total Tax Collected' mod='qlohotelreports'}</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach $tax_by_name as $taxSummary}
                    <tr>
                        <td>{$taxSummary.tax_name|escape:'html':'UTF-8'}</td>
                        <td class="text-right">{$taxSummary.tax_rate|string_format:"%.2f"}%</td>
                        <td class="text-right">{$currency_sign|escape:'html':'UTF-8'}{$taxSummary.taxable_amount|string_format:"%.2f"}</td>
                        <td class="text-right">{$currency_sign|escape:'html':'UTF-8'}{$taxSummary.tax_amount|string_format:"%.2f"}</td>
                    </tr>
                    {/foreach}
                </tbody>
                <tfoot>
                    <tr class="qlo-report-totals">
                        <td colspan="2"><strong>{l s='Grand Total' mod='qlohotelreports'}</strong></td>
                        <td class="text-right"><strong>{$currency_sign|escape:'html':'UTF-8'}{$tax_totals.taxable_amount|string_format:"%.2f"}</strong></td>
                        <td class="text-right"><strong>{$currency_sign|escape:'html':'UTF-8'}{$tax_totals.tax_amount|string_format:"%.2f"}</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        </div>
        </div>
        {/if}
    {/if}

    {* ── Outstanding / Balance Due Report ───────────────────────────── *}
    {if $active_report == 'outstanding'}
        <div class="row">
        <div class="col-lg-12">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>{l s='Booking ID' mod='qlohotelreports'}</th>
                        <th>{l s='Guest Name' mod='qlohotelreports'}</th>
                        <th>{l s='Guest Email' mod='qlohotelreports'}</th>
                        <th>{l s='Guest Phone' mod='qlohotelreports'}</th>
                        <th>{l s='Room Type' mod='qlohotelreports'}</th>
                        <th>{l s='Room No.' mod='qlohotelreports'}</th>
                        <th>{l s='Check-in Date' mod='qlohotelreports'}</th>
                        <th>{l s='Check-out Date' mod='qlohotelreports'}</th>
                        <th class="text-right">{l s='Total Charges' mod='qlohotelreports'}</th>
                        <th class="text-right">{l s='Total Paid' mod='qlohotelreports'}</th>
                        <th class="text-right">{l s='Outstanding Balance' mod='qlohotelreports'}</th>
                        <th class="text-center">{l s='Days Overdue' mod='qlohotelreports'}</th>
                        <th>{l s='Last Payment Date' mod='qlohotelreports'}</th>
                        <th>{l s='Booking Status' mod='qlohotelreports'}</th>
                    </tr>
                </thead>
                <tbody>
                    {if $outstanding}
                        {foreach $outstanding as $outstandingRow}
                            <tr>
                                <td>{$outstandingRow.id_order|intval}</td>
                                <td>{$outstandingRow.customer_name|escape:'html':'UTF-8'}</td>
                                <td>{if $outstandingRow.email}{$outstandingRow.email|escape:'html':'UTF-8'}{else}<span class="text-muted">—</span>{/if}</td>
                                <td>{if $outstandingRow.phone}{$outstandingRow.phone|escape:'html':'UTF-8'}{else}<span class="text-muted">—</span>{/if}</td>
                                <td>{$outstandingRow.room_type_name|escape:'html':'UTF-8'}</td>
                                <td>{$outstandingRow.room_num|escape:'html':'UTF-8'}</td>
                                <td>{$outstandingRow.date_from|escape:'html':'UTF-8'}</td>
                                <td>{$outstandingRow.date_to|escape:'html':'UTF-8'}</td>
                                <td class="text-right">{$currency_sign|escape:'html':'UTF-8'}{$outstandingRow.total_charges|string_format:"%.2f"}</td>
                                <td class="text-right">{$currency_sign|escape:'html':'UTF-8'}{$outstandingRow.total_paid|string_format:"%.2f"}</td>
                                <td class="text-right qlo-text-danger">{$currency_sign|escape:'html':'UTF-8'}{$outstandingRow.balance_due|string_format:"%.2f"}</td>
                                <td class="text-center">{$outstandingRow.days_overdue|intval}</td>
                                <td>{if $outstandingRow.last_payment_date}{$outstandingRow.last_payment_date|date_format:'%d-%m-%Y'}{else}<span class="text-muted">—</span>{/if}</td>
                                <td>
                                    {if isset($booking_statuses[$outstandingRow.id_status])}{$booking_statuses[$outstandingRow.id_status]|escape:'html':'UTF-8'}{else}<span class="text-muted">—</span>{/if}
                                </td>
                            </tr>
                        {/foreach}
                    {else}
                        <tr>
                            <td class="list-empty" colspan="14">
                                <div class="list-empty-msg">
                                    <i class="icon-warning-sign list-empty-icon"></i>
                                    {l s='No outstanding balances found for the selected date range.' mod='qlohotelreports'}
                                </div>
                            </td>
                        </tr>
                    {/if}
                </tbody>
                {if $outstanding}
                <tfoot>
                    <tr class="qlo-report-totals">
                        <td colspan="10"><strong>{l s='Total Outstanding' mod='qlohotelreports'}</strong></td>
                        <td class="text-right qlo-text-danger"><strong>{$currency_sign|escape:'html':'UTF-8'}{$total_outstanding|string_format:"%.2f"}</strong></td>
                        <td colspan="3"></td>
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

</div>
