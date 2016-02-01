<?php /* Smarty version Smarty-3.1.19, created on 2016-01-29 15:44:58
         compiled from "/home/sumit/public_html/html/hotelcommerce-master/admin/themes/default/template/controllers/outstanding/_print_pdf_icon.tpl" */ ?>
<?php /*%%SmartyHeaderCode:103808882256ab3ba2b98253-17769382%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd95f6befffec2317aa0886bf32530d98f542cb2d' => 
    array (
      0 => '/home/sumit/public_html/html/hotelcommerce-master/admin/themes/default/template/controllers/outstanding/_print_pdf_icon.tpl',
      1 => 1454062119,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '103808882256ab3ba2b98253-17769382',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'link' => 0,
    'id_invoice' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_56ab3ba2ba4451_95896058',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_56ab3ba2ba4451_95896058')) {function content_56ab3ba2ba4451_95896058($_smarty_tpl) {?>


<?php if (Configuration::get('PS_INVOICE')) {?>
	<span style="width:20px; margin-right:5px;">
		<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminPdf'), ENT_QUOTES, 'UTF-8', true);?>
&amp;submitAction=generateInvoicePDF&amp;id_order_invoice=<?php echo $_smarty_tpl->tpl_vars['id_invoice']->value;?>
"><img src="../img/admin/tab-invoice.gif" alt="invoice" /></a>
	</span>
<?php }?><?php }} ?>
