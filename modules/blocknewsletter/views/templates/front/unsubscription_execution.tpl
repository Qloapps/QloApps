{*
* Copyright since 2010 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright since 2010 Webkul IN
*  @license   https://store.webkul.com/license.html
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
