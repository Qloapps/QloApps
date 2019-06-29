<div class="panel">
	<h3>{l s='Priority management' mod='hotelreservationsystem'}</h3>
	<div class="alert alert-info">
		{l s='Sometimes one customer can fit into multiple Feature price rules. In this case priorities allow you to define which rule applies to the Room Type.' mod='hotelreservationsystem'}
	</div>
	<form id="{$table|escape:'htmlall':'UTF-8'}_form" class="defaultForm form-horizontal" action="{$current|escape:'htmlall':'UTF-8'}&{if !empty($submit_action)}{$submit_action|escape:'htmlall':'UTF-8'}{/if}&token={$token|escape:'htmlall':'UTF-8'}" method="post" enctype="multipart/form-data" {if isset($style)}style="{$style|escape:'htmlall':'UTF-8'}"{/if}>
		<div class="form-group">
			<label class="control-label col-lg-3" for="featurePricePriority">{l s='Feature Price Calculation Priorities' mod='hotelreservationsystem'} :: </label>
			<div class="input-group col-lg-9">
				<select name="featurePricePriority[]" class="featurePricePriority">
					<option class="specific_date" value="specific_date" {if isset($featurePricePriority) && $featurePricePriority[0]=='specific_date'}selected="selected"{/if}>{l s="Specific Date" mod='hotelreservationsystem'}</option>
					<option class="special_day" value="special_day" {if isset($featurePricePriority) && $featurePricePriority[0]=='special_day'}selected="selected"{/if}>{l s="Special Days" mod='hotelreservationsystem'}</option>
					<option class="date_range" value="date_range" {if isset($featurePricePriority) && $featurePricePriority[0]=='date_range'}selected="selected"{/if}>{l s="Date Ranges" mod='hotelreservationsystem'}</option>
				</select>
				<span class="input-group-addon"><i class="icon-chevron-right"></i></span>
				<select name="featurePricePriority[]" class="featurePricePriority">
					<option class="specific_date" value="specific_date" {if isset($featurePricePriority) && $featurePricePriority[1]=='specific_date'}selected="selected"{/if}>{l s="Specific Date" mod='hotelreservationsystem'}</option>
					<option class="special_day" value="special_day" {if isset($featurePricePriority) && $featurePricePriority[1]=='special_day'}selected="selected"{/if}>{l s="Special Days" mod='hotelreservationsystem'}</option>
					<option class="date_range" value="date_range" {if isset($featurePricePriority) && $featurePricePriority[1]=='date_range'}selected="selected"{/if}>{l s="Date Ranges" mod='hotelreservationsystem'}</option>
				</select>
				<span class="input-group-addon"><i class="icon-chevron-right"></i></span>
				<select name="featurePricePriority[]" class="featurePricePriority">
					<option class="specific_date" value="specific_date" {if isset($featurePricePriority) && $featurePricePriority[2]=='specific_date'}selected="selected"{/if}>{l s="Specific Date" mod='hotelreservationsystem'}</option>
					<option class="special_day" value="special_day" {if isset($featurePricePriority) && $featurePricePriority[2]=='special_day'}selected="selected"{/if}>{l s="Special Days" mod='hotelreservationsystem'}</option>
					<option class="date_range" value="date_range" {if isset($featurePricePriority) && $featurePricePriority[2]=='date_range'}selected="selected"{/if}>{l s="Date Ranges" mod='hotelreservationsystem'}</option>
				</select>
			</div>
		</div>
		<div class="panel-footer">
			<button type="submit" name="submitAddFeaturePricePriority" class="btn btn-default pull-right">
				<i class="process-icon-save"></i> {l s='Save' mod='hotelreservationsystem'}
			</button>
		</div>
	</form>
</div>
