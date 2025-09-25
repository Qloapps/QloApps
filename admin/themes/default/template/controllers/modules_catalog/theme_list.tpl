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

<div class="suggested-elements row">
    {foreach $elements as $element}
        <div class="col-sm-6 col-md-6 element-panel">
            <div class="panel">
                <div class="theme-info-wrapper">
                    <div class="theme-logo clearfix">
                        <img src="{if isset($element->image)}{$element->image}{else}{$modules_uri}/{$element->name}/{$element->logo}{/if}" title="{$element->displayName}">

                    </div>
                    <h4 class="name" data-name="{$element->name}">{$element->displayName}</h4>
                    <p class="text-muted">{$element->version} {l s='By'} {$element->author}</p>
                    <p>
                        {if $element->description_full}{$element->description_full|truncate:180:"..."}{else}{$element->description|truncate:180:"..."}{/if}
                    </p>
                    <div class="panel-action clearfix">
                        {if isset($element->type) && $element->type == 'addonsMustHave'}
                            <span class="theme-price">{if isset($element->price)}{if $element->price|floatval == 0}{l s='Free'}{elseif isset($element->id_currency)}{displayPrice price=$element->price currency=$element->id_currency}{/if}{/if}</span>
                            <a class="btn button-action pull-right btn-primary _blank" href="{$element->addons_buy_url|replace:' ':'+'|escape:'html':'UTF-8'}">{l s='Explore'}</a>
                        {else}
                            <a class="btn button-action pull-right btn-primary" href="{$element->options.install_url|escape:'html':'UTF-8'}" title="{l s='Install'}">{l s='Install'}</a>
                        {/if}
                    </div>
                </div>
            </div>
        </div>
    {/foreach}
</div>