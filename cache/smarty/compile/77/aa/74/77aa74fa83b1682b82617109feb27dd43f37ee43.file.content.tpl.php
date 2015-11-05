<?php /* Smarty version Smarty-3.1.19, created on 2015-11-05 18:44:56
         compiled from "/home/sumit/public_html/html/ps-hotel-reservation-system/admin/themes/default/template/controllers/attribute_generator/content.tpl" */ ?>
<?php /*%%SmartyHeaderCode:277406960563b565088ff70-55465033%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '77aa74fa83b1682b82617109feb27dd43f37ee43' => 
    array (
      0 => '/home/sumit/public_html/html/ps-hotel-reservation-system/admin/themes/default/template/controllers/attribute_generator/content.tpl',
      1 => 1446729263,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '277406960563b565088ff70-55465033',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'attribute_js' => 0,
    'idgrp' => 0,
    'group' => 0,
    'row' => 0,
    'idattr' => 0,
    'attrname' => 0,
    'tax_rates' => 0,
    'generate' => 0,
    'combinations_size' => 0,
    'url_generator' => 0,
    'attribute_groups' => 0,
    'attribute_group' => 0,
    'k' => 0,
    'v' => 0,
    'product_name' => 0,
    'currency_sign' => 0,
    'weight_unit' => 0,
    'attributes' => 0,
    'attribute' => 0,
    'product_reference' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_563b56509161b9_81965431',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_563b56509161b9_81965431')) {function content_563b56509161b9_81965431($_smarty_tpl) {?>

<script type="text/javascript">
	var attrs = new Array();
	attrs[0] = new Array(0, '---');

	<?php  $_smarty_tpl->tpl_vars['group'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['group']->_loop = false;
 $_smarty_tpl->tpl_vars['idgrp'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['attribute_js']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['group']->key => $_smarty_tpl->tpl_vars['group']->value) {
$_smarty_tpl->tpl_vars['group']->_loop = true;
 $_smarty_tpl->tpl_vars['idgrp']->value = $_smarty_tpl->tpl_vars['group']->key;
?>
		<?php $_smarty_tpl->tpl_vars["row"] = new Smarty_variable("attrs[".((string)$_smarty_tpl->tpl_vars['idgrp']->value)."] = new Array(0, '---'", null, 0);?>

		<?php  $_smarty_tpl->tpl_vars['attrname'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['attrname']->_loop = false;
 $_smarty_tpl->tpl_vars['idattr'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['group']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['attrname']->key => $_smarty_tpl->tpl_vars['attrname']->value) {
$_smarty_tpl->tpl_vars['attrname']->_loop = true;
 $_smarty_tpl->tpl_vars['idattr']->value = $_smarty_tpl->tpl_vars['attrname']->key;
?>
			<?php ob_start();?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['attrname']->value, ENT_QUOTES, 'UTF-8', true);?>
<?php $_tmp8=ob_get_clean();?><?php $_smarty_tpl->tpl_vars["row"] = new Smarty_variable(((string)$_smarty_tpl->tpl_vars['row']->value).", ".((string)$_smarty_tpl->tpl_vars['idattr']->value).", '".$_tmp8."'", null, 0);?>
		<?php } ?>

		<?php $_smarty_tpl->tpl_vars["row"] = new Smarty_variable(((string)$_smarty_tpl->tpl_vars['row']->value).");", null, 0);?>
		<?php echo $_smarty_tpl->tpl_vars['row']->value;?>

	<?php } ?>

	i18n_tax_exc = '<?php echo smartyTranslate(array('s'=>'Tax Excluded'),$_smarty_tpl);?>
 ';
	i18n_tax_inc = '<?php echo smartyTranslate(array('s'=>'Tax Included'),$_smarty_tpl);?>
 ';

	var product_tax = '<?php echo $_smarty_tpl->tpl_vars['tax_rates']->value;?>
';
	function calcPrice(element, element_has_tax)
	{
			var element_price = element.val().replace(/,/g, '.');
			var other_element_price = 0;

			if (!isNaN(element_price))
			{
				if (element_has_tax)
					other_element_price = parseFloat(element_price / ((product_tax / 100) + 1)).toFixed(6);
				else
					other_element_price = ps_round(parseFloat(element_price * ((product_tax / 100) + 1)), 2).toFixed(2);
			}

			$('#related_to_'+element.attr('name')).val(other_element_price);
	}

	$(document).ready(function() { $('.price_impact').each(function() { calcPrice($(this), false); }); });
</script>

<div class="leadin"></div>

<?php if ($_smarty_tpl->tpl_vars['generate']->value) {?><div class="alert alert-success clearfix"><?php echo smartyTranslate(array('s'=>'%d product(s) successfully created.','sprintf'=>$_smarty_tpl->tpl_vars['combinations_size']->value),$_smarty_tpl);?>
</div><?php }?>
<form enctype="multipart/form-data" method="post" id="generator" action="<?php echo $_smarty_tpl->tpl_vars['url_generator']->value;?>
">
	<div class="panel">
		<h3>
			<i class="icon-asterisk"></i>
			<?php echo smartyTranslate(array('s'=>'Attributes generator'),$_smarty_tpl);?>

		</h3>
		<div class="row">
			<div class="col-lg-3">
				<div class="form-group">
					<select multiple name="attributes[]" id="attribute_group" style="height: 500px">
						<?php  $_smarty_tpl->tpl_vars['attribute_group'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['attribute_group']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['attribute_groups']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['attribute_group']->key => $_smarty_tpl->tpl_vars['attribute_group']->value) {
$_smarty_tpl->tpl_vars['attribute_group']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['attribute_group']->key;
?>
							<?php if (isset($_smarty_tpl->tpl_vars['attribute_js']->value[$_smarty_tpl->tpl_vars['attribute_group']->value['id_attribute_group']])) {?>
								<optgroup name="<?php echo $_smarty_tpl->tpl_vars['attribute_group']->value['id_attribute_group'];?>
" id="<?php echo $_smarty_tpl->tpl_vars['attribute_group']->value['id_attribute_group'];?>
" label="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['attribute_group']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
">
									<?php  $_smarty_tpl->tpl_vars['v'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['v']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['attribute_js']->value[$_smarty_tpl->tpl_vars['attribute_group']->value['id_attribute_group']]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['v']->key => $_smarty_tpl->tpl_vars['v']->value) {
$_smarty_tpl->tpl_vars['v']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['v']->key;
?>
										<option name="<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
" id="attr_<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['v']->value, ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['v']->value, ENT_QUOTES, 'UTF-8', true);?>
"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['v']->value, ENT_QUOTES, 'UTF-8', true);?>
</option>
									<?php } ?>
								</optgroup>
							<?php }?>
						<?php } ?>
					</select>
				</div>
				<div class="form-group">
					<button type="button" class="btn btn-default" onclick="del_attr_multiple();"><i class="icon-minus-sign"></i> <?php echo smartyTranslate(array('s'=>'Delete'),$_smarty_tpl);?>
</button>
					<button type="button" class="btn btn-default pull-right" onclick="add_attr_multiple();"><i class="icon-plus-sign"></i> <?php echo smartyTranslate(array('s'=>'Add'),$_smarty_tpl);?>
</button>
				</div>
			</div>
			<div class="col-lg-8 col-lg-offset-1">
				<div class="alert alert-info"><?php echo smartyTranslate(array('s'=>'The Combinations Generator is a tool that allows you to easily create a series of combinations by selecting the related attributes. For example, if you\'re selling t-shirts in three different sizes and two different colors, the generator will create six combinations for you.'),$_smarty_tpl);?>
</div>

				<div class="alert alert-info"><?php echo smartyTranslate(array('s'=>'You\'re currently generating combinations for the following product:'),$_smarty_tpl);?>
 <b><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['product_name']->value, ENT_QUOTES, 'UTF-8', true);?>
</b></div>

				<div class="alert alert-info"><strong><?php echo smartyTranslate(array('s'=>'Step 1: On the left side, select the attributes you want to use (Hold down the "Ctrl" key on your keyboard and validate by clicking on "Add")'),$_smarty_tpl);?>
</strong></div>

				<?php  $_smarty_tpl->tpl_vars['attribute_group'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['attribute_group']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['attribute_groups']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['attribute_group']->key => $_smarty_tpl->tpl_vars['attribute_group']->value) {
$_smarty_tpl->tpl_vars['attribute_group']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['attribute_group']->key;
?>
					<?php if (isset($_smarty_tpl->tpl_vars['attribute_js']->value[$_smarty_tpl->tpl_vars['attribute_group']->value['id_attribute_group']])) {?>
					<div class="row">
						<table class="table" style="display:none">
							<thead>
								<tr>
									<th id="tab_h1" class="fixed-width-md"><span class="title_box"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['attribute_group']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
</span></th>
									<th id="tab_h2" colspan="2"><span class="title_box"><?php echo smartyTranslate(array('s'=>'Impact on the product price'),$_smarty_tpl);?>
 (<?php echo $_smarty_tpl->tpl_vars['currency_sign']->value;?>
)</span></th>
									<th><span class="title_box"><?php echo smartyTranslate(array('s'=>'Impact on the product weight'),$_smarty_tpl);?>
 (<?php echo $_smarty_tpl->tpl_vars['weight_unit']->value;?>
)</span></th>
								</tr>
							</thead>
							<tbody id="table_<?php echo $_smarty_tpl->tpl_vars['attribute_group']->value['id_attribute_group'];?>
" name="result_table">
							</tbody>
						</table>
					</div>
						<?php if (isset($_smarty_tpl->tpl_vars['attributes']->value[$_smarty_tpl->tpl_vars['attribute_group']->value['id_attribute_group']])) {?>
							<?php  $_smarty_tpl->tpl_vars['attribute'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['attribute']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['attributes']->value[$_smarty_tpl->tpl_vars['attribute_group']->value['id_attribute_group']]; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['attribute']->key => $_smarty_tpl->tpl_vars['attribute']->value) {
$_smarty_tpl->tpl_vars['attribute']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['attribute']->key;
?>
								<script type="text/javascript">
									$('#table_<?php echo $_smarty_tpl->tpl_vars['attribute_group']->value['id_attribute_group'];?>
').append(create_attribute_row(<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
, <?php echo $_smarty_tpl->tpl_vars['attribute_group']->value['id_attribute_group'];?>
, '<?php echo addslashes($_smarty_tpl->tpl_vars['attribute']->value['attribute_name']);?>
', <?php echo $_smarty_tpl->tpl_vars['attribute']->value['price'];?>
, <?php echo $_smarty_tpl->tpl_vars['attribute']->value['weight'];?>
));
									toggle(getE('table_' + <?php echo $_smarty_tpl->tpl_vars['attribute_group']->value['id_attribute_group'];?>
).parentNode, true);
								</script>
							<?php } ?>
						<?php }?>
					<?php }?>
				<?php } ?>
				<div class="alert alert-info"><?php echo smartyTranslate(array('s'=>'Select a default quantity, and reference, for each combination the generator will create for this product.'),$_smarty_tpl);?>
</div>
				<table class="table">
					<tbody>
						<tr>
							<td><?php echo smartyTranslate(array('s'=>'Default Quantity:'),$_smarty_tpl);?>
</td>
							<td><input type="text" name="quantity" value="0" /></td>
						</tr>
						<tr>
							<td><?php echo smartyTranslate(array('s'=>'Default Reference:'),$_smarty_tpl);?>
</td>
							<td><input type="text" name="reference" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['product_reference']->value, ENT_QUOTES, 'UTF-8', true);?>
" /></td>
						</tr>
					</tbody>
				</table>
				<div class="alert alert-info"><?php echo smartyTranslate(array('s'=>'Please click on "Generate these Combinations"'),$_smarty_tpl);?>
</div>
				<button type="submit" class="btn btn-default" name="generate"><i class="icon-random"></i> <?php echo smartyTranslate(array('s'=>'Generate these Combinations'),$_smarty_tpl);?>
</button>
			</div>
		</div>
	</div>
</form>
<?php }} ?>
