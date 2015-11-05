<?php /* Smarty version Smarty-3.1.19, created on 2015-11-05 18:44:55
         compiled from "/home/sumit/public_html/html/ps-hotel-reservation-system/admin/themes/default/template/controllers/payment/restrictions.tpl" */ ?>
<?php /*%%SmartyHeaderCode:827439394563b564f9da958-40293038%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2f01cc89f3fceafc8a9dffba9a2be220968c3463' => 
    array (
      0 => '/home/sumit/public_html/html/ps-hotel-reservation-system/admin/themes/default/template/controllers/payment/restrictions.tpl',
      1 => 1446729264,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '827439394563b564f9da958-40293038',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'url_submit' => 0,
    'list' => 0,
    'payment_modules' => 0,
    'module' => 0,
    'item' => 0,
    'key_module' => 0,
    'type' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_563b564fa75a13_90154232',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_563b564fa75a13_90154232')) {function content_563b564fa75a13_90154232($_smarty_tpl) {?>

<form action="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['url_submit']->value, ENT_QUOTES, 'UTF-8', true);?>
" method="post" id="form_<?php echo $_smarty_tpl->tpl_vars['list']->value['name_id'];?>
" class="form-horizontal">
	<div class="panel">
		<h3>
			<i class="<?php echo $_smarty_tpl->tpl_vars['list']->value['icon'];?>
"></i>
			<?php echo $_smarty_tpl->tpl_vars['list']->value['title'];?>

		</h3>
		<p class="help-block"><?php echo $_smarty_tpl->tpl_vars['list']->value['desc'];?>
</p>
		<div class="row table-responsive clearfix ">
			<div class="overflow-y">
				<table class="table">
					<thead>
						<tr>
							<th style="width:40%"><span class="title_box"><?php echo $_smarty_tpl->tpl_vars['list']->value['title'];?>
</span></th>
							<?php  $_smarty_tpl->tpl_vars['module'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['module']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['payment_modules']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['module']->key => $_smarty_tpl->tpl_vars['module']->value) {
$_smarty_tpl->tpl_vars['module']->_loop = true;
?>
								<?php if ($_smarty_tpl->tpl_vars['module']->value->active) {?>
									<th class="text-center">
										<?php if ($_smarty_tpl->tpl_vars['list']->value['name_id']!='currency'||$_smarty_tpl->tpl_vars['module']->value->currencies_mode=='checkbox') {?>
											<input type="hidden" id="checkedBox_<?php echo $_smarty_tpl->tpl_vars['list']->value['name_id'];?>
_<?php echo $_smarty_tpl->tpl_vars['module']->value->name;?>
" value="checked"/>
											<a href="javascript:checkPaymentBoxes('<?php echo $_smarty_tpl->tpl_vars['list']->value['name_id'];?>
', '<?php echo $_smarty_tpl->tpl_vars['module']->value->name;?>
')">
										<?php }?>
										<?php echo $_smarty_tpl->tpl_vars['module']->value->displayName;?>

										<?php if ($_smarty_tpl->tpl_vars['list']->value['name_id']!='currency'||$_smarty_tpl->tpl_vars['module']->value->currencies_mode=='checkbox') {?>
											</a>
										<?php }?>
									</th>
								<?php }?>
							<?php } ?>
						</tr>
					</thead>
					<tbody>
					<?php  $_smarty_tpl->tpl_vars['item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['list']->value['items']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['item']->key => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->_loop = true;
?>
						<tr>
							<td>
								<span><?php echo $_smarty_tpl->tpl_vars['item']->value['name'];?>
</span>
							</td>
							<?php  $_smarty_tpl->tpl_vars['module'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['module']->_loop = false;
 $_smarty_tpl->tpl_vars['key_module'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['payment_modules']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['module']->key => $_smarty_tpl->tpl_vars['module']->value) {
$_smarty_tpl->tpl_vars['module']->_loop = true;
 $_smarty_tpl->tpl_vars['key_module']->value = $_smarty_tpl->tpl_vars['module']->key;
?>
								<?php if ($_smarty_tpl->tpl_vars['module']->value->active) {?>
									<td class="text-center">
										<?php $_smarty_tpl->tpl_vars['type'] = new Smarty_variable('null', null, 0);?>
										<?php if (!$_smarty_tpl->tpl_vars['item']->value['check_list'][$_smarty_tpl->tpl_vars['key_module']->value]) {?>
											
										<?php } elseif ($_smarty_tpl->tpl_vars['list']->value['name_id']==='currency') {?>
											<?php if ($_smarty_tpl->tpl_vars['module']->value->currencies&&$_smarty_tpl->tpl_vars['module']->value->currencies_mode=='checkbox') {?>
												<?php $_smarty_tpl->tpl_vars['type'] = new Smarty_variable('checkbox', null, 0);?>
											<?php } elseif ($_smarty_tpl->tpl_vars['module']->value->currencies&&$_smarty_tpl->tpl_vars['module']->value->currencies_mode=='radio') {?>
												<?php $_smarty_tpl->tpl_vars['type'] = new Smarty_variable('radio', null, 0);?>
											<?php }?>
										<?php } else { ?>
											<?php $_smarty_tpl->tpl_vars['type'] = new Smarty_variable('checkbox', null, 0);?>
										<?php }?>
										<?php if ($_smarty_tpl->tpl_vars['type']->value!='null') {?>
											<input type="<?php echo $_smarty_tpl->tpl_vars['type']->value;?>
" name="<?php echo $_smarty_tpl->tpl_vars['module']->value->name;?>
_<?php echo $_smarty_tpl->tpl_vars['list']->value['name_id'];?>
[]" value="<?php echo $_smarty_tpl->tpl_vars['item']->value[$_smarty_tpl->tpl_vars['list']->value['identifier']];?>
" <?php if ($_smarty_tpl->tpl_vars['item']->value['check_list'][$_smarty_tpl->tpl_vars['key_module']->value]=='checked') {?>checked="checked"<?php }?>/>
										<?php } else { ?>
											<input type="hidden" name="<?php echo $_smarty_tpl->tpl_vars['module']->value->name;?>
_<?php echo $_smarty_tpl->tpl_vars['list']->value['name_id'];?>
[]" value="<?php echo $_smarty_tpl->tpl_vars['item']->value[$_smarty_tpl->tpl_vars['list']->value['identifier']];?>
"/>--
										<?php }?>
									</td>
								<?php }?>
							<?php } ?>
						</tr>
					<?php } ?>
					<?php if ($_smarty_tpl->tpl_vars['list']->value['name_id']==='currency') {?>
						<tr>
							<td>
								<span><?php echo smartyTranslate(array('s'=>'Customer currency'),$_smarty_tpl);?>
</span>
							</td>
							<?php  $_smarty_tpl->tpl_vars['module'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['module']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['payment_modules']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['module']->key => $_smarty_tpl->tpl_vars['module']->value) {
$_smarty_tpl->tpl_vars['module']->_loop = true;
?>
								<?php if ($_smarty_tpl->tpl_vars['module']->value->active) {?>
									<td class="text-center">
										<?php if ($_smarty_tpl->tpl_vars['module']->value->currencies&&$_smarty_tpl->tpl_vars['module']->value->currencies_mode=='radio') {?>
											<input type="radio" name="<?php echo $_smarty_tpl->tpl_vars['module']->value->name;?>
_<?php echo $_smarty_tpl->tpl_vars['list']->value['name_id'];?>
[]" value="-1"<?php if (in_array(-1,$_smarty_tpl->tpl_vars['module']->value->{$_smarty_tpl->tpl_vars['list']->value['name_id']})) {?> checked="checked"
										<?php }?> />
										<?php } else { ?>
											--
										<?php }?>
									</td>
								<?php }?>
							<?php } ?>
						</tr>
						<tr>
							<td>
								<span><?php echo smartyTranslate(array('s'=>'Shop default currency'),$_smarty_tpl);?>
</span>
							</td>
							<?php  $_smarty_tpl->tpl_vars['module'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['module']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['payment_modules']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['module']->key => $_smarty_tpl->tpl_vars['module']->value) {
$_smarty_tpl->tpl_vars['module']->_loop = true;
?>
								<?php if ($_smarty_tpl->tpl_vars['module']->value->active) {?>
									<td class="text-center">
										<?php if ($_smarty_tpl->tpl_vars['module']->value->currencies&&$_smarty_tpl->tpl_vars['module']->value->currencies_mode=='radio') {?>
											<input type="radio" name="<?php echo $_smarty_tpl->tpl_vars['module']->value->name;?>
_<?php echo $_smarty_tpl->tpl_vars['list']->value['name_id'];?>
[]" value="-2"<?php if (in_array(-2,$_smarty_tpl->tpl_vars['module']->value->{$_smarty_tpl->tpl_vars['list']->value['name_id']})) {?> checked="checked"
										<?php }?> 
											/>
										<?php } else { ?>
											--
										<?php }?>
									</td>
								<?php }?>
							<?php } ?>
						</tr>
					<?php }?>
					</tbody>
				</table>
			</div>
		</div>
		<div class="panel-footer">
			<button type="submit" class="btn btn-default pull-right" name="submitModule<?php echo $_smarty_tpl->tpl_vars['list']->value['name_id'];?>
">
				<i class="process-icon-save"></i> <?php echo smartyTranslate(array('s'=>'Save restrictions'),$_smarty_tpl);?>

			</button>
		</div>
	</div>
</form>
<?php }} ?>
