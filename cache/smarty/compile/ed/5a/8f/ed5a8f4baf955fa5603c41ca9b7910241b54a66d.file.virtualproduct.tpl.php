<?php /* Smarty version Smarty-3.1.19, created on 2015-11-05 17:58:38
         compiled from "/home/sumit/public_html/html/ps-hotel-reservation-system/admin338kklw7d/themes/default/template/controllers/products/virtualproduct.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1369568877563b4b768d4130-08216316%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ed5a8f4baf955fa5603c41ca9b7910241b54a66d' => 
    array (
      0 => '/home/sumit/public_html/html/ps-hotel-reservation-system/admin338kklw7d/themes/default/template/controllers/products/virtualproduct.tpl',
      1 => 1446725532,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1369568877563b4b768d4130-08216316',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'product' => 0,
    'product_downloaded' => 0,
    'download_product_file_missing' => 0,
    'download_dir_writable' => 0,
    'is_file' => 0,
    'virtual_product_file_uploader' => 0,
    'currentIndex' => 0,
    'token' => 0,
    'error_product_download' => 0,
    'link' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_563b4b7697e1c9_86370848',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_563b4b7697e1c9_86370848')) {function content_563b4b7697e1c9_86370848($_smarty_tpl) {?>

<script type="text/javascript">
	var newLabel = '<?php echo smartyTranslate(array('s'=>'New label'),$_smarty_tpl);?>
';
	var choose_language = '<?php echo smartyTranslate(array('s'=>'Choose language:'),$_smarty_tpl);?>
';
	var required = '<?php echo smartyTranslate(array('s'=>'Required'),$_smarty_tpl);?>
';
	var customizationUploadableFileNumber = '<?php echo $_smarty_tpl->tpl_vars['product']->value->uploadable_files;?>
';
	var customizationTextFieldNumber = '<?php echo $_smarty_tpl->tpl_vars['product']->value->text_fields;?>
';
	var uploadableFileLabel = 0;
	var textFieldLabel = 0;
</script>
<div id="product-virtualproduct" class="panel product-tab">
	<input type="hidden" name="submitted_tabs[]" value="VirtualProduct" />
	<input type="hidden" id="virtual_product_filename" name="virtual_product_filename" value="<?php echo $_smarty_tpl->tpl_vars['product']->value->productDownload->filename;?>
" />
	<h3><?php echo smartyTranslate(array('s'=>'Virtual Product (services, booking or downloadable products)'),$_smarty_tpl);?>
</h3>
	<div class="is_virtual_good" class="form-group">
		<input type="checkbox" id="is_virtual_good" name="is_virtual_good" value="true" <?php if ($_smarty_tpl->tpl_vars['product']->value->is_virtual&&$_smarty_tpl->tpl_vars['product']->value->productDownload->active) {?>checked="checked"<?php }?> />
		<label for="is_virtual_good" class="t bold"><?php echo smartyTranslate(array('s'=>'Is this a virtual product?'),$_smarty_tpl);?>
</label>
	</div>
	<div id="virtual_good" <?php if (!$_smarty_tpl->tpl_vars['product']->value->productDownload->id||$_smarty_tpl->tpl_vars['product']->value->productDownload->active) {?>style="display:none"<?php }?> class="form-group">
		<div class="form-group">
			<label class="control-label col-lg-3"><?php echo smartyTranslate(array('s'=>'Does this product have an associated file?'),$_smarty_tpl);?>
</label>
			<div class="col-lg-2">
				<span class="switch prestashop-switch">
					<input type="radio" name="is_virtual_file" id="is_virtual_file_on" value="1" <?php if ($_smarty_tpl->tpl_vars['product_downloaded']->value) {?> checked="checked"<?php }?> />
					<label for="is_virtual_file_on"><?php echo smartyTranslate(array('s'=>'Yes'),$_smarty_tpl);?>
</label>
					<input type="radio" name="is_virtual_file" id="is_virtual_file_off" value="0" <?php if (!$_smarty_tpl->tpl_vars['product_downloaded']->value) {?> checked="checked"<?php }?> />
					<label for="is_virtual_file_off"><?php echo smartyTranslate(array('s'=>'No'),$_smarty_tpl);?>
</label>
					<a class="slide-button btn"></a>
				</span>
			</div>
		</div>
		<div id="is_virtual_file_product" style="display:none;">
			<?php if ($_smarty_tpl->tpl_vars['download_product_file_missing']->value) {?>
			<div class="form-group">
				<div class="col-lg-push-3 col-lg-9">
					<div class="alert alert-danger" id="file_missing">
						<?php echo $_smarty_tpl->tpl_vars['download_product_file_missing']->value;?>
 :<br/>
						<strong><?php echo smartyTranslate(array('s'=>sprintf('Server file name : %s',$_smarty_tpl->tpl_vars['product']->value->productDownload->filename)),$_smarty_tpl);?>
</strong>
					</div>
				</div>
			</div>
			<?php }?>
			<?php if (!$_smarty_tpl->tpl_vars['download_dir_writable']->value) {?>
			<div class="form-group">
				<div class="col-lg-push-3 col-lg-9">
					<div class="alert alert-danger">
						<?php echo smartyTranslate(array('s'=>'Your download repository is not writable.'),$_smarty_tpl);?>

					</div>
				</div>
			</div>
			<?php }?>
			
			<?php if (empty($_smarty_tpl->tpl_vars['product']->value->cache_default_attribute)) {?>
				<?php if ($_smarty_tpl->tpl_vars['product']->value->productDownload->id) {?>
					<input type="hidden" id="virtual_product_id" name="virtual_product_id" value="<?php echo $_smarty_tpl->tpl_vars['product']->value->productDownload->id;?>
" />
				<?php }?>
				<div class="form-group"<?php if ($_smarty_tpl->tpl_vars['is_file']->value) {?> style="display:none"<?php }?>>
					<label id="virtual_product_file_label" for="virtual_product_file" class="control-label col-lg-3">
						<span class="label-tooltip" data-toggle="tooltip"
							title="<?php echo smartyTranslate(array('s'=>'Upload a file from your computer'),$_smarty_tpl);?>
 (<?php echo sprintf('%.2f',Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE'));?>
 <?php echo smartyTranslate(array('s'=>'MB max.'),$_smarty_tpl);?>
)">
							<?php echo smartyTranslate(array('s'=>'File'),$_smarty_tpl);?>

						</span>
					</label>
					<div class="col-lg-5">
					<?php echo $_smarty_tpl->tpl_vars['virtual_product_file_uploader']->value;?>

					<p class="help-block"><?php echo smartyTranslate(array('s'=>'Upload a file from your computer'),$_smarty_tpl);?>
 (<?php echo sprintf('%.2f',Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE'));?>
 <?php echo smartyTranslate(array('s'=>'MB max.'),$_smarty_tpl);?>
)</p>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-lg-3 required"><?php echo smartyTranslate(array('s'=>'Filename'),$_smarty_tpl);?>
</label>
					<div class="col-lg-5">
						<input type="text" id="virtual_product_name" name="virtual_product_name" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['product']->value->productDownload->display_filename, ENT_QUOTES, 'UTF-8', true);?>
" />
						<p class="help-block"><?php echo smartyTranslate(array('s'=>'The full filename with its extension (e.g. Book.pdf)'),$_smarty_tpl);?>
</p>
					</div>
				</div>
				<?php if ($_smarty_tpl->tpl_vars['is_file']->value) {?>
				<div class="form-group">
					<label class="control-label col-lg-3"><?php echo smartyTranslate(array('s'=>'Link to the file:'),$_smarty_tpl);?>
</label>
					<div class="col-lg-5">
						<a href="<?php echo $_smarty_tpl->tpl_vars['product']->value->productDownload->getTextLink(true);?>
" class="btn btn-default"><i class="icon-download"></i> <?php echo smartyTranslate(array('s'=>'Download file'),$_smarty_tpl);?>
</a>
						<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['currentIndex']->value, ENT_QUOTES, 'UTF-8', true);?>
&amp;deleteVirtualProduct=true&amp;updateproduct&amp;token=<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['token']->value, ENT_QUOTES, 'UTF-8', true);?>
&amp;id_product=<?php echo $_smarty_tpl->tpl_vars['product']->value->id;?>
" class="btn btn-default" onclick="return confirm('<?php echo smartyTranslate(array('s'=>'Do you really want to delete this file?','js'=>1),$_smarty_tpl);?>
');"><i class="icon-trash"></i> <?php echo smartyTranslate(array('s'=>'Delete this file'),$_smarty_tpl);?>
</a>
					</div>
				</div>
				<?php }?>
				<div class="form-group">
					<label class="control-label col-lg-3"><?php echo smartyTranslate(array('s'=>'Number of allowed downloads'),$_smarty_tpl);?>
</label>
					<div class="col-lg-3">
						<input type="text" id="virtual_product_nb_downloable" name="virtual_product_nb_downloable" value="<?php echo htmlentities($_smarty_tpl->tpl_vars['product']->value->productDownload->nb_downloadable);?>
" class="" size="6" />
						<p class="help-block"><?php echo smartyTranslate(array('s'=>'Number of downloads allowed per customer. Set to 0 for unlimited downloads.'),$_smarty_tpl);?>
</p>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-lg-3">
						<span class="label-tooltip" data-toggle="tooltip" title="" data-original-title="<?php echo smartyTranslate(array('s'=>'Format: YYYY-MM-DD.'),$_smarty_tpl);?>
">
							<?php echo smartyTranslate(array('s'=>'Expiration date'),$_smarty_tpl);?>

						</span>
					</label>
					<div class="col-lg-5">
						<input class="datepicker" type="text" id="virtual_product_expiration_date" name="virtual_product_expiration_date" value="<?php echo $_smarty_tpl->tpl_vars['product']->value->productDownload->date_expiration;?>
" size="11" maxlength="10" autocomplete="off" />
						<p class="help-block"><?php echo smartyTranslate(array('s'=>'If set, the file will not be downloadable after this date. Leave blank if you do not wish to attach an expiration date.'),$_smarty_tpl);?>
</p>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-lg-3 required"><?php echo smartyTranslate(array('s'=>'Number of days'),$_smarty_tpl);?>
</label>
					<div class="col-lg-3">
						<input type="text" id="virtual_product_nb_days" name="virtual_product_nb_days" value="<?php if (!$_smarty_tpl->tpl_vars['product']->value->productDownload->nb_days_accessible) {?>0<?php } else { ?><?php echo htmlentities($_smarty_tpl->tpl_vars['product']->value->productDownload->nb_days_accessible);?>
<?php }?>" class="" size="4" />
						<p class="help-block"><?php echo smartyTranslate(array('s'=>'Number of days this file can be accessed by customers. Set to zero for unlimited access.'),$_smarty_tpl);?>
</p>
					</div>
				</div>
				
				
					
					
						
						
					
				
			<?php } else { ?>
				<div class="alert alert-info">
					<?php echo smartyTranslate(array('s'=>'You cannot edit your file here because you used combinations. Please edit this file in the Combinations tab.'),$_smarty_tpl);?>

				</div>
				<?php if (isset($_smarty_tpl->tpl_vars['error_product_download']->value)) {?><?php echo $_smarty_tpl->tpl_vars['error_product_download']->value;?>
<?php }?>
			<?php }?>
		</div>
	</div>
	<div class="panel-footer">
		<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminProducts'), ENT_QUOTES, 'UTF-8', true);?>
<?php if (isset($_REQUEST['page'])&&$_REQUEST['page']>1) {?>&amp;submitFilterproduct=<?php echo intval($_REQUEST['page']);?>
<?php }?>" class="btn btn-default"><i class="process-icon-cancel"></i> <?php echo smartyTranslate(array('s'=>'Cancel'),$_smarty_tpl);?>
</a>
		<button type="submit" name="submitAddproduct" class="btn btn-default pull-right" disabled="disabled"><i class="process-icon-loading"></i> <?php echo smartyTranslate(array('s'=>'Save'),$_smarty_tpl);?>
</button>
		<button type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right" disabled="disabled"><i class="process-icon-loading"></i> <?php echo smartyTranslate(array('s'=>'Save and stay'),$_smarty_tpl);?>
</button>
	</div>
</div>
<?php }} ?>
