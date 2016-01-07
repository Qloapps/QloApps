<?php /* Smarty version Smarty-3.1.19, created on 2016-01-06 20:19:29
         compiled from "/home/sumit/public_html/html/hotel-reservation-system/pdf/invoice.addresses-tab.tpl" */ ?>
<?php /*%%SmartyHeaderCode:131424632568d2979e24755-66967362%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a30d2095740e48ea19114f0a1e0b9153b47d5c3b' => 
    array (
      0 => '/home/sumit/public_html/html/hotel-reservation-system/pdf/invoice.addresses-tab.tpl',
      1 => 1451999489,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '131424632568d2979e24755-66967362',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'order_invoice' => 0,
    'delivery_address' => 0,
    'invoice_address' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_568d2979e32e88_99772819',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_568d2979e32e88_99772819')) {function content_568d2979e32e88_99772819($_smarty_tpl) {?>
<table id="addresses-tab" cellspacing="0" cellpadding="0">
	<tr>
		<!-- <td width="33%"><span class="bold"> </span><br/><br/>
			<?php if (isset($_smarty_tpl->tpl_vars['order_invoice']->value)) {?><?php echo $_smarty_tpl->tpl_vars['order_invoice']->value->shop_address;?>
<?php }?>
		</td> -->
		<!-- <td width="33%"><?php if ($_smarty_tpl->tpl_vars['delivery_address']->value) {?><span class="bold"><?php echo smartyTranslate(array('s'=>'Delivery Address','pdf'=>'true'),$_smarty_tpl);?>
</span><br/><br/>
				<?php echo $_smarty_tpl->tpl_vars['delivery_address']->value;?>

			<?php }?>
		</td> -->
		<td width="33%"></td>
		<td  width="33%"></td>
		<td width="33%"><span class="bold"><?php echo smartyTranslate(array('s'=>'Customer Address','pdf'=>'true'),$_smarty_tpl);?>
</span><br/><br/>
				<?php echo $_smarty_tpl->tpl_vars['invoice_address']->value;?>

		</td>
	</tr>
</table>
<?php }} ?>
