<?php /* Smarty version Smarty-3.1.19, created on 2016-01-07 10:42:17
         compiled from "/home/sumit/public_html/html/hotel-reservation-system/admin_htl/themes/default/template/controllers/products/helpers/tree/tree_toolbar.tpl" */ ?>
<?php /*%%SmartyHeaderCode:674105659568df3b1c57f11-95058107%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '147b2987ab11c13d1bf1569e440a106fffa3978c' => 
    array (
      0 => '/home/sumit/public_html/html/hotel-reservation-system/admin_htl/themes/default/template/controllers/products/helpers/tree/tree_toolbar.tpl',
      1 => 1452142913,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '674105659568df3b1c57f11-95058107',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'actions' => 0,
    'action' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_568df3b1c8ce85_67534431',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_568df3b1c8ce85_67534431')) {function content_568df3b1c8ce85_67534431($_smarty_tpl) {?>
<div class="tree-actions pull-right">
	<?php if (isset($_smarty_tpl->tpl_vars['actions']->value)) {?>
	<?php  $_smarty_tpl->tpl_vars['action'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['action']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['actions']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['action']->key => $_smarty_tpl->tpl_vars['action']->value) {
$_smarty_tpl->tpl_vars['action']->_loop = true;
?>
		<?php echo $_smarty_tpl->tpl_vars['action']->value->render();?>

	<?php } ?>
	<?php }?>
</div><?php }} ?>
