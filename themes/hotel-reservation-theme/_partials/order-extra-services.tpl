{**
* 2010-2023 Webkul.
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
* @copyright 2010-2023 Webkul IN
* @license LICENSE.txt
*}

{block name='order_extra_services'}
    <div class="card">
        <div class="card-header">
            {l s='Extra Services'}
        </div>
        <div class="card-body">
            {if (isset($extraDemands) && $extraDemands) || (isset($additionalServices) && $additionalServices)}
                {block name='order_extra_services_tabs'}
                    <ul class="nav nav-tabs">
                        {if isset($additionalServices) && $additionalServices}
                            <li class="active"><a href="#room_type_service_product_desc" data-toggle="tab">{l s='Services'}</a></li>
                        {/if}
                        {if isset($extraDemands) && $extraDemands}
                            <li {if !isset($additionalServices) || !$additionalServices} class="active" {/if}><a href="#room_type_demands_desc" data-toggle="tab">{l s='Facilities'}</a></li>
                        {/if}
                    </ul>
                {/block}
                {block name='order_extra_services_tabs_content'}
                    <div class="tab-content">
                        {block name='order_extra_services_tab_content'}
                            {if isset($additionalServices) && $additionalServices}
                                <div id="room_type_service_product_desc" class="tab-pane {if isset($additionalServices) && $additionalServices}active{/if}">
                                    {assign var=roomCount value=1}
                                    {foreach $additionalServices as $key => $roomAdditionalService}
                                        <div class="room_demands">
                                            <div class="demand_header">
                                                {l s='Room'} {$roomCount|string_format:'%02d'}&nbsp;
                                                <span>({if {$roomAdditionalService['adults']} <= 9}0{$roomAdditionalService['adults']}{else}{$roomAdditionalService['adults']}{/if} {if $roomAdditionalService['adults'] > 1}{l s='Adults'}{else}{l s='Adult'}{/if}{if $roomAdditionalService['children'] > 0}, {if {$roomAdditionalService['children']} <= 9}0{$roomAdditionalService['children']}{else}{$roomAdditionalService['children']}{/if} {if $roomAdditionalService['children'] > 1}{l s='Children'}{else}{l s='Child'}{/if}{/if})</span>
                                            </div>
                                            <div class="room_demand_detail">
                                                {foreach $roomAdditionalService['additional_services'] as $additionalService}
                                                    <div class="room_demand_block">
                                                        <div class="">
                                                            <div class="">
                                                                {$additionalService['name']|escape:'html':'UTF-8'}
                                                                {if $additionalService['allow_multiple_quantity']}
                                                                    <span class="quantity">{l s='(Quantity: %s)' sprintf=[$additionalService['quantity']|string_format:'%02d']}</span>
                                                                {/if}
                                                            </div>
                                                        </div>
                                                        <div class="">
                                                            <span>
                                                                {if $useTax}
                                                                    {displayPrice price=$additionalService['total_price_tax_incl']|escape:'html':'UTF-8' currency=$objOrder->id_currency}
                                                                {else}
                                                                    {displayPrice price=$additionalService['total_price_tax_excl']|escape:'html':'UTF-8' currency=$objOrder->id_currency}
                                                                {/if}
                                                            </span>
                                                        </div>
                                                    </div>
                                                {/foreach}
                                            </div>
                                        </div>
                                        {assign var=roomCount value=$roomCount+1}
                                    {/foreach}
                                </div>
                            {/if}
                        {/block}

                        {block name='order_extra_demands_tab_content'}
                            {if isset($extraDemands) && $extraDemands}
                                <div id="room_type_demands_desc" class="tab-pane {if !isset($additionalServices) || !$additionalServices}active{/if}">
                                    {assign var=roomCount value=1}
                                    {foreach $extraDemands as $roomDemand}
                                        <div class="room_demands">
                                            <div class="demand_header">
                                                {l s='Room'} {$roomCount|string_format:'%02d'}&nbsp;
                                                <span>({if {$roomDemand['adults']} <= 9}0{$roomDemand['adults']}{else}{$roomDemand['adults']}{/if} {if $roomDemand['adults'] > 1}{l s='Adults'}{else}{l s='Adult'}{/if}{if $roomDemand['children'] > 0}, {if {$roomDemand['children']} <= 9}0{$roomDemand['children']}{else}{$roomDemand['children']}{/if} {if $roomDemand['children'] > 1}{l s='Children'}{else}{l s='Child'}{/if}{/if})</span>
                                            </div>
                                            <div class="room_demand_detail">
                                                {foreach $roomDemand['extra_demands'] as $demand}
                                                    <div class="room_demand_block">
                                                        <div class="">{$demand['name']|escape:'html':'UTF-8'}</div>
                                                        <div class="">
                                                            <span>
                                                                {if $useTax}
                                                                    {displayPrice price="{$demand['total_price_tax_incl']|escape:'html':'UTF-8'}" currency=$objOrder->id_currency}
                                                                {else}
                                                                    {displayPrice price="{$demand['total_price_tax_excl']|escape:'html':'UTF-8'}" currency=$objOrder->id_currency}
                                                                {/if}
                                                            </span>
                                                        </div>
                                                    </div>
                                                {/foreach}
                                            </div>
                                        </div>
                                        {assign var=roomCount value=$roomCount+1}
                                    {/foreach}
                                    </div>
                                </div>
                            {/if}
                        {/block}
                    </div>
                {/block}
            {/if}
        </div>
    </div>
{/block}
