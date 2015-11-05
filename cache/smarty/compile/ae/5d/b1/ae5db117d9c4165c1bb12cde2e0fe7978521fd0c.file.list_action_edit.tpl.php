<?php /* Smarty version Smarty-3.1.19, created on 2015-11-05 16:05:58
         compiled from "/home/sumit/public_html/html/ps-hotel-reservation-system/admin945qmi5wu/themes/default/template/helpers/list/list_action_edit.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2138724082563b310e64d915-42641729%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ae5db117d9c4165c1bb12cde2e0fe7978521fd0c' => 
    array (
      0 => '/home/sumit/public_html/html/ps-hotel-reservation-system/admin945qmi5wu/themes/default/template/helpers/list/list_action_edit.tpl',
      1 => 1446455062,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2138724082563b310e64d915-42641729',
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
  'unifunc' => 'content_563b310e65aa89_72697512',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_563b310e65aa89_72697512')) {function content_563b310e65aa89_72697512($_smarty_tpl) {?>
<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['href']->value, ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['action']->value, ENT_QUOTES, 'UTF-8', true);?>
" class="edit">
	<i class="icon-pencil"></i> <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['action']->value, ENT_QUOTES, 'UTF-8', true);?>

</a><?php }} ?>
