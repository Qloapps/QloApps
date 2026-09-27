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
<div class="col-sm-12">
    <section id="ghc-dashboard-notification" class="widget panel">
        <h3>
            <i class="icon-{if $is_connected && empty($hotel_names)}check-circle text-success{else}warning-sign text-warning{/if}"></i>
            {l s='Google Hotel Connector' mod='qlogooglehotelconnector'}
        </h3>

        {if !$is_connected}
            <p class="qlo-ghc-notif-desc">
                {l s='Not connected to the Google Hotel Center.' mod='qlogooglehotelconnector'}
                &nbsp;<a href="{$config_url|escape:'html':'UTF-8'}">{l s='Fix in Google Hotel Setup' mod='qlogooglehotelconnector'}</a>
            </p>
        {elseif !empty($hotel_names)}
            <p class="qlo-ghc-notif-desc">
                {l s='Connected to the Google Hotel Center, but some hotels are not listed.' mod='qlogooglehotelconnector'}
            </p>
        {else}
            <p class="qlo-ghc-notif-desc">
                {l s='Connected to the Google Hotel Center.' mod='qlogooglehotelconnector'}
            </p>
        {/if}

        {if !empty($hotel_names)}
            <p class="qlo-ghc-notif-title">
                <strong>{l s='Hotels with an issue:' mod='qlogooglehotelconnector'}</strong>
                &nbsp;<span class="label label-danger qlo-ghc-badge-urgent">{l s='Urgent Action Needed' mod='qlogooglehotelconnector'}</span>
            </p>
            <ul class="list-unstyled qlo-ghc-notif-list">
                {foreach from=$hotel_names item=hotelName}
                <li class="qlo-ghc-notif-item">
                    <i class="icon-circle text-danger qlo-ghc-notif-dot"></i>
                    &nbsp;<strong>{$hotelName|escape:'html':'UTF-8'}</strong>
                </li>
                {/foreach}
            </ul>
            <p class="qlo-ghc-notif-desc">
                <a href="{$hotel_list_url|escape:'html':'UTF-8'}">{l s='View Hotel List' mod='qlogooglehotelconnector'}</a>
            </p>
        {/if}

        <a href="{$config_url|escape:'html':'UTF-8'}" class="btn btn-default btn-sm btn-block">
            <i class="icon-cog"></i>
            {l s='Google Hotel Setup' mod='qlogooglehotelconnector'}
        </a>
    </section>
</div>
