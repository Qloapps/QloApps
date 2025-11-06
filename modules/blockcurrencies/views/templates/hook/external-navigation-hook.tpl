{*
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

{if count($currencies) > 1}
    <ul class="nav nav-pills nav-stacked visible-xs wk-nav-style">
        <li>
            <a class="btn-currency-selector-popup">
                {foreach from=$currencies item=currency}
                    {if $cookie->id_currency == $currency.id_currency}
                        {$currency.iso_code}
                        <span class="caret"></span>
                    {/if}
                {/foreach}
            </a>
        </li>
    </ul>

    <div id="currency-selector-popup" style="display: none;">
        <div class="list-group">
            {foreach from=$currencies item=currency}
                {if strpos($currency.name, '('|cat:$currency.iso_code:')') === false}
                    {assign var="currency_name" value={l s='%s (%s)' sprintf=[$currency.name, $currency.iso_code]}}
                {else}
                    {assign var="currency_name" value=$currency.name}
                {/if}

                <a class="list-group-item {if $cookie->id_currency == $currency.id_currency}active{/if}"
                    href="javascript:setCurrency({$currency.id_currency});"
                    rel="nofollow"
                    title="{$currency_name}">
                    <span>{$currency_name}</span>
                </a>
            {/foreach}
        </div>
    </div>
{/if}
