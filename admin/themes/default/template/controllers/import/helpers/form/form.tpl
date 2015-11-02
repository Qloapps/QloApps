{*
* 2007-2015 PrestaShop
**
* NOTICE OF LICENSE
**
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
**
* DISCLAIMER
**
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
**
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<div class="leadin">{block name="leadin"}{/block}</div>
{if $module_confirmation}
<div class="alert alert-success clearfix">
	{l s='Your .CSV file has been successfully imported into your shop. Don\'t forget to re-build the products\' search index.'}
</div>
{/if}
<div class="row">
	<div class="col-lg-8">
		{* Import fieldset *}
		<div class="panel">
			<h3>
				<i class="icon-upload"></i>
				{l s='Import'}
			</h3>
			<div class="alert alert-info">
				<ul class="list-unstyled">
					<li>{l s='You can read information on CSV import at:'}
						<a href="http://doc.prestashop.com/display/PS16/CSV+Import+Parameters" class="_blank">http://doc.prestashop.com/display/PS16/CSV+Import+Parameters</a>
					</li>
					<li>{l s='Read more about the CSV format at:'}
						<a href="http://en.wikipedia.org/wiki/Comma-separated_values" class="_blank">http://en.wikipedia.org/wiki/Comma-separated_values</a>
					</li>
				</ul>
			</div>
			<hr />
			<form id="preview_import" action="{$current|escape:'html':'UTF-8'}&amp;token={$token|escape:'html':'UTF-8'}" method="post" enctype="multipart/form-data" class="form-horizontal">
				<div class="form-group">
					<label for="entity" class="control-label col-lg-4">{l s='What kind of entity would you like to import?'} </label>
					<div class="col-lg-8">
						<select name="entity" id="entity" class="fixed-width-xxl form-control">
							{foreach $entities AS $entity => $i }
							<option value="{$i}"{if $entity_selected == $i} selected="selected"{/if}>
								{$entity}
							</option>
							{/foreach}
						</select>
					</div>
				</div>
				<div class="alert alert-warning import_products_categories">
					<ul>
						<li>{l s='Note that the Category import does not support having two categories with the same name.'}</li>
						<li>{l s='Note that you can have several products with the same reference.'}</li>
					</ul>
				</div>
				<div class="alert alert-warning import_supply_orders_details">
					<p>{l s='Importing Supply Order Details will reset your history of ordered products, if there are any.'}</p>
				</div>
				<hr />
				<div class="form-group" id="csv_file_uploader">
					<label for="file" class="control-label col-lg-4">{l s='Select a CSV file to import'}</label>
					<div class="col-lg-8">
						<input id="file" type="file" name="file" data-url="{$current|escape:'html':'UTF-8'}&amp;token={$token|escape:'html':'UTF-8'}&amp;ajax=1&amp;action=uploadCsv" class="hide" />
						<button class="ladda-button btn btn-default" data-style="expand-right" data-size="s" type="button" id="file-add-button">
							<i class="icon-folder-open"></i>
							{l s='Upload a file'}
						</button>
						{l s='or'}
						<button class="btn btn-default csv-history-btn" type="button">
							<span class="csv-history-nb badge">{$files_to_import|count}</span>
							{l s="Choose from history / FTP"}
						</button>
						<p class="help-block">
							{l s='Only UTF-8 and ISO 8859-1 encodings are allowed'}.<br/>
							{l s='You can also upload your file via FTP to the following directory: %s .' sprintf=$path_import}
						</p>
					</div>
					<div class="alert alert-danger" id="file-errors" style="display:none"></div>
				</div>
				<div class="form-group" id="csv_files_history" style="display:none;" >
					<div class="panel">
						<div class="panel-heading">
							{l s='History of uploaded .CSV'}
							<span class="csv-history-nb badge">{$files_to_import|count}</span>
							<button type="button" class="btn btn-link pull-right csv-history-btn">
								<i class="icon-remove"></i>
							</button>
						</div>
						<table id="csv_uploaded_history" class="table">
							<tr class="hide">
								<td></td>
								<td>
									<div class="btn-group pull-right">
										<button type="button" data-filename="" class="csv-use-btn btn btn-default">
											<i class="icon-ok"></i>
											{l s='Use'}
										</button>
										<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
											<i class="icon-chevron-down"></i>
										</button>
										<ul class="dropdown-menu" role="menu">
											<li>
												<a class="csv-download-link _blank" href="#">
													<i class="icon-download"></i>
													{l s='Download'}
												</a>
											</li>
											<li class="divider"></li>
											<li>
												<a class="csv-delete-link" href="#">
													<i class="icon-trash"></i>
													{l s='Delete'}
												</a>
											</li>
										</ul>
									</div>
								</td>
							</tr>
							{foreach $files_to_import AS $filename}
							<tr >
								<td>
									{$filename}
								</td>
								<td>
									<div class="btn-group pull-right">
										<button type="button" data-filename="{$filename|escape:'html':'UTF-8'}" class="csv-use-btn btn btn-default">
											<i class="icon-ok"></i>
											{l s='Use'}
										</button>
										<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
											<i class="icon-chevron-down"></i>
										</button>
										<ul class="dropdown-menu" role="menu">
											<li>
												<a href="{$current|escape:'html':'UTF-8'}&amp;token={$token|escape:'html':'UTF-8'}&amp;csvfilename={$filename|@urlencode}" class="_blank">
													<i class="icon-download"></i>
													{l s='Download'}
												</a>
											</li>
											<li class="divider"></li>
											<li>
												<a href="{$current|escape:'html':'UTF-8'}&amp;token={$token|escape:'html':'UTF-8'}&amp;csvfilename={$filename|@urlencode}&amp;delete=1">
													<i class="icon-trash"></i>
													{l s='Delete'}
												</a>
											</li>
										</ul>
									</div>
								</td>
							</tr>
							{/foreach}
						</table>
					</div>
				</div>
				<div class="form-group" id="csv_file_selected" style="display: none;">
					<div class="alert alert-success clearfix">
						<input type="hidden" value="{$csv_selected}" name="csv" id="csv_selected_value" />
						<div class="col-lg-8">
							<span id="csv_selected_filename">{$csv_selected|escape:'html':'UTF-8'}</span>
						</div>
						<div class="col-lg-4">
							<div class="btn-group pull-right">
								<button id="file-remove-button" type="button" class="btn btn-default">
									<i class="icon-refresh"></i>
									{l s='Change'}
								</button>
							</div>
						</div>
					</div>
				</div>
				<hr />
				<div class="form-group">
					<label for="iso_lang" class="control-label col-lg-4">
						<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='The locale must be installed'}">
							{l s='Language of the file'}
						</span>
					</label>
					<div class="col-lg-8">
						<select id="iso_lang" name="iso_lang" class="fixed-width-xl form-control">
							{foreach $languages AS $lang}
								<option value="{$lang.iso_code}" {if $lang.id_lang == $id_language} selected="selected"{/if}>{$lang.name}</option>
							{/foreach}
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="convert" class="control-label col-lg-4">{l s='ISO 8859-1 encoded file?'}</label>
					<div class="col-lg-8">
						<label class="switch-light prestashop-switch fixed-width-lg">
							<input name="convert" id="convert" type="checkbox" />
							<span>
								<span>{l s='Yes'}</span>
								<span>{l s='No'}</span>
							</span>
							<a class="slide-button btn"></a>
						</label>
					</div>
				</div>
				<div class="form-group">
					<label for="separator" class="control-label col-lg-4">{l s='Field separator'}</label>
					<div class="col-lg-8">
						<input id="separator" name="separator" class="fixed-width-xs form-control" type="text" value="{if isset($separator_selected)}{$separator_selected|escape:'html':'UTF-8'}{else};{/if}" />
						<div class="help-block">{l s='e.g. '} 1; Blouse; 129.90; 5</div>
					</div>
				</div>
				<div class="form-group">
					<label for="multiple_value_separator" class="control-label col-lg-4">{l s='Multiple value separator'}</label>
					<div class="col-lg-8">
						<input id="multiple_value_separator" name="multiple_value_separator" class="fixed-width-xs form-control" type="text" value="{if isset($multiple_value_separator_selected)}{$multiple_value_separator_selected|escape:'html':'UTF-8'}{else},{/if}" />
						<div class="help-block">{l s='e.g. '} Blouse; red.jpg, blue.jpg, green.jpg; 129.90</div>
					</div>
				</div>
				<hr />
				<div class="form-group">
					<label for="truncate" class="control-label col-lg-4">{l s='Delete all'} <span id="entitie">{l s='categories'}</span> {l s='before import'} </label>
					<div class="col-lg-8">
						<label class="switch-light prestashop-switch fixed-width-lg">
							<input id="truncate" name="truncate" type="checkbox"/>
							<span>
								<span>{l s='Yes'}</span>
								<span>{l s='No'}</span>
							</span>
							<a class="slide-button btn"></a>
						</label>
					</div>
				</div>
				<div class="form-group" style="display: none">
					<label for="match_ref" class="control-label col-lg-4">
						<span data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='If enabled, the product\'s reference number MUST be unique!'}">
							{l s='Use product reference as key'}
						</span>
					</label>
					<div class="col-lg-8">
						<label class="switch-light prestashop-switch fixed-width-lg">
							<input id="match_ref" name="match_ref" type="checkbox" />
							<span>
								<span>{l s='Yes'}</span>
								<span>{l s='No'}</span>
							</span>
							<a class="slide-button btn"></a>
						</label>
					</div>
				</div>
				<div class="form-group">
					<label for="regenerate" class="control-label col-lg-4">{l s='Skip thumbnails regeneration'}</label>
					<div class="col-lg-8">
						<label class="switch-light prestashop-switch fixed-width-lg">
							<input id="regenerate" name="regenerate" type="checkbox" />
							<span>
								<span>{l s='Yes'}</span>
								<span>{l s='No'}</span>
							</span>
							<a class="slide-button btn"></a>
						</label>
					</div>
				</div>
				<div class="form-group">
					<label for="forceIDs" class="control-label col-lg-4">
						<span data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='If you enable this option, your imported items\' ID number will be used as-is. If you do not enable this option, the imported ID number will be ignored, and PrestaShop will instead create auto-incremented ID numbers for all the imported items.'}">
							{l s='Force all ID numbers'}
						</span>
					</label>
					<div class="col-lg-8">
						<label class="switch-light prestashop-switch fixed-width-lg">
							<input  id="forceIDs" name="forceIDs" type="checkbox"/>
							<span>
								<span>{l s='Yes'}</span>
								<span>{l s='No'}</span>
							</span>
							<a class="slide-button btn"></a>
						</label>
					</div>
				</div>
<!--
				{*if empty($files_to_import)*}
				<div class="alert alert-info">{l s='You must upload a file in order to proceed to the next step'}</div>
				{*if !count($files_to_import)*}
				<p>{l s='There is no CSV file available. Please upload one using the \'Upload\' button above.'}</p>
-->
				<div class="panel-footer">
					<button type="submit" name="submitImportFile" id="submitImportFile" class="btn btn-default pull-right" >
						<i class="process-icon-next"></i> <span>{l s='Next step'}</span>
					</button>
				</div>
			</form>
		</div>
	</div>
	<div class="col-lg-4">
		{* Available and required fields *}
		<div class="panel">
			<h3>
				<i class="icon-list-alt"></i>
				{l s='Available fields'}
			</h3>
			<div id="availableFields" class="alert alert-info">
				{$available_fields}
			</div>
			<p>{l s='* Required field'}</p>
		</div>
		<div class="panel">
			<div class="panel-heading">
				<i class="icon-download"></i>
				{l s='Download sample csv files'}
			</div>

			<div class="list-group">
				<a class="list-group-item _blank" href="../docs/csv_import/categories_import.csv">
					{l s='Sample Categories file'}
				</a>
				<a class="list-group-item _blank" href="../docs/csv_import/products_import.csv">
					{l s='Sample Products file'}
				</a>
				<a class="list-group-item _blank" href="../docs/csv_import/combinations_import.csv">
					{l s='Sample Combinations file'}
				</a>
				<a class="list-group-item _blank" href="../docs/csv_import/customers_import.csv">
					{l s='Sample Customers file'}
				</a>
				<a class="list-group-item _blank" href="../docs/csv_import/addresses_import.csv">
					{l s='Sample Addresses file'}
				</a>
				<a class="list-group-item _blank" href="../docs/csv_import/manufacturers_import.csv">
					{l s='Sample Manufacturers file'}
				</a>
				<a class="list-group-item _blank" href="../docs/csv_import/suppliers_import.csv">
					{l s='Sample Suppliers file'}
				</a>
				<a class="list-group-item _blank" href="../docs/csv_import/alias_import.csv">
					{l s='Sample Aliases file'}
				</a>
				{if $PS_ADVANCED_STOCK_MANAGEMENT}
				<a class="list-group-item _blank" href="../docs/csv_import/supply_orders_import.csv">
					{l s='Sample Supply Orders file'}
				</a>
				<a class="list-group-item _blank" href="../docs/csv_import/supply_orders_details_import.csv">
					{l s='Sample Supply Order Details file'}
				</a>
				{/if}
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">

	function humanizeSize(bytes) {
		if (typeof bytes !== 'number')
			return '';
		if (bytes >= 1000000000)
			return (bytes / 1000000000).toFixed(2) + ' GB';
		if (bytes >= 1000000)
			return (bytes / 1000000).toFixed(2) + ' MB';
		return (bytes / 1000).toFixed(2) + ' KB';
	}
	// when user select a .csv
	function csv_select(filename) {
		$('#csv_selected_value').val(filename);
		$('#csv_selected_filename').html(filename);
		$('#csv_file_selected').show();
		$('#csv_file_uploader').hide();
		$('#csv_files_history').hide();
	}
	// when user unselect the .csv
	function csv_unselect() {
		$('#csv_file_selected').hide();
		$('#csv_file_uploader').show();
	}

	// add a disabled state when empty history
	function enableHistory(){
		if($('.csv-history-nb').text() == 0){
			$('button.csv-history-btn').attr('disabled','disabled');
		} else {
			$('button.csv-history-btn').attr('disabled',false);
		}
	}

	$(document).ready(function() {

		var file_add_button = Ladda.create(document.querySelector('#file-add-button'));
		var file_total_files = 0;

		$('#file').fileupload({
			dataType: 'json',
			autoUpload: true,
			acceptFileTypes: /(\.|\/)(csv)$/i,
			singleFileUploads: true,
			{if isset ($post_max_size)}maxFileSize: {$post_max_size},{/if}
			start: function (e) {
				file_add_button.start();
			},
			fail: function (e, data) {
				$('#file-errors').html(data.errorThrown.message).show();
			},
			done: function (e, data) {
				if (data.result) {
					if (typeof data.result.file !== 'undefined') {
						if (typeof data.result.file.error !== 'undefined' && data.result.file.error != '')
							$('#file-errors').html('<strong>'+data.result.file.name+'</strong> : '+data.result.file.error).show();
						else {
							$(data.context).find('button').remove();

							var filename = encodeURIComponent(data.result.file.filename);
							var row = $('#csv_uploaded_history tr:first').clone();

							$('#csv_uploaded_history').append(row);
							row.removeClass('hide');
							row.find('td:first').html(data.result.file.filename);
							row.find('button.csv-use-btn').data('filename', data.result.file.filename);
							row.find('a.csv-download-link').attr('href','{$current|escape:'html':'UTF-8'}&token={$token|escape:'html':'UTF-8'}&csvfilename='+filename);
							row.find('a.csv-delete-link').attr('href','{$current|escape:'html':'UTF-8'}&token={$token|escape:'html':'UTF-8'}&csvfilename='+filename+'&delete=1');
							csv_select(data.result.file.filename);
							var items = $('#csv_uploaded_history tr').length -1;
							$('.csv-history-nb').html(items);
							enableHistory();
						}
					}
				}
			},
		}).on('fileuploadalways', function (e, data) {
			file_add_button.stop();
		}).on('fileuploadprocessalways', function (e, data) {
			var index = data.index,	file = data.files[index];

			if (file.error) {
				$('#file-errors').append('<strong>'+file.name+'</strong> ('+humanizeSize(file.size)+') : '+file.error).show();
				$(data.context).find('button').trigger('click');
			}
		});

		$('#csv_uploaded_history').on('click', 'button.csv-use-btn', function(e){
			e.preventDefault();
			var filename = $(this).data('filename');
			csv_select(filename);
		});
		$('#file-add-button').on('click', function(e) {
			e.preventDefault();
			$('#file-success').hide();
			$('#file-errors').html('').hide();
			$('#file').trigger('click');
		});
		$('#file-remove-button').on('click', function(e) {
			e.preventDefault();
			csv_unselect();
		});

		$('.csv-history-btn').on('click',function(e){
			e.preventDefault();
			$('#csv_files_history').toggle();
			$('#csv_file_uploader').toggle();
		})
		//show selected csv if exists
		var selected = '{$csv_selected}';
		if(selected){
			$('#csv_file_selected').show();
			$('#csv_file_uploader').hide();
		}

		var truncateAuthorized = {$truncateAuthorized|intval};

		enableHistory();

		$('#preview_import').submit(function(e) {
			if ($('#truncate').get(0).checked) {
				if (truncateAuthorized) {
					if (!confirm('{l s='Are you sure that you would like to delete this entity: ' js=1}' + ' ' + $.trim($('#entity > option:selected').text().toLowerCase()) + '?'))
						e.preventDefault();
				}
				else {
					jAlert('{l s='You do not have permission to delete this. When the MultiStore mode is enabled, only a SuperAdmin can delete all items before an import.' js=1}');
					return false;
				}
			}
		});

		$("select#entity").change(function() {
			if ($("#entity > option:selected").val() == 8 || $("#entity > option:selected").val() == 9) {
				$("#truncate").closest('.form-group').hide();
			}
			else {
				$("#truncate").closest('.form-group').show();
			}
			if ($("#entity > option:selected").val() == 9) {
				$(".import_supply_orders_details").show();
			}
			else {
				$(".import_supply_orders_details").hide();
				$('input[name=multiple_value_separator]').val('{if isset($multiple_value_separator_selected)}{$multiple_value_separator_selected}{else},{/if}');
			}
			if ($("#entity > option:selected").val() == 1 || $("#entity > option:selected").val() == 2) {
				$("#match_ref").closest('.form-group').show();
			}
			else {
				$("#match_ref").closest('.form-group').hide();
			}
			if ($("#entity > option:selected").val() == 1 || $("#entity > option:selected").val() == 0) {
				$(".import_products_categories").show();
			}
			else {
				$(".import_products_categories").hide();
			}
			if ($("#entity > option:selected").val() == 0 || $("#entity > option:selected").val() == 1 ||
				$("#entity > option:selected").val() == 5 || $("#entity > option:selected").val() == 6) {
					$("#regenerate").closest('.form-group').show();
			}
			else {
				$("#regenerate").closest('.form-group').hide();
			}
			if ($("#entity > option:selected").val() == 0 || $("#entity > option:selected").val() == 1 ||
				$("#entity > option:selected").val() == 3 || $("#entity > option:selected").val() == 4 ||
				$("#entity > option:selected").val() == 5 || $("#entity > option:selected").val() == 6 ||
				$("#entity > option:selected").val() == 7) {
				$("#forceIDs").closest('.form-group').show();
			}
			else {
				$("#forceIDs").closest('.form-group').hide();
			}

			$("#entitie").html($("#entity > option:selected").text().toLowerCase());

			$.ajax({
				url: 'ajax.php',
				data: {
					getAvailableFields:1,
					entity: $("#entity").val()
				},
				dataType: 'json',
				success: function(j){
					var fields = "";
					$("#availableFields").empty();

					for (var i = 0; i < j.length; i++)
						fields += j[i].field;

					$("#availableFields").html(fields);
					$('.help-tooltip').tooltip();
				},
				error: function(j){}
			});
		});

		$("select#entity").trigger('change');

		$('#file-selectbutton').click(function(e){
			$('#file').trigger('click');
		});
		$('#filename').click(function(e){
			$('#file').trigger('click');
		});
		$('#file').change(function(e){
			var val = $(this).val();
			var file = val.split(/[\\/]/);
			$('#filename').val(file[file.length-1]);
		});
	});
</script>
