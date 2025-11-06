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

<div id="room_type_service_product_desc" class="tab-pane {if isset($show_active) && $show_active}active{/if} extra-services-container">
	{if isset($orderEdit) && $orderEdit}

		<div class="col-sm-12 facility_nav_btn">
            {if $customServiceAllowed}
                <button id="btn_new_room_service" class="btn btn-success pull-right"><i class="icon-plus-circle"></i> {l s='Add a new service'}</button>
            {/if}
			<button id="btn_new_existing_room_service" class="btn btn-success"><i class="icon-plus-circle"></i> {l s='Add existing service'}</button>
			<button id="back_to_service_btn" class="btn btn-default"><i class="icon-arrow-left"></i> {l s='Back'}</button>
            <hr>
		</div>


		{* Already selected room services *}
		<div class="col-sm-12 room_ordered_services table-responsive">
            <form id="update_selected_room_services_form">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>{l s='Name'}</th>
                            <th class="fixed-width-sm"></th>
                            <th class="fixed-width-sm text-center">{l s='Quantity'}</th>
                            <th>{l s='Unit Price (tax excl.)'}</th>
                            <th>{l s='Total Price (tax excl.)'}</th>
                            <th>{l s='Total Price (tax incl.)'}</th>
                            <th class="text-right">{l s='Action'}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {if isset($additionalServices) && $additionalServices}
                            {foreach $additionalServices['additional_services'] as $service}
                                <tr class="room_demand_block" data-id_service_product_order_detail="{$service['id_service_product_order_detail']}">
                                    <td>
                                        <div>{$service['name']|escape:'html':'UTF-8'}</div>
                                        <input value="{$service['id_service_product_order_detail']|escape:'html':'UTF-8'}" name="id_service_product_order_detail[]" type="hidden"/>
                                    </td>
                                    <td>
                                        {if $service['product_auto_add'] && $service['product_price_addition_type'] == Product::PRICE_ADDITION_TYPE_WITH_ROOM}
                                            <span class="badge badge-info label">{l s='Auto added'}</span><br>
                                        {/if}
                                        {if $service['product_auto_add'] && $service['product_price_addition_type'] == Product::PRICE_ADDITION_TYPE_INDEPENDENT}
                                            <span class="badge badge-info label">{l s='Convenience fee'}</span>
                                        {/if}
                                    </td>
                                    <td class="text-center">
                                        {if $service['allow_multiple_quantity']}
                                            <div class="qty_container">
                                                <input type="number" class="form-control qty" min="1" data-id_product="{$service['id_product']|escape:'html':'UTF-8'}" value="{$service['quantity']|escape:'html':'UTF-8'}" name="service_qty[{$service['id_service_product_order_detail']|escape:'html':'UTF-8'}]">
                                                {if $service['max_quantity']}
                                                    <p style="display:{if $service['quantity'] > $service['max_quantity']}block{else}none{/if}; margin-top: 4px;">
                                                        <span class="label label-warning">{l s='Maximum allowed quantity: %s' sprintf=$service['max_quantity']}</span>
                                                    </p>
                                                {/if}
                                            </div>
                                        {else}
                                            --
                                        {/if}
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <span class="input-group-addon">{$currencySign}</span>
                                            <input type="text" class="form-control unit_price" value="{Tools::ps_round($service['unit_price_tax_excl'], 2)}" data-id-product="{$service['id_product']}" name="service_price[{$service['id_service_product_order_detail']}]">
                                            {if Product::PRICE_CALCULATION_METHOD_PER_DAY == $service.price_calculation_method}
                                                <span class="input-group-addon">{l s='/ night'}</span>
                                            {/if}
                                        </div>
                                    </td>
                                    <td>{displayPrice price=$service['total_price_tax_excl']|escape:'html':'UTF-8' currency=$orderCurrency}</td>
                                    <td>{displayPrice price=$service['total_price_tax_incl']|escape:'html':'UTF-8' currency=$orderCurrency}</td>
                                    <td class="text-right"><a class="btn btn-danger pull-right del_room_additional_service" data-id_service_product_order_detail="{$service['id_service_product_order_detail']}" href="#"><i class="icon-trash"></i></a></td>
                                </tr>
                            {/foreach}
                        {else}
                            <tr>
                                <td colspan="3">
                                    <i class="icon-warning"></i> {l s='No services added yet.'}
                                </td>
                            </tr>
                        {/if}
                    </tbody>
                </table>

                <div class="modal-footer">
                    <button type="submit" id="update_selected_services" class="btn btn-primary"><i class="icon icon-save"></i> &nbsp;{l s="Update Services"}</button>
                </div>
            </form>
		</div>

        {* add already available service interface *}
		<form id="add_existing_room_services_form" class="col-sm-12 room_services_container">
			<div class="room_demand_detail">
				{if isset($serviceProducts) && $serviceProducts}
					<table class="table">
						<thead>
							<tr>
								<th></th>
								<th>{l s='Name'}</th>
								<th class="fixed-width-sm"> </th>
								<th class="fixed-width-sm text-center">{l s='Quantity'}</th>
								<th>{l s='Unit Price (tax excl.)'}</th>
							</tr>
						</thead>
						<tbody>
							{foreach $serviceProducts as $product}
								<tr class="room_demand_block">
									<td>
										<input data-id_booking_detail="{$id_booking_detail}" value="{$product['id_product']|escape:'html':'UTF-8'}" name="selected_service[]" type="checkbox" class="id_room_type_service"/>
									</td>
									<td>
										{$product['name']|escape:'html':'UTF-8'}
									</td>
									<td class="text-center">
										{if $product['auto_add_to_cart'] && $product['price_addition_type'] == Product::PRICE_ADDITION_TYPE_WITH_ROOM}
											<span class="badge badge-info label">{l s='Auto added'}</span><br>
										{/if}
										{if $product['auto_add_to_cart'] && $product['price_addition_type'] == Product::PRICE_ADDITION_TYPE_INDEPENDENT}
											<span class="badge badge-info label">{l s='Convenience fee'}</span>
										{/if}
									</td>
									<td class="text-center">
										{if $product['allow_multiple_quantity']}
											<div class="qty_container">
												<input type="number" class="form-control qty" min="1" id="qty_{$product['id_product']|escape:'html':'UTF-8'}" name="service_qty[{$product['id_product']|escape:'html':'UTF-8'}]" data-id-product="{$product.id_product|escape:'html':'UTF-8'}" value="1">
											</div>
										{else}
											{l s='--'}
										{/if}
									</td>
									<td class="text-right">
										<div class="input-group">
											<span class="input-group-addon">{$currencySign}</span>
											<input type="text" class="form-control unit_price" name="service_price[{$product['id_product']|escape:'html':'UTF-8'}]" value="{$product['price_tax_exc']}" data-id-product="{$product.id_product}">
											{if Product::PRICE_CALCULATION_METHOD_PER_DAY == $product['price_calculation_method']}
												<span class="input-group-addon">{l s='/ night'}</span>
											{/if}
										</div>
									</td>
								</tr>
							{/foreach}
						</tbody>
					</table>

                    <div class="modal-footer">
                        <button type="submit" id="save_service_service" class="btn btn-primary"><i class="icon icon-save"></i> &nbsp;{l s="Update Services"}</button>
                    </div>
				{else}
					<i class="icon-warning"></i> {l s='No services available to add to this room.'}
				{/if}
			</div>
			<input type="hidden" name="id_booking_detail" value="{$id_booking_detail}">
		</form>

        {if $customServiceAllowed}
            <form id="add_new_room_services_form" class="col-sm-12 room_services_container">
                <div class="row form-group">
                    <div class="col-sm-6">
                        <label class="control-label required">{l s='Name'}</label>
                        <input type="text" class="form-control" name="new_service_name"/>
                    </div>
                    <div class="col-sm-6">
                        <label class="control-label required">{l s='Price(tax excl.)'}</label>
                        <div class="input-group">
                            <span class="input-group-addon">{$currencySign}</span>
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
                {if $roomTypeTaxRuleGroupExist}
                    <div class="row form-group">
                        <div class="col-sm-12 help-block">
                            {l s='Note: If auto added service is enabled, then tax of the booking\'s room type will be applicable.'}
                        </div>
                    </div>
                {/if}
                <input type="hidden" name="id_booking_detail" value="{$id_booking_detail}">
                <input type="hidden" id="room_type_tax_rule_group_exist" name="room_type_tax_rule_group_exist" value="{$roomTypeTaxRuleGroupExist}">
                <div class="modal-footer">
                    <button type="submit" id="save_new_service" class="btn btn-primary"><i class="icon icon-save"></i> &nbsp;{l s="Update Service"}</button>
                </div>
            </form>
        {/if}

	{elseif isset($additionalServices) && $additionalServices}
		<table class="table room_demand_detail">
			<thead>
				<tr>
					<th>{l s='ID'}</th>
					<th>{l s='Name'}</th>
					<th></th>
					<th>{l s='Unit Price (tax excl.)'}</th>
					<th>{l s='Total Price (tax excl.)'}</th>
					<th>{l s='Total Price (tax incl.)'}</th>
				</tr>
			</thead>
			</tbody>
				{foreach $additionalServices['additional_services'] as $service}
					<tr class="room_demand_block">
						<td>
							{$service['id_product']|escape:'html':'UTF-8'}{if !$service['product_deleted']} <a target="blank" href="{$link->getAdminLink('AdminNormalProducts')|escape:'html':'UTF-8'}&amp;id_product={$service['id_product']|escape:'html':'UTF-8'}&amp;updateproduct"><i class="icon-external-link-sign"></i></a>{/if}
						</td>
						<td>{$service['name']|escape:'html':'UTF-8'}</td>
						<td>
							{if $service['product_auto_add'] && $service['product_price_addition_type'] == Product::PRICE_ADDITION_TYPE_INDEPENDENT}
								<span class="badge badge-info label">{l s='Convenience fee'}</span>
							{/if}
							{if $service['product_auto_add'] && $service['product_price_addition_type'] == Product::PRICE_ADDITION_TYPE_WITH_ROOM}
								<span class="badge badge-info label">{l s='Auto added'}</span>
							{/if}
						</td>
						<td>
							{displayPrice price=$service['unit_price_tax_excl'] currency=$orderCurrency}
							{if $service['price_calculation_method'] == Product::PRICE_CALCULATION_METHOD_PER_DAY}
								{l s='/ night'}
							{/if}
						</td>
						<td>
							{displayPrice price=$service['total_price_tax_excl'] currency=$orderCurrency}
						</td>
                        <td>
							{displayPrice price=$service['total_price_tax_incl'] currency=$orderCurrency}
						</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	{else}
		{l s='No services selected!'}
	{/if}
</div>
