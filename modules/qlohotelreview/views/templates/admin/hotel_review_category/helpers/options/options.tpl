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

{extends file="helpers/options/options.tpl"}

{block name="input"}
    {if $field["type"] == "text" && isset($field["prefix"])}
        <div class="col-lg-9">
            <div class="input-group {if isset($field.class)}{$field.class}{/if}">
                <span class="input-group-addon">{$field["prefix"]|strval}</span>
                <input class="form-control {if isset($field["class"])}{$field["class"]}{/if}"
                    type="{$field["type"]}"
                    {if isset($field["id"])} id="{$field["id"]}"{/if}
                    size="{if isset($field["size"])}{$field["size"]|intval}{else}5{/if}"
                    name="{$key}"
                    value="{if isset($field["no_escape"]) && $field["no_escape"]}{$field["value"]|escape:"UTF-8"}{else}{$field["value"]|escape:"html":"UTF-8"}{/if}"
                    {if isset($field["autocomplete"]) && !$field["autocomplete"]}autocomplete="off"{/if}/>
                    {if isset($field["suffix"])}
                        <span class="input-group-addon">{$field["suffix"]|strval}</span>
                    {/if}
            </div>
        </div>
        {if isset($field["desc"]) && !empty($field["desc"])}
            <div class="col-lg-9 col-lg-offset-3">
                <div class="help-block">
                    {if is_array($field["desc"])}
                        {foreach $field["desc"] as $p}
                            {if is_array($p)}
                                <span id="{$p.id}">{$p.text}</span><br />
                            {else}
                                {$p}<br />
                            {/if}
                        {/foreach}
                    {else}
                        {$field["desc"]}
                    {/if}
                </div>
            </div>
        {/if}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}