{*
* 2010-2018 Webkul.
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
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

<form method="post" action="{$current|escape:'htmlall':'UTF-8'}&{if !empty($submit_action)}{$submit_action|escape:'htmlall':'UTF-8'}{/if}&token={$token|escape:'htmlall':'UTF-8'}" class="defaultForm form-horizontal {$name_controller|escape:'htmlall':'UTF-8'}" enctype="multipart/form-data">
	{if isset($edit)}
		<input name="edit_hotel_id" type="hidden" value="{$hotel_id}">
	{/if}
	<div class="panel">
		<div class="panel-heading">
			<i class="icon-user"></i> {l s='Assign Features' mod='hotelreservationsystem'}
		</div>
		{if isset($hotels) && $hotels}
			<div class="form-wrapper">
				<div class="form-group">
					{if isset($edit)}
						<label class="control-label col-sm-5">
							<span>{l s='Hotel Name' mod='hotelreservationsystem'} : </span>
						</label>
						<select class="fixed-width-xl" name="id_hotel">
							{foreach $hotels as $hotel}
								{if $hotel_id == $hotel.id}
									<option readonly="true" selected="true" value="{$hotel.id|escape:'html':'UTF-8'}" >{$hotel.hotel_name|escape:'html':'UTF-8'}</option>
								{/if}
							{/foreach}
						</select>
					{else}
						<label class="control-label col-sm-5">
							<span>{l s='Select Hotel' mod='hotelreservationsystem'} : </span>
						</label>
						<div class="col-sm-4">
							<select class="fixed-width-xl" name="id_hotel">
							<option value='0'>{l s='Select Hotel' mod='hotelreservationsystem'}</option>>
								{foreach $hotels as $hotel}
									<option value="{$hotel.id|escape:'html':'UTF-8'}" >{$hotel.hotel_name|escape:'html':'UTF-8'}</option>
								{/foreach}
							</select>
						</div>
					{/if}
				</div>
			</div>
			{if isset($features_list) && $features_list}
				{assign var=i value=1}
				{foreach from=$features_list item=value}
					<div class="accordion">
						<div class="accordion-section">
							<a class="accordion-section-title" href="#accordion{$i}"><span class="icon-plus"></span>&nbsp&nbsp{l s={$value.name} mod='hotelreservationsyatem'}</a>
							<div id="accordion{$i}" class="accordion-section-content">
								<table id="" class="table" style="max-width:100%">
									<tbody>
										{if isset($value.children) && $value.children}
											{foreach from=$value.children item=val}
												<tr>
													<td class="border_top border_bottom border_bold">
														<span class=""> {l s={$val.name} mod='hotelreservationsyatem'} </span>
													</td>
													<td style="">
														<input name="hotel_fac[]" type="checkbox" value="{$val.id}" class="form-control" {if isset($edit) && $val.selected}checked='true'{/if}>
													</td>
												</tr>
											{/foreach}
										{/if}
									</tbody>
								</table>
							</div>
						</div>
					</div>
					{assign var=i value=$i+1}
				{/foreach}
			{/if}
			<div class="panel-footer">
				<a href="{$link->getAdminLink('AdminHotelFeatures')|escape:'html':'UTF-8'}" class="btn btn-default"><i class="process-icon-cancel"></i> {l s='Cancel' mod='hotelreservationsystem'}</a>
				<button type="submit" name="submitAddhtl_features" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Assign' mod='hotelreservationsystem'}</button>
				<!-- <button type="submit" name="submitAdd{$table|escape:'html':'UTF-8'}AndStay" class="btn btn-default pull-right">
					<i class="process-icon-save"></i> {l s='Assign and stay' mod='hotelreservationsystem'}
				</button> -->
			</div>
		{else}
			<div class="alert alert-warning">
				{l s='No hotel found to assign features.' mod='hotelreservationsystem'}
			</div>
		{/if}
	</div>
</form>