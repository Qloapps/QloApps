<?php /* Smarty version Smarty-3.1.19, created on 2015-11-03 08:57:10
         compiled from "/home/sumit/public_html/html/ps-hotel-reservation-system/admin980qbfmdx/themes/default/template/helpers/tree/tree_node_item_radio.tpl" */ ?>
<?php /*%%SmartyHeaderCode:6000765875638bd368a6713-06386762%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '45c4e47b86ad5dd53e90d084d1d13047e9faeed7' => 
    array (
      0 => '/home/sumit/public_html/html/ps-hotel-reservation-system/admin980qbfmdx/themes/default/template/helpers/tree/tree_node_item_radio.tpl',
      1 => 1446455061,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '6000765875638bd368a6713-06386762',
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
  'unifunc' => 'content_5638bd36904af4_99812441',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5638bd36904af4_99812441')) {function content_5638bd36904af4_99812441($_smarty_tpl) {?>
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
