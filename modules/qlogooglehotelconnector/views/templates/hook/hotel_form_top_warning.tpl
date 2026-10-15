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
<div id="ghc-hotel-top-warning">
    <div class="alert alert-warning">
        {if !$is_connected}
            <strong>{l s='Connect to Google Hotel to get more commission-free direct bookings.' mod='qlogooglehotelconnector'}</strong>
            &nbsp;<a href="{$hotel_edit_config_url|escape:'html':'UTF-8'}" class="alert-link">{l s='Connect in Google Hotel Setup' mod='qlogooglehotelconnector'}</a>
        {/if}

        {if !empty($issue_reasons)}
            <div class="qlo-ghc-warning-title">{l s='Need to fix the below mentioned issues:' mod='qlogooglehotelconnector'}</div>
            <ul class="qlo-ghc-missing-list">
                {foreach from=$issue_reasons item=reason}
                <li>{$reason|escape:'html':'UTF-8'}</li>
                {/foreach}
            </ul>
        {/if}
    </div>
</div>
