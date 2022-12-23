<div class="panel htl_conf_panel">
	<h3 class="tab"> <i class="icon-cogs"></i>&nbsp;&nbsp; {l s='Hotel Configuration' mod='hotelreservationsystem'}</h3>
	<div class="panel-body">
		<div class="btn-group setting-link-div col-sm-3 col-xs-12">
			<a type="button" href="{$general_setting_link}" class="setting-link btn btn-default col-sm-10 col-xs-10">
				<span class="col-sm-2 col-xs-2"><i class="icon-cogs"></i></span>
				<span class="setting-title col-sm-10 col-xs-10">{l s='General Settings' mod='hotelreservationsystem'}</span>
			</a>
			<a tabindex="0" class="btn btn-default col-sm-2 col-xs-2" role="button" data-toggle="popover" data-trigger="focus" title="{l s='Hotel General Settings' mod='hotelreservationsystem'}" data-content="{l s='Configure Your Hotel general Settings using this option.' mod='hotelreservationsystem'}" data-placement="bottom">
				<i class="icon-question-circle"></i>
			</a>
		</div>
		<!-- Setting to set prices for date range -->
		<div class="btn-group setting-link-div col-sm-3 col-xs-12">
			<a type="button" href="{$feature_price_setting_link}" class="setting-link btn btn-default col-sm-10 col-xs-10">
				<span class="col-sm-2 col-xs-2"><i class="icon-cog"></i></span>
				<span class="setting-title col-sm-10 col-xs-10">{l s='Advanced Price Rules' mod='hotelreservationsystem'}</span>
			</a>
			<a tabindex="0" class="btn btn-default col-sm-2 col-xs-2" role="button" data-toggle="popover" data-trigger="focus" title="{l s='Advanced Price Rule Settings' mod='hotelreservationsystem'}" data-content="{l s='Here set advanced price rules for specific dates.' mod='hotelreservationsystem'}" data-placement="bottom">
				<i class="icon-question-circle"></i>
			</a>
		</div>
		<!-- Setting to set addition demands for the room type -->
		<div class="btn-group setting-link-div col-sm-3 col-xs-12">
			<a type="button" href="{$additional_demand_setting_link}" class="setting-link btn btn-default col-sm-10 col-xs-10">
				<span class="col-sm-2 col-xs-2"><i class="icon-cog"></i></span>
				<span class="setting-title col-sm-10 col-xs-10">{l s='Additional Facilities' mod='hotelreservationsystem'}</span>
			</a>
			<a tabindex="0" class="btn btn-default col-sm-2 col-xs-2" role="button" data-toggle="popover" data-trigger="focus" title="{l s='Additional Facilities Settings' mod='hotelreservationsystem'}" data-content="{l s='Here create additional facilities and their prices for room types.' mod='hotelreservationsystem'}" data-placement="bottom">
				<i class="icon-question-circle"></i>
			</a>
		</div>

		{hook h='displayAddModuleSettingLink'}
	</div>
</div>
