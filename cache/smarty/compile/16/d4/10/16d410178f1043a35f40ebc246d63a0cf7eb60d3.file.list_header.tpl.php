<?php /* Smarty version Smarty-3.1.19, created on 2015-11-02 20:02:46
         compiled from "/home/sumit/public_html/html/ps-hotel-reservation-system/admin/themes/default/template/controllers/tax_rules/helpers/list/list_header.tpl" */ ?>
<?php /*%%SmartyHeaderCode:13204180295637740ea80572-94785571%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '16d410178f1043a35f40ebc246d63a0cf7eb60d3' => 
    array (
      0 => '/home/sumit/public_html/html/ps-hotel-reservation-system/admin/themes/default/template/controllers/tax_rules/helpers/list/list_header.tpl',
      1 => 1446455072,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '13204180295637740ea80572-94785571',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'name_controller' => 0,
    'hookName' => 0,
    'currentIndex' => 0,
    'identifier' => 0,
    'token' => 0,
    'id_tax_rules_group' => 0,
    'table' => 0,
    'list_id' => 0,
    'table_id' => 0,
    'table_dnd' => 0,
    'bulk_actions' => 0,
    'has_bulk_actions' => 0,
    'fields_display' => 0,
    'params' => 0,
    'shop_link_type' => 0,
    'has_actions' => 0,
    'filters_has_value' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5637740eb04fd4_83453023',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5637740eb04fd4_83453023')) {function content_5637740eb04fd4_83453023($_smarty_tpl) {?>

<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>'displayAdminListBefore'),$_smarty_tpl);?>

<?php if (isset($_smarty_tpl->tpl_vars['name_controller']->value)) {?>
	<?php $_smarty_tpl->_capture_stack[0][] = array('hookName', 'hookName', null); ob_start(); ?>display<?php echo ucfirst($_smarty_tpl->tpl_vars['name_controller']->value);?>
ListBefore<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
	<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>$_smarty_tpl->tpl_vars['hookName']->value),$_smarty_tpl);?>

<?php } elseif (isset($_GET['controller'])) {?>
	<?php $_smarty_tpl->_capture_stack[0][] = array('hookName', 'hookName', null); ob_start(); ?>display<?php echo htmlentities(ucfirst($_GET['controller']));?>
ListBefore<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
	<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>$_smarty_tpl->tpl_vars['hookName']->value),$_smarty_tpl);?>

<?php }?>

<form method="post" action="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['currentIndex']->value, ENT_QUOTES, 'UTF-8', true);?>
&amp;<?php echo $_smarty_tpl->tpl_vars['identifier']->value;?>
&amp;token=<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['token']->value, ENT_QUOTES, 'UTF-8', true);?>
&amp;id_tax_rules_group=<?php echo $_smarty_tpl->tpl_vars['id_tax_rules_group']->value;?>
&amp;updatetax_rules_group#<?php echo $_smarty_tpl->tpl_vars['table']->value;?>
" class="form">
	<div class="panel">
		<input type="hidden" id="submitFilter<?php echo $_smarty_tpl->tpl_vars['list_id']->value;?>
" name="submitFilter<?php echo $_smarty_tpl->tpl_vars['list_id']->value;?>
" value="0"/>
		<div class="table-responsive clearfix">
			<table<?php if ($_smarty_tpl->tpl_vars['table_id']->value) {?> id="table-<?php echo $_smarty_tpl->tpl_vars['table_id']->value;?>
"<?php }?> class="table<?php if ($_smarty_tpl->tpl_vars['table_dnd']->value) {?> tableDnD<?php }?> <?php echo $_smarty_tpl->tpl_vars['list_id']->value;?>
">
				<?php if ($_smarty_tpl->tpl_vars['bulk_actions']->value&&$_smarty_tpl->tpl_vars['has_bulk_actions']->value) {?>
				<col style="width: 10px;" />
				<?php }?>
				<?php  $_smarty_tpl->tpl_vars['params'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['params']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['fields_display']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['params']->key => $_smarty_tpl->tpl_vars['params']->value) {
$_smarty_tpl->tpl_vars['params']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['params']->key;
?>
					<col<?php if (isset($_smarty_tpl->tpl_vars['params']->value['width'])&&$_smarty_tpl->tpl_vars['params']->value['width']!='auto') {?> width="<?php echo $_smarty_tpl->tpl_vars['params']->value['width'];?>
px"<?php }?>/>
				<?php } ?>
				<?php if ($_smarty_tpl->tpl_vars['shop_link_type']->value) {?>
					<col style="width: 80px;"/>
				<?php }?>
				<?php if ($_smarty_tpl->tpl_vars['has_actions']->value) {?>
					<col style="width: 52px;" />
				<?php }?>
				<thead>
					<tr class="nodrag nodrop">
						<?php if ($_smarty_tpl->tpl_vars['bulk_actions']->value&&$_smarty_tpl->tpl_vars['has_bulk_actions']->value) {?>
							<th class="center"></th>
						<?php }?>
						<?php  $_smarty_tpl->tpl_vars['params'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['params']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['fields_display']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['params']->key => $_smarty_tpl->tpl_vars['params']->value) {
$_smarty_tpl->tpl_vars['params']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['params']->key;
?>
							<th<?php if (isset($_smarty_tpl->tpl_vars['params']->value['align'])) {?> align="<?php echo $_smarty_tpl->tpl_vars['params']->value['align'];?>
"<?php }?><?php if (isset($_smarty_tpl->tpl_vars['params']->value['class'])) {?> class="<?php echo $_smarty_tpl->tpl_vars['params']->value['class'];?>
"<?php }?>>
								<?php if (isset($_smarty_tpl->tpl_vars['params']->value['hint'])) {?><span class="hint" name="help_box"><?php echo $_smarty_tpl->tpl_vars['params']->value['hint'];?>
<span class="hint-pointer">&nbsp;</span></span><?php }?>
								<span class="title_box">
									<?php echo $_smarty_tpl->tpl_vars['params']->value['title'];?>

								</span>
							</th>
						<?php } ?>
						<?php if ($_smarty_tpl->tpl_vars['shop_link_type']->value) {?>
							<th>
								<?php if ($_smarty_tpl->tpl_vars['shop_link_type']->value=='shop') {?>
									<?php echo smartyTranslate(array('s'=>'Shop'),$_smarty_tpl);?>

								<?php } else { ?>
									<?php echo smartyTranslate(array('s'=>'Shop group'),$_smarty_tpl);?>

								<?php }?>
							</th>
						<?php }?>
						<?php if ($_smarty_tpl->tpl_vars['has_actions']->value&&$_smarty_tpl->tpl_vars['filters_has_value']->value) {?>
							<th class="actions text-right"><button type="submit" name="submitReset<?php echo $_smarty_tpl->tpl_vars['list_id']->value;?>
" class="btn btn-warning">
									<i class="icon-eraser"></i> <?php echo smartyTranslate(array('s'=>'Reset'),$_smarty_tpl);?>

								</button>
							</th>
						<?php } else { ?>
							<th class="actions text-right"></th>
						<?php }?>
					</tr>
				</thead><?php }} ?>
