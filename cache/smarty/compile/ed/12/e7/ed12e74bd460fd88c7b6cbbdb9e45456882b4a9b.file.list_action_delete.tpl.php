<?php /* Smarty version Smarty-3.1.19, created on 2015-11-05 17:58:23
         compiled from "/home/sumit/public_html/html/ps-hotel-reservation-system/admin338kklw7d/themes/default/template/helpers/list/list_action_delete.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1325505318563b4b67500b78-60468662%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ed12e74bd460fd88c7b6cbbdb9e45456882b4a9b' => 
    array (
      0 => '/home/sumit/public_html/html/ps-hotel-reservation-system/admin338kklw7d/themes/default/template/helpers/list/list_action_delete.tpl',
      1 => 1446725531,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1325505318563b4b67500b78-60468662',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'href' => 0,
    'confirm' => 0,
    'action' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_563b4b675164b3_39349723',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_563b4b675164b3_39349723')) {function content_563b4b675164b3_39349723($_smarty_tpl) {?>
<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['href']->value, ENT_QUOTES, 'UTF-8', true);?>
"<?php if (isset($_smarty_tpl->tpl_vars['confirm']->value)) {?> onclick="if (confirm('<?php echo $_smarty_tpl->tpl_vars['confirm']->value;?>
')){return true;}else{event.stopPropagation(); event.preventDefault();};"<?php }?> title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['action']->value, ENT_QUOTES, 'UTF-8', true);?>
" class="delete">
	<i class="icon-trash"></i> <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['action']->value, ENT_QUOTES, 'UTF-8', true);?>

</a><?php }} ?>
