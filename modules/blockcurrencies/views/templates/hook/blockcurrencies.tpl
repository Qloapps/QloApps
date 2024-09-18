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

{block name='block_currencies'}
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
{/block}