<?php /* Smarty version Smarty-3.1.19, created on 2015-11-02 20:02:43
         compiled from "/home/sumit/public_html/html/ps-hotel-reservation-system/admin/themes/default/template/controllers/modules/favorites.tpl" */ ?>
<?php /*%%SmartyHeaderCode:3170722575637740be46a00-88950907%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1245e7dd72376593b1ecdcabc3091f9cb18590ee' => 
    array (
      0 => '/home/sumit/public_html/html/ps-hotel-reservation-system/admin/themes/default/template/controllers/modules/favorites.tpl',
      1 => 1446455063,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '3170722575637740be46a00-88950907',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'modules' => 0,
    'module' => 0,
    'tabs' => 0,
    't' => 0,
    'module_name' => 0,
    'tab_modules_preferences' => 0,
    't2' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5637740bef21b5_27530865',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5637740bef21b5_27530865')) {function content_5637740bef21b5_27530865($_smarty_tpl) {?>
<div class="panel">
	<div class="panel-heading">
		<i class="icon-list-ul"></i>
		<?php echo smartyTranslate(array('s'=>'Modules list'),$_smarty_tpl);?>

	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="btn-group pull-right">
				<a class="btn btn-default <?php if (!isset($_GET['select'])) {?> active<?php }?>" href="index.php?controller=<?php echo htmlentities($_GET['controller']);?>
&amp;token=<?php echo htmlentities($_GET['token']);?>
">
					<i class="icon-list"></i>
					<?php echo smartyTranslate(array('s'=>'Normal view'),$_smarty_tpl);?>
 
				</a>
				<a class="btn btn-default <?php if ($_GET['select']=='favorites') {?> active<?php }?>" href="javascript:void(0);">
					<i class="icon-star"></i>
					<?php echo smartyTranslate(array('s'=>'Favorites view'),$_smarty_tpl);?>

				</a>
			</div>
		</div>
	</div>
	<div class="row">
		<div id="container" class="col-lg-12">
			<div id="moduleContainer">
				<table class="table">
					<thead>
						<tr class="nodrag nodrop">
							<th colspan="2"></th>
							<th><?php echo smartyTranslate(array('s'=>'Module'),$_smarty_tpl);?>
</th>
							<th><?php echo smartyTranslate(array('s'=>'Tab'),$_smarty_tpl);?>
</th>
							<th><?php echo smartyTranslate(array('s'=>'Categories'),$_smarty_tpl);?>
</th>
							<th><?php echo smartyTranslate(array('s'=>'Interest'),$_smarty_tpl);?>
</th>
							<th><?php echo smartyTranslate(array('s'=>'Favorite'),$_smarty_tpl);?>
</th>
						</tr>
					</thead>
					<tbody>
						<?php  $_smarty_tpl->tpl_vars['module'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['module']->_loop = false;
 $_smarty_tpl->tpl_vars['km'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['modules']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['module']->key => $_smarty_tpl->tpl_vars['module']->value) {
$_smarty_tpl->tpl_vars['module']->_loop = true;
 $_smarty_tpl->tpl_vars['km']->value = $_smarty_tpl->tpl_vars['module']->key;
?>
							<?php $_smarty_tpl->_capture_stack[0][] = array("moduleStatusClass", null, null); ob_start(); ?>
								<?php if (isset($_smarty_tpl->tpl_vars['module']->value->id)&&$_smarty_tpl->tpl_vars['module']->value->id>0) {?>
									module_active
								<?php } else { ?>
									module_inactive
								<?php }?>
							<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
							<tr>
								<td width="10px" class="<?php echo Smarty::$_smarty_vars['capture']['moduleStatusClass'];?>
"></td>
								<td width="40px">
									<img src="<?php if (isset($_smarty_tpl->tpl_vars['module']->value->image)) {?><?php echo $_smarty_tpl->tpl_vars['module']->value->image;?>
<?php } else { ?>../modules/<?php echo $_smarty_tpl->tpl_vars['module']->value->name;?>
/<?php echo $_smarty_tpl->tpl_vars['module']->value->logo;?>
<?php }?>" width="32" height="32" />
								</td>
								<td class="moduleName">
									<h4><?php echo $_smarty_tpl->tpl_vars['module']->value->displayName;?>
</h4>
									<span class="moduleFavDesc text-muted"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['truncate'][0][0]->smarty_modifier_truncate($_smarty_tpl->tpl_vars['module']->value->description,80,'...');?>
</span>
								</td>
								<td width="240px">
									<?php $_smarty_tpl->tpl_vars["module_name"] = new Smarty_variable($_smarty_tpl->tpl_vars['module']->value->name, null, 0);?>
									<select name="t_<?php echo $_smarty_tpl->tpl_vars['module']->value->name;?>
" multiple="multiple" class="chosen moduleTabPreferencesChoise">
										<?php  $_smarty_tpl->tpl_vars['t'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['t']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['tabs']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['t']->key => $_smarty_tpl->tpl_vars['t']->value) {
$_smarty_tpl->tpl_vars['t']->_loop = true;
?>
											<?php if ($_smarty_tpl->tpl_vars['t']->value['active']) {?>
												<option <?php if (isset($_smarty_tpl->tpl_vars['tab_modules_preferences']->value[$_smarty_tpl->tpl_vars['module_name']->value])&&in_array($_smarty_tpl->tpl_vars['t']->value['id_tab'],$_smarty_tpl->tpl_vars['tab_modules_preferences']->value[$_smarty_tpl->tpl_vars['module_name']->value])) {?> selected="selected" <?php }?> class="group" value="<?php echo $_smarty_tpl->tpl_vars['t']->value['id_tab'];?>
"><?php if ($_smarty_tpl->tpl_vars['t']->value['name']=='') {?><?php echo $_smarty_tpl->tpl_vars['t']->value['class_name'];?>
<?php } else { ?><?php echo $_smarty_tpl->tpl_vars['t']->value['name'];?>
<?php }?></option>
												<?php  $_smarty_tpl->tpl_vars['t2'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['t2']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['t']->value['sub_tabs']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['t2']->key => $_smarty_tpl->tpl_vars['t2']->value) {
$_smarty_tpl->tpl_vars['t2']->_loop = true;
?>
													<?php if ($_smarty_tpl->tpl_vars['t2']->value['active']) {?>
														<?php $_smarty_tpl->tpl_vars["id_tab"] = new Smarty_variable($_smarty_tpl->tpl_vars['t']->value['id_tab'], null, 0);?>
														<option <?php if (isset($_smarty_tpl->tpl_vars['tab_modules_preferences']->value[$_smarty_tpl->tpl_vars['module_name']->value])&&in_array($_smarty_tpl->tpl_vars['t2']->value['id_tab'],$_smarty_tpl->tpl_vars['tab_modules_preferences']->value[$_smarty_tpl->tpl_vars['module_name']->value])) {?> selected="selected" <?php }?> value="<?php echo $_smarty_tpl->tpl_vars['t2']->value['id_tab'];?>
"><?php if ($_smarty_tpl->tpl_vars['t2']->value['name']=='') {?><?php echo $_smarty_tpl->tpl_vars['t2']->value['class_name'];?>
<?php } else { ?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['t2']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
<?php }?></option>
													<?php }?>
												<?php } ?>
											<?php }?>
										<?php } ?>
									</select>
								</td>
								<td>
									<span><?php echo $_smarty_tpl->tpl_vars['module']->value->categoryName;?>
</span>
								</td>
								<td>
									<select name="i_<?php echo $_smarty_tpl->tpl_vars['module']->value->name;?>
" class="moduleFavorite">
										<option value="" selected="selected">-</option>
										<option value="1" <?php if (isset($_smarty_tpl->tpl_vars['module']->value->preferences['interest'])&&$_smarty_tpl->tpl_vars['module']->value->preferences['interest']=='1') {?>selected="selected"<?php }?>><?php echo smartyTranslate(array('s'=>'Yes'),$_smarty_tpl);?>
</option>
										<option value="0" <?php if (isset($_smarty_tpl->tpl_vars['module']->value->preferences['interest'])&&$_smarty_tpl->tpl_vars['module']->value->preferences['interest']=='0') {?>selected="selected"<?php }?>><?php echo smartyTranslate(array('s'=>'No'),$_smarty_tpl);?>
</option>
									</select>
								</td>
								<td>
									<select name="f_<?php echo $_smarty_tpl->tpl_vars['module']->value->name;?>
" class="moduleFavorite">
										<option value="" selected="selected">-</option>
										<option value="1" <?php if (isset($_smarty_tpl->tpl_vars['module']->value->preferences['favorite'])&&$_smarty_tpl->tpl_vars['module']->value->preferences['favorite']=='1') {?>selected="selected"<?php }?>><?php echo smartyTranslate(array('s'=>'Yes'),$_smarty_tpl);?>
</option>
										<option value="0" <?php if (isset($_smarty_tpl->tpl_vars['module']->value->preferences['favorite'])&&$_smarty_tpl->tpl_vars['module']->value->preferences['favorite']=='0') {?>selected="selected"<?php }?>><?php echo smartyTranslate(array('s'=>'No'),$_smarty_tpl);?>
</option>
									</select>
								</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<?php }} ?>
