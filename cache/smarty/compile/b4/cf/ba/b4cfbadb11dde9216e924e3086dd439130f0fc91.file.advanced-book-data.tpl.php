<?php /* Smarty version Smarty-3.1.19, created on 2016-01-07 10:53:48
         compiled from "/home/sumit/public_html/html/hotel-reservation-system/mails/en/advanced-book-data.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1088908618568d305fba4893-10745313%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b4cfbadb11dde9216e924e3086dd439130f0fc91' => 
    array (
      0 => '/home/sumit/public_html/html/hotel-reservation-system/mails/en/advanced-book-data.tpl',
      1 => 1452142835,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1088908618568d305fba4893-10745313',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_568d305fbace06_56904102',
  'variables' => 
  array (
    'list' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_568d305fbace06_56904102')) {function content_568d305fbace06_56904102($_smarty_tpl) {?><?php if (!empty($_smarty_tpl->tpl_vars['list']->value)) {?>
	<tr class="conf_body">
		<td bgcolor="#f8f8f8" colspan="4" style="border:1px solid #D6D4D4;color:#333;padding:7px 0">
			<table class="table" style="width:100%;border-collapse:collapse">
				<tr>
					<td width="10" style="color:#333;padding:0">&nbsp;</td>
					<td align="right" style="color:#333;padding:0">
						<font size="2" face="Open-sans, sans-serif" color="#555454">
							<strong>Total Paid</strong>
						</font>
					</td>
					<td width="10" style="color:#333;padding:0">&nbsp;</td>
				</tr>
			</table>
		</td>
		<td bgcolor="#f8f8f8" colspan="4" style="border:1px solid #D6D4D4;color:#333;padding:7px 0">
			<table class="table" style="width:100%;border-collapse:collapse">
				<tr>
					<td width="10" style="color:#333;padding:0">&nbsp;</td>
					<td align="right" style="color:#333;padding:0">
						<font size="4" face="Open-sans, sans-serif" color="#555454">
							<?php echo $_smarty_tpl->tpl_vars['list']->value['total_paid_amount'];?>

						</font>
					</td>
					<td width="10" style="color:#333;padding:0">&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>

	<tr class="conf_body">
		<td bgcolor="#f8f8f8" colspan="4" style="border:1px solid #D6D4D4;color:#333;padding:7px 0">
			<table class="table" style="width:100%;border-collapse:collapse">
				<tr>
					<td width="10" style="color:#333;padding:0">&nbsp;</td>
					<td align="right" style="color:#333;padding:0">
						<font size="2" face="Open-sans, sans-serif" color="#555454">
							<strong>Total Due</strong>
						</font>
					</td>
					<td width="10" style="color:#333;padding:0">&nbsp;</td>
				</tr>
			</table>
		</td>
		<td bgcolor="#f8f8f8" colspan="4" style="border:1px solid #D6D4D4;color:#333;padding:7px 0">
			<table class="table" style="width:100%;border-collapse:collapse">
				<tr>
					<td width="10" style="color:#333;padding:0">&nbsp;</td>
					<td align="right" style="color:#333;padding:0">
						<font size="4" face="Open-sans, sans-serif" color="#555454">
							<?php echo $_smarty_tpl->tpl_vars['list']->value['total_due_amount'];?>

						</font>
					</td>
					<td width="10" style="color:#333;padding:0">&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>
<?php }?><?php }} ?>
