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
            {l s='To create new Additiona facilities '} <a target="_blank" href="{$link->getAdminLink('AdminRoomTypeGlobalDemand')}">{l s='click here.'}</a>
        </div>
        {if isset($allDemands) && $allDemands}
            <div class="panel-group">
                {foreach $allDemands as $key => $demand}
                    <div class="panel panel-sm">
                        <input class="selected_demand" type="checkbox" name="selected_demand[]" value="{$demand['id_global_demand']|escape:'html':'UTF-8'}" {if isset($roomDemandPrices[$demand['id_global_demand']])}checked{/if} />
                        <a href="#" onclick="$('#panel-demands-{$key|escape:'html':'UTF-8'}').slideToggle(250); return false;">
                            {$demand['name']|escape:'html':'UTF-8'}
                        </a>
                        <div class="panel-body collapse" id="panel-demands-{$key|escape:'html':'UTF-8'}">
                            {if isset($demand['adv_option']) && count($demand['adv_option'])}
                                <input type="hidden" name="demand_price_{$demand['id_global_demand']|escape:'html':'UTF-8'}"
                                value="{if isset($roomDemandPrices[$demand['id_global_demand']]['price'])}{$roomDemandPrices[$demand['id_global_demand']]['price']|escape:'html':'UTF-8'}{elseif isset($demand['price'])}{$demand['price']|escape:'html':'UTF-8'}{/if}"/>
                            {else}
                                <div class="form-group">
                                    <label class="col-sm-3 control-label required" >
                                        {l s='Price'}({l s='tax excl.'})
                                    </label>
                                    <div class="col-sm-3">
                                        <div class="input-group">
                                            <span class="input-group-addon">{$defaultcurrencySign|escape:'html':'UTF-8'}</span>
                                            <input type="text" name="demand_price_{$demand['id_global_demand']|escape:'html':'UTF-8'}"
                                            value="{if isset($roomDemandPrices[$demand['id_global_demand']]['price'])}{$roomDemandPrices[$demand['id_global_demand']]['price']|escape:'html':'UTF-8'}{elseif isset($demand['price'])}{$demand['price']|escape:'html':'UTF-8'}{/if}"/>
                                        </div>
                                    </div>
                                </div>
                            {/if}
                            {if isset($demand['adv_option']) && $demand['adv_option']}
                                <div class="adv_options_dtl form-group">
                                    <label class="col-sm-3 control-label">
                                        {l s='Advance options'}
                                    </label>
                                    <div class="col-sm-9">
                                        <div class="table-responsive-row clearfix">
                                            <table class="table table-bordered adv_option_table">
                                                <thead>
                                                    <tr>
                                                        <th>
                                                            <span>{l s='Option Name'}</span>
                                                        </th>
                                                        <th>
                                                            <span>{l s='Price'}</span>
                                                        </th>
                                                    </tr>
                                                </thead>
                                                {foreach from=$demand['adv_option'] key=key item=info}
                                                    <tr>
                                                        <td>
                                                            {$info['name']|escape:'html':'UTF-8'}
                                                        </td>
                                                        <td>
                                                            <div class="input-group">
                                                                <span class="input-group-addon">{$defaultcurrencySign|escape:'html':'UTF-8'}</span>
                                                                <input type="text" name="option_price_{$info['id']|escape:'html':'UTF-8'}" value="{if isset($roomDemandPrices[$demand['id_global_demand']]['adv_option'][$info['id']]['price'])}{$roomDemandPrices[$demand['id_global_demand']]['adv_option'][$info['id']]['price']|escape:'html':'UTF-8'}{else}{$info['price']|escape:'html':'UTF-8'}{/if}"/>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                {/foreach}
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            {/if}
                        </div>
                    </div>
                {/foreach}
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
