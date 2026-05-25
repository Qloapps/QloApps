{**
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

{if isset($product->id)}
<div id="product-amenities" class="panel product-tab">
	<input type="hidden" name="submitted_tabs[]" value="Amenities" />
	<h3>{l s='Amenities'}</h3>

	<div class="alert alert-info">
		{l s='Select the amenities available for this room type using the tree below.'}
		<a href="{$link->getAdminLink('AdminHotelAmenities')|escape:'html':'UTF-8'}" class="btn btn-link confirm_leave button" target="_blank">
			<i class="icon-plus-sign"></i> {l s='Manage amenities'} <i class="icon-external-link-sign"></i>
		</a>
	</div>

	{if isset($room_type_amenities_tree) && $room_type_amenities_tree}
		<div class="form-group">
			<label for="room-type-amenities-tree" class="control-label col-sm-3">
				<span class="label-tooltip" data-toggle="tooltip" data-original-title="{l s='Select the amenities available for this room type.'}">
					{l s='Select amenities'}
				</span>
			</label>
			<div class="col-sm-7 room_features_tree">
				{$room_type_amenities_tree}
			</div>
		</div>
	{else}
		<div class="alert alert-warning">
			<i class="icon-warning-sign"></i> {l s='No amenities have been defined yet.'}
		</div>
	{/if}

	<div class="panel-footer">
		<a href="{$link->getAdminLink('AdminProducts')|escape:'html':'UTF-8'}{if isset($smarty.request.page) && $smarty.request.page > 1}&amp;submitFilterproduct={$smarty.request.page|intval}{/if}" class="btn btn-default"><i class="process-icon-cancel"></i> {l s='Cancel'}</a>
		<button type="submit" name="submitAddproduct" class="btn btn-default pull-right" disabled="disabled"><i class="process-icon-loading"></i> {l s='Save'}</button>
		<button type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right" disabled="disabled"><i class="process-icon-loading"></i> {l s='Save and stay'}</button>
	</div>
</div>
{/if}
