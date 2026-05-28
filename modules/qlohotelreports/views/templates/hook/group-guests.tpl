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

{* ── Sub-report nav ──────────────────────────────────────────────────────── *}
<ul class="nav nav-pills qlo-sub-report-nav">
    {foreach $guest_sub_reports as $reportKey => $reportLabel}
    <li{if $active_report == $reportKey} class="active"{/if}>
        <a href="{$filter_base_url|escape:'html':'UTF-8'}&report={$reportKey|escape:'html':'UTF-8'}">{$reportLabel|escape:'html':'UTF-8'}</a>
    </li>
    {/foreach}
</ul>

{* ── Filters ─────────────────────────────────────────────────────────────── *}
<form method="get" action="{$filter_base_url|escape:'html':'UTF-8'}" class="form-horizontal clearfix list_action_wrapper">
    <input type="hidden" name="controller" value="AdminStats">
    <input type="hidden" name="module" value="qlohotelreports">
    <input type="hidden" name="tab" value="guests">
    <input type="hidden" name="report" value="{$active_report|escape:'html':'UTF-8'}">
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
        {if $active_report == 'guest-directory'}
        <div class="row">
            <label class="col-xs-3">{l s='Guest Type' mod='qlohotelreports'}</label>
            <div class="col-xs-9">
                <select name="guest_type" class="form-control">
                    <option value=""{if !$filter_guest_type} selected="selected"{/if}>{l s='All Guests' mod='qlohotelreports'}</option>
                    <option value="new"{if $filter_guest_type == 'new'} selected="selected"{/if}>{l s='New Guests' mod='qlohotelreports'}</option>
                    <option value="returning"{if $filter_guest_type == 'returning'} selected="selected"{/if}>{l s='Returning Guests' mod='qlohotelreports'}</option>
                </select>
            </div>
        </div>
        {/if}
        {if $active_report == 'services'}
        <div class="row">
            <label class="col-xs-3">{l s='Service Category' mod='qlohotelreports'}</label>
            <div class="col-xs-9">
                <select name="id_category" class="form-control">
                    <option value="0"{if !$filter_id_category} selected="selected"{/if}>{l s='All Categories' mod='qlohotelreports'}</option>
                    {foreach $service_categories as $cat}
                    <option value="{$cat.id_category|intval}"{if $filter_id_category == $cat.id_category} selected="selected"{/if}>
                        {$cat.name|escape:'html':'UTF-8'}
                    </option>
                    {/foreach}
                </select>
            </div>
        </div>
        <div class="row">
            <label class="col-xs-3">{l s='Service Name' mod='qlohotelreports'}</label>
            <div class="col-xs-9">
                <select name="id_service_product" class="form-control">
                    <option value="0"{if !$filter_id_service} selected="selected"{/if}>{l s='All Services' mod='qlohotelreports'}</option>
                    {foreach $service_products as $svc}
                    <option value="{$svc.id_product|intval}"{if $filter_id_service == $svc.id_product} selected="selected"{/if}>
                        {$svc.name|escape:'html':'UTF-8'}
                    </option>
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
   SERVICES
   ═══════════════════════════════════════════════════════════════════════════ *}
{if $active_report == 'services'}

    <div class="row">
    <div class="col-lg-12">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>{l s='Date' mod='qlohotelreports'}</th>
                    <th>{l s='Booking Ref. No.' mod='qlohotelreports'}</th>
                    <th>{l s='Guest Name' mod='qlohotelreports'}</th>
                    <th>{l s='Room No.' mod='qlohotelreports'}</th>
                    <th>{l s='Service Name' mod='qlohotelreports'}</th>
                    <th>{l s='Service Category' mod='qlohotelreports'}</th>
                    <th class="text-center">{l s='Quantity' mod='qlohotelreports'}</th>
                    <th class="text-right">{l s='Unit Price' mod='qlohotelreports'}</th>
                    <th class="text-right">{l s='Total Price (excl. Tax)' mod='qlohotelreports'}</th>
                    <th class="text-right">{l s='Tax Amount' mod='qlohotelreports'}</th>
                    <th class="text-right">{l s='Grand Total' mod='qlohotelreports'}</th>
                </tr>
            </thead>
            <tbody>
                {if $service_rows}
                    {foreach $service_rows as $serviceRow}
                    <tr>
                        <td>{$serviceRow.date_add|date_format:'%Y-%m-%d'}</td>
                        <td>{$serviceRow.reference|escape:'html':'UTF-8'}</td>
                        <td>{$serviceRow.customer_name|escape:'html':'UTF-8'}</td>
                        <td>{$serviceRow.room_num|escape:'html':'UTF-8'}</td>
                        <td>{$serviceRow.service_name|escape:'html':'UTF-8'}</td>
                        <td>{if $serviceRow.service_category}{$serviceRow.service_category|escape:'html':'UTF-8'}{else}&mdash;{/if}</td>
                        <td class="text-center">{$serviceRow.quantity|intval}</td>
                        <td class="text-right">{$currency_sign|escape:'html':'UTF-8'}{$serviceRow.unit_price|string_format:'%.2f'}</td>
                        <td class="text-right">{$currency_sign|escape:'html':'UTF-8'}{$serviceRow.total_price_tax_excl|string_format:'%.2f'}</td>
                        <td class="text-right">{$currency_sign|escape:'html':'UTF-8'}{$serviceRow.tax_amount|string_format:'%.2f'}</td>
                        <td class="text-right">{$currency_sign|escape:'html':'UTF-8'}{$serviceRow.total_price_tax_incl|string_format:'%.2f'}</td>
                    </tr>
                    {/foreach}
                {else}
                    <tr>
                        <td class="list-empty" colspan="11">
                            <div class="list-empty-msg">
                                <i class="icon-warning-sign list-empty-icon"></i>
                                {l s='No service transactions for the selected period.' mod='qlohotelreports'}
                            </div>
                        </td>
                    </tr>
                {/if}
            </tbody>
            {if $service_rows}
            <tfoot class="qlo-report-totals">
                <tr>
                    <td colspan="8"><strong>{l s='Total' mod='qlohotelreports'}</strong></td>
                    <td class="text-right"><strong>{$currency_sign|escape:'html':'UTF-8'}{$service_totals.total_excl|string_format:'%.2f'}</strong></td>
                    <td class="text-right"><strong>{$currency_sign|escape:'html':'UTF-8'}{$service_totals.total_tax|string_format:'%.2f'}</strong></td>
                    <td class="text-right"><strong>{$currency_sign|escape:'html':'UTF-8'}{$service_totals.total_incl|string_format:'%.2f'}</strong></td>
                </tr>
            </tfoot>
            {/if}
        </table>
    </div>
    </div>
    </div>

{* ═══════════════════════════════════════════════════════════════════════════
   GUEST DIRECTORY
   ═══════════════════════════════════════════════════════════════════════════ *}
{elseif $active_report == 'guest-directory'}

    <div class="row">
    <div class="col-lg-12">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>{l s='Guest ID' mod='qlohotelreports'}</th>
                    <th>{l s='Guest Name' mod='qlohotelreports'}</th>
                    <th>{l s='Email' mod='qlohotelreports'}</th>
                    <th>{l s='Phone' mod='qlohotelreports'}</th>
                    <th>{l s='Country' mod='qlohotelreports'}</th>
                    <th>{l s='State' mod='qlohotelreports'}</th>
                    <th>{l s='City' mod='qlohotelreports'}</th>
                    <th>{l s='Company' mod='qlohotelreports'}</th>
                    <th>{l s='VAT Number' mod='qlohotelreports'}</th>
                    <th>{l s='Address' mod='qlohotelreports'}</th>
                    <th>{l s='Postcode' mod='qlohotelreports'}</th>
                    <th>{l s='Guest Type' mod='qlohotelreports'}</th>
                    <th class="text-center">{l s='Total Stays' mod='qlohotelreports'}</th>
                    <th class="text-center">{l s='Total Nights' mod='qlohotelreports'}</th>
                    <th>{l s='Last Stay Date' mod='qlohotelreports'}</th>
                    <th class="text-right">{l s='Lifetime Revenue' mod='qlohotelreports'}</th>
                    <th class="text-right">{l s='Avg Spend Per Stay' mod='qlohotelreports'}</th>
                </tr>
            </thead>
            <tbody>
                {if $guests}
                    {foreach $guests as $guest}
                    <tr>
                        <td>{$guest.id_customer|intval}</td>
                        <td>{$guest.customer_name|escape:'html':'UTF-8'}</td>
                        <td>{$guest.email|escape:'html':'UTF-8'}</td>
                        <td>{if $guest.phone}{$guest.phone|escape:'html':'UTF-8'}{else}<span class="text-muted">—</span>{/if}</td>
                        <td>{if $guest.country}{$guest.country|escape:'html':'UTF-8'}{else}<span class="text-muted">—</span>{/if}</td>
                        <td>{if $guest.state}{$guest.state|escape:'html':'UTF-8'}{else}<span class="text-muted">—</span>{/if}</td>
                        <td>{if $guest.city}{$guest.city|escape:'html':'UTF-8'}{else}<span class="text-muted">—</span>{/if}</td>
                        <td>{if $guest.company}{$guest.company|escape:'html':'UTF-8'}{else}<span class="text-muted">—</span>{/if}</td>
                        <td>{if $guest.vat_number}{$guest.vat_number|escape:'html':'UTF-8'}{else}<span class="text-muted">—</span>{/if}</td>
                        <td>{if $guest.address1}{$guest.address1|escape:'html':'UTF-8'}{if $guest.address2}, {$guest.address2|escape:'html':'UTF-8'}{/if}{else}<span class="text-muted">—</span>{/if}</td>
                        <td>{if $guest.postcode}{$guest.postcode|escape:'html':'UTF-8'}{else}<span class="text-muted">—</span>{/if}</td>
                        <td>{if $guest.total_stays == 1}{l s='New' mod='qlohotelreports'}{else}{l s='Returning' mod='qlohotelreports'}{/if}</td>
                        <td class="text-center">{$guest.total_stays|intval}</td>
                        <td class="text-center">{$guest.total_nights|intval}</td>
                        <td>{if $guest.last_stay}{$guest.last_stay|escape:'html':'UTF-8'}{else}<span class="text-muted">—</span>{/if}</td>
                        <td class="text-right">{$currency_sign|escape:'html':'UTF-8'}{$guest.lifetime_revenue|string_format:'%.2f'}</td>
                        <td class="text-right">{$currency_sign|escape:'html':'UTF-8'}{$guest.avg_spend_per_stay|string_format:'%.2f'}</td>
                    </tr>
                    {/foreach}
                {else}
                    <tr>
                        <td class="list-empty" colspan="17">
                            <div class="list-empty-msg">
                                <i class="icon-warning-sign list-empty-icon"></i>
                                {l s='No guests found for the selected period.' mod='qlohotelreports'}
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
