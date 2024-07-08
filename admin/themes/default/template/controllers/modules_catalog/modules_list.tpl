{*
* 2010-2022 Webkul.
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
*  @copyright 2010-2022 Webkul IN
*  @license   https://store.webkul.com/license.html
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
                        {if isset($element->type) && $element->type == 'addonsMustHave' && $element->not_on_disk}
                            <span class="module-price">{if isset($element->price)}{if $element->price|floatval == 0}{l s='Free'}{elseif isset($element->id_currency)}{displayPrice price=$element->price currency=$element->id_currency}{/if}{/if}</span>
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