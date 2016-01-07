<?php /* Smarty version Smarty-3.1.19, created on 2016-01-07 10:35:34
         compiled from "/home/sumit/public_html/html/hotel-reservation-system/admin/themes/default/template/content.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1591723779568df21e2931d5-54772117%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e52d0c0d5e51a7d8d198d9048647a8a19cfa5ed9' => 
    array (
      0 => '/home/sumit/public_html/html/hotel-reservation-system/admin/themes/default/template/content.tpl',
      1 => 1452142870,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1591723779568df21e2931d5-54772117',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'content' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_568df21e2992a6_50195995',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_568df21e2992a6_50195995')) {function content_568df21e2992a6_50195995($_smarty_tpl) {?>
<div id="ajax_confirmation" class="alert alert-success hide"></div>

<div id="ajaxBox" style="display:none"></div>


<div class="row">
	<div class="col-lg-12">
		<?php if (isset($_smarty_tpl->tpl_vars['content']->value)) {?>
			<?php echo $_smarty_tpl->tpl_vars['content']->value;?>

		<?php }?>
	</div>
</div><?php }} ?>
