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

<div class="panel">
	<div class="panel-heading">
		<i class="icon-list"></i> {l s='Manage Amenities' mod='hotelreservationsystem'}
	</div>
	<div class="row">
		{if $amenities_tree}
			{foreach from=$amenities_tree item=category}
				<div class="col-sm-12 htl-amenity-category-block" id="amenity_category_{$category.id}">
					<div class="panel">
						<div class="panel-heading htl-amenity-category-heading clearfix">
							<div class="pull-right">
								<a class="btn btn-primary"
								   href="{$admin_link}&amp;addhtl_amenity_item&amp;id_category={$category.id}">
									<i class="icon-plus"></i>&nbsp;&nbsp;{l s='Add new Amenity' mod='hotelreservationsystem'}
								</a>
								<a class="btn btn-primary"
								   href="{$admin_link}&amp;updatehtl_amenity&amp;id={$category.id}">
									<i class="icon-pencil"></i>&nbsp;&nbsp;{l s='Edit' mod='hotelreservationsystem'}
								</a>
								<a href="javascript:void(0)" class="btn btn-primary htl-delete-category"
								   data-category-id="{$category.id}">
									<i class="icon-trash"></i>&nbsp;&nbsp;{l s='Delete' mod='hotelreservationsystem'}
								</a>
							</div>
							<span class="htl-amenity-category-name">{$category.name|escape:'htmlall':'UTF-8'}</span>
						</div>
						<div class="panel-body">
							{if isset($category.children) && $category.children}
								<table class="table tableDnD htl-amenity-table">
									<colgroup>
										<col style="width:60px;">
										<col style="width:400px;">
										<col style="width:50px;">
										<col style="width:200px;">
									</colgroup>
									<thead>
										<tr>
											<th>{l s='Logo' mod='hotelreservationsystem'}</th>
											<th>{l s='Name' mod='hotelreservationsystem'}</th>
											<th class="text-center">{l s='Active' mod='hotelreservationsystem'}</th>
											<th class="text-right">{l s='Actions' mod='hotelreservationsystem'}</th>
										</tr>
									</thead>
									<tbody>
										{foreach from=$category.children item=amenity}
											<tr id="amenity_row_{$amenity.id}">
												<td class="fixed-width-xs">
													{if $amenity.logo_type == 'icon' && $amenity.logo}
														<i class="{$amenity.logo|escape:'htmlall':'UTF-8'}"></i>
													{elseif $amenity.logo_type == 'image' && $amenity.logo}
														<img src="{$img_base_url}{$amenity.id}.jpg"
															 alt="{$amenity.name|escape:'htmlall':'UTF-8'}"
															 style="max-height:32px;max-width:32px;" />
													{else}
														&mdash;
													{/if}
												</td>
												<td>{$amenity.name|escape:'htmlall':'UTF-8'}</td>
												<td class="text-center">
													<a class="list-action-enable{if $amenity.active} action-enabled{else} action-disabled{/if}"
													   href="{$admin_link}&amp;id_htl_amenity={$amenity.id|intval}&amp;statushtl_amenity&amp;token={$token|escape:'htmlall':'UTF-8'}"
													   title="{if $amenity.active}{l s='Enabled' mod='hotelreservationsystem'}{else}{l s='Disabled' mod='hotelreservationsystem'}{/if}">
														<i class="icon-check{if !$amenity.active} hidden{/if}"></i>
														<i class="icon-remove{if $amenity.active} hidden{/if}"></i>
													</a>
												</td>
												<td class="text-right">
													<a class="btn btn-default"
													   href="{$admin_link}&amp;updatehtl_amenity_item&amp;id={$amenity.id}">
														<i class="icon-pencil"></i> {l s='Edit' mod='hotelreservationsystem'}
													</a>
													<button type="button" class="btn btn-default htl-delete-amenity"
															data-amenity-id="{$amenity.id}">
														<i class="icon-trash"></i> {l s='Delete' mod='hotelreservationsystem'}
													</button>
												</td>
											</tr>
										{/foreach}
									</tbody>
								</table>
							{else}
								<div class="alert alert-info">
									{l s='No amenities found in this category.' mod='hotelreservationsystem'}
									<a href="{$admin_link}&amp;addhtl_amenity_item&amp;id_category={$category.id}">
										{l s='Add the first amenity.' mod='hotelreservationsystem'}
									</a>
								</div>
							{/if}
						</div>
					</div>
				</div>
			{/foreach}
		{else}
			<div class="col-sm-12">
				<div class="alert alert-warning">
					{l s='No amenity categories found. Start by adding a new category.' mod='hotelreservationsystem'}
				</div>
			</div>
		{/if}
	</div>
</div>

{strip}
	{addJsDef delete_url=$admin_link js=1 mod='hotelreservationsystem'}
	{addJsDefL name=confirm_delete_msg}{l s='Are you sure you want to delete this? This action cannot be undone.' js=1 mod='hotelreservationsystem'}{/addJsDefL}
	{addJsDefL name=error_delete_msg}{l s='An error occurred while deleting. Please try again.' js=1 mod='hotelreservationsystem'}{/addJsDefL}
{/strip}

<script type="text/javascript">
(function ($) {
	$(document).on('click', '.htl-delete-category', function (e) {
		e.preventDefault();
		if (!confirm(confirm_delete_msg)) { return; }
		var $btn = $(this);
		$.ajax({
			url: delete_url,
			type: 'POST',
			data: {
				ajax: 1,
				action: 'DeleteCategory',
				id_category: $btn.data('category-id'),
				token: '{$token|escape:'javascript'}'
			},
			success: function (res) {
				var r = $.parseJSON(res);
				if (r.status) {
					window.location = delete_url + '&conf=1';
				} else {
					alert(r.msg || error_delete_msg);
				}
			}
		});
	});

	$(document).on('click', '.htl-delete-amenity', function (e) {
		e.preventDefault();
		if (!confirm(confirm_delete_msg)) { return; }
		var $btn = $(this);
		$.ajax({
			url: delete_url,
			type: 'POST',
			data: {
				ajax: 1,
				action: 'DeleteAmenityItem',
				id_amenity: $btn.data('amenity-id'),
				token: '{$token|escape:'javascript'}'
			},
			success: function (res) {
				var r = $.parseJSON(res);
				if (r.status) {
					window.location = delete_url + '&conf=1';
				} else {
					alert(r.msg || error_delete_msg);
				}
			}
		});
	});
}(jQuery));
</script>
