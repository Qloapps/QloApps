<?php /* Smarty version Smarty-3.1.19, created on 2016-01-07 11:41:32
         compiled from "/home/sumit/public_html/html/hotel-reservation-system/admin_htl/themes/default/template/helpers/list/list_action_view.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1677636859568e0194a8db99-90060989%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8ecdc99c556d817c2f2b3c2072f1f4ef109421ea' => 
    array (
      0 => '/home/sumit/public_html/html/hotel-reservation-system/admin_htl/themes/default/template/helpers/list/list_action_view.tpl',
      1 => 1452142888,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1677636859568e0194a8db99-90060989',
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
  'unifunc' => 'content_568e0194abb488_88305851',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_568e0194abb488_88305851')) {function content_568e0194abb488_88305851($_smarty_tpl) {?>
<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['href']->value, ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['action']->value, ENT_QUOTES, 'UTF-8', true);?>
" >
	<i class="icon-search-plus"></i> <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['action']->value, ENT_QUOTES, 'UTF-8', true);?>

</a><?php }} ?>
