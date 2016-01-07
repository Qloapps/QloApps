<?php /* Smarty version Smarty-3.1.19, created on 2016-01-07 10:53:48
         compiled from "/home/sumit/public_html/html/hotel-reservation-system/mails/en/hotel-booking-cart-data.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1741557276568d305fb63eb8-13205090%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '9774cc28557501c2d270ce5633decf7bca7a3bf6' => 
    array (
      0 => '/home/sumit/public_html/html/hotel-reservation-system/mails/en/hotel-booking-cart-data.tpl',
      1 => 1452142835,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1741557276568d305fb63eb8-13205090',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_568d305fb9f911_06967835',
  'variables' => 
  array (
    'list' => 0,
    'data_v' => 0,
    'rm_v' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_568d305fb9f911_06967835')) {function content_568d305fb9f911_06967835($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include '/home/sumit/public_html/html/hotel-reservation-system/tools/smarty/plugins/modifier.date_format.php';
?><?php if (isset($_smarty_tpl->tpl_vars['list']->value)) {?>
	<?php  $_smarty_tpl->tpl_vars['data_v'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['data_v']->_loop = false;
 $_smarty_tpl->tpl_vars['data_k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['list']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['data_v']->key => $_smarty_tpl->tpl_vars['data_v']->value) {
$_smarty_tpl->tpl_vars['data_v']->_loop = true;
 $_smarty_tpl->tpl_vars['data_k']->value = $_smarty_tpl->tpl_vars['data_v']->key;
?>
		<?php  $_smarty_tpl->tpl_vars['rm_v'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['rm_v']->_loop = false;
 $_smarty_tpl->tpl_vars['rm_k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['data_v']->value['date_diff']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['rm_v']->key => $_smarty_tpl->tpl_vars['rm_v']->value) {
$_smarty_tpl->tpl_vars['rm_v']->_loop = true;
 $_smarty_tpl->tpl_vars['rm_k']->value = $_smarty_tpl->tpl_vars['rm_v']->key;
?>
		<tr>
			<td style="border:1px solid #D6D4D4;">
				<table class="table">
					<tr>
						<td width="10">&nbsp;</td>
						<td class="text-center">
							<font size="2" face="Open-sans, sans-serif" color="#555454">
								<img src="<?php echo $_smarty_tpl->tpl_vars['data_v']->value['cover_img'];?>
" class="img-responsive" />
							</font>
						</td>
						<td width="10">&nbsp;</td>
					</tr>
				</table>
			</td>
			<td style="border:1px solid #D6D4D4;">
				<table class="table">
					<tr>
						<td width="10">&nbsp;</td>
						<td  class="text-center">
							<font size="2" face="Open-sans, sans-serif" color="#555454">
								<?php echo $_smarty_tpl->tpl_vars['data_v']->value['name'];?>

							</font>
						</td>
						<td width="10">&nbsp;</td>
					</tr>
				</table>
			</td>
			<td style="border:1px solid #D6D4D4;">
				<table class="table">
					<tr>
						<td width="10">&nbsp;</td>
						<td  class="text-center">
							<font size="2" face="Open-sans, sans-serif" color="#555454">
								<strong>
									<?php echo $_smarty_tpl->tpl_vars['data_v']->value['adult'];?>
 <?php echo smartyTranslate(array('s'=>'Adults'),$_smarty_tpl);?>
, <?php echo $_smarty_tpl->tpl_vars['data_v']->value['children'];?>
 <?php echo smartyTranslate(array('s'=>'Children'),$_smarty_tpl);?>

								</strong>
							</font>
						</td>
						<td width="10">&nbsp;</td>
					</tr>
				</table>
			</td>
			<td style="border:1px solid #D6D4D4;">
				<table class="table">
					<tr>
						<td width="10">&nbsp;</td>
						<td align="right"  class="text-center">
							<font size="2" face="Open-sans, sans-serif" color="#555454">
								<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['convertPrice'][0][0]->convertPrice(array('price'=>$_smarty_tpl->tpl_vars['data_v']->value['unit_price']),$_smarty_tpl);?>

							</font>
						</td>
						<td width="10">&nbsp;</td>
					</tr>
				</table>
			</td>
			<td style="border:1px solid #D6D4D4;">
				<table class="table">
					<tr>
						<td width="10">&nbsp;</td>
						<td align="right"  class="text-center">
							<font size="2" face="Open-sans, sans-serif" color="#555454">
								<?php echo $_smarty_tpl->tpl_vars['rm_v']->value['num_rm'];?>

							</font>
						</td>
						<td width="10">&nbsp;</td>
					</tr>
				</table>
			</td>
			<td style="border:1px solid #D6D4D4;">
				<table class="table">
					<tr>
						<td width="10">&nbsp;</td>
						<td align="right"  class="text-center">
							<font size="2" face="Open-sans, sans-serif" color="#555454">
								<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['rm_v']->value['data_form'],"%d-%b-%G");?>

							</font>
						</td>
						<td width="10">&nbsp;</td>
					</tr>
				</table>
			</td>
			<td style="border:1px solid #D6D4D4;">
				<table class="table">
					<tr>
						<td width="10">&nbsp;</td>
						<td align="right"  class="text-center">
							<font size="2" face="Open-sans, sans-serif" color="#555454">
								<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['rm_v']->value['data_to'],"%d-%b-%G");?>

							</font>
						</td>
						<td width="10">&nbsp;</td>
					</tr>
				</table>
			</td>
			<td style="border:1px solid #D6D4D4;">
				<table class="table">
					<tr>
						<td width="10">&nbsp;</td>
						<td align="right"  class="text-center">
							<font size="2" face="Open-sans, sans-serif" color="#555454">
								<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['convertPrice'][0][0]->convertPrice(array('price'=>$_smarty_tpl->tpl_vars['rm_v']->value['amount']),$_smarty_tpl);?>

							</font>
						</td>
						<td width="10">&nbsp;</td>
					</tr>
				</table>
			</td>
		</tr>
		<?php } ?>	
	<?php } ?>
<?php }?>
<?php }} ?>
