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

<div id="room_type_service_product_desc" class="tab-pane{if !isset($selectedRoomDemands) || !$selectedRoomDemands} active{/if}">
    {if $customServiceAllowed}
        <div class="row">
            <button id="btn_new_room_service" class="btn btn-success pull-right"><i class="icon-plus-circle"></i> {l s='Add a new service'}</button>
            <button id="back_to_service_btn" class="btn btn-default"><i class="icon-arrow-left"></i> {l s='Back'}</button>
        </div>
        <hr>
    {/if}
    {if isset($serviceProducts) && $serviceProducts}
        <div id="room_type_services_desc">
            {assign var=roomCount value=1}
            <div class="row room_demands_container">
                <div class="col-sm-12 room_demand_detail">
                    {if isset($serviceProducts) && $serviceProducts}
                        <form id="update_selected_room_services_form">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>{l s='Name'}</th>
                                        <th class="fixed-width-sm"></th>
                                        <th class="fixed-width-sm">{l s='Quantity'}</th>
                                        <th>{l s='Unit Price (tax excl.)'}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {foreach $serviceProducts as $product}
                                        {if isset($selectedRoomServiceProduct['selected_service']) && $selectedRoomServiceProduct['selected_service'] && ($product['id_product']|array_key_exists:$selectedRoomServiceProduct['selected_service'])}
                                            {assign var='serviceSelected' value=true}
                                            {$product.price_tax_incl = $selectedRoomServiceProduct['selected_service'][$product['id_product']]['unit_price_tax_incl']}
                                            {$product.price_tax_exc = $selectedRoomServiceProduct['selected_service'][$product['id_product']]['unit_price_tax_excl']}
                                        {else}
                                            {assign var='serviceSelected' value=false}
                                        {/if}
                                        <tr class="room_demand_block">
                                            <td>
                                                <input data-id_cart_booking="{$selectedRoomServiceProduct['id']}" value="{$product['id_product']|escape:'html':'UTF-8'}" type="checkbox" class="change_room_type_service_product" {if $serviceSelected}checked{/if}/>

                                                <input id="selected_service_product_{$product['id_product']}" type="hidden" value="{if $serviceSelected}1{else}0{/if}" name="selected_service_product[{$product.id_product}]"/>
                                            </td>
                                            <td>
                                                <p>{$product['name']|escape:'html':'UTF-8'}</p>
                                            </td>
                                            <td>
                                                {if $product['auto_add_to_cart'] && $product['price_addition_type'] == Product::PRICE_ADDITION_TYPE_INDEPENDENT}
                                                    <span class="badge badge-info label">{l s='Convenience fee'}</span>
                                                {/if}
                                                {if $product['auto_add_to_cart'] && $product['price_addition_type'] == Product::PRICE_ADDITION_TYPE_WITH_ROOM}
                                                    <span class="badge badge-info label">{l s='Auto added'}</span>
                                                {/if}
                                            </td>
                                            <td>
                                                {if $product.allow_multiple_quantity}
                                                    <div class="qty_container">
                                                        <input type="number" class="form-control room_type_service_product_qty qty" id="qty_{$product.id_product}" name="service_qty[{$product.id_product}]" data-id-product="{$product.id_product}" min="1" data-max-quantity="{$product.max_quantity}" value="{if $serviceSelected}{$selectedRoomServiceProduct['selected_service'][$product['id_product']]['quantity']}{else}1{/if}" name="service_qty[{$product['id_product']|escape:'html':'UTF-8'}]">

                                                        <p style="display:{if $serviceSelected && $selectedRoomServiceProduct['selected_service'][$product['id_product']]['quantity'] > $product.max_quantity}block{else}none{/if}; margin-top: 4px;">
                                                            <span class="label label-warning">{l s='Maximum allowed quantity: %s' sprintf=$product.max_quantity}</span>
                                                        </p>
                                                    </div>
                                                {else}
                                                    --
                                                {/if}
                                            </td>
                                            <td>
                                                {if ($product.show_price && !isset($restricted_country_mode)) || isset($groups)}
                                                    <div id="service_cart_price_{$selectedRoomServiceProduct['id']}_{$product['id_product']}_input" class="input-group">
                                                        <span class="input-group-addon">
                                                            {$cartCurrency->sign}
                                                        </span>
                                                        <input class="service_cart_price_input" id="service_cart_price_{$selectedRoomServiceProduct['id']}_{$product['id_product']}" type="text" value="{$product.price_tax_exc}" name="service_price[{$product['id_product']|escape:'html':'UTF-8'}]"/>
                                                        {if Product::PRICE_CALCULATION_METHOD_PER_DAY == $product['price_calculation_method']}
                                                            <span class="input-group-addon">{l s='/ night'}</span>
                                                        {/if}
                                                    </div>
                                                {/if}
                                            </td>
                                        </tr>
                                    {/foreach}
                                </tbody>
                            </table>
                            <input type="hidden" name="id_hotel_cart_booking" value="{$id_hotel_cart_booking}">
                            <div class="modal-footer">
                                <button type="submit" id="update_selected_services" class="btn btn-primary"><i class="icon icon-save"></i> &nbsp;{l s="Update Services"}</button>
                            </div>
                        </form>
                    {/if}
                </div>
            </div>
        </div>
    {/if}

    {if $customServiceAllowed}
        <div id="add_new_room_services_block" class="row">
            <form id="add_new_room_services_form" class="col-sm-12 room_services_container">
                <div class="row form-group">
                    <div class="col-sm-6">
                        <label class="control-label required">{l s='Name'}</label>
                        <input type="text" class="form-control" name="new_service_name"/>
                    </div>
                    <div class="col-sm-6">
                        <label class="control-label required">{l s='Price(tax excl.)'}</label>
                        <div class="input-group">
                            <span class="input-group-addon">{$cartCurrency->sign}</span>
                            <input type="text" class="form-control" name="new_service_price"/>
                        </div>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-sm-6">
                        <label class="control-label">{l s='Price calculation method'}</label>
                        <select class="form-control" name="new_service_price_calc_method">
                            <option value="{Product::PRICE_CALCULATION_METHOD_PER_BOOKING}">{l s='Add price once for the booking range'}</option>
                            <option value="{Product::PRICE_CALCULATION_METHOD_PER_DAY}">{l s='Add price for each day of booking'}</option>
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label class="control-label">{l s='Auto added service'}</label>
                        <div>
                            <span class="switch prestashop-switch fixed-width-lg">
                                <input type="radio" name="new_service_auto_added" id="new_service_auto_added_on" value="1"/>
                                <label for="new_service_auto_added_on" class="radioCheck">
                                    {l s='Yes'}
                                </label>
                                <input type="radio" name="new_service_auto_added" id="new_service_auto_added_off" value="0" checked="checked"/>
                                <label for="new_service_auto_added_off" class="radioCheck">
                                    {l s='No'}
                                </label>
                                <a class="slide-button btn"></a>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="row form-group">
                    <div id="new_service_price_tax_rule_container" class="col-sm-6">
                        <label class="control-label">{l s='Tax rule'}</label>
                        <select name="new_service_price_tax_rule_group">
                            <option value="0">{l s='No Tax'}</option>
                            {foreach from=$taxRulesGroups item=taxRuleGroup}
                                <option value="{$taxRuleGroup.id_tax_rules_group}">{$taxRuleGroup.name}</option>
                            {/foreach}
                        </select>
                    </div>
                    <div id="new_service_price_addition_type_container" class="col-sm-6" style="display:none;">
                        <label class="control-label">{l s='Price display preference'}</label>
                        <select name="new_service_price_addition_type" id="new_service_price_addition_type">
                            <option value="{Product::PRICE_ADDITION_TYPE_WITH_ROOM}">{l s='Add price in room price'}</option>
                            <option value="{Product::PRICE_ADDITION_TYPE_INDEPENDENT}">{l s='Add price as convenience Fee'}</option>
                        </select>
                    </div>
                    <div id="new_service_qty_container" class="col-sm-6">
                        <label class="control-label required">{l s='Quantity'}</label>
                        <input type="number" class="form-control qty" min="1" name="new_service_qty" value="1">
                    </div>
                </div>
                <input type="hidden" name="id_hotel_cart_booking" value="{$id_hotel_cart_booking}">
                <div class="row form-group">
                    <div class="col-sm-12 help-block">
                        {l s='Note: If auto added service is enabled, then tax of the booking\'s room type will be applicable.'}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="save_new_service" class="btn btn-primary"><i class="icon icon-save"></i> &nbsp;{l s="Update Service"}</button>
                </div>
            </form>
        </div>
    {/if}
</div>
