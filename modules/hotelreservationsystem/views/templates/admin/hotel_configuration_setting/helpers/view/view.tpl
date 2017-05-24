<div class="panel htl_conf_panel">
	<h3 class="tab"> <i class="icon-cogs"></i>&nbsp;&nbsp; {l s='Hotel Configuration' mod='hotelreservationsystem'}</h3>
	<div class="panel-body">
	<div class="btn-group setting-link-div col-sm-3 col-xs-12">
		<a type="button"  href="{$htl_reviews_conf_link}" class="setting-link btn btn-default col-sm-10 col-xs-10">
			<span class="col-sm-2 col-xs-2"><i class="icon-star"></i></span>
			<span class="setting-title col-sm-10 col-xs-10">{l s='Hotel Reviews Settings' mod='hotelreservationsystem'}</span>
		</a>
		<a tabindex="0" class="btn btn-default col-sm-2 col-xs-2" role="button" data-toggle="popover" data-trigger="focus" title="{l s='Hotel Reviews Settings' mod='hotelreservationsystem'}" data-content="{l s='Configure Reviews setting using this link.' mod='hotelreservationsystem'}" data-placement="bottom">
			<i class="icon-question-circle"></i>
		</a>
	</div>
	<div class="btn-group setting-link-div col-sm-3 col-xs-12">
		<a type="button" href="{$general_setting_link}" class="setting-link btn btn-default col-sm-10 col-xs-10">
			<span class="col-sm-2 col-xs-2"><i class="icon-cogs"></i></span>
			<span class="setting-title col-sm-10 col-xs-10">{l s='Hotel General Settings' mod='hotelreservationsystem'}</span>
		</a>
		<a tabindex="0" class="btn btn-default col-sm-2 col-xs-2" role="button" data-toggle="popover" data-trigger="focus" title="{l s='Hotel General Settings' mod='hotelreservationsystem'}" data-content="{l s='Configure Your Hotel,s general Settings using this link.' mod='hotelreservationsystem'}" data-placement="bottom">
			<i class="icon-question-circle"></i>
		</a>
	</div>
	<div class="btn-group setting-link-div col-sm-3 col-xs-12">
		<a type="button" href="{$payment_setting_link}" class="setting-link btn btn-default col-sm-10 col-xs-10">
			<span class="col-sm-2 col-xs-2"><i class="icon-money"></i></span>
			<span class="setting-title col-sm-10 col-xs-10">{l s='Payment Modules Setting' mod='hotelreservationsystem'}</span>
		</a>
		<a tabindex="0" class="btn btn-default col-sm-2 col-xs-2" role="button" data-toggle="popover" data-trigger="focus" title="{l s='Payment Modules Setting confi' mod='hotelreservationsystem'}" data-content="{l s='Configure your payment Modules settings using this link.' mod='hotelreservationsystem'}" data-placement="bottom">
			<i class="icon-question-circle"></i>
		</a>
	</div>
	<div class="btn-group setting-link-div col-sm-3 col-xs-12">
		<a type="button" href="{$order_restrict_setting_link}" class="setting-link btn btn-default col-sm-10 col-xs-10">
			<span class="col-sm-2 col-xs-2"><i class="icon-list-alt"></i></span>
			<span class="setting-title col-sm-10 col-xs-10">{l s='Order Restrict Settings' mod='hotelreservationsystem'}</span>
		</a>
		<a tabindex="0" class="btn btn-default col-sm-2 col-xs-2" role="button" data-toggle="popover" data-trigger="focus" title="{l s='Order Restrict Settings' mod='hotelreservationsystem'}" data-content="{l s='Configure if you want to restrict orders till a specific date for your hotels.' mod='hotelreservationsystem'}" data-placement="bottom">
			<i class="icon-question-circle"></i>
		</a>
	</div>
	<div class="btn-group setting-link-div col-sm-3 col-xs-12">
		<a type="button" href="{$other_module_setting_link}" class="setting-link btn btn-default col-sm-10 col-xs-10">
			<span class="col-sm-2 col-xs-2"><i class="icon-support"></i></span>
			<span class="setting-title col-sm-10 col-xs-10">{l s='Other Modules Settings' mod='hotelreservationsystem'}</span>
		</a>
		<a tabindex="0" class="btn btn-default col-sm-2 col-xs-2" role="button" data-toggle="popover" data-trigger="focus" title="{l s='Other Modules Settings' mod='hotelreservationsystem'}" data-content="{l s='Configure here settings of other modules of the software.' mod='hotelreservationsystem'}" data-placement="bottom">
			<i class="icon-question-circle"></i>
		</a>
	</div>
	<!-- Setting to set prices for date range -->
	<div class="btn-group setting-link-div col-sm-3 col-xs-12">
		<a type="button" href="{$feature_price_setting_link}" class="setting-link btn btn-default col-sm-10 col-xs-10">
			<span class="col-sm-2 col-xs-2"><i class="icon-cog"></i></span>
			<span class="setting-title col-sm-10 col-xs-10">{l s='Feature Prices Settings' mod='hotelreservationsystem'}</span>
		</a>
		<a tabindex="0" class="btn btn-default col-sm-2 col-xs-2" role="button" data-toggle="popover" data-trigger="focus" title="{l s='Feature Prices Settings' mod='hotelreservationsystem'}" data-content="{l s='Here set specific prices for specific dates.' mod='hotelreservationsystem'}" data-placement="bottom">
			<i class="icon-question-circle"></i>
		</a>
	</div>

	{hook h='displayAddModuleSettingLink'}
	</div>
</div>
