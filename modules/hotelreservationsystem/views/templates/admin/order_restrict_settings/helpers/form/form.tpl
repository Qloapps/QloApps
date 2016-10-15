<div class="panel">
	<div class="panel-heading">
		{if isset($edit)}
			<i class='icon-pencil'></i>&nbsp{l s='Edit Order Restrict Date' mod='hotelreservationsystem'}
		{else}
			<i class='icon-plus'></i>&nbsp{l s='Add New Order Restrict Date' mod='hotelreservationsystem'}
		{/if}
	</div>
	<form id="{$table|escape:'htmlall':'UTF-8'}_form" class="defaultForm {$name_controller|escape:'htmlall':'UTF-8'} form-horizontal" action="{$current|escape:'htmlall':'UTF-8'}&{if !empty($submit_action)}{$submit_action|escape:'htmlall':'UTF-8'}{/if}&token={$token|escape:'htmlall':'UTF-8'}" method="post" enctype="multipart/form-data" {if isset($style)}style="{$style|escape:'htmlall':'UTF-8'}"{/if}>
		{if isset($edit)}
			<input type="hidden" value="{$id}" name="id" />
			<div class="form-group">
				<label class="control-label col-sm-3 required" for="hotel_id">{l s='Hotel Name :' mod='hotelreservationsystem'}</label>
				<div class="col-sm-6">
					<p class="form-control-static">{$ordr_restrict_hotel_data['hotel_name']}</p>
					<input type="hidden" class="form-control" name="hotel_id" value="{$ordr_restrict_hotel_data['id_hotel']}" />
				</div>
			</div>
		{else}
			<div class="form-group">
				<label class="control-label col-sm-3 required" for="hotel_id">{l s='Select Hotel :' mod='hotelreservationsystem'}</label>
				<div class="col-sm-6">
					<div style="width: 195px;">
						<select class="form-control" name="hotel_id" id="hotel_id" value="">
							<option value='0' selected>{l s='Select Hotel' mod='hotelreservationsystem'}</option>
							{if isset($hotels_list) && $hotels_list}
								{foreach $hotels_list as $list}
									<option value="{$list['id']}">{$list['hotel_name']}</option>
								{/foreach}
							{/if}
						</select>
					</div>
				</div>
			</div>
		{/if}
		<div class="form-group">
			<label class="col-sm-3 control-label required" for="max_htl_book_date">
				{l s='Maximun date For Booking :' mod='hotelreservationsystem'}
			</label>
			<div class="col-sm-2">
				<input autocomplete="off" type="text" class="form-control" id="max_htl_book_date" name="max_htl_book_date" {if isset($ordr_restrict_hotel_data['max_date']) && $ordr_restrict_hotel_data['max_date']}value="{$ordr_restrict_hotel_data['max_date']}"{/if} readonly/>
				<input autocomplete="off" type="hidden" class="form-control" id="max_htl_book_date_hidden" name="max_htl_book_date_hidden" {if isset($ordr_restrict_hotel_data['max_date']) && $ordr_restrict_hotel_data['hidden_max_date']}value="{$ordr_restrict_hotel_data['hidden_max_date']}"{/if}/>
			</div>
		</div>
		<div class="panel-footer">
			<a href="{$link->getAdminLink('AdminOrderRestrictSettings')|escape:'html':'UTF-8'}" class="btn btn-default">
				<i class="process-icon-cancel"></i>{l s='Cancel' mod='hotelreservationsystem'}
			</a>
			<button type="submit" name="submitAdd{$table|escape:'html':'UTF-8'}" class="btn btn-default pull-right">
				<i class="process-icon-save"></i> {l s='Save' mod='hotelreservationsystem'}
			</button>
			<button type="submit" name="submitAdd{$table|escape:'html':'UTF-8'}AndStay" class="btn btn-default pull-right">
				<i class="process-icon-save"></i> {l s='Save and stay' mod='hotelreservationsystem'}
			</button>
		</div>
	</form>
</div>
