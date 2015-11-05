<?php /* Smarty version Smarty-3.1.19, created on 2015-11-05 19:12:25
         compiled from "/home/sumit/public_html/html/ps-hotel-reservation-system/admin709mp39ny/themes/default/template/helpers/list/list_action_preview.tpl" */ ?>
<?php /*%%SmartyHeaderCode:881188310563b5cc12f4fe0-17832639%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2fa2e173cf77dee2b2eaafc0a7b73bbb798669fc' => 
    array (
      0 => '/home/sumit/public_html/html/ps-hotel-reservation-system/admin709mp39ny/themes/default/template/helpers/list/list_action_preview.tpl',
      1 => 1446729263,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '881188310563b5cc12f4fe0-17832639',
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
  'unifunc' => 'content_563b5cc12fd468_35590476',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_563b5cc12fd468_35590476')) {function content_563b5cc12fd468_35590476($_smarty_tpl) {?>
<a href="<?php echo $_smarty_tpl->tpl_vars['href']->value;?>
" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['action']->value, ENT_QUOTES, 'UTF-8', true);?>
" target="_blank">
	<i class="icon-eye"></i> <?php echo $_smarty_tpl->tpl_vars['action']->value;?>

</a>
<?php }} ?>
