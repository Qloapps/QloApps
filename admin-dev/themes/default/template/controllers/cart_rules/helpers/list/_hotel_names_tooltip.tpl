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

{if $hotel_names_first}
    <span>{$hotel_names_first|escape:'html':'UTF-8'}</span>
    {if $hotel_names_remaining}
        {capture name='hotel_names_tooltip'}
            <div class="tooltip_cont">
                <div class="tip_header"><div class="tip_date">{l s='Selected Hotels'}</div></div>
                <ul>
                    {foreach from=$selected_hotels item='hotel'}
                        <li class="tip_element_value">{$hotel|escape:'html':'UTF-8'}</li>
                    {/foreach}
                </ul>
            </div>
        {/capture}
        {capture name='hotel_names_tooltip_title'}<span class="badge tooltip-trigger">+{$hotel_names_remaining|@count}</span>{/capture}
        {include file='helpers/tooltip.tpl' tooltip_content=$smarty.capture.hotel_names_tooltip tooltip_title=$smarty.capture.hotel_names_tooltip_title}
    {/if}
{else}
    --
{/if}
