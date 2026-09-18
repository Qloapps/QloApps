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

{capture name='stay_periods_tooltip'}
    <div class="bootstrap tooltip_cont">
        <div class="tip_header">
            <div>{l s='Stay Periods'}</div>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th class="tip_element_head">{l s='Duration'}</th>
                    <th class="tip_element_head">{l s='Rooms'}</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$stay_periods item=stay_period}
                    <tr>
                        <td class="tip_element_value">{$stay_period.from} &ndash; {$stay_period.to}</td>
                        <td class="tip_element_value center">{$stay_period.count}</td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
{/capture}
{capture name='stay_periods_tooltip_title'}<span class="badge tooltip-trigger">+{$extra_stay_periods_count}</span>{/capture}
{include file='helpers/tooltip.tpl' tooltip_content=$smarty.capture.stay_periods_tooltip tooltip_title=$smarty.capture.stay_periods_tooltip_title}
