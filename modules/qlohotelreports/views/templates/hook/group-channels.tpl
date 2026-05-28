{**
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*}

{* ── Sub-report nav ──────────────────────────────────────────────────────── *}
<ul class="nav nav-pills qlo-sub-report-nav">
    {foreach $channel_sub_reports as $reportKey => $reportLabel}
    <li{if $active_report == $reportKey} class="active"{/if}>
        <a href="{$filter_base_url|escape:'html':'UTF-8'}&report={$reportKey|escape:'html':'UTF-8'}">{$reportLabel|escape:'html':'UTF-8'}</a>
    </li>
    {/foreach}
</ul>

{* ── Filters ─────────────────────────────────────────────────────────────── *}
<form method="get" action="{$filter_base_url|escape:'html':'UTF-8'}" class="form-horizontal">
    <input type="hidden" name="controller" value="AdminStats">
    <input type="hidden" name="module" value="qlohotelreports">
    <input type="hidden" name="tab" value="channels">
    <input type="hidden" name="report" value="{$active_report|escape:'html':'UTF-8'}">
    {if isset($smarty.get.token)}<input type="hidden" name="token" value="{$smarty.get.token|escape:'html':'UTF-8'}">{/if}
    {if $hotels|count > 1}
    <div class="row row-margin-bottom">
        <label class="control-label col-lg-3">{l s='Hotel' mod='qlohotelreports'}</label>
        <div class="col-lg-3">
            <select name="id_hotel" class="form-control">
                <option value="0"{if !$id_hotel} selected="selected"{/if}>{l s='All Hotels' mod='qlohotelreports'}</option>
                {foreach $hotels as $hotel}
                <option value="{$hotel.id|intval}"{if $id_hotel == $hotel.id} selected="selected"{/if}>{$hotel.hotel_name|escape:'html':'UTF-8'}</option>
                {/foreach}
            </select>
        </div>
    </div>
    {/if}
    <div class="row row-margin-bottom">
        <label class="control-label col-lg-3">{l s='Booking Source' mod='qlohotelreports'}</label>
        <div class="col-lg-3">
            <select name="booking_type" class="form-control">
                <option value="0"{if !$filter_booking_type} selected="selected"{/if}>{l s='All Sources' mod='qlohotelreports'}</option>
                <option value="1"{if $filter_booking_type == 1} selected="selected"{/if}>{l s='Online / Direct' mod='qlohotelreports'}</option>
                <option value="2"{if $filter_booking_type == 2} selected="selected"{/if}>{l s='Walk-in / Admin' mod='qlohotelreports'}</option>
            </select>
        </div>
    </div>
    <div class="row row-margin-bottom">
        <div class="col-lg-3 col-lg-offset-3">
            <button type="submit" class="btn btn-sm btn-default">
                <i class="icon-filter"></i> {l s='Apply' mod='qlohotelreports'}
            </button>
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
                        <td class="text-right">{$currency_sign|escape:'html':'UTF-8'}{$sourceRow.revenue_excl|string_format:'%.2f'}</td>
                        <td class="text-right">{$currency_sign|escape:'html':'UTF-8'}0.00</td>
                        <td class="text-right">{$currency_sign|escape:'html':'UTF-8'}{$sourceRow.discount_amount|string_format:'%.2f'}</td>
                        <td class="text-right">{$currency_sign|escape:'html':'UTF-8'}{$sourceRow.refund_amount|string_format:'%.2f'}</td>
                        <td class="text-right">{$currency_sign|escape:'html':'UTF-8'}{$sourceRow.tax_amount|string_format:'%.2f'}</td>
                        <td class="text-right">{$currency_sign|escape:'html':'UTF-8'}{$sourceRow.revenue_incl|string_format:'%.2f'}</td>
                        <td class="text-right">{$currency_sign|escape:'html':'UTF-8'}{$sourceRow.revenue_excl|string_format:'%.2f'}</td>
                        <td class="text-center">{$sourceRow.cancellations|intval}</td>
                        <td class="text-center">{$sourceRow.cancel_rate_pct|string_format:'%.1f'}%</td>
                        <td class="text-right">{$currency_sign|escape:'html':'UTF-8'}{$sourceRow.adr|string_format:'%.2f'}</td>
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
                    <td class="text-right"><strong>{$currency_sign|escape:'html':'UTF-8'}{$source_totals.revenue_excl|string_format:'%.2f'}</strong></td>
                    <td class="text-right"><strong>{$currency_sign|escape:'html':'UTF-8'}0.00</strong></td>
                    <td class="text-right"><strong>{$currency_sign|escape:'html':'UTF-8'}{$source_totals.discount_amount|string_format:'%.2f'}</strong></td>
                    <td class="text-right"><strong>{$currency_sign|escape:'html':'UTF-8'}{$source_totals.refund_amount|string_format:'%.2f'}</strong></td>
                    <td class="text-right"><strong>{$currency_sign|escape:'html':'UTF-8'}{$source_totals.tax_amount|string_format:'%.2f'}</strong></td>
                    <td class="text-right"><strong>{$currency_sign|escape:'html':'UTF-8'}{$source_totals.revenue_incl|string_format:'%.2f'}</strong></td>
                    <td class="text-right"><strong>{$currency_sign|escape:'html':'UTF-8'}{$source_totals.revenue_excl|string_format:'%.2f'}</strong></td>
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
                        <td class="text-right">{$currency_sign|escape:'html':'UTF-8'}{$paymentMethodRow.revenue_excl|string_format:'%.2f'}</td>
                        <td class="text-right">{$currency_sign|escape:'html':'UTF-8'}{$paymentMethodRow.revenue_incl|string_format:'%.2f'}</td>
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
                    <td colspan="2" class="text-right"><strong>{$currency_sign|escape:'html':'UTF-8'}{$total_revenue|string_format:'%.2f'}</strong></td>
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
