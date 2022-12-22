{*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{if count($currencies) > 1}
    <div id="currencies-block-top" class="currencies-block-wrap nav-main-item-right hidden-xs pull-right">
        <div class="dropdown">
            <button class="btn dropdown-toggle" type="button" data-toggle="dropdown">
                {foreach from=$currencies key=k item=f_currency}
                    {if $cookie->id_currency == $f_currency.id_currency}{$f_currency.iso_code}{/if}
                {/foreach}
                <span class="caret"></span>
            </button>

            <ul class="dropdown-menu">
                {foreach from=$currencies key=k item=f_currency}
                    {if strpos($f_currency.name, '('|cat:$f_currency.iso_code:')') === false}
                        {assign var="currency_name" value={l s='%s (%s)' sprintf=[$f_currency.name, $f_currency.iso_code]}}
                    {else}
                        {assign var="currency_name" value=$f_currency.name}
                    {/if}
                    <li {if $cookie->id_currency == $f_currency.id_currency}class="disabled"{/if}>
                        <a href="javascript:setCurrency({$f_currency.id_currency});" title="{$currency_name}">
                            {$currency_name}
                        </a>
                    </li>
                {/foreach}
            </ul>
        </div>
    </div>
{/if}
