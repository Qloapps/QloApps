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

{block name='user_navigation'}
    {if !isset($ajaxCustomerLogin)}
        <div class="header-top-item header_user_info hidden-xs">
    {/if}
        {if $logged}
            <ul class="navbar-nav hidden-xs">
                <li class="dropdown">
                    <button class="btn dropdown-toggle" type="button" id="user_info_acc" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        <span class="account_user_name hide_xs">{$cookie->customer_firstname}</span>
                        <span class="account_user_name visi_xs"><i class="icon-user"></i></span>
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="user_info_acc">
                        <li><a href="{$link->getPageLink('my-account', true)|escape:'html'}" title="{l s='View my customer account' mod='blockuserinfo'}">{l s='Accounts'  mod='blockuserinfo'}</a></li>
                        <li><a href="{$link->getPageLink('history', true)|escape:'html'}" title="{l s='Bookings' mod='blockuserinfo'}">{l s='Bookings' mod='blockuserinfo'}</a></li>
                        <li><a href="{$link->getPageLink('index', true, NULL, "mylogout=1&token={$static_token}")|escape:'html':'UTF-8'}"  title="{l s='Log me out' mod='blockuserinfo'}">{l s='Logout' mod='blockuserinfo'}</a></li>
                        {block name='displayUserNavigationList'}
                            {hook h='displayUserNavigationList'}
                        {/block}
                    </ul>
                </li>
            </ul>
        {else}
            <a class="header-top-link" href="{$link->getPageLink('my-account', true)|escape:'html'}" rel="nofollow" title="{l s='Log in to your customer account' mod='blockuserinfo'}">
                <span class="hide_xs">{l s='Sign in' mod='blockuserinfo'}</span>
                <span class="visi_xs"><i class="icon-user"></i></span>
            </a>
        {/if}
    {if !isset($ajaxCustomerLogin)}
        </div>
    {/if}
{/block}
