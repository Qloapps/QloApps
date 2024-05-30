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

{if isset($product->id)}
    <input type="hidden" name="occupancy_loaded" value="1">
	<div id="product-occupancy" class="panel product-tab">
		<input type="hidden" name="submitted_tabs[]" value="Occupancy" />
		<h3 class="tab"> <i class="icon-users"></i> {l s='Occupancy'}</h3>

        <div class="alert alert-info">
            {l s='Below, enter the base occupancy of this room type.'}
            <p><b>{l s='Note'}</b>: {l s='Minimum 1 adult is madatory in the base occupancy of the room type'}</p>
        </div>

		<div class="form-group">
			<label class="control-label col-sm-2" for="base_adults">
                <span class="label-tooltip" data-toggle="tooltip"
				title="{l s=''}">
                    {l s='Base adults'}
                </span>
			</label>
			<div class="col-sm-3">
				<input id="base_adults" type="text" name="base_adults" class="form-control" value="{if isset($roomTypeInfo)}{$roomTypeInfo['adults']|escape:'htmlall':'UTF-8'}{else}2{/if}">
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-sm-2" for="base_children">
                <span class="label-tooltip" data-toggle="tooltip"
				title="{l s=''}">
                    {l s='Base children'}
                </span>
			</label>
			<div class="col-sm-3">
				<input id="base_children" type="text" name="base_children" class="form-control" {if isset($roomTypeInfo)}value="{$roomTypeInfo['children']|escape:'htmlall':'UTF-8'}"{/if}>
			</div>
		</div>
        <input id="is_occupancy_submit" type="hidden" name="is_occupancy_submit" class="form-control" value="0">

        <hr>

        <div class="alert alert-info">
            {l s='Below, enter the maximum number of adults and children which can be accommodated in a room of this room type.'}
			<br>
            {l s='For maximum room occupancy, the total number of guests which can be accommodated in a room of this room type.'}
        </div>

        <div class="form-group">
			<label class="control-label col-sm-2" for="max_adults">
                <span class="label-tooltip" data-toggle="tooltip"
				title="{l s='Enter maximum number of adults can be accommodated in a room of this room type.'}">
                    {l s='Maximum adults'}
                </span>
			</label>
			<div class="col-sm-3">
				<input id="max_adults" type="text" name="max_adults" class="form-control" {if isset($roomTypeInfo)}value="{$roomTypeInfo['max_adults']|escape:'htmlall':'UTF-8'}"{/if}>
			</div>
		</div>
        <div class="form-group">
			<label class="control-label col-sm-2" for="max_children">
                <span class="label-tooltip" data-toggle="tooltip"
				title="{l s='Enter maximum number of children can be accommodated in a room of this room type.'}">
                    {l s='Maximum children'}
                </span>
			</label>
			<div class="col-sm-3">
				<input id="max_children" type="text" name="max_children" class="form-control" {if isset($roomTypeInfo)}value="{$roomTypeInfo['max_children']|escape:'htmlall':'UTF-8'}"{/if}>
			</div>
		</div>
        <div class="form-group">
			<label class="control-label col-sm-2" for="max_guests">
                <span class="label-tooltip" data-toggle="tooltip"
				title="{l s='Enter maximum number of guests can be accommodated in a room of this room type.'}">
                    {l s='Maximum room occupancy'}
                </span>
			</label>
			<div class="col-sm-3">
				<input id="max_guests" type="text" name="max_guests" class="form-control" {if isset($roomTypeInfo)}value="{$roomTypeInfo['max_guests']|escape:'htmlall':'UTF-8'}"{/if}>
			</div>
		</div>

		<div class="panel-footer">
			<a href="{$link->getAdminLink('AdminProducts')|escape:'html':'UTF-8'}{if isset($smarty.request.page) && $smarty.request.page > 1}&amp;submitFilterproduct={$smarty.request.page|intval}{/if}" class="btn btn-default">
				<i class="process-icon-cancel"></i>
				{l s='Cancel'}
			</a>
			<button type="submit" name="submitAddproduct" class="btn btn-default pull-right checkOccupancySubmit" disabled="disabled">
				<i class="process-icon-loading"></i>
				{l s='Save'}
			</button>
			<button type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right checkOccupancySubmit"  disabled="disabled">
				<i class="process-icon-loading"></i>
					{l s='Save and stay'}
			</button>
		</div>
	</div>
{/if}

<script>
    $(document).ready(function() {
        $(".checkOccupancySubmit").on("click", function() {
            $("#is_occupancy_submit").val(1);
            return true;
        });
    });
</script>