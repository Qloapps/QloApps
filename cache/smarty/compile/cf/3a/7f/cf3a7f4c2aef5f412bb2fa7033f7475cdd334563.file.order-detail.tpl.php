<?php /* Smarty version Smarty-3.1.19, created on 2016-01-07 12:09:20
         compiled from "/home/sumit/public_html/html/hotel-reservation-system/themes/hotel-reservation-theme/order-detail.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1490004781568d37b15d21c5-28280395%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'cf3a7f4c2aef5f412bb2fa7033f7475cdd334563' => 
    array (
      0 => '/home/sumit/public_html/html/hotel-reservation-system/themes/hotel-reservation-theme/order-detail.tpl',
      1 => 1452148738,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1490004781568d37b15d21c5-28280395',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_568d37b1a3e5c7_30096773',
  'variables' => 
  array (
    'order' => 0,
    'reorderingAllowed' => 0,
    'opc' => 0,
    'link' => 0,
    'carrier' => 0,
    'shop_name' => 0,
    'invoice' => 0,
    'invoiceAllowed' => 0,
    'is_guest' => 0,
    'order_history' => 0,
    'state' => 0,
    'followup' => 0,
    'address_delivery' => 0,
    'dlv_adr_fields' => 0,
    'field_item' => 0,
    'address_words' => 0,
    'word_item' => 0,
    'deliveryAddressFormatedValues' => 0,
    'address_invoice' => 0,
    'inv_adr_fields' => 0,
    'invoiceAddressFormatedValues' => 0,
    'HOOK_ORDERDETAILDISPLAYED' => 0,
    'non_requested_rooms' => 0,
    'redirect_link_terms' => 0,
    'cart_htl_data' => 0,
    'return_allowed' => 0,
    'priceDisplay' => 0,
    'use_tax' => 0,
    'currency' => 0,
    'order_adv_dtl' => 0,
    'data_v' => 0,
    'group_use_tax' => 0,
    'rm_v' => 0,
    'products' => 0,
    'product' => 0,
    'productId' => 0,
    'productAttributeId' => 0,
    'customizedDatas' => 0,
    'customizationPerAddress' => 0,
    'customizationId' => 0,
    'customization' => 0,
    'type' => 0,
    'CUSTOMIZE_FILE' => 0,
    'datas' => 0,
    'pic_dir' => 0,
    'data' => 0,
    'CUSTOMIZE_TEXTFIELD' => 0,
    'customizationFieldName' => 0,
    'logable' => 0,
    'img_dir' => 0,
    'productQuantity' => 0,
    'discounts' => 0,
    'discount' => 0,
    'any_back_order' => 0,
    'shw_bo_msg' => 0,
    'back_ord_msg' => 0,
    'carriers' => 0,
    'line' => 0,
    'messages' => 0,
    'message' => 0,
    'errors' => 0,
    'error' => 0,
    'message_confirmation' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_568d37b1a3e5c7_30096773')) {function content_568d37b1a3e5c7_30096773($_smarty_tpl) {?><?php if (!is_callable('smarty_modifier_replace')) include '/home/sumit/public_html/html/hotel-reservation-system/tools/smarty/plugins/modifier.replace.php';
if (!is_callable('smarty_modifier_date_format')) include '/home/sumit/public_html/html/hotel-reservation-system/tools/smarty/plugins/modifier.date_format.php';
if (!is_callable('smarty_function_counter')) include '/home/sumit/public_html/html/hotel-reservation-system/tools/smarty/plugins/function.counter.php';
if (!is_callable('smarty_modifier_regex_replace')) include '/home/sumit/public_html/html/hotel-reservation-system/tools/smarty/plugins/modifier.regex_replace.php';
?>
<?php if (isset($_smarty_tpl->tpl_vars['order']->value)) {?>
<div class="box box-small clearfix">
	<?php if (isset($_smarty_tpl->tpl_vars['reorderingAllowed']->value)&&$_smarty_tpl->tpl_vars['reorderingAllowed']->value) {?>
	<form id="submitReorder" action="<?php if (isset($_smarty_tpl->tpl_vars['opc']->value)&&$_smarty_tpl->tpl_vars['opc']->value) {?><?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('order-opc',true);?>
<?php } else { ?><?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('order',true);?>
<?php }?>" method="post" class="submit">
		<input type="hidden" value="<?php echo $_smarty_tpl->tpl_vars['order']->value->id;?>
" name="id_order"/>
		<input type="hidden" value="" name="submitReorder"/>

		<!-- <a href="#" onclick="$(this).closest('form').submit(); return false;" class="button btn btn-default button-medium pull-right"><span><?php echo smartyTranslate(array('s'=>'Reorder'),$_smarty_tpl);?>
<i class="icon-chevron-right right"></i></span></a> --><!-- by webkul not to show reorder tab -->
	</form>
	<?php }?>
	<p class="dark">
		<strong><?php echo smartyTranslate(array('s'=>'Order Reference %s - placed on','sprintf'=>$_smarty_tpl->tpl_vars['order']->value->getUniqReference()),$_smarty_tpl);?>
 <?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['dateFormat'][0][0]->dateFormat(array('date'=>$_smarty_tpl->tpl_vars['order']->value->date_add,'full'=>0),$_smarty_tpl);?>
</strong>
	</p>
</div>
<div class="info-order box">
	<?php if ($_smarty_tpl->tpl_vars['carrier']->value->id) {?><p><strong class="dark"><?php echo smartyTranslate(array('s'=>'Carrier'),$_smarty_tpl);?>
</strong> <?php if ($_smarty_tpl->tpl_vars['carrier']->value->name=="0") {?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shop_name']->value, ENT_QUOTES, 'UTF-8', true);?>
<?php } else { ?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['carrier']->value->name, ENT_QUOTES, 'UTF-8', true);?>
<?php }?></p><?php }?>
	<p><strong class="dark"><?php echo smartyTranslate(array('s'=>'Payment method'),$_smarty_tpl);?>
</strong> <span class="color-myaccount"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->payment, ENT_QUOTES, 'UTF-8', true);?>
</span></p>
	<?php if ($_smarty_tpl->tpl_vars['invoice']->value&&$_smarty_tpl->tpl_vars['invoiceAllowed']->value) {?>
	<p>
		<i class="icon-file-text"></i>
		<a target="_blank" href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getPageLink('pdf-invoice',true);?>
?id_order=<?php echo intval($_smarty_tpl->tpl_vars['order']->value->id);?>
<?php if ($_smarty_tpl->tpl_vars['is_guest']->value) {?>&amp;secure_key=<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->secure_key, ENT_QUOTES, 'UTF-8', true);?>
<?php }?>"><?php echo smartyTranslate(array('s'=>'Download your invoice as a PDF file.'),$_smarty_tpl);?>
</a>
	</p>
	<?php }?>
	<?php if ($_smarty_tpl->tpl_vars['order']->value->recyclable) {?>
	<p><i class="icon-repeat"></i>&nbsp;<?php echo smartyTranslate(array('s'=>'You have given permission to receive your order in recycled packaging.'),$_smarty_tpl);?>
</p>
	<?php }?>
	<?php if ($_smarty_tpl->tpl_vars['order']->value->gift) {?>
		<p><i class="icon-gift"></i>&nbsp;<?php echo smartyTranslate(array('s'=>'You have requested gift wrapping for this order.'),$_smarty_tpl);?>
</p>
		<p><strong class="dark"><?php echo smartyTranslate(array('s'=>'Message'),$_smarty_tpl);?>
</strong> <?php echo nl2br($_smarty_tpl->tpl_vars['order']->value->gift_message);?>
</p>
	<?php }?>
</div>

<?php if (count($_smarty_tpl->tpl_vars['order_history']->value)) {?>
<h1 class="page-heading"><?php echo smartyTranslate(array('s'=>'Follow your order\'s status step-by-step'),$_smarty_tpl);?>
</h1>
<div class="table_block">
	<table class="detail_step_by_step table table-bordered">
		<thead>
			<tr>
				<th class="first_item"><?php echo smartyTranslate(array('s'=>'Date'),$_smarty_tpl);?>
</th>
				<th class="last_item"><?php echo smartyTranslate(array('s'=>'Status'),$_smarty_tpl);?>
</th>
			</tr>
		</thead>
		<tbody>
		<?php  $_smarty_tpl->tpl_vars['state'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['state']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['order_history']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['state']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['state']->iteration=0;
 $_smarty_tpl->tpl_vars['state']->index=-1;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']["orderStates"]['index']=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['state']->key => $_smarty_tpl->tpl_vars['state']->value) {
$_smarty_tpl->tpl_vars['state']->_loop = true;
 $_smarty_tpl->tpl_vars['state']->iteration++;
 $_smarty_tpl->tpl_vars['state']->index++;
 $_smarty_tpl->tpl_vars['state']->first = $_smarty_tpl->tpl_vars['state']->index === 0;
 $_smarty_tpl->tpl_vars['state']->last = $_smarty_tpl->tpl_vars['state']->iteration === $_smarty_tpl->tpl_vars['state']->total;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']["orderStates"]['first'] = $_smarty_tpl->tpl_vars['state']->first;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']["orderStates"]['index']++;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']["orderStates"]['last'] = $_smarty_tpl->tpl_vars['state']->last;
?>
			<tr class="<?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['orderStates']['first']) {?>first_item<?php } elseif ($_smarty_tpl->getVariable('smarty')->value['foreach']['orderStates']['last']) {?>last_item<?php }?> <?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['orderStates']['index']%2) {?>alternate_item<?php } else { ?>item<?php }?>">
				<td class="step-by-step-date"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['dateFormat'][0][0]->dateFormat(array('date'=>$_smarty_tpl->tpl_vars['state']->value['date_add'],'full'=>0),$_smarty_tpl);?>
</td>
				<td><span<?php if (isset($_smarty_tpl->tpl_vars['state']->value['color'])&&$_smarty_tpl->tpl_vars['state']->value['color']) {?> style="background-color:<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['state']->value['color'], ENT_QUOTES, 'UTF-8', true);?>
; border-color:<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['state']->value['color'], ENT_QUOTES, 'UTF-8', true);?>
;"<?php }?> class="label<?php if (isset($_smarty_tpl->tpl_vars['state']->value['color'])&&Tools::getBrightness($_smarty_tpl->tpl_vars['state']->value['color'])>128) {?> dark<?php }?>"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['state']->value['ostate_name'], ENT_QUOTES, 'UTF-8', true);?>
</span></td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
</div>
<?php }?>

<?php if (isset($_smarty_tpl->tpl_vars['followup']->value)) {?>
<p class="bold"><?php echo smartyTranslate(array('s'=>'Click the following link to track the delivery of your order'),$_smarty_tpl);?>
</p>
<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['followup']->value, ENT_QUOTES, 'UTF-8', true);?>
"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['followup']->value, ENT_QUOTES, 'UTF-8', true);?>
</a>
<?php }?>

<div class="adresses_bloc">
	<div class="row">
		<!-- <div class="col-xs-12 col-sm-6"<?php if ($_smarty_tpl->tpl_vars['order']->value->isVirtual()) {?> style="display:none;"<?php }?>>
			<ul class="address alternate_item box">
				<li><h3 class="page-subheading"><?php echo smartyTranslate(array('s'=>'Delivery address'),$_smarty_tpl);?>
 (<?php echo $_smarty_tpl->tpl_vars['address_delivery']->value->alias;?>
)</h3></li>
				<?php  $_smarty_tpl->tpl_vars['field_item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['field_item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['dlv_adr_fields']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['field_item']->key => $_smarty_tpl->tpl_vars['field_item']->value) {
$_smarty_tpl->tpl_vars['field_item']->_loop = true;
?>
					<?php if ($_smarty_tpl->tpl_vars['field_item']->value=="company"&&isset($_smarty_tpl->tpl_vars['address_delivery']->value->company)) {?><li class="address_company"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['address_delivery']->value->company, ENT_QUOTES, 'UTF-8', true);?>
</li>
					<?php } elseif ($_smarty_tpl->tpl_vars['field_item']->value=="address2"&&$_smarty_tpl->tpl_vars['address_delivery']->value->address2) {?><li class="address_address2"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['address_delivery']->value->address2, ENT_QUOTES, 'UTF-8', true);?>
</li>
					<?php } elseif ($_smarty_tpl->tpl_vars['field_item']->value=="phone_mobile"&&$_smarty_tpl->tpl_vars['address_delivery']->value->phone_mobile) {?><li class="address_phone_mobile"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['address_delivery']->value->phone_mobile, ENT_QUOTES, 'UTF-8', true);?>
</li>
					<?php } else { ?>
							<?php $_smarty_tpl->tpl_vars['address_words'] = new Smarty_variable(explode(" ",$_smarty_tpl->tpl_vars['field_item']->value), null, 0);?>
							<li><?php  $_smarty_tpl->tpl_vars['word_item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['word_item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['address_words']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['word_item']->index=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['word_item']->key => $_smarty_tpl->tpl_vars['word_item']->value) {
$_smarty_tpl->tpl_vars['word_item']->_loop = true;
 $_smarty_tpl->tpl_vars['word_item']->index++;
 $_smarty_tpl->tpl_vars['word_item']->first = $_smarty_tpl->tpl_vars['word_item']->index === 0;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']["word_loop"]['first'] = $_smarty_tpl->tpl_vars['word_item']->first;
?><?php if (!$_smarty_tpl->getVariable('smarty')->value['foreach']['word_loop']['first']) {?> <?php }?><span class="address_<?php echo smarty_modifier_replace($_smarty_tpl->tpl_vars['word_item']->value,',','');?>
"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['deliveryAddressFormatedValues']->value[smarty_modifier_replace($_smarty_tpl->tpl_vars['word_item']->value,',','')], ENT_QUOTES, 'UTF-8', true);?>
</span><?php } ?></li>
					<?php }?>
				<?php } ?>
			</ul>
		</div> --><!-- By webkul to hide delivery address of the product -->
		<div class="col-xs-12 col-sm-12">
			<ul class="address item <?php if ($_smarty_tpl->tpl_vars['order']->value->isVirtual()) {?>full_width<?php }?> box">
				<li><h3 class="page-subheading"><?php echo smartyTranslate(array('s'=>'Customer address'),$_smarty_tpl);?>
 (<?php echo $_smarty_tpl->tpl_vars['address_invoice']->value->alias;?>
)</h3></li>
				<?php  $_smarty_tpl->tpl_vars['field_item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['field_item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['inv_adr_fields']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['field_item']->key => $_smarty_tpl->tpl_vars['field_item']->value) {
$_smarty_tpl->tpl_vars['field_item']->_loop = true;
?>
					<?php if ($_smarty_tpl->tpl_vars['field_item']->value=="company"&&isset($_smarty_tpl->tpl_vars['address_invoice']->value->company)) {?><li class="address_company"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['address_invoice']->value->company, ENT_QUOTES, 'UTF-8', true);?>
</li>
					<?php } elseif ($_smarty_tpl->tpl_vars['field_item']->value=="address2"&&$_smarty_tpl->tpl_vars['address_invoice']->value->address2) {?><li class="address_address2"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['address_invoice']->value->address2, ENT_QUOTES, 'UTF-8', true);?>
</li>
					<?php } elseif ($_smarty_tpl->tpl_vars['field_item']->value=="phone_mobile"&&$_smarty_tpl->tpl_vars['address_invoice']->value->phone_mobile) {?><li class="address_phone_mobile"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['address_invoice']->value->phone_mobile, ENT_QUOTES, 'UTF-8', true);?>
</li>
					<?php } else { ?>
							<?php $_smarty_tpl->tpl_vars['address_words'] = new Smarty_variable(explode(" ",$_smarty_tpl->tpl_vars['field_item']->value), null, 0);?>
							<li><?php  $_smarty_tpl->tpl_vars['word_item'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['word_item']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['address_words']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['word_item']->index=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['word_item']->key => $_smarty_tpl->tpl_vars['word_item']->value) {
$_smarty_tpl->tpl_vars['word_item']->_loop = true;
 $_smarty_tpl->tpl_vars['word_item']->index++;
 $_smarty_tpl->tpl_vars['word_item']->first = $_smarty_tpl->tpl_vars['word_item']->index === 0;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']["word_loop"]['first'] = $_smarty_tpl->tpl_vars['word_item']->first;
?><?php if (!$_smarty_tpl->getVariable('smarty')->value['foreach']['word_loop']['first']) {?> <?php }?><span class="address_<?php echo smarty_modifier_replace($_smarty_tpl->tpl_vars['word_item']->value,',','');?>
"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['invoiceAddressFormatedValues']->value[smarty_modifier_replace($_smarty_tpl->tpl_vars['word_item']->value,',','')], ENT_QUOTES, 'UTF-8', true);?>
</span><?php } ?></li>
					<?php }?>
				<?php } ?>
			</ul>
		</div>
	</div>
</div>
<?php echo $_smarty_tpl->tpl_vars['HOOK_ORDERDETAILDISPLAYED']->value;?>

<?php if (!$_smarty_tpl->tpl_vars['is_guest']->value) {?><form action="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getPageLink('order-follow',true), ENT_QUOTES, 'UTF-8', true);?>
" method="post"><?php }?>

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
<div id="order-detail-content" class="table_block table-responsive">
	<table class="table table-bordered">
		<thead>
			<tr>
				<!-- <?php if ($_smarty_tpl->tpl_vars['return_allowed']->value) {?><th class="first_item"><input type="checkbox" /></th><?php }?>
				<th class="<?php if ($_smarty_tpl->tpl_vars['return_allowed']->value) {?>item<?php } else { ?>first_item<?php }?>"><?php echo smartyTranslate(array('s'=>'Reference'),$_smarty_tpl);?>
</th>
				<th class="item"><?php echo smartyTranslate(array('s'=>'Product'),$_smarty_tpl);?>
</th>
				<th class="item"><?php echo smartyTranslate(array('s'=>'Quantity'),$_smarty_tpl);?>
</th>
				<?php if ($_smarty_tpl->tpl_vars['order']->value->hasProductReturned()) {?>
					<th class="item"><?php echo smartyTranslate(array('s'=>'Returned'),$_smarty_tpl);?>
</th>
				<?php }?>
				<th class="item"><?php echo smartyTranslate(array('s'=>'Unit price'),$_smarty_tpl);?>
</th>
				<th class="last_item"><?php echo smartyTranslate(array('s'=>'Total price'),$_smarty_tpl);?>
</th> -->

				<th class="cart_product"><?php echo smartyTranslate(array('s'=>'Room Image'),$_smarty_tpl);?>
</th>
				<th class="cart_description"><?php echo smartyTranslate(array('s'=>'Room Description'),$_smarty_tpl);?>
</th>
				<th><?php echo smartyTranslate(array('s'=>'Room Capcity'),$_smarty_tpl);?>
</th>
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
				<th><?php echo smartyTranslate(array('s'=>'Refund Stage'),$_smarty_tpl);?>
</th>
				<th><?php echo smartyTranslate(array('s'=>'Refund Status'),$_smarty_tpl);?>
</th>
				<th><?php echo smartyTranslate(array('s'=>'BackOrder Status'),$_smarty_tpl);?>
</th>
			</tr>
		</thead>
		<tfoot>
			<?php if ($_smarty_tpl->tpl_vars['priceDisplay']->value&&$_smarty_tpl->tpl_vars['use_tax']->value) {?>
				<tr class="item">
					<td colspan="<?php if ($_smarty_tpl->tpl_vars['return_allowed']->value) {?>2<?php } else { ?>1<?php }?>">
						<strong><?php echo smartyTranslate(array('s'=>'Items (tax excl.)'),$_smarty_tpl);?>
</strong>
					</td>
					<td colspan="<?php if ($_smarty_tpl->tpl_vars['order']->value->hasProductReturned()) {?>5<?php } else { ?>4<?php }?>">
						<span class="price"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayWtPriceWithCurrency'][0][0]->displayWtPriceWithCurrency(array('price'=>$_smarty_tpl->tpl_vars['order']->value->getTotalProductsWithoutTaxes(),'currency'=>$_smarty_tpl->tpl_vars['currency']->value),$_smarty_tpl);?>
</span>
					</td>
				</tr>
			<?php }?>
			<tr class="item">
				<td colspan="<?php if ($_smarty_tpl->tpl_vars['return_allowed']->value) {?>2<?php } else { ?>1<?php }?>">
					<strong><?php echo smartyTranslate(array('s'=>'Items'),$_smarty_tpl);?>
 <?php if ($_smarty_tpl->tpl_vars['use_tax']->value) {?><?php echo smartyTranslate(array('s'=>'(tax incl.)'),$_smarty_tpl);?>
<?php }?> </strong>
				</td>
				<td colspan="<?php if ($_smarty_tpl->tpl_vars['order']->value->hasProductReturned()) {?>5<?php } else { ?>4<?php }?>">
					<span class="price"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayWtPriceWithCurrency'][0][0]->displayWtPriceWithCurrency(array('price'=>$_smarty_tpl->tpl_vars['order']->value->getTotalProductsWithTaxes(),'currency'=>$_smarty_tpl->tpl_vars['currency']->value),$_smarty_tpl);?>
</span>
				</td>
			</tr>
			<?php if ($_smarty_tpl->tpl_vars['order']->value->total_discounts>0) {?>
			<tr class="item">
				<td colspan="<?php if ($_smarty_tpl->tpl_vars['return_allowed']->value) {?>2<?php } else { ?>1<?php }?>">
					<strong><?php echo smartyTranslate(array('s'=>'Total vouchers'),$_smarty_tpl);?>
</strong>
				</td>
				<td colspan="<?php if ($_smarty_tpl->tpl_vars['order']->value->hasProductReturned()) {?>5<?php } else { ?>4<?php }?>">
					<span class="price-discount"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayWtPriceWithCurrency'][0][0]->displayWtPriceWithCurrency(array('price'=>$_smarty_tpl->tpl_vars['order']->value->total_discounts,'currency'=>$_smarty_tpl->tpl_vars['currency']->value,'convert'=>1),$_smarty_tpl);?>
</span>
				</td>
			</tr>
			<?php }?>
			<?php if ($_smarty_tpl->tpl_vars['order']->value->total_wrapping>0) {?>
			<tr class="item">
				<td colspan="<?php if ($_smarty_tpl->tpl_vars['return_allowed']->value) {?>2<?php } else { ?>1<?php }?>">
					<strong><?php echo smartyTranslate(array('s'=>'Total gift wrapping cost'),$_smarty_tpl);?>
</strong>
				</td>
				<td colspan="<?php if ($_smarty_tpl->tpl_vars['order']->value->hasProductReturned()) {?>5<?php } else { ?>4<?php }?>">
					<span class="price-wrapping"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayWtPriceWithCurrency'][0][0]->displayWtPriceWithCurrency(array('price'=>$_smarty_tpl->tpl_vars['order']->value->total_wrapping,'currency'=>$_smarty_tpl->tpl_vars['currency']->value),$_smarty_tpl);?>
</span>
				</td>
			</tr>
			<?php }?>
			
			<?php if (isset($_smarty_tpl->tpl_vars['order_adv_dtl']->value)) {?>
				<tr class="item">
					<td colspan="<?php if ($_smarty_tpl->tpl_vars['return_allowed']->value) {?>2<?php } else { ?>1<?php }?>">
						<strong><?php echo smartyTranslate(array('s'=>'Total Paid'),$_smarty_tpl);?>
</strong>
					</td>
					<td colspan="<?php if ($_smarty_tpl->tpl_vars['order']->value->hasProductReturned()) {?>5<?php } else { ?>4<?php }?>">
						<span class="price"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayWtPriceWithCurrency'][0][0]->displayWtPriceWithCurrency(array('price'=>$_smarty_tpl->tpl_vars['order_adv_dtl']->value['total_paid_amount'],'currency'=>$_smarty_tpl->tpl_vars['currency']->value),$_smarty_tpl);?>
</span>
					</td>
				</tr>
				<tr class="item">
					<td colspan="<?php if ($_smarty_tpl->tpl_vars['return_allowed']->value) {?>2<?php } else { ?>1<?php }?>">
						<strong><?php echo smartyTranslate(array('s'=>'Total Due'),$_smarty_tpl);?>
</strong>
					</td>
					<td colspan="<?php if ($_smarty_tpl->tpl_vars['order']->value->hasProductReturned()) {?>5<?php } else { ?>4<?php }?>">
						<span class="price"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayWtPriceWithCurrency'][0][0]->displayWtPriceWithCurrency(array('price'=>($_smarty_tpl->tpl_vars['order_adv_dtl']->value['total_order_amount']-$_smarty_tpl->tpl_vars['order_adv_dtl']->value['total_paid_amount']),'currency'=>$_smarty_tpl->tpl_vars['currency']->value),$_smarty_tpl);?>
</span>
					</td>
				</tr>
			<?php }?>

			<!-- <tr class="item">
				<td colspan="<?php if ($_smarty_tpl->tpl_vars['return_allowed']->value) {?>2<?php } else { ?>1<?php }?>">
					<strong><?php echo smartyTranslate(array('s'=>'Shipping & handling'),$_smarty_tpl);?>
 <?php if ($_smarty_tpl->tpl_vars['use_tax']->value) {?><?php echo smartyTranslate(array('s'=>'(tax incl.)'),$_smarty_tpl);?>
<?php }?> </strong>
				</td>
				<td colspan="<?php if ($_smarty_tpl->tpl_vars['order']->value->hasProductReturned()) {?>5<?php } else { ?>4<?php }?>">
					<span class="price-shipping"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayWtPriceWithCurrency'][0][0]->displayWtPriceWithCurrency(array('price'=>$_smarty_tpl->tpl_vars['order']->value->total_shipping,'currency'=>$_smarty_tpl->tpl_vars['currency']->value),$_smarty_tpl);?>
</span>
				</td>
			</tr> -->
			<tr class="totalprice item">
				<td colspan="<?php if ($_smarty_tpl->tpl_vars['return_allowed']->value) {?>2<?php } else { ?>1<?php }?>">
					<strong><?php echo smartyTranslate(array('s'=>'Total'),$_smarty_tpl);?>
</strong>
				</td>
				<td colspan="<?php if ($_smarty_tpl->tpl_vars['order']->value->hasProductReturned()) {?>5<?php } else { ?>4<?php }?>">
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
							<td>
								<p class="text-left">
									<?php echo $_smarty_tpl->tpl_vars['data_v']->value['adult'];?>
 <?php echo smartyTranslate(array('s'=>'Adults'),$_smarty_tpl);?>
, <?php echo $_smarty_tpl->tpl_vars['data_v']->value['children'];?>
 <?php echo smartyTranslate(array('s'=>'Children'),$_smarty_tpl);?>

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
							<td class="text-center stage_name">
								<p>
									<?php if (isset($_smarty_tpl->tpl_vars['rm_v']->value['stage_name'])&&$_smarty_tpl->tpl_vars['rm_v']->value['stage_name']) {?>
										<?php echo $_smarty_tpl->tpl_vars['rm_v']->value['stage_name'];?>

									<?php } else { ?>
										--
									<?php }?>
								</p>
							</td>
							<td class="text-center status_name">
								<p>
									<?php if ($_smarty_tpl->tpl_vars['rm_v']->value['stage_name']=='Refunded'||$_smarty_tpl->tpl_vars['rm_v']->value['stage_name']=='Rejected') {?>
										<?php echo smartyTranslate(array('s'=>"Done!"),$_smarty_tpl);?>

									<?php } elseif ($_smarty_tpl->tpl_vars['rm_v']->value['stage_name']=='Waitting'||$_smarty_tpl->tpl_vars['rm_v']->value['stage_name']=='Accepted') {?>
										<?php echo smartyTranslate(array('s'=>"Pending..."),$_smarty_tpl);?>

									<?php } else { ?>
										--
									<?php }?>
								</p>
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

		<!-- <?php  $_smarty_tpl->tpl_vars['product'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['product']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['products']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['products']['index']=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['product']->key => $_smarty_tpl->tpl_vars['product']->value) {
$_smarty_tpl->tpl_vars['product']->_loop = true;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['products']['index']++;
?>
			<?php if (!isset($_smarty_tpl->tpl_vars['product']->value['deleted'])) {?>
				<?php $_smarty_tpl->tpl_vars['productId'] = new Smarty_variable($_smarty_tpl->tpl_vars['product']->value['product_id'], null, 0);?>
				<?php $_smarty_tpl->tpl_vars['productAttributeId'] = new Smarty_variable($_smarty_tpl->tpl_vars['product']->value['product_attribute_id'], null, 0);?>
				<?php if (isset($_smarty_tpl->tpl_vars['product']->value['customizedDatas'])) {?>
					<?php $_smarty_tpl->tpl_vars['productQuantity'] = new Smarty_variable($_smarty_tpl->tpl_vars['product']->value['product_quantity']-$_smarty_tpl->tpl_vars['product']->value['customizationQuantityTotal'], null, 0);?>
				<?php } else { ?>
					<?php $_smarty_tpl->tpl_vars['productQuantity'] = new Smarty_variable($_smarty_tpl->tpl_vars['product']->value['product_quantity'], null, 0);?>
				<?php }?>
				<?php if (isset($_smarty_tpl->tpl_vars['product']->value['customizedDatas'])) {?>
					<tr class="item">
						<?php if ($_smarty_tpl->tpl_vars['return_allowed']->value) {?><td class="order_cb"></td><?php }?>
						<td><label for="cb_<?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_order_detail']);?>
"><?php if ($_smarty_tpl->tpl_vars['product']->value['product_reference']) {?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['product']->value['product_reference'], ENT_QUOTES, 'UTF-8', true);?>
<?php } else { ?>--<?php }?></label></td>
						<td class="bold">
							<label for="cb_<?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_order_detail']);?>
"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['product']->value['product_name'], ENT_QUOTES, 'UTF-8', true);?>
</label>
						</td>
						<td>
						<input class="order_qte_input form-control grey"  name="order_qte_input[<?php echo $_smarty_tpl->getVariable('smarty')->value['foreach']['products']['index'];?>
]" type="text" size="2" value="<?php echo intval($_smarty_tpl->tpl_vars['product']->value['customizationQuantityTotal']);?>
" />
							<div class="clearfix return_quantity_buttons">
								<a href="#" class="return_quantity_down btn btn-default button-minus"><span><i class="icon-minus"></i></span></a>
								<a href="#" class="return_quantity_up btn btn-default button-plus"><span><i class="icon-plus"></i></span></a>
							</div>
							<label for="cb_<?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_order_detail']);?>
"><span class="order_qte_span editable"><?php echo intval($_smarty_tpl->tpl_vars['product']->value['customizationQuantityTotal']);?>
</span></label></td>
						<?php if ($_smarty_tpl->tpl_vars['order']->value->hasProductReturned()) {?>
							<td>
								<?php echo $_smarty_tpl->tpl_vars['product']->value['qty_returned'];?>

							</td>
						<?php }?>
						<td>
							<label class="price" for="cb_<?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_order_detail']);?>
">
								<?php if ($_smarty_tpl->tpl_vars['group_use_tax']->value) {?>
									<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['convertPriceWithCurrency'][0][0]->convertPriceWithCurrency(array('price'=>$_smarty_tpl->tpl_vars['product']->value['unit_price_tax_incl'],'currency'=>$_smarty_tpl->tpl_vars['currency']->value),$_smarty_tpl);?>

								<?php } else { ?>
									<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['convertPriceWithCurrency'][0][0]->convertPriceWithCurrency(array('price'=>$_smarty_tpl->tpl_vars['product']->value['unit_price_tax_excl'],'currency'=>$_smarty_tpl->tpl_vars['currency']->value),$_smarty_tpl);?>

								<?php }?>
							</label>
						</td>
						<td>
							<label class="price" for="cb_<?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_order_detail']);?>
">
								<?php if (isset($_smarty_tpl->tpl_vars['customizedDatas']->value[$_smarty_tpl->tpl_vars['productId']->value][$_smarty_tpl->tpl_vars['productAttributeId']->value])) {?>
									<?php if ($_smarty_tpl->tpl_vars['group_use_tax']->value) {?>
										<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['convertPriceWithCurrency'][0][0]->convertPriceWithCurrency(array('price'=>$_smarty_tpl->tpl_vars['product']->value['total_customization_wt'],'currency'=>$_smarty_tpl->tpl_vars['currency']->value),$_smarty_tpl);?>

									<?php } else { ?>
										<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['convertPriceWithCurrency'][0][0]->convertPriceWithCurrency(array('price'=>$_smarty_tpl->tpl_vars['product']->value['total_customization'],'currency'=>$_smarty_tpl->tpl_vars['currency']->value),$_smarty_tpl);?>

									<?php }?>
								<?php } else { ?>
									<?php if ($_smarty_tpl->tpl_vars['group_use_tax']->value) {?>
										<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['convertPriceWithCurrency'][0][0]->convertPriceWithCurrency(array('price'=>$_smarty_tpl->tpl_vars['product']->value['total_price_tax_incl'],'currency'=>$_smarty_tpl->tpl_vars['currency']->value),$_smarty_tpl);?>

									<?php } else { ?>
										<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['convertPriceWithCurrency'][0][0]->convertPriceWithCurrency(array('price'=>$_smarty_tpl->tpl_vars['product']->value['total_price_tax_excl'],'currency'=>$_smarty_tpl->tpl_vars['currency']->value),$_smarty_tpl);?>

									<?php }?>
								<?php }?>
							</label>
						</td>
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
						<tr class="alternate_item">
							<?php if ($_smarty_tpl->tpl_vars['return_allowed']->value) {?><td class="order_cb"><input type="checkbox" id="cb_<?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_order_detail']);?>
" name="customization_ids[<?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_order_detail']);?>
][]" value="<?php echo intval($_smarty_tpl->tpl_vars['customizationId']->value);?>
" /></td><?php }?>
							<td colspan="2">
							<?php  $_smarty_tpl->tpl_vars['datas'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['datas']->_loop = false;
 $_smarty_tpl->tpl_vars['type'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['customization']->value['datas']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['datas']->key => $_smarty_tpl->tpl_vars['datas']->value) {
$_smarty_tpl->tpl_vars['datas']->_loop = true;
 $_smarty_tpl->tpl_vars['type']->value = $_smarty_tpl->tpl_vars['datas']->key;
?>
								<?php if ($_smarty_tpl->tpl_vars['type']->value==$_smarty_tpl->tpl_vars['CUSTOMIZE_FILE']->value) {?>
								<ul class="customizationUploaded">
									<?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['data']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['datas']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value) {
$_smarty_tpl->tpl_vars['data']->_loop = true;
?>
										<li><img src="<?php echo $_smarty_tpl->tpl_vars['pic_dir']->value;?>
<?php echo $_smarty_tpl->tpl_vars['data']->value['value'];?>
_small" alt="" class="customizationUploaded" /></li>
									<?php } ?>
								</ul>
								<?php } elseif ($_smarty_tpl->tpl_vars['type']->value==$_smarty_tpl->tpl_vars['CUSTOMIZE_TEXTFIELD']->value) {?>
								<ul class="typedText"><?php echo smarty_function_counter(array('start'=>0,'print'=>false),$_smarty_tpl);?>

									<?php  $_smarty_tpl->tpl_vars['data'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['data']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['datas']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['data']->key => $_smarty_tpl->tpl_vars['data']->value) {
$_smarty_tpl->tpl_vars['data']->_loop = true;
?>
										<?php $_smarty_tpl->tpl_vars['customizationFieldName'] = new Smarty_variable(("Text #").($_smarty_tpl->tpl_vars['data']->value['id_customization_field']), null, 0);?>
										<li><?php echo (($tmp = @$_smarty_tpl->tpl_vars['data']->value['name'])===null||$tmp==='' ? $_smarty_tpl->tpl_vars['customizationFieldName']->value : $tmp);?>
 : <?php echo $_smarty_tpl->tpl_vars['data']->value['value'];?>
</li>
									<?php } ?>
								</ul>
								<?php }?>
							<?php } ?>
							</td>
							<td>
								<input class="order_qte_input form-control grey" name="customization_qty_input[<?php echo intval($_smarty_tpl->tpl_vars['customizationId']->value);?>
]" type="text" size="2" value="<?php echo intval($_smarty_tpl->tpl_vars['customization']->value['quantity']);?>
" />
								<div class="clearfix return_quantity_buttons">
									<a href="#" class="return_quantity_down btn btn-default button-minus"><span><i class="icon-minus"></i></span></a>
									<a href="#" class="return_quantity_up btn btn-default button-plus"><span><i class="icon-plus"></i></span></a>
								</div>
								<label for="cb_<?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_order_detail']);?>
"><span class="order_qte_span editable"><?php echo intval($_smarty_tpl->tpl_vars['customization']->value['quantity']);?>
</span></label>
							</td>
							<td colspan="2"></td>
						</tr>
						<?php } ?>
					<?php } ?>
				<?php }?>
				<?php if ($_smarty_tpl->tpl_vars['product']->value['product_quantity']>$_smarty_tpl->tpl_vars['product']->value['customizationQuantityTotal']) {?>
					<tr class="item">
						<?php if ($_smarty_tpl->tpl_vars['return_allowed']->value) {?><td class="order_cb"><input type="checkbox" id="cb_<?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_order_detail']);?>
" name="ids_order_detail[<?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_order_detail']);?>
]" value="<?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_order_detail']);?>
" /></td><?php }?>
						<td><label for="cb_<?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_order_detail']);?>
"><?php if ($_smarty_tpl->tpl_vars['product']->value['product_reference']) {?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['product']->value['product_reference'], ENT_QUOTES, 'UTF-8', true);?>
<?php } else { ?>--<?php }?></label></td>
						<td class="bold">
							<label for="cb_<?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_order_detail']);?>
">
								<?php if ($_smarty_tpl->tpl_vars['product']->value['download_hash']&&$_smarty_tpl->tpl_vars['logable']->value&&$_smarty_tpl->tpl_vars['product']->value['display_filename']!=''&&$_smarty_tpl->tpl_vars['product']->value['product_quantity_refunded']==0&&$_smarty_tpl->tpl_vars['product']->value['product_quantity_return']==0) {?>
									<?php if (isset($_smarty_tpl->tpl_vars['is_guest']->value)&&$_smarty_tpl->tpl_vars['is_guest']->value) {?>
									<a href="<?php ob_start();?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['product']->value['filename'], ENT_QUOTES, 'UTF-8', true);?>
<?php $_tmp1=ob_get_clean();?><?php ob_start();?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['product']->value['download_hash'], ENT_QUOTES, 'UTF-8', true);?>
<?php $_tmp2=ob_get_clean();?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getPageLink('get-file',true,null,"key=".$_tmp1."-".$_tmp2."&amp;id_order=".((string)$_smarty_tpl->tpl_vars['order']->value->id)."&secure_key=".((string)$_smarty_tpl->tpl_vars['order']->value->secure_key)), ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo smartyTranslate(array('s'=>'Download this product'),$_smarty_tpl);?>
">
									<?php } else { ?>
										<a href="<?php ob_start();?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['product']->value['filename'], ENT_QUOTES, 'UTF-8', true);?>
<?php $_tmp3=ob_get_clean();?><?php ob_start();?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['product']->value['download_hash'], ENT_QUOTES, 'UTF-8', true);?>
<?php $_tmp4=ob_get_clean();?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getPageLink('get-file',true,null,"key=".$_tmp3."-".$_tmp4), ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo smartyTranslate(array('s'=>'Download this product'),$_smarty_tpl);?>
">
									<?php }?>
										<img src="<?php echo $_smarty_tpl->tpl_vars['img_dir']->value;?>
icon/download_product.gif" class="icon" alt="<?php echo smartyTranslate(array('s'=>'Download product'),$_smarty_tpl);?>
" />
									</a>
									<?php if (isset($_smarty_tpl->tpl_vars['is_guest']->value)&&$_smarty_tpl->tpl_vars['is_guest']->value) {?>
										<a href="<?php ob_start();?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['product']->value['filename'], ENT_QUOTES, 'UTF-8', true);?>
<?php $_tmp5=ob_get_clean();?><?php ob_start();?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['product']->value['download_hash'], ENT_QUOTES, 'UTF-8', true);?>
<?php $_tmp6=ob_get_clean();?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getPageLink('get-file',true,null,"key=".$_tmp5."-".$_tmp6."&id_order=".((string)$_smarty_tpl->tpl_vars['order']->value->id)."&secure_key=".((string)$_smarty_tpl->tpl_vars['order']->value->secure_key)), ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo smartyTranslate(array('s'=>'Download this product'),$_smarty_tpl);?>
"> <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['product']->value['product_name'], ENT_QUOTES, 'UTF-8', true);?>
 	</a>
									<?php } else { ?>
									<a href="<?php ob_start();?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['product']->value['filename'], ENT_QUOTES, 'UTF-8', true);?>
<?php $_tmp7=ob_get_clean();?><?php ob_start();?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['product']->value['download_hash'], ENT_QUOTES, 'UTF-8', true);?>
<?php $_tmp8=ob_get_clean();?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getPageLink('get-file',true,null,"key=".$_tmp7."-".$_tmp8), ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo smartyTranslate(array('s'=>'Download this product'),$_smarty_tpl);?>
"> <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['product']->value['product_name'], ENT_QUOTES, 'UTF-8', true);?>
 	</a>
									<?php }?>
								<?php } else { ?>
									<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['product']->value['product_name'], ENT_QUOTES, 'UTF-8', true);?>

								<?php }?>
							</label>
						</td>
						<td class="return_quantity">
							<input class="order_qte_input form-control grey" name="order_qte_input[<?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_order_detail']);?>
]" type="text" size="2" value="<?php echo intval($_smarty_tpl->tpl_vars['productQuantity']->value);?>
" />
							<div class="clearfix return_quantity_buttons">
								<a href="#" class="return_quantity_down btn btn-default button-minus"><span><i class="icon-minus"></i></span></a>
								<a href="#" class="return_quantity_up btn btn-default button-plus"><span><i class="icon-plus"></i></span></a>
							</div>
							<label for="cb_<?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_order_detail']);?>
"><span class="order_qte_span editable"><?php echo intval($_smarty_tpl->tpl_vars['productQuantity']->value);?>
</span></label></td>
						<?php if ($_smarty_tpl->tpl_vars['order']->value->hasProductReturned()) {?>
							<td>
								<?php echo $_smarty_tpl->tpl_vars['product']->value['qty_returned'];?>

							</td>
						<?php }?>
						<td class="price">
							<label for="cb_<?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_order_detail']);?>
">
							<?php if ($_smarty_tpl->tpl_vars['group_use_tax']->value) {?>
								<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['convertPriceWithCurrency'][0][0]->convertPriceWithCurrency(array('price'=>$_smarty_tpl->tpl_vars['product']->value['unit_price_tax_incl'],'currency'=>$_smarty_tpl->tpl_vars['currency']->value),$_smarty_tpl);?>

							<?php } else { ?>
								<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['convertPriceWithCurrency'][0][0]->convertPriceWithCurrency(array('price'=>$_smarty_tpl->tpl_vars['product']->value['unit_price_tax_excl'],'currency'=>$_smarty_tpl->tpl_vars['currency']->value),$_smarty_tpl);?>

							<?php }?>
							</label>
						</td>
						<td class="price">
							<label for="cb_<?php echo intval($_smarty_tpl->tpl_vars['product']->value['id_order_detail']);?>
">
							<?php if ($_smarty_tpl->tpl_vars['group_use_tax']->value) {?>
								<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['convertPriceWithCurrency'][0][0]->convertPriceWithCurrency(array('price'=>$_smarty_tpl->tpl_vars['product']->value['total_price_tax_incl'],'currency'=>$_smarty_tpl->tpl_vars['currency']->value),$_smarty_tpl);?>

							<?php } else { ?>
								<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['convertPriceWithCurrency'][0][0]->convertPriceWithCurrency(array('price'=>$_smarty_tpl->tpl_vars['product']->value['total_price_tax_excl'],'currency'=>$_smarty_tpl->tpl_vars['currency']->value),$_smarty_tpl);?>

							<?php }?>
							</label>
						</td>
					</tr>
				<?php }?>
			<?php }?>
		<?php } ?> -->
		<?php  $_smarty_tpl->tpl_vars['discount'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['discount']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['discounts']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['discount']->key => $_smarty_tpl->tpl_vars['discount']->value) {
$_smarty_tpl->tpl_vars['discount']->_loop = true;
?>
			<tr class="item">
				<td class="text-center"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['discount']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
</td>
				<td class="text-center"><?php echo smartyTranslate(array('s'=>'Voucher'),$_smarty_tpl);?>
 <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['discount']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
</td>
				<td class="text-center" colspan="2">&nbsp;</td>
				<td class="text-center"><span class="order_qte_span editable">1</span></td>
				<td class="text-center" colspan="2">&nbsp;</td>
				<td class="text-center"><?php if ($_smarty_tpl->tpl_vars['discount']->value['value']!=0.00) {?>-<?php }?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['convertPriceWithCurrency'][0][0]->convertPriceWithCurrency(array('price'=>$_smarty_tpl->tpl_vars['discount']->value['value'],'currency'=>$_smarty_tpl->tpl_vars['currency']->value),$_smarty_tpl);?>
</td>
				<?php if ($_smarty_tpl->tpl_vars['return_allowed']->value) {?>
				<td>&nbsp;</td>
				<?php }?>
			</tr>
		<?php } ?>
		</tbody>
	</table>
</div>
<?php if ($_smarty_tpl->tpl_vars['any_back_order']->value) {?>
	<?php if ($_smarty_tpl->tpl_vars['shw_bo_msg']->value) {?>
		<p class="back_o_msg"><strong><sup>*</sup><?php echo smartyTranslate(array('s'=>'Some of your rooms are on back order. Please read the following message for rooms with status on backorder'),$_smarty_tpl);?>
</strong></p>
		<p>
			-&nbsp;&nbsp;<?php echo $_smarty_tpl->tpl_vars['back_ord_msg']->value;?>

		</p>
	<?php }?>
<?php }?>
<?php if ($_smarty_tpl->tpl_vars['return_allowed']->value) {?>
	<div id="returnOrderMessage">
		<h3 class="page-heading bottom-indent"><?php echo smartyTranslate(array('s'=>'Merchandise return'),$_smarty_tpl);?>
</h3>
		<p><?php echo smartyTranslate(array('s'=>'If you wish to return one or more products, please mark the corresponding boxes and provide an explanation for the return. When complete, click the button below.'),$_smarty_tpl);?>
</p>
		<p class="form-group">
			<textarea class="form-control" cols="67" rows="3" name="returnText"></textarea>
		</p>
		<p class="form-group">
			<button type="submit" name="submitReturnMerchandise" class="btn btn-default button button-small"><span><?php echo smartyTranslate(array('s'=>'Make an RMA slip'),$_smarty_tpl);?>
<i class="icon-chevron-right right"></i></span></button>
			<input type="hidden" class="hidden" value="<?php echo intval($_smarty_tpl->tpl_vars['order']->value->id);?>
" name="id_order" />
		</p>
	</div>
<?php }?>
<?php if (!$_smarty_tpl->tpl_vars['is_guest']->value) {?></form><?php }?>
<?php $_smarty_tpl->tpl_vars['carriers'] = new Smarty_variable($_smarty_tpl->tpl_vars['order']->value->getShipping(), null, 0);?>
<?php if (count($_smarty_tpl->tpl_vars['carriers']->value)>0&&isset($_smarty_tpl->tpl_vars['carriers']->value[0]['carrier_name'])&&$_smarty_tpl->tpl_vars['carriers']->value[0]['carrier_name']) {?>
	<table class="table table-bordered footab">
		<thead>
			<tr>
				<th class="first_item"><?php echo smartyTranslate(array('s'=>'Date'),$_smarty_tpl);?>
</th>
				<th class="item" data-sort-ignore="true"><?php echo smartyTranslate(array('s'=>'Carrier'),$_smarty_tpl);?>
</th>
				<th data-hide="phone" class="item"><?php echo smartyTranslate(array('s'=>'Weight'),$_smarty_tpl);?>
</th>
				<th data-hide="phone" class="item"><?php echo smartyTranslate(array('s'=>'Shipping cost'),$_smarty_tpl);?>
</th>
				<th data-hide="phone" class="last_item" data-sort-ignore="true"><?php echo smartyTranslate(array('s'=>'Tracking number'),$_smarty_tpl);?>
</th>
			</tr>
		</thead>
		<tbody>
			<?php  $_smarty_tpl->tpl_vars['line'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['line']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['carriers']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['line']->key => $_smarty_tpl->tpl_vars['line']->value) {
$_smarty_tpl->tpl_vars['line']->_loop = true;
?>
			<tr class="item">
				<td data-value="<?php echo smarty_modifier_regex_replace($_smarty_tpl->tpl_vars['line']->value['date_add'],"/[\-\:\ ]/",'');?>
"><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['dateFormat'][0][0]->dateFormat(array('date'=>$_smarty_tpl->tpl_vars['line']->value['date_add'],'full'=>0),$_smarty_tpl);?>
</td>
				<td><?php echo $_smarty_tpl->tpl_vars['line']->value['carrier_name'];?>
</td>
				<td data-value="<?php if ($_smarty_tpl->tpl_vars['line']->value['weight']>0) {?><?php echo sprintf("%.3f",$_smarty_tpl->tpl_vars['line']->value['weight']);?>
<?php } else { ?>0<?php }?>"><?php if ($_smarty_tpl->tpl_vars['line']->value['weight']>0) {?><?php echo sprintf("%.3f",$_smarty_tpl->tpl_vars['line']->value['weight']);?>
 <?php echo Configuration::get('PS_WEIGHT_UNIT');?>
<?php } else { ?>-<?php }?></td>
				<td data-value="<?php if ($_smarty_tpl->tpl_vars['order']->value->getTaxCalculationMethod()==@constant('PS_TAX_INC')) {?><?php echo $_smarty_tpl->tpl_vars['line']->value['shipping_cost_tax_incl'];?>
<?php } else { ?><?php echo $_smarty_tpl->tpl_vars['line']->value['shipping_cost_tax_excl'];?>
<?php }?>"><?php if ($_smarty_tpl->tpl_vars['order']->value->getTaxCalculationMethod()==@constant('PS_TAX_INC')) {?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['line']->value['shipping_cost_tax_incl'],'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>
<?php } else { ?><?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['displayPrice'][0][0]->displayPriceSmarty(array('price'=>$_smarty_tpl->tpl_vars['line']->value['shipping_cost_tax_excl'],'currency'=>$_smarty_tpl->tpl_vars['currency']->value->id),$_smarty_tpl);?>
<?php }?></td>
				<td>
					<span class="shipping_number_show"><?php if ($_smarty_tpl->tpl_vars['line']->value['tracking_number']) {?><?php if ($_smarty_tpl->tpl_vars['line']->value['url']&&$_smarty_tpl->tpl_vars['line']->value['tracking_number']) {?><a href="<?php echo smarty_modifier_replace($_smarty_tpl->tpl_vars['line']->value['url'],'@',$_smarty_tpl->tpl_vars['line']->value['tracking_number']);?>
"><?php echo $_smarty_tpl->tpl_vars['line']->value['tracking_number'];?>
</a><?php } else { ?><?php echo $_smarty_tpl->tpl_vars['line']->value['tracking_number'];?>
<?php }?><?php } else { ?>-<?php }?></span>
				</td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
<?php }?>
<?php if (!$_smarty_tpl->tpl_vars['is_guest']->value) {?>
	<?php if (count($_smarty_tpl->tpl_vars['messages']->value)) {?>
	<h3 class="page-heading"><?php echo smartyTranslate(array('s'=>'Messages'),$_smarty_tpl);?>
</h3>
	 <div class="table_block">
		<table class="detail_step_by_step table table-bordered">
			<thead>
				<tr>
					<th class="first_item" style="width:150px;"><?php echo smartyTranslate(array('s'=>'From'),$_smarty_tpl);?>
</th>
					<th class="last_item"><?php echo smartyTranslate(array('s'=>'Message'),$_smarty_tpl);?>
</th>
				</tr>
			</thead>
			<tbody>
			<?php  $_smarty_tpl->tpl_vars['message'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['message']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['messages']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['message']->total= $_smarty_tpl->_count($_from);
 $_smarty_tpl->tpl_vars['message']->iteration=0;
 $_smarty_tpl->tpl_vars['message']->index=-1;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']["messageList"]['index']=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['message']->key => $_smarty_tpl->tpl_vars['message']->value) {
$_smarty_tpl->tpl_vars['message']->_loop = true;
 $_smarty_tpl->tpl_vars['message']->iteration++;
 $_smarty_tpl->tpl_vars['message']->index++;
 $_smarty_tpl->tpl_vars['message']->first = $_smarty_tpl->tpl_vars['message']->index === 0;
 $_smarty_tpl->tpl_vars['message']->last = $_smarty_tpl->tpl_vars['message']->iteration === $_smarty_tpl->tpl_vars['message']->total;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']["messageList"]['first'] = $_smarty_tpl->tpl_vars['message']->first;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']["messageList"]['index']++;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']["messageList"]['last'] = $_smarty_tpl->tpl_vars['message']->last;
?>
				<tr class="<?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['messageList']['first']) {?>first_item<?php } elseif ($_smarty_tpl->getVariable('smarty')->value['foreach']['messageList']['last']) {?>last_item<?php }?> <?php if ($_smarty_tpl->getVariable('smarty')->value['foreach']['messageList']['index']%2) {?>alternate_item<?php } else { ?>item<?php }?>">
					<td>
						<strong class="dark">
							<?php if (isset($_smarty_tpl->tpl_vars['message']->value['elastname'])&&$_smarty_tpl->tpl_vars['message']->value['elastname']) {?>
								<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['message']->value['efirstname'], ENT_QUOTES, 'UTF-8', true);?>
 <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['message']->value['elastname'], ENT_QUOTES, 'UTF-8', true);?>

							<?php } elseif ($_smarty_tpl->tpl_vars['message']->value['clastname']) {?>
								<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['message']->value['cfirstname'], ENT_QUOTES, 'UTF-8', true);?>
 <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['message']->value['clastname'], ENT_QUOTES, 'UTF-8', true);?>

							<?php } else { ?>
								<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shop_name']->value, ENT_QUOTES, 'UTF-8', true);?>

							<?php }?>
						</strong>
						<br />
						<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['dateFormat'][0][0]->dateFormat(array('date'=>$_smarty_tpl->tpl_vars['message']->value['date_add'],'full'=>1),$_smarty_tpl);?>

					</td>
					<td><?php echo nl2br(htmlspecialchars($_smarty_tpl->tpl_vars['message']->value['message'], ENT_QUOTES, 'UTF-8', true));?>
</td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
	</div>
	<?php }?>
	<?php if (isset($_smarty_tpl->tpl_vars['errors']->value)&&$_smarty_tpl->tpl_vars['errors']->value) {?>
		<div class="alert alert-danger">
			<p><?php if (count($_smarty_tpl->tpl_vars['errors']->value)>1) {?><?php echo smartyTranslate(array('s'=>'There are %d errors','sprintf'=>count($_smarty_tpl->tpl_vars['errors']->value)),$_smarty_tpl);?>
<?php } else { ?><?php echo smartyTranslate(array('s'=>'There is %d error','sprintf'=>count($_smarty_tpl->tpl_vars['errors']->value)),$_smarty_tpl);?>
<?php }?></p>
			<ol>
			<?php  $_smarty_tpl->tpl_vars['error'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['error']->_loop = false;
 $_smarty_tpl->tpl_vars['k'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['errors']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['error']->key => $_smarty_tpl->tpl_vars['error']->value) {
$_smarty_tpl->tpl_vars['error']->_loop = true;
 $_smarty_tpl->tpl_vars['k']->value = $_smarty_tpl->tpl_vars['error']->key;
?>
				<li><?php echo $_smarty_tpl->tpl_vars['error']->value;?>
</li>
			<?php } ?>
			</ol>
		</div>
	<?php }?>
	<?php if (isset($_smarty_tpl->tpl_vars['message_confirmation']->value)&&$_smarty_tpl->tpl_vars['message_confirmation']->value) {?>
	<p class="alert alert-success">
		<?php echo smartyTranslate(array('s'=>'Message successfully sent'),$_smarty_tpl);?>

	</p>
	<?php }?>
	<form action="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getPageLink('order-detail',true), ENT_QUOTES, 'UTF-8', true);?>
" method="post" class="std" id="sendOrderMessage">
		<h3 class="page-heading bottom-indent"><?php echo smartyTranslate(array('s'=>'Add a message'),$_smarty_tpl);?>
</h3>
		<p><?php echo smartyTranslate(array('s'=>'If you would like to add a comment about your order, please write it in the field below.'),$_smarty_tpl);?>
</p>
		<p class="form-group">
		<label for="id_product"><?php echo smartyTranslate(array('s'=>'Product'),$_smarty_tpl);?>
</label>
			<select name="id_product" class="form-control">
				<option value="0"><?php echo smartyTranslate(array('s'=>'-- Choose --'),$_smarty_tpl);?>
</option>
				<?php  $_smarty_tpl->tpl_vars['product'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['product']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['products']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['products']['index']=-1;
foreach ($_from as $_smarty_tpl->tpl_vars['product']->key => $_smarty_tpl->tpl_vars['product']->value) {
$_smarty_tpl->tpl_vars['product']->_loop = true;
 $_smarty_tpl->tpl_vars['smarty']->value['foreach']['products']['index']++;
?>
					<option value="<?php echo $_smarty_tpl->tpl_vars['product']->value['product_id'];?>
"><?php echo $_smarty_tpl->tpl_vars['product']->value['product_name'];?>
</option>
				<?php } ?>
			</select>
		</p>
		<p class="form-group">
			<textarea class="form-control" cols="67" rows="3" name="msgText"></textarea>
		</p>
		<div class="submit">
			<input type="hidden" name="id_order" value="<?php echo intval($_smarty_tpl->tpl_vars['order']->value->id);?>
" />
			<input type="submit" class="unvisible" name="submitMessage" value="<?php echo smartyTranslate(array('s'=>'Send'),$_smarty_tpl);?>
"/>
			<button type="submit" name="submitMessage" class="button btn btn-default button-medium"><span><?php echo smartyTranslate(array('s'=>'Send'),$_smarty_tpl);?>
<i class="icon-chevron-right right"></i></span></button>
		</div>
	</form>
<?php } else { ?>
<p class="alert alert-info"><i class="icon-info-sign"></i><?php echo smartyTranslate(array('s'=>'You cannot return merchandise with a guest account'),$_smarty_tpl);?>
</p>
<?php }?>
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
<?php }} ?>
