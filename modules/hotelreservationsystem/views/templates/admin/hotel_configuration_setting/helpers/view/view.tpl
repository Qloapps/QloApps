<div class="panel">
	<h3 class="tab"> <i class="icon-info"></i> {l s='Configuration' mod='hotelreservationsystem'}</h3>
	<div class="panel-body">
		<form method="post" action="">
			<div class="row">	
				<div class="form-group col-sm-6">
					<label for="enable_location" class="control-label col-sm-4 required">
						<span title="" data-toggle="tooltip" class="label-tooltip">{l s='Enable Hotal Location Field' mod='hotelreservationsystem'}</span>
					</label>
					<div class="col-sm-8">
						<input type="switch" name="enable_location" class="form-control" id="enable_location" {if isset($date_from)}value="{$date_from}"{/if}>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>