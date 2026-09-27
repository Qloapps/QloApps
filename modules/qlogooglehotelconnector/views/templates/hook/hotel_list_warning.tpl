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
{if !$is_connected}
    <div class="alert alert-danger">
        <strong>{l s='Not connected to the Google Hotel Center.' mod='qlogooglehotelconnector'}</strong>
        {l s='Hotels cannot sync with Google Hotel Ads until this is fixed.' mod='qlogooglehotelconnector'}
        &nbsp;<a href="{$config_url|escape:'html':'UTF-8'}" class="alert-link">{l s='Fix in Google Hotel Setup' mod='qlogooglehotelconnector'}</a>
    </div>
{/if}

{if !empty($hotels_with_issues)}
    <div class="alert alert-warning">
        <strong>{l s='The following hotels are not listed on Google Hotel:' mod='qlogooglehotelconnector'}</strong>
        <ul class="qlo-ghc-missing-list">
            {foreach from=$hotels_with_issues item=hotel}
            <li><a href="{$hotel.edit_url|escape:'html':'UTF-8'}">{$hotel.hotel_name|escape:'html':'UTF-8'}</a></li>
            {/foreach}
        </ul>
    </div>
{/if}
