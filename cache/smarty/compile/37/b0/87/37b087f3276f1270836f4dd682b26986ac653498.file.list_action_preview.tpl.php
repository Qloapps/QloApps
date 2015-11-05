<?php /* Smarty version Smarty-3.1.19, created on 2015-11-03 05:49:26
         compiled from "/home/sumit/public_html/html/ps-hotel-reservation-system/admin757uelgkq/themes/default/template/helpers/list/list_action_preview.tpl" */ ?>
<?php /*%%SmartyHeaderCode:214294207556389136700da2-40341804%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '37b087f3276f1270836f4dd682b26986ac653498' => 
    array (
      0 => '/home/sumit/public_html/html/ps-hotel-reservation-system/admin757uelgkq/themes/default/template/helpers/list/list_action_preview.tpl',
      1 => 1446455061,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '214294207556389136700da2-40341804',
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
  'unifunc' => 'content_563891367354e6_00433121',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_563891367354e6_00433121')) {function content_563891367354e6_00433121($_smarty_tpl) {?>
<a href="<?php echo $_smarty_tpl->tpl_vars['href']->value;?>
" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['action']->value, ENT_QUOTES, 'UTF-8', true);?>
" target="_blank">
	<i class="icon-eye"></i> <?php echo $_smarty_tpl->tpl_vars['action']->value;?>

</a>
<?php }} ?>
