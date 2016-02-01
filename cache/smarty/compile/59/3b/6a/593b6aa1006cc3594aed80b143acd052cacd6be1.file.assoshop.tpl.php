<?php /* Smarty version Smarty-3.1.19, created on 2016-01-29 15:44:55
         compiled from "/home/sumit/public_html/html/hotelcommerce-master/admin/themes/default/template/helpers/form/assoshop.tpl" */ ?>
<?php /*%%SmartyHeaderCode:15319993856ab3b9f6d6265-50146428%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '593b6aa1006cc3594aed80b143acd052cacd6be1' => 
    array (
      0 => '/home/sumit/public_html/html/hotelcommerce-master/admin/themes/default/template/helpers/form/assoshop.tpl',
      1 => 1454062117,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '15319993856ab3b9f6d6265-50146428',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'input' => 0,
    'table' => 0,
    'groupID' => 0,
    'groupChecked' => 0,
    'groupData' => 0,
    'shopID' => 0,
    'fields_value' => 0,
    'j' => 0,
    'checked' => 0,
    'shopData' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_56ab3b9f721b96_42084215',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56ab3b9f721b96_42084215')) {function content_56ab3b9f721b96_42084215($_smarty_tpl) {?>

<script type="text/javascript">
$().ready(function() {
	$('.input_all_shop').live('click', function() {
		var checked = $(this).prop('checked');
		$('.input_shop_group:not(:disabled)').attr('checked', checked);
		$('.input_shop:not(:disabled)').attr('checked', checked);
	});

	// Click on a group shop
	$('.input_shop_group').live('click', function() {
		$('.input_shop[value='+$(this).val()+']').attr('checked', $(this).prop('checked'));
		check_all_shop();
	});

	// Click on a shop
	$('.input_shop').live('click', function() {
		check_shop_group_status($(this).val());
		check_all_shop();
	});

	// Initialize checkbox
	$('.input_shop_group').each(function(k, v) {
		check_shop_group_status($(v).val());
		check_all_shop();
	});
});

function check_shop_group_status(id_group) {
	var groupChecked = true;
	var total = 0;
	$('.input_shop[value='+id_group+']').each(function(k, v) {
		total++;
		if (!$(v).prop('checked'))
			groupChecked = false;
	});

	if (total > 0)
		$('.input_shop_group[value='+id_group+']').attr('checked', groupChecked);
}

function check_all_shop() {
	var allChecked = true;
	$('.input_shop_group:not(:disabled)').each(function(k, v) {
		if (!$(v).prop('checked'))
			allChecked = false;
		});
	$('.input_all_shop').attr('checked', allChecked);
}
</script>

<div class="assoShop">
	<table class="table">
		<tr>
			<th><?php echo smartyTranslate(array('s'=>'Shop'),$_smarty_tpl);?>
</th>
		</tr>
		<tr>
			<td>
				<label>
					<input class="input_all_shop" type="checkbox" /><?php echo smartyTranslate(array('s'=>'All shops'),$_smarty_tpl);?>

				</label>
			</td>
		</tr>
		<?php  $_smarty_tpl->tpl_vars['groupData'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['groupData']->_loop = false;
 $_smarty_tpl->tpl_vars['groupID'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['input']->value['values']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['groupData']->key => $_smarty_tpl->tpl_vars['groupData']->value) {
$_smarty_tpl->tpl_vars['groupData']->_loop = true;
 $_smarty_tpl->tpl_vars['groupID']->value = $_smarty_tpl->tpl_vars['groupData']->key;
?>
				<?php $_smarty_tpl->tpl_vars['groupChecked'] = new Smarty_variable(false, null, 0);?>
			<tr <?php if ($_smarty_tpl->tpl_vars['input']->value['type']=='shop') {?>class="alt_row"<?php }?>>
				<td>
					<img style="vertical-align:middle;" alt="" src="../img/admin/lv2_b.gif" />
					<label>
						<input class="input_shop_group"
							type="checkbox"
							name="checkBoxShopGroupAsso_<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
[<?php echo intval($_smarty_tpl->tpl_vars['groupID']->value);?>
]"
							value="<?php echo intval($_smarty_tpl->tpl_vars['groupID']->value);?>
"
							<?php if ($_smarty_tpl->tpl_vars['groupChecked']->value) {?> checked="checked"<?php }?> />
						<?php echo smartyTranslate(array('s'=>'Group:'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['groupData']->value['name'];?>

					</label>
				</td>
			</tr>
	
			<?php if ($_smarty_tpl->tpl_vars['input']->value['type']=='shop') {?>
				<?php $_smarty_tpl->tpl_vars['j'] = new Smarty_variable(0, null, 0);?>
				<?php  $_smarty_tpl->tpl_vars['shopData'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['shopData']->_loop = false;
 $_smarty_tpl->tpl_vars['shopID'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['groupData']->value['shops']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['shopData']->key => $_smarty_tpl->tpl_vars['shopData']->value) {
$_smarty_tpl->tpl_vars['shopData']->_loop = true;
 $_smarty_tpl->tpl_vars['shopID']->value = $_smarty_tpl->tpl_vars['shopData']->key;
?>
					<?php if ((isset($_smarty_tpl->tpl_vars['fields_value']->value['shop'][$_smarty_tpl->tpl_vars['shopID']->value]))) {?>
						<?php $_smarty_tpl->tpl_vars['checked'] = new Smarty_variable(true, null, 0);?>
					<?php } else { ?>
						<?php $_smarty_tpl->tpl_vars['checked'] = new Smarty_variable(false, null, 0);?>
					<?php }?>
					<tr>
						<td <?php if ($_smarty_tpl->tpl_vars['groupData']->value['disable_shops']) {?>style="font-style:italic;background-color:#CFC4FF"<?php }?>>
							<img style="vertical-align:middle;" alt="" src="../img/admin/lv3_<?php if ($_smarty_tpl->tpl_vars['j']->value<count($_smarty_tpl->tpl_vars['groupData']->value['shops'])-1) {?>b<?php } else { ?>f<?php }?>.png" />
							<label class="child">
								<input class="input_shop"
									type="checkbox"
									value="<?php echo intval($_smarty_tpl->tpl_vars['groupID']->value);?>
"
									shop_id="<?php echo intval($_smarty_tpl->tpl_vars['shopID']->value);?>
"
									name="checkBoxShopAsso_<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
[<?php echo intval($_smarty_tpl->tpl_vars['shopID']->value);?>
]"
									id="checkedBox_<?php echo intval($_smarty_tpl->tpl_vars['shopID']->value);?>
"
									<?php if ($_smarty_tpl->tpl_vars['checked']->value) {?> checked="checked"<?php }?> 
									<?php if ($_smarty_tpl->tpl_vars['groupData']->value['disable_shops']) {?> readonly="readonly" onclick="return false"<?php }?>
									/>
								<?php echo $_smarty_tpl->tpl_vars['shopData']->value['name'];?>

							</label>
						</td>
					</tr>
					<?php $_smarty_tpl->tpl_vars['j'] = new Smarty_variable($_smarty_tpl->tpl_vars['j']->value+1, null, 0);?>
				<?php } ?>
			<?php }?>
		<?php } ?>
	</table>
</div><?php }} ?>
