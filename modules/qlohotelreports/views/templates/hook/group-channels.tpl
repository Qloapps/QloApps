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
        <div class="row">
            <label class="col-xs-3">{l s='Booking Source' mod='qlohotelreports'}</label>
            <div class="col-xs-9">
                <select name="booking_type" class="form-control">
                    <option value="0"{if !$filter_booking_type} selected="selected"{/if}>{l s='All Sources' mod='qlohotelreports'}</option>
                    <option value="1"{if $filter_booking_type == 1} selected="selected"{/if}>{l s='Online / Direct' mod='qlohotelreports'}</option>
                    <option value="2"{if $filter_booking_type == 2} selected="selected"{/if}>{l s='Walk-in / Admin' mod='qlohotelreports'}</option>
                </select>
            </div>
        </div>
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
   BOOKING SOURCE
   ═══════════════════════════════════════════════════════════════════════════ *}
{if $active_report == 'source'}

    <div class="row">
    <div class="col-lg-12">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>{l s='Booking Source' mod='qlohotelreports'}</th>
                    <th class="text-center">{l s='Total Bookings' mod='qlohotelreports'}</th>
                    <th class="text-center">{l s='Nights Sold' mod='qlohotelreports'}</th>
                    <th class="text-right">{l s='Room Revenue (excl. Tax)' mod='qlohotelreports'}</th>
                    <th class="text-right">{l s='Extra Services Revenue' mod='qlohotelreports'}</th>
                    <th class="text-right">{l s='Discount Amount' mod='qlohotelreports'}</th>
                    <th class="text-right">{l s='Refund Amount' mod='qlohotelreports'}</th>
                    <th class="text-right">{l s='Tax Amount' mod='qlohotelreports'}</th>
                    <th class="text-right">{l s='Total Collection' mod='qlohotelreports'}</th>
                    <th class="text-right">{l s='Net Revenue' mod='qlohotelreports'}</th>
                    <th class="text-center">{l s='Cancellations' mod='qlohotelreports'}</th>
                    <th class="text-center">{l s='Cancel Rate %' mod='qlohotelreports'}</th>
                    <th class="text-right">{l s='ADR (Avg. Daily Rate)' mod='qlohotelreports'}</th>
                    <th class="text-right">{l s='Contribution %' mod='qlohotelreports'}</th>
                </tr>
            </thead>
            <tbody>
                {if $source_rows}
                    {foreach $source_rows as $sourceRow}
                    <tr>
                        <td>{$sourceRow.channel_label|escape:'html':'UTF-8'}</td>
                        <td class="text-center">{$sourceRow.bookings|intval}</td>
                        <td class="text-center">{$sourceRow.room_nights|intval}</td>
                        <td class="text-right">{displayPrice price=$sourceRow.revenue_excl currency=$id_currency}</td>
                        <td class="text-right">{displayPrice price=0 currency=$id_currency}</td>
                        <td class="text-right">{displayPrice price=$sourceRow.discount_amount currency=$id_currency}</td>
                        <td class="text-right">{displayPrice price=$sourceRow.refund_amount currency=$id_currency}</td>
                        <td class="text-right">{displayPrice price=$sourceRow.tax_amount currency=$id_currency}</td>
                        <td class="text-right">{displayPrice price=$sourceRow.revenue_incl currency=$id_currency}</td>
                        <td class="text-right">{displayPrice price=$sourceRow.revenue_excl currency=$id_currency}</td>
                        <td class="text-center">{$sourceRow.cancellations|intval}</td>
                        <td class="text-center">{$sourceRow.cancel_rate_pct|string_format:'%.1f'}%</td>
                        <td class="text-right">{displayPrice price=$sourceRow.adr currency=$id_currency}</td>
                        <td class="text-right">{$sourceRow.contribution_pct|string_format:'%.1f'}%</td>
                    </tr>
                    {/foreach}
                {else}
                    <tr>
                        <td class="list-empty" colspan="14">
                            <div class="list-empty-msg">
                                <i class="icon-warning-sign list-empty-icon"></i>
                                {l s='No bookings found for the selected period.' mod='qlohotelreports'}
                            </div>
                        </td>
                    </tr>
                {/if}
            </tbody>
            {if $source_rows}
            <tfoot class="qlo-report-totals">
                <tr>
                    <td><strong>{l s='Total' mod='qlohotelreports'}</strong></td>
                    <td class="text-center"><strong>{$source_totals.bookings|intval}</strong></td>
                    <td class="text-center"><strong>{$source_totals.room_nights|intval}</strong></td>
                    <td class="text-right"><strong>{displayPrice price=$source_totals.revenue_excl currency=$id_currency}</strong></td>
                    <td class="text-right"><strong>{displayPrice price=0 currency=$id_currency}</strong></td>
                    <td class="text-right"><strong>{displayPrice price=$source_totals.discount_amount currency=$id_currency}</strong></td>
                    <td class="text-right"><strong>{displayPrice price=$source_totals.refund_amount currency=$id_currency}</strong></td>
                    <td class="text-right"><strong>{displayPrice price=$source_totals.tax_amount currency=$id_currency}</strong></td>
                    <td class="text-right"><strong>{displayPrice price=$source_totals.revenue_incl currency=$id_currency}</strong></td>
                    <td class="text-right"><strong>{displayPrice price=$source_totals.revenue_excl currency=$id_currency}</strong></td>
                    <td class="text-center"><strong>{$source_totals.cancellations|intval}</strong></td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
            {/if}
        </table>
    </div>
    </div>
    </div>

{* ═══════════════════════════════════════════════════════════════════════════
   PAYMENT METHODS
   ═══════════════════════════════════════════════════════════════════════════ *}
{elseif $active_report == 'payment-method'}

    <div class="row">
    <div class="col-lg-12">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>{l s='Payment Method' mod='qlohotelreports'}</th>
                    <th>{l s='Module' mod='qlohotelreports'}</th>
                    <th class="text-center">{l s='Bookings' mod='qlohotelreports'}</th>
                    <th class="text-right">{l s='Revenue (excl. Tax)' mod='qlohotelreports'}</th>
                    <th class="text-right">{l s='Revenue (incl. Tax)' mod='qlohotelreports'}</th>
                </tr>
            </thead>
            <tbody>
                {if $payment_rows}
                    {foreach $payment_rows as $paymentMethodRow}
                    <tr>
                        <td>{$paymentMethodRow.payment_method|escape:'html':'UTF-8'}</td>
                        <td>{$paymentMethodRow.module|escape:'html':'UTF-8'}</td>
                        <td class="text-center">{$paymentMethodRow.bookings|intval}</td>
                        <td class="text-right">{displayPrice price=$paymentMethodRow.revenue_excl currency=$id_currency}</td>
                        <td class="text-right">{displayPrice price=$paymentMethodRow.revenue_incl currency=$id_currency}</td>
                    </tr>
                    {/foreach}
                {else}
                    <tr>
                        <td class="list-empty" colspan="5">
                            <div class="list-empty-msg">
                                <i class="icon-warning-sign list-empty-icon"></i>
                                {l s='No payment data found for the selected period.' mod='qlohotelreports'}
                            </div>
                        </td>
                    </tr>
                {/if}
            </tbody>
            {if $payment_rows}
            <tfoot class="qlo-report-totals">
                <tr>
                    <td colspan="3"><strong>{l s='Total' mod='qlohotelreports'}</strong></td>
                    <td colspan="2" class="text-right"><strong>{displayPrice price=$total_revenue currency=$id_currency}</strong></td>
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
