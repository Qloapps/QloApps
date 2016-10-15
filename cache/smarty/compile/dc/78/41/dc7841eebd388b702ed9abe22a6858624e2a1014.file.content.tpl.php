<?php /* Smarty version Smarty-3.1.19, created on 2016-10-14 12:50:29
         compiled from "/home/rjain/www/html/d4/htld6/adminhtl/themes/default/template/content.tpl" */ ?>
<?php /*%%SmartyHeaderCode:18184055415800873dd946a3-99829689%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'dc7841eebd388b702ed9abe22a6858624e2a1014' => 
    array (
      0 => '/home/rjain/www/html/d4/htld6/adminhtl/themes/default/template/content.tpl',
      1 => 1475505965,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '18184055415800873dd946a3-99829689',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'content' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_5800873dd9ed48_96061936',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5800873dd9ed48_96061936')) {function content_5800873dd9ed48_96061936($_smarty_tpl) {?>
<div id="ajax_confirmation" class="alert alert-success hide"></div>

<div id="ajaxBox" style="display:none"></div>


<div class="row">
	<div class="col-lg-12">
		<?php if (isset($_smarty_tpl->tpl_vars['content']->value)) {?>
			<?php echo $_smarty_tpl->tpl_vars['content']->value;?>

		<?php }?>
	</div>
</div><?php }} ?>
