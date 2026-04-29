{*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License version 3.0
* that is bundled with this package in the file LICENSE.txt
* It is also available through the world-wide-web at this URL:
* https://opensource.org/licenses/AFL-3.0
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your
* needs please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*}


{* Modal pop up for parameters of ics download for all orders *}
{if isset($allOrders) && $allOrders}
	<div class="modal fade" id="ics-download-form" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close margin-right-10" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title"><i class="icon-download-alt"></i>&nbsp; {l s='Export iCalendar File' mod='qloicsexport'}</h4>
				</div>
				<form action="{$export_link|escape:'htmlall':'UTF-8'}" method="post" class="defaultForm form-horizontal" id="ics_export_form">
					<input type="hidden" name="ics_method" value="export_all_bookings"/>
					<div class="modal-body">
						{* here put all the fields for download *}
						<div class="form-group">
							<label for="name" class="col-sm-3 control-label">
								<span class="label-tooltip" data-toggle="tooltip" title="" data-original-title="{l s='Select the hotels which bookings you want to export.' mod='qloicsexport'}">{l s='Hotels' mod='qloicsexport'}</span>
							</label>
							<div class="col-sm-9">
								<select id="ics_hotels" name="ics_hotels[]" class="form-control chosen" multiple="multiple">
									{foreach $hotels as $hotel}
										<option value="{$hotel.id_hotel|escape:'htmlall':'UTF-8'}">{$hotel.hotel_name|escape:'htmlall':'UTF-8'}</option>
									{/foreach}
								</select>
								<p class="field-error"></p>
							</div>
						</div>
						<div class="form-group">
							<label for="ics_date_from" class="col-sm-3 control-label">
								<span class="label-tooltip" data-toggle="tooltip" title="" data-original-title="{l s='Select the start date after which you want to export the bookings.' mod='qloicsexport'}">{l s='Date From' mod='qloicsexport'}</span>
							</label>
							<div class="col-sm-9">
								<div class="input-group">
									<input class="form-control" type="text" name="ics_date_from" id="ics_date_from" readonly/>
									<span class="input-group-addon ics_date_from_icon"><i class="icon-calendar"></i></span>
								</div>
								<p class="field-error"></p>
							</div>
						</div>
						<div class="form-group">
							<label for="ics_date_to" class="col-sm-3 control-label">
								<span class="label-tooltip" data-toggle="tooltip" title="" data-original-title="{l s='Select the end date to which you want to export the bookings.' mod='qloicsexport'}">{l s='Date To' mod='qloicsexport'}</span>
							</label>
							<div class="col-sm-9">
								<div class="input-group">
									<input class="form-control" type="text" name="ics_date_to" id="ics_date_to" readonly/>
									<span class="input-group-addon ics_date_to_icon"><i class="icon-calendar"></i></span>
								</div>
								<p class="field-error"></p>
							</div>
						</div>
						<div class="row form_fields_info">
							<div class="col-sm-12">
								<div class="col-sm-12 alert alert-info">
									** {l s='You can choose parameters from the above fields for exporting the iCalendar (.ics) file of your bookings. For all bookings, leave the fields blank.' mod='qloicsexport'}
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="submit" target="_blank" id="submitIcsExport" name="submitIcsExport" class="btn btn-success">
							<i class="icon-download-alt"></i> &nbsp;{l s='Export' mod='qloicsexport'}
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
{/if}