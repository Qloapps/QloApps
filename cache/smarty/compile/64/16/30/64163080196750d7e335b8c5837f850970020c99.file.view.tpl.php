<?php /* Smarty version Smarty-3.1.19, created on 2016-01-07 11:53:04
         compiled from "/home/sumit/public_html/html/hotel-reservation-system/admin_htl/themes/default/template/controllers/orders/helpers/view/view.tpl" */ ?>
<?php /*%%SmartyHeaderCode:961345758568e04489aaee5-97615435%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '64163080196750d7e335b8c5837f850970020c99' => 
    array (
      0 => '/home/sumit/public_html/html/hotel-reservation-system/admin_htl/themes/default/template/controllers/orders/helpers/view/view.tpl',
      1 => 1452142914,
      2 => 'file',
    ),
    '4d8f0e7c6c4595e71cc6430e0c940f8b59d74b1a' => 
    array (
      0 => '/home/sumit/public_html/html/hotel-reservation-system/admin_htl/themes/default/template/helpers/view/view.tpl',
      1 => 1452142887,
      2 => 'file',
    ),
    'b5c566fe6a5bb1d3946dc9087e8e4b9458f13301' => 
    array (
      0 => '/home/sumit/public_html/html/hotel-reservation-system/admin_htl/themes/default/template/controllers/orders/_documents.tpl',
      1 => 1452142889,
      2 => 'file',
    ),
    'd278d57cfabaa499a1ba0d73612c1ce5c95254c1' => 
    array (
      0 => '/home/sumit/public_html/html/hotel-reservation-system/admin_htl/themes/default/template/controllers/orders/_shipping.tpl',
      1 => 1452142889,
      2 => 'file',
    ),
    'b79d116d8d89c112a9c2203152498ffe6893032e' => 
    array (
      0 => '/home/sumit/public_html/html/hotel-reservation-system/admin_htl/themes/default/template/controllers/orders/_customized_data.tpl',
      1 => 1452142889,
      2 => 'file',
    ),
    'd2ba443b1039e5e3b6c56538a0c7004c82d0e217' => 
    array (
      0 => '/home/sumit/public_html/html/hotel-reservation-system/admin_htl/themes/default/template/controllers/orders/_product_line.tpl',
      1 => 1452142889,
      2 => 'file',
    ),
    '6960e84a5753dec8a3d61b826fdc244b0edea852' => 
    array (
      0 => '/home/sumit/public_html/html/hotel-reservation-system/admin_htl/themes/default/template/controllers/orders/_new_product.tpl',
      1 => 1452142889,
      2 => 'file',
    ),
    '0ecea605ba6033a45c55128a05f6a8a8ba031150' => 
    array (
      0 => '/home/sumit/public_html/html/hotel-reservation-system/admin_htl/themes/default/template/controllers/orders/_discount_form.tpl',
      1 => 1452142889,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '961345758568e04489aaee5-97615435',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'name_controller' => 0,
    'hookName' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_568e0449818d94_65954146',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_568e0449818d94_65954146')) {function content_568e0449818d94_65954146($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include '/home/sumit/public_html/html/hotel-reservation-system/tools/smarty/plugins/modifier.date_format.php';
if (!is_callable('smarty_modifier_replace')) include '/home/sumit/public_html/html/hotel-reservation-system/tools/smarty/plugins/modifier.replace.php';
if (!is_callable('smarty_modifier_regex_replace')) include '/home/sumit/public_html/html/hotel-reservation-system/tools/smarty/plugins/modifier.regex_replace.php';
?>

<div class="leadin"></div>


	<script type="text/javascript">
	var admin_order_tab_link = "<?php echo addslashes($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminOrders'));?>
";
	var id_order = <?php echo $_smarty_tpl->tpl_vars['order']->value->id;?>
;
	var id_lang = <?php echo $_smarty_tpl->tpl_vars['current_id_lang']->value;?>
;
	var id_currency = <?php echo $_smarty_tpl->tpl_vars['order']->value->id_currency;?>
;
	var id_customer = <?php echo intval($_smarty_tpl->tpl_vars['order']->value->id_customer);?>
;
	<?php $_smarty_tpl->tpl_vars['PS_TAX_ADDRESS_TYPE'] = new Smarty_variable(Configuration::get('PS_TAX_ADDRESS_TYPE'), null, 0);?>
	var id_address = <?php echo $_smarty_tpl->tpl_vars['order']->value->{$_smarty_tpl->tpl_vars['PS_TAX_ADDRESS_TYPE']->value};?>
;
	var currency_sign = "<?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
";
	var currency_format = "<?php echo $_smarty_tpl->tpl_vars['currency']->value->format;?>
";
	var currency_blank = "<?php echo $_smarty_tpl->tpl_vars['currency']->value->blank;?>
";
	var priceDisplayPrecision = <?php echo intval(@constant('_PS_PRICE_DISPLAY_PRECISION_'));?>
;
	var use_taxes = <?php if ($_smarty_tpl->tpl_vars['order']->value->getTaxCalculationMethod()==@constant('PS_TAX_INC')) {?>true<?php } else { ?>false<?php }?>;
	var stock_management = <?php echo intval($_smarty_tpl->tpl_vars['stock_management']->value);?>
;
	var txt_add_product_stock_issue = "<?php echo smartyTranslate(array('s'=>'Are you sure you want to add this quantity?','js'=>1),$_smarty_tpl);?>
";
	var txt_add_product_new_invoice = "<?php echo smartyTranslate(array('s'=>'Are you sure you want to create a new invoice?','js'=>1),$_smarty_tpl);?>
";
	var txt_add_product_no_product = "<?php echo smartyTranslate(array('s'=>'Error: No product has been selected','js'=>1),$_smarty_tpl);?>
";
	var txt_add_product_no_product_quantity = "<?php echo smartyTranslate(array('s'=>'Error: Quantity of products must be set','js'=>1),$_smarty_tpl);?>
";
	var txt_add_product_no_product_price = "<?php echo smartyTranslate(array('s'=>'Error: Product price must be set','js'=>1),$_smarty_tpl);?>
";
	var txt_confirm = "<?php echo smartyTranslate(array('s'=>'Are you sure?','js'=>1),$_smarty_tpl);?>
";
	var statesShipped = new Array();
	var has_voucher = <?php if (count($_smarty_tpl->tpl_vars['discounts']->value)) {?>1<?php } else { ?>0<?php }?>;
	<?php  $_smarty_tpl->tpl_vars['state'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['state']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['states']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['state']->key => $_smarty_tpl->tpl_vars['state']->value) {
$_smarty_tpl->tpl_vars['state']->_loop = true;
?>
		<?php if ((isset($_smarty_tpl->tpl_vars['currentState']->value->shipped)&&!$_smarty_tpl->tpl_vars['currentState']->value->shipped&&$_smarty_tpl->tpl_vars['state']->value['shipped'])) {?>
			statesShipped.push(<?php echo $_smarty_tpl->tpl_vars['state']->value['id_order_state'];?>
);
		<?php }?>
	<?php } ?>
	var order_discount_price = <?php if (($_smarty_tpl->tpl_vars['order']->value->getTaxCalculationMethod()==@constant('PS_TAX_EXC'))) {?>
									<?php echo $_smarty_tpl->tpl_vars['order']->value->total_discounts_tax_excl;?>

								<?php } else { ?>
									<?php echo $_smarty_tpl->tpl_vars['order']->value->total_discounts_tax_incl;?>

								<?php }?>;

	var errorRefund = "<?php echo smartyTranslate(array('s'=>'Error. You cannot refund a negative amount.'),$_smarty_tpl);?>
";
	</script>

	<?php ob_start();?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>"displayInvoice",'id_order'=>$_smarty_tpl->tpl_vars['order']->value->id),$_smarty_tpl);?>
<?php $_tmp1=ob_get_clean();?><?php $_smarty_tpl->tpl_vars["hook_invoice"] = new Smarty_variable($_tmp1, null, 0);?>
	<?php if (($_smarty_tpl->tpl_vars['hook_invoice']->value)) {?>
	<div><?php echo $_smarty_tpl->tpl_vars['hook_invoice']->value;?>
</div>
	<?php }?>

	<div class="panel kpi-container">
		<div class="row">
			<div class="col-xs-6 col-sm-3 box-stats color3" >
				<div class="kpi-content">
					<i class="icon-calendar-empty"></i>
					<span class="title"><?php echo smartyTranslate(array('s'=>'Date'),$_smarty_tpl);?>
</span>
					<span class="value"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['dateFormat'][0][0]->dateFormat(array('date'=>$_smarty_tpl->tpl_vars['order']->value->date_add,'full'=>false),$_smarty_tpl);?>
</span>
				</div>
			</div>
			<div class="col-xs-6 col-sm-3 box-stats color4" >
				<div class="kpi-content">
					<i class="icon-money"></i>
					<span class="title"><?php echo smartyTranslate(array('s'=>'Total'),$_smarty_tpl);?>
</span>
					<span class="value"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['order']->value->total_paid_tax_incl,'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>
</span>
				</div>
			</div>
			<div class="col-xs-6 col-sm-3 box-stats color2" >
				<div class="kpi-content">
					<i class="icon-comments"></i>
					<span class="title"><?php echo smartyTranslate(array('s'=>'Messages'),$_smarty_tpl);?>
</span>
					<span class="value"><a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminCustomerThreads'), ENT_QUOTES, 'UTF-8', true);?>
&amp;id_order=<?php echo intval($_smarty_tpl->tpl_vars['order']->value->id);?>
"><?php echo sizeof($_smarty_tpl->tpl_vars['customer_thread_message']->value);?>
</a></span>
				</div>
			</div>
			<div class="col-xs-6 col-sm-3 box-stats color1" >
				<a href="#start_products">
					<div class="kpi-content">
						<i class="icon icon-home"></i>
						<!-- Original -->
						<!-- <span class="title"><?php echo smartyTranslate(array('s'=>'Total'),$_smarty_tpl);?>
</span>
						<span class="value"><?php echo sizeof($_smarty_tpl->tpl_vars['products']->value);?>
</span> -->
						<span class="title"><?php echo smartyTranslate(array('s'=>'Total Rooms'),$_smarty_tpl);?>
</span>
						<span class="value"><?php echo count($_smarty_tpl->tpl_vars['cart_detail_data']->value);?>
</span>
					</div>
				</a>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-7">
			<div class="panel">
				<div class="panel-heading">
					<i class="icon-credit-card"></i>
					<?php echo smartyTranslate(array('s'=>'Order'),$_smarty_tpl);?>

					<span class="badge"><?php echo $_smarty_tpl->tpl_vars['order']->value->reference;?>
</span>
					<span class="badge"><?php echo smartyTranslate(array('s'=>"#"),$_smarty_tpl);?>
<?php echo $_smarty_tpl->tpl_vars['order']->value->id;?>
</span>
					<div class="panel-heading-action">
						<div class="btn-group">
							<a class="btn btn-default<?php if (!$_smarty_tpl->tpl_vars['previousOrder']->value) {?> disabled<?php }?>" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminOrders'), ENT_QUOTES, 'UTF-8', true);?>
&amp;vieworder&amp;id_order=<?php echo intval($_smarty_tpl->tpl_vars['previousOrder']->value);?>
">
								<i class="icon-backward"></i>
							</a>
							<a class="btn btn-default<?php if (!$_smarty_tpl->tpl_vars['nextOrder']->value) {?> disabled<?php }?>" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminOrders'), ENT_QUOTES, 'UTF-8', true);?>
&amp;vieworder&amp;id_order=<?php echo intval($_smarty_tpl->tpl_vars['nextOrder']->value);?>
">
								<i class="icon-forward"></i>
							</a>
						</div>
					</div>
				</div>
				<!-- Orders Actions -->
				<div class="well hidden-print">
					<a class="btn btn-default" href="javascript:window.print()">
						<i class="icon-print"></i>
						<?php echo smartyTranslate(array('s'=>'Print order'),$_smarty_tpl);?>

					</a>
					&nbsp;
					<?php if (Configuration::get('PS_INVOICE')&&count($_smarty_tpl->tpl_vars['invoices_collection']->value)&&$_smarty_tpl->tpl_vars['order']->value->invoice_number) {?>
						<a data-selenium-id="view_invoice" class="btn btn-default _blank" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminPdf'), ENT_QUOTES, 'UTF-8', true);?>
&amp;submitAction=generateInvoicePDF&amp;id_order=<?php echo intval($_smarty_tpl->tpl_vars['order']->value->id);?>
">
							<i class="icon-file"></i>
							<?php echo smartyTranslate(array('s'=>'View invoice'),$_smarty_tpl);?>

						</a>
					<?php } else { ?>
						<span class="span label label-inactive">
							<i class="icon-remove"></i>
							<?php echo smartyTranslate(array('s'=>'No invoice'),$_smarty_tpl);?>

						</span>
					<?php }?>
					&nbsp;

					<!-- By webkul to hide unneccessary data on order detail page -->
					<!-- <?php if ($_smarty_tpl->tpl_vars['order']->value->delivery_number) {?>
						<a class="btn btn-default _blank"  href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminPdf'), ENT_QUOTES, 'UTF-8', true);?>
&amp;submitAction=generateDeliverySlipPDF&amp;id_order=<?php echo intval($_smarty_tpl->tpl_vars['order']->value->id);?>
">
							<i class="icon-truck"></i>
							<?php echo smartyTranslate(array('s'=>'View delivery slip'),$_smarty_tpl);?>

						</a>
					<?php } else { ?>
						<span class="span label label-inactive">
							<i class="icon-remove"></i>
							<?php echo smartyTranslate(array('s'=>'No delivery slip'),$_smarty_tpl);?>

						</span>
					<?php }?> -->
					<!-- End -->
					&nbsp;
					<?php if (Configuration::get('PS_ORDER_RETURN')) {?>
						<a id="desc-order-standard_refund" class="btn btn-default" href="#refundForm">
							<i class="icon-exchange"></i>
							<?php if ($_smarty_tpl->tpl_vars['order']->value->hasBeenShipped()) {?>
								<?php echo smartyTranslate(array('s'=>'Return products'),$_smarty_tpl);?>

							<?php } elseif ($_smarty_tpl->tpl_vars['order']->value->hasBeenPaid()) {?>
								<?php echo smartyTranslate(array('s'=>'Standard refund'),$_smarty_tpl);?>

							<?php } else { ?>
								<?php echo smartyTranslate(array('s'=>'Cancel products'),$_smarty_tpl);?>

							<?php }?>
						</a>
						&nbsp;
					<?php }?>
					<?php if ($_smarty_tpl->tpl_vars['order']->value->hasInvoice()) {?>
						<a id="desc-order-partial_refund" class="btn btn-default hidden" href="#refundForm">
							<i class="icon-exchange"></i>
							<?php echo smartyTranslate(array('s'=>'Partial refund'),$_smarty_tpl);?>

						</a>
					<?php }?>
				</div>
				<!-- Tab nav -->
				<ul class="nav nav-tabs" id="tabOrder">
					<?php echo $_smarty_tpl->tpl_vars['HOOK_TAB_ORDER']->value;?>

					<li class="active">
						<a href="#status">
							<i class="icon-time"></i>
							<?php echo smartyTranslate(array('s'=>'Status'),$_smarty_tpl);?>
 <span class="badge"><?php echo count($_smarty_tpl->tpl_vars['history']->value);?>
</span>
						</a>
					</li>
					<li>
						<a href="#documents">
							<i class="icon-file-text"></i>
							<?php echo smartyTranslate(array('s'=>'Documents'),$_smarty_tpl);?>
 <span class="badge"><?php echo count($_smarty_tpl->tpl_vars['order']->value->getDocuments());?>
</span>
						</a>
					</li>
				</ul>
				<!-- Tab content -->
				<div class="tab-content panel">
					<?php echo $_smarty_tpl->tpl_vars['HOOK_CONTENT_ORDER']->value;?>

					<!-- Tab status -->
					<div class="tab-pane active" id="status">
						<h4 class="visible-print"><?php echo smartyTranslate(array('s'=>'Status'),$_smarty_tpl);?>
 <span class="badge">(<?php echo count($_smarty_tpl->tpl_vars['history']->value);?>
)</span></h4>
						<!-- History of status -->
						<div class="table-responsive">
							<table class="table history-status row-margin-bottom">
								<tbody>
									<?php  $_smarty_tpl->tpl_vars['row'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['row']->_loop = false;
 $_smarty_tpl->tpl_vars['key'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['history']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['row']->key => $_smarty_tpl->tpl_vars['row']->value) {
$_smarty_tpl->tpl_vars['row']->_loop = true;
 $_smarty_tpl->tpl_vars['key']->value = $_smarty_tpl->tpl_vars['row']->key;
?>
										<?php if (($_smarty_tpl->tpl_vars['key']->value==0)) {?>
											<tr>
												<td style="background-color:<?php echo $_smarty_tpl->tpl_vars['row']->value['color'];?>
"><img src="../img/os/<?php echo intval($_smarty_tpl->tpl_vars['row']->value['id_order_state']);?>
.gif" width="16" height="16" alt="<?php echo stripslashes($_smarty_tpl->tpl_vars['row']->value['ostate_name']);?>
" /></td>
												<td style="background-color:<?php echo $_smarty_tpl->tpl_vars['row']->value['color'];?>
;color:<?php echo $_smarty_tpl->tpl_vars['row']->value['text-color'];?>
"><?php echo stripslashes($_smarty_tpl->tpl_vars['row']->value['ostate_name']);?>
</td>
												<td style="background-color:<?php echo $_smarty_tpl->tpl_vars['row']->value['color'];?>
;color:<?php echo $_smarty_tpl->tpl_vars['row']->value['text-color'];?>
"><?php if ($_smarty_tpl->tpl_vars['row']->value['employee_lastname']) {?><?php echo stripslashes($_smarty_tpl->tpl_vars['row']->value['employee_firstname']);?>
 <?php echo stripslashes($_smarty_tpl->tpl_vars['row']->value['employee_lastname']);?>
<?php }?></td>
												<td style="background-color:<?php echo $_smarty_tpl->tpl_vars['row']->value['color'];?>
;color:<?php echo $_smarty_tpl->tpl_vars['row']->value['text-color'];?>
"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['dateFormat'][0][0]->dateFormat(array('date'=>$_smarty_tpl->tpl_vars['row']->value['date_add'],'full'=>true),$_smarty_tpl);?>
</td>
												<td style="background-color:<?php echo $_smarty_tpl->tpl_vars['row']->value['color'];?>
;color:<?php echo $_smarty_tpl->tpl_vars['row']->value['text-color'];?>
" class="text-right">
													<?php if (intval($_smarty_tpl->tpl_vars['row']->value['send_email'])) {?>
														<a class="btn btn-default" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminOrders'), ENT_QUOTES, 'UTF-8', true);?>
&amp;vieworder&amp;id_order=<?php echo intval($_smarty_tpl->tpl_vars['order']->value->id);?>
&amp;sendStateEmail=<?php echo intval($_smarty_tpl->tpl_vars['row']->value['id_order_state']);?>
&amp;id_order_history=<?php echo intval($_smarty_tpl->tpl_vars['row']->value['id_order_history']);?>
" title="<?php echo smartyTranslate(array('s'=>'Resend this email to the customer'),$_smarty_tpl);?>
">
															<i class="icon-mail-reply"></i>
															<?php echo smartyTranslate(array('s'=>'Resend email'),$_smarty_tpl);?>

														</a>
													<?php }?>
												</td>
											</tr>
										<?php } else { ?>
											<tr>
												<td><img src="../img/os/<?php echo intval($_smarty_tpl->tpl_vars['row']->value['id_order_state']);?>
.gif" width="16" height="16" /></td>
												<td><?php echo stripslashes($_smarty_tpl->tpl_vars['row']->value['ostate_name']);?>
</td>
												<td><?php if ($_smarty_tpl->tpl_vars['row']->value['employee_lastname']) {?><?php echo stripslashes($_smarty_tpl->tpl_vars['row']->value['employee_firstname']);?>
 <?php echo stripslashes($_smarty_tpl->tpl_vars['row']->value['employee_lastname']);?>
<?php } else { ?>&nbsp;<?php }?></td>
												<td><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['dateFormat'][0][0]->dateFormat(array('date'=>$_smarty_tpl->tpl_vars['row']->value['date_add'],'full'=>true),$_smarty_tpl);?>
</td>
												<td class="text-right">
													<?php if (intval($_smarty_tpl->tpl_vars['row']->value['send_email'])) {?>
														<a class="btn btn-default" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminOrders'), ENT_QUOTES, 'UTF-8', true);?>
&amp;vieworder&amp;id_order=<?php echo intval($_smarty_tpl->tpl_vars['order']->value->id);?>
&amp;sendStateEmail=<?php echo intval($_smarty_tpl->tpl_vars['row']->value['id_order_state']);?>
&amp;id_order_history=<?php echo intval($_smarty_tpl->tpl_vars['row']->value['id_order_history']);?>
" title="<?php echo smartyTranslate(array('s'=>'Resend this email to the customer'),$_smarty_tpl);?>
">
															<i class="icon-mail-reply"></i>
															<?php echo smartyTranslate(array('s'=>'Resend email'),$_smarty_tpl);?>

														</a>
													<?php }?>
												</td>
											</tr>
										<?php }?>
									<?php } ?>
								</tbody>
							</table>
						</div>
						<!-- Change status form -->
						<form action="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['currentIndex']->value, ENT_QUOTES, 'UTF-8', true);?>
&amp;vieworder&amp;token=<?php echo $_GET['token'];?>
" method="post" class="form-horizontal well hidden-print">
							<div class="row">
								<div class="col-lg-9">
									<select id="id_order_state" class="chosen form-control" name="id_order_state">
									<?php  $_smarty_tpl->tpl_vars['state'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['state']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['states']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['state']->key => $_smarty_tpl->tpl_vars['state']->value) {
$_smarty_tpl->tpl_vars['state']->_loop = true;
?>
									<!-- By webkul to hide unnecessary order statues from statuses dropdown -->
										<?php if ($_smarty_tpl->tpl_vars['state']->value['id_order_state']!=4&&$_smarty_tpl->tpl_vars['state']->value['id_order_state']!=5&&$_smarty_tpl->tpl_vars['state']->value['id_order_state']!=14) {?>
											<option value="<?php echo intval($_smarty_tpl->tpl_vars['state']->value['id_order_state']);?>
"<?php if (isset($_smarty_tpl->tpl_vars['currentState']->value)&&$_smarty_tpl->tpl_vars['state']->value['id_order_state']==$_smarty_tpl->tpl_vars['currentState']->value->id) {?> selected="selected" disabled="disabled"<?php }?>><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['state']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
</option>
										<?php }?>
									<?php } ?>
									</select>
									<input type="hidden" name="id_order" value="<?php echo $_smarty_tpl->tpl_vars['order']->value->id;?>
" />
								</div>
								<div class="col-lg-3">
									<button type="submit" name="submitState" class="btn btn-primary">
										<?php echo smartyTranslate(array('s'=>'Update status'),$_smarty_tpl);?>

									</button>
								</div>
							</div>
						</form>
					</div>
					<!-- Tab documents -->
					<div class="tab-pane" id="documents">
						<h4 class="visible-print"><?php echo smartyTranslate(array('s'=>'Documents'),$_smarty_tpl);?>
 <span class="badge">(<?php echo count($_smarty_tpl->tpl_vars['order']->value->getDocuments());?>
)</span></h4>
						
						<?php /*  Call merged included template "controllers/orders/_documents.tpl" */
$_tpl_stack[] = $_smarty_tpl;
 $_smarty_tpl = $_smarty_tpl->setupInlineSubTemplate('controllers/orders/_documents.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0, '961345758568e04489aaee5-97615435');
content_568e0448b32fe1_53922290($_smarty_tpl);
$_smarty_tpl = array_pop($_tpl_stack); 
/*  End of included template "controllers/orders/_documents.tpl" */?>
					</div>
				</div>
				<div class="row">
					<div class="panel">
						<div class="panel-heading order_status_heading">
							<i class="icon-credit-card"></i>
							<?php echo smartyTranslate(array('s'=>'Order Status'),$_smarty_tpl);?>

						</div>
						<div class="panel-content">
							<div class="row">
								<div class="col-lg-12">
									<table class="table table-responsive">
										<tr>
											<th><?php echo smartyTranslate(array('s'=>'Room No.'),$_smarty_tpl);?>
</th>
											<th><?php echo smartyTranslate(array('s'=>'Date From'),$_smarty_tpl);?>
</th>
											<th><?php echo smartyTranslate(array('s'=>'Date To'),$_smarty_tpl);?>
</th>
											<th><?php echo smartyTranslate(array('s'=>'Order Status'),$_smarty_tpl);?>
</th>
										</tr>
										<?php if (isset($_smarty_tpl->tpl_vars['htl_booking_order_data']->value)&&$_smarty_tpl->tpl_vars['htl_booking_order_data']->value) {?>
										<?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['data']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['htl_booking_order_data']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value) {
$_smarty_tpl->tpl_vars['data']->_loop = true;
?>
										<tr>
											<td>
												<?php echo $_smarty_tpl->tpl_vars['data']->value['room_num'];?>

											</td>
											<td>
												<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['data']->value['date_from'],"%d %b %Y");?>

											</td>
											<td>
												<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['data']->value['date_to'],"%d %b %Y");?>

											</td>
											<td>
												<form action="" method="post" class="form-horizontal">
													<select style="display:inline;width:40%;" name="booking_order_status">
													<?php  $_smarty_tpl->tpl_vars['state'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['state']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['hotel_order_status']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['state']->key => $_smarty_tpl->tpl_vars['state']->value) {
$_smarty_tpl->tpl_vars['state']->_loop = true;
?>
														<option value="<?php echo intval($_smarty_tpl->tpl_vars['state']->value['id']);?>
" <?php if (isset($_smarty_tpl->tpl_vars['data']->value['id_status'])&&$_smarty_tpl->tpl_vars['state']->value['id']==$_smarty_tpl->tpl_vars['data']->value['id_status']) {?> selected="selected" disabled="disabled"<?php }?>><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['state']->value['status'], ENT_QUOTES, 'UTF-8', true);?>
</option>
													<?php } ?> 
													</select>
													<input type="hidden" name="id_room" value="<?php echo $_smarty_tpl->tpl_vars['data']->value['id_room'];?>
" />
													<input type="hidden" name="id_order" value="<?php echo $_smarty_tpl->tpl_vars['order']->value->id;?>
" />
													<button style="margin-left:20px;" type="submit" name="submitbookingOrderStatus" class="btn btn-primary">
														<?php echo smartyTranslate(array('s'=>'Update Order Status'),$_smarty_tpl);?>

													</button>
												</form>
											</td>
										</tr>
										<?php } ?>
										<?php } else { ?>
										<tr>
											<td><?php echo smartyTranslate(array('s'=>'No data found.'),$_smarty_tpl);?>
</td>
										</tr>
										<?php }?>
									</table>	
								</div>
							</div>
						</div>
					</div>
				</div>
				<script>
					$('#tabOrder a').click(function (e) {
						e.preventDefault()
						$(this).tab('show')
					})
				</script>
				<hr />
				<!-- Tab nav -->
				<ul class="nav nav-tabs" id="myTab" style="display:none"><!-- by webkul -->
					<?php echo $_smarty_tpl->tpl_vars['HOOK_TAB_SHIP']->value;?>

					<li class="active">
						<a href="#shipping">
							<i class="icon-truck "></i>
							<?php echo smartyTranslate(array('s'=>'Shipping'),$_smarty_tpl);?>
 <span class="badge"><?php echo count($_smarty_tpl->tpl_vars['order']->value->getShipping());?>
</span>
						</a>
					</li>
					<li>
						<a href="#returns">
							<i class="icon-undo"></i>
							<?php echo smartyTranslate(array('s'=>'Merchandise Returns'),$_smarty_tpl);?>
 <span class="badge"><?php echo count($_smarty_tpl->tpl_vars['order']->value->getReturn());?>
</span>
						</a>
					</li>
				</ul>
				<!-- Tab content -->
				<div class="tab-content panel" style="display:none"><!-- by webkul -->
				<?php echo $_smarty_tpl->tpl_vars['HOOK_CONTENT_SHIP']->value;?>

					<!-- Tab shipping -->
					<div class="tab-pane active" id="shipping">
						<h4 class="visible-print"><?php echo smartyTranslate(array('s'=>'Shipping'),$_smarty_tpl);?>
 <span class="badge">(<?php echo count($_smarty_tpl->tpl_vars['order']->value->getShipping());?>
)</span></h4>
						<!-- Shipping block -->
						<?php if (!$_smarty_tpl->tpl_vars['order']->value->isVirtual()) {?>
						<div class="form-horizontal">
							<?php if ($_smarty_tpl->tpl_vars['order']->value->gift_message) {?>
							<div class="form-group">
								<label class="control-label col-lg-3"><?php echo smartyTranslate(array('s'=>'Message'),$_smarty_tpl);?>
</label>
								<div class="col-lg-9">
									<p class="form-control-static"><?php echo nl2br($_smarty_tpl->tpl_vars['order']->value->gift_message);?>
</p>
								</div>
							</div>
							<?php }?>
							<?php /*  Call merged included template "controllers/orders/_shipping.tpl" */
$_tpl_stack[] = $_smarty_tpl;
 $_smarty_tpl = $_smarty_tpl->setupInlineSubTemplate('controllers/orders/_shipping.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0, '961345758568e04489aaee5-97615435');
content_568e0448c6c3c6_88819540($_smarty_tpl);
$_smarty_tpl = array_pop($_tpl_stack); 
/*  End of included template "controllers/orders/_shipping.tpl" */?>
							<?php if ($_smarty_tpl->tpl_vars['carrierModuleCall']->value) {?>
								<?php echo $_smarty_tpl->tpl_vars['carrierModuleCall']->value;?>

							<?php }?>
							<hr />
							<?php if ($_smarty_tpl->tpl_vars['order']->value->recyclable) {?>
								<span class="label label-success"><i class="icon-check"></i> <?php echo smartyTranslate(array('s'=>'Recycled packaging'),$_smarty_tpl);?>
</span>
							<?php } else { ?>
								<span class="label label-inactive"><i class="icon-remove"></i> <?php echo smartyTranslate(array('s'=>'Recycled packaging'),$_smarty_tpl);?>
</span>
							<?php }?>

							<?php if ($_smarty_tpl->tpl_vars['order']->value->gift) {?>
								<span class="label label-success"><i class="icon-check"></i> <?php echo smartyTranslate(array('s'=>'Gift wrapping'),$_smarty_tpl);?>
</span>
							<?php } else { ?>
								<span class="label label-inactive"><i class="icon-remove"></i> <?php echo smartyTranslate(array('s'=>'Gift wrapping'),$_smarty_tpl);?>
</span>
							<?php }?>
						</div>
						<?php }?>
					</div>
					<!-- Tab returns -->
					<div class="tab-pane" id="returns">
						<h4 class="visible-print"><?php echo smartyTranslate(array('s'=>'Merchandise Returns'),$_smarty_tpl);?>
 <span class="badge">(<?php echo count($_smarty_tpl->tpl_vars['order']->value->getReturn());?>
)</span></h4>
						<?php if (!$_smarty_tpl->tpl_vars['order']->value->isVirtual()) {?>
						<!-- Return block -->
							<?php if (count($_smarty_tpl->tpl_vars['order']->value->getReturn())>0) {?>
							<div class="table-responsive">
								<table class="table">
									<thead>
										<tr>
											<th><span class="title_box ">Date</span></th>
											<th><span class="title_box ">Type</span></th>
											<th><span class="title_box ">Carrier</span></th>
											<th><span class="title_box ">Tracking number</span></th>
										</tr>
									</thead>
									<tbody>
										<?php  $_smarty_tpl->tpl_vars['line'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['line']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['order']->value->getReturn(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['line']->key => $_smarty_tpl->tpl_vars['line']->value) {
$_smarty_tpl->tpl_vars['line']->_loop = true;
?>
										<tr>
											<td><?php echo $_smarty_tpl->tpl_vars['line']->value['date_add'];?>
</td>
											<td><?php echo $_smarty_tpl->tpl_vars['line']->value['type'];?>
</td>
											<td><?php echo $_smarty_tpl->tpl_vars['line']->value['state_name'];?>
</td>
											<td class="actions">
												<span class="shipping_number_show"><?php if (isset($_smarty_tpl->tpl_vars['line']->value['url'])&&isset($_smarty_tpl->tpl_vars['line']->value['tracking_number'])) {?><a href="<?php echo htmlspecialchars(smarty_modifier_replace($_smarty_tpl->tpl_vars['line']->value['url'],'@',$_smarty_tpl->tpl_vars['line']->value['tracking_number']), ENT_QUOTES, 'UTF-8', true);?>
"><?php echo $_smarty_tpl->tpl_vars['line']->value['tracking_number'];?>
</a><?php } elseif (isset($_smarty_tpl->tpl_vars['line']->value['tracking_number'])) {?><?php echo $_smarty_tpl->tpl_vars['line']->value['tracking_number'];?>
<?php }?></span>
												<?php if ($_smarty_tpl->tpl_vars['line']->value['can_edit']) {?>
												<form method="post" action="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminOrders'), ENT_QUOTES, 'UTF-8', true);?>
&amp;vieworder&amp;id_order=<?php echo intval($_smarty_tpl->tpl_vars['order']->value->id);?>
&amp;id_order_invoice=<?php if ($_smarty_tpl->tpl_vars['line']->value['id_order_invoice']) {?><?php echo intval($_smarty_tpl->tpl_vars['line']->value['id_order_invoice']);?>
<?php } else { ?>0<?php }?>&amp;id_carrier=<?php if ($_smarty_tpl->tpl_vars['line']->value['id_carrier']) {?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['line']->value['id_carrier'], ENT_QUOTES, 'UTF-8', true);?>
<?php } else { ?>0<?php }?>">
													<span class="shipping_number_edit" style="display:none;">
														<button type="button" name="tracking_number">
															<?php echo htmlentities($_smarty_tpl->tpl_vars['line']->value['tracking_number']);?>

														</button>
														<button type="submit" class="btn btn-default" name="submitShippingNumber">
															<?php echo smartyTranslate(array('s'=>'Update'),$_smarty_tpl);?>

														</button>
													</span>
													<button href="#" class="edit_shipping_number_link">
														<i class="icon-pencil"></i>
														<?php echo smartyTranslate(array('s'=>'Edit'),$_smarty_tpl);?>

													</button>
													<button href="#" class="cancel_shipping_number_link" style="display: none;">
														<i class="icon-remove"></i>
														<?php echo smartyTranslate(array('s'=>'Cancel'),$_smarty_tpl);?>

													</button>
												</form>
												<?php }?>
											</td>
										</tr>
										<?php } ?>
									</tbody>
								</table>
							</div>
							<?php } else { ?>
							<div class="list-empty hidden-print">
								<div class="list-empty-msg">
									<i class="icon-warning-sign list-empty-icon"></i>
									<?php echo smartyTranslate(array('s'=>'No merchandise returned yet'),$_smarty_tpl);?>

								</div>
							</div>
							<?php }?>
							<?php if ($_smarty_tpl->tpl_vars['carrierModuleCall']->value) {?>
								<?php echo $_smarty_tpl->tpl_vars['carrierModuleCall']->value;?>

							<?php }?>
						<?php }?>
					</div>
				</div>
				<script>
					$('#myTab a').click(function (e) {
						e.preventDefault()
						$(this).tab('show')
					})
				</script>
			</div>
			<!-- Payments block -->
			<div id="formAddPaymentPanel" class="panel">
				<div class="panel-heading">
					<i class="icon-money"></i>
					<?php echo smartyTranslate(array('s'=>"Payment"),$_smarty_tpl);?>
 <span class="badge"><?php echo count($_smarty_tpl->tpl_vars['order']->value->getOrderPayments());?>
</span>
				</div>
				<?php if (count($_smarty_tpl->tpl_vars['order']->value->getOrderPayments())>0) {?>
					<p class="alert alert-danger"<?php if (round($_smarty_tpl->tpl_vars['orders_total_paid_tax_incl']->value,2)==round($_smarty_tpl->tpl_vars['total_paid']->value,2)||(isset($_smarty_tpl->tpl_vars['currentState']->value)&&$_smarty_tpl->tpl_vars['currentState']->value->id==6)) {?> style="display: none;"<?php }?>>
						<?php echo smartyTranslate(array('s'=>'Warning'),$_smarty_tpl);?>

						<strong><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['total_paid']->value,'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>
</strong>
						<?php echo smartyTranslate(array('s'=>'paid instead of'),$_smarty_tpl);?>

						<strong class="total_paid"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['orders_total_paid_tax_incl']->value,'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>
</strong>
						<?php  $_smarty_tpl->tpl_vars['brother_order'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['brother_order']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['order']->value->getBrother(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['brother_order']->index=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['brother_order']->key => $_smarty_tpl->tpl_vars['brother_order']->value) {
$_smarty_tpl->tpl_vars['brother_order']->_loop = true;
 $_smarty_tpl->tpl_vars['brother_order']->index++;
 $_smarty_tpl->tpl_vars['brother_order']->first = $_smarty_tpl->tpl_vars['brother_order']->index === 0;
?>
							<?php if ($_smarty_tpl->tpl_vars['brother_order']->first) {?>
								<?php if (count($_smarty_tpl->tpl_vars['order']->value->getBrother())==1) {?>
									<br /><?php echo smartyTranslate(array('s'=>'This warning also concerns order '),$_smarty_tpl);?>

								<?php } else { ?>
									<br /><?php echo smartyTranslate(array('s'=>'This warning also concerns the next orders:'),$_smarty_tpl);?>

								<?php }?>
							<?php }?>
							<a href="<?php echo $_smarty_tpl->tpl_vars['current_index']->value;?>
&amp;vieworder&amp;id_order=<?php echo $_smarty_tpl->tpl_vars['brother_order']->value->id;?>
&amp;token=<?php echo htmlspecialchars($_GET['token'], ENT_QUOTES, 'UTF-8', true);?>
">
								#<?php echo sprintf('%06d',$_smarty_tpl->tpl_vars['brother_order']->value->id);?>

							</a>
						<?php } ?>
					</p>
				<?php }?>
				<form id="formAddPayment"  method="post" action="<?php echo $_smarty_tpl->tpl_vars['current_index']->value;?>
&amp;vieworder&amp;id_order=<?php echo $_smarty_tpl->tpl_vars['order']->value->id;?>
&amp;token=<?php echo htmlspecialchars($_GET['token'], ENT_QUOTES, 'UTF-8', true);?>
">
					<div class="table-responsive">
						<table class="table">
							<thead>
								<tr>
									<th><span class="title_box "><?php echo smartyTranslate(array('s'=>'Date'),$_smarty_tpl);?>
</span></th>
									<th><span class="title_box "><?php echo smartyTranslate(array('s'=>'Payment method'),$_smarty_tpl);?>
</span></th>
									<th><span class="title_box "><?php echo smartyTranslate(array('s'=>'Transaction ID'),$_smarty_tpl);?>
</span></th>
									<th><span class="title_box "><?php echo smartyTranslate(array('s'=>'Amount'),$_smarty_tpl);?>
</span></th>
									<th><span class="title_box "><?php echo smartyTranslate(array('s'=>'Invoice'),$_smarty_tpl);?>
</span></th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								<?php  $_smarty_tpl->tpl_vars['payment'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['payment']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['order']->value->getOrderPaymentCollection(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['payment']->key => $_smarty_tpl->tpl_vars['payment']->value) {
$_smarty_tpl->tpl_vars['payment']->_loop = true;
?>
								<tr>
									<td><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['dateFormat'][0][0]->dateFormat(array('date'=>$_smarty_tpl->tpl_vars['payment']->value->date_add,'full'=>true),$_smarty_tpl);?>
</td>
									<td><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['payment']->value->payment_method, ENT_QUOTES, 'UTF-8', true);?>
</td>
									<td><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['payment']->value->transaction_id, ENT_QUOTES, 'UTF-8', true);?>
</td>
									<td><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['payment']->value->amount,'currency'=>$_smarty_tpl->tpl_vars['payment']->value->id_currency),$_smarty_tpl);?>
</td>
									<td>
									<?php if (!isset($_smarty_tpl->tpl_vars['invoice'])) $_smarty_tpl->tpl_vars['invoice'] = new Smarty_Variable(null);if ($_smarty_tpl->tpl_vars['invoice']->value = $_smarty_tpl->tpl_vars['payment']->value->getOrderInvoice($_smarty_tpl->tpl_vars['order']->value->id)) {?>
										<?php echo $_smarty_tpl->tpl_vars['invoice']->value->getInvoiceNumberFormatted($_smarty_tpl->tpl_vars['current_id_lang']->value,$_smarty_tpl->tpl_vars['order']->value->id_shop);?>

									<?php } else { ?>
									<?php }?>
									</td>
									<td class="actions">
										<button class="btn btn-default open_payment_information">
											<i class="icon-search"></i>
											<?php echo smartyTranslate(array('s'=>'Details'),$_smarty_tpl);?>

										</button>
									</td>
								</tr>
								<tr class="payment_information" style="display: none;">
									<td colspan="5">
										<p>
											<b><?php echo smartyTranslate(array('s'=>'Card Number'),$_smarty_tpl);?>
</b>&nbsp;
											<?php if ($_smarty_tpl->tpl_vars['payment']->value->card_number) {?>
												<?php echo $_smarty_tpl->tpl_vars['payment']->value->card_number;?>

											<?php } else { ?>
												<i><?php echo smartyTranslate(array('s'=>'Not defined'),$_smarty_tpl);?>
</i>
											<?php }?>
										</p>
										<p>
											<b><?php echo smartyTranslate(array('s'=>'Card Brand'),$_smarty_tpl);?>
</b>&nbsp;
											<?php if ($_smarty_tpl->tpl_vars['payment']->value->card_brand) {?>
												<?php echo $_smarty_tpl->tpl_vars['payment']->value->card_brand;?>

											<?php } else { ?>
												<i><?php echo smartyTranslate(array('s'=>'Not defined'),$_smarty_tpl);?>
</i>
											<?php }?>
										</p>
										<p>
											<b><?php echo smartyTranslate(array('s'=>'Card Expiration'),$_smarty_tpl);?>
</b>&nbsp;
											<?php if ($_smarty_tpl->tpl_vars['payment']->value->card_expiration) {?>
												<?php echo $_smarty_tpl->tpl_vars['payment']->value->card_expiration;?>

											<?php } else { ?>
												<i><?php echo smartyTranslate(array('s'=>'Not defined'),$_smarty_tpl);?>
</i>
											<?php }?>
										</p>
										<p>
											<b><?php echo smartyTranslate(array('s'=>'Card Holder'),$_smarty_tpl);?>
</b>&nbsp;
											<?php if ($_smarty_tpl->tpl_vars['payment']->value->card_holder) {?>
												<?php echo $_smarty_tpl->tpl_vars['payment']->value->card_holder;?>

											<?php } else { ?>
												<i><?php echo smartyTranslate(array('s'=>'Not defined'),$_smarty_tpl);?>
</i>
											<?php }?>
										</p>
									</td>
								</tr>
								<?php }
if (!$_smarty_tpl->tpl_vars['payment']->_loop) {
?>
								<tr>
									<td class="list-empty hidden-print" colspan="6">
										<div class="list-empty-msg">
											<i class="icon-warning-sign list-empty-icon"></i>
											<?php echo smartyTranslate(array('s'=>'No payment methods are available'),$_smarty_tpl);?>

										</div>
									</td>
								</tr>
								<?php } ?>
								<tr class="current-edit hidden-print">
									<td>
										<div class="input-group fixed-width-xl">
											<input type="text" name="payment_date" class="datepicker" value="<?php echo date('Y-m-d');?>
" />
											<div class="input-group-addon">
												<i class="icon-calendar-o"></i>
											</div>
										</div>
									</td>
									<td>
										<input name="payment_method" list="payment_method" class="payment_method">
										<datalist id="payment_method">
										<?php  $_smarty_tpl->tpl_vars['payment_method'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['payment_method']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['payment_methods']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['payment_method']->key => $_smarty_tpl->tpl_vars['payment_method']->value) {
$_smarty_tpl->tpl_vars['payment_method']->_loop = true;
?>
											<option value="<?php echo $_smarty_tpl->tpl_vars['payment_method']->value;?>
">
										<?php } ?>
										</datalist>
									</td>
									<td>
										<input type="text" name="payment_transaction_id" value="" class="form-control fixed-width-sm"/>
									</td>
									<td>
										<input type="text" name="payment_amount" value="" class="form-control fixed-width-sm pull-left" />
										<select name="payment_currency" class="payment_currency form-control fixed-width-xs pull-left">
											<?php  $_smarty_tpl->tpl_vars['current_currency'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['current_currency']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['currencies']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['current_currency']->key => $_smarty_tpl->tpl_vars['current_currency']->value) {
$_smarty_tpl->tpl_vars['current_currency']->_loop = true;
?>
												<option value="<?php echo $_smarty_tpl->tpl_vars['current_currency']->value['id_currency'];?>
"<?php if ($_smarty_tpl->tpl_vars['current_currency']->value['id_currency']==$_smarty_tpl->tpl_vars['currency']->value->id) {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['current_currency']->value['sign'];?>
</option>
											<?php } ?>
										</select>
									</td>
									<td>
										<?php if (count($_smarty_tpl->tpl_vars['invoices_collection']->value)>0) {?>
											<select name="payment_invoice" id="payment_invoice">
											<?php  $_smarty_tpl->tpl_vars['invoice'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['invoice']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['invoices_collection']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['invoice']->key => $_smarty_tpl->tpl_vars['invoice']->value) {
$_smarty_tpl->tpl_vars['invoice']->_loop = true;
?>
												<option value="<?php echo $_smarty_tpl->tpl_vars['invoice']->value->id;?>
" selected="selected"><?php echo $_smarty_tpl->tpl_vars['invoice']->value->getInvoiceNumberFormatted($_smarty_tpl->tpl_vars['current_id_lang']->value,$_smarty_tpl->tpl_vars['order']->value->id_shop);?>
</option>
											<?php } ?>
											</select>
										<?php }?>
									</td>
									<td class="actions">
										<button class="btn btn-primary" type="submit" name="submitAddPayment">
											<?php echo smartyTranslate(array('s'=>'Add'),$_smarty_tpl);?>

										</button>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</form>
				<?php if ((!$_smarty_tpl->tpl_vars['order']->value->valid&&sizeof($_smarty_tpl->tpl_vars['currencies']->value)>1)) {?>
					<form class="form-horizontal well" method="post" action="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['currentIndex']->value, ENT_QUOTES, 'UTF-8', true);?>
&amp;vieworder&amp;id_order=<?php echo $_smarty_tpl->tpl_vars['order']->value->id;?>
&amp;token=<?php echo htmlspecialchars($_GET['token'], ENT_QUOTES, 'UTF-8', true);?>
">
						<div class="row">
							<label class="control-label col-lg-3"><?php echo smartyTranslate(array('s'=>'Change currency'),$_smarty_tpl);?>
</label>
							<div class="col-lg-6">
								<select name="new_currency">
								<?php  $_smarty_tpl->tpl_vars['currency_change'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['currency_change']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['currencies']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['currency_change']->key => $_smarty_tpl->tpl_vars['currency_change']->value) {
$_smarty_tpl->tpl_vars['currency_change']->_loop = true;
?>
									<?php if ($_smarty_tpl->tpl_vars['currency_change']->value['id_currency']!=$_smarty_tpl->tpl_vars['order']->value->id_currency) {?>
									<option value="<?php echo $_smarty_tpl->tpl_vars['currency_change']->value['id_currency'];?>
"><?php echo $_smarty_tpl->tpl_vars['currency_change']->value['name'];?>
 - <?php echo $_smarty_tpl->tpl_vars['currency_change']->value['sign'];?>
</option>
									<?php }?>
								<?php } ?>
								</select>
								<p class="help-block"><?php echo smartyTranslate(array('s'=>'Do not forget to update your exchange rate before making this change.'),$_smarty_tpl);?>
</p>
							</div>
							<div class="col-lg-3">
								<button type="submit" class="btn btn-default" name="submitChangeCurrency"><i class="icon-refresh"></i> <?php echo smartyTranslate(array('s'=>'Change'),$_smarty_tpl);?>
</button>
							</div>
						</div>
					</form>
				<?php }?>
			</div>
			<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>"displayAdminOrderLeft",'id_order'=>$_smarty_tpl->tpl_vars['order']->value->id),$_smarty_tpl);?>

		</div>
		<div class="col-lg-5">
			<!-- Customer informations -->
			<div class="panel">
				<?php if ($_smarty_tpl->tpl_vars['customer']->value->id) {?>
					<div class="panel-heading">
						<i class="icon-user"></i>
						<?php echo smartyTranslate(array('s'=>'Customer'),$_smarty_tpl);?>

						<span class="badge">
							<a href="?tab=AdminCustomers&amp;id_customer=<?php echo $_smarty_tpl->tpl_vars['customer']->value->id;?>
&amp;viewcustomer&amp;token=<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminCustomers'),$_smarty_tpl);?>
">
								<?php if (Configuration::get('PS_B2B_ENABLE')) {?><?php echo $_smarty_tpl->tpl_vars['customer']->value->company;?>
 - <?php }?>
								<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['gender']->value->name, ENT_QUOTES, 'UTF-8', true);?>

								<?php echo $_smarty_tpl->tpl_vars['customer']->value->firstname;?>

								<?php echo $_smarty_tpl->tpl_vars['customer']->value->lastname;?>

							</a>
						</span>
						<span class="badge">
							<?php echo smartyTranslate(array('s'=>'#'),$_smarty_tpl);?>
<?php echo $_smarty_tpl->tpl_vars['customer']->value->id;?>

						</span>
					</div>
					<div class="row">
						<div class="col-xs-6">
							<?php if (($_smarty_tpl->tpl_vars['customer']->value->isGuest())) {?>
								<?php echo smartyTranslate(array('s'=>'This order has been placed by a guest.'),$_smarty_tpl);?>

								<?php if ((!Customer::customerExists($_smarty_tpl->tpl_vars['customer']->value->email))) {?>
									<form method="post" action="index.php?tab=AdminCustomers&amp;id_customer=<?php echo $_smarty_tpl->tpl_vars['customer']->value->id;?>
&amp;token=<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminCustomers'),$_smarty_tpl);?>
">
										<input type="hidden" name="id_lang" value="<?php echo $_smarty_tpl->tpl_vars['order']->value->id_lang;?>
" />
										<input class="btn btn-default" type="submit" name="submitGuestToCustomer" value="<?php echo smartyTranslate(array('s'=>'Transform a guest into a customer'),$_smarty_tpl);?>
" />
										<p class="help-block"><?php echo smartyTranslate(array('s'=>'This feature will generate a random password and send an email to the customer.'),$_smarty_tpl);?>
</p>
									</form>
								<?php } else { ?>
									<div class="alert alert-warning">
										<?php echo smartyTranslate(array('s'=>'A registered customer account has already claimed this email address'),$_smarty_tpl);?>

									</div>
								<?php }?>
							<?php } else { ?>
								<dl class="well list-detail">
									<dt><?php echo smartyTranslate(array('s'=>'Name'),$_smarty_tpl);?>
</dt>
										<dd><?php echo $_smarty_tpl->tpl_vars['customer']->value->firstname;?>
&nbsp<?php echo $_smarty_tpl->tpl_vars['customer']->value->lastname;?>
</dd><br>
									<dt><?php echo smartyTranslate(array('s'=>'Email'),$_smarty_tpl);?>
</dt>
										<dd><a href="mailto:<?php echo $_smarty_tpl->tpl_vars['customer']->value->email;?>
"><i class="icon-envelope-o"></i> <?php echo $_smarty_tpl->tpl_vars['customer']->value->email;?>
</a></dd><br>
									<dt><?php echo smartyTranslate(array('s'=>'Account registered'),$_smarty_tpl);?>
</dt>
										<dd class="text-muted"><i class="icon-calendar-o"></i> <?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['dateFormat'][0][0]->dateFormat(array('date'=>$_smarty_tpl->tpl_vars['customer']->value->date_add,'full'=>true),$_smarty_tpl);?>
</dd>
									<!-- <dt><?php echo smartyTranslate(array('s'=>'Valid orders placed'),$_smarty_tpl);?>
</dt>
										<dd><span class="badge"><?php echo intval($_smarty_tpl->tpl_vars['customerStats']->value['nb_orders']);?>
</span></dd>
									<dt><?php echo smartyTranslate(array('s'=>'Total spent since registration'),$_smarty_tpl);?>
</dt>
										<dd><span class="badge badge-success"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>Tools::ps_round(Tools::convertPrice($_smarty_tpl->tpl_vars['customerStats']->value['total_orders'],$_smarty_tpl->tpl_vars['currency']->value),2),'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>
</span></dd>
									<?php if (Configuration::get('PS_B2B_ENABLE')) {?>
										<dt><?php echo smartyTranslate(array('s'=>'Siret'),$_smarty_tpl);?>
</dt>
											<dd><?php echo $_smarty_tpl->tpl_vars['customer']->value->siret;?>
</dd>
										<dt><?php echo smartyTranslate(array('s'=>'APE'),$_smarty_tpl);?>
</dt>
											<dd><?php echo $_smarty_tpl->tpl_vars['customer']->value->ape;?>
</dd>
									<?php }?> -->
								</dl>
							<?php }?>
						</div>

						<div class="col-xs-6">
							<div class="form-group hidden-print">
								<a href="?tab=AdminCustomers&amp;id_customer=<?php echo $_smarty_tpl->tpl_vars['customer']->value->id;?>
&amp;viewcustomer&amp;token=<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminCustomers'),$_smarty_tpl);?>
" class="btn btn-default btn-block"><?php echo smartyTranslate(array('s'=>'View full details...'),$_smarty_tpl);?>
</a>
							</div>
							<div class="panel panel-sm">
								<div class="panel-heading">
									<i class="icon-eye-slash"></i>
									<?php echo smartyTranslate(array('s'=>'Private note'),$_smarty_tpl);?>

								</div>
								<form id="customer_note" class="form-horizontal" action="ajax.php" method="post" onsubmit="saveCustomerNote(<?php echo $_smarty_tpl->tpl_vars['customer']->value->id;?>
);return false;" >
									<div class="form-group">
										<div class="col-lg-12">
											<textarea name="note" id="noteContent" class="textarea-autosize" onkeyup="$(this).val().length > 0 ? $('#submitCustomerNote').removeAttr('disabled') : $('#submitCustomerNote').attr('disabled', 'disabled')"><?php echo $_smarty_tpl->tpl_vars['customer']->value->note;?>
</textarea>
										</div>
									</div>
									<div class="row">
										<div class="col-lg-12">
											<button type="submit" id="submitCustomerNote" class="btn btn-default pull-right" disabled="disabled">
												<i class="icon-save"></i>
												<?php echo smartyTranslate(array('s'=>'Save'),$_smarty_tpl);?>

											</button>
										</div>
									</div>
									<span id="note_feedback"></span>
								</form>
							</div>
						</div>
					</div>
				<?php }?>
				<!-- Tab nav -->
				<div class="row" style="display:none">
					<ul class="nav nav-tabs" id="tabAddresses">
						<li class="active">
							<a href="#addressShipping">
								<i class="icon-truck"></i>
								<?php echo smartyTranslate(array('s'=>'Shipping address'),$_smarty_tpl);?>

							</a>
						</li>
						<li>
							<a href="#addressInvoice">
								<i class="icon-file-text"></i>
								<?php echo smartyTranslate(array('s'=>'Invoice address'),$_smarty_tpl);?>

							</a>
						</li>
					</ul>
					<!-- Tab content -->
					<div class="tab-content panel">
						<!-- Tab status -->
						<div class="tab-pane  in active" id="addressShipping">
							<!-- Addresses -->
							<h4 class="visible-print"><?php echo smartyTranslate(array('s'=>'Shipping address'),$_smarty_tpl);?>
</h4>
							<?php if (!$_smarty_tpl->tpl_vars['order']->value->isVirtual()) {?>
							<!-- Shipping address -->
								<?php if ($_smarty_tpl->tpl_vars['can_edit']->value) {?>
									<form class="form-horizontal hidden-print" method="post" action="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminOrders'), ENT_QUOTES, 'UTF-8', true);?>
&amp;vieworder&amp;id_order=<?php echo intval($_smarty_tpl->tpl_vars['order']->value->id);?>
">
										<div class="form-group">
											<div class="col-lg-9">
												<select name="id_address">
													<?php  $_smarty_tpl->tpl_vars['address'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['address']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['customer_addresses']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['address']->key => $_smarty_tpl->tpl_vars['address']->value) {
$_smarty_tpl->tpl_vars['address']->_loop = true;
?>
													<option value="<?php echo $_smarty_tpl->tpl_vars['address']->value['id_address'];?>
"
														<?php if ($_smarty_tpl->tpl_vars['address']->value['id_address']==$_smarty_tpl->tpl_vars['order']->value->id_address_delivery) {?>
															selected="selected"
														<?php }?>>
														<?php echo $_smarty_tpl->tpl_vars['address']->value['alias'];?>
 -
														<?php echo $_smarty_tpl->tpl_vars['address']->value['address1'];?>

														<?php echo $_smarty_tpl->tpl_vars['address']->value['postcode'];?>

														<?php echo $_smarty_tpl->tpl_vars['address']->value['city'];?>

														<?php if (!empty($_smarty_tpl->tpl_vars['address']->value['state'])) {?>
															<?php echo $_smarty_tpl->tpl_vars['address']->value['state'];?>

														<?php }?>,
														<?php echo $_smarty_tpl->tpl_vars['address']->value['country'];?>

													</option>
													<?php } ?>
												</select>
											</div>
											<div class="col-lg-3">
												<button class="btn btn-default" type="submit" name="submitAddressShipping"><i class="icon-refresh"></i> <?php echo smartyTranslate(array('s'=>'Change'),$_smarty_tpl);?>
</button>
											</div>
										</div>
									</form>
								<?php }?>
								<div class="well">
									<div class="row">
										<div class="col-sm-6">
											<a class="btn btn-default pull-right" href="?tab=AdminAddresses&amp;id_address=<?php echo $_smarty_tpl->tpl_vars['addresses']->value['delivery']->id;?>
&amp;addaddress&amp;realedit=1&amp;id_order=<?php echo $_smarty_tpl->tpl_vars['order']->value->id;?>
&amp;address_type=1&amp;token=<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminAddresses'),$_smarty_tpl);?>
&amp;back=<?php echo urlencode($_SERVER['REQUEST_URI']);?>
">
												<i class="icon-pencil"></i>
												<?php echo smartyTranslate(array('s'=>'Edit'),$_smarty_tpl);?>

											</a>
											<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayAddressDetail'][0][0]->generateAddressSmarty(array('address'=>$_smarty_tpl->tpl_vars['addresses']->value['delivery'],'newLine'=>'<br />'),$_smarty_tpl);?>

											<?php if ($_smarty_tpl->tpl_vars['addresses']->value['delivery']->other) {?>
												<hr /><?php echo $_smarty_tpl->tpl_vars['addresses']->value['delivery']->other;?>
<br />
											<?php }?>
										</div>
										<div class="col-sm-6 hidden-print">
											<div id="map-delivery-canvas" style="height: 190px"></div>
										</div>
									</div>
								</div>
							<?php }?>
						</div>
						<div class="tab-pane " id="addressInvoice">
							<!-- Invoice address -->
							<h4 class="visible-print"><?php echo smartyTranslate(array('s'=>'Invoice address'),$_smarty_tpl);?>
</h4>
							<?php if ($_smarty_tpl->tpl_vars['can_edit']->value) {?>
								<form class="form-horizontal hidden-print" method="post" action="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminOrders'), ENT_QUOTES, 'UTF-8', true);?>
&amp;vieworder&amp;id_order=<?php echo intval($_smarty_tpl->tpl_vars['order']->value->id);?>
">
									<div class="form-group">
										<div class="col-lg-9">
											<select name="id_address">
												<?php  $_smarty_tpl->tpl_vars['address'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['address']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['customer_addresses']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['address']->key => $_smarty_tpl->tpl_vars['address']->value) {
$_smarty_tpl->tpl_vars['address']->_loop = true;
?>
												<option value="<?php echo $_smarty_tpl->tpl_vars['address']->value['id_address'];?>
"
													<?php if ($_smarty_tpl->tpl_vars['address']->value['id_address']==$_smarty_tpl->tpl_vars['order']->value->id_address_invoice) {?>
													selected="selected"
													<?php }?>>
													<?php echo $_smarty_tpl->tpl_vars['address']->value['alias'];?>
 -
													<?php echo $_smarty_tpl->tpl_vars['address']->value['address1'];?>

													<?php echo $_smarty_tpl->tpl_vars['address']->value['postcode'];?>

													<?php echo $_smarty_tpl->tpl_vars['address']->value['city'];?>

													<?php if (!empty($_smarty_tpl->tpl_vars['address']->value['state'])) {?>
														<?php echo $_smarty_tpl->tpl_vars['address']->value['state'];?>

													<?php }?>,
													<?php echo $_smarty_tpl->tpl_vars['address']->value['country'];?>

												</option>
												<?php } ?>
											</select>
										</div>
										<div class="col-lg-3">
											<button class="btn btn-default" type="submit" name="submitAddressInvoice"><i class="icon-refresh"></i> <?php echo smartyTranslate(array('s'=>'Change'),$_smarty_tpl);?>
</button>
										</div>
									</div>
								</form>
							<?php }?>
							<div class="well">
								<div class="row">
									<div class="col-sm-6">
										<a class="btn btn-default pull-right" href="?tab=AdminAddresses&amp;id_address=<?php echo $_smarty_tpl->tpl_vars['addresses']->value['invoice']->id;?>
&amp;addaddress&amp;realedit=1&amp;id_order=<?php echo $_smarty_tpl->tpl_vars['order']->value->id;?>
&amp;address_type=2&amp;back=<?php echo urlencode($_SERVER['REQUEST_URI']);?>
&amp;token=<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminAddresses'),$_smarty_tpl);?>
">
											<i class="icon-pencil"></i>
											<?php echo smartyTranslate(array('s'=>'Edit'),$_smarty_tpl);?>

										</a>
										<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayAddressDetail'][0][0]->generateAddressSmarty(array('address'=>$_smarty_tpl->tpl_vars['addresses']->value['invoice'],'newLine'=>'<br />'),$_smarty_tpl);?>

										<?php if ($_smarty_tpl->tpl_vars['addresses']->value['invoice']->other) {?>
											<hr /><?php echo $_smarty_tpl->tpl_vars['addresses']->value['invoice']->other;?>
<br />
										<?php }?>
									</div>
									<div class="col-sm-6 hidden-print">
										<div id="map-invoice-canvas" style="height: 190px"></div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<script>
					$('#tabAddresses a').click(function (e) {
						e.preventDefault()
						$(this).tab('show')
					})
				</script>
			</div>
			<div class="panel">
				<div class="panel-heading">
					<i class="icon-envelope"></i> <?php echo smartyTranslate(array('s'=>'Messages'),$_smarty_tpl);?>
 <span class="badge"><?php echo sizeof($_smarty_tpl->tpl_vars['customer_thread_message']->value);?>
</span>
				</div>
				<?php if ((sizeof($_smarty_tpl->tpl_vars['messages']->value))) {?>
					<div class="panel panel-highlighted">
						<div class="message-item">
							<?php  $_smarty_tpl->tpl_vars['message'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['message']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['messages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['message']->key => $_smarty_tpl->tpl_vars['message']->value) {
$_smarty_tpl->tpl_vars['message']->_loop = true;
?>
								<div class="message-avatar">
									<div class="avatar-md">
										<i class="icon-user icon-2x"></i>
									</div>
								</div>
								<div class="message-body">

									<span class="message-date">&nbsp;<i class="icon-calendar"></i>
										<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['dateFormat'][0][0]->dateFormat(array('date'=>$_smarty_tpl->tpl_vars['message']->value['date_add']),$_smarty_tpl);?>
 -
									</span>
									<h4 class="message-item-heading">
										<?php if ((htmlspecialchars($_smarty_tpl->tpl_vars['message']->value['elastname'], ENT_QUOTES, 'UTF-8', true))) {?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['message']->value['efirstname'], ENT_QUOTES, 'UTF-8', true);?>

											<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['message']->value['elastname'], ENT_QUOTES, 'UTF-8', true);?>
<?php } else { ?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['message']->value['cfirstname'], ENT_QUOTES, 'UTF-8', true);?>
 <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['message']->value['clastname'], ENT_QUOTES, 'UTF-8', true);?>

										<?php }?>
										<?php if (($_smarty_tpl->tpl_vars['message']->value['private']==1)) {?>
											<span class="badge badge-info"><?php echo smartyTranslate(array('s'=>'Private'),$_smarty_tpl);?>
</span>
										<?php }?>
									</h4>
									<p class="message-item-text">
										<?php echo nl2br(htmlspecialchars($_smarty_tpl->tpl_vars['message']->value['message'], ENT_QUOTES, 'UTF-8', true));?>

									</p>
								</div>
								
							<?php } ?>
						</div>
					</div>
				<?php }?>
				<div id="messages" class="well hidden-print">
					<form action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES, 'UTF-8', true);?>
&amp;token=<?php echo htmlspecialchars($_GET['token'], ENT_QUOTES, 'UTF-8', true);?>
" method="post" onsubmit="if (getE('visibility').checked == true) return confirm('<?php echo smartyTranslate(array('s'=>'Do you want to send this message to the customer?'),$_smarty_tpl);?>
');">
						<div id="message" class="form-horizontal">
							<div class="form-group">
								<label class="control-label col-lg-3"><?php echo smartyTranslate(array('s'=>'Choose a standard message'),$_smarty_tpl);?>
</label>
								<div class="col-lg-9">
									<select class="chosen form-control" name="order_message" id="order_message" onchange="orderOverwriteMessage(this, '<?php echo smartyTranslate(array('s'=>'Do you want to overwrite your existing message?'),$_smarty_tpl);?>
')">
										<option value="0" selected="selected">-</option>
										<?php  $_smarty_tpl->tpl_vars['orderMessage'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['orderMessage']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['orderMessages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['orderMessage']->key => $_smarty_tpl->tpl_vars['orderMessage']->value) {
$_smarty_tpl->tpl_vars['orderMessage']->_loop = true;
?>
										<option value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['orderMessage']->value['message'], ENT_QUOTES, 'UTF-8', true);?>
"><?php echo $_smarty_tpl->tpl_vars['orderMessage']->value['name'];?>
</option>
										<?php } ?>
									</select>
									<p class="help-block">
										<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminOrderMessage'), ENT_QUOTES, 'UTF-8', true);?>
">
											<?php echo smartyTranslate(array('s'=>'Configure predefined messages'),$_smarty_tpl);?>

											<i class="icon-external-link"></i>
										</a>
									</p>
								</div>
							</div>

							<div class="form-group">
								<label class="control-label col-lg-3"><?php echo smartyTranslate(array('s'=>'Display to customer?'),$_smarty_tpl);?>
</label>
								<div class="col-lg-9">
									<span class="switch prestashop-switch fixed-width-lg">
										<input type="radio" name="visibility" id="visibility_on" value="0" />
										<label for="visibility_on">
											<?php echo smartyTranslate(array('s'=>'Yes'),$_smarty_tpl);?>

										</label>
										<input type="radio" name="visibility" id="visibility_off" value="1" checked="checked" />
										<label for="visibility_off">
											<?php echo smartyTranslate(array('s'=>'No'),$_smarty_tpl);?>

										</label>
										<a class="slide-button btn"></a>
									</span>
								</div>
							</div>

							<div class="form-group">
								<label class="control-label col-lg-3"><?php echo smartyTranslate(array('s'=>'Message'),$_smarty_tpl);?>
</label>
								<div class="col-lg-9">
									<textarea id="txt_msg" class="textarea-autosize" name="message"><?php echo htmlspecialchars(Tools::getValue('message'), ENT_QUOTES, 'UTF-8', true);?>
</textarea>
									<p id="nbchars"></p>
								</div>
							</div>


							<input type="hidden" name="id_order" value="<?php echo $_smarty_tpl->tpl_vars['order']->value->id;?>
" />
							<input type="hidden" name="id_customer" value="<?php echo $_smarty_tpl->tpl_vars['order']->value->id_customer;?>
" />
							<button type="submit" id="submitMessage" class="btn btn-primary pull-right" name="submitMessage">
								<?php echo smartyTranslate(array('s'=>'Send message'),$_smarty_tpl);?>

							</button>
							<a class="btn btn-default" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminCustomerThreads'), ENT_QUOTES, 'UTF-8', true);?>
&amp;id_order=<?php echo intval($_smarty_tpl->tpl_vars['order']->value->id);?>
">
								<?php echo smartyTranslate(array('s'=>'Show all messages'),$_smarty_tpl);?>

								<i class="icon-external-link"></i>
							</a>
						</div>
					</form>
				</div>
			</div>
			<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>"displayAdminOrderRight",'id_order'=>$_smarty_tpl->tpl_vars['order']->value->id),$_smarty_tpl);?>

		</div>
	</div>
	<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>"displayAdminOrder",'id_order'=>$_smarty_tpl->tpl_vars['order']->value->id),$_smarty_tpl);?>

	<div class="row" id="start_products">
		<div class="col-lg-12">
			<form class="container-command-top-spacing" action="<?php echo $_smarty_tpl->tpl_vars['current_index']->value;?>
&amp;vieworder&amp;token=<?php echo htmlspecialchars($_GET['token'], ENT_QUOTES, 'UTF-8', true);?>
&amp;id_order=<?php echo intval($_smarty_tpl->tpl_vars['order']->value->id);?>
" method="post" onsubmit="return orderDeleteProduct('<?php echo smartyTranslate(array('s'=>'This product cannot be returned.'),$_smarty_tpl);?>
', '<?php echo smartyTranslate(array('s'=>'Quantity to cancel is greater than quantity available.'),$_smarty_tpl);?>
');">
				<input type="hidden" name="id_order" value="<?php echo $_smarty_tpl->tpl_vars['order']->value->id;?>
" />
				<div style="display: none">
					<input type="hidden" value="<?php echo implode($_smarty_tpl->tpl_vars['order']->value->getWarehouseList());?>
" id="warehouse_list" />
				</div>

				<div class="panel">
					<div class="panel-heading">
						<i class="icon-shopping-cart"></i>
						<?php echo smartyTranslate(array('s'=>'Rooms In This Order'),$_smarty_tpl);?>
 <span class="badge"><?php echo count($_smarty_tpl->tpl_vars['cart_detail_data']->value);?>
</span><!-- by webkul products changes as rooms -->
					</div>
					<!--by webkul this code is added for showing rooms information on the order detail page-->
					<div class="row">
						<div class="col-lg-12">
							<table class="table" id="customer_cart_details">
								<thead>
									<tr>
										<th><span class="title_box"><?php echo smartyTranslate(array('s'=>'Room No.'),$_smarty_tpl);?>
</span></th>
										<th><span class="title_box"><?php echo smartyTranslate(array('s'=>'Room Image'),$_smarty_tpl);?>
</th>
										<th><span class="title_box"><?php echo smartyTranslate(array('s'=>'Room Type'),$_smarty_tpl);?>
</span></th>
										<th><span class="title_box"><?php echo smartyTranslate(array('s'=>'Duration'),$_smarty_tpl);?>
</span></th>
										<th><span class="title_box"><?php echo smartyTranslate(array('s'=>'Total Price'),$_smarty_tpl);?>
</span></th>
										<th><span class="title_box"><?php echo smartyTranslate(array('s'=>'Refund Stage'),$_smarty_tpl);?>
</span></th>
										<th><span class="title_box"><?php echo smartyTranslate(array('s'=>'Refund Status'),$_smarty_tpl);?>
</span></th>
										<th><span class="title_box"><?php echo smartyTranslate(array('s'=>'Reallocate Room'),$_smarty_tpl);?>
</span></th>
									</tr>
								</thead>
								<tbody>
								<?php if ($_smarty_tpl->tpl_vars['cart_detail_data']->value) {?>
									<?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['data']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['cart_detail_data']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value) {
$_smarty_tpl->tpl_vars['data']->_loop = true;
?>
										<tr>
											<td>
												<?php echo $_smarty_tpl->tpl_vars['data']->value['room_num'];?>

											</td>
											<td>
												<img src="<?php echo $_smarty_tpl->tpl_vars['data']->value['image_link'];?>
" title="Room image" />
											</td>
											<td>
												<?php echo $_smarty_tpl->tpl_vars['data']->value['room_type'];?>

											</td>
											<td>
												<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['data']->value['date_from'],"%d %b %Y");?>
&nbsp-&nbsp <?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['data']->value['date_to'],"%d %b %Y");?>

											</td>
											<td>
												<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['convertPriceWithCurrency'][0][0]->convertPriceWithCurrency(array('price'=>$_smarty_tpl->tpl_vars['data']->value['amt_with_qty'],'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>

											</td>
											
											<td class="text-center stage_name">
												<p>
													<?php if (isset($_smarty_tpl->tpl_vars['data']->value['stage_name'])&&$_smarty_tpl->tpl_vars['data']->value['stage_name']) {?>
														<?php echo $_smarty_tpl->tpl_vars['data']->value['stage_name'];?>

													<?php } else { ?>
														--
													<?php }?>
												</p>
											</td>
											<td class="text-center status_name">
												<p>
													<?php if ($_smarty_tpl->tpl_vars['data']->value['stage_name']=='Refunded'||$_smarty_tpl->tpl_vars['data']->value['stage_name']=='Rejected') {?>
														<?php echo smartyTranslate(array('s'=>"Done!"),$_smarty_tpl);?>

													<?php } elseif ($_smarty_tpl->tpl_vars['data']->value['stage_name']=='Waitting'||$_smarty_tpl->tpl_vars['data']->value['stage_name']=='Accepted') {?>
														<?php echo smartyTranslate(array('s'=>"Pending..."),$_smarty_tpl);?>

													<?php } else { ?>
														--
													<?php }?>
												</p>
											</td>

											<?php if ($_smarty_tpl->tpl_vars['data']->value['booking_type']==1) {?>
											<td>
												<?php if ($_smarty_tpl->tpl_vars['data']->value['stage_name']=='Refunded'||$_smarty_tpl->tpl_vars['data']->value['stage_name']=='Rejected') {?>
													<p class="text-center">----</p>
												<?php } else { ?>
													<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#mySwappigModal" data-id_order="<?php echo $_smarty_tpl->tpl_vars['order']->value->id;?>
" data-room_num=<?php echo $_smarty_tpl->tpl_vars['data']->value['room_num'];?>
 data-date_from=<?php echo $_smarty_tpl->tpl_vars['data']->value['date_from'];?>
 data-date_to=<?php echo $_smarty_tpl->tpl_vars['data']->value['date_to'];?>
 data-id_room=<?php echo $_smarty_tpl->tpl_vars['data']->value['id_room'];?>
 data-cust_name="<?php echo $_smarty_tpl->tpl_vars['data']->value['alloted_cust_name'];?>
" data-cust_email="<?php echo $_smarty_tpl->tpl_vars['data']->value['alloted_cust_email'];?>
" data-avail_rm_swap=<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['json_encode'][0][0]->jsonEncode($_smarty_tpl->tpl_vars['data']->value['avail_rooms_to_swap']);?>
 data-avail_rm_realloc=<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['json_encode'][0][0]->jsonEncode($_smarty_tpl->tpl_vars['data']->value['avail_rooms_to_realloc']);?>
>
														<?php echo smartyTranslate(array('s'=>'Reallocate Room','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>

													</button>
												<?php }?>
											</td>
											<?php }?>
										</tr>
									<?php } ?>
								<?php } else { ?>
									<tr>
										<td><?php echo smartyTranslate(array('s'=>'No Data Found.'),$_smarty_tpl);?>
</td>
									</tr>
								<?php }?>
								</tbody>
							</table>
						</div>
					</div>
					<div id="refundForm">
					<!--
						<a href="#" class="standard_refund"><img src="../img/admin/add.gif" alt="<?php echo smartyTranslate(array('s'=>'Process a standard refund'),$_smarty_tpl);?>
" /> <?php echo smartyTranslate(array('s'=>'Process a standard refund'),$_smarty_tpl);?>
</a>
						<a href="#" class="partial_refund"><img src="../img/admin/add.gif" alt="<?php echo smartyTranslate(array('s'=>'Process a partial refund'),$_smarty_tpl);?>
" /> <?php echo smartyTranslate(array('s'=>'Process a partial refund'),$_smarty_tpl);?>
</a>
					--><!-- by webkul to hide unnessary things in the page-->
					</div>

					<!-- <?php $_smarty_tpl->_capture_stack[0][] = array("TaxMethod", null, null); ob_start(); ?>
						<?php if (($_smarty_tpl->tpl_vars['order']->value->getTaxCalculationMethod()==@constant('PS_TAX_EXC'))) {?>
							<?php echo smartyTranslate(array('s'=>'tax excluded.'),$_smarty_tpl);?>

						<?php } else { ?>
							<?php echo smartyTranslate(array('s'=>'tax included.'),$_smarty_tpl);?>

						<?php }?>
					<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
					<?php if (($_smarty_tpl->tpl_vars['order']->value->getTaxCalculationMethod()==@constant('PS_TAX_EXC'))) {?>
						<input type="hidden" name="TaxMethod" value="0">
					<?php } else { ?>
						<input type="hidden" name="TaxMethod" value="1">
					<?php }?>
					<div class="table-responsive">
						<table class="table" id="orderProducts">
							<thead>
								<tr>
									<th></th>
									<th><span class="title_box "><?php echo smartyTranslate(array('s'=>'Product'),$_smarty_tpl);?>
</span></th>
									<th>
										<span class="title_box "><?php echo smartyTranslate(array('s'=>'Unit Price'),$_smarty_tpl);?>
</span>
										<small class="text-muted"><?php echo Smarty::$_smarty_vars['capture']['TaxMethod'];?>
</small>
									</th>
									<th class="text-center"><span class="title_box "><?php echo smartyTranslate(array('s'=>'Qty'),$_smarty_tpl);?>
</span></th>
									<?php if ($_smarty_tpl->tpl_vars['display_warehouse']->value) {?><th><span class="title_box "><?php echo smartyTranslate(array('s'=>'Warehouse'),$_smarty_tpl);?>
</span></th><?php }?>
									<?php if (($_smarty_tpl->tpl_vars['order']->value->hasBeenPaid())) {?><th class="text-center"><span class="title_box "><?php echo smartyTranslate(array('s'=>'Refunded'),$_smarty_tpl);?>
</span></th><?php }?>
									<?php if (($_smarty_tpl->tpl_vars['order']->value->hasBeenDelivered()||$_smarty_tpl->tpl_vars['order']->value->hasProductReturned())) {?>
										<th class="text-center"><span class="title_box "><?php echo smartyTranslate(array('s'=>'Returned'),$_smarty_tpl);?>
</span></th>
									<?php }?>
									<?php if ($_smarty_tpl->tpl_vars['stock_management']->value) {?><th class="text-center"><span class="title_box "><?php echo smartyTranslate(array('s'=>'Available quantity'),$_smarty_tpl);?>
</span></th><?php }?>
									<th>
										<span class="title_box "><?php echo smartyTranslate(array('s'=>'Total'),$_smarty_tpl);?>
</span>
										<small class="text-muted"><?php echo Smarty::$_smarty_vars['capture']['TaxMethod'];?>
</small>
									</th>
									<th style="display: none;" class="add_product_fields"></th>
									<th style="display: none;" class="edit_product_fields"></th>
									<th style="display: none;" class="standard_refund_fields">
										<i class="icon-minus-sign"></i>
										<?php if (($_smarty_tpl->tpl_vars['order']->value->hasBeenDelivered()||$_smarty_tpl->tpl_vars['order']->value->hasBeenShipped())) {?>
											<?php echo smartyTranslate(array('s'=>'Return'),$_smarty_tpl);?>

										<?php } elseif (($_smarty_tpl->tpl_vars['order']->value->hasBeenPaid())) {?>
											<?php echo smartyTranslate(array('s'=>'Refund'),$_smarty_tpl);?>

										<?php } else { ?>
											<?php echo smartyTranslate(array('s'=>'Cancel'),$_smarty_tpl);?>

										<?php }?>
									</th>
									<th style="display:none" class="partial_refund_fields">
										<span class="title_box "><?php echo smartyTranslate(array('s'=>'Partial refund'),$_smarty_tpl);?>
</span>
									</th>
									<?php if (!$_smarty_tpl->tpl_vars['order']->value->hasBeenDelivered()) {?>
									<th></th>
									<?php }?>
								</tr>
							</thead>
							<tbody>
							<?php  $_smarty_tpl->tpl_vars['product'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['product']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['products']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['product']->key => $_smarty_tpl->tpl_vars['product']->value) {
$_smarty_tpl->tpl_vars['product']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['product']->key;
?>
								
								<?php /*  Call merged included template "controllers/orders/_customized_data.tpl" */
$_tpl_stack[] = $_smarty_tpl;
 $_smarty_tpl = $_smarty_tpl->setupInlineSubTemplate('controllers/orders/_customized_data.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0, '961345758568e04489aaee5-97615435');
content_568e044911e471_08201355($_smarty_tpl);
$_smarty_tpl = array_pop($_tpl_stack); 
/*  End of included template "controllers/orders/_customized_data.tpl" */?>
								
								<?php /*  Call merged included template "controllers/orders/_product_line.tpl" */
$_tpl_stack[] = $_smarty_tpl;
 $_smarty_tpl = $_smarty_tpl->setupInlineSubTemplate('controllers/orders/_product_line.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0, '961345758568e04489aaee5-97615435');
content_568e044931e766_24352412($_smarty_tpl);
$_smarty_tpl = array_pop($_tpl_stack); 
/*  End of included template "controllers/orders/_product_line.tpl" */?>
							<?php } ?>
							<?php if ($_smarty_tpl->tpl_vars['can_edit']->value) {?>
								<?php /*  Call merged included template "controllers/orders/_new_product.tpl" */
$_tpl_stack[] = $_smarty_tpl;
 $_smarty_tpl = $_smarty_tpl->setupInlineSubTemplate('controllers/orders/_new_product.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0, '961345758568e04489aaee5-97615435');
content_568e04495059a2_37151400($_smarty_tpl);
$_smarty_tpl = array_pop($_tpl_stack); 
/*  End of included template "controllers/orders/_new_product.tpl" */?>
							<?php }?>
							</tbody>
						</table>
					</div>

					<?php if ($_smarty_tpl->tpl_vars['can_edit']->value) {?>
					<div class="row-margin-bottom row-margin-top order_action">
					<?php if (!$_smarty_tpl->tpl_vars['order']->value->hasBeenDelivered()) {?>
						<button type="button" id="add_product" class="btn btn-default">
							<i class="icon-plus-sign"></i>
							<?php echo smartyTranslate(array('s'=>'Add a product'),$_smarty_tpl);?>

						</button>
					<?php }?>
						<button id="add_voucher" class="btn btn-default" type="button" >
							<i class="icon-ticket"></i>
							<?php echo smartyTranslate(array('s'=>'Add a new discount'),$_smarty_tpl);?>

						</button>
					</div>
					<?php }?> --> <!-- by webkul to hide unnessary things in the page-->
					<div class="clear">&nbsp;</div>
					<hr>
					<div class="row">
						<!-- <div class="col-xs-6">
							<div class="alert alert-warning">
								<?php echo smartyTranslate(array('s'=>'For this customer group, prices are displayed as: [1]%s[/1]','sprintf'=>array(Smarty::$_smarty_vars['capture']['TaxMethod']),'tags'=>array('<strong>')),$_smarty_tpl);?>

								<?php if (!Configuration::get('PS_ORDER_RETURN')) {?>
									<br/><strong><?php echo smartyTranslate(array('s'=>'Merchandise returns are disabled'),$_smarty_tpl);?>
</strong>
								<?php }?>
							</div>
						</div> --> <!-- by webkul to hide unnessary things in the page-->
						
						<!-- For Due amount submit panel (by webkul) -->
						<?php if (isset($_smarty_tpl->tpl_vars['order_adv_dtl']->value)) {?>
							<div class="col-xs-6">
								<div class="panel panel-advanced-payment">
									<form class="clearfix" method="POST" action="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminOrders'), ENT_QUOTES, 'UTF-8', true);?>
&amp;vieworder&amp;id_order=<?php echo intval($_smarty_tpl->tpl_vars['order_adv_dtl']->value['id_order']);?>
">
										<div class="table-responsive">
											<table class="table">
												<tr>
													<td>
														<strong><?php echo smartyTranslate(array('s'=>'Paid Amount'),$_smarty_tpl);?>
</strong>
													</td>
													<td>
														<strong>
															<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['order_adv_dtl']->value['total_paid_amount'],'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>

														</strong>
													</td>
												</tr>
												<tr>
													<td>
														<strong><?php echo smartyTranslate(array('s'=>'Due Amount'),$_smarty_tpl);?>
</strong>
													</td>
													<td>
														<strong><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>($_smarty_tpl->tpl_vars['order_adv_dtl']->value['total_order_amount']-$_smarty_tpl->tpl_vars['order_adv_dtl']->value['total_paid_amount']),'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>
</strong>
													</td>
												</tr>
												<tr>
													<td>
														<strong><?php echo smartyTranslate(array('s'=>'submitted Amount'),$_smarty_tpl);?>
</strong>
													</td>
													<td>
														<div class="input-group">
															<span class="input-group-addon" id="htl_order_currenct"><?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
</span>
															<input type="text" class="form-control" placeholder="Amount" aria-describedby="htl_order_currenct" name="submitted_amount">
														</div>
													</td>
												</tr>
											</table>
										</div>
										<button type="submit" class="btn btn-primary" name="payDueAmount" style="margin-top:10px;"><?php echo smartyTranslate(array('s'=>"Submit Amount"),$_smarty_tpl);?>
</button>
									</form>
								</div>
							</div>
						<?php }?>
						
						<div class="col-xs-6 pull-right">
							<div class="panel panel-vouchers" style="<?php if (!sizeof($_smarty_tpl->tpl_vars['discounts']->value)) {?>display:none;<?php }?>">
								<?php if ((sizeof($_smarty_tpl->tpl_vars['discounts']->value)||$_smarty_tpl->tpl_vars['can_edit']->value)) {?>
								<div class="table-responsive">
									<table class="table">
										<thead>
											<tr>
												<th>
													<span class="title_box ">
														<?php echo smartyTranslate(array('s'=>'Discount name'),$_smarty_tpl);?>

													</span>
												</th>
												<th>
													<span class="title_box ">
														<?php echo smartyTranslate(array('s'=>'Value'),$_smarty_tpl);?>

													</span>
												</th>
												<?php if ($_smarty_tpl->tpl_vars['can_edit']->value) {?>
												<th></th>
												<?php }?>
											</tr>
										</thead>
										<tbody>
											<?php  $_smarty_tpl->tpl_vars['discount'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['discount']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['discounts']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['discount']->key => $_smarty_tpl->tpl_vars['discount']->value) {
$_smarty_tpl->tpl_vars['discount']->_loop = true;
?>
											<tr>
												<td><?php echo $_smarty_tpl->tpl_vars['discount']->value['name'];?>
</td>
												<td>
												<?php if ($_smarty_tpl->tpl_vars['discount']->value['value']!=0.00) {?>
													-
												<?php }?>
												<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['discount']->value['value'],'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>

												</td>
												<?php if ($_smarty_tpl->tpl_vars['can_edit']->value) {?>
												<td>
													<a href="<?php echo $_smarty_tpl->tpl_vars['current_index']->value;?>
&amp;submitDeleteVoucher&amp;id_order_cart_rule=<?php echo $_smarty_tpl->tpl_vars['discount']->value['id_order_cart_rule'];?>
&amp;id_order=<?php echo $_smarty_tpl->tpl_vars['order']->value->id;?>
&amp;token=<?php echo htmlspecialchars($_GET['token'], ENT_QUOTES, 'UTF-8', true);?>
">
														<i class="icon-minus-sign"></i>
														<?php echo smartyTranslate(array('s'=>'Delete voucher'),$_smarty_tpl);?>

													</a>
												</td>
												<?php }?>
											</tr>
											<?php } ?>
										</tbody>
									</table>
								</div>
								<div class="current-edit" id="voucher_form" style="display:none;">
									<?php /*  Call merged included template "controllers/orders/_discount_form.tpl" */
$_tpl_stack[] = $_smarty_tpl;
 $_smarty_tpl = $_smarty_tpl->setupInlineSubTemplate('controllers/orders/_discount_form.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, null, array(), 0, '961345758568e04489aaee5-97615435');
content_568e04495ca977_33262913($_smarty_tpl);
$_smarty_tpl = array_pop($_tpl_stack); 
/*  End of included template "controllers/orders/_discount_form.tpl" */?>
								</div>
								<?php }?>
							</div>
							<div class="panel panel-total">
								<div class="table-responsive">
									<table class="table">
										
										<?php if (($_smarty_tpl->tpl_vars['order']->value->getTaxCalculationMethod()==@constant('PS_TAX_EXC'))) {?>
											<?php $_smarty_tpl->tpl_vars['order_product_price'] = new Smarty_variable(($_smarty_tpl->tpl_vars['order']->value->total_products), null, 0);?>
											<?php $_smarty_tpl->tpl_vars['order_discount_price'] = new Smarty_variable($_smarty_tpl->tpl_vars['order']->value->total_discounts_tax_excl, null, 0);?>
											<?php $_smarty_tpl->tpl_vars['order_wrapping_price'] = new Smarty_variable($_smarty_tpl->tpl_vars['order']->value->total_wrapping_tax_excl, null, 0);?>
											<?php $_smarty_tpl->tpl_vars['order_shipping_price'] = new Smarty_variable($_smarty_tpl->tpl_vars['order']->value->total_shipping_tax_excl, null, 0);?>
										<?php } else { ?>
											<?php $_smarty_tpl->tpl_vars['order_product_price'] = new Smarty_variable($_smarty_tpl->tpl_vars['order']->value->total_products_wt, null, 0);?>
											<?php $_smarty_tpl->tpl_vars['order_discount_price'] = new Smarty_variable($_smarty_tpl->tpl_vars['order']->value->total_discounts_tax_incl, null, 0);?>
											<?php $_smarty_tpl->tpl_vars['order_wrapping_price'] = new Smarty_variable($_smarty_tpl->tpl_vars['order']->value->total_wrapping_tax_incl, null, 0);?>
											<?php $_smarty_tpl->tpl_vars['order_shipping_price'] = new Smarty_variable($_smarty_tpl->tpl_vars['order']->value->total_shipping_tax_incl, null, 0);?>
										<?php }?>
										<tr id="total_products">
											<td class="text-right"><strong><?php echo smartyTranslate(array('s'=>'Total Rooms Cost'),$_smarty_tpl);?>
</strong></td>
											<td class="amount text-right nowrap">
												<strong><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['total_rooms_cost']->value,'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>
</strong>
											</td>
											<td class="partial_refund_fields current-edit" style="display:none;"></td>
										</tr>
										<tr id="total_tax_order">
											<td class="text-right"><strong><?php echo smartyTranslate(array('s'=>'Total Tax'),$_smarty_tpl);?>
</strong></td>
											<td class="text-right nowrap">
												<strong><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['total_tax_in_order']->value,'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>
</strong>
											</td>
											<td class="partial_refund_fields current-edit" style="display:none;"></td>
										</tr>
										<tr id="total_discounts" <?php if ($_smarty_tpl->tpl_vars['order']->value->total_discounts_tax_incl==0) {?>style="display: none;"<?php }?>>
											<td class="text-right"><?php echo smartyTranslate(array('s'=>'Discounts'),$_smarty_tpl);?>
</td>
											<td class="amount text-right nowrap">
												-<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['order_discount_price']->value,'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>

											</td>
											<td class="partial_refund_fields current-edit" style="display:none;"></td>
										</tr>
										<tr id="total_wrapping" <?php if ($_smarty_tpl->tpl_vars['order']->value->total_wrapping_tax_incl==0) {?>style="display: none;"<?php }?>>
											<td class="text-right"><?php echo smartyTranslate(array('s'=>'Wrapping'),$_smarty_tpl);?>
</td>
											<td class="amount text-right nowrap">
												<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['order_wrapping_price']->value,'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>

											</td>
											<td class="partial_refund_fields current-edit" style="display:none;"></td>
										</tr>
										<!-- <tr id="total_shipping">
											<td class="text-right"><?php echo smartyTranslate(array('s'=>'Shipping'),$_smarty_tpl);?>
</td>
											<td class="amount text-right nowrap" >
												<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['order_shipping_price']->value,'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>

											</td>
											<td class="partial_refund_fields current-edit" style="display:none;">
												<div class="input-group">
													<div class="input-group-addon">
														<?php echo $_smarty_tpl->tpl_vars['currency']->value->prefix;?>

														<?php echo $_smarty_tpl->tpl_vars['currency']->value->suffix;?>

													</div>
													<input type="text" name="partialRefundShippingCost" value="0" />
												</div>
												<p class="help-block"><i class="icon-warning-sign"></i> <?php echo smartyTranslate(array('s'=>'(%s)','sprintf'=>Smarty::$_smarty_vars['capture']['TaxMethod']),$_smarty_tpl);?>
</p>
											</td>
										</tr> -->    <!-- by webkul to hide unnessary things in the page-->
										<!-- <?php if (($_smarty_tpl->tpl_vars['order']->value->getTaxCalculationMethod()==@constant('PS_TAX_EXC'))) {?>
			 							<tr id="total_taxes">
			 								<td class="text-right"><?php echo smartyTranslate(array('s'=>'Taxes'),$_smarty_tpl);?>
</td>
			 								<td class="amount text-right nowrap" ><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>($_smarty_tpl->tpl_vars['order']->value->total_paid_tax_incl-$_smarty_tpl->tpl_vars['order']->value->total_paid_tax_excl),'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>
</td>
			 								<td class="partial_refund_fields current-edit" style="display:none;"></td>
			 							</tr>
			 							<?php }?> -->

			 							<?php if (isset($_smarty_tpl->tpl_vars['order_adv_dtl']->value)) {?>
				 							<tr>
												<td class="text-right"><strong><?php echo smartyTranslate(array('s'=>'Paid Amount'),$_smarty_tpl);?>
</strong></td>
												<td class="amount text-right nowrap">
													<strong>
														<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['order_adv_dtl']->value['total_paid_amount'],'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>

													</strong>
												</td>
											</tr>
											<tr>
												<td class="text-right"><strong><?php echo smartyTranslate(array('s'=>'Due Amount'),$_smarty_tpl);?>
</strong></td>
												<td class="amount text-right nowrap">
													<strong>
														<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>($_smarty_tpl->tpl_vars['order_adv_dtl']->value['total_order_amount']-$_smarty_tpl->tpl_vars['order_adv_dtl']->value['total_paid_amount']),'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>

													</strong>
												</td>
											</tr>
										<?php }?>
										
										<?php $_smarty_tpl->tpl_vars['order_total_price'] = new Smarty_variable($_smarty_tpl->tpl_vars['order']->value->total_paid_tax_incl, null, 0);?>
										
										<tr id="total_order">
											<td class="text-right"><strong><?php echo smartyTranslate(array('s'=>'Total'),$_smarty_tpl);?>
</strong></td>
											<td class="amount text-right nowrap">
												<strong><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['order_total_price']->value,'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>
</strong>
											</td>
											<td class="partial_refund_fields current-edit" style="display:none;"></td>
										</tr>
									</table>
								</div>
							</div>
						</div>
					</div>
					<div style="display: none;" class="standard_refund_fields form-horizontal panel">
						<div class="form-group">
							<?php if (($_smarty_tpl->tpl_vars['order']->value->hasBeenDelivered()&&Configuration::get('PS_ORDER_RETURN'))) {?>
							<p class="checkbox">
								<label for="reinjectQuantities">
									<input type="checkbox" id="reinjectQuantities" name="reinjectQuantities" />
									<?php echo smartyTranslate(array('s'=>'Re-stock products'),$_smarty_tpl);?>

								</label>
							</p>
							<?php }?>
							<?php if (((!$_smarty_tpl->tpl_vars['order']->value->hasBeenDelivered()&&$_smarty_tpl->tpl_vars['order']->value->hasBeenPaid())||($_smarty_tpl->tpl_vars['order']->value->hasBeenDelivered()&&Configuration::get('PS_ORDER_RETURN')))) {?>
							<p class="checkbox">
								<label for="generateCreditSlip">
									<input type="checkbox" id="generateCreditSlip" name="generateCreditSlip" onclick="toggleShippingCost()" />
									<?php echo smartyTranslate(array('s'=>'Generate a credit slip'),$_smarty_tpl);?>

								</label>
							</p>
							<p class="checkbox">
								<label for="generateDiscount">
									<input type="checkbox" id="generateDiscount" name="generateDiscount" onclick="toggleShippingCost()" />
									<?php echo smartyTranslate(array('s'=>'Generate a voucher'),$_smarty_tpl);?>

								</label>
							</p>
							<p class="checkbox" id="spanShippingBack" style="display:none;">
								<label for="shippingBack">
									<input type="checkbox" id="shippingBack" name="shippingBack" />
									<?php echo smartyTranslate(array('s'=>'Repay shipping costs'),$_smarty_tpl);?>

								</label>
							</p>
							<?php if ($_smarty_tpl->tpl_vars['order']->value->total_discounts_tax_excl>0||$_smarty_tpl->tpl_vars['order']->value->total_discounts_tax_incl>0) {?>
							<br/><p><?php echo smartyTranslate(array('s'=>'This order has been partially paid by voucher. Choose the amount you want to refund:'),$_smarty_tpl);?>
</p>
							<p class="radio">
								<label id="lab_refund_total_1" for="refund_total_1">
									<input type="radio" value="0" name="refund_total_voucher_off" id="refund_total_1" checked="checked" />
									<?php echo smartyTranslate(array('s'=>'Include amount of initial voucher: '),$_smarty_tpl);?>

								</label>
							</p>
							<p class="radio">
								<label id="lab_refund_total_2" for="refund_total_2">
									<input type="radio" value="1" name="refund_total_voucher_off" id="refund_total_2"/>
									<?php echo smartyTranslate(array('s'=>'Exclude amount of initial voucher: '),$_smarty_tpl);?>

								</label>
							</p>
							<div class="nowrap radio-inline">
								<label id="lab_refund_total_3" class="pull-left" for="refund_total_3">
									<?php echo smartyTranslate(array('s'=>'Amount of your choice: '),$_smarty_tpl);?>

									<input type="radio" value="2" name="refund_total_voucher_off" id="refund_total_3"/>
								</label>
								<div class="input-group col-lg-1 pull-left">
									<div class="input-group-addon">
										<?php echo $_smarty_tpl->tpl_vars['currency']->value->prefix;?>

										<?php echo $_smarty_tpl->tpl_vars['currency']->value->suffix;?>

									</div>
									<input type="text" class="input fixed-width-md" name="refund_total_voucher_choose" value="0"/>
								</div>
							</div>
							<?php }?>
						<?php }?>
						</div>
						<?php if ((!$_smarty_tpl->tpl_vars['order']->value->hasBeenDelivered()||($_smarty_tpl->tpl_vars['order']->value->hasBeenDelivered()&&Configuration::get('PS_ORDER_RETURN')))) {?>
						<div class="row">
							<input type="submit" name="cancelProduct" value="<?php if ($_smarty_tpl->tpl_vars['order']->value->hasBeenDelivered()) {?><?php echo smartyTranslate(array('s'=>'Return products'),$_smarty_tpl);?>
<?php } elseif ($_smarty_tpl->tpl_vars['order']->value->hasBeenPaid()) {?><?php echo smartyTranslate(array('s'=>'Refund products'),$_smarty_tpl);?>
<?php } else { ?><?php echo smartyTranslate(array('s'=>'Cancel products'),$_smarty_tpl);?>
<?php }?>" class="btn btn-default" />
						</div>
						<?php }?>
					</div>
					<div style="display:none;" class="partial_refund_fields">
						<p class="checkbox">
							<label for="reinjectQuantitiesRefund">
								<input type="checkbox" id="reinjectQuantitiesRefund" name="reinjectQuantities" />
								<?php echo smartyTranslate(array('s'=>'Re-stock products'),$_smarty_tpl);?>

							</label>
						</p>
						<p class="checkbox">
							<label for="generateDiscountRefund">
								<input type="checkbox" id="generateDiscountRefund" name="generateDiscountRefund" onclick="toggleShippingCost()" />
								<?php echo smartyTranslate(array('s'=>'Generate a voucher'),$_smarty_tpl);?>

							</label>
						</p>
						<?php if ($_smarty_tpl->tpl_vars['order']->value->total_discounts_tax_excl>0||$_smarty_tpl->tpl_vars['order']->value->total_discounts_tax_incl>0) {?>
						<p><?php echo smartyTranslate(array('s'=>'This order has been partially paid by voucher. Choose the amount you want to refund:'),$_smarty_tpl);?>
</p>
						<p class="radio">
							<label id="lab_refund_1" for="refund_1">
								<input type="radio" value="0" name="refund_voucher_off" id="refund_1" checked="checked" />
								<?php echo smartyTranslate(array('s'=>'Product(s) price: '),$_smarty_tpl);?>

							</label>
						</p>
						<p class="radio">
							<label id="lab_refund_2" for="refund_2">
								<input type="radio" value="1" name="refund_voucher_off" id="refund_2"/>
								<?php echo smartyTranslate(array('s'=>'Product(s) price, excluding amount of initial voucher: '),$_smarty_tpl);?>

							</label>
						</p>
						<div class="nowrap radio-inline">
								<label id="lab_refund_3" class="pull-left" for="refund_3">
									<?php echo smartyTranslate(array('s'=>'Amount of your choice: '),$_smarty_tpl);?>

									<input type="radio" value="2" name="refund_voucher_off" id="refund_3"/>
								</label>
								<div class="input-group col-lg-1 pull-left">
									<div class="input-group-addon">
										<?php echo $_smarty_tpl->tpl_vars['currency']->value->prefix;?>

										<?php echo $_smarty_tpl->tpl_vars['currency']->value->suffix;?>

									</div>
									<input type="text" class="input fixed-width-md" name="refund_voucher_choose" value="0"/>
								</div>
							</div>
						<?php }?>
						<br/>
						<button type="submit" name="partialRefund" class="btn btn-default">
							<i class="icon-check"></i> <?php echo smartyTranslate(array('s'=>'Partial refund'),$_smarty_tpl);?>

						</button>
					</div>
				</div>
			</form>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-12">
			<!-- Sources block -->
			<?php if ((sizeof($_smarty_tpl->tpl_vars['sources']->value))) {?>
			<div class="panel">
				<div class="panel-heading">
					<i class="icon-globe"></i>
					<?php echo smartyTranslate(array('s'=>'Sources'),$_smarty_tpl);?>
 <span class="badge"><?php echo count($_smarty_tpl->tpl_vars['sources']->value);?>
</span>
				</div>
				<ul <?php if (sizeof($_smarty_tpl->tpl_vars['sources']->value)>3) {?>style="height: 200px; overflow-y: scroll;"<?php }?>>
				<?php  $_smarty_tpl->tpl_vars['source'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['source']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['sources']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['source']->key => $_smarty_tpl->tpl_vars['source']->value) {
$_smarty_tpl->tpl_vars['source']->_loop = true;
?>
					<li>
						<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['dateFormat'][0][0]->dateFormat(array('date'=>$_smarty_tpl->tpl_vars['source']->value['date_add'],'full'=>true),$_smarty_tpl);?>
<br />
						<b><?php echo smartyTranslate(array('s'=>'From'),$_smarty_tpl);?>
</b><?php if ($_smarty_tpl->tpl_vars['source']->value['http_referer']!='') {?><a href="<?php echo $_smarty_tpl->tpl_vars['source']->value['http_referer'];?>
"><?php echo smarty_modifier_regex_replace(parse_url($_smarty_tpl->tpl_vars['source']->value['http_referer'],@constant('PHP_URL_HOST')),'/^www./','');?>
</a><?php } else { ?>-<?php }?><br />
						<b><?php echo smartyTranslate(array('s'=>'To'),$_smarty_tpl);?>
</b> <a href="http://<?php echo $_smarty_tpl->tpl_vars['source']->value['request_uri'];?>
"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['truncate'][0][0]->smarty_modifier_truncate($_smarty_tpl->tpl_vars['source']->value['request_uri'],100,'...');?>
</a><br />
						<?php if ($_smarty_tpl->tpl_vars['source']->value['keywords']) {?><b><?php echo smartyTranslate(array('s'=>'Keywords'),$_smarty_tpl);?>
</b> <?php echo $_smarty_tpl->tpl_vars['source']->value['keywords'];?>
<br /><?php }?><br />
					</li>
				<?php } ?>
				</ul>
			</div>
			<?php }?>

			<!-- linked orders block -->
			<?php if (count($_smarty_tpl->tpl_vars['order']->value->getBrother())>0) {?>
			<div class="panel">
				<div class="panel-heading">
					<i class="icon-cart"></i>
					<?php echo smartyTranslate(array('s'=>'Linked orders'),$_smarty_tpl);?>

				</div>
				<div class="table-responsive">
					<table class="table">
						<thead>
							<tr>
								<th>
									<?php echo smartyTranslate(array('s'=>'Order no. '),$_smarty_tpl);?>

								</th>
								<th>
									<?php echo smartyTranslate(array('s'=>'Status'),$_smarty_tpl);?>

								</th>
								<th>
									<?php echo smartyTranslate(array('s'=>'Amount'),$_smarty_tpl);?>

								</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<?php  $_smarty_tpl->tpl_vars['brother_order'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['brother_order']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['order']->value->getBrother(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['brother_order']->index=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['brother_order']->key => $_smarty_tpl->tpl_vars['brother_order']->value) {
$_smarty_tpl->tpl_vars['brother_order']->_loop = true;
 $_smarty_tpl->tpl_vars['brother_order']->index++;
 $_smarty_tpl->tpl_vars['brother_order']->first = $_smarty_tpl->tpl_vars['brother_order']->index === 0;
?>
							<tr>
								<td>
									<a href="<?php echo $_smarty_tpl->tpl_vars['current_index']->value;?>
&amp;vieworder&amp;id_order=<?php echo $_smarty_tpl->tpl_vars['brother_order']->value->id;?>
&amp;token=<?php echo htmlspecialchars($_GET['token'], ENT_QUOTES, 'UTF-8', true);?>
">#<?php echo $_smarty_tpl->tpl_vars['brother_order']->value->id;?>
</a>
								</td>
								<td>
									<?php echo $_smarty_tpl->tpl_vars['brother_order']->value->getCurrentOrderState()->name[$_smarty_tpl->tpl_vars['current_id_lang']->value];?>

								</td>
								<td>
									<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['brother_order']->value->total_paid_tax_incl,'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>

								</td>
								<td>
									<a href="<?php echo $_smarty_tpl->tpl_vars['current_index']->value;?>
&amp;vieworder&amp;id_order=<?php echo $_smarty_tpl->tpl_vars['brother_order']->value->id;?>
&amp;token=<?php echo htmlspecialchars($_GET['token'], ENT_QUOTES, 'UTF-8', true);?>
">
										<i class="icon-eye-open"></i>
										<?php echo smartyTranslate(array('s'=>'See the order'),$_smarty_tpl);?>

									</a>
								</td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
			<?php }?>
		</div>
	</div>

<!-- Modal for reallocation of rooms -->
<div class="modal fade" id="mySwappigModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
    	<ul class="nav nav-tabs" role="tablist">
		    <li role="presentation" class="active"><a href="#reallocate_room_tab" aria-controls="reallocate" role="tab" data-toggle="tab"><?php echo smartyTranslate(array('s'=>'Room Reallocation','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
</a></li>
		    <li role="presentation"><a href="#swap_room_tab" aria-controls="swap" role="tab" data-toggle="tab"><?php echo smartyTranslate(array('s'=>'Swap Room','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
</a></li>
		 </ul>
		<div class="tab-content panel active">
			<div role="tabpanel" class="tab-pane active" id="reallocate_room_tab">
				<form method="post" action="">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="realloc_myModalLabel"><?php echo smartyTranslate(array('s'=>'Reallocate Rooms'),$_smarty_tpl);?>
</h4>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<label for="curr_room_num" class="control-label"><?php echo smartyTranslate(array('s'=>'Current Room Number:','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
</label>
							<input type="text" class="form-control modal_curr_room_num" name="modal_curr_room_num" readonly="true">
							<input type="hidden" class="form-control modal_date_from" name="modal_date_from">
							<input type="hidden" class="form-control modal_date_to" name="modal_date_to">
							<input type="hidden" class="form-control modal_id_room" name="modal_id_room">
						</div>
						<div class="form-group">
							<label for="realloc_avail_rooms" class="control-label"><?php echo smartyTranslate(array('s'=>'Available Rooms To Reallocate:','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
</label>
							<div style="width: 195px;">
								<select class="form-control" name="realloc_avail_rooms" id="realloc_avail_rooms">
									<option value="0" selected="selected"><?php echo smartyTranslate(array('s'=>'Select Rooms','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
</option>
								</select>
								<p class="error_text" id="realloc_sel_rm_err_p"></p>
							</div>
						</div>
						<div class="form-group">
							<label style="text-decoration:underline;margin-top:5px;" for="message-text" class="col-sm-12 control-label"><i class="icon-info-circle"></i>&nbsp;<?php echo smartyTranslate(array('s'=>'Currently Alloted Customer Information:','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
</label>
							<dl class="well list-detail">
								<dt><?php echo smartyTranslate(array('s'=>'Name'),$_smarty_tpl);?>
</dt>
								<dd class="cust_name"></dd><br>
								<dt><?php echo smartyTranslate(array('s'=>'Email'),$_smarty_tpl);?>
</dt>
								<dd class="cust_email"></dd><br>
							</dl>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo smartyTranslate(array('s'=>"Close",'mod'=>"hotelreservationsyatem"),$_smarty_tpl);?>
</button>
						<input type="submit" id="realloc_allocated_rooms" name="realloc_allocated_rooms" class="btn btn-primary" value="Reallocate">
					</div>
				</form>
			</div>
			<div role="tabpanel" class="tab-pane" id="swap_room_tab">
				<form method="post" action="">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="swap_myModalLabel"><?php echo smartyTranslate(array('s'=>'Swap Rooms'),$_smarty_tpl);?>
</h4>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<label for="swap_curr_room_num" class="control-label"><?php echo smartyTranslate(array('s'=>'Current Room Number:','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
</label>
							<input type="text" class="form-control modal_curr_room_num" name="modal_curr_room_num" readonly="true">
							<input type="hidden" class="form-control modal_date_from" name="modal_date_from">
							<input type="hidden" class="form-control modal_date_to" name="modal_date_to">
							<input type="hidden" class="form-control modal_id_room" name="modal_id_room">
							<input type="hidden" class="form-control modal_id_order" name="modal_id_order">
						</div>
						<div class="form-group">
							<label for="swap_avail_rooms" class="control-label"><?php echo smartyTranslate(array('s'=>'Available Rooms To Swap:','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
</label>
							<div style="width: 195px;">
								<select class="form-control" name="swap_avail_rooms" id="swap_avail_rooms">
									<option value="0" selected="selected"><?php echo smartyTranslate(array('s'=>'Select Rooms','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
</option>
								</select>
								<p class="error_text" id="swap_sel_rm_err_p"></p>
							</div>
						</div>
						<div class="form-group">
							<label style="text-decoration:underline;margin-top:5px;" for="message-text" class="col-sm-12 control-label"><i class="icon-info-circle"></i>&nbsp;<?php echo smartyTranslate(array('s'=>'Currently Alloted Customer Information:','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
</label>
							<dl class="well list-detail">
								<dt><?php echo smartyTranslate(array('s'=>'Name'),$_smarty_tpl);?>
</dt>
								<dd class="cust_name"></dd><br>
								<dt><?php echo smartyTranslate(array('s'=>'Email'),$_smarty_tpl);?>
</dt>
								<dd class="cust_email"></dd><br>
							</dl>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo smartyTranslate(array('s'=>"Close",'mod'=>"hotelreservationsyatem"),$_smarty_tpl);?>
</button>
						<input type="submit" id="swap_allocated_rooms" name="swap_allocated_rooms" class="btn btn-primary" value="Swap">
					</div>
				</form>
			</div>
		</div>
    </div>
  </div>
</div>
<?php $_smarty_tpl->smarty->_tag_stack[] = array('addJsDefL', array('name'=>'no_rm_avail_txt')); $_block_repeat=true; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'no_rm_avail_txt'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smartyTranslate(array('s'=>'No rooms available.','js'=>1,'mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'no_rm_avail_txt'), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>
<?php $_smarty_tpl->smarty->_tag_stack[] = array('addJsDefL', array('name'=>'slct_rm_err')); $_block_repeat=true; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'slct_rm_err'), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
<?php echo smartyTranslate(array('s'=>'Please select a room first.','js'=>1,'mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo $_smarty_tpl->smarty->registered_plugins['block']['addJsDefL'][0][0]->addJsDefL(array('name'=>'slct_rm_err'), $_block_content, $_smarty_tpl, $_block_repeat); } array_pop($_smarty_tpl->smarty->_tag_stack);?>

	<script type="text/javascript">
		var geocoder = new google.maps.Geocoder();
		var delivery_map, invoice_map;

		$(document).ready(function()
		{
			/*By webkul for swapping Booked rooms modal js*/
		    $("#swap_allocated_rooms").on('click', function(e){
		        $(".error_text").text('');
		        if ($('#swapped_avail_rooms').val() == 0)
		        {
		            $("#sel_rm_err_p").text(slct_rm_err);
		            return false;
		        }
		    });

		    $('#mySwappigModal').on('hidden.bs.modal', function (e)
		    {
		    	$(".modal_id_order").val('');
		        $(".modal_date_from").val('');
		        $(".modal_date_to").val('');
		        $(".modal_id_room").val('');
		        $(".modal_curr_room_num").val('');
		        $(".cust_name").text('');
		        $(".cust_email").text('');
		        $(".swp_rm_opts").remove();
		        $(".realloc_rm_opts").remove();
		    });

		    $('#mySwappigModal').on('shown.bs.modal', function (e)
		    {
		    	$(".modal_id_order").val(e.relatedTarget.dataset.id_order);
		        $(".modal_date_from").val(e.relatedTarget.dataset.date_from);
		        $(".modal_date_to").val(e.relatedTarget.dataset.date_to);
		        $(".modal_id_room").val(e.relatedTarget.dataset.id_room);
		        $(".modal_curr_room_num").val(e.relatedTarget.dataset.room_num);
		        $(".cust_name").text(e.relatedTarget.dataset.cust_name);
		        $(".cust_email").text(e.relatedTarget.dataset.cust_email);
		        html = '';
		        var json_arr_rm_swp = JSON.parse(e.relatedTarget.dataset.avail_rm_swap);
		        $.each(json_arr_rm_swp, function(key,val)
		        {
		            html += '<option class="swp_rm_opts" value="'+val.id_room+'" >'+val.room_num+'</option>';
		        });
		        if (html != '')
		            $("#swap_avail_rooms").append(html);

		        html = '';
		        var json_arr_rm_realloc = JSON.parse(e.relatedTarget.dataset.avail_rm_realloc);
		        $.each(json_arr_rm_realloc, function(key,val)
		        {
		            html += '<option class="realloc_rm_opts" value="'+val.id_room+'" >'+val.room_num+'</option>';
		        });
		        if (html != '')
		            $("#realloc_avail_rooms").append(html);
		    });
			
			/*For swaping rooms in the modal*/
		    $("#realloc_allocated_rooms").on('click', function(e){
		        $(".error_text").text('');
		        if ($('#realloc_avail_rooms').val() == 0)
		        {
		            $("#realloc_sel_rm_err_p").text(slct_rm_err);
		            return false;
		        }
		    });
		    $("#swap_allocated_rooms").on('click', function(e){
		        $(".error_text").text('');
		        if ($('#swap_avail_rooms').val() == 0)
		        {
		            $("#swap_sel_rm_err_p").text(slct_rm_err);
		            return false;
		        }
		    });	
		
		    /*END*/

			$(".textarea-autosize").autosize();

			geocoder.geocode({
				address: '<?php echo addcslashes($_smarty_tpl->tpl_vars['addresses']->value['delivery']->address1,'\'');?>
,<?php echo addcslashes($_smarty_tpl->tpl_vars['addresses']->value['delivery']->postcode,'\'');?>
,<?php echo addcslashes($_smarty_tpl->tpl_vars['addresses']->value['delivery']->city,'\'');?>
<?php if (isset($_smarty_tpl->tpl_vars['addresses']->value['deliveryState']->name)&&$_smarty_tpl->tpl_vars['addresses']->value['delivery']->id_state) {?>,<?php echo addcslashes($_smarty_tpl->tpl_vars['addresses']->value['deliveryState']->name,'\'');?>
<?php }?>,<?php echo addcslashes($_smarty_tpl->tpl_vars['addresses']->value['delivery']->country,'\'');?>
'
				}, function(results, status) {
				if (status === google.maps.GeocoderStatus.OK)
				{
					delivery_map = new google.maps.Map(document.getElementById('map-delivery-canvas'), {
						zoom: 10,
						mapTypeId: google.maps.MapTypeId.ROADMAP,
						center: results[0].geometry.location
					});
					var delivery_marker = new google.maps.Marker({
						map: delivery_map,
						position: results[0].geometry.location,
						url: 'http://maps.google.com?q=<?php echo urlencode($_smarty_tpl->tpl_vars['addresses']->value['delivery']->address1);?>
,<?php echo urlencode($_smarty_tpl->tpl_vars['addresses']->value['delivery']->postcode);?>
,<?php echo urlencode($_smarty_tpl->tpl_vars['addresses']->value['delivery']->city);?>
<?php if (isset($_smarty_tpl->tpl_vars['addresses']->value['deliveryState']->name)&&$_smarty_tpl->tpl_vars['addresses']->value['delivery']->id_state) {?>,<?php echo urlencode($_smarty_tpl->tpl_vars['addresses']->value['deliveryState']->name);?>
<?php }?>,<?php echo urlencode($_smarty_tpl->tpl_vars['addresses']->value['delivery']->country);?>
'
					});
					google.maps.event.addListener(delivery_marker, 'click', function() {
						window.open(delivery_marker.url);
					});
				}
			});

			geocoder.geocode({
				address: '<?php echo addcslashes($_smarty_tpl->tpl_vars['addresses']->value['invoice']->address1,'\'');?>
,<?php echo addcslashes($_smarty_tpl->tpl_vars['addresses']->value['invoice']->postcode,'\'');?>
,<?php echo addcslashes($_smarty_tpl->tpl_vars['addresses']->value['invoice']->city,'\'');?>
<?php if (isset($_smarty_tpl->tpl_vars['addresses']->value['deliveryState']->name)&&$_smarty_tpl->tpl_vars['addresses']->value['invoice']->id_state) {?>,<?php echo addcslashes($_smarty_tpl->tpl_vars['addresses']->value['deliveryState']->name,'\'');?>
<?php }?>,<?php echo addcslashes($_smarty_tpl->tpl_vars['addresses']->value['invoice']->country,'\'');?>
'
				}, function(results, status) {
				if (status === google.maps.GeocoderStatus.OK)
				{
					invoice_map = new google.maps.Map(document.getElementById('map-invoice-canvas'), {
						zoom: 10,
						mapTypeId: google.maps.MapTypeId.ROADMAP,
						center: results[0].geometry.location
					});
					invoice_marker = new google.maps.Marker({
						map: invoice_map,
						position: results[0].geometry.location,
						url: 'http://maps.google.com?q=<?php echo urlencode($_smarty_tpl->tpl_vars['addresses']->value['invoice']->address1);?>
,<?php echo urlencode($_smarty_tpl->tpl_vars['addresses']->value['invoice']->postcode);?>
,<?php echo urlencode($_smarty_tpl->tpl_vars['addresses']->value['invoice']->city);?>
<?php if (isset($_smarty_tpl->tpl_vars['addresses']->value['deliveryState']->name)&&$_smarty_tpl->tpl_vars['addresses']->value['invoice']->id_state) {?>,<?php echo urlencode($_smarty_tpl->tpl_vars['addresses']->value['deliveryState']->name);?>
<?php }?>,<?php echo urlencode($_smarty_tpl->tpl_vars['addresses']->value['invoice']->country);?>
'
					});
					google.maps.event.addListener(invoice_marker, 'click', function() {
						window.open(invoice_marker.url);
					});
				}
			});

			var date = new Date();
			var hours = date.getHours();
			if (hours < 10)
				hours = "0" + hours;
			var mins = date.getMinutes();
			if (mins < 10)
				mins = "0" + mins;
			var secs = date.getSeconds();
			if (secs < 10)
				secs = "0" + secs;

			$('.datepicker').datetimepicker({
				prevText: '',
				nextText: '',
				dateFormat: 'yy-mm-dd ' + hours + ':' + mins + ':' + secs
			});
		});

		// Fix wrong maps center when map is hidden
		$('#tabAddresses').click(function(){
			x = delivery_map.getZoom();
			c = delivery_map.getCenter();
			google.maps.event.trigger(delivery_map, 'resize');
			delivery_map.setZoom(x);
			delivery_map.setCenter(c);

			x = invoice_map.getZoom();
			c = invoice_map.getCenter();
			google.maps.event.trigger(invoice_map, 'resize');
			invoice_map.setZoom(x);
			invoice_map.setCenter(c);
		});
	</script>



<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>'displayAdminView'),$_smarty_tpl);?>

<?php if (isset($_smarty_tpl->tpl_vars['name_controller']->value)) {?>
	<?php $_smarty_tpl->_capture_stack[0][] = array('hookName', 'hookName', null); ob_start(); ?>display<?php echo ucfirst($_smarty_tpl->tpl_vars['name_controller']->value);?>
View<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
	<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>$_smarty_tpl->tpl_vars['hookName']->value),$_smarty_tpl);?>

<?php } elseif (isset($_GET['controller'])) {?>
	<?php $_smarty_tpl->_capture_stack[0][] = array('hookName', 'hookName', null); ob_start(); ?>display<?php echo htmlentities(ucfirst($_GET['controller']));?>
View<?php list($_capture_buffer, $_capture_assign, $_capture_append) = array_pop($_smarty_tpl->_capture_stack[0]);
if (!empty($_capture_buffer)) {
 if (isset($_capture_assign)) $_smarty_tpl->assign($_capture_assign, ob_get_contents());
 if (isset( $_capture_append)) $_smarty_tpl->append( $_capture_append, ob_get_contents());
 Smarty::$_smarty_vars['capture'][$_capture_buffer]=ob_get_clean();
} else $_smarty_tpl->capture_error();?>
	<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0][0]->smartyHook(array('h'=>$_smarty_tpl->tpl_vars['hookName']->value),$_smarty_tpl);?>

<?php }?>
<?php }} ?>
<?php /* Smarty version Smarty-3.1.19, created on 2016-01-07 11:53:04
         compiled from "/home/sumit/public_html/html/hotel-reservation-system/admin_htl/themes/default/template/controllers/orders/_documents.tpl" */ ?>
<?php if ($_valid && !is_callable('content_568e0448b32fe1_53922290')) {function content_568e0448b32fe1_53922290($_smarty_tpl) {?>
<div class="table-responsive">
	<table class="table" id="documents_table">
		<thead>
			<tr>
				<th>
					<span class="title_box "><?php echo smartyTranslate(array('s'=>'Date'),$_smarty_tpl);?>
</span>
				</th>
				<th>
					<span class="title_box "><?php echo smartyTranslate(array('s'=>'Document'),$_smarty_tpl);?>
</span>
				</th>
				<th>
					<span class="title_box "><?php echo smartyTranslate(array('s'=>'Number'),$_smarty_tpl);?>
</span>
				</th>
				<th>
					<span class="title_box "><?php echo smartyTranslate(array('s'=>'Amount'),$_smarty_tpl);?>
</span>
				</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php  $_smarty_tpl->tpl_vars['document'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['document']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['order']->value->getDocuments(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['document']->key => $_smarty_tpl->tpl_vars['document']->value) {
$_smarty_tpl->tpl_vars['document']->_loop = true;
?>

				<?php if (get_class($_smarty_tpl->tpl_vars['document']->value)=='OrderInvoice') {?>
					<?php if (isset($_smarty_tpl->tpl_vars['document']->value->is_delivery)) {?>
					<tr id="delivery_<?php echo $_smarty_tpl->tpl_vars['document']->value->id;?>
">
					<?php } else { ?>
					<tr id="invoice_<?php echo $_smarty_tpl->tpl_vars['document']->value->id;?>
">
					<?php }?>
				<?php } elseif (get_class($_smarty_tpl->tpl_vars['document']->value)=='OrderSlip') {?>
					<tr id="orderslip_<?php echo $_smarty_tpl->tpl_vars['document']->value->id;?>
">
				<?php }?>

						<td><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['dateFormat'][0][0]->dateFormat(array('date'=>$_smarty_tpl->tpl_vars['document']->value->date_add),$_smarty_tpl);?>
</td>
						<td>
							<?php if (get_class($_smarty_tpl->tpl_vars['document']->value)=='OrderInvoice') {?>
								<?php if (isset($_smarty_tpl->tpl_vars['document']->value->is_delivery)) {?>
									<?php echo smartyTranslate(array('s'=>'Delivery slip'),$_smarty_tpl);?>

								<?php } else { ?>
									<?php echo smartyTranslate(array('s'=>'Invoice'),$_smarty_tpl);?>

								<?php }?>
							<?php } elseif (get_class($_smarty_tpl->tpl_vars['document']->value)=='OrderSlip') {?>
								<?php echo smartyTranslate(array('s'=>'Credit Slip'),$_smarty_tpl);?>

							<?php }?>
						</td>
						<td>
							<?php if (get_class($_smarty_tpl->tpl_vars['document']->value)=='OrderInvoice') {?>
								<?php if (isset($_smarty_tpl->tpl_vars['document']->value->is_delivery)) {?>
									<a class="_blank" title="<?php echo smartyTranslate(array('s'=>'See the document'),$_smarty_tpl);?>
" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminPdf'), ENT_QUOTES, 'UTF-8', true);?>
&amp;submitAction=generateDeliverySlipPDF&amp;id_order_invoice=<?php echo $_smarty_tpl->tpl_vars['document']->value->id;?>
">
								<?php } else { ?>
									<a class="_blank" title="<?php echo smartyTranslate(array('s'=>'See the document'),$_smarty_tpl);?>
" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminPdf'), ENT_QUOTES, 'UTF-8', true);?>
&amp;submitAction=generateInvoicePDF&amp;id_order_invoice=<?php echo $_smarty_tpl->tpl_vars['document']->value->id;?>
">
							   <?php }?>
							<?php } elseif (get_class($_smarty_tpl->tpl_vars['document']->value)=='OrderSlip') {?>
								<a class="_blank" title="<?php echo smartyTranslate(array('s'=>'See the document'),$_smarty_tpl);?>
" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminPdf'), ENT_QUOTES, 'UTF-8', true);?>
&amp;submitAction=generateOrderSlipPDF&amp;id_order_slip=<?php echo $_smarty_tpl->tpl_vars['document']->value->id;?>
">
							<?php }?>
							<?php if (get_class($_smarty_tpl->tpl_vars['document']->value)=='OrderInvoice') {?>
								<?php if (isset($_smarty_tpl->tpl_vars['document']->value->is_delivery)) {?>
									<?php echo Configuration::get('PS_DELIVERY_PREFIX',$_smarty_tpl->tpl_vars['current_id_lang']->value,null,$_smarty_tpl->tpl_vars['order']->value->id_shop);?>
<?php echo sprintf('%06d',$_smarty_tpl->tpl_vars['document']->value->delivery_number);?>

								<?php } else { ?>
									<?php echo $_smarty_tpl->tpl_vars['document']->value->getInvoiceNumberFormatted($_smarty_tpl->tpl_vars['current_id_lang']->value,$_smarty_tpl->tpl_vars['order']->value->id_shop);?>

								<?php }?>
							<?php } elseif (get_class($_smarty_tpl->tpl_vars['document']->value)=='OrderSlip') {?>
								<?php echo Configuration::get('PS_CREDIT_SLIP_PREFIX',$_smarty_tpl->tpl_vars['current_id_lang']->value);?>
<?php echo sprintf('%06d',$_smarty_tpl->tpl_vars['document']->value->id);?>

							<?php }?>
							</a>
						</td>
						<td>
						<?php if (get_class($_smarty_tpl->tpl_vars['document']->value)=='OrderInvoice') {?>
							<?php if (isset($_smarty_tpl->tpl_vars['document']->value->is_delivery)) {?>
								--
							<?php } else { ?>
								<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['document']->value->total_paid_tax_incl,'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>
&nbsp;
								<?php if ($_smarty_tpl->tpl_vars['document']->value->getTotalPaid()) {?>
									<span>
									<?php if ($_smarty_tpl->tpl_vars['document']->value->getRestPaid()>0) {?>
										(<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['document']->value->getRestPaid(),'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>
 <?php echo smartyTranslate(array('s'=>'not paid'),$_smarty_tpl);?>
)
									<?php } elseif ($_smarty_tpl->tpl_vars['document']->value->getRestPaid()<0) {?>
										(<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>-$_smarty_tpl->tpl_vars['document']->value->getRestPaid(),'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>
 <?php echo smartyTranslate(array('s'=>'overpaid'),$_smarty_tpl);?>
)
									<?php }?>
									</span>
								<?php }?>
							<?php }?>
						<?php } elseif (get_class($_smarty_tpl->tpl_vars['document']->value)=='OrderSlip') {?>
							<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['document']->value->amount+$_smarty_tpl->tpl_vars['document']->value->shipping_cost_amount,'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>

						<?php }?>
						</td>
						<td class="text-right document_action">
						<?php if (get_class($_smarty_tpl->tpl_vars['document']->value)=='OrderInvoice') {?>
							<?php if (!isset($_smarty_tpl->tpl_vars['document']->value->is_delivery)) {?>

								<?php if ($_smarty_tpl->tpl_vars['document']->value->getRestPaid()) {?>
									<a href="#formAddPaymentPanel" class="js-set-payment btn btn-default anchor" data-amount="<?php echo $_smarty_tpl->tpl_vars['document']->value->getRestPaid();?>
" data-id-invoice="<?php echo $_smarty_tpl->tpl_vars['document']->value->id;?>
" title="<?php echo smartyTranslate(array('s'=>'Set payment form'),$_smarty_tpl);?>
">
										<i class="icon-money"></i>
										<?php echo smartyTranslate(array('s'=>'Enter payment'),$_smarty_tpl);?>

									</a>
								<?php }?>

								<a href="#" class="btn btn-default" onclick="$('#invoiceNote<?php echo $_smarty_tpl->tpl_vars['document']->value->id;?>
').show(); return false;" title="<?php if ($_smarty_tpl->tpl_vars['document']->value->note=='') {?><?php echo smartyTranslate(array('s'=>'Add note'),$_smarty_tpl);?>
<?php } else { ?><?php echo smartyTranslate(array('s'=>'Edit note'),$_smarty_tpl);?>
<?php }?>">
									<?php if ($_smarty_tpl->tpl_vars['document']->value->note=='') {?>
										<i class="icon-plus-sign-alt"></i>
										<?php echo smartyTranslate(array('s'=>'Add note'),$_smarty_tpl);?>

									<?php } else { ?>
										<i class="icon-pencil"></i>
										<?php echo smartyTranslate(array('s'=>'Edit note'),$_smarty_tpl);?>

									<?php }?>
								</a>

							<?php }?>
						<?php }?>
						</td>
					</tr>
				<?php if (get_class($_smarty_tpl->tpl_vars['document']->value)=='OrderInvoice') {?>
					<?php if (!isset($_smarty_tpl->tpl_vars['document']->value->is_delivery)) {?>
					<tr id="invoiceNote<?php echo $_smarty_tpl->tpl_vars['document']->value->id;?>
" style="display:none">
						<td colspan="5">
							<form action="<?php echo $_smarty_tpl->tpl_vars['current_index']->value;?>
&amp;viewOrder&amp;id_order=<?php echo $_smarty_tpl->tpl_vars['order']->value->id;?>
<?php if (isset($_GET['token'])) {?>&amp;token=<?php echo htmlspecialchars($_GET['token'], ENT_QUOTES, 'UTF-8', true);?>
<?php }?>" method="post">
								<p>
									<label for="editNote<?php echo $_smarty_tpl->tpl_vars['document']->value->id;?>
" class="t"><?php echo smartyTranslate(array('s'=>'Note'),$_smarty_tpl);?>
</label>
									<input type="hidden" name="id_order_invoice" value="<?php echo $_smarty_tpl->tpl_vars['document']->value->id;?>
" />
									<textarea name="note" id="editNote<?php echo $_smarty_tpl->tpl_vars['document']->value->id;?>
" class="edit-note textarea-autosize"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['document']->value->note, ENT_QUOTES, 'UTF-8', true);?>
</textarea>
								</p>
								<p>
									<button type="submit" name="submitEditNote" class="btn btn-default">
										<i class="icon-save"></i>
										<?php echo smartyTranslate(array('s'=>'Save'),$_smarty_tpl);?>

									</button>
									<a class="btn btn-default" href="#" id="cancelNote" onclick="$('#invoiceNote<?php echo $_smarty_tpl->tpl_vars['document']->value->id;?>
').hide();return false;">
										<i class="icon-remove"></i>
										<?php echo smartyTranslate(array('s'=>'Cancel'),$_smarty_tpl);?>

									</a>
								</p>
							</form>
						</td>
					</tr>
					<?php }?>
				<?php }?>
			<?php }
if (!$_smarty_tpl->tpl_vars['document']->_loop) {
?>
				<tr>
					<td colspan="5" class="list-empty">
						<div class="list-empty-msg">
							<i class="icon-warning-sign list-empty-icon"></i>
							<?php echo smartyTranslate(array('s'=>'There is no available document'),$_smarty_tpl);?>

						</div>
						<?php if (isset($_smarty_tpl->tpl_vars['invoice_management_active']->value)&&$_smarty_tpl->tpl_vars['invoice_management_active']->value) {?>
							<a class="btn btn-default" href="<?php echo $_smarty_tpl->tpl_vars['current_index']->value;?>
&amp;viewOrder&amp;submitGenerateInvoice&amp;id_order=<?php echo $_smarty_tpl->tpl_vars['order']->value->id;?>
<?php if (isset($_GET['token'])) {?>&amp;token=<?php echo htmlspecialchars($_GET['token'], ENT_QUOTES, 'UTF-8', true);?>
<?php }?>">
								<i class="icon-repeat"></i>
								<?php echo smartyTranslate(array('s'=>'Generate invoice'),$_smarty_tpl);?>

							</a>
						<?php }?>
					</td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
</div>
<?php }} ?>
<?php /* Smarty version Smarty-3.1.19, created on 2016-01-07 11:53:04
         compiled from "/home/sumit/public_html/html/hotel-reservation-system/admin_htl/themes/default/template/controllers/orders/_shipping.tpl" */ ?>
<?php if ($_valid && !is_callable('content_568e0448c6c3c6_88819540')) {function content_568e0448c6c3c6_88819540($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_replace')) include '/home/sumit/public_html/html/hotel-reservation-system/tools/smarty/plugins/modifier.replace.php';
?>
<div class="table-responsive">
	<table class="table" id="shipping_table">
		<thead>
			<tr>
				<th>
					<span class="title_box "><?php echo smartyTranslate(array('s'=>'Date'),$_smarty_tpl);?>
</span>
				</th>
				<th>
					<span class="title_box ">&nbsp;</span>
				</th>
				<th>
					<span class="title_box "><?php echo smartyTranslate(array('s'=>'Carrier'),$_smarty_tpl);?>
</span>
				</th>
				<th>
					<span class="title_box "><?php echo smartyTranslate(array('s'=>'Weight'),$_smarty_tpl);?>
</span>
				</th>
				<th>
					<span class="title_box "><?php echo smartyTranslate(array('s'=>'Shipping cost'),$_smarty_tpl);?>
</span>
				</th>
				<th>
					<span class="title_box "><?php echo smartyTranslate(array('s'=>'Tracking number'),$_smarty_tpl);?>
</span>
				</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			<?php  $_smarty_tpl->tpl_vars['line'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['line']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['order']->value->getShipping(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['line']->key => $_smarty_tpl->tpl_vars['line']->value) {
$_smarty_tpl->tpl_vars['line']->_loop = true;
?>
			<tr>
				<td><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['dateFormat'][0][0]->dateFormat(array('date'=>$_smarty_tpl->tpl_vars['line']->value['date_add'],'full'=>true),$_smarty_tpl);?>
</td>
				<td>&nbsp;</td>
				<td><?php echo $_smarty_tpl->tpl_vars['line']->value['carrier_name'];?>
</td>
				<td class="weight"><?php echo sprintf("%.3f",$_smarty_tpl->tpl_vars['line']->value['weight']);?>
 <?php echo Configuration::get('PS_WEIGHT_UNIT');?>
</td>
				<td class="center">
					<?php if ($_smarty_tpl->tpl_vars['order']->value->getTaxCalculationMethod()==@constant('PS_TAX_INC')) {?>
						<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['line']->value['shipping_cost_tax_incl'],'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>

					<?php } else { ?>
						<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['line']->value['shipping_cost_tax_excl'],'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>

					<?php }?>
				</td>
				<td>
					<span class="shipping_number_show"><?php if ($_smarty_tpl->tpl_vars['line']->value['url']&&$_smarty_tpl->tpl_vars['line']->value['tracking_number']) {?><a class="_blank" href="<?php echo smarty_modifier_replace($_smarty_tpl->tpl_vars['line']->value['url'],'@',$_smarty_tpl->tpl_vars['line']->value['tracking_number']);?>
"><?php echo $_smarty_tpl->tpl_vars['line']->value['tracking_number'];?>
</a><?php } else { ?><?php echo $_smarty_tpl->tpl_vars['line']->value['tracking_number'];?>
<?php }?></span>
				</td>
				<td>
					<?php if ($_smarty_tpl->tpl_vars['line']->value['can_edit']) {?>
						<form method="post" action="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminOrders'), ENT_QUOTES, 'UTF-8', true);?>
&amp;vieworder&amp;id_order=<?php echo intval($_smarty_tpl->tpl_vars['order']->value->id);?>
">
							<span class="shipping_number_edit" style="display:none;">
								<input type="hidden" name="id_order_carrier" value="<?php echo htmlentities($_smarty_tpl->tpl_vars['line']->value['id_order_carrier']);?>
" />
								<input type="text" name="tracking_number" value="<?php echo htmlentities($_smarty_tpl->tpl_vars['line']->value['tracking_number']);?>
" />
								<button type="submit" class="btn btn-default" name="submitShippingNumber">
									<i class="icon-ok"></i>
									<?php echo smartyTranslate(array('s'=>'Update'),$_smarty_tpl);?>

								</button>
							</span>
							<a href="#" class="edit_shipping_number_link btn btn-default">
								<i class="icon-pencil"></i>
								<?php echo smartyTranslate(array('s'=>'Edit'),$_smarty_tpl);?>

							</a>
							<a href="#" class="cancel_shipping_number_link btn btn-default" style="display: none;">
								<i class="icon-remove"></i>
								<?php echo smartyTranslate(array('s'=>'Cancel'),$_smarty_tpl);?>

							</a>
						</form>
					<?php }?>
				</td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
</div>
<?php }} ?>
<?php /* Smarty version Smarty-3.1.19, created on 2016-01-07 11:53:05
         compiled from "/home/sumit/public_html/html/hotel-reservation-system/admin_htl/themes/default/template/controllers/orders/_customized_data.tpl" */ ?>
<?php if ($_valid && !is_callable('content_568e044911e471_08201355')) {function content_568e044911e471_08201355($_smarty_tpl) {?>
<?php if ($_smarty_tpl->tpl_vars['product']->value['customizedDatas']) {?>

<?php if (($_smarty_tpl->tpl_vars['order']->value->getTaxCalculationMethod()==@constant('PS_TAX_EXC'))) {?>
	<?php $_smarty_tpl->tpl_vars['product_price'] = new Smarty_variable(($_smarty_tpl->tpl_vars['product']->value['unit_price_tax_excl']+$_smarty_tpl->tpl_vars['product']->value['ecotax']), null, 0);?>
<?php } else { ?>
	<?php $_smarty_tpl->tpl_vars['product_price'] = new Smarty_variable($_smarty_tpl->tpl_vars['product']->value['unit_price_tax_incl'], null, 0);?>
<?php }?>
	<tr class="customized customized-<?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_order_detail']);?>
 product-line-row">
		<td>
			<input type="hidden" class="edit_product_id_order_detail" value="<?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_order_detail']);?>
" />
			<?php if (isset($_smarty_tpl->tpl_vars['product']->value['image'])&&intval($_smarty_tpl->tpl_vars['product']->value['image']->id)) {?><?php echo $_smarty_tpl->tpl_vars['product']->value['image_tag'];?>
<?php } else { ?>--<?php }?>
		</td>
		<td>
			<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminProducts'), ENT_QUOTES, 'UTF-8', true);?>
&amp;id_product=<?php echo intval($_smarty_tpl->tpl_vars['product']->value['product_id']);?>
&amp;updateproduct&amp;token=<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminProducts'),$_smarty_tpl);?>
">
			<span class="productName"><?php echo $_smarty_tpl->tpl_vars['product']->value['product_name'];?>
 - <?php echo smartyTranslate(array('s'=>'Customized'),$_smarty_tpl);?>
</span><br />
			<?php if (($_smarty_tpl->tpl_vars['product']->value['product_reference'])) {?><?php echo smartyTranslate(array('s'=>'Reference number:'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['product']->value['product_reference'];?>
<br /><?php }?>
			<?php if (($_smarty_tpl->tpl_vars['product']->value['product_supplier_reference'])) {?><?php echo smartyTranslate(array('s'=>'Supplier reference:'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['product']->value['product_supplier_reference'];?>
<?php }?>
			</a>
		</td>
		<td>
			<span class="product_price_show"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['product_price']->value,'currency'=>intval($_smarty_tpl->tpl_vars['currency']->value->id)),$_smarty_tpl);?>
</span>
			<?php if ($_smarty_tpl->tpl_vars['can_edit']->value) {?>
			<div class="product_price_edit" style="display:none;">
				<input type="hidden" name="product_id_order_detail" class="edit_product_id_order_detail" value="<?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_order_detail']);?>
" />
				<div class="form-group">
					<div class="fixed-width-xl">
						<div class="input-group">
							<?php if ($_smarty_tpl->tpl_vars['currency']->value->format%2) {?><div class="input-group-addon"><?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
 <?php echo smartyTranslate(array('s'=>'tax excl.'),$_smarty_tpl);?>
</div><?php }?>
							<input type="text" name="product_price_tax_excl" class="edit_product_price_tax_excl edit_product_price" value="<?php echo Tools::ps_round($_smarty_tpl->tpl_vars['product']->value['unit_price_tax_excl'],2);?>
" size="5" />
							<?php if (!($_smarty_tpl->tpl_vars['currency']->value->format%2)) {?><div class="input-group-addon"><?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
 <?php echo smartyTranslate(array('s'=>'tax excl.'),$_smarty_tpl);?>
</div><?php }?>
						</div>
					</div>
					<br/>
					<div class="fixed-width-xl">
						<div class="input-group">
							<?php if ($_smarty_tpl->tpl_vars['currency']->value->format%2) {?><div class="input-group-addon"><?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
 <?php echo smartyTranslate(array('s'=>'tax incl.'),$_smarty_tpl);?>
</div><?php }?>
							<input type="text" name="product_price_tax_incl" class="edit_product_price_tax_incl edit_product_price" value="<?php echo Tools::ps_round($_smarty_tpl->tpl_vars['product']->value['unit_price_tax_incl'],2);?>
" size="5" />
							<?php if (!($_smarty_tpl->tpl_vars['currency']->value->format%2)) {?><div class="input-group-addon"><?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
 <?php echo smartyTranslate(array('s'=>'tax incl.'),$_smarty_tpl);?>
</div><?php }?>
						</div>
					</div>
				</div>
			</div>
			<?php }?>
		</td>
		<td class="productQuantity text-center"><?php echo $_smarty_tpl->tpl_vars['product']->value['customizationQuantityTotal'];?>
</td>
		<?php if ($_smarty_tpl->tpl_vars['display_warehouse']->value) {?><td>&nbsp;</td><?php }?>
		<?php if (($_smarty_tpl->tpl_vars['order']->value->hasBeenPaid())) {?><td class="productQuantity text-center"><?php echo $_smarty_tpl->tpl_vars['product']->value['customizationQuantityRefunded'];?>
</td><?php }?>
		<?php if (($_smarty_tpl->tpl_vars['order']->value->hasBeenDelivered()||$_smarty_tpl->tpl_vars['order']->value->hasProductReturned())) {?><td class="productQuantity text-center"><?php echo $_smarty_tpl->tpl_vars['product']->value['customizationQuantityReturned'];?>
</td><?php }?>
		<?php if ($_smarty_tpl->tpl_vars['stock_management']->value) {?><td class="text-center"><?php echo $_smarty_tpl->tpl_vars['product']->value['current_stock'];?>
</td><?php }?>
		<td class="total_product">
		<?php if (($_smarty_tpl->tpl_vars['order']->value->getTaxCalculationMethod()==@constant('PS_TAX_EXC'))) {?>
			<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>Tools::ps_round($_smarty_tpl->tpl_vars['product']->value['product_price']*$_smarty_tpl->tpl_vars['product']->value['customizationQuantityTotal'],2),'currency'=>intval($_smarty_tpl->tpl_vars['currency']->value->id)),$_smarty_tpl);?>

		<?php } else { ?>
			<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>Tools::ps_round($_smarty_tpl->tpl_vars['product']->value['product_price_wt']*$_smarty_tpl->tpl_vars['product']->value['customizationQuantityTotal'],2),'currency'=>intval($_smarty_tpl->tpl_vars['currency']->value->id)),$_smarty_tpl);?>

		<?php }?>
		</td>
		<td class="cancelQuantity standard_refund_fields current-edit" style="display:none" colspan="2">
			&nbsp;
		</td>
		<td class="edit_product_fields" colspan="2" style="display:none">&nbsp;</td>
		<td class="partial_refund_fields current-edit" style="text-align:left;display:none;"></td>
		<?php if (($_smarty_tpl->tpl_vars['can_edit']->value&&!$_smarty_tpl->tpl_vars['order']->value->hasBeenDelivered())) {?>
			<td class="product_action text-right">
				
				<div class="btn-group">
					<button type="button" class="btn btn-default edit_product_change_link">
						<i class="icon-pencil"></i>
						<?php echo smartyTranslate(array('s'=>'Edit'),$_smarty_tpl);?>

					</button>
					<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
						<span class="caret"></span>
					</button>
					<ul class="dropdown-menu" role="menu">
						<li>
							<a href="#" class="delete_product_line">
								<i class="icon-trash"></i>
								<?php echo smartyTranslate(array('s'=>'Delete'),$_smarty_tpl);?>

							</a>
						</li>
					</ul>
				</div>
				
				<button type="button" class="btn btn-default submitProductChange" style="display: none;">
					<i class="icon-ok"></i>
					<?php echo smartyTranslate(array('s'=>'Update'),$_smarty_tpl);?>

				</button>
				<button type="button" class="btn btn-default cancel_product_change_link" style="display: none;">
					<i class="icon-remove"></i>
					<?php echo smartyTranslate(array('s'=>'Cancel'),$_smarty_tpl);?>

				</button>
			</td>
		<?php }?>
	</tr>
	<?php  $_smarty_tpl->tpl_vars['customizationPerAddress'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['customizationPerAddress']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['product']->value['customizedDatas']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['customizationPerAddress']->key => $_smarty_tpl->tpl_vars['customizationPerAddress']->value) {
$_smarty_tpl->tpl_vars['customizationPerAddress']->_loop = true;
?>
		<?php  $_smarty_tpl->tpl_vars['customization'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['customization']->_loop = false;
 $_smarty_tpl->tpl_vars['customizationId'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['customizationPerAddress']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['customization']->key => $_smarty_tpl->tpl_vars['customization']->value) {
$_smarty_tpl->tpl_vars['customization']->_loop = true;
 $_smarty_tpl->tpl_vars['customizationId']->value = $_smarty_tpl->tpl_vars['customization']->key;
?>
			<tr class="customized customized-<?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_order_detail']);?>
">
				<td colspan="2">
				<input type="hidden" class="edit_product_id_order_detail" value="<?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_order_detail']);?>
" />
					<div class="form-horizontal">
						<?php  $_smarty_tpl->tpl_vars['datas'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['datas']->_loop = false;
 $_smarty_tpl->tpl_vars['type'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['customization']->value['datas']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['datas']->key => $_smarty_tpl->tpl_vars['datas']->value) {
$_smarty_tpl->tpl_vars['datas']->_loop = true;
 $_smarty_tpl->tpl_vars['type']->value = $_smarty_tpl->tpl_vars['datas']->key;
?>
							<?php if (($_smarty_tpl->tpl_vars['type']->value==Product::CUSTOMIZE_FILE)) {?>
								<?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['data']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['datas']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['data']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value) {
$_smarty_tpl->tpl_vars['data']->_loop = true;
 $_smarty_tpl->tpl_vars['data']->iteration++;
?>
									<div class="form-group">
										<span class="col-lg-4 control-label"><strong><?php if ($_smarty_tpl->tpl_vars['data']->value['name']) {?><?php echo $_smarty_tpl->tpl_vars['data']->value['name'];?>
<?php } else { ?><?php echo smartyTranslate(array('s'=>'Picture #'),$_smarty_tpl);?>
<?php echo $_smarty_tpl->tpl_vars['data']->iteration;?>
<?php }?></strong></span>
										<div class="col-lg-8">
											<a href="displayImage.php?img=<?php echo $_smarty_tpl->tpl_vars['data']->value['value'];?>
&amp;name=<?php echo intval($_smarty_tpl->tpl_vars['order']->value->id);?>
-file<?php echo $_smarty_tpl->tpl_vars['data']->iteration;?>
" class="_blank">
												<img class="img-thumbnail" src="<?php echo @constant('_THEME_PROD_PIC_DIR_');?>
<?php echo $_smarty_tpl->tpl_vars['data']->value['value'];?>
_small" alt=""/>
											</a>
										</div>
									</div>
								<?php } ?>
							<?php } elseif (($_smarty_tpl->tpl_vars['type']->value==Product::CUSTOMIZE_TEXTFIELD)) {?>
								<?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['data']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['datas']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['data']->iteration=0;
foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value) {
$_smarty_tpl->tpl_vars['data']->_loop = true;
 $_smarty_tpl->tpl_vars['data']->iteration++;
?>
									<div class="form-group">
										<span class="col-lg-4 control-label"><strong><?php if ($_smarty_tpl->tpl_vars['data']->value['name']) {?><?php echo smartyTranslate(array('s'=>'%s','sprintf'=>$_smarty_tpl->tpl_vars['data']->value['name']),$_smarty_tpl);?>
<?php } else { ?><?php echo smartyTranslate(array('s'=>'Text #%s','sprintf'=>$_smarty_tpl->tpl_vars['data']->iteration),$_smarty_tpl);?>
<?php }?></strong></span>
										<div class="col-lg-8">
											<p class="form-control-static"><?php echo $_smarty_tpl->tpl_vars['data']->value['value'];?>
</p>
										</div>
									</div>
								<?php } ?>
							<?php }?>
						<?php } ?>
					</div>
				</td>
				<td>-</td>
				<td class="productQuantity text-center">
					<span class="product_quantity_show<?php if ((int)$_smarty_tpl->tpl_vars['customization']->value['quantity']>1) {?> red bold<?php }?>"><?php echo $_smarty_tpl->tpl_vars['customization']->value['quantity'];?>
</span>
					<?php if ($_smarty_tpl->tpl_vars['can_edit']->value) {?>
					<span class="product_quantity_edit" style="display:none;">
						<input type="text" name="product_quantity[<?php echo intval($_smarty_tpl->tpl_vars['customizationId']->value);?>
]" class="edit_product_quantity" value="<?php echo htmlentities($_smarty_tpl->tpl_vars['customization']->value['quantity']);?>
" size="2" />
					</span>
					<?php }?>
				</td>
				<?php if ($_smarty_tpl->tpl_vars['display_warehouse']->value) {?><td>&nbsp;</td><?php }?>
				<?php if (($_smarty_tpl->tpl_vars['order']->value->hasBeenPaid())) {?>
				<td class="text-center">
					<?php if (!empty($_smarty_tpl->tpl_vars['product']->value['amount_refund'])) {?>
					<?php echo smartyTranslate(array('s'=>'%s (%s refund)','sprintf'=>array($_smarty_tpl->tpl_vars['customization']->value['quantity_refunded'],$_smarty_tpl->tpl_vars['product']->value['amount_refund'])),$_smarty_tpl);?>

					<?php }?>
					<input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['product']->value['quantity_refundable'];?>
" class="partialRefundProductQuantity" />
					<input type="hidden" value="<?php echo (Tools::ps_round($_smarty_tpl->tpl_vars['product_price']->value,2)*($_smarty_tpl->tpl_vars['product']->value['product_quantity']-$_smarty_tpl->tpl_vars['product']->value['customizationQuantityTotal']));?>
" class="partialRefundProductAmount" />
				</td>
				<?php }?>
				<?php if (($_smarty_tpl->tpl_vars['order']->value->hasBeenDelivered())) {?><td class="text-center"><?php echo $_smarty_tpl->tpl_vars['customization']->value['quantity_returned'];?>
</td><?php }?>
				<td class="text-center">-</td>
				<td class="total_product">
					<?php if (($_smarty_tpl->tpl_vars['order']->value->getTaxCalculationMethod()==@constant('PS_TAX_EXC'))) {?>
						<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>Tools::ps_round($_smarty_tpl->tpl_vars['product']->value['product_price']*$_smarty_tpl->tpl_vars['customization']->value['quantity'],2),'currency'=>intval($_smarty_tpl->tpl_vars['currency']->value->id)),$_smarty_tpl);?>

					<?php } else { ?>
						<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>Tools::ps_round($_smarty_tpl->tpl_vars['product']->value['product_price_wt']*$_smarty_tpl->tpl_vars['customization']->value['quantity'],2),'currency'=>intval($_smarty_tpl->tpl_vars['currency']->value->id)),$_smarty_tpl);?>

					<?php }?>
				</td>
				<td class="cancelCheck standard_refund_fields current-edit" style="display:none">
					<input type="hidden" name="totalQtyReturn" id="totalQtyReturn" value="<?php echo intval($_smarty_tpl->tpl_vars['customization']->value['quantity_returned']);?>
" />
					<input type="hidden" name="totalQty" id="totalQty" value="<?php echo intval($_smarty_tpl->tpl_vars['customization']->value['quantity']);?>
" />
					<input type="hidden" name="productName" id="productName" value="<?php echo $_smarty_tpl->tpl_vars['product']->value['product_name'];?>
" />
					<?php if (((!$_smarty_tpl->tpl_vars['order']->value->hasBeenDelivered()||Configuration::get('PS_ORDER_RETURN'))&&(int)($_smarty_tpl->tpl_vars['customization']->value['quantity_returned'])<(int)($_smarty_tpl->tpl_vars['customization']->value['quantity']))) {?>
						<input type="checkbox" name="id_customization[<?php echo intval($_smarty_tpl->tpl_vars['customizationId']->value);?>
]" id="id_customization[<?php echo intval($_smarty_tpl->tpl_vars['customizationId']->value);?>
]" value="<?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_order_detail']);?>
" onchange="setCancelQuantity(this, <?php echo intval($_smarty_tpl->tpl_vars['customizationId']->value);?>
, <?php echo $_smarty_tpl->tpl_vars['customization']->value['quantity']-$_smarty_tpl->tpl_vars['product']->value['customizationQuantityTotal']-$_smarty_tpl->tpl_vars['product']->value['product_quantity_reinjected'];?>
)" <?php if (($_smarty_tpl->tpl_vars['product']->value['product_quantity_return']+$_smarty_tpl->tpl_vars['product']->value['product_quantity_refunded']>=$_smarty_tpl->tpl_vars['product']->value['product_quantity'])) {?>disabled="disabled" <?php }?>/>
					<?php } else { ?>
					--
				<?php }?>
				</td>
				<td class="cancelQuantity standard_refund_fields current-edit" style="display:none">
				<?php if (($_smarty_tpl->tpl_vars['customization']->value['quantity_returned']+$_smarty_tpl->tpl_vars['customization']->value['quantity_refunded']>=$_smarty_tpl->tpl_vars['customization']->value['quantity'])) {?>
					<input type="hidden" name="cancelCustomizationQuantity[<?php echo intval($_smarty_tpl->tpl_vars['customizationId']->value);?>
]" value="0" />
				<?php } elseif ((!$_smarty_tpl->tpl_vars['order']->value->hasBeenDelivered()||Configuration::get('PS_ORDER_RETURN'))) {?>
					<input type="text" id="cancelQuantity_<?php echo intval($_smarty_tpl->tpl_vars['customizationId']->value);?>
" name="cancelCustomizationQuantity[<?php echo intval($_smarty_tpl->tpl_vars['customizationId']->value);?>
]" size="2" onclick="selectCheckbox(this);" value="" />0/<?php echo $_smarty_tpl->tpl_vars['customization']->value['quantity']-$_smarty_tpl->tpl_vars['customization']->value['quantity_refunded'];?>

				<?php }?>
				</td>
				<td class="partial_refund_fields current-edit" colspan="2" style="display:none; width: 250px;">
					<?php if ($_smarty_tpl->tpl_vars['product']->value['quantity_refundable']>0) {?>
					<?php if (($_smarty_tpl->tpl_vars['order']->value->getTaxCalculationMethod()==@constant('PS_TAX_EXC'))) {?>
						<?php $_smarty_tpl->tpl_vars['amount_refundable'] = new Smarty_variable($_smarty_tpl->tpl_vars['product']->value['amount_refundable'], null, 0);?>
					<?php } else { ?>
						<?php $_smarty_tpl->tpl_vars['amount_refundable'] = new Smarty_variable($_smarty_tpl->tpl_vars['product']->value['amount_refundable_tax_incl'], null, 0);?>
					<?php }?>
					<div class="form-group">
						<div class="<?php if ($_smarty_tpl->tpl_vars['product']->value['amount_refundable']>0) {?>col-lg-4<?php } else { ?>col-lg-12<?php }?>">
							<label class="control-label">
								<?php echo smartyTranslate(array('s'=>'Quantity:'),$_smarty_tpl);?>

							</label>
							<div class="input-group">
								<input onchange="checkPartialRefundProductQuantity(this)" type="text" name="partialRefundProductQuantity[<?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_order_detail']);?>
]" value="<?php if (($_smarty_tpl->tpl_vars['customization']->value['quantity']-$_smarty_tpl->tpl_vars['customization']->value['quantity_refunded'])>0) {?>1<?php } else { ?>0<?php }?>" />
								<div class="input-group-addon">/ <?php echo $_smarty_tpl->tpl_vars['product']->value['quantity_refundable'];?>
</div>
							</div>
						</div>
						<div class="<?php if ($_smarty_tpl->tpl_vars['product']->value['quantity_refundable']>0) {?>col-lg-8<?php } else { ?>col-lg-12<?php }?>">
							<label class="control-label">
								<span class="title_box "><?php echo smartyTranslate(array('s'=>'Amount:'),$_smarty_tpl);?>
</span>
								<small class="text-muted">(<?php echo Smarty::$_smarty_vars['capture']['TaxMethod'];?>
)</small>
							</label>
							<div class="input-group">
								<?php if ($_smarty_tpl->tpl_vars['currency']->value->format%2) {?><div class="input-group-addon"><?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
</div><?php }?>
								<input onchange="checkPartialRefundProductAmount(this)" type="text" name="partialRefundProduct[<?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_order_detail']);?>
]" />
								<?php if (!($_smarty_tpl->tpl_vars['currency']->value->format%2)) {?><div class="input-group-addon"><?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
</div><?php }?>
							</div>
							<p class="help-block"><i class="icon-warning-sign"></i> <?php echo smartyTranslate(array('s'=>'(Max %s %s)','sprintf'=>array(Tools::displayPrice(Tools::ps_round($_smarty_tpl->tpl_vars['amount_refundable']->value,2),$_smarty_tpl->tpl_vars['currency']->value->id),Smarty::$_smarty_vars['capture']['TaxMethod'])),$_smarty_tpl);?>
</p>
						</div>
					</div>
					<?php }?>
				</td>
				<?php if (($_smarty_tpl->tpl_vars['can_edit']->value&&!$_smarty_tpl->tpl_vars['order']->value->hasBeenDelivered())) {?>
					<td class="edit_product_fields" colspan="2" style="display:none"></td>
					<td class="product_action" style="text-align:right"></td>
				<?php }?>
			</tr>
		<?php } ?>
	<?php } ?>
<?php }?>
<?php }} ?>
<?php /* Smarty version Smarty-3.1.19, created on 2016-01-07 11:53:05
         compiled from "/home/sumit/public_html/html/hotel-reservation-system/admin_htl/themes/default/template/controllers/orders/_product_line.tpl" */ ?>
<?php if ($_valid && !is_callable('content_568e044931e766_24352412')) {function content_568e044931e766_24352412($_smarty_tpl) {?>


<?php if (($_smarty_tpl->tpl_vars['order']->value->getTaxCalculationMethod()==@constant('PS_TAX_EXC'))) {?>
	<?php $_smarty_tpl->tpl_vars['product_price'] = new Smarty_variable(($_smarty_tpl->tpl_vars['product']->value['unit_price_tax_excl']+$_smarty_tpl->tpl_vars['product']->value['ecotax']), null, 0);?>
<?php } else { ?>
	<?php $_smarty_tpl->tpl_vars['product_price'] = new Smarty_variable($_smarty_tpl->tpl_vars['product']->value['unit_price_tax_incl'], null, 0);?>
<?php }?>

<?php if (($_smarty_tpl->tpl_vars['product']->value['product_quantity']>$_smarty_tpl->tpl_vars['product']->value['customized_product_quantity'])) {?>
<tr class="product-line-row">
	<td><?php if (isset($_smarty_tpl->tpl_vars['product']->value['image'])&&$_smarty_tpl->tpl_vars['product']->value['image']->id) {?><?php echo $_smarty_tpl->tpl_vars['product']->value['image_tag'];?>
<?php }?></td>
	<td>
		<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminProducts'), ENT_QUOTES, 'UTF-8', true);?>
&amp;id_product=<?php echo intval($_smarty_tpl->tpl_vars['product']->value['product_id']);?>
&amp;updateproduct&amp;token=<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['getAdminToken'][0][0]->getAdminTokenLiteSmarty(array('tab'=>'AdminProducts'),$_smarty_tpl);?>
">
			<span class="productName"><?php echo $_smarty_tpl->tpl_vars['product']->value['product_name'];?>
</span><br />
			<?php if ($_smarty_tpl->tpl_vars['product']->value['product_reference']) {?><?php echo smartyTranslate(array('s'=>'Reference number:'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['product']->value['product_reference'];?>
<br /><?php }?>
			<?php if ($_smarty_tpl->tpl_vars['product']->value['product_supplier_reference']) {?><?php echo smartyTranslate(array('s'=>'Supplier reference:'),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->tpl_vars['product']->value['product_supplier_reference'];?>
<?php }?>
		</a>
		<div class="row-editing-warning" style="display:none;">
			<div class="alert alert-warning">
				<strong><?php echo smartyTranslate(array('s'=>'Editing this product line will remove the reduction and base price.'),$_smarty_tpl);?>
</strong>
			</div>
		</div>
	</td>
	<td>
		<span class="product_price_show"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['product_price']->value,'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>
</span>
		<?php if ($_smarty_tpl->tpl_vars['can_edit']->value) {?>
		<div class="product_price_edit" style="display:none;">
			<input type="hidden" name="product_id_order_detail" class="edit_product_id_order_detail" value="<?php echo $_smarty_tpl->tpl_vars['product']->value['id_order_detail'];?>
" />
			<div class="form-group">
				<div class="fixed-width-xl">
					<div class="input-group">
						<?php if ($_smarty_tpl->tpl_vars['currency']->value->format%2) {?><div class="input-group-addon"><?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
 <?php echo smartyTranslate(array('s'=>'tax excl.'),$_smarty_tpl);?>
</div><?php }?>
						<input type="text" name="product_price_tax_excl" class="edit_product_price_tax_excl edit_product_price" value="<?php echo Tools::ps_round($_smarty_tpl->tpl_vars['product']->value['unit_price_tax_excl'],2);?>
"/>
						<?php if (!($_smarty_tpl->tpl_vars['currency']->value->format%2)) {?><div class="input-group-addon"><?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
 <?php echo smartyTranslate(array('s'=>'tax excl.'),$_smarty_tpl);?>
</div><?php }?>
					</div>
				</div>
				<br/>
				<div class="fixed-width-xl">
					<div class="input-group">
						<?php if ($_smarty_tpl->tpl_vars['currency']->value->format%2) {?><div class="input-group-addon"><?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
 <?php echo smartyTranslate(array('s'=>'tax incl.'),$_smarty_tpl);?>
</div><?php }?>
						<input type="text" name="product_price_tax_incl" class="edit_product_price_tax_incl edit_product_price" value="<?php echo Tools::ps_round($_smarty_tpl->tpl_vars['product']->value['unit_price_tax_incl'],2);?>
"/>
						<?php if (!($_smarty_tpl->tpl_vars['currency']->value->format%2)) {?><div class="input-group-addon"><?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
 <?php echo smartyTranslate(array('s'=>'tax incl.'),$_smarty_tpl);?>
</div><?php }?>
					</div>
				</div>
			</div>
		</div>
		<?php }?>
	</td>
	<td class="productQuantity text-center">
		<span class="product_quantity_show<?php if ((int)$_smarty_tpl->tpl_vars['product']->value['product_quantity']-(int)$_smarty_tpl->tpl_vars['product']->value['customized_product_quantity']>1) {?> badge<?php }?>"><?php echo (int)$_smarty_tpl->tpl_vars['product']->value['product_quantity']-(int)$_smarty_tpl->tpl_vars['product']->value['customized_product_quantity'];?>
</span>
		<?php if ($_smarty_tpl->tpl_vars['can_edit']->value) {?>
		<span class="product_quantity_edit" style="display:none;">
			<input type="text" name="product_quantity" class="edit_product_quantity" value="<?php echo htmlentities($_smarty_tpl->tpl_vars['product']->value['product_quantity']);?>
"/>
		</span>
		<?php }?>
	</td>
	<?php if ($_smarty_tpl->tpl_vars['display_warehouse']->value) {?>
		<td>
			<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['product']->value['warehouse_name'], ENT_QUOTES, 'UTF-8', true);?>

			<?php if ($_smarty_tpl->tpl_vars['product']->value['warehouse_location']) {?>
				<br><?php echo smartyTranslate(array('s'=>'Location'),$_smarty_tpl);?>
: <strong><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['product']->value['warehouse_location'], ENT_QUOTES, 'UTF-8', true);?>
</strong>
			<?php }?>
		</td>
	<?php }?>
	<?php if (($_smarty_tpl->tpl_vars['order']->value->hasBeenPaid())) {?>
		<td class="productQuantity text-center">
			<?php if (!empty($_smarty_tpl->tpl_vars['product']->value['amount_refund'])) {?>
				<?php echo smartyTranslate(array('s'=>'%s (%s refund)','sprintf'=>array($_smarty_tpl->tpl_vars['product']->value['product_quantity_refunded'],$_smarty_tpl->tpl_vars['product']->value['amount_refund'])),$_smarty_tpl);?>

			<?php }?>
			<input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['product']->value['quantity_refundable'];?>
" class="partialRefundProductQuantity" />
			<input type="hidden" value="<?php echo (Tools::ps_round($_smarty_tpl->tpl_vars['product_price']->value,2)*($_smarty_tpl->tpl_vars['product']->value['product_quantity']-$_smarty_tpl->tpl_vars['product']->value['customizationQuantityTotal']));?>
" class="partialRefundProductAmount" />
			<?php if (count($_smarty_tpl->tpl_vars['product']->value['refund_history'])) {?>
				<span class="tooltip">
					<span class="tooltip_label tooltip_button">+</span>
					<span class="tooltip_content">
					<span class="title"><?php echo smartyTranslate(array('s'=>'Refund history'),$_smarty_tpl);?>
</span>
					<?php  $_smarty_tpl->tpl_vars['refund'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['refund']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['product']->value['refund_history']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['refund']->key => $_smarty_tpl->tpl_vars['refund']->value) {
$_smarty_tpl->tpl_vars['refund']->_loop = true;
?>
						<?php ob_start();?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['dateFormat'][0][0]->dateFormat(array('date'=>$_smarty_tpl->tpl_vars['refund']->value['date_add']),$_smarty_tpl);?>
<?php $_tmp2=ob_get_clean();?><?php ob_start();?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['refund']->value['amount_tax_incl']),$_smarty_tpl);?>
<?php $_tmp3=ob_get_clean();?><?php echo smartyTranslate(array('s'=>'%1s - %2s','sprintf'=>array($_tmp2,$_tmp3)),$_smarty_tpl);?>
<br />
					<?php } ?>
					</span>
				</span>
			<?php }?>
		</td>
	<?php }?>
	<?php if ($_smarty_tpl->tpl_vars['order']->value->hasBeenDelivered()||$_smarty_tpl->tpl_vars['order']->value->hasProductReturned()) {?>
		<td class="productQuantity text-center">
			<?php echo $_smarty_tpl->tpl_vars['product']->value['product_quantity_return'];?>

			<?php if (count($_smarty_tpl->tpl_vars['product']->value['return_history'])) {?>
				<span class="tooltip">
					<span class="tooltip_label tooltip_button">+</span>
					<span class="tooltip_content">
					<span class="title"><?php echo smartyTranslate(array('s'=>'Return history'),$_smarty_tpl);?>
</span>
					<?php  $_smarty_tpl->tpl_vars['return'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['return']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['product']->value['return_history']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['return']->key => $_smarty_tpl->tpl_vars['return']->value) {
$_smarty_tpl->tpl_vars['return']->_loop = true;
?>
						<?php ob_start();?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['dateFormat'][0][0]->dateFormat(array('date'=>$_smarty_tpl->tpl_vars['return']->value['date_add']),$_smarty_tpl);?>
<?php $_tmp4=ob_get_clean();?><?php echo smartyTranslate(array('s'=>'%1s - %2s - %3s','sprintf'=>array($_tmp4,$_smarty_tpl->tpl_vars['return']->value['product_quantity'],$_smarty_tpl->tpl_vars['return']->value['state'])),$_smarty_tpl);?>
<br />
					<?php } ?>
					</span>
				</span>
			<?php }?>
		</td>
	<?php }?>
	<?php if ($_smarty_tpl->tpl_vars['stock_management']->value) {?><td class="productQuantity product_stock text-center"><?php echo $_smarty_tpl->tpl_vars['product']->value['current_stock'];?>
</td><?php }?>
	<td class="total_product">
		<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>(Tools::ps_round($_smarty_tpl->tpl_vars['product_price']->value,2)*($_smarty_tpl->tpl_vars['product']->value['product_quantity']-$_smarty_tpl->tpl_vars['product']->value['customizationQuantityTotal'])),'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>

	</td>
	<td colspan="2" style="display: none;" class="add_product_fields">&nbsp;</td>
	<td class="cancelCheck standard_refund_fields current-edit" style="display:none">
		<input type="hidden" name="totalQtyReturn" id="totalQtyReturn" value="<?php echo $_smarty_tpl->tpl_vars['product']->value['product_quantity_return'];?>
" />
		<input type="hidden" name="totalQty" id="totalQty" value="<?php echo $_smarty_tpl->tpl_vars['product']->value['product_quantity'];?>
" />
		<input type="hidden" name="productName" id="productName" value="<?php echo $_smarty_tpl->tpl_vars['product']->value['product_name'];?>
" />
	<?php if (((!$_smarty_tpl->tpl_vars['order']->value->hasBeenDelivered()||Configuration::get('PS_ORDER_RETURN'))&&(int)($_smarty_tpl->tpl_vars['product']->value['product_quantity_return'])<(int)($_smarty_tpl->tpl_vars['product']->value['product_quantity']))) {?>
		<input type="checkbox" name="id_order_detail[<?php echo $_smarty_tpl->tpl_vars['product']->value['id_order_detail'];?>
]" id="id_order_detail[<?php echo $_smarty_tpl->tpl_vars['product']->value['id_order_detail'];?>
]" value="<?php echo $_smarty_tpl->tpl_vars['product']->value['id_order_detail'];?>
" onchange="setCancelQuantity(this, <?php echo $_smarty_tpl->tpl_vars['product']->value['id_order_detail'];?>
, <?php echo $_smarty_tpl->tpl_vars['product']->value['product_quantity']-$_smarty_tpl->tpl_vars['product']->value['customizationQuantityTotal']-$_smarty_tpl->tpl_vars['product']->value['product_quantity_return']-$_smarty_tpl->tpl_vars['product']->value['product_quantity_refunded'];?>
)" <?php if (($_smarty_tpl->tpl_vars['product']->value['product_quantity_return']+$_smarty_tpl->tpl_vars['product']->value['product_quantity_refunded']>=$_smarty_tpl->tpl_vars['product']->value['product_quantity'])) {?>disabled="disabled" <?php }?>/>
	<?php } else { ?>
		--
	<?php }?>
	</td>
	<td class="cancelQuantity standard_refund_fields current-edit" style="display:none">
	<?php if (($_smarty_tpl->tpl_vars['product']->value['product_quantity_return']+$_smarty_tpl->tpl_vars['product']->value['product_quantity_refunded']>=$_smarty_tpl->tpl_vars['product']->value['product_quantity'])) {?>
		<input type="hidden" name="cancelQuantity[<?php echo $_smarty_tpl->tpl_vars['product']->value['id_order_detail'];?>
]" value="0" />
	<?php } elseif ((!$_smarty_tpl->tpl_vars['order']->value->hasBeenDelivered()||Configuration::get('PS_ORDER_RETURN'))) {?>
		<input type="text" id="cancelQuantity_<?php echo $_smarty_tpl->tpl_vars['product']->value['id_order_detail'];?>
" name="cancelQuantity[<?php echo $_smarty_tpl->tpl_vars['product']->value['id_order_detail'];?>
]" onchange="checkTotalRefundProductQuantity(this)" value="" />
	<?php }?>

	<?php if ($_smarty_tpl->tpl_vars['product']->value['customizationQuantityTotal']) {?>
		<?php $_smarty_tpl->tpl_vars['productQuantity'] = new Smarty_variable(($_smarty_tpl->tpl_vars['product']->value['product_quantity']-$_smarty_tpl->tpl_vars['product']->value['customizationQuantityTotal']), null, 0);?>
	<?php } else { ?>
		<?php $_smarty_tpl->tpl_vars['productQuantity'] = new Smarty_variable($_smarty_tpl->tpl_vars['product']->value['product_quantity'], null, 0);?>
	<?php }?>

	<?php if (($_smarty_tpl->tpl_vars['order']->value->hasBeenDelivered())) {?>
		<?php echo $_smarty_tpl->tpl_vars['product']->value['product_quantity_refunded'];?>
/<?php echo $_smarty_tpl->tpl_vars['productQuantity']->value-$_smarty_tpl->tpl_vars['product']->value['product_quantity_refunded'];?>

	<?php } elseif (($_smarty_tpl->tpl_vars['order']->value->hasBeenPaid())) {?>
		<?php echo $_smarty_tpl->tpl_vars['product']->value['product_quantity_return'];?>
/<?php echo $_smarty_tpl->tpl_vars['productQuantity']->value;?>

	<?php } else { ?>
		0/<?php echo $_smarty_tpl->tpl_vars['productQuantity']->value;?>

	<?php }?>
	</td>
	<td class="partial_refund_fields current-edit" colspan="2" style="display:none; width: 250px;">
		<?php if ($_smarty_tpl->tpl_vars['product']->value['quantity_refundable']>0) {?>
		<?php if (($_smarty_tpl->tpl_vars['order']->value->getTaxCalculationMethod()==@constant('PS_TAX_EXC'))) {?>
			<?php $_smarty_tpl->tpl_vars['amount_refundable'] = new Smarty_variable($_smarty_tpl->tpl_vars['product']->value['amount_refundable'], null, 0);?>
		<?php } else { ?>
			<?php $_smarty_tpl->tpl_vars['amount_refundable'] = new Smarty_variable($_smarty_tpl->tpl_vars['product']->value['amount_refundable_tax_incl'], null, 0);?>
		<?php }?>
		<div class="form-group">
			<div class="<?php if ($_smarty_tpl->tpl_vars['product']->value['amount_refundable']>0) {?>col-lg-4<?php } else { ?>col-lg-12<?php }?>">
				<label class="control-label">
					<?php echo smartyTranslate(array('s'=>'Quantity:'),$_smarty_tpl);?>

				</label>
				<div class="input-group">
					<input onchange="checkPartialRefundProductQuantity(this)" type="text" name="partialRefundProductQuantity[<?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['product']->value['id_order_detail'];?>
<?php $_tmp5=ob_get_clean();?><?php echo $_tmp5;?>
]" value="0" />
					<div class="input-group-addon">/ <?php echo $_smarty_tpl->tpl_vars['product']->value['quantity_refundable'];?>
</div>
				</div>
			</div>
			<div class="<?php if ($_smarty_tpl->tpl_vars['product']->value['quantity_refundable']>0) {?>col-lg-8<?php } else { ?>col-lg-12<?php }?>">
				<label class="control-label">
					<span class="title_box "><?php echo smartyTranslate(array('s'=>'Amount:'),$_smarty_tpl);?>
</span>
					<small class="text-muted">(<?php echo Smarty::$_smarty_vars['capture']['TaxMethod'];?>
)</small>
				</label>
				<div class="input-group">
					<?php if ($_smarty_tpl->tpl_vars['currency']->value->format%2) {?><div class="input-group-addon"><?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
</div><?php }?>
					<input onchange="checkPartialRefundProductAmount(this)" type="text" name="partialRefundProduct[<?php echo $_smarty_tpl->tpl_vars['product']->value['id_order_detail'];?>
]" />
					<?php if (!($_smarty_tpl->tpl_vars['currency']->value->format%2)) {?><div class="input-group-addon"><?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
</div><?php }?>
				</div>
				<p class="help-block"><i class="icon-warning-sign"></i> <?php echo smartyTranslate(array('s'=>'(Max %s %s)','sprintf'=>array(Tools::displayPrice(Tools::ps_round($_smarty_tpl->tpl_vars['amount_refundable']->value,2),$_smarty_tpl->tpl_vars['currency']->value->id),Smarty::$_smarty_vars['capture']['TaxMethod'])),$_smarty_tpl);?>
</p>
			</div>
		</div>
		<?php }?>
	</td>
	<?php if (($_smarty_tpl->tpl_vars['can_edit']->value&&!$_smarty_tpl->tpl_vars['order']->value->hasBeenDelivered())) {?>
	<td class="product_invoice" style="display: none;">
		<?php if (sizeof($_smarty_tpl->tpl_vars['invoices_collection']->value)) {?>
		<select name="product_invoice" class="edit_product_invoice">
			<?php  $_smarty_tpl->tpl_vars['invoice'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['invoice']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['invoices_collection']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['invoice']->key => $_smarty_tpl->tpl_vars['invoice']->value) {
$_smarty_tpl->tpl_vars['invoice']->_loop = true;
?>
			<option value="<?php echo $_smarty_tpl->tpl_vars['invoice']->value->id;?>
" <?php if ($_smarty_tpl->tpl_vars['invoice']->value->id==$_smarty_tpl->tpl_vars['product']->value['id_order_invoice']) {?>selected="selected"<?php }?>>
				#<?php echo Configuration::get('PS_INVOICE_PREFIX',$_smarty_tpl->tpl_vars['current_id_lang']->value,null,$_smarty_tpl->tpl_vars['order']->value->id_shop);?>
<?php echo sprintf('%06d',$_smarty_tpl->tpl_vars['invoice']->value->number);?>

			</option>
			<?php } ?>
		</select>
		<?php } else { ?>
		&nbsp;
		<?php }?>
	</td>
	<td class="product_action text-right">
		
		<div class="btn-group">
			<button type="button" class="btn btn-default edit_product_change_link">
				<i class="icon-pencil"></i>
				<?php echo smartyTranslate(array('s'=>'Edit'),$_smarty_tpl);?>

			</button>
			<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
				<span class="caret"></span>
			</button>
			<ul class="dropdown-menu" role="menu">
				<li>
					<a href="#" class="delete_product_line">
						<i class="icon-trash"></i>
						<?php echo smartyTranslate(array('s'=>'Delete'),$_smarty_tpl);?>

					</a>
				</li>
			</ul>
		</div>
		
		<button type="button" class="btn btn-default submitProductChange" style="display: none;">
			<i class="icon-ok"></i>
			<?php echo smartyTranslate(array('s'=>'Update'),$_smarty_tpl);?>

		</button>
		<button type="button" class="btn btn-default cancel_product_change_link" style="display: none;">
			<i class="icon-remove"></i>
			<?php echo smartyTranslate(array('s'=>'Cancel'),$_smarty_tpl);?>

		</button>
	</td>
	<?php }?>
</tr>
<?php }?>
<?php }} ?>
<?php /* Smarty version Smarty-3.1.19, created on 2016-01-07 11:53:05
         compiled from "/home/sumit/public_html/html/hotel-reservation-system/admin_htl/themes/default/template/controllers/orders/_new_product.tpl" */ ?>
<?php if ($_valid && !is_callable('content_568e04495059a2_37151400')) {function content_568e04495059a2_37151400($_smarty_tpl) {?>
<tr id="new_product" style="display:none">
	<td style="display:none;" colspan="2">
		<input type="hidden" id="add_product_product_id" name="add_product[product_id]" value="0" />

		<div class="form-group">
			<label><?php echo smartyTranslate(array('s'=>'Product:'),$_smarty_tpl);?>
</label>
			<div class="input-group">
				<input type="text" id="add_product_product_name" value=""/>
				<div class="input-group-addon">
					<i class="icon-search"></i>
				</div>
			</div>
		</div>

		<div id="add_product_product_attribute_area" class="form-group" style="display: none;">
			<label><?php echo smartyTranslate(array('s'=>'Combinations'),$_smarty_tpl);?>
</label>
			<select name="add_product[product_attribute_id]" id="add_product_product_attribute_id"></select>
		</div>

		<div id="add_product_product_warehouse_area" class="form-group" style="display: none;">
			<label><?php echo smartyTranslate(array('s'=>'Warehouse'),$_smarty_tpl);?>
</label>
			<select  id="add_product_warehouse" name="add_product_warehouse"></select>
		</div>
	</td>

	<td style="display:none;">
		<div class="row">
			<div class="input-group fixed-width-xl">
				<?php if ($_smarty_tpl->tpl_vars['currency']->value->format%2) {?><div class="input-group-addon"><?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
 <?php echo smartyTranslate(array('s'=>'tax excl.'),$_smarty_tpl);?>
</div><?php }?>
				<input type="text" name="add_product[product_price_tax_excl]" id="add_product_product_price_tax_excl" value="" disabled="disabled" />
				<?php if (!($_smarty_tpl->tpl_vars['currency']->value->format%2)) {?><div class="input-group-addon"><?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
 <?php echo smartyTranslate(array('s'=>'tax excl.'),$_smarty_tpl);?>
</div><?php }?>
			</div>
		</div>
		<br/>
		<div class="row">
			<div class="input-group fixed-width-xl">
				<?php if ($_smarty_tpl->tpl_vars['currency']->value->format%2) {?><div class="input-group-addon"><?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
 <?php echo smartyTranslate(array('s'=>'tax incl.'),$_smarty_tpl);?>
</div><?php }?>
				<input type="text" name="add_product[product_price_tax_incl]" id="add_product_product_price_tax_incl" value="" disabled="disabled" />
				<?php if (!($_smarty_tpl->tpl_vars['currency']->value->format%2)) {?><div class="input-group-addon"><?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
 <?php echo smartyTranslate(array('s'=>'tax incl.'),$_smarty_tpl);?>
</div><?php }?>
			</div>
		</div>
	</td>

	<td style="display:none;" class="productQuantity">
		<input type="number" class="form-control fixed-width-sm" name="add_product[product_quantity]" id="add_product_product_quantity" value="1" disabled="disabled" />
	</td>
	<?php if (($_smarty_tpl->tpl_vars['order']->value->hasBeenPaid())) {?><td style="display:none;" class="productQuantity"></td><?php }?>
	<?php if ($_smarty_tpl->tpl_vars['display_warehouse']->value) {?><td></td><?php }?>
	<?php if (($_smarty_tpl->tpl_vars['order']->value->hasBeenDelivered())) {?><td style="display:none;" class="productQuantity"></td><?php }?>
	<td style="display:none;" class="productQuantity" id="add_product_product_stock">0</td>
	<td style="display:none;" id="add_product_product_total"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>0,'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>
</td>
	<td style="display:none;" colspan="2">
		<?php if (sizeof($_smarty_tpl->tpl_vars['invoices_collection']->value)) {?>
		<select class="form-control" name="add_product[invoice]" id="add_product_product_invoice" disabled="disabled">
			<optgroup class="existing" label="<?php echo smartyTranslate(array('s'=>'Existing'),$_smarty_tpl);?>
">
				<?php  $_smarty_tpl->tpl_vars['invoice'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['invoice']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['invoices_collection']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['invoice']->key => $_smarty_tpl->tpl_vars['invoice']->value) {
$_smarty_tpl->tpl_vars['invoice']->_loop = true;
?>
				<option value="<?php echo $_smarty_tpl->tpl_vars['invoice']->value->id;?>
"><?php echo $_smarty_tpl->tpl_vars['invoice']->value->getInvoiceNumberFormatted($_smarty_tpl->tpl_vars['current_id_lang']->value);?>
</option>
				<?php } ?>
			</optgroup>
			<optgroup label="<?php echo smartyTranslate(array('s'=>'New'),$_smarty_tpl);?>
">
				<option value="0"><?php echo smartyTranslate(array('s'=>'Create a new invoice'),$_smarty_tpl);?>
</option>
			</optgroup>
		</select>
		<?php }?>
	</td>
	<td style="display:none;">
		<button type="button" class="btn btn-default" id="cancelAddProduct">
			<i class="icon-remove text-danger"></i>
			<?php echo smartyTranslate(array('s'=>'Cancel'),$_smarty_tpl);?>

		</button>
		<button type="button" class="btn btn-default" id="submitAddProduct" disabled="disabled">
			<i class="icon-ok text-success"></i>
			<?php echo smartyTranslate(array('s'=>'Add'),$_smarty_tpl);?>

		</button>
	</td>
</tr>

<tr id="new_invoice" style="display:none">
	<td colspan="10">
		<h4><?php echo smartyTranslate(array('s'=>'New invoice information'),$_smarty_tpl);?>
</h4>
		<div class="form-horizontal">
			<div class="form-group">
				<label class="control-label col-lg-3"><?php echo smartyTranslate(array('s'=>'Carrier'),$_smarty_tpl);?>
</label>
				<div class="col-lg-9">
					<p class="form-control-static"><strong><?php echo $_smarty_tpl->tpl_vars['carrier']->value->name;?>
</strong></p>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label col-lg-3"><?php echo smartyTranslate(array('s'=>'Shipping Cost'),$_smarty_tpl);?>
</label>
				<div class="col-lg-9">
					<div class="checkbox">
						<label>
							<input type="checkbox" name="add_invoice[free_shipping]" value="1" />
							<?php echo smartyTranslate(array('s'=>'Free shipping'),$_smarty_tpl);?>

						</label>
						<p class="help-block"><?php echo smartyTranslate(array('s'=>'If you don\'t select "Free shipping," the normal shipping cost will be applied.'),$_smarty_tpl);?>
</p>
					</div>
				</div>
			</div>
		</div>
	</td>
</tr>
<?php }} ?>
<?php /* Smarty version Smarty-3.1.19, created on 2016-01-07 11:53:05
         compiled from "/home/sumit/public_html/html/hotel-reservation-system/admin_htl/themes/default/template/controllers/orders/_discount_form.tpl" */ ?>
<?php if ($_valid && !is_callable('content_568e04495ca977_33262913')) {function content_568e04495ca977_33262913($_smarty_tpl) {?>

<div class="form-horizontal well">
	<div class="form-group">
		<label class="control-label col-lg-3">
			<?php echo smartyTranslate(array('s'=>'Name'),$_smarty_tpl);?>

		</label>
		<div class="col-lg-9">
			<input class="form-control" type="text" name="discount_name" value="" />
		</div>
	</div>

	<div class="form-group">
		<label class="control-label col-lg-3">
			<?php echo smartyTranslate(array('s'=>'Type'),$_smarty_tpl);?>

		</label>
		<div class="col-lg-9">
			<select class="form-control" name="discount_type" id="discount_type">
				<option value="1"><?php echo smartyTranslate(array('s'=>'Percent'),$_smarty_tpl);?>
</option>
				<option value="2"><?php echo smartyTranslate(array('s'=>'Amount'),$_smarty_tpl);?>
</option>
				<option value="3"><?php echo smartyTranslate(array('s'=>'Free shipping'),$_smarty_tpl);?>
</option>
			</select>
		</div>
	</div>

	<div id="discount_value_field" class="form-group">
		<label class="control-label col-lg-3">
			<?php echo smartyTranslate(array('s'=>'Value'),$_smarty_tpl);?>

		</label>
		<div class="col-lg-9">
			<div class="input-group">
				<div class="input-group-addon">
					<span id="discount_currency_sign" style="display: none;"><?php echo $_smarty_tpl->tpl_vars['currency']->value->sign;?>
</span>
					<span id="discount_percent_symbol">%</span>
				</div>
				<input class="form-control" type="text" name="discount_value"/>
			</div>
			<p class="text-muted" id="discount_value_help" style="display: none;">
				<?php echo smartyTranslate(array('s'=>'This value must include taxes.'),$_smarty_tpl);?>

			</p>
		</div>
	</div>

	<?php if ($_smarty_tpl->tpl_vars['order']->value->hasInvoice()) {?>
	<div class="form-group">
		<label class="control-label col-lg-3">
			<?php echo smartyTranslate(array('s'=>'Invoice'),$_smarty_tpl);?>

		</label>
		<div class="col-lg-9">
			<select name="discount_invoice">
				<?php  $_smarty_tpl->tpl_vars['invoice'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['invoice']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['invoices_collection']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['invoice']->key => $_smarty_tpl->tpl_vars['invoice']->value) {
$_smarty_tpl->tpl_vars['invoice']->_loop = true;
?>
				<option value="<?php echo $_smarty_tpl->tpl_vars['invoice']->value->id;?>
" selected="selected">
					<?php echo $_smarty_tpl->tpl_vars['invoice']->value->getInvoiceNumberFormatted($_smarty_tpl->tpl_vars['current_id_lang']->value);?>
 - <?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['invoice']->value->total_paid_tax_incl,'currency'=>$_smarty_tpl->tpl_vars['order']->value->id_currency),$_smarty_tpl);?>

				</option>
				<?php } ?>
			</select>
		</div>
	</div>

	<div class="form-group">
		<div class="col-lg-9 col-lg-offset-3">
			<p class="checkbox">
				<label class="control-label" for="discount_all_invoices">
					<input type="checkbox" name="discount_all_invoices" id="discount_all_invoices" value="1" /> 
					<?php echo smartyTranslate(array('s'=>'Apply on all invoices'),$_smarty_tpl);?>

				</label>
			</p>
			<p class="help-block">
				<?php echo smartyTranslate(array('s'=>'If you chooses to create this discount for all invoices, only one discount will be created per order invoice.'),$_smarty_tpl);?>

			</p>
		</div>
	</div>
	<?php }?>

	<div class="row">
		<div class="col-lg-9 col-lg-offset-3">
			<button class="btn btn-default" type="button" id="cancel_add_voucher">
				<i class="icon-remove text-danger"></i>
				<?php echo smartyTranslate(array('s'=>'Cancel'),$_smarty_tpl);?>

			</button>
			<button class="btn btn-default" type="submit" name="submitNewVoucher">
				<i class="icon-ok text-success"></i>
				<?php echo smartyTranslate(array('s'=>'Add'),$_smarty_tpl);?>

			</button>
		</div>
	</div>
</div><?php }} ?>
