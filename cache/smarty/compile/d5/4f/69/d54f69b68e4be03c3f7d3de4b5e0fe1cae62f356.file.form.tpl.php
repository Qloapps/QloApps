<?php /* Smarty version Smarty-3.1.19, created on 2015-11-05 18:44:56
         compiled from "/home/sumit/public_html/html/ps-hotel-reservation-system/admin/themes/default/template/controllers/import/helpers/form/form.tpl" */ ?>
<?php /*%%SmartyHeaderCode:4713243563b5650b68186-39168006%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd54f69b68e4be03c3f7d3de4b5e0fe1cae62f356' => 
    array (
      0 => '/home/sumit/public_html/html/ps-hotel-reservation-system/admin/themes/default/template/controllers/import/helpers/form/form.tpl',
      1 => 1446729267,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '4713243563b5650b68186-39168006',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'module_confirmation' => 0,
    'current' => 0,
    'token' => 0,
    'entities' => 0,
    'i' => 0,
    'entity_selected' => 0,
    'entity' => 0,
    'files_to_import' => 0,
    'path_import' => 0,
    'filename' => 0,
    'csv_selected' => 0,
    'languages' => 0,
    'lang' => 0,
    'id_language' => 0,
    'separator_selected' => 0,
    'multiple_value_separator_selected' => 0,
    'available_fields' => 0,
    'PS_ADVANCED_STOCK_MANAGEMENT' => 0,
    'post_max_size' => 0,
    'truncateAuthorized' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_563b5650c63040_33552409',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_563b5650c63040_33552409')) {function content_563b5650c63040_33552409($_smarty_tpl) {?>
<div class="leadin"></div>
<?php if ($_smarty_tpl->tpl_vars['module_confirmation']->value) {?>
<div class="alert alert-success clearfix">
	<?php echo smartyTranslate(array('s'=>'Your .CSV file has been successfully imported into your shop. Don\'t forget to re-build the products\' search index.'),$_smarty_tpl);?>

</div>
<?php }?>
<div class="row">
	<div class="col-lg-8">
		
		<div class="panel">
			<h3>
				<i class="icon-upload"></i>
				<?php echo smartyTranslate(array('s'=>'Import'),$_smarty_tpl);?>

			</h3>
			<div class="alert alert-info">
				<ul class="list-unstyled">
					<li><?php echo smartyTranslate(array('s'=>'You can read information on CSV import at:'),$_smarty_tpl);?>

						<a href="http://doc.prestashop.com/display/PS16/CSV+Import+Parameters" class="_blank">http://doc.prestashop.com/display/PS16/CSV+Import+Parameters</a>
					</li>
					<li><?php echo smartyTranslate(array('s'=>'Read more about the CSV format at:'),$_smarty_tpl);?>

						<a href="http://en.wikipedia.org/wiki/Comma-separated_values" class="_blank">http://en.wikipedia.org/wiki/Comma-separated_values</a>
					</li>
				</ul>
			</div>
			<hr />
			<form id="preview_import" action="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['current']->value, ENT_QUOTES, 'UTF-8', true);?>
&amp;token=<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['token']->value, ENT_QUOTES, 'UTF-8', true);?>
" method="post" enctype="multipart/form-data" class="form-horizontal">
				<div class="form-group">
					<label for="entity" class="control-label col-lg-4"><?php echo smartyTranslate(array('s'=>'What kind of entity would you like to import?'),$_smarty_tpl);?>
 </label>
					<div class="col-lg-8">
						<select name="entity" id="entity" class="fixed-width-xxl form-control">
							<?php  $_smarty_tpl->tpl_vars['i'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['i']->_loop = false;
 $_smarty_tpl->tpl_vars['entity'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['entities']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['i']->key => $_smarty_tpl->tpl_vars['i']->value) {
$_smarty_tpl->tpl_vars['i']->_loop = true;
 $_smarty_tpl->tpl_vars['entity']->value = $_smarty_tpl->tpl_vars['i']->key;
?>
							<option value="<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
"<?php if ($_smarty_tpl->tpl_vars['entity_selected']->value==$_smarty_tpl->tpl_vars['i']->value) {?> selected="selected"<?php }?>>
								<?php echo $_smarty_tpl->tpl_vars['entity']->value;?>

							</option>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="alert alert-warning import_products_categories">
					<ul>
						<li><?php echo smartyTranslate(array('s'=>'Note that the Category import does not support having two categories with the same name.'),$_smarty_tpl);?>
</li>
						<li><?php echo smartyTranslate(array('s'=>'Note that you can have several products with the same reference.'),$_smarty_tpl);?>
</li>
					</ul>
				</div>
				<div class="alert alert-warning import_supply_orders_details">
					<p><?php echo smartyTranslate(array('s'=>'Importing Supply Order Details will reset your history of ordered products, if there are any.'),$_smarty_tpl);?>
</p>
				</div>
				<hr />
				<div class="form-group" id="csv_file_uploader">
					<label for="file" class="control-label col-lg-4"><?php echo smartyTranslate(array('s'=>'Select a CSV file to import'),$_smarty_tpl);?>
</label>
					<div class="col-lg-8">
						<input id="file" type="file" name="file" data-url="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['current']->value, ENT_QUOTES, 'UTF-8', true);?>
&amp;token=<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['token']->value, ENT_QUOTES, 'UTF-8', true);?>
&amp;ajax=1&amp;action=uploadCsv" class="hide" />
						<button class="ladda-button btn btn-default" data-style="expand-right" data-size="s" type="button" id="file-add-button">
							<i class="icon-folder-open"></i>
							<?php echo smartyTranslate(array('s'=>'Upload a file'),$_smarty_tpl);?>

						</button>
						<?php echo smartyTranslate(array('s'=>'or'),$_smarty_tpl);?>

						<button class="btn btn-default csv-history-btn" type="button">
							<span class="csv-history-nb badge"><?php echo count($_smarty_tpl->tpl_vars['files_to_import']->value);?>
</span>
							<?php echo smartyTranslate(array('s'=>"Choose from history / FTP"),$_smarty_tpl);?>

						</button>
						<p class="help-block">
							<?php echo smartyTranslate(array('s'=>'Only UTF-8 and ISO 8859-1 encodings are allowed'),$_smarty_tpl);?>
.<br/>
							<?php echo smartyTranslate(array('s'=>'You can also upload your file via FTP to the following directory: %s .','sprintf'=>$_smarty_tpl->tpl_vars['path_import']->value),$_smarty_tpl);?>

						</p>
					</div>
					<div class="alert alert-danger" id="file-errors" style="display:none"></div>
				</div>
				<div class="form-group" id="csv_files_history" style="display:none;" >
					<div class="panel">
						<div class="panel-heading">
							<?php echo smartyTranslate(array('s'=>'History of uploaded .CSV'),$_smarty_tpl);?>

							<span class="csv-history-nb badge"><?php echo count($_smarty_tpl->tpl_vars['files_to_import']->value);?>
</span>
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
											<?php echo smartyTranslate(array('s'=>'Use'),$_smarty_tpl);?>

										</button>
										<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
											<i class="icon-chevron-down"></i>
										</button>
										<ul class="dropdown-menu" role="menu">
											<li>
												<a class="csv-download-link _blank" href="#">
													<i class="icon-download"></i>
													<?php echo smartyTranslate(array('s'=>'Download'),$_smarty_tpl);?>

												</a>
											</li>
											<li class="divider"></li>
											<li>
												<a class="csv-delete-link" href="#">
													<i class="icon-trash"></i>
													<?php echo smartyTranslate(array('s'=>'Delete'),$_smarty_tpl);?>

												</a>
											</li>
										</ul>
									</div>
								</td>
							</tr>
							<?php  $_smarty_tpl->tpl_vars['filename'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['filename']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['files_to_import']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['filename']->key => $_smarty_tpl->tpl_vars['filename']->value) {
$_smarty_tpl->tpl_vars['filename']->_loop = true;
?>
							<tr >
								<td>
									<?php echo $_smarty_tpl->tpl_vars['filename']->value;?>

								</td>
								<td>
									<div class="btn-group pull-right">
										<button type="button" data-filename="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['filename']->value, ENT_QUOTES, 'UTF-8', true);?>
" class="csv-use-btn btn btn-default">
											<i class="icon-ok"></i>
											<?php echo smartyTranslate(array('s'=>'Use'),$_smarty_tpl);?>

										</button>
										<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
											<i class="icon-chevron-down"></i>
										</button>
										<ul class="dropdown-menu" role="menu">
											<li>
												<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['current']->value, ENT_QUOTES, 'UTF-8', true);?>
&amp;token=<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['token']->value, ENT_QUOTES, 'UTF-8', true);?>
&amp;csvfilename=<?php echo urlencode($_smarty_tpl->tpl_vars['filename']->value);?>
" class="_blank">
													<i class="icon-download"></i>
													<?php echo smartyTranslate(array('s'=>'Download'),$_smarty_tpl);?>

												</a>
											</li>
											<li class="divider"></li>
											<li>
												<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['current']->value, ENT_QUOTES, 'UTF-8', true);?>
&amp;token=<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['token']->value, ENT_QUOTES, 'UTF-8', true);?>
&amp;csvfilename=<?php echo urlencode($_smarty_tpl->tpl_vars['filename']->value);?>
&amp;delete=1">
													<i class="icon-trash"></i>
													<?php echo smartyTranslate(array('s'=>'Delete'),$_smarty_tpl);?>

												</a>
											</li>
										</ul>
									</div>
								</td>
							</tr>
							<?php } ?>
						</table>
					</div>
				</div>
				<div class="form-group" id="csv_file_selected" style="display: none;">
					<div class="alert alert-success clearfix">
						<input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['csv_selected']->value;?>
" name="csv" id="csv_selected_value" />
						<div class="col-lg-8">
							<span id="csv_selected_filename"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['csv_selected']->value, ENT_QUOTES, 'UTF-8', true);?>
</span>
						</div>
						<div class="col-lg-4">
							<div class="btn-group pull-right">
								<button id="file-remove-button" type="button" class="btn btn-default">
									<i class="icon-refresh"></i>
									<?php echo smartyTranslate(array('s'=>'Change'),$_smarty_tpl);?>

								</button>
							</div>
						</div>
					</div>
				</div>
				<hr />
				<div class="form-group">
					<label for="iso_lang" class="control-label col-lg-4">
						<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="<?php echo smartyTranslate(array('s'=>'The locale must be installed'),$_smarty_tpl);?>
">
							<?php echo smartyTranslate(array('s'=>'Language of the file'),$_smarty_tpl);?>

						</span>
					</label>
					<div class="col-lg-8">
						<select id="iso_lang" name="iso_lang" class="fixed-width-xl form-control">
							<?php  $_smarty_tpl->tpl_vars['lang'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['lang']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['languages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['lang']->key => $_smarty_tpl->tpl_vars['lang']->value) {
$_smarty_tpl->tpl_vars['lang']->_loop = true;
?>
								<option value="<?php echo $_smarty_tpl->tpl_vars['lang']->value['iso_code'];?>
" <?php if ($_smarty_tpl->tpl_vars['lang']->value['id_lang']==$_smarty_tpl->tpl_vars['id_language']->value) {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['lang']->value['name'];?>
</option>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="convert" class="control-label col-lg-4"><?php echo smartyTranslate(array('s'=>'ISO 8859-1 encoded file?'),$_smarty_tpl);?>
</label>
					<div class="col-lg-8">
						<label class="switch-light prestashop-switch fixed-width-lg">
							<input name="convert" id="convert" type="checkbox" />
							<span>
								<span><?php echo smartyTranslate(array('s'=>'Yes'),$_smarty_tpl);?>
</span>
								<span><?php echo smartyTranslate(array('s'=>'No'),$_smarty_tpl);?>
</span>
							</span>
							<a class="slide-button btn"></a>
						</label>
					</div>
				</div>
				<div class="form-group">
					<label for="separator" class="control-label col-lg-4"><?php echo smartyTranslate(array('s'=>'Field separator'),$_smarty_tpl);?>
</label>
					<div class="col-lg-8">
						<input id="separator" name="separator" class="fixed-width-xs form-control" type="text" value="<?php if (isset($_smarty_tpl->tpl_vars['separator_selected']->value)) {?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['separator_selected']->value, ENT_QUOTES, 'UTF-8', true);?>
<?php } else { ?>;<?php }?>" />
						<div class="help-block"><?php echo smartyTranslate(array('s'=>'e.g. '),$_smarty_tpl);?>
 1; Blouse; 129.90; 5</div>
					</div>
				</div>
				<div class="form-group">
					<label for="multiple_value_separator" class="control-label col-lg-4"><?php echo smartyTranslate(array('s'=>'Multiple value separator'),$_smarty_tpl);?>
</label>
					<div class="col-lg-8">
						<input id="multiple_value_separator" name="multiple_value_separator" class="fixed-width-xs form-control" type="text" value="<?php if (isset($_smarty_tpl->tpl_vars['multiple_value_separator_selected']->value)) {?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['multiple_value_separator_selected']->value, ENT_QUOTES, 'UTF-8', true);?>
<?php } else { ?>,<?php }?>" />
						<div class="help-block"><?php echo smartyTranslate(array('s'=>'e.g. '),$_smarty_tpl);?>
 Blouse; red.jpg, blue.jpg, green.jpg; 129.90</div>
					</div>
				</div>
				<hr />
				<div class="form-group">
					<label for="truncate" class="control-label col-lg-4"><?php echo smartyTranslate(array('s'=>'Delete all'),$_smarty_tpl);?>
 <span id="entitie"><?php echo smartyTranslate(array('s'=>'categories'),$_smarty_tpl);?>
</span> <?php echo smartyTranslate(array('s'=>'before import'),$_smarty_tpl);?>
 </label>
					<div class="col-lg-8">
						<label class="switch-light prestashop-switch fixed-width-lg">
							<input id="truncate" name="truncate" type="checkbox"/>
							<span>
								<span><?php echo smartyTranslate(array('s'=>'Yes'),$_smarty_tpl);?>
</span>
								<span><?php echo smartyTranslate(array('s'=>'No'),$_smarty_tpl);?>
</span>
							</span>
							<a class="slide-button btn"></a>
						</label>
					</div>
				</div>
				<div class="form-group" style="display: none">
					<label for="match_ref" class="control-label col-lg-4">
						<span data-toggle="tooltip" class="label-tooltip" data-original-title="<?php echo smartyTranslate(array('s'=>'If enabled, the product\'s reference number MUST be unique!'),$_smarty_tpl);?>
">
							<?php echo smartyTranslate(array('s'=>'Use product reference as key'),$_smarty_tpl);?>

						</span>
					</label>
					<div class="col-lg-8">
						<label class="switch-light prestashop-switch fixed-width-lg">
							<input id="match_ref" name="match_ref" type="checkbox" />
							<span>
								<span><?php echo smartyTranslate(array('s'=>'Yes'),$_smarty_tpl);?>
</span>
								<span><?php echo smartyTranslate(array('s'=>'No'),$_smarty_tpl);?>
</span>
							</span>
							<a class="slide-button btn"></a>
						</label>
					</div>
				</div>
				<div class="form-group">
					<label for="regenerate" class="control-label col-lg-4"><?php echo smartyTranslate(array('s'=>'Skip thumbnails regeneration'),$_smarty_tpl);?>
</label>
					<div class="col-lg-8">
						<label class="switch-light prestashop-switch fixed-width-lg">
							<input id="regenerate" name="regenerate" type="checkbox" />
							<span>
								<span><?php echo smartyTranslate(array('s'=>'Yes'),$_smarty_tpl);?>
</span>
								<span><?php echo smartyTranslate(array('s'=>'No'),$_smarty_tpl);?>
</span>
							</span>
							<a class="slide-button btn"></a>
						</label>
					</div>
				</div>
				<div class="form-group">
					<label for="forceIDs" class="control-label col-lg-4">
						<span data-toggle="tooltip" class="label-tooltip" data-original-title="<?php echo smartyTranslate(array('s'=>'If you enable this option, your imported items\' ID number will be used as-is. If you do not enable this option, the imported ID number will be ignored, and PrestaShop will instead create auto-incremented ID numbers for all the imported items.'),$_smarty_tpl);?>
">
							<?php echo smartyTranslate(array('s'=>'Force all ID numbers'),$_smarty_tpl);?>

						</span>
					</label>
					<div class="col-lg-8">
						<label class="switch-light prestashop-switch fixed-width-lg">
							<input  id="forceIDs" name="forceIDs" type="checkbox"/>
							<span>
								<span><?php echo smartyTranslate(array('s'=>'Yes'),$_smarty_tpl);?>
</span>
								<span><?php echo smartyTranslate(array('s'=>'No'),$_smarty_tpl);?>
</span>
							</span>
							<a class="slide-button btn"></a>
						</label>
					</div>
				</div>
<!--
				
				<div class="alert alert-info"><?php echo smartyTranslate(array('s'=>'You must upload a file in order to proceed to the next step'),$_smarty_tpl);?>
</div>
				
				<p><?php echo smartyTranslate(array('s'=>'There is no CSV file available. Please upload one using the \'Upload\' button above.'),$_smarty_tpl);?>
</p>
-->
				<div class="panel-footer">
					<button type="submit" name="submitImportFile" id="submitImportFile" class="btn btn-default pull-right" >
						<i class="process-icon-next"></i> <span><?php echo smartyTranslate(array('s'=>'Next step'),$_smarty_tpl);?>
</span>
					</button>
				</div>
			</form>
		</div>
	</div>
	<div class="col-lg-4">
		
		<div class="panel">
			<h3>
				<i class="icon-list-alt"></i>
				<?php echo smartyTranslate(array('s'=>'Available fields'),$_smarty_tpl);?>

			</h3>
			<div id="availableFields" class="alert alert-info">
				<?php echo $_smarty_tpl->tpl_vars['available_fields']->value;?>

			</div>
			<p><?php echo smartyTranslate(array('s'=>'* Required field'),$_smarty_tpl);?>
</p>
		</div>
		<div class="panel">
			<div class="panel-heading">
				<i class="icon-download"></i>
				<?php echo smartyTranslate(array('s'=>'Download sample csv files'),$_smarty_tpl);?>

			</div>

			<div class="list-group">
				<a class="list-group-item _blank" href="../docs/csv_import/categories_import.csv">
					<?php echo smartyTranslate(array('s'=>'Sample Categories file'),$_smarty_tpl);?>

				</a>
				<a class="list-group-item _blank" href="../docs/csv_import/products_import.csv">
					<?php echo smartyTranslate(array('s'=>'Sample Products file'),$_smarty_tpl);?>

				</a>
				<a class="list-group-item _blank" href="../docs/csv_import/combinations_import.csv">
					<?php echo smartyTranslate(array('s'=>'Sample Combinations file'),$_smarty_tpl);?>

				</a>
				<a class="list-group-item _blank" href="../docs/csv_import/customers_import.csv">
					<?php echo smartyTranslate(array('s'=>'Sample Customers file'),$_smarty_tpl);?>

				</a>
				<a class="list-group-item _blank" href="../docs/csv_import/addresses_import.csv">
					<?php echo smartyTranslate(array('s'=>'Sample Addresses file'),$_smarty_tpl);?>

				</a>
				<a class="list-group-item _blank" href="../docs/csv_import/manufacturers_import.csv">
					<?php echo smartyTranslate(array('s'=>'Sample Manufacturers file'),$_smarty_tpl);?>

				</a>
				<a class="list-group-item _blank" href="../docs/csv_import/suppliers_import.csv">
					<?php echo smartyTranslate(array('s'=>'Sample Suppliers file'),$_smarty_tpl);?>

				</a>
				<a class="list-group-item _blank" href="../docs/csv_import/alias_import.csv">
					<?php echo smartyTranslate(array('s'=>'Sample Aliases file'),$_smarty_tpl);?>

				</a>
				<?php if ($_smarty_tpl->tpl_vars['PS_ADVANCED_STOCK_MANAGEMENT']->value) {?>
				<a class="list-group-item _blank" href="../docs/csv_import/supply_orders_import.csv">
					<?php echo smartyTranslate(array('s'=>'Sample Supply Orders file'),$_smarty_tpl);?>

				</a>
				<a class="list-group-item _blank" href="../docs/csv_import/supply_orders_details_import.csv">
					<?php echo smartyTranslate(array('s'=>'Sample Supply Order Details file'),$_smarty_tpl);?>

				</a>
				<?php }?>
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
			<?php if (isset($_smarty_tpl->tpl_vars['post_max_size']->value)) {?>maxFileSize: <?php echo $_smarty_tpl->tpl_vars['post_max_size']->value;?>
,<?php }?>
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
							row.find('a.csv-download-link').attr('href','<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['current']->value, ENT_QUOTES, 'UTF-8', true);?>
&token=<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['token']->value, ENT_QUOTES, 'UTF-8', true);?>
&csvfilename='+filename);
							row.find('a.csv-delete-link').attr('href','<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['current']->value, ENT_QUOTES, 'UTF-8', true);?>
&token=<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['token']->value, ENT_QUOTES, 'UTF-8', true);?>
&csvfilename='+filename+'&delete=1');
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
		var selected = '<?php echo $_smarty_tpl->tpl_vars['csv_selected']->value;?>
';
		if(selected){
			$('#csv_file_selected').show();
			$('#csv_file_uploader').hide();
		}

		var truncateAuthorized = <?php echo intval($_smarty_tpl->tpl_vars['truncateAuthorized']->value);?>
;

		enableHistory();

		$('#preview_import').submit(function(e) {
			if ($('#truncate').get(0).checked) {
				if (truncateAuthorized) {
					if (!confirm('<?php echo smartyTranslate(array('s'=>'Are you sure that you would like to delete this entity: ','js'=>1),$_smarty_tpl);?>
' + ' ' + $.trim($('#entity > option:selected').text().toLowerCase()) + '?'))
						e.preventDefault();
				}
				else {
					jAlert('<?php echo smartyTranslate(array('s'=>'You do not have permission to delete this. When the MultiStore mode is enabled, only a SuperAdmin can delete all items before an import.','js'=>1),$_smarty_tpl);?>
');
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
				$('input[name=multiple_value_separator]').val('<?php if (isset($_smarty_tpl->tpl_vars['multiple_value_separator_selected']->value)) {?><?php echo $_smarty_tpl->tpl_vars['multiple_value_separator_selected']->value;?>
<?php } else { ?>,<?php }?>');
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
<?php }} ?>
