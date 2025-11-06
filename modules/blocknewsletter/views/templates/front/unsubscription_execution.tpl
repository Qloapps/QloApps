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

{include file="$tpl_dir./errors.tpl"}

{if !count($errors)}
    <p class="alert alert-success">
        {l s='You have unsubscribed successfully.' mod='blocknewsletter'}
    </p>

    <p class="msg-redirect">
        {l s='You will be redirected to home page in ' mod='blocknewsletter'}
        <span class="countdown-seconds">{l s='5' mod='blocknewsletter'}</span>
        {l s='seconds.' mod='blocknewsletter'}
    </p>

    <a href="{$link->getPageLink('index')}" class="btn btn-primary btn-homepage">
        <span>{l s='Home Page' mod='blocknewsletter'}</span>
    </a>
{/if}
