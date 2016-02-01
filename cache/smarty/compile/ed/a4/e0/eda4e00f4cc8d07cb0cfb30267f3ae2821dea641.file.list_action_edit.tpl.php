<?php /* Smarty version Smarty-3.1.19, created on 2016-01-29 15:44:56
         compiled from "/home/sumit/public_html/html/hotelcommerce-master/admin/themes/default/template/helpers/list/list_action_edit.tpl" */ ?>
<?php /*%%SmartyHeaderCode:140690974156ab3ba0388db7-31845790%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'eda4e00f4cc8d07cb0cfb30267f3ae2821dea641' => 
    array (
      0 => '/home/sumit/public_html/html/hotelcommerce-master/admin/themes/default/template/helpers/list/list_action_edit.tpl',
      1 => 1454062118,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '140690974156ab3ba0388db7-31845790',
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
  'unifunc' => 'content_56ab3ba0392fc1_63655488',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56ab3ba0392fc1_63655488')) {function content_56ab3ba0392fc1_63655488($_smarty_tpl) {?>
<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['href']->value, ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['action']->value, ENT_QUOTES, 'UTF-8', true);?>
" class="edit">
	<i class="icon-pencil"></i> <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['action']->value, ENT_QUOTES, 'UTF-8', true);?>

</a><?php }} ?>
