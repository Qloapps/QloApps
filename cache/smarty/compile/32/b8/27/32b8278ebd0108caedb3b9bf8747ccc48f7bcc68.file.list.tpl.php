<?php /* Smarty version Smarty-3.1.19, created on 2016-01-07 10:35:28
         compiled from "/home/sumit/public_html/html/hotel-reservation-system/admin/themes/default/template/helpers/shops_list/list.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1083642818568df2183f54c3-67032300%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '32b8278ebd0108caedb3b9bf8747ccc48f7bcc68' => 
    array (
      0 => '/home/sumit/public_html/html/hotel-reservation-system/admin/themes/default/template/helpers/shops_list/list.tpl',
      1 => 1452142888,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1083642818568df2183f54c3-67032300',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'current_shop_name' => 0,
    'current_shop_value' => 0,
    'url' => 0,
    'tree' => 0,
    'multishop_context' => 0,
    'is_group_context' => 0,
    'group_id' => 0,
    'multishop_context_group' => 0,
    'group_data' => 0,
    'is_shop_context' => 0,
    'shop_data' => 0,
    'shop_id' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_568df218450168_61320275',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_568df218450168_61320275')) {function content_568df218450168_61320275($_smarty_tpl) {?>
<a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><?php echo $_smarty_tpl->tpl_vars['current_shop_name']->value;?>
 <i class="icon-caret-down"></i></a><ul class="dropdown-menu"><li<?php if (!isset($_smarty_tpl->tpl_vars['current_shop_value']->value)||$_smarty_tpl->tpl_vars['current_shop_value']->value=='') {?> class="active"<?php }?>><a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['url']->value, ENT_QUOTES, 'UTF-8', true);?>
"><?php echo smartyTranslate(array('s'=>'All shops'),$_smarty_tpl);?>
</a></li><?php  $_smarty_tpl->tpl_vars['group_data'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['group_data']->_loop = false;
 $_smarty_tpl->tpl_vars['group_id'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['tree']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['group_data']->key => $_smarty_tpl->tpl_vars['group_data']->value) {
$_smarty_tpl->tpl_vars['group_data']->_loop = true;
 $_smarty_tpl->tpl_vars['group_id']->value = $_smarty_tpl->tpl_vars['group_data']->key;
?><?php if (!isset($_smarty_tpl->tpl_vars['multishop_context']->value)||$_smarty_tpl->tpl_vars['is_group_context']->value) {?><li class="group<?php if ($_smarty_tpl->tpl_vars['current_shop_value']->value==('g-').($_smarty_tpl->tpl_vars['group_id']->value)) {?> active<?php }?><?php if ($_smarty_tpl->tpl_vars['multishop_context_group']->value==false) {?> disabled<?php }?>"><a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['url']->value, ENT_QUOTES, 'UTF-8', true);?>
g-<?php echo $_smarty_tpl->tpl_vars['group_id']->value;?>
"><?php echo smartyTranslate(array('s'=>'%s group','sprintf'=>array(htmlspecialchars($_smarty_tpl->tpl_vars['group_data']->value['name'], ENT_QUOTES, 'UTF-8', true))),$_smarty_tpl);?>
</a></li><?php } else { ?><ul class="group <?php if ($_smarty_tpl->tpl_vars['multishop_context_group']->value==false) {?> disabled<?php }?>"><?php echo smartyTranslate(array('s'=>'%s group','sprintf'=>array(htmlspecialchars($_smarty_tpl->tpl_vars['group_data']->value['name'], ENT_QUOTES, 'UTF-8', true))),$_smarty_tpl);?>
<?php }?><?php if (!isset($_smarty_tpl->tpl_vars['multishop_context']->value)||$_smarty_tpl->tpl_vars['is_shop_context']->value) {?><?php  $_smarty_tpl->tpl_vars['shop_data'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['shop_data']->_loop = false;
 $_smarty_tpl->tpl_vars['shop_id'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['group_data']->value['shops']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['shop_data']->key => $_smarty_tpl->tpl_vars['shop_data']->value) {
$_smarty_tpl->tpl_vars['shop_data']->_loop = true;
 $_smarty_tpl->tpl_vars['shop_id']->value = $_smarty_tpl->tpl_vars['shop_data']->key;
?><?php if (($_smarty_tpl->tpl_vars['shop_data']->value['active'])) {?><li class="shop<?php if ($_smarty_tpl->tpl_vars['current_shop_value']->value==('s-').($_smarty_tpl->tpl_vars['shop_id']->value)) {?> active<?php }?>"><a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['url']->value, ENT_QUOTES, 'UTF-8', true);?>
s-<?php echo $_smarty_tpl->tpl_vars['shop_id']->value;?>
"><?php if ($_smarty_tpl->tpl_vars['multishop_context_group']->value==false) {?><?php echo (htmlspecialchars($_smarty_tpl->tpl_vars['group_data']->value['name'], ENT_QUOTES, 'UTF-8', true)).(' - ').($_smarty_tpl->tpl_vars['shop_data']->value['name']);?>
<?php } else { ?><?php echo $_smarty_tpl->tpl_vars['shop_data']->value['name'];?>
<?php }?></a></li><?php }?><?php } ?><?php }?><?php if (!(!isset($_smarty_tpl->tpl_vars['multishop_context']->value)||$_smarty_tpl->tpl_vars['is_group_context']->value)) {?></ul><?php }?><?php } ?></ul>
<?php }} ?>
