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

<div class="suggested-elements row">
    {foreach $elements as $element}
        <div class="col-xs-12 col-sm-6 col-md-4 element-panel">
            <div class="panel">
                <div class="module-info-wrapper">
                    <div class="module-logo">
                        <img src="{if isset($element->image)}{$element->image}{else}{$modules_uri}/{$element->name}/{$element->logo}{/if}" title="{$element->displayName}">
                        <div>
                            <h4 class="name" data-name="{$element->name}">{$element->displayName}</h4>
                            <p class="text-muted">{$element->version} {l s='By'} {$element->author}</p>
                        </div>
                    </div>
                    <p>
                        {if $element->description_full}
                            {$element->description_full|truncate:90:"..."}
                        {else}
                            {$element->description|truncate:90:"..."}
                        {/if}
                    </p>
                    <div class="panel-action clearfix">
                        <span class="module-price">{if isset($element->price)}{if $element->price|floatval == 0}{l s='Free'}{else}{$element->price_formatted}{/if}{else}{l s='Free'}{/if}</span>
                        {if isset($element->type) && $element->type == 'addonsMustHave' && !$element->not_on_disk}&nbsp;<span class="label label-primary">{l s='In Module Directory'}</span>{/if}
                        {if isset($element->type) && $element->type == 'addonsMustHave' && $element->not_on_disk}
                            <a class="btn button-action pull-right btn-primary _blank" href="{$element->addons_buy_url|replace:' ':'+'|escape:'html':'UTF-8'}">{l s='Explore'}</a>
                        {else}
                            <a class="btn button-action pull-right btn-primary{if !$element->trusted} untrustedaddon{/if}" {if !$element->trusted} data-target="#moduleNotTrusted"  data-toggle="modal"{/if} data-link="{$element->options.install_url|escape:'html':'UTF-8'}" data-module-display-name="{$element->displayName|escape:'html':'UTF-8'}" data-module-name="{$element->name|escape:'html':'UTF-8'}" data-module-image="{if isset($element->image)}{$element->image}{else}{$modules_uri}/{$element->name}/{$element->logo}{/if}" data-author-name="{$element->author|escape:'html':'UTF-8'}" data-author-uri="{if isset($element->author_uri)}{$element->author_uri|escape:'html':'UTF-8'}{/if}" href="{if !$element->trusted}#{else}{$element->options.install_url|escape:'html':'UTF-8'}{/if}" title="{l s='Install'}">{l s='Install'}</a>
                        {/if}
                    </div>
                </div>
            </div>
        </div>
    {/foreach}
</div>