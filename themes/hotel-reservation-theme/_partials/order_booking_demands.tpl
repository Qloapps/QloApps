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

<section>
	{if (isset($extraDemands) && $extraDemands) || (isset($additionalServices) && $additionalServices)}
		<ul class="nav nav-tabs">
			{if isset($extraDemands) && $extraDemands}
				<li class="active"><a href="#room_type_demands_desc" data-toggle="tab">{l s='Facilities'}</a></li>
			{/if}
			{if isset($additionalServices) && $additionalServices}
				<li{if !isset($extraDemands) || !$extraDemands} class="active"{/if}><a href="#room_type_service_product_desc" data-toggle="tab">{l s='Services'}</a></li>
			{/if}
		</ul>
		<div class="tab-content">
			{if isset($extraDemands) && $extraDemands}
				<div id="room_type_demands_desc" class="tab-pane active">
					<div class="rooms_extra_demands_head">
						<p class="rooms_extra_demands_text">{l s='Below are the facilities chosen by you in this booking'}</p>
					</div>
					{assign var=roomCount value=1}
					{foreach $extraDemands as $roomDemand}
						<div class="card">
							<div class="row">
								<div class="col-sm-12 demand_header">
									{l s='Room'} {$roomCount|escape:'html':'UTF-8'}&nbsp;
									<span>({if {$roomDemand['adults']} <= 9}0{$roomDemand['adults']}{else}{$roomDemand['adults']}{/if} {if $roomDemand['adults'] > 1}{l s='Adults'}{else}{l s='Adult'}{/if}, {if {$roomDemand['children']} <= 9}0{$roomDemand['children']}{else}{$roomDemand['children']}{/if} {if $roomDemand['children'] > 1}{l s='Children'}{else}{l s='Child'}{/if})</span>
								</div>
								<div class="col-sm-12 room_demand_detail">
									{foreach $roomDemand['extra_demands'] as $demand}
										<div class="row room_demand_block">
											<div class="col-xs-6">{$demand['name']|escape:'html':'UTF-8'}</div>
											<div class="col-xs-6">
												<span class="pull-right">
													{if $useTax}
														{displayPrice price="{$demand['total_price_tax_incl']|escape:'html':'UTF-8'}"}
													{else}
														{displayPrice price="{$demand['total_price_tax_excl']|escape:'html':'UTF-8'}"}
													{/if}
												</span>
											</div>
										</div>
									{/foreach}
								</div>
							</div>
						</div>
						{assign var=roomCount value=$roomCount+1}
					{/foreach}
				</div>
			{/if}

			{if isset($additionalServices) && $additionalServices}
				<div id="room_type_service_product_desc" class="tab-pane{if !isset($extraDemands) || !$extraDemands} active{/if}">
					<div class="rooms_extra_demands_head">
						<p class="rooms_extra_demands_text">{l s='Below are the services chosen by you in this booking'}</p>
					</div>
					{assign var=roomCount value=1}
					{foreach $additionalServices as $key => $roomAdditionalService}
						<div class="card">
							<div class="row">
								<div class="col-sm-12 demand_header">
									{l s='Room'} {$roomCount|escape:'html':'UTF-8'}&nbsp;
									<span>({if {$roomAdditionalService['adults']} <= 9}0{$roomAdditionalService['adults']}{else}{$roomAdditionalService['adults']}{/if} {if $roomAdditionalService['adults'] > 1}{l s='Adults'}{else}{l s='Adult'}{/if}, {if {$roomAdditionalService['children']} <= 9}0{$roomAdditionalService['children']}{else}{$roomAdditionalService['children']}{/if} {if $roomAdditionalService['children'] > 1}{l s='Children'}{else}{l s='Child'}{/if})</span>
								</div>
								<div class="col-sm-12 room_demand_detail">
									{foreach $roomAdditionalService['additional_services'] as $additionalService}
										<div class="row room_demand_block">
											<div class="col-xs-6">
												<div>{$additionalService['name']|escape:'html':'UTF-8'}</div>
												{if $additionalService['allow_multiple_quantity']}
													<div class="quantity">{l s='Qty:'}&nbsp;{$additionalService['quantity']|escape:'html':'UTF-8'}</div>
												{/if}
											</div>

											<div class="col-xs-6">
												<span class="pull-right">
													{if $useTax}
														{displayPrice price=$additionalService['total_price_tax_incl']|escape:'html':'UTF-8'}
													{else}
														{displayPrice price=$additionalService['total_price_tax_excl']|escape:'html':'UTF-8'}
													{/if}
												</span>
											</div>
										</div>
									{/foreach}
								</div>
							</div>
						</div>
					{/foreach}
				</div>
			{/if}
		</div>
	{/if}
</section>