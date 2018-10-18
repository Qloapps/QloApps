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

<div class="panel">
	<div class="panel-heading">
		{l s='Hotel Features' mod='hotelreservationsystem'}
	</div>
	<div class="row">
		<form method="post" action="{$current|escape:'htmlall':'UTF-8'}&{if !empty($submit_action)}{$submit_action|escape:'htmlall':'UTF-8'}{/if}&token={$token|escape:'htmlall':'UTF-8'}" class="defaultForm form-horizontal features_list_form" enctype="multipart/form-data">
			{if $features_list}
				{foreach from=$features_list item=value}
					<div class="col-sm-12 feature_div" id="grand_feature_div_{$value.id}">
						<div class="row row-margin-bottom row-margin-top">
							<div class="col-sm-12">
								<div class="row feature-border-div">
									<div class="col-sm-12 feature-header-div">
										<span>{l s={$value.name} mod='hotelreservationsyatem'}</span>
										<a class="btn btn-primary pull-right edit_feature col-sm-1" href="{$link->getAdminLink('AdminHotelFeatures')}&amp;updatehtl_features&amp;id={$value.id}"><span><i class="icon-pencil"></i>&nbsp;&nbsp;&nbsp;&nbsp;{l s='Edit' mod='hotelreservationsystem'}</span></a>
										<button class="btn btn-primary pull-right dlt-feature col-sm-1" data-feature-id="{$value.id}"><i class="icon-trash"></i>&nbsp;&nbsp;&nbsp;&nbsp;{l s='Delete' mod='hotelreservationsystem'}</button>
									</div>
								</div>
							</div>
						</div>
						<div class="row child-features-container">
							<div class="col-sm-12">
								{if isset($value.children) && $value.children}
									{foreach from=$value.children item=val}
										<p>{l s={$val.name} mod='hotelreservationsyatem'}</p>
									{/foreach}
								{/if}
							</div>
						</div>
					</div>
				{/foreach}
			{else}
				<div class="alert alert-warning">
					{l s='No hotel features found. Start adding new features for the hotels.' mod='hotelreservationsystem'}
				</div>
			{/if}
		</form>
	</div>
</div>
{strip}
	{addJsDef delete_url=$link->getAdminLink('AdminHotelFeatures') js=1 mod='hotelreservationsystem'}
	{addJsDefL name=success_delete_msg}{l s='Successfully Deleted.' js=1 mod='hotelreservationsystem'}{/addJsDefL}
	{addJsDefL name=error_delete_msg}{l s='Some error occured while deleting feature.Please try again.' js=1 mod='hotelreservationsystem'}{/addJsDefL}
	{addJsDefL name=no_feature_warning_txt}{l s='No hotel features found. Start adding new features for the hotels.' js=1 mod='hotelreservationsystem'}{/addJsDefL}
	{addJsDefL name=confirm_delete_msg}{l s='Are you sure?' js=1 mod='hotelreservationsystem'}{/addJsDefL}
{/strip}
