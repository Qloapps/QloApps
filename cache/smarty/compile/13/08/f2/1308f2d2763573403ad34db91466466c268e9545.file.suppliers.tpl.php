<?php /* Smarty version Smarty-3.1.19, created on 2015-11-05 03:29:11
         compiled from "/home/sumit/public_html/html/ps-hotel-reservation-system/admin584hec64n/themes/default/template/controllers/products/suppliers.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1491010319563b1357bbefc8-01982003%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1308f2d2763573403ad34db91466466c268e9545' => 
    array (
      0 => '/home/sumit/public_html/html/ps-hotel-reservation-system/admin584hec64n/themes/default/template/controllers/products/suppliers.tpl',
      1 => 1446483943,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1491010319563b1357bbefc8-01982003',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'product' => 0,
    'suppliers' => 0,
    'supplier' => 0,
    'link' => 0,
    'associated_suppliers' => 0,
    'attributes' => 0,
    'id_default_currency' => 0,
    'associated_suppliers_collection' => 0,
    'asc' => 0,
    'attribute' => 0,
    'index' => 0,
    'product_designation' => 0,
    'reference' => 0,
    'price_te' => 0,
    'currencies' => 0,
    'currency' => 0,
    'id_currency' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_563b1357cdb323_32753810',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_563b1357cdb323_32753810')) {function content_563b1357cdb323_32753810($_smarty_tpl) {?>

<input type="hidden" name="supplier_loaded" value="1">
<?php if (isset($_smarty_tpl->tpl_vars['product']->value->id)) {?>
<div id="product-suppliers" class="panel product-tab">
	<input type="hidden" name="submitted_tabs[]" value="Suppliers" />
	<h3><?php echo smartyTranslate(array('s'=>'Suppliers of the current product'),$_smarty_tpl);?>
</h3>
	<div class="alert alert-info">
		<?php echo smartyTranslate(array('s'=>'This interface allows you to specify the suppliers of the current product and eventually its combinations.'),$_smarty_tpl);?>
<br />
		<?php echo smartyTranslate(array('s'=>'It is also possible to specify supplier references according to previously associated suppliers.'),$_smarty_tpl);?>
<br />
		<br />
		<?php echo smartyTranslate(array('s'=>'When using the advanced stock management tool (see Preferences/Products), the values you define (prices, references) will be used in supply orders.'),$_smarty_tpl);?>

	</div>
	<label><?php echo smartyTranslate(array('s'=>'Please choose the suppliers associated with this product. Please select a default supplier, as well.'),$_smarty_tpl);?>
</label>
	<table class="table">
		<thead>
			<tr>
				<th class="fixed-width-xs"><span class="title_box"><?php echo smartyTranslate(array('s'=>'Selected'),$_smarty_tpl);?>
</span></th>
				<th><span class="title_box"><?php echo smartyTranslate(array('s'=>'Supplier Name'),$_smarty_tpl);?>
</span></th>
				<th class="fixed-width-xs"><span class="title_box"><?php echo smartyTranslate(array('s'=>'Default'),$_smarty_tpl);?>
</span></th>
			</tr>
		</thead>
		<tbody>
		<?php  $_smarty_tpl->tpl_vars['supplier'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['supplier']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['suppliers']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['supplier']->key => $_smarty_tpl->tpl_vars['supplier']->value) {
$_smarty_tpl->tpl_vars['supplier']->_loop = true;
?>
			<tr>
				<td><input type="checkbox" class="supplierCheckBox" name="check_supplier_<?php echo $_smarty_tpl->tpl_vars['supplier']->value['id_supplier'];?>
" <?php if ($_smarty_tpl->tpl_vars['supplier']->value['is_selected']==true) {?>checked="checked"<?php }?> value="<?php echo $_smarty_tpl->tpl_vars['supplier']->value['id_supplier'];?>
" /></td>
				<td><?php echo $_smarty_tpl->tpl_vars['supplier']->value['name'];?>
</td>
				<td><input type="radio" id="default_supplier_<?php echo $_smarty_tpl->tpl_vars['supplier']->value['id_supplier'];?>
" name="default_supplier" value="<?php echo $_smarty_tpl->tpl_vars['supplier']->value['id_supplier'];?>
" <?php if ($_smarty_tpl->tpl_vars['supplier']->value['is_selected']==false) {?>disabled="disabled"<?php }?> <?php if ($_smarty_tpl->tpl_vars['supplier']->value['is_default']==true) {?>checked="checked"<?php }?> /></td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
	<a class="btn btn-link bt-icon confirm_leave" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminSuppliers'), ENT_QUOTES, 'UTF-8', true);?>
&addsupplier">
		<i class="icon-plus"></i> <?php echo smartyTranslate(array('s'=>'Create a new supplier'),$_smarty_tpl);?>
 <i class="icon-external-link-sign"></i>
	</a>
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
<div class="panel">
	<h3><?php echo smartyTranslate(array('s'=>'Supplier reference(s)'),$_smarty_tpl);?>
</h3>
	<div class="alert alert-info">
		<?php if (count($_smarty_tpl->tpl_vars['associated_suppliers']->value)==0) {?>
			<?php echo smartyTranslate(array('s'=>'You must specify the suppliers associated with this product. You must also select the default product supplier before setting references.'),$_smarty_tpl);?>

		<?php } else { ?>
			<?php echo smartyTranslate(array('s'=>'You can specify product reference(s) for each associated supplier.'),$_smarty_tpl);?>

		<?php }?>
		<?php echo smartyTranslate(array('s'=>'Click "Save and Stay" after changing selected suppliers to display the associated product references.'),$_smarty_tpl);?>

	</div>
	<div class="panel-group" id="accordion-supplier">
		<?php  $_smarty_tpl->tpl_vars['supplier'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['supplier']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['associated_suppliers']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['supplier']->key => $_smarty_tpl->tpl_vars['supplier']->value) {
$_smarty_tpl->tpl_vars['supplier']->_loop = true;
?>
		<div class="panel">
			<div class="panel-heading">
				<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion-supplier" href="#supplier-<?php echo $_smarty_tpl->tpl_vars['supplier']->value->id;?>
"><?php if (isset($_smarty_tpl->tpl_vars['supplier']->value->name)) {?><?php echo $_smarty_tpl->tpl_vars['supplier']->value->name;?>
<?php }?></a>
			</div>
			<div id="supplier-<?php echo $_smarty_tpl->tpl_vars['supplier']->value->id;?>
">
				<div class="panel-body">
					<table class="table">
						<thead>
							<tr>
								<th><span class="title_box"><?php echo smartyTranslate(array('s'=>'Product name'),$_smarty_tpl);?>
</span></th>
								<th><span class="title_box"><?php echo smartyTranslate(array('s'=>'Supplier reference'),$_smarty_tpl);?>
</span></th>
								<th><span class="title_box"><?php echo smartyTranslate(array('s'=>'Unit price tax excluded'),$_smarty_tpl);?>
</span></th>
								<th><span class="title_box"><?php echo smartyTranslate(array('s'=>'Unit price currency'),$_smarty_tpl);?>
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
							<?php $_smarty_tpl->tpl_vars['reference'] = new Smarty_variable('', null, 0);?>
							<?php $_smarty_tpl->tpl_vars['price_te'] = new Smarty_variable('', null, 0);?>
							<?php $_smarty_tpl->tpl_vars['id_currency'] = new Smarty_variable($_smarty_tpl->tpl_vars['id_default_currency']->value, null, 0);?>
							<?php  $_smarty_tpl->tpl_vars['asc'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['asc']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['associated_suppliers_collection']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['asc']->key => $_smarty_tpl->tpl_vars['asc']->value) {
$_smarty_tpl->tpl_vars['asc']->_loop = true;
?>
								<?php if ($_smarty_tpl->tpl_vars['asc']->value->id_product==$_smarty_tpl->tpl_vars['attribute']->value['id_product']&&$_smarty_tpl->tpl_vars['asc']->value->id_product_attribute==$_smarty_tpl->tpl_vars['attribute']->value['id_product_attribute']&&$_smarty_tpl->tpl_vars['asc']->value->id_supplier==$_smarty_tpl->tpl_vars['supplier']->value->id_supplier) {?>
									<?php $_smarty_tpl->tpl_vars['reference'] = new Smarty_variable($_smarty_tpl->tpl_vars['asc']->value->product_supplier_reference, null, 0);?>
									<?php $_smarty_tpl->tpl_vars['price_te'] = new Smarty_variable(Tools::ps_round($_smarty_tpl->tpl_vars['asc']->value->product_supplier_price_te,2), null, 0);?>
									<?php if ($_smarty_tpl->tpl_vars['asc']->value->id_currency) {?>
										<?php $_smarty_tpl->tpl_vars['id_currency'] = new Smarty_variable($_smarty_tpl->tpl_vars['asc']->value->id_currency, null, 0);?>
									<?php }?>
								<?php }?>
							<?php } ?>
							<tr <?php if ((1 & $_smarty_tpl->tpl_vars['index']->value)) {?>class="alt_row"<?php }?>>
								<td><?php echo $_smarty_tpl->tpl_vars['product_designation']->value[$_smarty_tpl->tpl_vars['attribute']->value['id_product_attribute']];?>
</td>
								<td>
									<input type="text" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['reference']->value, ENT_QUOTES, 'UTF-8', true);?>
" name="supplier_reference_<?php echo $_smarty_tpl->tpl_vars['attribute']->value['id_product'];?>
_<?php echo $_smarty_tpl->tpl_vars['attribute']->value['id_product_attribute'];?>
_<?php echo $_smarty_tpl->tpl_vars['supplier']->value->id_supplier;?>
" />
								</td>
								<td>
									<input type="text" value="<?php echo htmlentities($_smarty_tpl->tpl_vars['price_te']->value);?>
" name="product_price_<?php echo $_smarty_tpl->tpl_vars['attribute']->value['id_product'];?>
_<?php echo $_smarty_tpl->tpl_vars['attribute']->value['id_product_attribute'];?>
_<?php echo $_smarty_tpl->tpl_vars['supplier']->value->id_supplier;?>
" />
								</td>
								<td>
									<select name="product_price_currency_<?php echo $_smarty_tpl->tpl_vars['attribute']->value['id_product'];?>
_<?php echo $_smarty_tpl->tpl_vars['attribute']->value['id_product_attribute'];?>
_<?php echo $_smarty_tpl->tpl_vars['supplier']->value->id_supplier;?>
">
										<?php  $_smarty_tpl->tpl_vars['currency'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['currency']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['currencies']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['currency']->key => $_smarty_tpl->tpl_vars['currency']->value) {
$_smarty_tpl->tpl_vars['currency']->_loop = true;
?>
											<option value="<?php echo $_smarty_tpl->tpl_vars['currency']->value['id_currency'];?>
"
												<?php if ($_smarty_tpl->tpl_vars['currency']->value['id_currency']==$_smarty_tpl->tpl_vars['id_currency']->value) {?>selected="selected"<?php }?>
											><?php echo $_smarty_tpl->tpl_vars['currency']->value['name'];?>
</option>
										<?php } ?>
									</select>
								</td>
							</tr>
						<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<?php } ?>
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
