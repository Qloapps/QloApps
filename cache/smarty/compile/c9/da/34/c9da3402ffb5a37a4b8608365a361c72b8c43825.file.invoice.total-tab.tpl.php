<?php /* Smarty version Smarty-3.1.19, created on 2016-01-06 20:19:29
         compiled from "/home/sumit/public_html/html/hotel-reservation-system/pdf/invoice.total-tab.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1208240620568d2979efd547-44248740%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c9da3402ffb5a37a4b8608365a361c72b8c43825' => 
    array (
      0 => '/home/sumit/public_html/html/hotel-reservation-system/pdf/invoice.total-tab.tpl',
      1 => 1451999489,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1208240620568d2979efd547-44248740',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'order' => 0,
    'footer' => 0,
    'order_adv_dtl' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_568d297a005c97_26411015',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_568d297a005c97_26411015')) {function content_568d297a005c97_26411015($_smarty_tpl) {?>
<table id="total-tab" width="100%">

	<tr>
		<td class="grey" width="70%">
			<?php echo smartyTranslate(array('s'=>'Total Order Cost','pdf'=>'true'),$_smarty_tpl);?>

		</td>
		<td class="white" width="30%">
			<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('currency'=>$_smarty_tpl->tpl_vars['order']->value->id_currency,'price'=>$_smarty_tpl->tpl_vars['footer']->value['products_before_discounts_tax_excl']),$_smarty_tpl);?>

		</td>
	</tr>

	<?php if ($_smarty_tpl->tpl_vars['footer']->value['product_discounts_tax_excl']>0) {?>

		<tr>
			<td class="grey" width="70%">
				<?php echo smartyTranslate(array('s'=>'Total Discounts','pdf'=>'true'),$_smarty_tpl);?>

			</td>
			<td class="white" width="30%">
				- <?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('currency'=>$_smarty_tpl->tpl_vars['order']->value->id_currency,'price'=>$_smarty_tpl->tpl_vars['footer']->value['product_discounts_tax_excl']),$_smarty_tpl);?>

			</td>
		</tr>

	<?php }?>

	<!-- <tr>
		<td class="grey" width="70%">
			<?php echo smartyTranslate(array('s'=>'Shipping Cost','pdf'=>'true'),$_smarty_tpl);?>

		</td>
		<td class="white" width="30%">
			<?php if ($_smarty_tpl->tpl_vars['footer']->value['shipping_tax_excl']>0) {?>
				<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('currency'=>$_smarty_tpl->tpl_vars['order']->value->id_currency,'price'=>$_smarty_tpl->tpl_vars['footer']->value['shipping_tax_excl']),$_smarty_tpl);?>

			<?php } else { ?>
				<?php echo smartyTranslate(array('s'=>'Free Shipping','pdf'=>true),$_smarty_tpl);?>

			<?php }?>
		</td>
	</tr> -->

	<!-- <?php if ($_smarty_tpl->tpl_vars['footer']->value['wrapping_tax_excl']>0) {?>
		<tr>
			<td class="grey">
				<?php echo smartyTranslate(array('s'=>'Wrapping Cost','pdf'=>'true'),$_smarty_tpl);?>

			</td>
			<td class="white"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('currency'=>$_smarty_tpl->tpl_vars['order']->value->id_currency,'price'=>$_smarty_tpl->tpl_vars['footer']->value['wrapping_tax_excl']),$_smarty_tpl);?>
</td>
		</tr>
	<?php }?> -->

	<tr class="bold">
		<td class="grey">
			<?php echo smartyTranslate(array('s'=>'Total (Tax excl.)','pdf'=>'true'),$_smarty_tpl);?>

		</td>
		<td class="white">
			<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('currency'=>$_smarty_tpl->tpl_vars['order']->value->id_currency,'price'=>$_smarty_tpl->tpl_vars['footer']->value['total_paid_tax_excl']),$_smarty_tpl);?>

		</td>
	</tr>
	<?php if ($_smarty_tpl->tpl_vars['footer']->value['total_taxes']>0) {?>
	<tr class="bold">
		<td class="grey">
			<?php echo smartyTranslate(array('s'=>'Total Tax','pdf'=>'true'),$_smarty_tpl);?>

		</td>
		<td class="white">
			<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('currency'=>$_smarty_tpl->tpl_vars['order']->value->id_currency,'price'=>$_smarty_tpl->tpl_vars['footer']->value['total_taxes']),$_smarty_tpl);?>

		</td>
	</tr>
	<?php }?>

	<?php if (isset($_smarty_tpl->tpl_vars['order_adv_dtl']->value)) {?>
		<tr class="bold big">
			<td class="grey">
				<?php echo smartyTranslate(array('s'=>'Total Paid','pdf'=>'true'),$_smarty_tpl);?>

			</td>
			<td class="white">
				<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('currency'=>$_smarty_tpl->tpl_vars['order_adv_dtl']->value['id_currency'],'price'=>$_smarty_tpl->tpl_vars['order_adv_dtl']->value['total_paid_amount']),$_smarty_tpl);?>

			</td>
		</tr>
		<tr class="bold big">
			<td class="grey">
				<?php echo smartyTranslate(array('s'=>'Total Due','pdf'=>'true'),$_smarty_tpl);?>

			</td>
			<td class="white">
				<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('currency'=>$_smarty_tpl->tpl_vars['order_adv_dtl']->value['id_currency'],'price'=>($_smarty_tpl->tpl_vars['order_adv_dtl']->value['total_order_amount']-$_smarty_tpl->tpl_vars['order_adv_dtl']->value['total_paid_amount'])),$_smarty_tpl);?>

			</td>
		</tr>
	<?php }?>

	<tr class="bold big">
		<td class="grey">
			<?php echo smartyTranslate(array('s'=>'Total','pdf'=>'true'),$_smarty_tpl);?>

		</td>
		<td class="white">
			<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('currency'=>$_smarty_tpl->tpl_vars['order']->value->id_currency,'price'=>$_smarty_tpl->tpl_vars['footer']->value['total_paid_tax_incl']),$_smarty_tpl);?>

		</td>
	</tr>
</table>
<?php }} ?>
