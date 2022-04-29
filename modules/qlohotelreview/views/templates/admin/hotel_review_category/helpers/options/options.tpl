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