<?php /* Smarty version Smarty-3.1.19, created on 2016-01-30 00:52:29
         compiled from "/home/sumit/public_html/html/hotelcommerce-master/admin977xro9m8/themes/default/template/helpers/list/list_action_edit.tpl" */ ?>
<?php /*%%SmartyHeaderCode:9913508256ab8abd5fc556-59118395%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '15900ece85d1214a956651e5b2ef781856bb46e0' => 
    array (
      0 => '/home/sumit/public_html/html/hotelcommerce-master/admin977xro9m8/themes/default/template/helpers/list/list_action_edit.tpl',
      1 => 1454062118,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '9913508256ab8abd5fc556-59118395',
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
  'unifunc' => 'content_56ab8abd632aa7_88487965',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56ab8abd632aa7_88487965')) {function content_56ab8abd632aa7_88487965($_smarty_tpl) {?>
<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['href']->value, ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['action']->value, ENT_QUOTES, 'UTF-8', true);?>
" class="edit">
	<i class="icon-pencil"></i> <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['action']->value, ENT_QUOTES, 'UTF-8', true);?>

</a><?php }} ?>
