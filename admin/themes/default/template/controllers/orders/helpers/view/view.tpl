{*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{extends file="helpers/view/view.tpl"}

{block name="override_tpl"}
	<script type="text/javascript">
	var admin_order_tab_link = "{$link->getAdminLink('AdminOrders')|addslashes}";
	var id_order = {$order->id};
	var id_lang = {$current_id_lang};
	var id_currency = {$order->id_currency};
	var id_customer = {$order->id_customer|intval};
	{assign var=PS_TAX_ADDRESS_TYPE value=Configuration::get('PS_TAX_ADDRESS_TYPE')}
	var id_address = {$order->$PS_TAX_ADDRESS_TYPE};
	var currency_sign = "{$currency->sign}";
	var currency_format = "{$currency->format}";
	var currency_blank = "{$currency->blank}";
	var priceDisplayPrecision = {$smarty.const._PS_PRICE_DISPLAY_PRECISION_|intval};
	var use_taxes = {if $order->getTaxCalculationMethod() == $smarty.const.PS_TAX_INC}true{else}false{/if};
	var stock_management = {$stock_management|intval};
	var txt_add_product_stock_issue = "{l s='Are you sure you want to add this quantity?' js=1}";
	var txt_add_product_new_invoice = "{l s='Are you sure you want to create a new invoice?' js=1}";
	var txt_add_product_no_product = "{l s='Error: No product has been selected' js=1}";
	var txt_add_product_no_product_quantity = "{l s='Error: Quantity of products must be set' js=1}";
	var txt_add_product_no_product_price = "{l s='Error: Product price must be set' js=1}";
	var txt_confirm = "{l s='Are you sure?' js=1}";
	var statesShipped = new Array();
	var has_voucher = {if count($discounts)}1{else}0{/if};
	{foreach from=$states item=state}
		{if (isset($currentState->shipped) && !$currentState->shipped && $state['shipped'])}
			statesShipped.push({$state['id_order_state']});
		{/if}
	{/foreach}
	var order_discount_price = {if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)}
									{$order->total_discounts_tax_excl}
								{else}
									{$order->total_discounts_tax_incl}
								{/if};

	var errorRefund = "{l s='Error. You cannot refund a negative amount.'}";
	</script>

	{assign var="hook_invoice" value={hook h="displayInvoice" id_order=$order->id}}
	{if ($hook_invoice)}
	<div>{$hook_invoice}</div>
	{/if}

	<div class="panel kpi-container">
		<div class="row">
			<div class="col-xs-6 col-sm-3 box-stats color3" >
				<div class="kpi-content">
					<i class="icon-calendar-empty"></i>
					<span class="title">{l s='Date'}</span>
					<span class="value">{dateFormat date=$order->date_add full=false}</span>
				</div>
			</div>
			<div class="col-xs-6 col-sm-3 box-stats color4" >
				<div class="kpi-content">
					<i class="icon-money"></i>
					<span class="title">{l s='Total'}</span>
					<span class="value">{displayPrice price=$order->total_paid_tax_incl currency=$currency->id}</span>
				</div>
			</div>
			<div class="col-xs-6 col-sm-3 box-stats color2" >
				<div class="kpi-content">
					<i class="icon-comments"></i>
					<span class="title">{l s='Messages'}</span>
					<span class="value"><a href="{$link->getAdminLink('AdminCustomerThreads')|escape:'html':'UTF-8'}&amp;id_order={$order->id|intval}">{sizeof($customer_thread_message)}</a></span>
				</div>
			</div>
			<div class="col-xs-6 col-sm-3 box-stats color1" >
				<a href="#start_products">
					<div class="kpi-content">
						<i class="icon icon-home"></i>
						<!-- Original -->
						<!-- <span class="title">{l s='Total'}</span>
						<span class="value">{sizeof($products)}</span> -->
						<span class="title">{l s='Total Rooms'}</span>
						<span class="value">{$order_detail_data|@count}</span>
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
					{l s='Order'}
					<span class="badge">{$order->reference}</span>
					<span class="badge">{l s="#"}{$order->id}</span>
					<div class="panel-heading-action">
						<div class="btn-group">
							<a class="btn btn-default{if !$previousOrder} disabled{/if}" href="{$link->getAdminLink('AdminOrders')|escape:'html':'UTF-8'}&amp;vieworder&amp;id_order={$previousOrder|intval}">
								<i class="icon-backward"></i>
							</a>
							<a class="btn btn-default{if !$nextOrder} disabled{/if}" href="{$link->getAdminLink('AdminOrders')|escape:'html':'UTF-8'}&amp;vieworder&amp;id_order={$nextOrder|intval}">
								<i class="icon-forward"></i>
							</a>
						</div>
					</div>
				</div>
				<!-- Orders Actions -->
				<div class="well hidden-print">
					<a class="btn btn-default" href="javascript:window.print()">
						<i class="icon-print"></i>
						{l s='Print order'}
					</a>
					&nbsp;
					{if Configuration::get('PS_INVOICE') && count($invoices_collection) && $order->invoice_number}
						<a data-selenium-id="view_invoice" class="btn btn-default _blank" href="{$link->getAdminLink('AdminPdf')|escape:'html':'UTF-8'}&amp;submitAction=generateInvoicePDF&amp;id_order={$order->id|intval}">
							<i class="icon-file"></i>
							{l s='View invoice'}
						</a>
					{else}
						<span class="span label label-inactive">
							<i class="icon-remove"></i>
							{l s='No invoice'}
						</span>
					{/if}
					&nbsp;

					<!-- By webkul to hide unneccessary data on order detail page -->
					<!-- {if $order->delivery_number}
						<a class="btn btn-default _blank"  href="{$link->getAdminLink('AdminPdf')|escape:'html':'UTF-8'}&amp;submitAction=generateDeliverySlipPDF&amp;id_order={$order->id|intval}">
							<i class="icon-truck"></i>
							{l s='View delivery slip'}
						</a>
					{else}
						<span class="span label label-inactive">
							<i class="icon-remove"></i>
							{l s='No delivery slip'}
						</span>
					{/if} -->
					<!-- End -->
					&nbsp;
					{if $refund_allowed && !$hasCompletelyRefunded}
						<a id="desc-order-standard_refund" class="btn btn-default" href="#refundForm">
							<i class="icon-exchange"></i>
							{if $order->hasBeenPaid()}
								{l s='Initiate refund'}
							{else}
								{l s='Cancel bookings'}
							{/if}
						</a>
						&nbsp;
					{/if}
				</div>
				<!-- Tab nav -->
				<ul class="nav nav-tabs" id="tabOrder">
					{$HOOK_TAB_ORDER}
					<li class="active">
						<a href="#status">
							<i class="icon-time"></i>
							{l s='Status'} <span class="badge">{$history|@count}</span>
						</a>
					</li>
					<li>
						<a href="#documents">
							<i class="icon-file-text"></i>
							{l s='Documents'} <span class="badge">{$order->getDocuments()|@count}</span>
						</a>
					</li>
				</ul>
				<!-- Tab content -->
				<div class="tab-content panel">
					{$HOOK_CONTENT_ORDER}
					<!-- Tab status -->
					<div class="tab-pane active" id="status">
						<h4 class="visible-print">{l s='Status'} <span class="badge">({$history|@count})</span></h4>
						<!-- History of status -->
						<div class="table-responsive">
							<table class="table history-status row-margin-bottom">
								<tbody>
									{foreach from=$history item=row key=key}
										{if ($key == 0)}
											<tr>
												<td style="background-color:{$row['color']}"><img src="{$link->getMediaLink("`$img_dir`os/`$row['id_order_state']|intval`.gif")}" width="16" height="16" alt="{$row['ostate_name']|stripslashes}" /></td> {* by webkul to get media link *}
												<td style="background-color:{$row['color']};color:{$row['text-color']}">{$row['ostate_name']|stripslashes}</td>
												<td style="background-color:{$row['color']};color:{$row['text-color']}">{if $row['employee_lastname']}{$row['employee_firstname']|stripslashes} {$row['employee_lastname']|stripslashes}{/if}</td>
												<td style="background-color:{$row['color']};color:{$row['text-color']}">{dateFormat date=$row['date_add'] full=true}</td>
												<td style="background-color:{$row['color']};color:{$row['text-color']}" class="text-right">
													{if $row['send_email']|intval}
														<a class="btn btn-default" href="{$link->getAdminLink('AdminOrders')|escape:'html':'UTF-8'}&amp;vieworder&amp;id_order={$order->id|intval}&amp;sendStateEmail={$row['id_order_state']|intval}&amp;id_order_history={$row['id_order_history']|intval}" title="{l s='Resend this email to the customer'}">
															<i class="icon-mail-reply"></i>
															{l s='Resend email'}
														</a>
													{/if}
												</td>
											</tr>
										{else}
											<tr>
												<td><img src="{$link->getMediaLink("`$img_dir`os/`$row['id_order_state']|intval`.gif")}" width="16" height="16" /></td>
												<td>{$row['ostate_name']|stripslashes}</td>
												<td>{if $row['employee_lastname']}{$row['employee_firstname']|stripslashes} {$row['employee_lastname']|stripslashes}{else}&nbsp;{/if}</td>
												<td>{dateFormat date=$row['date_add'] full=true}</td>
												<td class="text-right">
													{if $row['send_email']|intval}
														<a class="btn btn-default" href="{$link->getAdminLink('AdminOrders')|escape:'html':'UTF-8'}&amp;vieworder&amp;id_order={$order->id|intval}&amp;sendStateEmail={$row['id_order_state']|intval}&amp;id_order_history={$row['id_order_history']|intval}" title="{l s='Resend this email to the customer'}">
															<i class="icon-mail-reply"></i>
															{l s='Resend email'}
														</a>
													{/if}
												</td>
											</tr>
										{/if}
									{/foreach}
								</tbody>
							</table>
						</div>
						<!-- Change status form -->
						<form action="{$currentIndex|escape:'html':'UTF-8'}&amp;vieworder&amp;token={$smarty.get.token}" method="post" class="form-horizontal well hidden-print">
							<div class="row">
								<div class="col-lg-9">
									<select id="id_order_state" class="chosen form-control" name="id_order_state">
										{foreach from=$states item=state}
											<option value="{$state['id_order_state']|intval}"{if isset($currentState) && $state['id_order_state'] == $currentState->id} selected="selected" disabled="disabled"{/if}>{$state['name']|escape}</option>
										{/foreach}
									</select>
									<input type="hidden" name="id_order" value="{$order->id}" />
								</div>
								<div class="col-lg-3">
									<button type="submit" name="submitState" class="btn btn-primary">
										{l s='Update status'}
									</button>
								</div>
							</div>
						</form>
					</div>
					<!-- Tab documents -->
					<div class="tab-pane" id="documents">
						<h4 class="visible-print">{l s='Documents'} <span class="badge">({$order->getDocuments()|@count})</span></h4>
						{* Include document template *}
						{include file='controllers/orders/_documents.tpl'}
					</div>
				</div>
				{if $returns}
					<div class="alert alert-warning">
						{l s='Booking cancellation requests has raised  from this order. To see booking cancelation requests'} <a target="_blank" href="{$link->getAdminLink('AdminOrderRefundRequests')}&amp;id_order={$order->id}&amp;vieworder_return">{l s='Click here'}</a>.
					</div>
				{/if}
				<div class="row">
					<div class="panel">
						<div class="panel-heading order_status_heading">
							<i class="icon-credit-card"></i>
							{l s='Rooms Status'}
						</div>
						<div class="panel-content">
							<div class="row">
								<div class="col-lg-12" id="room_status_info_wrapper">
									<table class="table table-responsive">
										<tr>
											<th>{l s='Room No.'}</th>
											<th>{l s='Hotel Name'}</th>
											<th>{l s='Date From'}</th>
											<th>{l s='Date To'}</th>
											<th>{l s='Order Status'}</th>
										</tr>
										{if isset($htl_booking_order_data) && $htl_booking_order_data}
											{foreach from=$htl_booking_order_data item=data}
												<tr>
													<td>
														{$data['room_num']}
													</td>
													<td>
														<a href="{$link->getAdminLink('AdminAddHotel')}&amp;id={$data['id_hotel']}&amp;updatehtl_branch_info" target="_blank">
															<span>{$data['hotel_name']}</span>
														</a>
													</td>
													<td>
														{$data['date_from']|date_format:"%d-%m-%Y"}
													</td>
													<td>
														{$data['date_to']|date_format:"%d-%m-%Y"}
													</td>
													<td>
														<form action="" method="post" class="form-horizontal row room_status_info_form">
															<div class="col-sm-7">
																<select name="booking_order_status" class="form-control booking_order_status margin-bottom-5">
																	{foreach from=$hotel_order_status item=state}
																		<option value="{$state['id_status']|intval}" {if isset($data.id_status) && $state.id_status == $data.id_status} selected="selected" disabled="disabled"{/if}>{$state.name|escape}</option>
																	{/foreach}
																</select>

																{if $data['id_status'] == $hotel_order_status['STATUS_CHECKED_IN']['id_status']}<span class="badge badge-success margin-bottom-5">{l s='checked in on'} {$data['check_in']|date_format:"%d-%m-%Y"}</span>{elseif $data['id_status'] == $hotel_order_status['STATUS_CHECKED_OUT']['id_status']}<span class="badge badge-success margin-bottom-5">{l s='checked out on'} {$data['check_out']|date_format:"%d-%m-%Y"}</span>{/if}

																{* field for the current date *}
																<input class="room_status_date wk-input-date" type="text" name="status_date" value="{$data['date_from']|date_format:"%d-%m-%Y"}" readonly/>

																<input type="hidden" name="date_from" value="{$data['date_from']|date_format:"%Y-%m-%d"}" />
																<input type="hidden" name="date_to" value="{$data['date_to']|date_format:"%Y-%m-%d"}" />
																<input type="hidden" name="id_room" value="{$data['id_room']}" />
																<input type="hidden" name="id_order" value="{$order->id}" />
															</div>
															<div class="col-sm-5">
																<button type="submit" name="submitbookingOrderStatus" class="btn btn-primary">
																	{l s='Update Status'}
																</button>
															</div>
														</form>
													</td>
												</tr>
											{/foreach}
										{else}
											<tr>
												<td>{l s='No data found.'}</td>
											</tr>
										{/if}
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
					{$HOOK_TAB_SHIP}
					<li class="active">
						<a href="#shipping">
							<i class="icon-truck "></i>
							{l s='Shipping'} <span class="badge">{$order->getShipping()|@count}</span>
						</a>
					</li>
					<li>
						<a href="#returns">
							<i class="icon-undo"></i>
							{l s='Merchandise Returns'} <span class="badge">{$order->getReturn()|@count}</span>
						</a>
					</li>
				</ul>
				<!-- Tab content -->
				<div class="tab-content panel" style="display:none"><!-- by webkul -->
				{$HOOK_CONTENT_SHIP}
					<!-- Tab shipping -->
					<div class="tab-pane active" id="shipping">
						<h4 class="visible-print">{l s='Shipping'} <span class="badge">({$order->getShipping()|@count})</span></h4>
						<!-- Shipping block -->
						{if !$order->isVirtual()}
						<div class="form-horizontal">
							{if $order->gift_message}
							<div class="form-group">
								<label class="control-label col-lg-3">{l s='Message'}</label>
								<div class="col-lg-9">
									<p class="form-control-static">{$order->gift_message|nl2br}</p>
								</div>
							</div>
							{/if}
							{include file='controllers/orders/_shipping.tpl'}
							{if $carrierModuleCall}
								{$carrierModuleCall}
							{/if}
							<hr />
							{if $order->recyclable}
								<span class="label label-success"><i class="icon-check"></i> {l s='Recycled packaging'}</span>
							{else}
								<span class="label label-inactive"><i class="icon-remove"></i> {l s='Recycled packaging'}</span>
							{/if}

							{if $order->gift}
								<span class="label label-success"><i class="icon-check"></i> {l s='Gift wrapping'}</span>
							{else}
								<span class="label label-inactive"><i class="icon-remove"></i> {l s='Gift wrapping'}</span>
							{/if}
						</div>
						{/if}
					</div>
					<!-- Tab returns -->
					<div class="tab-pane" id="returns">
						<h4 class="visible-print">{l s='Merchandise Returns'} <span class="badge">({$order->getReturn()|@count})</span></h4>
						{if !$order->isVirtual()}
						<!-- Return block -->
							{if $order->getReturn()|count > 0}
							<div class="table-responsive">
								<table class="table">
									<thead>
										<tr>
											<th><span class="title_box ">{l s='Date'}</span></th>
											<th><span class="title_box ">{l s='Type'}</span></th>
											<th><span class="title_box ">{l s='Carrier'}</span></th>
											<th><span class="title_box ">{l s='Tracking number'}</span></th>
										</tr>
									</thead>
									<tbody>
										{foreach from=$order->getReturn() item=line}
										<tr>
											<td>{$line.date_add}</td>
											<td>{l s=$line.type}</td>
											<td>{$line.state_name}</td>
											<td class="actions">
												<span class="shipping_number_show">{if isset($line.url) && isset($line.tracking_number)}<a href="{$line.url|replace:'@':$line.tracking_number|escape:'html':'UTF-8'}">{$line.tracking_number}</a>{elseif isset($line.tracking_number)}{$line.tracking_number}{/if}</span>
												{if $line.can_edit}
												<form method="post" action="{$link->getAdminLink('AdminOrders')|escape:'html':'UTF-8'}&amp;vieworder&amp;id_order={$order->id|intval}&amp;id_order_invoice={if $line.id_order_invoice}{$line.id_order_invoice|intval}{else}0{/if}&amp;id_carrier={if $line.id_carrier}{$line.id_carrier|escape:'html':'UTF-8'}{else}0{/if}">
													<span class="shipping_number_edit" style="display:none;">
														<button type="button" name="tracking_number">
															{$line.tracking_number|htmlentities}
														</button>
														<button type="submit" class="btn btn-default" name="submitShippingNumber">
															{l s='Update'}
														</button>
													</span>
													<button href="#" class="edit_shipping_number_link">
														<i class="icon-pencil"></i>
														{l s='Edit'}
													</button>
													<button href="#" class="cancel_shipping_number_link" style="display: none;">
														<i class="icon-remove"></i>
														{l s='Cancel'}
													</button>
												</form>
												{/if}
											</td>
										</tr>
										{/foreach}
									</tbody>
								</table>
							</div>
							{else}
							<div class="list-empty hidden-print">
								<div class="list-empty-msg">
									<i class="icon-warning-sign list-empty-icon"></i>
									{l s='No merchandise returned yet'}
								</div>
							</div>
							{/if}
							{if $carrierModuleCall}
								{$carrierModuleCall}
							{/if}
						{/if}
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
					{l s="Payment"} <span class="badge">{$order->getOrderPayments()|@count}</span>
				</div>
				{if count($order->getOrderPayments()) > 0}
					<p class="alert alert-danger"{if round($orders_total_paid_tax_incl, 2) == round($total_paid, 2) || (isset($currentState) && $currentState->id == 6)} style="display: none;"{/if}>
						{l s='Warning'}
						<strong>{displayPrice price=$total_paid currency=$currency->id}</strong>
						{l s='paid instead of'}
						<strong class="total_paid">{displayPrice price=$orders_total_paid_tax_incl currency=$currency->id}</strong>
						{foreach $order->getBrother() as $brother_order}
							{if $brother_order@first}
								{if count($order->getBrother()) == 1}
									<br />{l s='This warning also concerns order '}
								{else}
									<br />{l s='This warning also concerns the next orders:'}
								{/if}
							{/if}
							<a href="{$current_index}&amp;vieworder&amp;id_order={$brother_order->id}&amp;token={$smarty.get.token|escape:'html':'UTF-8'}">
								#{'%06d'|sprintf:$brother_order->id}
							</a>
						{/foreach}
					</p>
				{/if}
				<form id="formAddPayment"  method="post" action="{$current_index}&amp;vieworder&amp;id_order={$order->id}&amp;token={$smarty.get.token|escape:'html':'UTF-8'}">
					<div class="table-responsive">
						<table class="table">
							<thead>
								<tr>
									<th><span class="title_box ">{l s='Date'}</span></th>
									<th><span class="title_box ">{l s='Payment method'}</span></th>
									<th><span class="title_box ">{l s='Transaction ID'}</span></th>
									<th><span class="title_box ">{l s='Amount'}</span></th>
									<th><span class="title_box ">{l s='Invoice'}</span></th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								{foreach from=$order->getOrderPaymentCollection() item=payment}
								<tr>
									<td>{dateFormat date=$payment->date_add full=true}</td>
									<td>{$payment->payment_method|escape:'html':'UTF-8'}</td>
									<td>{$payment->transaction_id|escape:'html':'UTF-8'}</td>
									<td>{displayPrice price=$payment->amount currency=$payment->id_currency}</td>
									<td>
									{if $invoice = $payment->getOrderInvoice($order->id)}
										{$invoice->getInvoiceNumberFormatted($current_id_lang, $order->id_shop)}
									{else}
									{/if}
									</td>
									<td class="actions">
										<button class="btn btn-default open_payment_information">
											<i class="icon-search"></i>
											{l s='Details'}
										</button>
									</td>
								</tr>
								<tr class="payment_information" style="display: none;">
									<td colspan="5">
										<p>
											<b>{l s='Card Number'}</b>&nbsp;
											{if $payment->card_number}
												{$payment->card_number}
											{else}
												<i>{l s='Not defined'}</i>
											{/if}
										</p>
										<p>
											<b>{l s='Card Brand'}</b>&nbsp;
											{if $payment->card_brand}
												{$payment->card_brand}
											{else}
												<i>{l s='Not defined'}</i>
											{/if}
										</p>
										<p>
											<b>{l s='Card Expiration'}</b>&nbsp;
											{if $payment->card_expiration}
												{$payment->card_expiration}
											{else}
												<i>{l s='Not defined'}</i>
											{/if}
										</p>
										<p>
											<b>{l s='Card Holder'}</b>&nbsp;
											{if $payment->card_holder}
												{$payment->card_holder}
											{else}
												<i>{l s='Not defined'}</i>
											{/if}
										</p>
									</td>
								</tr>
								{foreachelse}
								<tr>
									<td class="list-empty hidden-print" colspan="6">
										<div class="list-empty-msg">
											<i class="icon-warning-sign list-empty-icon"></i>
											{l s='No payment methods are available'}
										</div>
									</td>
								</tr>
								{/foreach}
								<tr class="current-edit hidden-print">
									<td>
										<div class="input-group fixed-width-xl">
											<input type="text" name="payment_date" class="datepicker" value="{date('Y-m-d')}" />
											<div class="input-group-addon">
												<i class="icon-calendar-o"></i>
											</div>
										</div>
									</td>
									<td>
										<input name="payment_method" list="payment_method" class="payment_method">
										<datalist id="payment_method">
										{foreach from=$payment_methods item=payment_method}
											<option value="{$payment_method}">
										{/foreach}
										</datalist>
									</td>
									<td>
										<input type="text" name="payment_transaction_id" value="" class="form-control fixed-width-sm"/>
									</td>
									<td>
										<input type="text" name="payment_amount" value="" class="form-control fixed-width-sm pull-left" />
										<select name="payment_currency" class="payment_currency form-control fixed-width-xs pull-left">
											{foreach from=$currencies item=current_currency}
												<option value="{$current_currency['id_currency']}"{if $current_currency['id_currency'] == $currency->id} selected="selected"{/if}>{$current_currency['sign']}</option>
											{/foreach}
										</select>
									</td>
									<td>
										{if count($invoices_collection) > 0}
											<select name="payment_invoice" id="payment_invoice">
											{foreach from=$invoices_collection item=invoice}
												<option value="{$invoice->id}" selected="selected">{$invoice->getInvoiceNumberFormatted($current_id_lang, $order->id_shop)}</option>
											{/foreach}
											</select>
										{/if}
									</td>
									<td class="actions">
										<button class="btn btn-primary" type="submit" name="submitAddPayment">
											{l s='Add'}
										</button>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</form>
				{if (!$order->valid && sizeof($currencies) > 1)}
					<form class="form-horizontal well" method="post" action="{$currentIndex|escape:'html':'UTF-8'}&amp;vieworder&amp;id_order={$order->id}&amp;token={$smarty.get.token|escape:'html':'UTF-8'}">
						<div class="row">
							<label class="control-label col-lg-3">{l s='Change currency'}</label>
							<div class="col-lg-6">
								<select name="new_currency">
								{foreach from=$currencies item=currency_change}
									{if $currency_change['id_currency'] != $order->id_currency}
									<option value="{$currency_change['id_currency']}">{$currency_change['name']} - {$currency_change['sign']}</option>
									{/if}
								{/foreach}
								</select>
								<p class="help-block">{l s='Do not forget to update your exchange rate before making this change.'}</p>
							</div>
							<div class="col-lg-3">
								<button type="submit" class="btn btn-default" name="submitChangeCurrency"><i class="icon-refresh"></i> {l s='Change'}</button>
							</div>
						</div>
					</form>
				{/if}
			</div>
			{hook h="displayAdminOrderLeft" id_order=$order->id}
		</div>
		<div class="col-lg-5">
			<!-- Customer informations -->
			<div class="panel">
				{if $customer->id}
					<div class="panel-heading">
						<i class="icon-user"></i>
						{l s='Customer'}
						<span class="badge">
							<a href="?tab=AdminCustomers&amp;id_customer={$customer->id}&amp;viewcustomer&amp;token={getAdminToken tab='AdminCustomers'}">
								{if Configuration::get('PS_B2B_ENABLE')}{$customer->company} - {/if}
								{$gender->name|escape:'html':'UTF-8'}
								{$customer->firstname}
								{$customer->lastname}
							</a>
						</span>
						<span class="badge">
							{l s='#'}{$customer->id}
						</span>
					</div>
					<div class="row">
						<div class="col-xs-6">
							{if ($customer->isGuest())}
								{l s='This order has been placed by a guest.'}
								{if (!Customer::customerExists($customer->email))}
									<form method="post" action="index.php?tab=AdminCustomers&amp;id_customer={$customer->id}&amp;id_order={$order->id|intval}&amp;token={getAdminToken tab='AdminCustomers'}">
										<input type="hidden" name="id_lang" value="{$order->id_lang}" />
										<input class="btn btn-default" type="submit" name="submitGuestToCustomer" value="{l s='Transform a guest into a customer'}" />
										<p class="help-block">{l s='This feature will generate a random password and send an email to the customer.'}</p>
									</form>
								{else}
									<div class="alert alert-warning">
										{l s='A registered customer account has already claimed this email address'}
									</div>
								{/if}
							{else}
								<dl class="well list-detail">
									<dt>{l s='Email'}</dt>
										<dd><a href="mailto:{$customer->email}"><i class="icon-envelope-o"></i> {$customer->email}</a></dd><br>
									<dt>{l s='Account registered'}</dt>
										<dd class="text-muted"><i class="icon-calendar-o"></i> {dateFormat date=$customer->date_add full=true}</dd>
									<!-- <dt>{l s='Valid orders placed'}</dt>
										<dd><span class="badge">{$customerStats['nb_orders']|intval}</span></dd>
									<dt>{l s='Total spent since registration'}</dt>
										<dd><span class="badge badge-success">{displayPrice price=Tools::ps_round(Tools::convertPrice($customerStats['total_orders'], $currency), 2) currency=$currency->id}</span></dd>
									{if Configuration::get('PS_B2B_ENABLE')}
										<dt>{l s='Siret'}</dt>
											<dd>{$customer->siret}</dd>
										<dt>{l s='APE'}</dt>
											<dd>{$customer->ape}</dd>
									{/if} -->
								</dl>
							{/if}
						</div>

						<div class="col-xs-6">
							<div class="form-group hidden-print">
								<a href="?tab=AdminCustomers&amp;id_customer={$customer->id}&amp;viewcustomer&amp;token={getAdminToken tab='AdminCustomers'}" class="btn btn-default btn-block">{l s='View full details...'}</a>
							</div>
							<div class="panel panel-sm">
								<div class="panel-heading">
									<i class="icon-eye-slash"></i>
									{l s='Private note'}
								</div>
								<form id="customer_note" class="form-horizontal" action="ajax.php" method="post" onsubmit="saveCustomerNote({$customer->id});return false;" >
									<div class="form-group">
										<div class="col-lg-12">
											<textarea name="note" id="noteContent" class="textarea-autosize" onkeyup="$(this).val().length > 0 ? $('#submitCustomerNote').removeAttr('disabled') : $('#submitCustomerNote').attr('disabled', 'disabled')">{$customer->note}</textarea>
										</div>
									</div>
									<div class="row">
										<div class="col-lg-12">
											<button type="submit" id="submitCustomerNote" class="btn btn-default pull-right" disabled="disabled">
												<i class="icon-save"></i>
												{l s='Save'}
											</button>
										</div>
									</div>
									<span id="note_feedback"></span>
								</form>
							</div>
						</div>
					</div>
				{/if}
				<!-- Tab nav -->
				<div class="row" style="display:none">
					<ul class="nav nav-tabs" id="tabAddresses">
						<li class="active">
							<a href="#addressShipping">
								<i class="icon-truck"></i>
								{l s='Shipping address'}
							</a>
						</li>
						<li>
							<a href="#addressInvoice">
								<i class="icon-file-text"></i>
								{l s='Invoice address'}
							</a>
						</li>
					</ul>
					<!-- Tab content -->
					<div class="tab-content panel">
						<!-- Tab status -->
						<div class="tab-pane  in active" id="addressShipping">
							<!-- Addresses -->
							<h4 class="visible-print">{l s='Shipping address'}</h4>
							{if !$order->isVirtual()}
							<!-- Shipping address -->
								{if $can_edit}
									<form class="form-horizontal hidden-print" method="post" action="{$link->getAdminLink('AdminOrders')|escape:'html':'UTF-8'}&amp;vieworder&amp;id_order={$order->id|intval}">
										<div class="form-group">
											<div class="col-lg-9">
												<select name="id_address">
													{foreach from=$customer_addresses item=address}
													<option value="{$address['id_address']}"
														{if $address['id_address'] == $order->id_address_delivery}
															selected="selected"
														{/if}>
														{$address['alias']} -
														{$address['address1']}
														{$address['postcode']}
														{$address['city']}
														{if !empty($address['state'])}
															{$address['state']}
														{/if},
														{$address['country']}
													</option>
													{/foreach}
												</select>
											</div>
											<div class="col-lg-3">
												<button class="btn btn-default" type="submit" name="submitAddressShipping"><i class="icon-refresh"></i> {l s='Change'}</button>
											</div>
										</div>
									</form>
								{/if}
								<div class="well">
									<div class="row">
										<div class="col-sm-6">
											<a class="btn btn-default pull-right" href="?tab=AdminAddresses&amp;id_address={$addresses.delivery->id}&amp;addaddress&amp;realedit=1&amp;id_order={$order->id}&amp;address_type=1&amp;token={getAdminToken tab='AdminAddresses'}&amp;back={$smarty.server.REQUEST_URI|urlencode}">
												<i class="icon-pencil"></i>
												{l s='Edit'}
											</a>
											{displayAddressDetail address=$addresses.delivery newLine='<br />'}
											{if $addresses.delivery->other}
												<hr />{$addresses.delivery->other}<br />
											{/if}
										</div>
									</div>
								</div>
							{/if}
						</div>
						<div class="tab-pane " id="addressInvoice">
							<!-- Invoice address -->
							<h4 class="visible-print">{l s='Invoice address'}</h4>
							{if $can_edit}
								<form class="form-horizontal hidden-print" method="post" action="{$link->getAdminLink('AdminOrders')|escape:'html':'UTF-8'}&amp;vieworder&amp;id_order={$order->id|intval}">
									<div class="form-group">
										<div class="col-lg-9">
											<select name="id_address">
												{foreach from=$customer_addresses item=address}
												<option value="{$address['id_address']}"
													{if $address['id_address'] == $order->id_address_invoice}
													selected="selected"
													{/if}>
													{$address['alias']} -
													{$address['address1']}
													{$address['postcode']}
													{$address['city']}
													{if !empty($address['state'])}
														{$address['state']}
													{/if},
													{$address['country']}
												</option>
												{/foreach}
											</select>
										</div>
										<div class="col-lg-3">
											<button class="btn btn-default" type="submit" name="submitAddressInvoice"><i class="icon-refresh"></i> {l s='Change'}</button>
										</div>
									</div>
								</form>
							{/if}
							<div class="well">
								<div class="row">
									<div class="col-sm-6">
										<a class="btn btn-default pull-right" href="?tab=AdminAddresses&amp;id_address={$addresses.invoice->id}&amp;addaddress&amp;realedit=1&amp;id_order={$order->id}&amp;address_type=2&amp;back={$smarty.server.REQUEST_URI|urlencode}&amp;token={getAdminToken tab='AdminAddresses'}">
											<i class="icon-pencil"></i>
											{l s='Edit'}
										</a>
										{displayAddressDetail address=$addresses.invoice newLine='<br />'}
										{if $addresses.invoice->other}
											<hr />{$addresses.invoice->other}<br />
										{/if}
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
					<i class="icon-envelope"></i> {l s='Messages'} <span class="badge">{sizeof($customer_thread_message)}</span>
				</div>
				{if (sizeof($messages))}
					<div class="panel panel-highlighted">
						<div class="message-item">
							{foreach from=$messages item=message}
								<div class="message-avatar">
									<div class="avatar-md">
										<i class="icon-user icon-2x"></i>
									</div>
								</div>
								<div class="message-body">

									<span class="message-date">&nbsp;<i class="icon-calendar"></i>
										{dateFormat date=$message['date_add']} -
									</span>
									<h4 class="message-item-heading">
										{if ($message['elastname']|escape:'html':'UTF-8')}{$message['efirstname']|escape:'html':'UTF-8'}
											{$message['elastname']|escape:'html':'UTF-8'}{else}{$message['cfirstname']|escape:'html':'UTF-8'} {$message['clastname']|escape:'html':'UTF-8'}
										{/if}
										{if ($message['private'] == 1)}
											<span class="badge badge-info">{l s='Private'}</span>
										{/if}
									</h4>
									<p class="message-item-text">
										{$message['message']|escape:'html':'UTF-8'|nl2br}
									</p>
								</div>
								{*{if ($message['is_new_for_me'])}
									<a class="new_message" title="{l s='Mark this message as \'viewed\''}" href="{$smarty.server.REQUEST_URI}&amp;token={$smarty.get.token}&amp;messageReaded={$message['id_message']}">
										<i class="icon-ok"></i>
									</a>
								{/if}*}
							{/foreach}
						</div>
					</div>
				{/if}
				<div id="messages" class="well hidden-print">
					<form action="{$smarty.server.REQUEST_URI|escape:'html':'UTF-8'}&amp;token={$smarty.get.token|escape:'html':'UTF-8'}" method="post" onsubmit="if (getE('visibility').checked == true) return confirm('{l s='Do you want to send this message to the customer?'}');">
						<div id="message" class="form-horizontal">
							<div class="form-group">
								<label class="control-label col-lg-3">{l s='Choose a standard message'}</label>
								<div class="col-lg-9">
									<select class="chosen form-control" name="order_message" id="order_message" onchange="orderOverwriteMessage(this, '{l s='Do you want to overwrite your existing message?'}')">
										<option value="0" selected="selected">-</option>
										{foreach from=$orderMessages item=orderMessage}
										<option value="{$orderMessage['message']|escape:'html':'UTF-8'}">{$orderMessage['name']}</option>
										{/foreach}
									</select>
									<p class="help-block">
										<a href="{$link->getAdminLink('AdminOrderMessage')|escape:'html':'UTF-8'}">
											{l s='Configure predefined messages'}
											<i class="icon-external-link"></i>
										</a>
									</p>
								</div>
							</div>

							<div class="form-group">
								<label class="control-label col-lg-3">{l s='Display to customer?'}</label>
								<div class="col-lg-9">
									<span class="switch prestashop-switch fixed-width-lg">
										<input type="radio" name="visibility" id="visibility_on" value="0" />
										<label for="visibility_on">
											{l s='Yes'}
										</label>
										<input type="radio" name="visibility" id="visibility_off" value="1" checked="checked" />
										<label for="visibility_off">
											{l s='No'}
										</label>
										<a class="slide-button btn"></a>
									</span>
								</div>
							</div>

							<div class="form-group">
								<label class="control-label col-lg-3">{l s='Message'}</label>
								<div class="col-lg-9">
									<textarea id="txt_msg" class="textarea-autosize" name="message">{Tools::getValue('message')|escape:'html':'UTF-8'}</textarea>
									<p id="nbchars"></p>
								</div>
							</div>


							<input type="hidden" name="id_order" value="{$order->id}" />
							<input type="hidden" name="id_customer" value="{$order->id_customer}" />
							<button type="submit" id="submitMessage" class="btn btn-primary pull-right" name="submitMessage">
								{l s='Send message'}
							</button>
							<a class="btn btn-default" href="{$link->getAdminLink('AdminCustomerThreads')|escape:'html':'UTF-8'}&amp;id_order={$order->id|intval}">
								{l s='Show all messages'}
								<i class="icon-external-link"></i>
							</a>
						</div>
					</form>
				</div>
			</div>
			{hook h="displayAdminOrderRight" id_order=$order->id}
		</div>
	</div>
	{hook h="displayAdminOrder" id_order=$order->id}
	<div class="row" id="start_products">
		<div class="col-lg-12">
			<form class="container-command-top-spacing" action="{$current_index}&amp;vieworder&amp;token={$smarty.get.token|escape:'html':'UTF-8'}&amp;id_order={$order->id|intval}" method="post" onsubmit="return orderDeleteProduct('{l s='This product cannot be returned.'}', '{l s='Quantity to cancel is greater than quantity available.'}');">
				<input type="hidden" name="id_order" value="{$order->id}" />
				<div style="display: none">
					<input type="hidden" value="{$order->getWarehouseList()|implode}" id="warehouse_list" />
				</div>

				<div class="panel">
					<div class="panel-heading">
						<i class="icon-shopping-cart"></i>
						{l s='Rooms In This Order'} <span class="badge">{$order_detail_data|@count}</span>
						{* by webkul products changes as rooms *}
					</div>
					{* by webkul this code is added for showing rooms information on the order detail page *}
					<div class="row">
						<div class="col-lg-12">
							<table class="table" id="customer_cart_details">
								<thead>
									<tr>
										{if $refund_allowed}
											<th class="standard_refund_fields" style="display:none"></th>
										{/if}
										<th class="text-center"><span class="title_box">{l s='Room No.'}</span></th>
										<th class="text-center"><span class="title_box">{l s='Room Image'}</th>
										<th class="text-center"><span class="title_box">{l s='Room Type'}</span></th>
										<th class="text-center"><span class="title_box">{l s='Hotel Name'}</span></th>
										<th class="text-center"><span class="title_box">{l s='Duration'}</span></th>
										<th class="text-center"><span class="title_box">{l s='Unit Price (Tax excl.)'}</span></th>
										<th class="text-center"><span class="title_box">{l s='Total Price (Tax incl.)'}</span></th>
										{if isset($refundReqBookings) && $refundReqBookings}
											<th class="text-center"><span class="title_box">{l s='Refund State'}</span></th>
											<th class="text-center"><span class="title_box">{l s='Refunded amount'}</span></th>
										{/if}
										<th class="text-center fixed-width-xxl"><span class="title_box">{l s='Reallocate Room'}</span></th>
										{if ($can_edit && !$order->hasBeenDelivered())}
										<th class="text-center"><span class="title_box">{l s='Edit Order'}</th>
										{/if}
									</tr>
								</thead>
								<tbody>
								{if $order_detail_data}
									{foreach from=$order_detail_data item=data}
										{* Include product line partial *}
										{include file='controllers/orders/_product_line.tpl'}
									{/foreach}
								{else}
									<tr>
										<td>{l s='No Data Found.'}</td>
									</tr>
								{/if}
								{* Include product line partial *}
								{include file='controllers/orders/_new_product.tpl'}
								</tbody>
							</table>
						</div>
					</div>

					{capture "TaxMethod"}
						{if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)}
							{l s='tax excluded.'}
						{else}
							{l s='tax included.'}
						{/if}
					{/capture}
					{if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)}
						<input type="hidden" name="TaxMethod" value="0">
					{else}
						<input type="hidden" name="TaxMethod" value="1">
					{/if}
					{if $can_edit}
						<div class="row-margin-bottom row-margin-top order_action standard_refund_fields"  style="display: none;">
							<textarea class="cancellation_reason" name="cancellation_reason" placeholder="{l s='Enter reason to cancel bookings'}"></textarea>
						</div>
						<div class="row-margin-bottom row-margin-top order_action">
							{if !$order->hasBeenDelivered()}
								<button type="button" id="add_product" class="btn btn-default">
									<i class="icon-plus-sign"></i>
									{l s='Add Rooms In Order'}
								</button>
							{/if}
							<button id="add_voucher" class="btn btn-default" type="button" >
								<i class="icon-ticket"></i>
								{l s='Add a new discount'}
							</button>

							{if $refund_allowed && !$hasCompletelyRefunded}
								<button style="display: none;" type="submit" name="initiateRefund" class="btn btn-success standard_refund_fields pull-right" id="initiateRefund">
									{if $order->hasBeenPaid()}<i class="icon-undo"></i> {l s='Initiate Refund'}{else}{l s='Cancel bookings'}{/if}
								</button>
							{/if}
						</div>
					{/if}
					<div class="clear">&nbsp;</div>
					<div class="row">
						<div class="col-xs-6">
							<div class="alert alert-warning">
								{l s='For this customer group, prices are displayed as: [1]%s[/1]' sprintf=[$smarty.capture.TaxMethod] tags=['<strong>']}
								{if !$refund_allowed}
									<br/><strong>{l s='Refunds are disabled'}</strong>
								{/if}
							</div>
						</div>
						<div class="col-xs-6 pull-right">
							<div class="panel panel-vouchers" style="{if !sizeof($discounts)}display:none;{/if}">
								{if (sizeof($discounts) || $can_edit)}
									<div class="table-responsive">
										<table class="table">
											<thead>
												<tr>
													<th>
														<span class="title_box ">
															{l s='Discount name'}
														</span>
													</th>
													<th>
														<span class="title_box ">
															{l s='Value'}
														</span>
													</th>
													{if $can_edit}
													<th></th>
													{/if}
												</tr>
											</thead>
											<tbody>
												{foreach from=$discounts item=discount}
												<tr>
													<td>{$discount['name']}</td>
													<td>
													{if $discount['value'] != 0.00}
														-
													{/if}
													{displayPrice price=$discount['value'] currency=$currency->id}
													</td>
													{if $can_edit}
													<td>
														<a href="{$current_index}&amp;submitDeleteVoucher&amp;id_order_cart_rule={$discount['id_order_cart_rule']}&amp;id_order={$order->id}&amp;token={$smarty.get.token|escape:'html':'UTF-8'}">
															<i class="icon-minus-sign"></i>
															{l s='Delete voucher'}
														</a>
													</td>
													{/if}
												</tr>
												{/foreach}
											</tbody>
										</table>
									</div>
									<div class="current-edit" id="voucher_form" style="display:none;">
										{include file='controllers/orders/_discount_form.tpl'}
									</div>
								{/if}
							</div>
							<div class="panel panel-total">
								<div class="table-responsive">
									<table class="table">
										{* Assign order price *}
										{if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)}
											{assign var=order_product_price value=($order->total_products)}
											{assign var=order_discount_price value=$order->total_discounts_tax_excl}
											{assign var=order_wrapping_price value=$order->total_wrapping_tax_excl}
											{assign var=order_shipping_price value=$order->total_shipping_tax_excl}
										{else}
											{assign var=order_product_price value=$order->total_products_wt}
											{assign var=order_discount_price value=$order->total_discounts_tax_incl}
											{assign var=order_wrapping_price value=$order->total_wrapping_tax_incl}
											{assign var=order_shipping_price value=$order->total_shipping_tax_incl}
										{/if}
										<tr id="total_products">
											<td class="text-right"><strong>{l s='Total Rooms Cost (tax excl.)'}</strong></td>
											<td class="amount text-right nowrap">
												<strong>{displayPrice price=$totalRoomsCostTE currency=$currency->id}</strong>
											</td>
											<td class="partial_refund_fields current-edit" style="display:none;"></td>
										</tr>
										{if isset($totalDemandsPriceTE) && $totalDemandsPriceTE > 0}
											<tr id="total_products">
												<td class="text-right"><strong>{l s='Additional Facilities Cost (tax excl.)'}</strong></td>
												<td class="amount text-right nowrap">
													<strong>{displayPrice price=$totalDemandsPriceTE currency=$currency->id}</strong>
												</td>
												<td class="partial_refund_fields current-edit" style="display:none;"></td>
											</tr>
										{/if}
										<tr id="total_tax_order">
											<td class="text-right"><strong>{l s='Total Rooms Tax'}</strong></td>
											<td class="text-right nowrap">
												<strong>{displayPrice price=$total_room_tax currency=$currency->id}</strong>
											</td>
											<td class="partial_refund_fields current-edit" style="display:none;"></td>
										</tr>
										{if isset($totalDemandsPriceTE) && $totalDemandsPriceTE > 0}
											<tr id="total_tax_order">
												<td class="text-right"><strong>{l s='Additional Facilities Tax'}</strong></td>
												<td class="text-right nowrap">
													<strong>{displayPrice price=($totalDemandsPriceTI - $totalDemandsPriceTE) currency=$currency->id}</strong>
												</td>
												<td class="partial_refund_fields current-edit" style="display:none;"></td>
											</tr>
										{/if}
										<tr id="total_discounts" {if $order->total_discounts_tax_incl == 0}style="display: none;"{/if}>
											<td class="text-right">{l s='Discounts'}</td>
											<td class="amount text-right nowrap">
												-{displayPrice price=$order_discount_price currency=$currency->id}
											</td>
											<td class="partial_refund_fields current-edit" style="display:none;"></td>
										</tr>
										<tr id="total_wrapping" {if $order->total_wrapping_tax_incl == 0}style="display: none;"{/if}>
											<td class="text-right">{l s='Wrapping'}</td>
											<td class="amount text-right nowrap">
												{displayPrice price=$order_wrapping_price currency=$currency->id}
											</td>
											<td class="partial_refund_fields current-edit" style="display:none;"></td>
										</tr>
										{if ($order->getTaxCalculationMethod() == $smarty.const.PS_TAX_EXC)}
											<tr id="total_taxes">
												<td class="text-right"><strong>{l s='Total Taxes'}</strong></td>
												<td class="amount text-right nowrap" ><strong>{displayPrice price=($order->total_paid_tax_incl - $order->total_paid_tax_excl) currency=$currency->id}</strong></td>
												<td class="partial_refund_fields current-edit" style="display:none;"></td>
											</tr>
			 							{/if}
										<tr id="total_order">
											<td class="text-right"><strong>{l s='Final Booking Total'}</strong></td>
											<td class="amount text-right nowrap">
												<strong>{displayPrice price=$order->total_paid_tax_incl currency=$currency->id}</strong>
											</td>
											<td class="partial_refund_fields current-edit" style="display:none;"></td>
										</tr>

										{if isset($refundReqBookings) && $refundReqBookings}
											<tr id="total_order">
												<td class="text-right"><strong>* {l s='Refunded Amount'}</strong></td>
												<td class="amount text-right nowrap">
													<strong>{displayPrice price=$refundedAmount currency=$currency->id}</strong>
												</td>
												<td class="partial_refund_fields current-edit" style="display:none;"></td>
											</tr>
										{/if}

										{if $order->total_paid_tax_incl > $order->total_paid_real}
											<tr>
												<td class="text-right"><strong>{l s='Due Amount'}</strong></td>
												<td class="amount text-right nowrap">
													<strong>
														{displayPrice currency=$order->id_currency price=($order->total_paid_tax_incl - $order->total_paid_real)}
													</strong>
												</td>
											</tr>
										{/if}
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>

	<div class="row">
		<div class="col-lg-12">
			<!-- Sources block -->
			{if (sizeof($sources))}
			<div class="panel">
				<div class="panel-heading">
					<i class="icon-globe"></i>
					{l s='Sources'} <span class="badge">{$sources|@count}</span>
				</div>
				<ul {if sizeof($sources) > 3}style="height: 200px; overflow-y: scroll;"{/if}>
				{foreach from=$sources item=source}
					<li>
						{dateFormat date=$source['date_add'] full=true}<br />
						<b>{l s='From'}</b>{if $source['http_referer'] != ''}<a href="{$source['http_referer']}">{parse_url($source['http_referer'], $smarty.const.PHP_URL_HOST)|regex_replace:'/^www./':''}</a>{else}-{/if}<br />
						<b>{l s='To'}</b> <a href="http://{$source['request_uri']}">{$source['request_uri']|truncate:100:'...'}</a><br />
						{if $source['keywords']}<b>{l s='Keywords'}</b> {$source['keywords']}<br />{/if}<br />
					</li>
				{/foreach}
				</ul>
			</div>
			{/if}

			<!-- linked orders block -->
			{if count($order->getBrother()) > 0}
			<div class="panel">
				<div class="panel-heading">
					<i class="icon-cart"></i>
					{l s='Linked orders'}
				</div>
				<div class="table-responsive">
					<table class="table">
						<thead>
							<tr>
								<th>
									{l s='Order no. '}
								</th>
								<th>
									{l s='Status'}
								</th>
								<th>
									{l s='Amount'}
								</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							{foreach $order->getBrother() as $brother_order}
							<tr>
								<td>
									<a href="{$current_index}&amp;vieworder&amp;id_order={$brother_order->id}&amp;token={$smarty.get.token|escape:'html':'UTF-8'}">#{$brother_order->id}</a>
								</td>
								<td>
									{$brother_order->getCurrentOrderState()->name[$current_id_lang]}
								</td>
								<td>
									{displayPrice price=$brother_order->total_paid_tax_incl currency=$currency->id}
								</td>
								<td>
									<a href="{$current_index}&amp;vieworder&amp;id_order={$brother_order->id}&amp;token={$smarty.get.token|escape:'html':'UTF-8'}">
										<i class="icon-eye-open"></i>
										{l s='See the order'}
									</a>
								</td>
							</tr>
							{/foreach}
						</tbody>
					</table>
				</div>
			</div>
			{/if}
		</div>
	</div>

<!-- Modal for reallocation of rooms -->
<div class="modal fade" id="mySwappigModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
    	<ul class="nav nav-tabs" role="tablist">
		    <li role="presentation" class="active"><a href="#reallocate_room_tab" aria-controls="reallocate" role="tab" data-toggle="tab">{l s='Room Reallocation'}</a></li>
		    <li role="presentation"><a href="#swap_room_tab" aria-controls="swap" role="tab" data-toggle="tab">{l s='Swap Room'}</a></li>
		 </ul>
		<div class="tab-content panel active">
			<div role="tabpanel" class="tab-pane active" id="reallocate_room_tab">
				<form method="post" action="{$link->getAdminLink('AdminOrders')|escape:'html':'UTF-8'}&amp;vieworder&amp;id_order={$order->id|intval}">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="realloc_myModalLabel">{l s='Reallocate Rooms'}</h4>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<label for="curr_room_num" class="control-label model-label">{l s='Current Room Number:'}</label>
							<input type="text" class="form-control modal_curr_room_num" name="modal_curr_room_num" readonly="true">
							<input type="hidden" class="form-control modal_date_from" name="modal_date_from">
							<input type="hidden" class="form-control modal_date_to" name="modal_date_to">
							<input type="hidden" class="form-control modal_id_room" name="modal_id_room">
						</div>
						<div class="form-group">
							<label for="realloc_avail_rooms" class="control-label model-label">{l s='Available Rooms To Reallocate:'}</label>
							<div class="realloc_avail_rooms_container" style="width: 195px;">
								<select class="form-control" name="realloc_avail_rooms" id="realloc_avail_rooms">
									<option value="0" selected="selected">{l s='Select Rooms'}</option>
								</select>
							</div>
							<p class="error_text" id="realloc_sel_rm_err_p"></p>
						</div>
						<div class="form-group">
							<label style="text-decoration:underline;margin-top:5px;" for="message-text" class="col-sm-12 control-label model-label"><i class="icon-info-circle"></i>&nbsp;{l s='Currently Alloted Customer Information:'}</label>
							<dl class="well list-detail">
								<dt>{l s='Name'}</dt>
								<dd class="cust_name"></dd><br>
								<dt>{l s='Email'}</dt>
								<dd class="cust_email"></dd><br>
							</dl>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">{l s="Close" mod="hotelreservationsyatem"}</button>
						<input type="submit" id="realloc_allocated_rooms" name="realloc_allocated_rooms" class="btn btn-primary" value="Reallocate">
					</div>
				</form>
			</div>
			<div role="tabpanel" class="tab-pane" id="swap_room_tab">
				<form method="post" action="{$link->getAdminLink('AdminOrders')|escape:'html':'UTF-8'}&amp;vieworder&amp;id_order={$order->id|intval}">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="swap_myModalLabel">{l s='Swap Rooms'}</h4>
					</div>
					<div class="modal-body">
						<div class="form-group">
							<label for="swap_curr_room_num" class="control-label model-label">{l s='Current Room Number:'}</label>
							<input type="text" class="form-control modal_curr_room_num" name="modal_curr_room_num" readonly="true">
							<input type="hidden" class="form-control modal_date_from" name="modal_date_from">
							<input type="hidden" class="form-control modal_date_to" name="modal_date_to">
							<input type="hidden" class="form-control modal_id_room" name="modal_id_room">
							<input type="hidden" class="form-control modal_id_order" name="modal_id_order">
						</div>
						<div class="form-group">
							<label for="swap_avail_rooms" class="control-label model-label">{l s='Available Rooms To Swap:'}</label>
							<div class="swap_avail_rooms_container"></div>
							<p class="error_text" id="swap_sel_rm_err_p"></p>
						</div>
						<div class="form-group">
							<label style="text-decoration:underline;margin-top:5px;" for="message-text" class="col-sm-12 control-label model-label"><i class="icon-info-circle"></i>&nbsp;{l s='Currently Alloted Customer Information:'}</label>
							<dl class="well list-detail">
								<dt>{l s='Name'}</dt>
								<dd class="cust_name"></dd><br>
								<dt>{l s='Email'}</dt>
								<dd class="cust_email"></dd><br>
							</dl>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">{l s="Close" mod="hotelreservationsyatem"}</button>
						<input type="submit" id="swap_allocated_rooms" name="swap_allocated_rooms" class="btn btn-primary" value="Swap">
					</div>
				</form>
			</div>
		</div>
    </div>
  </div>
</div>

{* MOdal for extra demands *}
<div class="modal" tabindex="-1" role="dialog" id="rooms_type_extra_demands">
	<div class="modal-dialog" role="document">
		<div class="modal-content" id="room_extra_demand_content">

		</div>
	</div>
</div>

{* Apply css for the page *}
<style>
	.error_text {
		color:red;}
	.model-label {
		font-weight:bold!important;}
	.room_type_old_price {
		text-decoration: line-through;
		color:#979797;
		font-size:12px;}
	.wk-input-date {
		cursor: text!important;
		background-color: #F5F8F9!important;}
	#room_status_info_wrapper .margin-bottom-5 {
		margin-bottom: 5px!important;}
	#room_status_info_wrapper .room_status_date {
		display: none;}
</style>


{strip}
	{addJsDefL name=no_rm_avail_txt}{l s='No rooms available.' js=1}{/addJsDefL}
	{addJsDefL name=slct_rm_err}{l s='Please select a room first.' js=1}{/addJsDefL}
	{addJsDefL name=txtExtraDemandSucc}{l s='Updated Successfully' js=1}{/addJsDefL}
	{addJsDefL name=atleastSelectTxt}{l s='Select at least one facility to update.' js=1}{/addJsDefL}

	{addJsDefL name=txtSomeErr}{l s='Some error occurred. Please try again.' js=1}{/addJsDefL}
	{addJsDefL name=txtDeleteSucc}{l s='Deleted successfully' js=1}{/addJsDefL}
	{addJsDefL name=txtInvalidDemandVal}{l s='Invalid demand value found' js=1}{/addJsDefL}
{/strip}

{* Apply javascript for the page *}
<script>
	$(document).ready(function() {
		{* check id reason is inserted before submitting the refund *}
		$('#initiateRefund').on('click', function(e) {
			if ($.trim($('.cancellation_reason').val()) == '') {
				$('.cancellation_reason').focus().css('border', '1px solid red');
				return false;
			}
		});

		{* toggle date input of check-in checkout dates as per status selected *}
		$('.booking_order_status').on('change', function() {
			var status = $(this).val();
			if (status == '2' || status == '3') {
				$(this).closest('.room_status_info_form').find('.room_status_date').show();
			} else {
				$(this).closest('.room_status_info_form').find('.room_status_date').hide();
			}
		});

		{* open date picker for the date input of check-in checkout dates *}
		$(document).on('focus', '.room_status_date', function() {
			var dateFrom = $(this).closest('.room_status_info_form').find('[name="date_from"]').val();
			dateFrom = dateFrom.split("-");
            minDate = new Date($.datepicker.formatDate('yy-mm-dd', new Date(dateFrom[0], dateFrom[1] - 1, dateFrom[2])));

			var dateTo = $(this).closest('.room_status_info_form').find('[name="date_to"]').val();
			dateTo = dateTo.split("-");
            maxDate = new Date($.datepicker.formatDate('yy-mm-dd', new Date(dateTo[0], dateTo[1] - 1, dateTo[2])));

			$(this).datepicker({
				dateFormat: 'dd-mm-yy',
				minDate: minDate,
				maxDate: maxDate,
				dayNamesMin: [ "Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"]
			});
		});

		{* open fancybox for extra demands *}
		$('.open_room_extra_demands').on('click', function(e) {
			e.preventDefault();
			var idProduct = $(this).attr('id_product');
			var idOrder = $(this).attr('id_order');
			var idRoom = $(this).attr('id_room');
			var dateFrom = $(this).attr('date_from');
			var dateTo = $(this).attr('date_to');
			var idHtlBooking = $(this).attr('id_htl_booking');
			var orderEdit = 0;
			if(  $(this).closest('.product-line-row').find(".submitProductChange").is(":visible") == true ) {
				orderEdit = 1;
			}

			$.ajax({
				type: 'POST',
				headers: {
					"cache-control": "no-cache"
				},
				url: admin_order_tab_link,
				dataType: 'html',
				cache: false,
				data: {
					id_room: idRoom,
					id_product: idProduct,
					id_order: idOrder,
					date_from: dateFrom,
					date_to: dateTo,
					orderEdit: orderEdit,
					action: 'getRoomTypeBookingDemands',
					ajax: true
				},
				success: function(result) {
					$('#room_extra_demand_content').html('');
					$('#room_extra_demand_content').append(result);

					$('#room_extra_demand_content #id_htl_booking').val(idHtlBooking);

					$('#rooms_type_extra_demands').modal('show');
				},
			});
		});

		{* when choose to add new facilities while additional facilities edit *}
		$(document).on('click', '#room_extra_demand_content #btn_new_room_facility', function() {
			$('#rooms_extra_demands .room_demands_container').show();
			$('#room_extra_demand_content #save_room_facilities').show();
			$('#room_extra_demand_content #back_to_facilities_btn').show();
			$('#rooms_extra_demands .room_ordered_demands').hide();
			$('#room_extra_demand_content #btn_new_room_facility').hide();
		});
		{* click on back button on created facilities while additional facilities edit *}
		$(document).on('click', '#room_extra_demand_content #back_to_facilities_btn', function() {
			$('#rooms_extra_demands .room_ordered_demands').show();
			$('#room_extra_demand_content #btn_new_room_facility').show();
			$('#rooms_extra_demands .room_demands_container').hide();
			$('#room_extra_demand_content #save_room_facilities').hide();
			$('#room_extra_demand_content #back_to_facilities_btn').hide();
		});

		{* // save room extra demand to the order *}
		$(document).on('click', '#save_room_facilities', function(e) {
			e.preventDefault();
			var idHtlBooking = $(this).closest('#room_extra_demand_content').find('#id_htl_booking').val();
			if (idHtlBooking) {
				var roomDemands = [];
				// get the selected extra demands by customer
				$(this).closest('#room_extra_demand_content').find('input:checkbox.id_room_type_demand:checked').each(function () {
					roomDemands.push({
						'id_global_demand':$(this).val(),
						'id_option': $(this).closest('.room_demand_block').find('.id_option').val()
					});
				});

				if (roomDemands.length) {
					$.ajax({
						type: 'POST',
						headers: {
							"cache-control": "no-cache"
						},
						url: "{$link->getAdminLink('AdminOrders')|addslashes}",
						dataType: 'JSON',
						cache: false,
						data: {
							id_htl_booking: idHtlBooking,
							room_demands: JSON.stringify(roomDemands),
							action: 'EditRoomExtraDemands',
							ajax: true
						},
						success: function(result) {
							if (result == 1) {
								showSuccessMessage(txtExtraDemandSucc);
								$('#rooms_type_extra_demands').modal('hide');
							} else {
								showErrorMessage(txtSomeErr);
							}
						}
					});
				} else {
					showErrorMessage(atleastSelectTxt);
				}
			}
		});

		{* Delete ordered room booking demand *}
		$(document).on('click', '.del-order-room-demand', function(e) {
			e.preventDefault();
			if (confirm(txt_confirm)) {
				var idBookingDemand = $(this).attr('id_booking_demand');
				$currentItem = $(this);
				if (idBookingDemand) {
					$.ajax({
						type: 'POST',
						headers: {
							"cache-control": "no-cache"
						},
						url: "{$link->getAdminLink('AdminOrders')|addslashes}",
						dataType: 'JSON',
						cache: false,
						data: {
							id_booking_demand: idBookingDemand,
							action: 'DeleteRoomExtraDemand',
							ajax: true
						},
						success: function(result) {
							if (result == 1) {
								$currentItem.closest('tr').remove();
								showSuccessMessage(txtDeleteSucc);
							} else {
								showErrorMessage(txtSomeErr);
							}
						}
					});
				} else {
					showErrorMessage(txtInvalidDemandVal);
				}
			}
		});

		// change advance option of extra demand
		$(document).on('change', '.demand_adv_option_block .id_option', function(e) {
			var option_selected = $(this).find('option:selected');
			var extra_demand_price = option_selected.attr("optionPrice")
			extra_demand_price = parseFloat(extra_demand_price);
			extra_demand_price = formatCurrency(extra_demand_price, currency_format, currency_sign, currency_blank);
			$(this).closest('.room_demand_block').find('.extra_demand_option_price').text(extra_demand_price);
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

			// For Rooms Swapping
			if (e.relatedTarget.dataset.avail_rm_swap != 'false') {
				var json_arr_rm_swp = JSON.parse(e.relatedTarget.dataset.avail_rm_swap);

				html = '<select class="form-control" name="swap_avail_rooms" id="swap_avail_rooms" style="width:195px;">';
					$.each(json_arr_rm_swp, function(key,val) {
						html += '<option class="swp_rm_opts" value="'+val.id_room+'" >'+val.room_num+'</option>';
					});
				html += '</select>';
				$(".swap_avail_rooms_container").empty().append(html);
			} else {
				$(".swap_avail_rooms_container").empty().text(no_rm_avail_txt);
			}

			// For Rooms Reallocation
			if (e.relatedTarget.dataset.avail_rm_realloc != 'false') {
				var json_arr_rm_realloc = JSON.parse(e.relatedTarget.dataset.avail_rm_realloc);

				html = '<select class="form-control" name="realloc_avail_rooms" id="realloc_avail_rooms" style="width:195px;">';
					$.each(json_arr_rm_realloc, function(key,val) {
						html += '<option class="realloc_rm_opts" value="'+val.id_room+'" >'+val.room_num+'</option>';
					});
				html += '</select>';
				$(".realloc_avail_rooms_container").empty().append(html);
			} else {
				$(".realloc_avail_rooms_container").empty().text(no_rm_avail_txt);
			}
		});

		/*For reallocating rooms in the modal*/
		$("#realloc_allocated_rooms").on('click', function(e){
			$(".error_text").text('');
			var room_to_reallocate = $('#realloc_avail_rooms').val();
			if (typeof room_to_reallocate == 'undefined' || room_to_reallocate == 0) {
				$("#realloc_sel_rm_err_p").text(slct_rm_err);
				return false;
			}
		});
		/*For swaping rooms in the modal*/
		$("#swap_allocated_rooms").on('click', function(e){
			$(".error_text").text('');
			var room_to_swap = $('#swap_avail_rooms').val();
			if (typeof room_to_swap == 'undefined' || room_to_swap == 0) {
				$("#swap_sel_rm_err_p").text(slct_rm_err);
				return false;
			}
		});

		/*END*/

		$(".textarea-autosize").autosize();

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
</script>

{/block}
