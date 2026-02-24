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

{block name='my_account'}
    {capture name=path}{l s='My account'}{/capture}
    <div class="container">
        {block name='my_account_heading'}
            <h1 class="page-heading text-center text-bolder h5 font-weight-bold">{l s='My account'}</h1>
            <hr>
            {if isset($account_created)}
                <div class="text-center small">
                    {l s='Your account has been created.'}
                </div>
            {/if}
            <div class="text-center small">{l s='Welcome to your account. Here you can manage all of your personal information and orders.'}</div>
        {/block}
        <div class="row m-4">
            <div class="col-12">
                <ul class="myaccount-link-list qlo-link-list">
                    {block name='my_account_tabs'}
                        <li class="qlo-item">
                            <a class="qlo-action-card" href="{$link->getPageLink('address', true)|escape:'html':'UTF-8'}" title="{l s='Addresses'}">
                                <span class="qlo-action-card__icon" aria-hidden="true"><i class="icon-map-marker"></i></span>
                                <span class="qlo-action-card__title">{l s='Addresses'}</span>
                            </a>
                        </li>
                        <li class="qlo-item">
                            <a class="qlo-action-card" href="{$link->getPageLink('history', true)|escape:'html':'UTF-8'}" title="{l s='Order history and details'}">
                                <span class="qlo-action-card__icon" aria-hidden="true"><i class="icon-list-alt"></i></span>
                                <span class="qlo-action-card__title">{l s='Order history and details'}</span>
                            </a>
                        </li>
                        {if $refundAllowed}
                            <li class="qlo-item">
                                <a class="qlo-action-card" href="{$link->getPageLink('order-follow', true)|escape:'html':'UTF-8'}" title="{l s='Booking refund requests'}">
                                    <span class="qlo-action-card__icon" aria-hidden="true"><i class="icon-money"></i></span>
                                    <span class="qlo-action-card__title">{l s='Booking refund requests'}</span>
                                </a>
                            </li>
                        {/if}
                        <li class="qlo-item">
                            <a class="qlo-action-card" href="{$link->getPageLink('identity', true)|escape:'html':'UTF-8'}" title="{l s='Personal information'}">
                                <span class="qlo-action-card__icon" aria-hidden="true"><i class="icon-user"></i></span>
                                <span class="qlo-action-card__title">{l s='Personal information'}</span>
                            </a>
                        </li>
                        <li class="qlo-item">
                            <a class="qlo-action-card" href="{$link->getPageLink('order-slip', true)|escape:'html':'UTF-8'}" title="{l s='Credit slips'}">
                                <span class="qlo-action-card__icon" aria-hidden="true"><i class="icon-file-text"></i></span>
                                <span class="qlo-action-card__title">{l s='Credit slips'}</span>
                            </a>
                        </li>
                    {/block}
                    {block name='displayCustomerAccount'}
                        {if $voucherAllowed}
                            <li class="qlo-item">
                                <a class="qlo-action-card" href="{$link->getPageLink('discount', true)|escape:'html':'UTF-8'}" title="{l s='Vouchers'}">
                                    <span class="qlo-action-card__icon" aria-hidden="true"><i class="icon-ticket"></i></span>
                                    <span class="qlo-action-card__title">{l s='Vouchers'}</span>
                                </a>
                            </li>
                        {/if}
                        {if isset($HOOK_CUSTOMER_ACCOUNT) && $HOOK_CUSTOMER_ACCOUNT !=''}
                            {$HOOK_CUSTOMER_ACCOUNT}
                        {/if}
                    {/block}
                </ul>
            </div>
        </div>

        {block name='displayCustomerAccountAfterTabs'}
            {hook h='displayCustomerAccountAfterTabs'}
        {/block}

        {block name='my_account_footer_links'}
            <ul class="footer_links clearfix">
            <li><a class="link text-secondary" href="{if isset($force_ssl) && $force_ssl}{$base_dir_ssl}{else}{$base_dir}{/if}" title="{l s='Home'}"><span><i class="icon-angles-left"></i> {l s='Home'}</span></a></li>
            </ul>
        {/block}
    </div>
{/block}
