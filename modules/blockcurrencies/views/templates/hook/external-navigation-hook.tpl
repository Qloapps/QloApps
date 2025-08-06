{*
* 2010-2022 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through LICENSE.txt file inside our module
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright 2010-2022 Webkul IN
* @license LICENSE.txt
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
