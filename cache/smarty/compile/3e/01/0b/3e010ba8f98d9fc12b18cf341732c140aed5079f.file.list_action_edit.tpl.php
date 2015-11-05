<?php /* Smarty version Smarty-3.1.19, created on 2015-11-05 19:12:25
         compiled from "/home/sumit/public_html/html/ps-hotel-reservation-system/admin709mp39ny/themes/default/template/helpers/list/list_action_edit.tpl" */ ?>
<?php /*%%SmartyHeaderCode:611389283563b5cc12de4d3-29126567%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3e010ba8f98d9fc12b18cf341732c140aed5079f' => 
    array (
      0 => '/home/sumit/public_html/html/ps-hotel-reservation-system/admin709mp39ny/themes/default/template/helpers/list/list_action_edit.tpl',
      1 => 1446729263,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '611389283563b5cc12de4d3-29126567',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'href' => 0,
    'action' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_563b5cc12ea190_00303258',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_563b5cc12ea190_00303258')) {function content_563b5cc12ea190_00303258($_smarty_tpl) {?>
<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['href']->value, ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['action']->value, ENT_QUOTES, 'UTF-8', true);?>
" class="edit">
	<i class="icon-pencil"></i> <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['action']->value, ENT_QUOTES, 'UTF-8', true);?>

</a><?php }} ?>
