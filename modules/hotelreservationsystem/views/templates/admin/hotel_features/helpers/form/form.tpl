{if isset($addfeatures)}
	<form method="post" action="{$current|escape:'htmlall':'UTF-8'}&{if !empty($submit_action)}{$submit_action|escape:'htmlall':'UTF-8'}{/if}&token={$token|escape:'htmlall':'UTF-8'}" class="defaultForm form-horizontal {$name_controller|escape:'htmlall':'UTF-8'}" enctype="multipart/form-data">
		<div class="panel" style="float:left">
			<div class="panel-heading">
				<i class="icon-plus"></i>&nbsp {l s='Add New Features' mod='hotelreservationsystem'}
			</div>

			<a data-mfp-src="#test-popup" class="btn btn-primary open-popup-link-feature" data-toggle="modal" data-target="#basicModal_addNewFeature"><span><i class="icon-plus"></i>&nbsp{l s='Add New Features' mod='hotelreservationsystem'}</span></a>

			{foreach from=$features_list item=value}
				<div class="col-sm-12 feature_div" id="grand_feature_div_{$value.id}">
					<div class="row row-margin-bottom row-margin-top">
						<div class="col-sm-12">
							<div class="row feature-border-div">
								<div class="col-sm-12 feature-header-div">
									<span>{l s={$value.name} mod='hotelreservationsyatem'}</span>
									<a class="btn btn-primary pull-right edit_feature col-sm-1" data-feature='{$value|@json_encode}'><span><i class="icon-pencil"></i>&nbsp&nbsp&nbsp&nbsp{l s='Edit' mod='hotelreservationsystem'}</span></a>
									<button class="btn btn-primary pull-right dlt-feature col-sm-1" data-feature-id="{$value.id}"><i class="icon-trash"></i>&nbsp&nbsp&nbsp&nbsp{l s='Delete' mod='hotelreservationsystem'}</button>
								</div>
							</div>
						</div>
					</div>
					<div class="row child-features-container">
						<div class="col-sm-12">
						{foreach from=$value.children item=val}
							<p>{l s={$val.name} mod='hotelreservationsyatem'}</p>
						{/foreach}
						</div>
					</div>
				</div>
			{foreachelse}
				<!-- code for foreachelse -->
			{/foreach}
		</div>
	</form>
{else}
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
					{assign var=i value=1}
					{foreach from=$features_list item=value}
					<div class="accordion">
					    <div class="accordion-section">
					        <a class="accordion-section-title" href="#accordion{$i}"><span class="icon-plus"></span>&nbsp&nbsp{l s={$value.name} mod='hotelreservationsyatem'}</a>
					        <div id="accordion{$i}" class="accordion-section-content">
					        	<table id="" class="table" style="max-width:100%">
									<tbody>
										{foreach from=$value.children item=val}
											<tr>
												<td class="border_top border_bottom border_bold">
													<span class=""> {l s={$val.name} mod='hotelreservationsyatem'} </span>
												</td>
												<td style="">
													<input name="hotel_fac[]" type="checkbox" value="{$val.id}" class="form-control" {if isset($edit) && $val.selected}checked='true'{/if}>
												</td>
											</tr>
										{foreachelse}
											<!-- code for foreachelse -->
										{/foreach}
									</tbody>
								</table>
					        </div>
					    </div>
					</div>
					{assign var=i value=$i+1}
					{foreachelse}
						<!-- code for foreachelse -->
					{/foreach}
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
{/if}

<!-- model box for add new features -->
<div class="modal fade" id="basicModal_addNewFeature" tabindex="-1" role="dialog" aria-labelledby="basicModal_features" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content add_hotel_feature_form_div">
			<div class="modal-header" style="padding-left:15px;">
				<h2 class="page-subheading_admin_wallet">
					{l s='Add Hotel Features' mod='hotelreservationsystem'}
				</h2>
			</div>
			<form method="post" action="{$link->getAdminLink('AdminHotelFeatures')}">
				<div class="modal-body">
					<div class="row row-margin-bottom">	
						<label style="font-weight:600" class="col-sm-3 control-label" >{l s='Parent Feature :' mod='hotelreservationsystem'}</label>
						<div class="col-sm-7">
							<input type="text" name="parent_ftr" class="parent_ftr" placeholder="{l s='parent feature name' mod='hotelreservationsystem'}" class="form-control" />
							<input type="hidden" name="parent_ftr_id" class="parent_ftr_id"/>
							<p class="error_text" id="prnt_ftr_err_p"></p>
						</div>
					</div>
					<div class="row row-margin-bottom">	
						<label style="font-weight:600" class="col-sm-3 control-label">{l s='Position :' mod='hotelreservationsystem'}</label>
						<div class="col-sm-7">
							<input type="text" name="position" class="position" placeholder="{l s='feature position' mod='hotelreservationsystem'}" class="form-control"/>
							<p class="error_text" id="pos_err_p"></p>
						</div>
					</div>
					<div class="row row-margin-bottom">	
						<label style="font-weight:600" class="col-sm-3 control-label">{l s='Child Feature :' mod='hotelreservationsystem'}</label>
						<div class="col-sm-5">
							<input type="text" placeholder="child feature name" class="child_ftr col-sm-4" name="child_ftr">
							<p class="error_text" id="chld_ftr_err_p"></p>
						</div>
						<button type="button" class='col-sm-2 btn btn-primary add_feature_to_list'>{l s='Add' mod='hotelreservationsystem'}</button>
					</div>
					<div class="row row-margin-bottom added_feature">	
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary admin_submit_feature" name="submit_add_btn_feature">
						<span>
							{l s='Create Feature' mod='hotelreservationsystem'}
						</span>
					</button>
					<button type="button" class="btn btn-primary" data-dismiss="modal">
						<span>
							{l s='Cancel' mod='hotelreservationsystem'}
						</span>
					</button>
				</div>
			</form>
		</div>
	</div>
</div>
<!-- END -->




{strip}
	{addJsDef delete_url=$link->getAdminLink('AdminHotelFeatures') js=1 mod='hotelreservationsystem'}
	{addJsDefL name=success_delete_msg}{l s='Successfully Deleted.' js=1 mod='hotelreservationsystem'}{/addJsDefL}
	{addJsDefL name=error_delete_msg}{l s='Some error occured while deleting feature.Please try again.' js=1 mod='hotelreservationsystem'}{/addJsDefL}
	{addJsDefL name=confirm_delete_msg}{l s='Are you sure?' js=1 mod='hotelreservationsystem'}{/addJsDefL}

	{addJsDefL name=prnt_ftr_err}{l s='Enter Parent feature name first.' js=1 mod='hotelreservationsystem'}{/addJsDefL}
	{addJsDefL name=chld_ftr_err}{l s='Enter at least one child feature.' js=1 mod='hotelreservationsystem'}{/addJsDefL}
	{addJsDefL name=chld_ftr_text_err}{l s='Enter child feature name.' js=1 mod='hotelreservationsystem'}{/addJsDefL}
	{addJsDefL name=pos_numeric_err}{l s='Position should be numeric.' js=1 mod='hotelreservationsystem'}{/addJsDefL}
{/strip}
