<?php /* Smarty version Smarty-3.1.19, created on 2016-01-07 10:42:27
         compiled from "/home/sumit/public_html/html/hotel-reservation-system/admin_htl/themes/default/template/controllers/products/associations.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1433027360568df3bbc865f3-94663069%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd116d207bc03e4c1adb6a8413aa5ed86864733e2' => 
    array (
      0 => '/home/sumit/public_html/html/hotel-reservation-system/admin_htl/themes/default/template/controllers/products/associations.tpl',
      1 => 1452142888,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1433027360568df3bbc865f3-94663069',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'category_tree' => 0,
    'link' => 0,
    'selected_cat' => 0,
    'cat' => 0,
    'id_category_default' => 0,
    'accessories' => 0,
    'accessory' => 0,
    'product' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_568df3bbcfd313_39198801',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_568df3bbcfd313_39198801')) {function content_568df3bbcfd313_39198801($_smarty_tpl) {?>
<div id="product-associations" class="panel product-tab">
	<input type="hidden" name="submitted_tabs[]" value="Associations" />
	<h3><?php echo smartyTranslate(array('s'=>'Associations'),$_smarty_tpl);?>
</h3>
	<?php echo $_smarty_tpl->getSubTemplate ("controllers/products/multishop/check_fields.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array('product_tab'=>"Associations"), 0);?>

	<div id="no_default_category" class="alert alert-info">
		<?php echo smartyTranslate(array('s'=>'Please select a default category.'),$_smarty_tpl);?>

	</div>
	<div class="form-group">
		<div class="col-lg-1"><span class="pull-right"><?php echo $_smarty_tpl->getSubTemplate ("controllers/products/multishop/checkbox.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array('field'=>"category_box",'type'=>"category_box"), 0);?>
</span></div>
		<label class="control-label col-lg-2" for="category_block">
			<?php echo smartyTranslate(array('s'=>'Associated categories'),$_smarty_tpl);?>

		</label>
		<div class="col-lg-9">
			<div id="category_block">
				<?php echo $_smarty_tpl->tpl_vars['category_tree']->value;?>

			</div>
			<a class="btn btn-link bt-icon confirm_leave" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminCategories'), ENT_QUOTES, 'UTF-8', true);?>
&amp;addcategory">
				<i class="icon-plus-sign"></i> <?php echo smartyTranslate(array('s'=>'Create new category'),$_smarty_tpl);?>
 <i class="icon-external-link-sign"></i>
			</a>
		</div>
	</div>
	<div class="form-group">
		<div class="col-lg-1"><span class="pull-right"><?php echo $_smarty_tpl->getSubTemplate ("controllers/products/multishop/checkbox.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array('field'=>"id_category_default",'type'=>"default"), 0);?>
</span></div>
		<label class="control-label col-lg-2" for="id_category_default">
			<span class="label-tooltip" data-toggle="tooltip" title="<?php echo smartyTranslate(array('s'=>'The default category is the main category for your product, and is displayed by default.'),$_smarty_tpl);?>
">
				<?php echo smartyTranslate(array('s'=>'Default category'),$_smarty_tpl);?>

			</span>
		</label>
		<div class="col-lg-5">
			<select id="id_category_default" name="id_category_default">
				<?php  $_smarty_tpl->tpl_vars['cat'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['cat']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['selected_cat']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['cat']->key => $_smarty_tpl->tpl_vars['cat']->value) {
$_smarty_tpl->tpl_vars['cat']->_loop = true;
?>
					<option value="<?php echo $_smarty_tpl->tpl_vars['cat']->value['id_category'];?>
" <?php if ($_smarty_tpl->tpl_vars['id_category_default']->value==$_smarty_tpl->tpl_vars['cat']->value['id_category']) {?>selected="selected"<?php }?> ><?php echo $_smarty_tpl->tpl_vars['cat']->value['name'];?>
</option>
				<?php } ?>
			</select>
		</div>
	</div>
	<!-- By webkul to hide unneccessary fields -->
	<!-- <div class="form-group">
		<label class="control-label col-lg-3" for="product_autocomplete_input">
			<span class="label-tooltip" data-toggle="tooltip"
			title="<?php echo smartyTranslate(array('s'=>'You can indicate existing products as accessories for this product.'),$_smarty_tpl);?>
<?php echo smartyTranslate(array('s'=>'Start by typing the first letters of the product\'s name, then select the product from the drop-down list.'),$_smarty_tpl);?>
<?php echo smartyTranslate(array('s'=>'Do not forget to save the product afterwards!'),$_smarty_tpl);?>
">
			<?php echo smartyTranslate(array('s'=>'Accessories'),$_smarty_tpl);?>

			</span>
		</label>
		<div class="col-lg-5">
			<input type="hidden" name="inputAccessories" id="inputAccessories" value="<?php  $_smarty_tpl->tpl_vars['accessory'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['accessory']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['accessories']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['accessory']->key => $_smarty_tpl->tpl_vars['accessory']->value) {
$_smarty_tpl->tpl_vars['accessory']->_loop = true;
?><?php echo $_smarty_tpl->tpl_vars['accessory']->value['id_product'];?>
-<?php } ?>" />
			<input type="hidden" name="nameAccessories" id="nameAccessories" value="<?php  $_smarty_tpl->tpl_vars['accessory'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['accessory']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['accessories']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['accessory']->key => $_smarty_tpl->tpl_vars['accessory']->value) {
$_smarty_tpl->tpl_vars['accessory']->_loop = true;
?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['accessory']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
¤<?php } ?>" />
			<div id="ajax_choose_product">
				<div class="input-group">
					<input type="text" id="product_autocomplete_input" name="product_autocomplete_input" />
					<span class="input-group-addon"><i class="icon-search"></i></span>
				</div>
			</div>

			<div id="divAccessories">
			<?php  $_smarty_tpl->tpl_vars['accessory'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['accessory']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['accessories']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['accessory']->key => $_smarty_tpl->tpl_vars['accessory']->value) {
$_smarty_tpl->tpl_vars['accessory']->_loop = true;
?>
			<div class="form-control-static">
				<button type="button" class="btn btn-default delAccessory" name="<?php echo $_smarty_tpl->tpl_vars['accessory']->value['id_product'];?>
">
					<i class="icon-remove text-danger"></i>
				</button>
				<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['accessory']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
<?php if (!empty($_smarty_tpl->tpl_vars['accessory']->value['reference'])) {?>&nbsp;<?php echo smartyTranslate(array('s'=>'(ref: %s)','sprintf'=>$_smarty_tpl->tpl_vars['accessory']->value['reference']),$_smarty_tpl);?>
<?php }?>
			</div>
			<?php } ?>
			</div>
		</div>
	</div> -->
	<!-- By webkul to hide unneccessary fields -->
	<!-- <div class="form-group">
		<label class="control-label col-lg-3" for="id_manufacturer"><?php echo smartyTranslate(array('s'=>'Manufacturer'),$_smarty_tpl);?>
</label>
		<div class="col-lg-5">
			<select name="id_manufacturer" id="id_manufacturer">
				<option value="0">- <?php echo smartyTranslate(array('s'=>'Choose (optional)'),$_smarty_tpl);?>
 -</option>
				<?php if ($_smarty_tpl->tpl_vars['product']->value->id_manufacturer) {?>
				<option value="<?php echo $_smarty_tpl->tpl_vars['product']->value->id_manufacturer;?>
" selected="selected"><?php echo $_smarty_tpl->tpl_vars['product']->value->manufacturer_name;?>
</option>
				<?php }?>
				<option disabled="disabled">-</option>
			</select>
		</div>
		<div class="col-lg-4">
			<a class="btn btn-link bt-icon confirm_leave" style="margin-bottom:0" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminManufacturers'), ENT_QUOTES, 'UTF-8', true);?>
&amp;addmanufacturer">
				<i class="icon-plus-sign"></i> <?php echo smartyTranslate(array('s'=>'Create new manufacturer'),$_smarty_tpl);?>
 <i class="icon-external-link-sign"></i>
			</a>
		</div>
	</div> -->
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
