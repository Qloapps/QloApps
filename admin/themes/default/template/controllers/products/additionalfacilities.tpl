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

 {if isset($product->id) && $product->id}
    <input type="hidden" name="submitted_tabs[]" value="AdditionalFacilities" />
    <div class="panel">
        <div class="panel-heading">
            <i class="icon-user"></i> {l s='Room Type Additional Facilities'}
        </div>
        <div class="alert alert-info">
            {l s='To create new Additional facilities'} <a target="_blank" href="{$link->getAdminLink('AdminRoomTypeGlobalDemand')}">{l s='click here.'}</a>
        </div>
        {if isset($allDemands) && $allDemands}

            <div class="from-group table-responsive-row clearfix">
                <table class="table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>
                                {l s='Name'}
                            </th>
                            <th>
                                {l s='Option'}
                            </th>
                            <th>
                                {l s='Price'}
                            </th>
                            <th>
                                {l s='Tax rate'}
                            </th>
                            <th class="fixed-width-lg text-center">
                                {l s='Per day price calculation'}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach $allDemands as $key => $demand}
                            {assign var="rowcount" value=0}
                            {if isset($demand['adv_option']) && $demand['adv_option']}
                                {assign var="rowspan" value=$demand['adv_option']|count}
                                {assign var="adv_option" value=$demand['adv_option']}
                            {else}
                                {assign var="rowspan" value=1}
                                {assign var="adv_option" value=[]}
                            {/if}
                                {foreach $adv_option as $option}
                                    {assign var="rowcount" value=$rowcount + 1}
                                    <tr>
                                        {if $rowcount <= 1}
                                            <td rowspan="{$rowspan}">
                                                <input class="selected_demand" type="checkbox" name="selected_demand[]" value="{$demand['id_global_demand']|escape:'html':'UTF-8'}" {if isset($roomDemandPrices[$demand['id_global_demand']])}checked{/if} />
                                                <input type="hidden" name="demand_price_{$demand['id_global_demand']|escape:'html':'UTF-8'}" value="{if isset($roomDemandPrices[$demand['id_global_demand']]['price'])}{$roomDemandPrices[$demand['id_global_demand']]['price']|escape:'html':'UTF-8'}{elseif isset($demand['price'])}{$demand['price']|escape:'html':'UTF-8'}{/if}"/>
                                            </td>
                                            <td rowspan="{$rowspan}">
                                                <a target="blank" href="{$link->getAdminLink('AdminRoomTypeGlobalDemand')|escape:'html':'UTF-8'}&amp;id_global_demand={$demand['id_global_demand']|escape:'html':'UTF-8'}&amp;updatehtl_room_type_global_demand"><i class="icon-external-link-sign"></i></a> {$demand['name']|escape:'html':'UTF-8'}
                                            </td>
                                        {/if}
                                        <td>
                                            {$option['name']|escape:'html':'UTF-8'}
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                <span class="input-group-addon">{$defaultcurrencySign|escape:'html':'UTF-8'}</span>
                                                <input type="text" name="option_price_{$option['id']|escape:'html':'UTF-8'}" value="{if isset($roomDemandPrices[$demand['id_global_demand']]['adv_option'][$option['id']]['price'])}{$roomDemandPrices[$demand['id_global_demand']]['adv_option'][$option['id']]['price']|escape:'html':'UTF-8'}{else}{$option['price']|escape:'html':'UTF-8'}{/if}"/>
                                            </div>
                                        </td>
                                        {if $rowcount == 1}
                                            <td rowspan="{$rowspan}">
                                                {$demand['default_tax_rules_group_name']}
                                            </td>
                                            <td class="text-center" rowspan="{$rowspan}">
                                                {if $demand['price_calc_method'] == 1}
                                                    <span class="badge badge-success">{l s='Yes'}</span>
                                                {else}
                                                    <span>{l s='No'}</span>
                                                {/if}
                                            </td>
                                        {/if}
                                    </tr>
                                {foreachelse}
                                    <tr>
                                        <td>
                                            <input class="selected_demand" type="checkbox" name="selected_demand[]" value="{$demand['id_global_demand']|escape:'html':'UTF-8'}" {if isset($roomDemandPrices[$demand['id_global_demand']])}checked{/if} />
                                        </td>
                                        <td>
                                            <a target="blank" href="{$link->getAdminLink('AdminRoomTypeGlobalDemand')|escape:'html':'UTF-8'}&amp;id_global_demand={$demand['id_global_demand']|escape:'html':'UTF-8'}&amp;updatehtl_room_type_global_demand"><i class="icon-external-link-sign"></i></a> {$demand['name']|escape:'html':'UTF-8'}
                                        </td>

                                        <td></td>
                                        <td>
                                            <div class="input-group">
                                                <span class="input-group-addon">{$defaultcurrencySign|escape:'html':'UTF-8'}</span>
                                                <input type="text" name="demand_price_{$demand['id_global_demand']|escape:'html':'UTF-8'}"
                                                value="{if isset($roomDemandPrices[$demand['id_global_demand']]['price'])}{$roomDemandPrices[$demand['id_global_demand']]['price']|escape:'html':'UTF-8'}{elseif isset($demand['price'])}{$demand['price']|escape:'html':'UTF-8'}{/if}"/>
                                            </div>
                                        </td>
                                        <td>
                                            {$demand['default_tax_rules_group_name']}
                                        </td>
                                        <td class="text-center">
                                            {if $demand['price_calc_method'] == 1}
                                                <span class="badge badge-success">{l s='Yes'}</span>
                                            {else}
                                                <span>{l s='No'}</span>
                                            {/if}
                                        </td>
                                    </tr>
                                {/foreach}
                        {/foreach}
                    </tbody>
                </table>
            </div>
            <div class="panel-footer">
                <a href="{$link->getAdminLink('AdminProducts')|escape:'html':'UTF-8'}{if isset($smarty.request.page) && $smarty.request.page > 1}&amp;submitFilterproduct={$smarty.request.page|intval}{/if}" class="btn btn-default">
                    <i class="process-icon-cancel"></i>
                    {l s='Cancel'}
                </a>
                <button type="submit" name="submitAddproduct" class="btn btn-default pull-right" disabled="disabled">
                    <i class="process-icon-loading"></i>
                    {l s='Save'}
                </button>
                <button type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right"  disabled="disabled">
                    <i class="process-icon-loading"></i>
                        {l s='Save and stay'}
                </button>
            </div>
        {else}
            <div class="alert alert-warning">
                {l s='No additional facilities created yet. To create please visit'} <a target="_blank" href="{$link->getAdminLink('AdminRoomTypeGlobalDemand')}">{l s='Additional facilities'}</a> {l s='page'}.
            </div>
        {/if}
    </div>
{/if}