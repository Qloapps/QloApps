<?php /* Smarty version Smarty-3.1.19, created on 2016-01-07 12:09:03
         compiled from "/home/sumit/public_html/html/hotel-reservation-system/themes/hotel-reservation-theme/order-confirmation.tpl" */ ?>
<?php /*%%SmartyHeaderCode:931476648568d353cdfc371-96750337%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd991adcb10c9c617047e4deb6a07d3d2a2218795' => 
    array (
      0 => '/home/sumit/public_html/html/hotel-reservation-system/themes/hotel-reservation-theme/order-confirmation.tpl',
      1 => 1452148737,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '931476648568d353cdfc371-96750337',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_568d353cf28770_28009613',
  'variables' => 
  array (
    'HOOK_ORDER_CONFIRMATION' => 0,
    'HOOK_PAYMENT_RETURN' => 0,
    'order' => 0,
    'is_guest' => 0,
    'id_order_formatted' => 0,
    'reference_order' => 0,
    'email' => 0,
    'link' => 0,
    'any_back_order' => 0,
    'shw_bo_msg' => 0,
    'back_ord_msg' => 0,
    'non_requested_rooms' => 0,
    'redirect_link_terms' => 0,
    'cart_htl_data' => 0,
    'priceDisplay' => 0,
    'use_tax' => 0,
    'return_allowed' => 0,
    'currency' => 0,
    'order_adv_dtl' => 0,
    'data_v' => 0,
    'group_use_tax' => 0,
    'rm_v' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_568d353cf28770_28009613')) {function content_568d353cf28770_28009613($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include '/home/sumit/public_html/html/hotel-reservation-system/tools/smarty/plugins/modifier.date_format.php';
?>

<?php $_smarty_tpl->_capture_stack[0][] = array('path', null, null); ob_start(); ?><?php echo smartyTranslate(array('s'=>'Order confirmation'),$_smarty_tpl);?>
<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>

<h1 class="page-heading"><?php echo smartyTranslate(array('s'=>'Order confirmation'),$_smarty_tpl);?>
</h1>

<?php $_smarty_tpl->tpl_vars['current_step'] = new Smarty_variable('payment', null, 0);?>
<?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['tpl_dir']->value)."./order-steps.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>


<?php echo $_smarty_tpl->getSubTemplate (((string)$_smarty_tpl->tpl_vars['tpl_dir']->value)."./errors.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0);?>



<?php echo $_smarty_tpl->tpl_vars['HOOK_ORDER_CONFIRMATION']->value;?>

<div class="box">
	<?php echo $_smarty_tpl->tpl_vars['HOOK_PAYMENT_RETURN']->value;?>

	<?php if (isset($_smarty_tpl->tpl_vars['order']->value->id)&&$_smarty_tpl->tpl_vars['order']->value->id) {?>
		<?php if ($_smarty_tpl->tpl_vars['is_guest']->value) {?>
			<p><?php echo smartyTranslate(array('s'=>'Your order ID is:'),$_smarty_tpl);?>
 <span class="bold"><?php echo $_smarty_tpl->tpl_vars['id_order_formatted']->value;?>
</span> . <?php echo smartyTranslate(array('s'=>'Your order ID has been sent via email.'),$_smarty_tpl);?>
</p>
		    <p class="cart_navigation exclusive">
			<a class="button-exclusive btn btn-default" href="<?php ob_start();?><?php echo urlencode($_smarty_tpl->tpl_vars['reference_order']->value);?>
<?php $_tmp1=ob_get_clean();?><?php ob_start();?><?php echo urlencode($_smarty_tpl->tpl_vars['email']->value);?>
<?php $_tmp2=ob_get_clean();?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getPageLink('guest-tracking',true,null,"id_order=".$_tmp1."&email=".$_tmp2), ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo smartyTranslate(array('s'=>'Follow my order'),$_smarty_tpl);?>
"><i class="icon-chevron-left"></i><?php echo smartyTranslate(array('s'=>'Follow my order'),$_smarty_tpl);?>
</a>
		    </p>
		<?php } else { ?>
			<p><strong><?php echo smartyTranslate(array('s'=>'Order Status :'),$_smarty_tpl);?>
</strong> <span><?php echo smartyTranslate(array('s'=>'Confirmed'),$_smarty_tpl);?>
</span></p>
			<p><strong><?php echo smartyTranslate(array('s'=>'Order Reference :'),$_smarty_tpl);?>
</strong> <span class="bold"><?php echo $_smarty_tpl->tpl_vars['order']->value->reference;?>
</span></p>
			<?php if ($_smarty_tpl->tpl_vars['any_back_order']->value) {?>
				<?php if ($_smarty_tpl->tpl_vars['shw_bo_msg']->value) {?>
					<br>
					<p class="back_o_msg"><strong><sup>*</sup><?php echo smartyTranslate(array('s'=>'Some of your rooms are on back order. Please read the following message for rooms with status on backorder'),$_smarty_tpl);?>
</strong></p>
					<p>
						-&nbsp;&nbsp;<?php echo $_smarty_tpl->tpl_vars['back_ord_msg']->value;?>

					</p>
				<?php }?>
			<?php }?>
			<hr>
			<p><strong><?php echo smartyTranslate(array('s'=>'Order Details -'),$_smarty_tpl);?>
</strong></p>
			<div class="row totalOrdercancellation_div" <?php if (!$_smarty_tpl->tpl_vars['non_requested_rooms']->value) {?>style="display:none;"<?php }?>>
				<div class="col-xs-12 col-sm-12">
					<p style="text-align:center;"><a class="terms_btn btn btn-default pull-right" href="<?php echo $_smarty_tpl->tpl_vars['redirect_link_terms']->value;?>
" target="_blank"><i class="icon-file-text large"></i>&nbsp;&nbsp;<?php echo smartyTranslate(array('s'=>'Terms & Conditions'),$_smarty_tpl);?>
</a></p>
					<button type="button" data-id_order="<?php echo $_smarty_tpl->tpl_vars['order']->value->id;?>
" data-id_currency="<?php echo $_smarty_tpl->tpl_vars['order']->value->id_currency;?>
" data-id_customer="<?php echo $_smarty_tpl->tpl_vars['order']->value->id_customer;?>
" data-order_data='<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['json_encode'][0][0]->jsonEncode($_smarty_tpl->tpl_vars['cart_htl_data']->value);?>
' name="totalOrdercancellation_btn" class="totalOrdercancellation_btn btn btn-default pull-right" href="#htlRefundReasonForm"><span><?php echo smartyTranslate(array('s'=>'Request Total Order Cancellation'),$_smarty_tpl);?>
</span></button>
				</div>
			</div>
			<div id="order-detail-content" class="">
				<table class="table table-bordered">
					<thead>
						<tr>
							<th class="cart_product"><?php echo smartyTranslate(array('s'=>'Room Image'),$_smarty_tpl);?>
</th>
							<th class="cart_description"><?php echo smartyTranslate(array('s'=>'Room Description'),$_smarty_tpl);?>
</th>
							<!-- <th><?php echo smartyTranslate(array('s'=>'Room Capcity'),$_smarty_tpl);?>
</th> -->
							<th class="cart_unit"><?php echo smartyTranslate(array('s'=>'Unit Price'),$_smarty_tpl);?>
</th>
							<th><?php echo smartyTranslate(array('s'=>'Rooms'),$_smarty_tpl);?>
</th>
							<th><?php echo smartyTranslate(array('s'=>'Check-in Date'),$_smarty_tpl);?>
</th>
							<th><?php echo smartyTranslate(array('s'=>'Check-out Date'),$_smarty_tpl);?>
</th>
							<th class="cart_total"><?php echo smartyTranslate(array('s'=>'Total'),$_smarty_tpl);?>
</th>
							<th><?php echo smartyTranslate(array('s'=>'Request Refund'),$_smarty_tpl);?>
</th>
							<th><?php echo smartyTranslate(array('s'=>'BackOrder Status'),$_smarty_tpl);?>
</th>
						</tr>
					</thead>
					<tfoot>
						<?php if ($_smarty_tpl->tpl_vars['priceDisplay']->value&&$_smarty_tpl->tpl_vars['use_tax']->value) {?>
							<tr class="item">
								<td colspan="7"></td>
								<td colspan="<?php if ($_smarty_tpl->tpl_vars['return_allowed']->value) {?>2<?php } else { ?>1<?php }?>">
									<strong><?php echo smartyTranslate(array('s'=>'Items (tax excl.)'),$_smarty_tpl);?>
</strong>
								</td>
								<td colspan="<?php if ($_smarty_tpl->tpl_vars['order']->value->hasProductReturned()) {?>3<?php } else { ?>2<?php }?>">
									<span class="price"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayWtPriceWithCurrency'][0][0]->displayWtPriceWithCurrency(array('price'=>$_smarty_tpl->tpl_vars['order']->value->getTotalProductsWithoutTaxes(),'currency'=>$_smarty_tpl->tpl_vars['currency']->value),$_smarty_tpl);?>
</span>
								</td>
							</tr>
						<?php }?>
						<tr class="item">
							<td colspan="7"></td>
							<td colspan="<?php if ($_smarty_tpl->tpl_vars['return_allowed']->value) {?>2<?php } else { ?>1<?php }?>">
								<strong><?php echo smartyTranslate(array('s'=>'Items'),$_smarty_tpl);?>
 <?php if ($_smarty_tpl->tpl_vars['use_tax']->value) {?><?php echo smartyTranslate(array('s'=>'(tax incl.)'),$_smarty_tpl);?>
<?php }?> </strong>
							</td>
							<td colspan="<?php if ($_smarty_tpl->tpl_vars['order']->value->hasProductReturned()) {?>3<?php } else { ?>2<?php }?>">
								<span class="price"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayWtPriceWithCurrency'][0][0]->displayWtPriceWithCurrency(array('price'=>$_smarty_tpl->tpl_vars['order']->value->getTotalProductsWithTaxes(),'currency'=>$_smarty_tpl->tpl_vars['currency']->value),$_smarty_tpl);?>
</span>
							</td>
						</tr>
						<?php if ($_smarty_tpl->tpl_vars['order']->value->total_discounts>0) {?>
						<tr class="item">
							<td colspan="7"></td>
							<td colspan="<?php if ($_smarty_tpl->tpl_vars['return_allowed']->value) {?>2<?php } else { ?>1<?php }?>">
								<strong><?php echo smartyTranslate(array('s'=>'Total vouchers'),$_smarty_tpl);?>
</strong>
							</td>
							<td colspan="<?php if ($_smarty_tpl->tpl_vars['order']->value->hasProductReturned()) {?>3<?php } else { ?>2<?php }?>">
								<span class="price-discount"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayWtPriceWithCurrency'][0][0]->displayWtPriceWithCurrency(array('price'=>$_smarty_tpl->tpl_vars['order']->value->total_discounts,'currency'=>$_smarty_tpl->tpl_vars['currency']->value,'convert'=>1),$_smarty_tpl);?>
</span>
							</td>
						</tr>
						<?php }?>
						<?php if ($_smarty_tpl->tpl_vars['order']->value->total_wrapping>0) {?>
						<tr class="item">
							<td colspan="7"></td>
							<td colspan="<?php if ($_smarty_tpl->tpl_vars['return_allowed']->value) {?>2<?php } else { ?>1<?php }?>">
								<strong><?php echo smartyTranslate(array('s'=>'Total gift wrapping cost'),$_smarty_tpl);?>
</strong>
							</td>
							<td colspan="<?php if ($_smarty_tpl->tpl_vars['order']->value->hasProductReturned()) {?>3<?php } else { ?>2<?php }?>">
								<span class="price-wrapping"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayWtPriceWithCurrency'][0][0]->displayWtPriceWithCurrency(array('price'=>$_smarty_tpl->tpl_vars['order']->value->total_wrapping,'currency'=>$_smarty_tpl->tpl_vars['currency']->value),$_smarty_tpl);?>
</span>
							</td>
						</tr>
						<?php }?>
						
						<?php if (isset($_smarty_tpl->tpl_vars['order_adv_dtl']->value)) {?>
							<tr class="item">
								<td colspan="7"></td>
								<td colspan="<?php if ($_smarty_tpl->tpl_vars['return_allowed']->value) {?>2<?php } else { ?>1<?php }?>">
									<strong><?php echo smartyTranslate(array('s'=>'Total Paid'),$_smarty_tpl);?>
</strong>
								</td>
								<td colspan="<?php if ($_smarty_tpl->tpl_vars['order']->value->hasProductReturned()) {?>3<?php } else { ?>2<?php }?>">
									<span class="price"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayWtPriceWithCurrency'][0][0]->displayWtPriceWithCurrency(array('price'=>$_smarty_tpl->tpl_vars['order_adv_dtl']->value['total_paid_amount'],'currency'=>$_smarty_tpl->tpl_vars['currency']->value),$_smarty_tpl);?>
</span>
								</td>
							</tr>
							<tr class="item">
								<td colspan="7"></td>
								<td colspan="<?php if ($_smarty_tpl->tpl_vars['return_allowed']->value) {?>2<?php } else { ?>1<?php }?>">
									<strong><?php echo smartyTranslate(array('s'=>'Total Due'),$_smarty_tpl);?>
</strong>
								</td>
								<td colspan="<?php if ($_smarty_tpl->tpl_vars['order']->value->hasProductReturned()) {?>3<?php } else { ?>2<?php }?>">
									<span class="price"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayWtPriceWithCurrency'][0][0]->displayWtPriceWithCurrency(array('price'=>($_smarty_tpl->tpl_vars['order_adv_dtl']->value['total_order_amount']-$_smarty_tpl->tpl_vars['order_adv_dtl']->value['total_paid_amount']),'currency'=>$_smarty_tpl->tpl_vars['currency']->value),$_smarty_tpl);?>
</span>
								</td>
							</tr>
						<?php }?>
						<tr class="totalprice item">
							<td colspan="7"></td>
							<td colspan="<?php if ($_smarty_tpl->tpl_vars['return_allowed']->value) {?>2<?php } else { ?>1<?php }?>">
								<strong><?php echo smartyTranslate(array('s'=>'Total'),$_smarty_tpl);?>
</strong>
							</td>
							<td colspan="<?php if ($_smarty_tpl->tpl_vars['order']->value->hasProductReturned()) {?>3<?php } else { ?>2<?php }?>">
								<span class="price"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayWtPriceWithCurrency'][0][0]->displayWtPriceWithCurrency(array('price'=>$_smarty_tpl->tpl_vars['order']->value->total_paid,'currency'=>$_smarty_tpl->tpl_vars['currency']->value),$_smarty_tpl);?>
</span>
							</td>
						</tr>
					</tfoot>
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
									<tr class="table_body">
										<td class="cart_product">
											<a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getProductLink($_smarty_tpl->tpl_vars['data_v']->value['id_product']);?>
">
												<img src="<?php echo $_smarty_tpl->tpl_vars['data_v']->value['cover_img'];?>
" class="img-responsive"/>
											</a>
										</td>
										<td class="cart_description">
											<p class="product-name">
												<a href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getProductLink($_smarty_tpl->tpl_vars['data_v']->value['id_product']);?>
">
													<?php echo $_smarty_tpl->tpl_vars['data_v']->value['name'];?>

												</a>
											</p>
										</td>
										<td class="cart_unit">
											<p class="text-center">
												<?php if ($_smarty_tpl->tpl_vars['group_use_tax']->value) {?>
													<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayWtPriceWithCurrency'][0][0]->displayWtPriceWithCurrency(array('price'=>$_smarty_tpl->tpl_vars['data_v']->value['unit_price_tax_incl'],'currency'=>$_smarty_tpl->tpl_vars['currency']->value),$_smarty_tpl);?>

												<?php } else { ?>
													<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayWtPriceWithCurrency'][0][0]->displayWtPriceWithCurrency(array('price'=>$_smarty_tpl->tpl_vars['data_v']->value['unit_price_tax_excl'],'currency'=>$_smarty_tpl->tpl_vars['currency']->value),$_smarty_tpl);?>

												<?php }?>
											</p>
										</td>
										<td class="text-center">
											<p>
												<?php echo $_smarty_tpl->tpl_vars['rm_v']->value['num_rm'];?>

											</p>
										</td>
										<td class="text-center">
											<p>
												<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['rm_v']->value['data_form'],"%d-%b-%G");?>

											</p>
										</td>
										<td class="text-center">
											<p>
												<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['rm_v']->value['data_to'],"%d-%b-%G");?>

											</p>
										</td>
										<td class="cart_total text-left">
											<p class="text-left">
												<?php if ($_smarty_tpl->tpl_vars['group_use_tax']->value) {?>
													<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayWtPriceWithCurrency'][0][0]->displayWtPriceWithCurrency(array('price'=>$_smarty_tpl->tpl_vars['rm_v']->value['amount_tax_incl'],'currency'=>$_smarty_tpl->tpl_vars['currency']->value),$_smarty_tpl);?>

												<?php } else { ?>
													<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayWtPriceWithCurrency'][0][0]->displayWtPriceWithCurrency(array('price'=>$_smarty_tpl->tpl_vars['rm_v']->value['amount_tax_excl'],'currency'=>$_smarty_tpl->tpl_vars['currency']->value),$_smarty_tpl);?>

												<?php }?>
											</p>
										</td>
										<td class="cart_total text-left">
											<?php if (isset($_smarty_tpl->tpl_vars['rm_v']->value['stage_name'])&&$_smarty_tpl->tpl_vars['rm_v']->value['stage_name']) {?>
												<p><?php echo smartyTranslate(array('s'=>"Request Sent.."),$_smarty_tpl);?>
</p>
											<?php } else { ?>
												<button data-amount="<?php echo $_smarty_tpl->tpl_vars['rm_v']->value['amount_tax_incl'];?>
" data-id_order="<?php echo $_smarty_tpl->tpl_vars['order']->value->id;?>
" data-id_currency="<?php echo $_smarty_tpl->tpl_vars['order']->value->id_currency;?>
" data-id_customer="<?php echo $_smarty_tpl->tpl_vars['order']->value->id_customer;?>
" data-id_product="<?php echo $_smarty_tpl->tpl_vars['data_v']->value['id_product'];?>
" data-num_rooms="<?php echo $_smarty_tpl->tpl_vars['rm_v']->value['num_rm'];?>
" data-date_from="<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['rm_v']->value['data_form'],"%G-%m-%d");?>
" type="button" data-date_to="<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['rm_v']->value['data_to'],"%G-%m-%d");?>
"  name="roomRequestForRefund" class="order_cancel_request_button_<?php echo $_smarty_tpl->tpl_vars['data_v']->value['id_product'];?>
_<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['rm_v']->value['data_form'],"%G-%m-%d");?>
_<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['rm_v']->value['data_to'],"%G-%m-%d");?>
 btn btn-default button button-small roomRequestForRefund" href="#htlRefundReasonForm"><span><?php echo smartyTranslate(array('s'=>'Request Refund'),$_smarty_tpl);?>
</span></button>
											<?php }?>
										</td>
										<td class="text-center">
											<?php if (isset($_smarty_tpl->tpl_vars['rm_v']->value['is_backorder'])&&$_smarty_tpl->tpl_vars['rm_v']->value['is_backorder']) {?>
												<?php echo smartyTranslate(array('s'=>'On Backorder'),$_smarty_tpl);?>

											<?php } else { ?>
												--
											<?php }?>
										</td>
									</tr>
								<?php } ?>
							<?php } ?>
						<?php }?>
					</tbody>
				</table>
			</div>
			<p><?php echo smartyTranslate(array('s'=>'An email has been sent with this information.','mod'=>'bankwire'),$_smarty_tpl);?>

				<br /><strong><?php echo smartyTranslate(array('s'=>'Your order will be sent as soon as we receive payment.','mod'=>'bankwire'),$_smarty_tpl);?>
</strong>
				<br /><?php echo smartyTranslate(array('s'=>'If you have questions, comments or concerns, please contact our','mod'=>'bankwire'),$_smarty_tpl);?>
 <a class="cust_serv_lnk" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getPageLink('contact',true), ENT_QUOTES, 'UTF-8', true);?>
"><?php echo smartyTranslate(array('s'=>'expert customer support team','mod'=>'bankwire'),$_smarty_tpl);?>
</a>
			</p>
		</div>
	<?php }?>
	<p class="cart_navigation exclusive">
		<a class="btn htl-reservation-form-btn-small" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getPageLink('history',true), ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo smartyTranslate(array('s'=>'Go to your order history page'),$_smarty_tpl);?>
"><i class="icon-chevron-left"></i><?php echo smartyTranslate(array('s'=>'View your order history'),$_smarty_tpl);?>
</a>
	</p>
<?php }?>


<!-- Fancybox -->
<div style="display: none;" id="reason_fancybox_content">
	<div id="htlRefundReasonForm">
		<h2 class="refund_reason_head">
			<?php echo smartyTranslate(array('s'=>'Write a reason For order cancellation'),$_smarty_tpl);?>

		</h2>
		<div>
			<div class="refundReasonFormContent col-sm-12 col-xs-12">
				<input type="hidden" value="" id="cancel_req_total_order_data">
				<input type="hidden" value="" id="cancel_req_id_room">
				<input type="hidden" value="" id="cancel_req_amount">
				<input type="hidden" value="" id="cancel_req_id_order">
				<input type="hidden" value="" id="cancel_req_id_currency">
				<input type="hidden" value="" id="cancel_req_id_customer">
				<input type="hidden" value="" id="cancel_req_id_product">
				<input type="hidden" value="" id="cancel_req_num_rooms">
				<input type="hidden" value="" id="cancel_req_date_from">
				<input type="hidden" value="" id="cancel_req_date_to">
				<textarea class="form-control reasonForRefund" rows="1" name="reasonForRefund" placeholder="Write a reason for cancellation of this booking"></textarea>
				<div>
					<p class="fl required required_err" style="color:#AA1F00; display:none"><sup>*</sup> <?php echo smartyTranslate(array('s'=>'Required field'),$_smarty_tpl);?>
</p><br>
					<p class="fr">
						<button id="submit_refund_reason" name="submit_refund_reason" type="submit" class="btn button button-medium">
							<span><?php echo smartyTranslate(array('s'=>'Submit','mod'=>'marketplace'),$_smarty_tpl);?>
</span>
						</button>&nbsp;
					</p>
				</div>
			</div>
		</div>
	</div>
</div>
<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['addJsDef'][0][0]->addJsDef(array('historyUrl'=>preg_replace("%(?<!\\\\)'%", "\'",$_smarty_tpl->tpl_vars['link']->value->getPageLink("orderdetail",true))),$_smarty_tpl);?>
<?php $_smarty_tpl->smarty->_tag_stack[] = array('addJsDefL', array('name'=>'req_sent_msg')); $_block_repeat=true; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'req_sent_msg'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smartyTranslate(array('s'=>'Request Sent..','js'=>1),$_smarty_tpl);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'req_sent_msg'), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php $_smarty_tpl->smarty->_tag_stack[] = array('addJsDefL', array('name'=>'wait_stage_msg')); $_block_repeat=true; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'wait_stage_msg'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smartyTranslate(array('s'=>'Waitting','js'=>1),$_smarty_tpl);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'wait_stage_msg'), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php $_smarty_tpl->smarty->_tag_stack[] = array('addJsDefL', array('name'=>'pending_state_msg')); $_block_repeat=true; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'pending_state_msg'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smartyTranslate(array('s'=>'Pending...','js'=>1),$_smarty_tpl);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'pending_state_msg'), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php $_smarty_tpl->smarty->_tag_stack[] = array('addJsDefL', array('name'=>'mail_sending_err')); $_block_repeat=true; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'mail_sending_err'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smartyTranslate(array('s'=>'Some error occurred while sending mail to the customer','js'=>1),$_smarty_tpl);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'mail_sending_err'), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php $_smarty_tpl->smarty->_tag_stack[] = array('addJsDefL', array('name'=>'refund_request_sending_error')); $_block_repeat=true; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'refund_request_sending_error'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smartyTranslate(array('s'=>'Some error occurred while processing request for order cancellation.','js'=>1),$_smarty_tpl);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'refund_request_sending_error'), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php }} ?>
