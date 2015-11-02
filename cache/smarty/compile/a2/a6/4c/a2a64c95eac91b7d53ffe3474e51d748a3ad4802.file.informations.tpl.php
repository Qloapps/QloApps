<?php /* Smarty version Smarty-3.1.19, created on 2015-11-02 11:05:16
         compiled from "/home/sumit/public_html/html/ps-hotel-reservation-system/admin527qbaxyx/themes/default/template/controllers/products/informations.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1509080736563789bc71abc9-14500406%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a2a64c95eac91b7d53ffe3474e51d748a3ad4802' => 
    array (
      0 => '/home/sumit/public_html/html/ps-hotel-reservation-system/admin527qbaxyx/themes/default/template/controllers/products/informations.tpl',
      1 => 1446455063,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1509080736563789bc71abc9-14500406',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'check_product_association_ajax' => 0,
    'ps_force_friendly_product' => 0,
    'PS_ALLOW_ACCENTED_CHARS_URL' => 0,
    'combinationImagesJs' => 0,
    'link' => 0,
    'id_lang' => 0,
    'display_common_field' => 0,
    'bullet_common_field' => 0,
    'product_type' => 0,
    'is_in_pack' => 0,
    'languages' => 0,
    'class_input_ajax' => 0,
    'product' => 0,
    'product_name_redirected' => 0,
    'display_multishop_checkboxes' => 0,
    'PS_PRODUCT_SHORT_DESC_LIMIT' => 0,
    'images' => 0,
    'key' => 0,
    'image' => 0,
    'imagesTypes' => 0,
    'type' => 0,
    'language' => 0,
    'table' => 0,
    'default_form_language' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_563789bc90e056_81954287',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_563789bc90e056_81954287')) {function content_563789bc90e056_81954287($_smarty_tpl) {?>
<?php if ($_smarty_tpl->tpl_vars['check_product_association_ajax']->value) {?>
	<?php $_smarty_tpl->tpl_vars['class_input_ajax'] = new Smarty_variable('check_product_name ', null, 0);?>
<?php } else { ?>
	<?php $_smarty_tpl->tpl_vars['class_input_ajax'] = new Smarty_variable('', null, 0);?>
<?php }?>

<div id="product-informations" class="panel product-tab">
	<input type="hidden" name="submitted_tabs[]" value="Informations" />
	<h3 class="tab"> <i class="icon-info"></i> <?php echo smartyTranslate(array('s'=>'Information'),$_smarty_tpl);?>
</h3>
	<script type="text/javascript">

		var msg_select_one = "<?php echo smartyTranslate(array('s'=>'Please select at least one product.','js'=>1),$_smarty_tpl);?>
";
		var msg_set_quantity = "<?php echo smartyTranslate(array('s'=>'Please set a quantity to add a product.','js'=>1),$_smarty_tpl);?>
";

		<?php if (isset($_smarty_tpl->tpl_vars['ps_force_friendly_product']->value)&&$_smarty_tpl->tpl_vars['ps_force_friendly_product']->value) {?>
			var ps_force_friendly_product = 1;
		<?php } else { ?>
			var ps_force_friendly_product = 0;
		<?php }?>
		<?php if (isset($_smarty_tpl->tpl_vars['PS_ALLOW_ACCENTED_CHARS_URL']->value)&&$_smarty_tpl->tpl_vars['PS_ALLOW_ACCENTED_CHARS_URL']->value) {?>
			var PS_ALLOW_ACCENTED_CHARS_URL = 1;
		<?php } else { ?>
			var PS_ALLOW_ACCENTED_CHARS_URL = 0;
		<?php }?>
		<?php echo $_smarty_tpl->tpl_vars['combinationImagesJs']->value;?>

		<?php if ($_smarty_tpl->tpl_vars['check_product_association_ajax']->value) {?>
				var search_term = '';
				$('document').ready( function() {
					$(".check_product_name")
						.autocomplete(
							'<?php echo addslashes($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminProducts',true));?>
', {
								minChars: 3,
								max: 10,
								width: $(".check_product_name").width(),
								selectFirst: false,
								scroll: false,
								dataType: "json",
								formatItem: function(data, i, max, value, term) {
									search_term = term;
									// adding the little
									if ($('.ac_results').find('.separation').length == 0)
										$('.ac_results').css('background-color', '#EFEFEF')
											.prepend('<div style="color:#585A69; padding:2px 5px"><?php echo smartyTranslate(array('s'=>'Use a product from the list'),$_smarty_tpl);?>
<div class="separation"></div></div>');
									return value;
								},
								parse: function(data) {
									var mytab = new Array();
									for (var i = 0; i < data.length; i++)
										mytab[mytab.length] = { data: data[i], value: data[i].name };
									return mytab;
								},
								extraParams: {
									ajax: 1,
									action: 'checkProductName',
									id_lang: <?php echo $_smarty_tpl->tpl_vars['id_lang']->value;?>

								}
							}
						)
						.result(function(event, data, formatted) {
							// keep the searched term in the input
							$('#name_<?php echo $_smarty_tpl->tpl_vars['id_lang']->value;?>
').val(search_term);
							jConfirm('<?php echo smartyTranslate(array('s'=>'Do you want to use this product?'),$_smarty_tpl);?>
&nbsp;<strong>'+data.name+'</strong>', '<?php echo smartyTranslate(array('s'=>'Confirmation'),$_smarty_tpl);?>
', function(confirm){
								if (confirm == true)
									document.location.href = '<?php echo $_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminProducts',true);?>
&updateproduct&id_product='+data.id_product;
								else
									return false;
							});
						});
				});
		<?php }?>
	</script>

	<?php if (isset($_smarty_tpl->tpl_vars['display_common_field']->value)&&$_smarty_tpl->tpl_vars['display_common_field']->value) {?>
	<div class="alert alert-warning" style="display: block"><?php echo smartyTranslate(array('s'=>'Warning, if you change the value of fields with an orange bullet %s, the value will be changed for all other shops for this product','sprintf'=>$_smarty_tpl->tpl_vars['bullet_common_field']->value),$_smarty_tpl);?>
</div>
	<?php }?>

	<?php echo $_smarty_tpl->getSubTemplate ("controllers/products/multishop/check_fields.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array('product_tab'=>"Informations"), 0);?>


	<div class="form-group">
		<label class="control-label col-lg-3" for="simple_product">
			<?php echo $_smarty_tpl->tpl_vars['bullet_common_field']->value;?>
 <?php echo smartyTranslate(array('s'=>'Type'),$_smarty_tpl);?>

		</label>
		<div class="col-lg-9">
			<div class="radio">
				<label for="simple_product">
					<input type="radio" name="type_product" id="simple_product" value="<?php echo Product::PTYPE_SIMPLE;?>
" <?php if ($_smarty_tpl->tpl_vars['product_type']->value==Product::PTYPE_SIMPLE) {?>checked="checked"<?php }?>>
					<?php echo smartyTranslate(array('s'=>'Standard product'),$_smarty_tpl);?>
</label>
			</div>
			<div class="radio">
				<label for="pack_product">
					<input type="radio" name="type_product" <?php if ($_smarty_tpl->tpl_vars['is_in_pack']->value) {?>disabled="disabled"<?php }?> id="pack_product" value="<?php echo Product::PTYPE_PACK;?>
" <?php if ($_smarty_tpl->tpl_vars['product_type']->value==Product::PTYPE_PACK) {?>checked="checked"<?php }?>> <?php echo smartyTranslate(array('s'=>'Pack of existing products'),$_smarty_tpl);?>
</label>
			</div>
			<div class="radio">
				<label for="virtual_product">
					<input type="radio" name="type_product" id="virtual_product" <?php if ($_smarty_tpl->tpl_vars['is_in_pack']->value) {?>disabled="disabled"<?php }?> value="<?php echo Product::PTYPE_VIRTUAL;?>
 <?php if ($_smarty_tpl->tpl_vars['product_type']->value==Product::PTYPE_VIRTUAL) {?>checked="checked"<?php }?>">
					<?php echo smartyTranslate(array('s'=>'Virtual product (services, booking, downloadable products, etc.)'),$_smarty_tpl);?>
</label>
			</div>
			<div class="row row-padding-top">
				<div id="warn_virtual_combinations" class="alert alert-warning" style="display:none"><?php echo smartyTranslate(array('s'=>'You cannot use combinations with a virtual product.'),$_smarty_tpl);?>
</div>
				<div id="warn_pack_combinations" class="alert alert-warning" style="display:none"><?php echo smartyTranslate(array('s'=>'You cannot use combinations with a pack.'),$_smarty_tpl);?>
</div>
			</div>
		</div>
	</div>

	<div id="product-pack-container" <?php if ($_smarty_tpl->tpl_vars['product_type']->value!=Product::PTYPE_PACK) {?>style="display:none"<?php }?>></div>

	<hr />

	<div class="form-group">
		<div class="col-lg-1"><span class="pull-right"><?php echo $_smarty_tpl->getSubTemplate ("controllers/products/multishop/checkbox.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array('field'=>"name",'type'=>"default",'multilang'=>"true"), 0);?>
</span></div>
		<label class="control-label col-lg-2 required" for="name_<?php echo $_smarty_tpl->tpl_vars['id_lang']->value;?>
">
			<span class="label-tooltip" data-toggle="tooltip" title="<?php echo smartyTranslate(array('s'=>'The public name for this product.'),$_smarty_tpl);?>
 <?php echo smartyTranslate(array('s'=>'Invalid characters:'),$_smarty_tpl);?>
 &lt;&gt;;=#{}">
				<?php echo smartyTranslate(array('s'=>'Room Type'),$_smarty_tpl);?>

			</span>
		</label>
		<div class="col-lg-5">
			<?php ob_start();?><?php if (!$_smarty_tpl->tpl_vars['product']->value->id||Configuration::get('PS_FORCE_FRIENDLY_PRODUCT')) {?><?php echo "copy2friendlyUrl";?><?php }?><?php $_tmp1=ob_get_clean();?><?php echo $_smarty_tpl->getSubTemplate ("controllers/products/input_text_lang.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array('languages'=>$_smarty_tpl->tpl_vars['languages']->value,'input_class'=>((string)$_smarty_tpl->tpl_vars['class_input_ajax']->value).$_tmp1." updateCurrentText",'input_value'=>$_smarty_tpl->tpl_vars['product']->value->name,'input_name'=>"name",'required'=>true), 0);?>

		</div>
	</div>

	<div class="form-group hidden">
		<label class="control-label col-lg-3" for="reference">
			<span class="label-tooltip" data-toggle="tooltip"
			title="<?php echo smartyTranslate(array('s'=>'Your internal reference code for this product.'),$_smarty_tpl);?>
 <?php echo smartyTranslate(array('s'=>'Allowed special characters:'),$_smarty_tpl);?>
 .-_#\">
				<?php echo $_smarty_tpl->tpl_vars['bullet_common_field']->value;?>
 <?php echo smartyTranslate(array('s'=>'Reference code'),$_smarty_tpl);?>

			</span>
		</label>
		<div class="col-lg-5">
			<input type="text" id="reference" name="reference" value="<?php echo smarty_modifier_htmlentitiesUTF8($_smarty_tpl->tpl_vars['product']->value->reference);?>
" />
		</div>
	</div>

	<div class="form-group hidden">
		<label class="control-label col-lg-3" for="ean13">
			<span class="label-tooltip" data-toggle="tooltip"
				title="<?php echo smartyTranslate(array('s'=>'This type of product code is specific to Europe and Japan, but is widely used internationally. It is a superset of the UPC code: all products marked with an EAN will be accepted in North America.'),$_smarty_tpl);?>
">
				<?php echo $_smarty_tpl->tpl_vars['bullet_common_field']->value;?>
 <?php echo smartyTranslate(array('s'=>'EAN-13 or JAN barcode'),$_smarty_tpl);?>

			</span>
		</label>
		<div class="col-lg-3">
			<input maxlength="13" type="text" id="ean13" name="ean13" value="<?php echo smarty_modifier_htmlentitiesUTF8($_smarty_tpl->tpl_vars['product']->value->ean13);?>
" />
		</div>
	</div>

	<div class="form-group hidden">
		<label class="control-label col-lg-3" for="upc">
			<span class="label-tooltip" data-toggle="tooltip"
				title="<?php echo smartyTranslate(array('s'=>'This type of product code is widely used in the United States, Canada, the United Kingdom, Australia, New Zealand and in other countries.'),$_smarty_tpl);?>
">
				<?php echo $_smarty_tpl->tpl_vars['bullet_common_field']->value;?>
 <?php echo smartyTranslate(array('s'=>'UPC barcode'),$_smarty_tpl);?>

			</span>
		</label>
		<div class="col-lg-3">
			<input maxlength="12" type="text" id="upc" name="upc" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['product']->value->upc, ENT_QUOTES, 'UTF-8', true);?>
" />
		</div>
	</div>

	<hr/>

	
	<div class="form-group">
		<div class="col-lg-1"><span class="pull-right"><?php echo $_smarty_tpl->getSubTemplate ("controllers/products/multishop/checkbox.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array('field'=>"active",'type'=>"radio",'onclick'=>''), 0);?>
</span></div>
		<label class="control-label col-lg-2">
			<?php echo smartyTranslate(array('s'=>'Enabled'),$_smarty_tpl);?>

		</label>
		<div class="col-lg-9">
			<span class="switch prestashop-switch fixed-width-lg">
				<input onclick="toggleDraftWarning(false);showOptions(true);showRedirectProductOptions(false);" type="radio" name="active" id="active_on" value="1" <?php if ($_smarty_tpl->tpl_vars['product']->value->active||!$_smarty_tpl->tpl_vars['product']->value->isAssociatedToShop()) {?>checked="checked" <?php }?> />
				<label for="active_on" class="radioCheck">
					<?php echo smartyTranslate(array('s'=>'Yes'),$_smarty_tpl);?>

				</label>
				<input onclick="toggleDraftWarning(true);showOptions(false);showRedirectProductOptions(true);"  type="radio" name="active" id="active_off" value="0" <?php if (!$_smarty_tpl->tpl_vars['product']->value->active&&$_smarty_tpl->tpl_vars['product']->value->isAssociatedToShop()) {?>checked="checked"<?php }?> />
				<label for="active_off" class="radioCheck">
					<?php echo smartyTranslate(array('s'=>'No'),$_smarty_tpl);?>

				</label>
				<a class="slide-button btn"></a>
			</span>
		</div>
	</div>

	<div class="form-group redirect_product_options" style="display:none">
		<div class="col-lg-1"><span class="pull-right"><?php echo $_smarty_tpl->getSubTemplate ("controllers/products/multishop/checkbox.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array('field'=>"redirect_type",'type'=>"radio",'onclick'=>''), 0);?>
</span></div>
		<label class="control-label col-lg-2" for="redirect_type">
			<?php echo smartyTranslate(array('s'=>'Redirect when disabled'),$_smarty_tpl);?>

		</label>
		<div class="col-lg-5">
			<select name="redirect_type" id="redirect_type">
				<option value="404" <?php if ($_smarty_tpl->tpl_vars['product']->value->redirect_type=='404') {?> selected="selected" <?php }?>><?php echo smartyTranslate(array('s'=>'No redirect (404)'),$_smarty_tpl);?>
</option>
				<option value="301" <?php if ($_smarty_tpl->tpl_vars['product']->value->redirect_type=='301') {?> selected="selected" <?php }?>><?php echo smartyTranslate(array('s'=>'Redirected permanently (301)'),$_smarty_tpl);?>
</option>
				<option value="302" <?php if ($_smarty_tpl->tpl_vars['product']->value->redirect_type=='302') {?> selected="selected" <?php }?>><?php echo smartyTranslate(array('s'=>'Redirected temporarily (302)'),$_smarty_tpl);?>
</option>
			</select>
		</div>
	</div>
	<div class="form-group redirect_product_options" style="display:none">
		<div class="col-lg-9 col-lg-offset-3">
			<div class="alert alert-info">
				<?php echo smartyTranslate(array('s'=>'404 Not Found = Do not redirect and display a 404 page.'),$_smarty_tpl);?>
<br/>
				<?php echo smartyTranslate(array('s'=>'301 Moved Permanently = Permanently display another product instead.'),$_smarty_tpl);?>
<br/>
				<?php echo smartyTranslate(array('s'=>'302 Moved Temporarily = Temporarily display another product instead.'),$_smarty_tpl);?>

			</div>
		</div>
	</div>

	<div class="form-group redirect_product_options redirect_product_options_product_choise" style="display:none">
		<div class="col-lg-1"><span class="pull-right"><?php echo $_smarty_tpl->getSubTemplate ("controllers/products/multishop/checkbox.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array('field'=>"id_product_redirected",'type'=>"radio",'onclick'=>''), 0);?>
</span></div>
		<label class="control-label col-lg-2" for="related_product_autocomplete_input">
			<?php echo smartyTranslate(array('s'=>'Related product:'),$_smarty_tpl);?>

		</label>
		<div class="col-lg-7">
			<input type="hidden" value="" name="id_product_redirected" />

			<div class="input-group">
				<input type="text" id="related_product_autocomplete_input" name="related_product_autocomplete_input" autocomplete="off" class="ac_input" />
				<span class="input-group-addon"><i class="icon-search"></i></span>
			</div>

			<div class="form-control-static">
				<span id="related_product_name"><i class="icon-warning-sign"></i>&nbsp;<?php echo smartyTranslate(array('s'=>'No related product.'),$_smarty_tpl);?>
</span>
				<span id="related_product_remove" style="display:none">
					<a class="btn btn-default" href="#" onclick="removeRelatedProduct(); return false" id="related_product_remove_link">
						<i class="icon-remove text-danger"></i>
					</a>
				</span>
			</div>

		</div>
		<script>
			var no_related_product = '<?php echo smartyTranslate(array('s'=>'No related product'),$_smarty_tpl);?>
';
			var id_product_redirected = <?php echo intval($_smarty_tpl->tpl_vars['product']->value->id_product_redirected);?>
;
			var product_name_redirected = '<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['product_name_redirected']->value, ENT_QUOTES, 'UTF-8', true);?>
';
		</script>
	</div>

	<div class="form-group">
		<div class="col-lg-1"><span class="pull-right"><?php echo $_smarty_tpl->getSubTemplate ("controllers/products/multishop/checkbox.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array('field'=>"visibility",'type'=>"default"), 0);?>
</span></div>
		<label class="control-label col-lg-2" for="visibility">
			<?php echo smartyTranslate(array('s'=>'Visibility'),$_smarty_tpl);?>

		</label>
		<div class="col-lg-3">
			<select name="visibility" id="visibility">
				<option value="both" <?php if ($_smarty_tpl->tpl_vars['product']->value->visibility=='both') {?>selected="selected"<?php }?> ><?php echo smartyTranslate(array('s'=>'Everywhere'),$_smarty_tpl);?>
</option>
				<option value="catalog" <?php if ($_smarty_tpl->tpl_vars['product']->value->visibility=='catalog') {?>selected="selected"<?php }?> ><?php echo smartyTranslate(array('s'=>'Catalog only'),$_smarty_tpl);?>
</option>
				<option value="search" <?php if ($_smarty_tpl->tpl_vars['product']->value->visibility=='search') {?>selected="selected"<?php }?> ><?php echo smartyTranslate(array('s'=>'Search only'),$_smarty_tpl);?>
</option>
				<option value="none" <?php if ($_smarty_tpl->tpl_vars['product']->value->visibility=='none') {?>selected="selected"<?php }?>><?php echo smartyTranslate(array('s'=>'Nowhere'),$_smarty_tpl);?>
</option>
			</select>
		</div>
	</div>

	<div id="product_options" class="form-group">
		<div class="col-lg-12">
			<div class="form-group">
				<div class="col-lg-1">
					<span class="pull-right">
						<?php if (isset($_smarty_tpl->tpl_vars['display_multishop_checkboxes']->value)&&$_smarty_tpl->tpl_vars['display_multishop_checkboxes']->value) {?>
							<?php echo $_smarty_tpl->getSubTemplate ("controllers/products/multishop/checkbox.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array('only_checkbox'=>"true",'field'=>"available_for_order",'type'=>"default"), 0);?>

							<?php echo $_smarty_tpl->getSubTemplate ("controllers/products/multishop/checkbox.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array('only_checkbox'=>"true",'field'=>"show_price",'type'=>"show_price"), 0);?>

							<?php echo $_smarty_tpl->getSubTemplate ("controllers/products/multishop/checkbox.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array('only_checkbox'=>"true",'field'=>"online_only",'type'=>"default"), 0);?>

						<?php }?>
					</span>
				</div>
				<label class="control-label col-lg-2" for="available_for_order">
					<?php echo smartyTranslate(array('s'=>'Options'),$_smarty_tpl);?>

				</label>
				<div class="col-lg-9">
					<div class="checkbox">
						<label for="available_for_order">
							<input type="checkbox" name="available_for_order" id="available_for_order" value="1" <?php if ($_smarty_tpl->tpl_vars['product']->value->available_for_order) {?>checked="checked"<?php }?> >
							<?php echo smartyTranslate(array('s'=>'Available for order'),$_smarty_tpl);?>
</label>
					</div>
					<div class="checkbox">
						<label for="show_price">
							<input type="checkbox" name="show_price" id="show_price" value="1" <?php if ($_smarty_tpl->tpl_vars['product']->value->show_price) {?>checked="checked"<?php }?> <?php if ($_smarty_tpl->tpl_vars['product']->value->available_for_order) {?>disabled="disabled"<?php }?> >
							<?php echo smartyTranslate(array('s'=>'Show price'),$_smarty_tpl);?>
</label>
					</div>
					<div class="checkbox">
						<label for="online_only">
							<input type="checkbox" name="online_only" id="online_only" value="1" <?php if ($_smarty_tpl->tpl_vars['product']->value->online_only) {?>checked="checked"<?php }?> >
							<?php echo smartyTranslate(array('s'=>'Online only (not sold in your retail store)'),$_smarty_tpl);?>
</label>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-lg-1"><span class="pull-right"><?php echo $_smarty_tpl->getSubTemplate ("controllers/products/multishop/checkbox.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array('field'=>"condition",'type'=>"default"), 0);?>
</span></div>
				<label class="control-label col-lg-2" for="condition">
					<?php echo smartyTranslate(array('s'=>'Condition'),$_smarty_tpl);?>

				</label>
				<div class="col-lg-3">
					<select name="condition" id="condition">
						<option value="new" <?php if ($_smarty_tpl->tpl_vars['product']->value->condition=='new') {?>selected="selected"<?php }?> ><?php echo smartyTranslate(array('s'=>'New'),$_smarty_tpl);?>
</option>
						<option value="used" <?php if ($_smarty_tpl->tpl_vars['product']->value->condition=='used') {?>selected="selected"<?php }?> ><?php echo smartyTranslate(array('s'=>'Used'),$_smarty_tpl);?>
</option>
						<option value="refurbished" <?php if ($_smarty_tpl->tpl_vars['product']->value->condition=='refurbished') {?>selected="selected"<?php }?>><?php echo smartyTranslate(array('s'=>'Refurbished'),$_smarty_tpl);?>
</option>
					</select>
				</div>
			</div>
		</div>
	</div>
	<hr/>
	<div class="form-group">
		<div class="col-lg-1"><span class="pull-right"><?php echo $_smarty_tpl->getSubTemplate ("controllers/products/multishop/checkbox.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array('field'=>"description_short",'type'=>"tinymce",'multilang'=>"true"), 0);?>
</span></div>
		<label class="control-label col-lg-2" for="description_short_<?php echo $_smarty_tpl->tpl_vars['id_lang']->value;?>
">
			<span class="label-tooltip" data-toggle="tooltip" title="<?php echo smartyTranslate(array('s'=>'Appears in the product list(s), and at the top of the product page.'),$_smarty_tpl);?>
">
				<?php echo smartyTranslate(array('s'=>'Short description'),$_smarty_tpl);?>

			</span>
		</label>
		<div class="col-lg-9">
			<?php echo $_smarty_tpl->getSubTemplate ("controllers/products/textarea_lang.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array('languages'=>$_smarty_tpl->tpl_vars['languages']->value,'input_name'=>'description_short','class'=>"autoload_rte",'input_value'=>$_smarty_tpl->tpl_vars['product']->value->description_short,'max'=>$_smarty_tpl->tpl_vars['PS_PRODUCT_SHORT_DESC_LIMIT']->value), 0);?>

		</div>
	</div>
	<div class="form-group">
		<div class="col-lg-1"><span class="pull-right"><?php echo $_smarty_tpl->getSubTemplate ("controllers/products/multishop/checkbox.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array('field'=>"description",'type'=>"tinymce",'multilang'=>"true"), 0);?>
</span></div>
		<label class="control-label col-lg-2" for="description_<?php echo $_smarty_tpl->tpl_vars['id_lang']->value;?>
">
			<span class="label-tooltip" data-toggle="tooltip"
				title="<?php echo smartyTranslate(array('s'=>'Appears in the body of the product page.'),$_smarty_tpl);?>
">
				<?php echo smartyTranslate(array('s'=>'Description'),$_smarty_tpl);?>

			</span>
		</label>
		<div class="col-lg-9">
			<?php echo $_smarty_tpl->getSubTemplate ("controllers/products/textarea_lang.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array('languages'=>$_smarty_tpl->tpl_vars['languages']->value,'input_name'=>'description','class'=>"autoload_rte",'input_value'=>$_smarty_tpl->tpl_vars['product']->value->description), 0);?>

		</div>
	</div>
	<?php if ($_smarty_tpl->tpl_vars['images']->value) {?>
	<div class="form-group">
		<div class="col-lg-9 col-lg-offset-3">
			<div class="alert alert-info">
				<?php $_smarty_tpl->_capture_stack[0][] = array('default', null, null); ob_start(); ?><a class="addImageDescription" href="javascript:void(0);"><?php echo smartyTranslate(array('s'=>'Click here'),$_smarty_tpl);?>
</a><?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
				<?php echo smartyTranslate(array('s'=>'Would you like to add an image in your description? %s and paste the given tag in the description.','sprintf'=>Smarty::$_smarty_vars['capture']['default']),$_smarty_tpl);?>

			</div>
		</div>
	</div>
	<div id="createImageDescription" class="panel" style="display:none">
		<div class="form-group">
			<label class="control-label col-lg-3" for="smallImage_0"><?php echo smartyTranslate(array('s'=>'Select your image'),$_smarty_tpl);?>
</label>
			<div class="col-lg-9">
				<ul class="list-inline">
					<?php  $_smarty_tpl->tpl_vars['image'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['image']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['images']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['image']->key => $_smarty_tpl->tpl_vars['image']->value) {
$_smarty_tpl->tpl_vars['image']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['image']->key;
?>
					<li>
						<input type="radio" name="smallImage" id="smallImage_<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" value="<?php echo $_smarty_tpl->tpl_vars['image']->value['id_image'];?>
" <?php if ($_smarty_tpl->tpl_vars['key']->value==0) {?>checked="checked"<?php }?> >
						<label for="smallImage_<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" >
							<img src="<?php echo $_smarty_tpl->tpl_vars['image']->value['src'];?>
" alt="<?php echo $_smarty_tpl->tpl_vars['image']->value['legend'];?>
" />
						</label>
					</li>
					<?php } ?>
				</ul>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-3" for="leftRight_1"><?php echo smartyTranslate(array('s'=>'Position'),$_smarty_tpl);?>
</label>
			<div class="col-lg-5">
				<p class="checkbox">
					<input type="radio" name="leftRight" id="leftRight_1" value="left" checked>
					<label for="leftRight_1" ><?php echo smartyTranslate(array('s'=>'left'),$_smarty_tpl);?>
</label>
				</p>
				<p class="checkbox">
					<input type="radio" name="leftRight" id="leftRight_2" value="right">
					<label for="leftRight_2" ><?php echo smartyTranslate(array('s'=>'right'),$_smarty_tpl);?>
</label>
				</p>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-3" for="imageTypes_0"><?php echo smartyTranslate(array('s'=>'Select the type of picture'),$_smarty_tpl);?>
</label>
			<div class="col-lg-5">
				<?php  $_smarty_tpl->tpl_vars['type'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['type']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['imagesTypes']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['type']->key => $_smarty_tpl->tpl_vars['type']->value) {
$_smarty_tpl->tpl_vars['type']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['type']->key;
?>
				<p class="checkbox">
					<input type="radio" name="imageTypes" id="imageTypes_<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" value="<?php echo $_smarty_tpl->tpl_vars['type']->value['name'];?>
" <?php if ($_smarty_tpl->tpl_vars['key']->value==0) {?>checked="checked"<?php }?>>
					<label for="imageTypes_<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
" >
						<?php echo $_smarty_tpl->tpl_vars['type']->value['name'];?>
 <span><?php echo smartyTranslate(array('s'=>'%dpx by %dpx','sprintf'=>array($_smarty_tpl->tpl_vars['type']->value['width'],$_smarty_tpl->tpl_vars['type']->value['height'])),$_smarty_tpl);?>
</span>
					</label>
				</p>
				<?php } ?>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-3" for="resultImage">
				<span class="label-tooltip" data-toggle="tooltip"
				title="<?php echo smartyTranslate(array('s'=>'The tag to copy/paste into the description.'),$_smarty_tpl);?>
">
					<?php echo smartyTranslate(array('s'=>'Image tag to insert'),$_smarty_tpl);?>

				</span>
			</label>
			<div class="col-lg-4">
				<input type="text" id="resultImage" name="resultImage" />
			</div>
			<p class="help-block"></p>
		</div>
	</div>
	<?php }?>

	<div class="form-group">
		<label class="control-label col-lg-3" for="tags_<?php echo $_smarty_tpl->tpl_vars['id_lang']->value;?>
">
			<span class="label-tooltip" data-toggle="tooltip"
				title="<?php echo smartyTranslate(array('s'=>'Will be displayed in the tags block when enabled. Tags help customers easily find your products.'),$_smarty_tpl);?>
">
				<?php echo smartyTranslate(array('s'=>'Tags:'),$_smarty_tpl);?>

			</span>
		</label>
		<div class="col-lg-9">
			<?php if (count($_smarty_tpl->tpl_vars['languages']->value)>1) {?>
			<div class="row">
			<?php }?>
				<?php  $_smarty_tpl->tpl_vars['language'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['language']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['languages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['language']->key => $_smarty_tpl->tpl_vars['language']->value) {
$_smarty_tpl->tpl_vars['language']->_loop = true;
?>
					
					<script type="text/javascript">
						$().ready(function () {
							var input_id = 'tags_<?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
';
							$('#'+input_id).tagify({delimiters: [13,44], addTagPrompt: '<?php echo smartyTranslate(array('s'=>'Add tag','js'=>1),$_smarty_tpl);?>
'});
							$('#<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
_form').submit( function() {
								$(this).find('#'+input_id).val($('#'+input_id).tagify('serialize'));
							});
						});
					</script>
					
				<?php if (count($_smarty_tpl->tpl_vars['languages']->value)>1) {?>
				<div class="translatable-field lang-<?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
">
					<div class="col-lg-9">
				<?php }?>
						<input type="text" id="tags_<?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
" class="tagify updateCurrentText" name="tags_<?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
" value="<?php echo smarty_modifier_htmlentitiesUTF8($_smarty_tpl->tpl_vars['product']->value->getTags($_smarty_tpl->tpl_vars['language']->value['id_lang'],true));?>
" />
				<?php if (count($_smarty_tpl->tpl_vars['languages']->value)>1) {?>
					</div>
					<div class="col-lg-2">
						<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
							<?php echo $_smarty_tpl->tpl_vars['language']->value['iso_code'];?>

							<span class="caret"></span>
						</button>
						<ul class="dropdown-menu">
							<?php  $_smarty_tpl->tpl_vars['language'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['language']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['languages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['language']->key => $_smarty_tpl->tpl_vars['language']->value) {
$_smarty_tpl->tpl_vars['language']->_loop = true;
?>
							<li>
								<a href="javascript:tabs_manager.allow_hide_other_languages = false;hideOtherLanguage(<?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
);"><?php echo $_smarty_tpl->tpl_vars['language']->value['name'];?>
</a>
							</li>
							<?php } ?>
						</ul>
					</div>
				</div>
				<?php }?>
				<?php } ?>
			<?php if (count($_smarty_tpl->tpl_vars['languages']->value)>1) {?>
			</div>
			<?php }?>
		</div>
		<div class="col-lg-9 col-lg-offset-3">
			<div class="help-block"><?php echo smartyTranslate(array('s'=>'Each tag has to be followed by a comma. The following characters are forbidden: %s','sprintf'=>'!&lt;;&gt;;?=+#&quot;&deg;{}_$%.'),$_smarty_tpl);?>

			</div>
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
<script type="text/javascript">
	hideOtherLanguage(<?php echo $_smarty_tpl->tpl_vars['default_form_language']->value;?>
);
	var missing_product_name = '<?php echo smartyTranslate(array('s'=>'Please fill product name input field','js'=>1),$_smarty_tpl);?>
';
</script>
<?php }} ?>
