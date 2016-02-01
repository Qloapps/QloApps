<?php /* Smarty version Smarty-3.1.19, created on 2016-01-29 15:44:56
         compiled from "/home/sumit/public_html/html/hotelcommerce-master/admin/themes/default/template/helpers/list/list_action_supply_order_change_state.tpl" */ ?>
<?php /*%%SmartyHeaderCode:21126654256ab3ba03b1902-71083980%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e8beadc0ef19ac84b543ec12e31ee13244e563f9' => 
    array (
      0 => '/home/sumit/public_html/html/hotelcommerce-master/admin/themes/default/template/helpers/list/list_action_supply_order_change_state.tpl',
      1 => 1454062118,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '21126654256ab3ba03b1902-71083980',
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
  'unifunc' => 'content_56ab3ba03bb859_26124971',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56ab3ba03bb859_26124971')) {function content_56ab3ba03bb859_26124971($_smarty_tpl) {?>
<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['href']->value, ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['action']->value, ENT_QUOTES, 'UTF-8', true);?>
">
	<i class="icon-time"></i> <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['action']->value, ENT_QUOTES, 'UTF-8', true);?>

</a><?php }} ?>
