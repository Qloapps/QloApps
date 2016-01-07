<?php /* Smarty version Smarty-3.1.19, created on 2016-01-07 11:41:47
         compiled from "/home/sumit/public_html/html/hotel-reservation-system/modules/hotelreservationsystem/views/templates/admin/order_refund_requests/helpers/view/view.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1484557933568e01a38772e5-50508479%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '7f3284d34d80a535bd5c556310d71260094d26ba' => 
    array (
      0 => '/home/sumit/public_html/html/hotel-reservation-system/modules/hotelreservationsystem/views/templates/admin/order_refund_requests/helpers/view/view.tpl',
      1 => 1452142909,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1484557933568e01a38772e5-50508479',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'customer_name' => 0,
    'customer_email' => 0,
    'htl_name' => 0,
    'product_name' => 0,
    'room_numbers' => 0,
    'rm_name' => 0,
    'date_from' => 0,
    'date_to' => 0,
    'total_amount' => 0,
    'curr_code' => 0,
    'currentStage' => 0,
    'way_of_payment' => 0,
    'adv_paid_amount' => 0,
    'refunded_amount' => 0,
    'cancel_charge' => 0,
    'deduction_amount' => 0,
    'refund_amount' => 0,
    'all_ord_refund_stages' => 0,
    'stage' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_568e01a395a9e7_22019949',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_568e01a395a9e7_22019949')) {function content_568e01a395a9e7_22019949($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_date_format')) include '/home/sumit/public_html/html/hotel-reservation-system/tools/smarty/plugins/modifier.date_format.php';
?><div id="row">
	<div class="col-lg-12">
		<div class="panel">
			<div class="panel-heading">
				<i class="icon-info"></i> &nbsp;&nbsp;<?php echo smartyTranslate(array('s'=>'Order Cancellation Request Information','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>

			</div>
			<div class="panel-content">
				<div class="customer_details details-div">
					<h3><?php echo smartyTranslate(array('s'=>"Customer Details",'mod'=>"hotelreservationsyatem"),$_smarty_tpl);?>
</h3>
					<?php if (isset($_smarty_tpl->tpl_vars['customer_name']->value)) {?>
						
						<p><strong><?php echo smartyTranslate(array('s'=>'Customer Name','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
 :  </strong>&nbsp;&nbsp;<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['customer_name']->value, ENT_QUOTES, 'UTF-8', true);?>
</p>
						
						<p><strong><?php echo smartyTranslate(array('s'=>'Customer Email','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
 :  </strong>&nbsp;&nbsp;<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['customer_email']->value, ENT_QUOTES, 'UTF-8', true);?>
</p>
					<?php } else { ?>
						<p><strong><?php echo smartyTranslate(array('s'=>'Customer','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
 :  </strong><?php echo smartyTranslate(array('s'=>'As a guest','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
</p>
					<?php }?>
				</div>
				<hr>
				<div class="order_cancellation_details details-div row">
					<h3><?php echo smartyTranslate(array('s'=>"Order Cancellation Details",'mod'=>"hotelreservationsyatem"),$_smarty_tpl);?>
</h3>
					<div class="col-lg-6">
						
						<p><strong><?php echo smartyTranslate(array('s'=>'Hotel Name','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
 :  </strong>&nbsp;&nbsp;<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['htl_name']->value, ENT_QUOTES, 'UTF-8', true);?>
</p>
						
						<p><strong><?php echo smartyTranslate(array('s'=>'Room Type','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
 :  </strong>&nbsp;&nbsp;<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['product_name']->value, ENT_QUOTES, 'UTF-8', true);?>
</p>
						
						<p><strong><?php echo smartyTranslate(array('s'=>'Room Numbers','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
 :  </strong>&nbsp;&nbsp;						<?php  $_smarty_tpl->tpl_vars['rm_name'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['rm_name']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['room_numbers']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['rm_name']->key => $_smarty_tpl->tpl_vars['rm_name']->value) {
$_smarty_tpl->tpl_vars['rm_name']->_loop = true;
?>	
							<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['rm_name']->value, ENT_QUOTES, 'UTF-8', true);?>
,&nbsp;
						<?php } ?>	
						</p>
						
						<p><strong><?php echo smartyTranslate(array('s'=>'Date From','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
 :  </strong>&nbsp;&nbsp;<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['date_from']->value,"%d-%b-%G");?>
</p>

						<p><strong><?php echo smartyTranslate(array('s'=>'Date To','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
 :  </strong>&nbsp;&nbsp;<?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['date_to']->value,"%d-%b-%G");?>
</p>

					</div>
					<div class="col-lg-6">
						<p><strong><?php echo smartyTranslate(array('s'=>'Total Amount','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
 :  </strong>&nbsp;&nbsp;<?php echo $_smarty_tpl->tpl_vars['total_amount']->value;?>
&nbsp;<?php echo $_smarty_tpl->tpl_vars['curr_code']->value;?>
</p>

						
						<p><strong><?php echo smartyTranslate(array('s'=>'Order Cancellation Stage','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
 :  </strong>&nbsp;&nbsp;<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['currentStage']->value->name, ENT_QUOTES, 'UTF-8', true);?>
</p>

						<?php if (isset($_smarty_tpl->tpl_vars['currentStage']->value->name)&&$_smarty_tpl->tpl_vars['currentStage']->value->name=='Refunded') {?>
							<?php if ($_smarty_tpl->tpl_vars['way_of_payment']->value=='Advance Payment') {?>
								<?php $_smarty_tpl->tpl_vars["cancel_charge"] = new Smarty_variable($_smarty_tpl->tpl_vars['adv_paid_amount']->value-$_smarty_tpl->tpl_vars['refunded_amount']->value, null, 0);?>
							<?php } else { ?>
								<?php $_smarty_tpl->tpl_vars["cancel_charge"] = new Smarty_variable($_smarty_tpl->tpl_vars['total_amount']->value-$_smarty_tpl->tpl_vars['refunded_amount']->value, null, 0);?>
							<?php }?>
							<p><strong><?php echo smartyTranslate(array('s'=>'Total Cancellation Charges','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
 :  </strong>&nbsp;&nbsp;<?php echo round($_smarty_tpl->tpl_vars['cancel_charge']->value,"2");?>
&nbsp;<?php echo $_smarty_tpl->tpl_vars['curr_code']->value;?>
</p>
							<p><strong><?php echo smartyTranslate(array('s'=>'Total Refund Amount','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
 :  </strong>&nbsp;&nbsp;<?php echo round($_smarty_tpl->tpl_vars['refunded_amount']->value,"2");?>
&nbsp;<?php echo $_smarty_tpl->tpl_vars['curr_code']->value;?>
</p>
						<?php }?>

						<p><strong><?php echo smartyTranslate(array('s'=>'Way Of Payment','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
 :  </strong>&nbsp;&nbsp;<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['way_of_payment']->value, ENT_QUOTES, 'UTF-8', true);?>
</p>
						<?php if ($_smarty_tpl->tpl_vars['way_of_payment']->value=='Advance Payment') {?>
							<p><strong><?php echo smartyTranslate(array('s'=>'Advance Paid Amount','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
 :  </strong>&nbsp;&nbsp;<?php echo $_smarty_tpl->tpl_vars['adv_paid_amount']->value;?>
&nbsp;<?php echo $_smarty_tpl->tpl_vars['curr_code']->value;?>
</p>
						<?php }?>
					</div>
				</div>

				<hr>

				<!-- Change order cancellation stage form -->
				<?php if ($_smarty_tpl->tpl_vars['currentStage']->value->name!='Refunded'&&$_smarty_tpl->tpl_vars['currentStage']->value->name!='Rejected') {?>
					<div class="new_stage_change details-div">
						<h3><?php echo smartyTranslate(array('s'=>"Change Order Cancellation Status",'mod'=>"hotelreservationsyatem"),$_smarty_tpl);?>
</h3>
						<form action="" method="post" class="form-horizontal well hidden-print">
							<div class="row">

							<p><strong><?php echo smartyTranslate(array('s'=>'Total Cancellation Charges','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
 :  </strong>&nbsp;&nbsp;<?php echo round($_smarty_tpl->tpl_vars['deduction_amount']->value,"2");?>
&nbsp;<?php echo $_smarty_tpl->tpl_vars['curr_code']->value;?>
</p>

							<?php if ($_smarty_tpl->tpl_vars['way_of_payment']->value=='Advance Payment') {?>
								<?php $_smarty_tpl->tpl_vars["refund_amount"] = new Smarty_variable($_smarty_tpl->tpl_vars['adv_paid_amount']->value-$_smarty_tpl->tpl_vars['deduction_amount']->value, null, 0);?>
							<?php } else { ?>
								<?php $_smarty_tpl->tpl_vars["refund_amount"] = new Smarty_variable($_smarty_tpl->tpl_vars['total_amount']->value-$_smarty_tpl->tpl_vars['deduction_amount']->value, null, 0);?>
							<?php }?>
							<p><strong><?php echo smartyTranslate(array('s'=>'Total Refund Amount','mod'=>'hotelreservationsystem'),$_smarty_tpl);?>
 :  </strong>&nbsp;&nbsp;<?php echo round($_smarty_tpl->tpl_vars['refund_amount']->value,"2");?>
&nbsp;<?php echo $_smarty_tpl->tpl_vars['curr_code']->value;?>
</p>
							
							<hr>
								<div class="form-group">
									<label for="id_order_cancellation_stage" class="required control-label col-lg-2">
										<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title='<?php echo smartyTranslate(array('s'=>"Select New Stage of order cancellation.",'mod'=>"hotelreservationsyatem"),$_smarty_tpl);?>
'><?php echo smartyTranslate(array('s'=>"Select New Stage",'mod'=>"hotelreservationsystem"),$_smarty_tpl);?>
</span>
									</label>
									<div class="col-lg-8">
										<div class="row">
											<div class="col-lg-3">
												<select id="id_order_cancellation_stage" name="id_order_cancellation_stage">
													<?php  $_smarty_tpl->tpl_vars['stage'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['stage']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['all_ord_refund_stages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['stage']->key => $_smarty_tpl->tpl_vars['stage']->value) {
$_smarty_tpl->tpl_vars['stage']->_loop = true;
?>
														<option value="<?php echo intval($_smarty_tpl->tpl_vars['stage']->value['id']);?>
"<?php if (isset($_smarty_tpl->tpl_vars['currentStage']->value)&&$_smarty_tpl->tpl_vars['stage']->value['id']==$_smarty_tpl->tpl_vars['currentStage']->value->id) {?> selected="selected" disabled="disabled"<?php }?>><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['stage']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
</option>
													<?php } ?>
												</select>
											</div>
										</div>
									</div>
								</div>
								<div class="form-group cancellation_charge_div">
									<label for="cancellation_charge" class="control-label col-lg-2">
										<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="Enter How much Amount you want that will be paid by this customer as cancellation charges"><?php echo smartyTranslate(array('s'=>'Cancellation Charges','mod'=>"hotelreservationsyatem"),$_smarty_tpl);?>
</span>
									</label>
									<div class="col-lg-2">
										<div class="input-group col-lg-12">
											<input type="text" id="cancellation_charge" name="cancellation_charge">
										</div>
									</div>
								</div>
								<!-- <div class="form-group">
									<label for="refund_amount" class="required control-label col-lg-2">
										<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title=""><?php echo smartyTranslate(array('s'=>'Refund Amount','mod'=>"hotelreservationsyatem"),$_smarty_tpl);?>
</span>
									</label>
									<div class="col-lg-2">
										<div class="input-group">
											<input type="text" id="refund_amount" name="refund_amount">
										</div>
									</div>
								</div> -->
								<div class="col-lg-4">
									<button type="submit" name="submitOrderCancelStage" class="btn btn-primary pull-right">
										<?php echo smartyTranslate(array('s'=>'Update Stage','mod'=>"hotelreservationsystem"),$_smarty_tpl);?>

									</button>
								</div>
							</div>
						</form>
					</div>
				<?php }?>
				<hr>
			</div>
		</div>
	</div>
</div>
<?php }} ?>
