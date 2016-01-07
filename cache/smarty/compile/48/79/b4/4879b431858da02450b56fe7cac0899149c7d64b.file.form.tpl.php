<?php /* Smarty version Smarty-3.1.19, created on 2016-01-07 10:35:33
         compiled from "/home/sumit/public_html/html/hotel-reservation-system/admin/themes/default/template/controllers/modules_positions/form.tpl" */ ?>
<?php /*%%SmartyHeaderCode:891791346568df21d79b8e5-90009706%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4879b431858da02450b56fe7cac0899149c7d64b' => 
    array (
      0 => '/home/sumit/public_html/html/hotel-reservation-system/admin/themes/default/template/controllers/modules_positions/form.tpl',
      1 => 1452142888,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '891791346568df21d79b8e5-90009706',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'url_submit' => 0,
    'table' => 0,
    'display_key' => 0,
    'edit_graft' => 0,
    'hooks' => 0,
    'modules' => 0,
    'module' => 0,
    'id_module' => 0,
    'show_modules' => 0,
    'hook' => 0,
    'id_hook' => 0,
    'except_diff' => 0,
    'exception_list' => 0,
    'exception_list_diff' => 0,
    'value' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_568df21d811c46_09184105',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_568df21d811c46_09184105')) {function content_568df21d811c46_09184105($_smarty_tpl) {?>

<div class="leadin"></div>

<form action="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['url_submit']->value, ENT_QUOTES, 'UTF-8', true);?>
" id="<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
_form" method="post" class="form-horizontal">
	<?php if ($_smarty_tpl->tpl_vars['display_key']->value) {?>
		<input type="hidden" name="show_modules" value="<?php echo $_smarty_tpl->tpl_vars['display_key']->value;?>
" />
	<?php }?>
	<div class="panel">
		<h3>
			<i class="icon-paste"></i>
			<?php echo smartyTranslate(array('s'=>'Transplant a module'),$_smarty_tpl);?>

		</h3>
		<div class="form-group">
			<label class="control-label col-lg-3 required"> <?php echo smartyTranslate(array('s'=>'Module'),$_smarty_tpl);?>
</label>
			<div class="col-lg-9">
				<select name="id_module" <?php if ($_smarty_tpl->tpl_vars['edit_graft']->value) {?> disabled="disabled"<?php }?>>
					<?php if (!$_smarty_tpl->tpl_vars['hooks']->value) {?>
						<option value="0"><?php echo smartyTranslate(array('s'=>'Please select a module'),$_smarty_tpl);?>
</option>
					<?php }?>
					<?php  $_smarty_tpl->tpl_vars['module'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['module']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['modules']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['module']->key => $_smarty_tpl->tpl_vars['module']->value) {
$_smarty_tpl->tpl_vars['module']->_loop = true;
?>
						<option value="<?php echo intval($_smarty_tpl->tpl_vars['module']->value->id);?>
"<?php if ($_smarty_tpl->tpl_vars['id_module']->value==$_smarty_tpl->tpl_vars['module']->value->id||(!$_smarty_tpl->tpl_vars['id_module']->value&&$_smarty_tpl->tpl_vars['show_modules']->value==$_smarty_tpl->tpl_vars['module']->value->id)) {?> selected="selected"<?php }?>><?php echo stripslashes($_smarty_tpl->tpl_vars['module']->value->displayName);?>
</option>
					<?php } ?>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-3 required"> <?php echo smartyTranslate(array('s'=>'Transplant to'),$_smarty_tpl);?>
</label>
			<div class="col-lg-9">
				<select name="id_hook"<?php if (!count($_smarty_tpl->tpl_vars['hooks']->value)) {?> disabled="disabled"<?php }?>>
					<?php if (!$_smarty_tpl->tpl_vars['hooks']->value) {?>
						<option value="0"><?php echo smartyTranslate(array('s'=>'Select a module above before choosing from available hooks'),$_smarty_tpl);?>
</option>
					<?php } else { ?>
						<?php  $_smarty_tpl->tpl_vars['hook'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['hook']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['hooks']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['hook']->key => $_smarty_tpl->tpl_vars['hook']->value) {
$_smarty_tpl->tpl_vars['hook']->_loop = true;
?>
							<option value="<?php echo $_smarty_tpl->tpl_vars['hook']->value['id_hook'];?>
" <?php if ($_smarty_tpl->tpl_vars['id_hook']->value==$_smarty_tpl->tpl_vars['hook']->value['id_hook']) {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['hook']->value['name'];?>
<?php if ($_smarty_tpl->tpl_vars['hook']->value['name']!=$_smarty_tpl->tpl_vars['hook']->value['title']) {?> (<?php echo $_smarty_tpl->tpl_vars['hook']->value['title'];?>
)<?php }?></option>
						<?php } ?>
					<?php }?>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="control-label col-lg-3"><?php echo smartyTranslate(array('s'=>'Exceptions'),$_smarty_tpl);?>
</label>
			<div class="col-lg-9">
				<div class="well">
					<div>
						<?php echo smartyTranslate(array('s'=>'Please specify the files for which you do not want the module to be displayed.'),$_smarty_tpl);?>
<br />
						<?php echo smartyTranslate(array('s'=>'Please input each filename, separated by a comma (",").'),$_smarty_tpl);?>
<br />
						<?php echo smartyTranslate(array('s'=>'You can also click the filename in the list below, and even make a multiple selection by keeping the Ctrl key pressed while clicking, or choose a whole range of filename by keeping the Shift key pressed while clicking.'),$_smarty_tpl);?>
<br />
						<?php if (!$_smarty_tpl->tpl_vars['except_diff']->value) {?>
							<?php echo $_smarty_tpl->tpl_vars['exception_list']->value;?>

						<?php } else { ?>
							<?php  $_smarty_tpl->tpl_vars['value'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['value']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['exception_list_diff']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['value']->key => $_smarty_tpl->tpl_vars['value']->value) {
$_smarty_tpl->tpl_vars['value']->_loop = true;
?>
								<?php echo $_smarty_tpl->tpl_vars['value']->value;?>

							<?php } ?>
						<?php }?>
					</div>
				</div>
			</div>
		</div>
		<div class="panel-footer">
			<?php if ($_smarty_tpl->tpl_vars['edit_graft']->value) {?>
				<input type="hidden" name="id_module" value="<?php echo $_smarty_tpl->tpl_vars['id_module']->value;?>
" />
				<input type="hidden" name="id_hook" value="<?php echo $_smarty_tpl->tpl_vars['id_hook']->value;?>
" />
			<?php }?>
			<button type="submit" name="<?php if ($_smarty_tpl->tpl_vars['edit_graft']->value) {?>submitEditGraft<?php } else { ?>submitAddToHook<?php }?>" id="<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
_form_submit_btn" class="btn btn-default pull-right"><i class="process-icon-save"></i> <?php echo smartyTranslate(array('s'=>'Save'),$_smarty_tpl);?>
</button>
		</div>
	</div>
</form>
<script type="text/javascript">
	//<![CDATA
	function position_exception_textchange() {
		// TODO : Add & Remove automatically the "custom pages" in the "em_list_x"
		var obj = $(this);
		var shopID = obj.attr('id').replace(/\D/g, '');
		var list = obj.closest('form').find('#em_list_' + shopID);
		var values = obj.val().split(',');
		var len = values.length;

		list.find('option').prop('selected', false);
		for (var i = 0; i < len; i++)
			list.find('option[value="' + $.trim(values[i]) + '"]').prop('selected', true);
	}
	function position_exception_listchange() {
		var obj = $(this);
		var shopID = obj.attr('id').replace(/\D/g, '');
		var val = obj.val();
		var str = '';
		if (val)
			str = val.join(', ');
		obj.closest('form').find('#em_text_' + shopID).val(str);
	}
	$(document).ready(function(){
		$('form[id="hook_module_form"] input[id^="em_text_"]').each(function(){
			$(this).change(position_exception_textchange).change();
		});
		$('form[id="hook_module_form"] select[id^="em_list_"]').each(function(){
			$(this).change(position_exception_listchange);
		});
	});
	//]]>
</script>
<?php }} ?>
