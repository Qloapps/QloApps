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

{if $opc}
	{assign var="back_order_page" value="order-opc.php"}
	{else}
	{assign var="back_order_page" value="order.php"}
{/if}

<section id="wrapper">
	<div class="container">
		<section id="content">
			<div class="row">
				{if $PS_CATALOG_MODE}
					{capture name=path}{l s='Your booking cart'}{/capture}
					<h2 class="page-heading">{l s='Your booking cart'}</h2>

					<p class="alert alert-warning">{l s='The hotel is currently not accepting any bookings.'}</p>
				{else}
					{if $productNumber}

						<div class="col-md-8">
							{include file="$tpl_dir./errors.tpl"}

							{* Accordian for all blocks *}
							<div class="accordion" id="oprder-opc-accordion">
								<input type="hidden" name="opc_id_address_delivery" value="{$cart->id_address_delivery}" id="opc_id_address_delivery" />
								<input type="hidden" name="opc_id_address_invoice" value="{$cart->id_address_invoice}" id="opc_id_address_invoice" />
								{if isset($checkout_process_steps) && $checkout_process_steps}
									{foreach $checkout_process_steps as $step}
										{if $step->step_key == 'checkout_rooms_summary'}
											<div class="card">
												<div class="card-header" id="shopping-cart-summary-head">
													<h5 class="accordion-header" data-toggle="collapse" data-target="#collapse-shopping-cart" aria-expanded="true" aria-controls="collapse-shopping-cart">
														<span>{l s='Rooms & Price Summary'}</span>
														<i class="icon-angle-left pull-right accordion-left-arrow {if $step->step_is_current}hidden{/if}"></i>
													</h5>
												</div>
												{if $step->step_is_reachable}
													<div id="collapse-shopping-cart" class="opc-collapse {if !$step->step_is_current}collapse{/if}" aria-labelledby="shopping-cart-head" data-parent="#oprder-opc-accordion">
														<div class="card-body">
															{* This tpl includes room type lists in the orders *}
															{include file="$tpl_dir./shopping-cart.tpl"}
														</div>
													</div>
												{/if}
											</div>
										{* End Shopping Cart *}
										{elseif $step->step_key == 'checkout_customer'}
											<div class="card" id="guest-info-block">
												<div class="card-header" id="guest-info-head">
													<h5 class="accordion-header" data-toggle="collapse" data-target="#collapse-guest-info" aria-expanded="true" aria-controls="collapse-guest-info">
														<span>{l s='Guest Information'}</span>
														<i class="icon-angle-left pull-right accordion-left-arrow {if $step->step_is_current}hidden{/if}"></i>
													</h5>
												</div>
												{if $step->step_is_reachable}
													<div id="collapse-guest-info" class="opc-collapse {if !$step->step_is_current}collapse{/if}" aria-labelledby="guest-info-head" data-parent="#oprder-opc-accordion">
														<div class="card-body">
															{if $is_logged || $isGuest}
																{if $is_logged}
																	<form id="customer_guest_detail_form">
																		<p class="checkbox">
																			<input type="checkbox" name="customer_guest_detail" id="customer_guest_detail" value="1" {if $id_customer_guest_detail}checked="checked"{/if}/>
																			<label for="customer_guest_detail" id="customer_guest_detail_txt">{l s='Booking for someone else?'}</label>
																		</p>
																		<div id="customer-guest-detail-container" {if !$id_customer_guest_detail}style="display: none;"{/if}>
																			<div class="row">
																				<div class="required clearfix gender-line col-sm-2">
																					<label>{l s='Social title'}</label>
																					<select name="customer_guest_detail_gender" id="customer_guest_detail_gender">
																						{foreach from=$genders key=k item=gender}
																							<option value="{$gender->id_gender}"{if isset($smarty.post.customer_guest_detail_gender) && $smarty.post.customer_guest_detail_gender == $gender->id_gender || (isset($customer_guest_detail) && $customer_guest_detail.id_gender == $gender->id_gender)} selected="selected"{/if}>{$gender->name}</option>
																						{/foreach}
																					</select>
																				</div>
																				<div class="required form-group col-sm-5">
																					<label for="firstname">{l s='First name'} <sup>*</sup></label>
																					<input type="text" class="text form-control validate is_required" id="customer_guest_detail_firstname" name="customer_guest_detail_firstname" data-validate="isName"{if isset($smarty.post.customer_guest_detail_firstname) && $smarty.post.customer_guest_detail_firstname}  value="{$smarty.post.customer_guest_detail_firstname}"{elseif isset($customer_guest_detail) && $customer_guest_detail.firstname} value="{$customer_guest_detail.firstname}"{/if}/>
																				</div>
																				<div class="required form-group col-sm-5">
																					<label for="lastname">{l s='Last name'} <sup>*</sup></label>
																					<input type="text" class="form-control validate is_required" id="customer_guest_detail_lastname" name="customer_guest_detail_lastname" data-validate="isName"{if isset($smarty.post.customer_guest_detail_lastname) && $smarty.post.customer_guest_detail_lastname}  value="{$smarty.post.customer_guest_detail_lastname}"{elseif isset($customer_guest_detail) && $customer_guest_detail.lastname} value="{$customer_guest_detail.lastname}"{/if}/>
																				</div>
																			</div>
																			<div class="row">
																				<div class="required text form-group col-sm-6">
																					<label for="email">{l s='Email'} <sup>*</sup></label>
																					<input type="email" class="text form-control validate is_required" id="customer_guest_detail_email" name="customer_guest_detail_email" data-validate="isEmail"{if isset($smarty.post.customer_guest_detail_email) && $smarty.post.customer_guest_detail_email}  value="{$smarty.post.customer_guest_detail_email}"{elseif isset($customer_guest_detail) && $customer_guest_detail.email} value="{$customer_guest_detail.email}"{/if}/>
																				</div>
																			</div>
																			<div class="row">
																				<div class="{if isset($one_phone_at_least) && $one_phone_at_least}required {/if}form-group col-sm-6">
																					<label for="phone_mobile">{l s='Mobile phone'}{if isset($one_phone_at_least) && $one_phone_at_least} <sup>**</sup>{/if}</label>
																					<input type="text" class="text form-control validate is_required" name="customer_guest_detail_phone" id="customer_guest_detail_phone" data-validate="isPhoneNumber"{if isset($smarty.post.customer_guest_detail_phone) && $smarty.post.customer_guest_detail_phone}  value="{$smarty.post.customer_guest_detail_phone}"{elseif isset($customer_guest_detail) && $customer_guest_detail.phone} value="{$customer_guest_detail.phone}"{/if}/>
																				</div>
																			</div>
																		</div>
																	</form>
																{/if}
																<div id="checkout-guest-info-block"  {if $id_customer_guest_detail}style="display: none;"{/if}>
																	<div class="row margin-btm-10">
																		<div class="col-sm-3 col-xs-5 info-head">{l s='Name'}</div>
																		<div class="col-sm-9 col-xs-7 info-value">
																			{if $isGuest}
																				{$guestInformations['customer_firstname']} {$guestInformations['customer_lastname']}
																			{else}
																				{$guestInformations['firstname']} {$guestInformations['lastname']}
																			{/if}
																		</div>
																	</div>
																	<div class="row margin-btm-10">
																		<div class="col-sm-3 col-xs-5 info-head">{l s='Email'}</div>
																		<div class="col-sm-9 col-xs-7 info-value">{$guestInformations['email']}</div>
																	</div>
																	{if (isset($delivery->phone_mobile) && $delivery->phone_mobile) || (isset($delivery->phone) && $delivery->phone)}
																		<div class="row margin-btm-10">
																			<div class="col-sm-3 col-xs-5 info-head">
																				{if isset($delivery->phone_mobile) && $delivery->phone_mobile}
																					{l s='Mobile Number'}
																				{else}
																					{l s='Phone Number'}
																				{/if}
																			</div>
																			<div class="col-sm-9 col-xs-7 info-value">
																				{if isset($delivery->phone_mobile) && $delivery->phone_mobile}
																					{$delivery->phone_mobile|escape:'html':'UTF-8'}
																				{else}
																					{$delivery->phone|escape:'html':'UTF-8'}
																				{/if}
																			</div>
																		</div>
																	{/if}
																</div>

																{* proceed only if no order restrict errors are there *}
																{if !$orderRestrictErr}
																	<hr>
																	<div class="row">
																		<div class="col-sm-12 proceed_btn_block">
																			<a class="btn btn-default button button-medium pull-right" href="{$link->getPageLink('order-opc', null, null, ['proceed_to_payment' => 1])}" title="Proceed to Payment" rel="nofollow">
																				<span>
																					{l s='Proceed'}
																				</span>
																			</a>
																			{if $isGuest}
																				<a class="btn btn-default btn-edit-guest-info pull-right" href="#" rel="nofollow">
																					<span>
																						{l s='Edit'}
																					</span>
																				</a>
																			{/if}
																		</div>
																	</div>
																{/if}
															{else}
																<!-- Create account / Guest account / Login block -->
																{include file="$tpl_dir./order-opc-new-account.tpl"}
															{/if}
														</div>
														<div class="card-body hidden" id="order-opc-edit-guest-info">
															{include file="./order-opc-edit-guest-info.tpl"}
														</div>
													</div>
												{/if}
											</div>
										{elseif $step->step_key == 'checkout_payment'}
											{* <div class="card col-sm-12">
												<!-- Carrier -->
												{include file="$tpl_dir./order-carrier.tpl"}
												<!-- END Carrier -->
											</div> *}
											<div class="card">
												<div class="card-header" id="order-payment-head">
													<h5 class="accordion-header" data-toggle="collapse" data-target="#collapse-order-payment" aria-expanded="true" aria-controls="collapse-order-payment">
														<span>{l s='Payment Information'}</span>
														<i class="icon-angle-left pull-right accordion-left-arrow {if $step->step_is_current}hidden{/if}"></i>
													</h5>
												</div>
												{if $step->step_is_reachable}
													<div id="collapse-order-payment" class="opc-collapse {if !$step->step_is_current}collapse{/if}" aria-labelledby="order-payment-head" data-parent="#oprder-opc-accordion">
														<div class="card-body">
															<!-- Payment -->
															{include file="$tpl_dir./order-payment.tpl"}
															<!-- END Payment -->
														</div>
													</div>
												{/if}
											</div>
										{/if}
									{/foreach}
								{/if}
							</div>
						</div>
						<div class="col-md-4">
							{* Total cart details, tax details, advance payment details and voucher details *}
							<div class="col-sm-12 card cart_total_detail_block">
								{* {if $total_rooms}
									<p>
										<span>{l s='Total rooms cost'}{if $display_tax_label}{l s=' (tax excl.)'}{/if}</span>
										<span class="cart_total_values">{displayPrice price=$total_rooms}</span>
									</p>
								{/if}
								{if (isset($total_extra_demands) && $total_extra_demands) || (isset($total_additional_services) && $total_additional_services)}
									<p>
										<span>{l s='Total extra services'}{if $display_tax_label}{l s=' (tax excl.)'}{/if}</span>
										<span class="cart_total_values">{displayPrice price=($total_extra_demands + $total_additional_services)}</span>
									</p>
								{/if} *}
								{* {if $total_service_products}
									<p>
										<span>{l s='Total service products cost'}{if $display_tax_label}{l s=' (tax excl.)'}{/if}</span>
										<span class="cart_total_values">{displayPrice price=$total_service_products}</span>
									</p>
								{/if} *}
								<p>
									<span>
										{l s='Total rooms cost'}
										{if $display_tax_label}
											{if $use_taxes && $priceDisplay == 0}
												{l s='(tax incl)'}
											{else}
												{l s='(tax excl)'}
											{/if}
										{/if}
									</span>
									<span class="cart_total_values">
										{if $use_taxes && $priceDisplay == 0}
											{assign var='total_rooms_cost' value=($total_rooms_wt + $total_extra_demands_wt + $total_additional_services_wt + $total_additional_services_auto_add_wt)}
										{else}
											{assign var='total_rooms_cost' value=($total_rooms + $total_extra_demands + $total_additional_services + $total_additional_services_auto_add)}
										{/if}
										{displayPrice price=$total_rooms_cost}
									</span>
								</p>
								<p class="total_discount_block {if $total_discounts == 0}unvisible{/if}">
									<span>
										{if $display_tax_label}
											{if $use_taxes && $priceDisplay == 0}
												{l s='Total Discount (tax incl)'}
											{else}
												{l s='Total Discount (tax excl)'}
											{/if}
										{else}
											{l s='Total Discount'}
										{/if}
									</span>
									<span class="cart_total_values">
										{if $use_taxes && $priceDisplay == 0}
											{assign var='total_discounts_negative' value=$total_discounts * -1}
										{else}
											{assign var='total_discounts_negative' value=$total_discounts_tax_exc * -1}
										{/if}
										{displayPrice price=$total_discounts_negative}
									</span>
								</p>
								{* {if $priceDisplay != 0} *}
									<p class="cart_total_price {if $total_discounts == 0}unvisible{/if}" >
										<span>
											{l s='Total'}
											{if $display_tax_label}
												{if $use_taxes && $priceDisplay == 0}
													{l s='(tax incl)'}
												{else}
													{l s='(tax excl)'}
												{/if}
											{/if}
										</span>
										<span class="cart_total_values">
										{if $use_taxes && $priceDisplay == 0}
												{displayPrice price=$total_price - $convenience_fee_wt}
											{else}
												{displayPrice price=$total_price_without_tax - $convenience_fee}
											{/if}
										</span>
									</p>
								{* {/if} *}
								{* {if $total_rooms_wt != 0}
									<p class="cart_total_tax">
										<span>{l s='Tax on rooms'}</span>
										<span class="cart_total_values">{displayPrice price=($total_rooms_wt - $total_rooms)}</span>
									</p>
								{/if} *}
								{if $convenience_fee_wt}
									<p>
										<span>{l s='Convenience Fees'}</span>
										<span class="cart_total_values">
										{if $use_taxes && $priceDisplay == 0}
											{displayPrice price=$convenience_fee_wt}
										{else}
											{displayPrice price=$convenience_fee}
										{/if}
										</span>
									</p>
								{/if}
								<p class="cart_total_tax">
									<span>{l s='Total tax'}</span>
									<span class="cart_total_values">{displayPrice price=($total_tax)}</span>
								</p>
								{* {if $use_taxes && $show_taxes && ($total_extra_demands_wt + $total_additional_services_wt)!= 0 }
									<p class="cart_total_tax">
										<span>{l s='Tax on services'}</span>
										<span class="cart_total_values">{displayPrice price=(($total_extra_demands_wt - $total_extra_demands) + ($total_additional_services_wt - $total_additional_services))}</span>
									</p>
								{/if} *}
								{* {if $use_taxes && $show_taxes && $total_service_products_wt != 0 }
									<p class="cart_total_tax">
										<span>{l s='Tax on service products'}</span>
										<span class="cart_total_values">{displayPrice price=($total_service_products_wt - $total_service_products)}</span>
									</p>
								{/if} *}

								<p {if $total_wrapping == 0}class="unvisible"{/if}>
									<span>
										{if $use_taxes}
											{if $display_tax_label}{l s='Total gift wrapping (tax incl.)'}{else}{l s='Total gift-wrapping cost'}{/if}
										{else}
											{l s='Total gift-wrapping cost'}
										{/if}
									</span>
									<span class="cart_total_values">
										{if $use_taxes}
											{if $priceDisplay}
												{displayPrice price=$total_wrapping_tax_exc}
											{else}
												{displayPrice price=$total_wrapping}
											{/if}
										{else}
											{displayPrice price=$total_wrapping_tax_exc}
										{/if}
									</span>
								</p>

								{if isset($is_advance_payment) && $is_advance_payment}
									<p>
										<span>{l s='Advance Payment Amount'}</span>
										<span class="cart_total_values">{displayPrice price=$advPaymentAmount}</span>
									</p>
									<p>
										<span>{l s='Due Amount'}</span>
										<span class="cart_total_values">{displayPrice price=$dueAmount}</span>
									</p>
								{/if}
								<p class="cart_final_total_block">
									<span>{l s='Final Total'}</span>
									{if isset($is_advance_payment) && $is_advance_payment}
										<span class="cart_total_values">{displayPrice price=$advPaymentAmount}</span>
									{else}
										<span class="cart_total_values">
											{if $use_taxes}
												{displayPrice price=$total_price}
											{else}
												{displayPrice price=$total_price_without_tax}
											{/if}
										</span>
										<div class="hookDisplayProductPriceBlock-price">
											{hook h="displayCartTotalPriceLabel"}
										</div>
									{/if}
								</p>
							</div>
							{* Check if voucher feature is enabled currently *}
							{if $voucherAllowed}
								{* Cart vouchers block *}
								<div class="col-sm-12 card cart_voucher_detail_block">
									<p class="cart_voucher_head">{l s='Apply Coupon'}</p>
									<p><span>{l s='Have promocode ?'}</span></p>
									{* Applied vouchers to the cart *}
									{if sizeof($discounts)}
										<div class="row">
											{foreach $discounts as $discount}
												{if ((float)$discount.value_real == 0 && $discount.free_shipping != 1) || ((float)$discount.value_real == 0 && $discount.code == '')}
													{continue}
												{/if}

												<div class="col-sm-12 margin-btm-10 cart_discount {if $discount@last}last_item{elseif $discount@first}first_item{else}item{/if}" id="cart_discount_{$discount.id_discount}">
													<span class="cart_discount_name">
														{$discount.name|escape:'html':'UTF-8'}
														{if strlen($discount.code)}
															<a
																href="{if $opc}{$link->getPageLink('order-opc', true)}{else}{$link->getPageLink('order', true)}{/if}?deleteDiscount={$discount.id_discount}"
																class="price_discount_delete pull-right"
																title="{l s='Delete'}">
																<i class="icon-times"></i>
															</a>
														{/if}
													</span>
													<span class="voucher_apply_state pull-right">
														<img src="{$img_dir}/icon/form-ok-circle.svg" /> {l s='Applied'}
													</span>
												</div>
											{/foreach}
										</div>
										<hr class="seperator">
									{/if}
									<div class="row margin-btm-20">
										{* Form to apply voucher to the cart *}
										<form action="{if $opc}{$link->getPageLink('order-opc', true)}{else}{$link->getPageLink('order', true)}{/if}" method="post" id="voucher">
											<div class="col-sm-8 col-xs-12 col-md-12 col-lg-8">
												<input type="text" class="discount_name form-control" id="discount_name" name="discount_name" value="{if isset($discount_name) && $discount_name}{$discount_name}{/if}" />
												<input type="hidden" name="submitDiscount" />
											</div>
											<div class="col-sm-4 col-xs-12 col-md-12 col-lg-4 submit_discount_div">
												<button type="submit" name="submitAddDiscount" class="opc-button-small opc-btn-primary">
													<span>{l s='Apply'}</span>
												</button>
											</div>
										</form>
									</div>

									{* The available highlighted vouchers for the customer*}
									{if $displayVouchers}
										<p class="cart_voucher_head">{l s='Available Coupons'}</p>
										<div class="row avail_vouchers_block">
											{foreach from=$displayVouchers key=key item=voucher name=availVoucher}
												<div class="col-xs-12">
													<p class="avail_voucher_name">
														<span class="voucher_name" data-code="{$voucher.code|escape:'html':'UTF-8'}">{$voucher.code|escape:'html':'UTF-8'} - </span>{$voucher.name}
													</p>
													<p class="avail_voucher_des">{$voucher['description']}</p>
													{if not $smarty.foreach.availVoucher.last}
														<hr class="seperator">
													{/if}
												</div>
											{/foreach}
										</div>
									{/if}
								</div>
							{/if}
							{* End Voucher Block *}
						</div>
					{else}
						{capture name=path}{l s='Your booking cart'}{/capture}
						<h2 class="page-heading">{l s='Your booking cart'}</h2>
						{include file="$tpl_dir./errors.tpl"}

						<p class="alert alert-warning">{l s='You have not added any room to your cart yet.'}</p>
					{/if}
					{strip}
						{addJsDef imgDir=$img_dir}
						{addJsDef authenticationUrl=$link->getPageLink("authentication", true)|escape:'quotes':'UTF-8'}
						{addJsDef orderOpcUrl=$link->getPageLink("order-opc", true)|escape:'quotes':'UTF-8'}
						{addJsDef historyUrl=$link->getPageLink("history", true)|escape:'quotes':'UTF-8'}
						{addJsDef guestTrackingUrl=$link->getPageLink("guest-tracking", true)|escape:'quotes':'UTF-8'}
						{addJsDef addressUrl=$link->getPageLink("address", true, NULL, "back={$back_order_page}")|escape:'quotes':'UTF-8'}
						{addJsDef orderProcess='order-opc'}
						{addJsDef guestCheckoutEnabled=$PS_GUEST_CHECKOUT_ENABLED|intval}
						{addJsDef displayPrice=$priceDisplay}
						{addJsDef taxEnabled=$use_taxes}
						{addJsDef conditionEnabled=$conditions|intval}
						{addJsDef errorCarrier=$errorCarrier|@addcslashes:'\''}
						{addJsDef errorTOS=$errorTOS|@addcslashes:'\''}
						{addJsDef checkedCarrier=$checked|intval}
						{addJsDef addresses=array()}
						{addJsDef isVirtualCart=$isVirtualCart|intval}
						{addJsDef isPaymentStep=$isPaymentStep|intval}
						{addJsDefL name=txtWithTax}{l s='(tax incl.)' js=1}{/addJsDefL}
						{addJsDefL name=txtWithoutTax}{l s='(tax excl.)' js=1}{/addJsDefL}
						{addJsDefL name=txtHasBeenSelected}{l s='has been selected' js=1}{/addJsDefL}
						{addJsDefL name=txtNoCarrierIsSelected}{l s='No carrier has been selected' js=1}{/addJsDefL}
						{addJsDefL name=txtNoCarrierIsNeeded}{l s='No carrier is needed for this order' js=1}{/addJsDefL}
						{addJsDefL name=txtConditionsIsNotNeeded}{l s='You do not need to accept the Terms of Service for this order.' js=1}{/addJsDefL}
						{addJsDefL name=txtTOSIsAccepted}{l s='The service terms have been accepted' js=1}{/addJsDefL}
						{addJsDefL name=txtTOSIsNotAccepted}{l s='The service terms have not been accepted' js=1}{/addJsDefL}
						{addJsDefL name=txtThereis}{l s='There is' js=1}{/addJsDefL}
						{addJsDefL name=txtErrors}{l s='Error(s)' js=1}{/addJsDefL}
						{addJsDefL name=txtDeliveryAddress}{l s='Delivery address' js=1}{/addJsDefL}
						{addJsDefL name=txtInvoiceAddress}{l s='Invoice address' js=1}{/addJsDefL}
						{addJsDefL name=txtModifyMyAddress}{l s='Modify my address' js=1}{/addJsDefL}
						{addJsDefL name=txtInstantCheckout}{l s='Instant checkout' js=1}{/addJsDefL}
						{addJsDefL name=txtSelectAnAddressFirst}{l s='Please start by selecting an address.' js=1}{/addJsDefL}
						{addJsDefL name=txtFree}{l s='Free' js=1}{/addJsDefL}

						{capture}{if $back}&mod={$back|urlencode}{/if}{/capture}
						{capture name=addressUrl}{$link->getPageLink('address', true, NULL, 'back='|cat:$back_order_page|cat:'?step=1'|cat:$smarty.capture.default)|escape:'quotes':'UTF-8'}{/capture}
						{addJsDef addressUrl=$smarty.capture.addressUrl}
						{capture}{'&multi-shipping=1'|urlencode}{/capture}
						{addJsDef addressMultishippingUrl=$smarty.capture.addressUrl|cat:$smarty.capture.default}
						{capture name=addressUrlAdd}{$smarty.capture.addressUrl|cat:'&id_address='}{/capture}
						{addJsDef addressUrlAdd=$smarty.capture.addressUrlAdd}
						{addJsDef opc=$opc|boolval}
						{capture}<h3 class="page-subheading">{l s='Your billing address' js=1}</h3>{/capture}
						{addJsDefL name=titleInvoice}{$smarty.capture.default|@addcslashes:'\''}{/addJsDefL}
						{capture}<h3 class="page-subheading">{l s='Your delivery address' js=1}</h3>{/capture}
						{addJsDefL name=titleDelivery}{$smarty.capture.default|@addcslashes:'\''}{/addJsDefL}
						{capture}<a class="button button-small btn btn-default" href="{$smarty.capture.addressUrlAdd}" title="{l s='Update' js=1}"><span>{l s='Update' js=1}<i class="icon-chevron-right right"></i></span></a>{/capture}
						{addJsDefL name=liUpdate}{$smarty.capture.default|@addcslashes:'\''}{/addJsDefL}
						{addJsDefL name=txtExtraDemandSucc}{l s='Updated Successfully' js=1}{/addJsDefL}
						{addJsDefL name=txtMaxQuantityAdded}{l s='Maximum quantity of service added' js=1}{/addJsDefL}
						{addJsDefL name=txtExtraDemandErr}{l s='Some error occurred while updating demands' js=1}{/addJsDefL}
					{/strip}
				{/if}
			</div>
		</section>
	</div>
</section>