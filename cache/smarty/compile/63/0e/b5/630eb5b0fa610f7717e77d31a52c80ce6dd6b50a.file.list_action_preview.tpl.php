<?php /* Smarty version Smarty-3.1.19, created on 2015-11-05 17:14:59
         compiled from "/home/sumit/public_html/html/ps-hotel-reservation-system/admin382ubvnd2/themes/default/template/helpers/list/list_action_preview.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1792594573563b413b988888-39749747%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '630eb5b0fa610f7717e77d31a52c80ce6dd6b50a' => 
    array (
      0 => '/home/sumit/public_html/html/ps-hotel-reservation-system/admin382ubvnd2/themes/default/template/helpers/list/list_action_preview.tpl',
      1 => 1446723175,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1792594573563b413b988888-39749747',
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
  'unifunc' => 'content_563b413b990cc4_21914758',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_563b413b990cc4_21914758')) {function content_563b413b990cc4_21914758($_smarty_tpl) {?>
<a href="<?php echo $_smarty_tpl->tpl_vars['href']->value;?>
" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['action']->value, ENT_QUOTES, 'UTF-8', true);?>
" target="_blank">
	<i class="icon-eye"></i> <?php echo $_smarty_tpl->tpl_vars['action']->value;?>

</a>
<?php }} ?>
