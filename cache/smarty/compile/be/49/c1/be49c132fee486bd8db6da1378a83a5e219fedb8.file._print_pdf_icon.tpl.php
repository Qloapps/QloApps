<?php /* Smarty version Smarty-3.1.19, created on 2016-01-07 10:35:31
         compiled from "/home/sumit/public_html/html/hotel-reservation-system/admin/themes/default/template/controllers/slip/_print_pdf_icon.tpl" */ ?>
<?php /*%%SmartyHeaderCode:867421319568df21b0227d1-16676483%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'be49c132fee486bd8db6da1378a83a5e219fedb8' => 
    array (
      0 => '/home/sumit/public_html/html/hotel-reservation-system/admin/themes/default/template/controllers/slip/_print_pdf_icon.tpl',
      1 => 1452142888,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '867421319568df21b0227d1-16676483',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'link' => 0,
    'order_slip' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_568df21b02df48_72074161',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_568df21b02df48_72074161')) {function content_568df21b02df48_72074161($_smarty_tpl) {?>



<a class="btn btn-default _blank" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminPdf'), ENT_QUOTES, 'UTF-8', true);?>
&amp;submitAction=generateOrderSlipPDF&amp;id_order_slip=<?php echo intval($_smarty_tpl->tpl_vars['order_slip']->value->id);?>
">
	<i class="icon-file-text"></i>
	<?php echo smartyTranslate(array('s'=>'Download credit slip'),$_smarty_tpl);?>

</a>

<?php }} ?>
