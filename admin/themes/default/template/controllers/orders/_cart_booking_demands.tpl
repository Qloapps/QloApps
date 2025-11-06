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

<div class="modal" tabindex="-1" role="dialog" id="rooms_type_extra_demands">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><i class="icon-remove-sign"></i></button>
                <h4 class="modal-title"><i class="icon icon-bed"></i> &nbsp;{l s='Room Services'}</h4>
            </div>
			<div class="modal-body" id="rooms_extra_demands">
                <ul class="nav nav-tabs" role="tablist">
					{if isset($selectedRoomDemands) && $selectedRoomDemands}
						<li role="presentation" class="active"><a href="#room_type_demands_desc" aria-controls="facilities" role="tab" data-toggle="tab">{l s='Facilities'}</a></li>
					{/if}
					{if isset($serviceProducts) && $serviceProducts}
						<li role="presentation" {if !isset($selectedRoomDemands) || !$selectedRoomDemands}class="active"{/if}><a href="#room_type_service_product_desc" aria-controls="services" role="tab" data-toggle="tab">{l s='Services'}</a></li>
					{/if}
				</ul>
				<div class="tab-content">
					{if isset($selectedRoomDemands) && $selectedRoomDemands}
						<div id="room_type_demands_desc" class="tab-pane active">
							<div id="room_type_demands_desc">
								{if isset($selectedRoomDemands) && $selectedRoomDemands}
									{assign var=roomCount value=1}
									{foreach $selectedRoomDemands as $key => $roomDemand}
										<div class="row room_demands_container">
											<div class="col-sm-12 room_demand_detail">
												{if isset($roomTypeDemands) && $roomTypeDemands}
                                                    <table class="table table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th></th>
                                                                <th>{l s='Name'}</th>
                                                                <th>{l s='Options'}</th>
                                                                <th>{l s='Unit Price (tax excl.)'}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            {foreach $roomTypeDemands as $idGlobalDemand => $demand}
                                                                <tr class="room_demand_block">
                                                                    <td>
                                                                        <input id_cart_booking="{$roomDemand['id']}" value="{$idGlobalDemand|escape:'html':'UTF-8'}" type="checkbox" class="id_room_type_demand" {if  isset($roomDemand['selected_global_demands']) && $roomDemand['selected_global_demands'] && ($idGlobalDemand|in_array:$roomDemand['selected_global_demands'])}checked{/if} />
                                                                    </td>
                                                                    <td class="demand_adv_option_block">
                                                                        <p>{$demand['name']|escape:'html':'UTF-8'}</p>
                                                                    </td>
                                                                    <td class="demand_adv_option_block">
                                                                        {if isset($demand['adv_option']) && $demand['adv_option']}
                                                                            <select class="id_option">
                                                                                {foreach $demand['adv_option'] as $idOption => $option}
                                                                                    {assign var=demand_key value="`$idGlobalDemand`-`$idOption`"}
                                                                                    <option optionPrice="{$option['price_tax_excl']|escape:'html':'UTF-8'}" value="{$idOption|escape:'html':'UTF-8'}" {if isset($roomDemand['extra_demands'][$demand_key])}selected{/if} key="{$demand_key}">{$option['name']}</option>
                                                                                    {if isset($roomDemand['extra_demands'][$demand_key])}
                                                                                        {assign var=selected_adv_option value="$idOption"}
                                                                                    {/if}
                                                                                {/foreach}
                                                                            </select>
                                                                        {else}
                                                                            --
                                                                            <input type="hidden" class="id_option" value="0" />
                                                                        {/if}
                                                                    </td>
                                                                    <td>
                                                                        <span class="extra_demand_option_price">
                                                                            {if isset($selected_adv_option) && isset($demand['adv_option'][$selected_adv_option]['price_tax_excl'])}{convertPrice price = $demand['adv_option'][$selected_adv_option]['price_tax_excl']|escape:'html':'UTF-8'}{else if isset($demand['adv_option']) && $demand['adv_option']}{convertPrice price = $demand['adv_option'][$demand['adv_option']|@key]['price_tax_excl']}{else}{convertPrice price = $demand['price_tax_excl']|escape:'html':'UTF-8'}{/if}
                                                                        </span>
                                                                        {if $demand['price_calc_method'] == HotelRoomTypeGlobalDemand::WK_PRICE_CALC_METHOD_EACH_DAY}
                                                                            {l s='/ night'}
                                                                        {/if}
                                                                    </td>
                                                                </tr>
                                                            {/foreach}
                                                        </tbody>
                                                    </table>
												{/if}
											</div>
										</div>
										{assign var=roomCount value=$roomCount+1}
									{/foreach}
								{/if}
							</div>
						</div>
					{/if}

                    {include file='controllers/orders/modals/_add_order_extra_services_tab_content.tpl'}
				</div>
                {if isset($loaderImg) && $loaderImg}
                    <div class="loading_overlay">
                        <img src='{$loaderImg}' class="loading-img"/>
                    </div>
                {/if}
			</div>
		</div>
	</div>
</div>
