<?php /* Smarty version Smarty-3.1.19, created on 2016-01-06 20:19:29
         compiled from "/home/sumit/public_html/html/hotel-reservation-system/pdf/invoice.product-tab.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1967289380568d2979e5bad6-05599274%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '897b1afe2be3671035117682bd3b67448a2891f5' => 
    array (
      0 => '/home/sumit/public_html/html/hotel-reservation-system/pdf/invoice.product-tab.tpl',
      1 => 1451999489,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1967289380568d2979e5bad6-05599274',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'layout' => 0,
    'refunded_rooms' => 0,
    'cart_htl_data' => 0,
    'data_v' => 0,
    'bgcolor_class' => 0,
    'order' => 0,
    'rm_v' => 0,
    'cart_rules' => 0,
    'cart_rule' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_568d2979ec9d06_15413443',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_568d2979ec9d06_15413443')) {function content_568d2979ec9d06_15413443($_smarty_tpl) {?><?php if (!is_callable('smarty_function_cycle')) include '/home/sumit/public_html/html/hotel-reservation-system/tools/smarty/plugins/function.cycle.php';
if (!is_callable('smarty_modifier_date_format')) include '/home/sumit/public_html/html/hotel-reservation-system/tools/smarty/plugins/modifier.date_format.php';
?>
<table class="product" width="100%" cellpadding="4" cellspacing="0">
	<thead>
		<tr>
			<th class="product header small"><?php echo smartyTranslate(array('s'=>'Room Image','pdf'=>'true'),$_smarty_tpl);?>
</th>
			<th class="product header small"><?php echo smartyTranslate(array('s'=>'Room Description','pdf'=>'true'),$_smarty_tpl);?>
</th>
			<th class="product header small"><?php echo smartyTranslate(array('s'=>'Room Capcity','pdf'=>'true'),$_smarty_tpl);?>
</th>

			<?php if (isset($_smarty_tpl->tpl_vars['layout']->value['before_discount'])) {?>
				<th class="product header small"><?php echo smartyTranslate(array('s'=>'Base price','pdf'=>'true'),$_smarty_tpl);?>
 <br /> <?php echo smartyTranslate(array('s'=>'(Tax excl.)','pdf'=>'true'),$_smarty_tpl);?>
</th>
			<?php }?>

			<th class="product header-right small" width="<?php echo $_smarty_tpl->tpl_vars['layout']->value['unit_price_tax_excl']['width'];?>
%"><?php echo smartyTranslate(array('s'=>'Unit Price','pdf'=>'true'),$_smarty_tpl);?>
 <br /> <?php echo smartyTranslate(array('s'=>'(Tax excl.)','pdf'=>'true'),$_smarty_tpl);?>
</th>
			<th class="product header small"><?php echo smartyTranslate(array('s'=>'Rooms Qty','pdf'=>'true'),$_smarty_tpl);?>
</th>
			<th class="product header small"><?php echo smartyTranslate(array('s'=>'Check-in Date','pdf'=>'true'),$_smarty_tpl);?>
</th>
			<th class="product header small"><?php echo smartyTranslate(array('s'=>'Check-out Date','pdf'=>'true'),$_smarty_tpl);?>
</th>
			<th class="product header-right small"><?php echo smartyTranslate(array('s'=>'Total','pdf'=>'true'),$_smarty_tpl);?>
 <br /> <?php echo smartyTranslate(array('s'=>'(Tax excl.)','pdf'=>'true'),$_smarty_tpl);?>
</th>
			<?php if (isset($_smarty_tpl->tpl_vars['refunded_rooms']->value)&&$_smarty_tpl->tpl_vars['refunded_rooms']->value) {?>
				<th class="product header-right small"><?php echo smartyTranslate(array('s'=>'Refund Status','pdf'=>'true'),$_smarty_tpl);?>
</th>
			<?php }?>
		</tr>
	</thead>
	<tbody>
	<?php if (isset($_smarty_tpl->tpl_vars['cart_htl_data']->value)) {?>
		<?php  $_smarty_tpl->tpl_vars['data_v'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['data_v']->_loop = false;
 $_smarty_tpl->tpl_vars['data_k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['cart_htl_data']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
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
				<?php echo smarty_function_cycle(array('values'=>array("color_line_even","color_line_odd"),'assign'=>'bgcolor_class'),$_smarty_tpl);?>

				<tr class="product <?php echo $_smarty_tpl->tpl_vars['bgcolor_class']->value;?>
">
					<td class="cart_product">
						<img src="<?php echo $_smarty_tpl->tpl_vars['data_v']->value['cover_img'];?>
" class="img-responsive" />
					</td>
					<td class="product center">
						<p class="product-name">
							<?php echo $_smarty_tpl->tpl_vars['data_v']->value['name'];?>

						</p>
					</td>
					<td>
						<p class="text-left">
							<?php echo $_smarty_tpl->tpl_vars['data_v']->value['adult'];?>
 <?php echo smartyTranslate(array('s'=>'Adults'),$_smarty_tpl);?>
, <?php echo $_smarty_tpl->tpl_vars['data_v']->value['children'];?>
 <?php echo smartyTranslate(array('s'=>'Children'),$_smarty_tpl);?>

						</p>
					</td>
					<td class="product center">
						<p class="text-center">
							<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('currency'=>$_smarty_tpl->tpl_vars['order']->value->id_currency,'price'=>$_smarty_tpl->tpl_vars['data_v']->value['unit_price']),$_smarty_tpl);?>

						</p>
					</td>
					<td class="product center">
						<p class="text-left">
							<?php echo $_smarty_tpl->tpl_vars['rm_v']->value['num_rm'];?>

						</p>
					</td>
					<td class="product center">
						<p>
							<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['rm_v']->value['data_form'],"%d-%b-%G");?>

						</p>
					</td>
					<td class="product center">
						<p>
							<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['rm_v']->value['data_to'],"%d-%b-%G");?>

						</p>
					</td>
					<td class="product center">
						<p>
							<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('currency'=>$_smarty_tpl->tpl_vars['order']->value->id_currency,'price'=>$_smarty_tpl->tpl_vars['rm_v']->value['amount']),$_smarty_tpl);?>

						</p>
					</td>
					<?php if (isset($_smarty_tpl->tpl_vars['refunded_rooms']->value)&&$_smarty_tpl->tpl_vars['refunded_rooms']->value) {?>
						<?php if (isset($_smarty_tpl->tpl_vars['rm_v']->value['stage_name'])&&$_smarty_tpl->tpl_vars['rm_v']->value['stage_name']=='Refunded') {?>
							<td class="product center">
								<p style="background-color:green; padding-left:5px;">
									<?php echo smartyTranslate(array('s'=>'Refunded','pdf'=>'true'),$_smarty_tpl);?>

								</p>
							</td>
						<?php } else { ?>
							--
						<?php }?>
					<?php }?>
				</tr>
			<?php } ?>
		<?php } ?>
	<?php }?>

	<!-- END PRODUCTS -->

	<!-- CART RULES -->

	<!-- <?php $_smarty_tpl->tpl_vars["shipping_discount_tax_incl"] = new Smarty_variable("0", null, 0);?>
	<?php  $_smarty_tpl->tpl_vars['cart_rule'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['cart_rule']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['cart_rules']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['cart_rule']->index=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['cart_rule']->key => $_smarty_tpl->tpl_vars['cart_rule']->value) {
$_smarty_tpl->tpl_vars['cart_rule']->_loop = true;
 $_smarty_tpl->tpl_vars['cart_rule']->index++;
 $_smarty_tpl->tpl_vars['cart_rule']->first = $_smarty_tpl->tpl_vars['cart_rule']->index === 0;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']["cart_rules_loop"]['first'] = $_smarty_tpl->tpl_vars['cart_rule']->first;
?>
		<?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['cart_rules_loop']['first']) {?>
		<tr class="discount">
			<th class="header" colspan="<?php echo $_smarty_tpl->tpl_vars['layout']->value['_colCount'];?>
">
				<?php echo smartyTranslate(array('s'=>'Discounts','pdf'=>'true'),$_smarty_tpl);?>

			</th>
		</tr>
		<?php }?>
		<tr class="discount">
			<td class="white right" colspan="<?php echo $_smarty_tpl->tpl_vars['layout']->value['_colCount']-1;?>
">
				<?php echo $_smarty_tpl->tpl_vars['cart_rule']->value['name'];?>

			</td>
			<td class="right white">
				- <?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('currency'=>$_smarty_tpl->tpl_vars['order']->value->id_currency,'price'=>$_smarty_tpl->tpl_vars['cart_rule']->value['value_tax_excl']),$_smarty_tpl);?>

			</td>
		</tr>
	<?php } ?> -->

	</tbody>
</table><?php }} ?>
