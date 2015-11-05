<?php /* Smarty version Smarty-3.1.19, created on 2015-11-05 17:14:59
         compiled from "/home/sumit/public_html/html/ps-hotel-reservation-system/admin382ubvnd2/themes/default/template/helpers/tree/tree_node_item_radio.tpl" */ ?>
<?php /*%%SmartyHeaderCode:547240505563b413b03e7b1-87704880%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '39fb2671076fb1abec9d0ecc4d35ea79f1ba8e5c' => 
    array (
      0 => '/home/sumit/public_html/html/ps-hotel-reservation-system/admin382ubvnd2/themes/default/template/helpers/tree/tree_node_item_radio.tpl',
      1 => 1446723175,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '547240505563b413b03e7b1-87704880',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'node' => 0,
    'input_name' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_563b413b050752_60153735',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_563b413b050752_60153735')) {function content_563b413b050752_60153735($_smarty_tpl) {?>
<li class="tree-item<?php if (isset($_smarty_tpl->tpl_vars['node']->value['disabled'])&&$_smarty_tpl->tpl_vars['node']->value['disabled']==true) {?> tree-item-disable<?php }?>">
	<span class="tree-item-name">
		<input type="radio" name="<?php echo $_smarty_tpl->tpl_vars['input_name']->value;?>
" value="<?php echo $_smarty_tpl->tpl_vars['node']->value['id_category'];?>
"<?php if (isset($_smarty_tpl->tpl_vars['node']->value['disabled'])&&$_smarty_tpl->tpl_vars['node']->value['disabled']==true) {?> disabled="disabled"<?php }?> />
		<i class="tree-dot"></i>
		<label class="tree-toggler"><?php echo $_smarty_tpl->tpl_vars['node']->value['name'];?>
</label>
	</span>
</li><?php }} ?>
