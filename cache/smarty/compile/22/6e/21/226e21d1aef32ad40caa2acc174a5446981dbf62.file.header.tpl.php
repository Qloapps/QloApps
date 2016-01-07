<?php /* Smarty version Smarty-3.1.19, created on 2016-01-06 20:19:29
         compiled from "/home/sumit/public_html/html/hotel-reservation-system/pdf/header.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1812022988568d2979cf97c8-82166594%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '226e21d1aef32ad40caa2acc174a5446981dbf62' => 
    array (
      0 => '/home/sumit/public_html/html/hotel-reservation-system/pdf/header.tpl',
      1 => 1451999489,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1812022988568d2979cf97c8-82166594',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'logo_path' => 0,
    'width_logo' => 0,
    'height_logo' => 0,
    'header' => 0,
    'date' => 0,
    'title' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_568d2979d0dda9_06587866',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_568d2979d0dda9_06587866')) {function content_568d2979d0dda9_06587866($_smarty_tpl) {?>

<table style="width: 100%">
<tr>
	<td style="width: 50%">
		<?php if ($_smarty_tpl->tpl_vars['logo_path']->value) {?>
			<img src="<?php echo $_smarty_tpl->tpl_vars['logo_path']->value;?>
" style="width:<?php echo $_smarty_tpl->tpl_vars['width_logo']->value;?>
px; height:<?php echo $_smarty_tpl->tpl_vars['height_logo']->value;?>
px;" />
		<?php }?>
	</td>
	<td style="width: 50%; text-align: right;">
		<table style="width: 100%">
			<tr>
				<td style="font-weight: bold; font-size: 14pt; color: #444; width: 100%"><?php if (isset($_smarty_tpl->tpl_vars['header']->value)) {?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['header']->value, ENT_QUOTES, 'UTF-8', true);?>
<?php }?></td>
			</tr>
			<tr>
				<td style="font-size: 14pt; color: #9E9F9E"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['date']->value, ENT_QUOTES, 'UTF-8', true);?>
</td>
			</tr>
			<tr>
				<td style="font-size: 14pt; color: #9E9F9E"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['title']->value, ENT_QUOTES, 'UTF-8', true);?>
</td>
			</tr>
		</table>
	</td>
</tr>
</table>

<?php }} ?>
