<?php /* Smarty version Smarty-3.1.19, created on 2016-01-29 15:45:00
         compiled from "/home/sumit/public_html/html/hotelcommerce-master/admin/themes/default/template/controllers/products/warehouses.tpl" */ ?>
<?php /*%%SmartyHeaderCode:122032004056ab3ba43bbcb0-06560296%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6567fd073b6108d906873ffef9b14de78289e596' => 
    array (
      0 => '/home/sumit/public_html/html/hotelcommerce-master/admin/themes/default/template/controllers/products/warehouses.tpl',
      1 => 1454062119,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '122032004056ab3ba43bbcb0-06560296',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'product' => 0,
    'link' => 0,
    'warehouses' => 0,
    'warehouse' => 0,
    'attributes' => 0,
    'associated_warehouses' => 0,
    'aw' => 0,
    'attribute' => 0,
    'index' => 0,
    'selected' => 0,
    'product_designation' => 0,
    'location' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_56ab3ba4442ee8_75295954',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56ab3ba4442ee8_75295954')) {function content_56ab3ba4442ee8_75295954($_smarty_tpl) {?>

<input type="hidden" name="warehouse_loaded" value="1">
<?php if (isset($_smarty_tpl->tpl_vars['product']->value->id)) {?>
<div id="product-warehouses" class="panel product-tab">
	<input type="hidden" name="submitted_tabs[]" value="Warehouses" />
	<h3><?php echo smartyTranslate(array('s'=>'Product location in warehouses'),$_smarty_tpl);?>
</h3>
	<div class="row">
		<div class="alert alert-info" style="display:block; position:'auto';">
			<p><?php echo smartyTranslate(array('s'=>'This interface allows you to specify the warehouse in which the product is stocked.'),$_smarty_tpl);?>
</p>
			<p><?php echo smartyTranslate(array('s'=>'You can also specify product/product combinations as it relates to warehouse location. '),$_smarty_tpl);?>
</p>
		</div>
		<p><?php echo smartyTranslate(array('s'=>'Please choose the warehouses associated with this product.'),$_smarty_tpl);?>
</p>
	</div>
	<div class="row">
		<a class="btn btn-link confirm_leave" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminWarehouses'), ENT_QUOTES, 'UTF-8', true);?>
&addwarehouse"><?php echo smartyTranslate(array('s'=>'Create a new warehouse'),$_smarty_tpl);?>
 <i class="icon-external-link-sign"></i></a>
	</div>
	<div class="row">
		<div class="panel-group" id="warehouse-accordion">
			<?php  $_smarty_tpl->tpl_vars['warehouse'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['warehouse']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['warehouses']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['warehouse']->key => $_smarty_tpl->tpl_vars['warehouse']->value) {
$_smarty_tpl->tpl_vars['warehouse']->_loop = true;
?>
			    <div class="panel panel-default">
					<div class="panel-heading">
							<a class="accordion-toggle" data-toggle="collapse" data-parent="#warehouse-accordion" href="#warehouse-<?php echo $_smarty_tpl->tpl_vars['warehouse']->value['id_warehouse'];?>
"><?php echo $_smarty_tpl->tpl_vars['warehouse']->value['name'];?>
</a>
					</div>
					<div id="warehouse-<?php echo $_smarty_tpl->tpl_vars['warehouse']->value['id_warehouse'];?>
">
							<table class="table">
								<thead>
									<tr>
										<th class="fixed-width-xs" align="center"><span class="title_box"><?php echo smartyTranslate(array('s'=>'Stored'),$_smarty_tpl);?>
</span></th>
										<th><span class="title_box"><?php echo smartyTranslate(array('s'=>'Product'),$_smarty_tpl);?>
</span></th>
										<th><span class="title_box"><?php echo smartyTranslate(array('s'=>'Location (optional)'),$_smarty_tpl);?>
</span></th>
									</tr>
								</thead>
								<tbody>
								<?php  $_smarty_tpl->tpl_vars['attribute'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['attribute']->_loop = false;
 $_smarty_tpl->tpl_vars['index'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['attributes']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['attribute']->key => $_smarty_tpl->tpl_vars['attribute']->value) {
$_smarty_tpl->tpl_vars['attribute']->_loop = true;
 $_smarty_tpl->tpl_vars['index']->value = $_smarty_tpl->tpl_vars['attribute']->key;
?>
									<?php $_smarty_tpl->tpl_vars['location'] = new Smarty_variable('', null, 0);?>
									<?php $_smarty_tpl->tpl_vars['selected'] = new Smarty_variable('', null, 0);?>
									<?php  $_smarty_tpl->tpl_vars['aw'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['aw']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['associated_warehouses']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['aw']->key => $_smarty_tpl->tpl_vars['aw']->value) {
$_smarty_tpl->tpl_vars['aw']->_loop = true;
?>
										<?php if ($_smarty_tpl->tpl_vars['aw']->value->id_product==$_smarty_tpl->tpl_vars['attribute']->value['id_product']&&$_smarty_tpl->tpl_vars['aw']->value->id_product_attribute==$_smarty_tpl->tpl_vars['attribute']->value['id_product_attribute']&&$_smarty_tpl->tpl_vars['aw']->value->id_warehouse==$_smarty_tpl->tpl_vars['warehouse']->value['id_warehouse']) {?>
											<?php $_smarty_tpl->tpl_vars['location'] = new Smarty_variable($_smarty_tpl->tpl_vars['aw']->value->location, null, 0);?>
											<?php $_smarty_tpl->tpl_vars['selected'] = new Smarty_variable(true, null, 0);?>
										<?php }?>
									<?php } ?>
									<tr <?php if ((1 & $_smarty_tpl->tpl_vars['index']->value)) {?>class="alt_row"<?php }?>>
										<td class="fixed-width-xs" align="center"><input type="checkbox"
											name="check_warehouse_<?php echo $_smarty_tpl->tpl_vars['warehouse']->value['id_warehouse'];?>
_<?php echo $_smarty_tpl->tpl_vars['attribute']->value['id_product'];?>
_<?php echo $_smarty_tpl->tpl_vars['attribute']->value['id_product_attribute'];?>
"
											<?php if ($_smarty_tpl->tpl_vars['selected']->value==true) {?>checked="checked"<?php }?>
											value="1" />
										</td>
										<td><?php echo $_smarty_tpl->tpl_vars['product_designation']->value[$_smarty_tpl->tpl_vars['attribute']->value['id_product_attribute']];?>
</td>
										<td><input type="text"
											name="location_warehouse_<?php echo $_smarty_tpl->tpl_vars['warehouse']->value['id_warehouse'];?>
_<?php echo $_smarty_tpl->tpl_vars['attribute']->value['id_product'];?>
_<?php echo $_smarty_tpl->tpl_vars['attribute']->value['id_product_attribute'];?>
"
											value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['location']->value, ENT_QUOTES, 'UTF-8', true);?>
"
											size="20" />
										</td>
									</tr>
								<?php } ?>
							</table>
							<?php if (count($_smarty_tpl->tpl_vars['attributes']->value)>1) {?>
							<button type="button" class="btn btn-default check_all_warehouse" value="check_warehouse_<?php echo $_smarty_tpl->tpl_vars['warehouse']->value['id_warehouse'];?>
"><i class="icon-check-sign"></i> <?php echo smartyTranslate(array('s'=>'Mark / Unmark all product combinations as stored in this warehouse'),$_smarty_tpl);?>
</button>
							<!--<tr>
								<td><input type="checkbox" class="check_all_warehouse" value="check_warehouse_<?php echo $_smarty_tpl->tpl_vars['warehouse']->value['id_warehouse'];?>
" /></td>
								<td colspan="2"><i></i></td>
							</tr>-->
							<?php }?>
						</div>
				</div>
			<?php } ?>
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
<?php }?>
<?php }} ?>
