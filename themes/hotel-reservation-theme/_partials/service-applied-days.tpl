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

{if isset($price_calculation_method) && (int) $price_calculation_method != Product::PRICE_CALCULATION_METHOD_ONCE_FOR_BOOKING && !empty($date_from) && !empty($date_to)}
    {assign var='serviceAppliedDates' value=Product::getServicePriceApplicableDates($price_calculation_method, $date_from, $date_to)}
    {assign var='serviceAppliedDays' value=0}
    {foreach from=$serviceAppliedDates item='serviceAppliedGroupDates'}
        {assign var='serviceAppliedDays' value=$serviceAppliedDays + count($serviceAppliedGroupDates)}
    {/foreach}
    {if $serviceAppliedDays}
        {capture name='htl_applied_days_tooltip'}
        <div class="ui-tooltip-header">
            <div class="ui-tooltip-label">{l s='Applied on:'}</div>
        </div>
        <div class="ui-tooltip-body">
            <ul>
                {foreach from=$serviceAppliedDates key='serviceAppliedGroup' item='serviceAppliedGroupDates'}
                    {if $serviceAppliedGroupDates}
                        <li>
                            {if $serviceAppliedGroup == 'checkin'}{l s='Check-in day'}{elseif $serviceAppliedGroup == 'stay'}{l s='During-stay days'}{else}{l s='Check-out day'}{/if}
                            <ul>
                                {foreach from=$serviceAppliedGroupDates item='serviceAppliedDate'}
                                    <li>{Tools::displayDate($serviceAppliedDate)}</li>
                                {/foreach}
                            </ul>
                        </li>
                    {/if}
                {/foreach}
            </ul>
        </div>
        {/capture}
        {capture name='htl_applied_days_badge'}<span class="badge badge-success service-applied-days">{if $serviceAppliedDays == 1}{l s='Applied for 1 Day'}{else}{l s='Applied for %s Days' sprintf=$serviceAppliedDays}{/if}</span>{/capture}
        <div class="service-applied-days-wrap">
            {include file='_partials/ui-tooltip.tpl' tooltip_title=$smarty.capture.htl_applied_days_badge tooltip_content=$smarty.capture.htl_applied_days_tooltip allow_html=true}
        </div>
    {/if}
{/if}
