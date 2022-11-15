{**
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
                    <li><a href="{$link->getPageLink('history', true)|escape:'html'}" title="{l s='View my orders' mod='blockuserinfo'}">{l s='Orders'}</a></li>
                    <li><a href="{$link->getPageLink('index', true, NULL, "mylogout")|escape:'html'}"  title="{l s='Log me out' mod='blockuserinfo'}">{l s='Logout'}</a></li>
                </ul>
            </li>
        {else}
            <a class="header-top-link" href="{$link->getPageLink('my-account', true)|escape:'html'}" rel="nofollow" title="{l s='Log in to your customer account' mod='blockuserinfo'}">
                <span class="hide_xs">{l s='Sign in' mod='blockuserinfo'}</span>
                <span class="visi_xs"><i class="icon-user"></i></span>
            </a>
        </ul>
    {/if}
{if !isset($ajaxCustomerLogin)}
    </div>
{/if}
