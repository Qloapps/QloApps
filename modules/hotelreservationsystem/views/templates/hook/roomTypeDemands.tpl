{*
* 2010-2020 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{if isset($idProduct) && $idProduct}
	<form method="post" action="{$current|escape:'htmlall':'UTF-8'}&{if !empty($submit_action)}{$submit_action|escape:'htmlall':'UTF-8'}{/if}&token={$token|escape:'htmlall':'UTF-8'}" class="defaultForm form-horizontal" enctype="multipart/form-data">
		<div class="panel">
			<div class="panel-heading">
				<i class="icon-user"></i> {l s='Room Type Additional Facilities' mod='hotelreservationsystem'}
			</div>
			<div class="alert alert-info">
				{l s='To create new please visit' mod='hotelreservationsystem'} <a target="_blank" href="{$link->getAdminLink('AdminRoomTypeGlobalDemand')}">{l s='Additional facilities' mod='hotelreservationsystem'}</a> {l s='page' mod='hotelreservationsystem'}.
			</div>
			{if isset($allDemands) && $allDemands}
				<div id="service_accordian">
					{foreach $allDemands as $key => $demand}
						<div class="accordion">
							<div class="accordion-section">
								<a class="accordion-section-title" href="#accordion_{$key|escape:'html':'UTF-8'}">
									<input class="selected_demand" type="checkbox" name="selected_demand[]" value="{$demand['id_global_demand']|escape:'html':'UTF-8'}" {if isset($roomDemandPrices[$demand['id_global_demand']])}checked{/if} /> &nbsp;&nbsp;<span>{$demand['name']|escape:'html':'UTF-8'}<span class="pull-right"> <i class="icon-angle-left"></i>
								</a>
								<div id="accordion_{$key|escape:'html':'UTF-8'}" class="accordion-section-content">
									<div class="form-group">
										<label class="col-sm-3 control-label required" >
											{l s='Price' mod='hotelreservationsystem'}({l s='tax excl.' mod='hotelreservationsystem'})
										</label>
										<div class="col-sm-3">
											<div class="input-group">
												<span class="input-group-addon">{$defaultcurrencySign|escape:'html':'UTF-8'}</span>
												<input type="text" name="demand_price_{$demand['id_global_demand']|escape:'html':'UTF-8'}"
												value="{if isset($roomDemandPrices[$demand['id_global_demand']]['price'])}{$roomDemandPrices[$demand['id_global_demand']]['price']|escape:'html':'UTF-8'}{elseif isset($demand['price'])}{$demand['price']|escape:'html':'UTF-8'}{/if}"/>
											</div>
										</div>
									</div>
									{if isset($demand['adv_option']) && $demand['adv_option']}
										<div class="adv_options_dtl form-group">
											<label class="col-sm-3 control-label">
												{l s='Advance options' mod='hotelreservationsystem'}
											</label>
											<div class="col-sm-9">
												<div class="table-responsive-row clearfix">
													<table class="table table-bordered adv_option_table">
														<tr>
															<th>
																<span>{l s='Option Name' mod='hotelreservationsystem'}</span>
															</th>
															<th>
																<span>{l s='Price' mod='hotelreservationsystem'}</span>
															</th>
														</tr>
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
						</div>
					{/foreach}
				</div>
				<div class="panel-footer">
					<a href="{$link->getAdminLink('AdminProducts')|escape:'html':'UTF-8'}{if isset($smarty.request.page) && $smarty.request.page > 1}&amp;submitFilterproduct={$smarty.request.page|intval}{/if}" class="btn btn-default">
						<i class="process-icon-cancel"></i>
						{l s='Cancel' mod='hotelreservationsystem'}
					</a>
					<button type="submit" name="submitAddproduct" class="btn btn-default pull-right checkConfigurationClick" disabled="disabled">
						<i class="process-icon-loading"></i>
						{l s='Save' mod='hotelreservationsystem'}
					</button>
					<button type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right checkConfigurationClick"  disabled="disabled">
						<i class="process-icon-loading"></i>
							{l s='Save and stay' mod='hotelreservationsystem'}
					</button>
				</div>
			{else}
				<div class="alert alert-warning">
					{l s='No additional facilities created yet. To create please visit' mod='hotelreservationsystem'} <a target="_blank" href="{$link->getAdminLink('AdminRoomTypeGlobalDemand')}">{l s='Additional facilities' mod='hotelreservationsystem'}</a> {l s='page' mod='hotelreservationsystem'}.
				</div>
			{/if}
		</div>
	</form>
{else}
	<div class="product-tab-content">
		<div class="alert alert-warning">
			<button data-dismiss="alert" class="close" type="button">×</button>
			{l s='There is 1 warning.' mod='hotelreservationsystem'}
			<ul id="seeMore" style="display:block;">
				<li>{l s='You must save this product before selecting room type addttional facilities.' mod='hotelreservationsystem'}</li>
			</ul>
		</div>
    </div>
{/if}