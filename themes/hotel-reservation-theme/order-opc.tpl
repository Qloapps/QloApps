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

{block name='order_opc'}
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
						{block name='order_opc_heading'}
							<h2 class="page-heading">{l s='Your booking cart'}</h2>
						{/block}

						<p class="alert alert-warning">{l s='The hotel is currently not accepting any bookings.'}</p>
					{else}
						{if $productNumber}

							{block name='order_opc_left_column'}
								<div class="col-md-8">
            						{block name='errors'}
										{include file="$tpl_dir./errors.tpl"}
									{/block}

									{* Accordian for all blocks *}
									<div class="accordion" id="oprder-opc-accordion">
										<input type="hidden" name="opc_id_address_delivery" value="{$cart->id_address_delivery}" id="opc_id_address_delivery" />
										<input type="hidden" name="opc_id_address_invoice" value="{$cart->id_address_invoice}" id="opc_id_address_invoice" />
										{if isset($checkout_process_steps) && $checkout_process_steps}
											{foreach $checkout_process_steps as $step}
												{if $step->step_key == 'checkout_rooms_summary'}
													{block name='order_opc_rooms_summary'}
														<div class="card">
															<div class="card-header" id="shopping-cart-summary-head">
																{block name='order_opc_rooms_summary_heading'}
																	<h5 class="accordion-header" data-toggle="collapse" data-target="#collapse-shopping-cart" aria-expanded="true" aria-controls="collapse-shopping-cart">
																		<span>{l s='Rooms & Price Summary'}</span>
																		<i class="icon-angle-left pull-right accordion-left-arrow {if $step->step_is_current}hidden{/if}"></i>
																	</h5>
																{/block}
															</div>
															{if $step->step_is_reachable}
																<div id="collapse-shopping-cart" class="opc-collapse {if !$step->step_is_current}collapse{/if}" aria-labelledby="shopping-cart-head" data-parent="#oprder-opc-accordion">
																	<div class="card-body">
																		{* This tpl includes room type lists in the orders *}
																		{block name='shopping_cart'}
																			{include file="$tpl_dir./shopping-cart.tpl"}
																		{/block}
																	</div>
																</div>
															{/if}
														</div>
													{/block}
												{* End Shopping Cart *}
												{elseif $step->step_key == 'checkout_customer'}
													{block name='order_opc_guest_information'}
														<div class="card" id="guest-info-block">
															<div class="card-header" id="guest-info-head">
																{block name='order_opc_guest_information_heading'}
																	<h5 class="accordion-header" data-toggle="collapse" data-target="#collapse-guest-info" aria-expanded="true" aria-controls="collapse-guest-info">
																		<span>{l s='Guest Information'}</span>
																		<i class="icon-angle-left pull-right accordion-left-arrow {if $step->step_is_current}hidden{/if}"></i>
																	</h5>
																{/block}
															</div>
															{if $step->step_is_reachable}
																{block name='order_opc_guest_detail_wrapper'}
																	<div id="collapse-guest-info" class="opc-collapse {if !$step->step_is_current}collapse{/if}" aria-labelledby="guest-info-head" data-parent="#oprder-opc-accordion">
																		<div class="card-body">
																			{if $is_logged || $isGuest}
																				{if $is_logged}
																					{block name='order_opc_guest_detail_form'}
																						<form id="customer_guest_detail_form" method="post" action="{$link->getPageLink('order-opc', null, null)}">
																							<input type="hidden" name="submitGuestDetails" value="1">
																							<p class="checkbox">
																								<input type="checkbox" name="customer_guest_detail" id="customer_guest_detail" value="1" {if $id_customer_guest_detail}checked="checked"{/if}/>
																								<label for="customer_guest_detail" id="customer_guest_detail_txt">{l s='Booking for someone else?'}</label>
																							</p>
																							{if isset($customerGuestDetailErrors) && $customerGuestDetailErrors}
																								<div class="alert alert-danger" id="customer_guest_detail_errors">
																									<p>{if $customerGuestDetailErrors|@count > 1}{l s='There are %d errors' sprintf=$customerGuestDetailErrors|@count}{else}{l s='There is %d error' sprintf=$customerGuestDetailErrors|@count}{/if}</p>
																									<ol>
																										{foreach from=$customerGuestDetailErrors key=k item=customerGuestDetailError}
																											<li>{$customerGuestDetailError}</li>
																										{/foreach}
																									</ol>
																								</div>
																							{/if}
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
																										<input type="text" class="text form-control validate is_required" id="customer_guest_detail_firstname" name="customer_guest_detail_firstname" data-validate="isName" data-maxSize="32"{if isset($smarty.post.customer_guest_detail_firstname) && $smarty.post.customer_guest_detail_firstname}  value="{$smarty.post.customer_guest_detail_firstname}"{elseif isset($customer_guest_detail) && $customer_guest_detail.firstname} value="{$customer_guest_detail.firstname}"{/if}/>
																									</div>
																									<div class="required form-group col-sm-5">
																										<label for="lastname">{l s='Last name'} <sup>*</sup></label>
																										<input type="text" class="form-control validate is_required" id="customer_guest_detail_lastname" name="customer_guest_detail_lastname" data-validate="isName" data-maxSize="32"{if isset($smarty.post.customer_guest_detail_lastname) && $smarty.post.customer_guest_detail_lastname}  value="{$smarty.post.customer_guest_detail_lastname}"{elseif isset($customer_guest_detail) && $customer_guest_detail.lastname} value="{$customer_guest_detail.lastname}"{/if}/>
																									</div>
																								</div>
																								<div class="row">
																									<div class="required text form-group col-sm-6">
																										<label for="email">{l s='Email'} <sup>*</sup></label>
																										<input type="email" class="text form-control validate is_required" id="customer_guest_detail_email" name="customer_guest_detail_email" data-validate="isEmail" data-maxSize="128"{if isset($smarty.post.customer_guest_detail_email) && $smarty.post.customer_guest_detail_email}  value="{$smarty.post.customer_guest_detail_email}"{elseif isset($customer_guest_detail) && $customer_guest_detail.email} value="{$customer_guest_detail.email}"{/if}/>
																									</div>
																								</div>
																								<div class="row">
																									<div class="{if isset($one_phone_at_least) && $one_phone_at_least}required {/if}form-group col-sm-6">
																										<label for="phone_mobile">{l s='Mobile phone'}{if isset($one_phone_at_least) && $one_phone_at_least} <sup>**</sup>{/if}</label>
																										<input type="text" class="text form-control validate is_required" name="customer_guest_detail_phone" id="customer_guest_detail_phone" data-validate="isPhoneNumber" data-maxSize="32"{if isset($smarty.post.customer_guest_detail_phone) && $smarty.post.customer_guest_detail_phone}  value="{$smarty.post.customer_guest_detail_phone}"{elseif isset($customer_guest_detail) && $customer_guest_detail.phone} value="{$customer_guest_detail.phone}"{/if}/>
																									</div>
																								</div>
																							</div>
																						</form>
																					{/block}
																				{/if}
																				{block name='order_opc_guest_detail'}
																					<div id="checkout-guest-info-block"  {if $id_customer_guest_detail}style="display: none;"{/if}>
																						<div class="row margin-btm-10">
																							<div class="col-sm-3 col-xs-5 info-head">{l s='Name'}</div>
																							<div class="col-sm-9 col-xs-7 info-value">
																								{if $isGuest}
																									{$guestInformations['customer_firstname']|escape:'html':'UTF-8'} {$guestInformations['customer_lastname']|escape:'html':'UTF-8'}
																								{else}
																									{$guestInformations['firstname']|escape:'html':'UTF-8'} {$guestInformations['lastname']|escape:'html':'UTF-8'}
																								{/if}
																							</div>
																						</div>
																						<div class="row margin-btm-10">
																							<div class="col-sm-3 col-xs-5 info-head">{l s='Email'}</div>
																							<div class="col-sm-9 col-xs-7 info-value">{$guestInformations['email']|escape:'html':'UTF-8'}</div>
																						</div>
																						{if (isset({$guestInformations['phone']}) && {$guestInformations['phone']})}
																							<div class="row margin-btm-10">
																								<div class="col-sm-3 col-xs-5 info-head">
																									{l s='Phone Number'}
																								</div>
																								<div class="col-sm-9 col-xs-7 info-value">
																									{$guestInformations['phone']|escape:'html':'UTF-8'}
																								</div>
																							</div>
																						{/if}
																					</div>
																				{/block}

																				{* proceed only if no order restrict errors are there *}
																				{if !$orderRestrictErr}
																					{block name='order_opc_guest_detail_proceed_action'}
																						<hr>
																						<div class="row">
																							<div class="col-sm-12 proceed_btn_block">
																								<a class="btn btn-default button button-medium pull-right submit-guest-details" href="{$link->getPageLink('order-opc', null, null, ['proceed_to_payment' => 1])}" title="Proceed to Payment" rel="nofollow">
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
																					{/block}
																				{/if}
																			{else}
																				<!-- Create account / Guest account / Login block -->
																				{block name='order_opc_new_account'}
																					{include file="$tpl_dir./order-opc-new-account.tpl"}
																				{/block}
																			{/if}
																		</div>
																		{if $is_logged || $isGuest}
																			<div class="card-body hidden" id="order-opc-edit-guest-info">
																				{block name='order_opc_edit_guest_info'}
																					{include file="./order-opc-edit-guest-info.tpl"}
																				{/block}
																			</div>
																		{/if}
																	</div>
																{/block}
															{/if}
														</div>
													{/block}
												{elseif $step->step_key == 'checkout_payment'}
													{block name='order_opc_payment'}
														{* <div class="card col-sm-12">
															<!-- Carrier -->
															{include file="$tpl_dir./order-carrier.tpl"}
															<!-- END Carrier -->
														</div> *}
														<div class="card">
															<div class="card-header" id="order-payment-head">
																{block name='order_opc_payment_heading'}
																	<h5 class="accordion-header" data-toggle="collapse" data-target="#collapse-order-payment" aria-expanded="true" aria-controls="collapse-order-payment">
																		<span>{l s='Payment Information'}</span>
																		<i class="icon-angle-left pull-right accordion-left-arrow {if $step->step_is_current}hidden{/if}"></i>
																	</h5>
																{/block}
															</div>
															{if $step->step_is_reachable}
																<div id="collapse-order-payment" class="opc-collapse {if !$step->step_is_current}collapse{/if}" aria-labelledby="order-payment-head" data-parent="#oprder-opc-accordion">
																	<div class="card-body">
																		<!-- Payment -->
																		{block name='order_payment'}
																			{include file="$tpl_dir./order-payment.tpl"}
																		{/block}
																		<!-- END Payment -->
																	</div>
																</div>
															{/if}
														</div>
													{/block}
												{/if}
											{/foreach}
										{/if}
									</div>
								</div>
							{/block}
							{block name='order_opc_right_column'}
								<div class="col-md-4">
									{block name='order_opc_cart_total_detail'}
										{* Total cart details, tax details, advance payment details and voucher details *}
										{include file="$tpl_dir./cart-total-block.tpl"}
									{/block}
									{block name='order_opc_vouchers'}
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
																<span class="voucher_name" data-code="{$voucher.code|escape:'html':'UTF-8'}">{$voucher.code|escape:'html':'UTF-8'} -</span>&nbsp;{$voucher.name}
																</p>
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
									{/block}
									{block name='displayCartRightColumn'}
										{hook h="displayCartRightColumn"}
									{/block}
								</div>
							{/block}
						{else}
							{capture name=path}{l s='Your booking cart'}{/capture}
							{block name='order_opc_heading'}
								<h2 class="page-heading">{l s='Your booking cart'}</h2>
							{/block}
							{block name='errors'}
								{include file="$tpl_dir./errors.tpl"}
							{/block}

							<p class="alert alert-warning">{l s='You have not added any rooms or products to your cart yet.'}</p>
						{/if}
						{block name='order_opc_js_vars'}
							{strip}
								{addJsDef imgDir=$img_dir}
								{addJsDef authenticationUrl=$link->getPageLink("authentication", true)|escape:'quotes':'UTF-8'}
								{addJsDef orderOpcUrl=$link->getPageLink("order-opc", true)|escape:'quotes':'UTF-8'}
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
								{addJsDef PS_REGISTRATION_PROCESS_TYPE=$PS_REGISTRATION_PROCESS_TYPE|intval}
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
						{/block}
					{/if}
				</div>
			</section>
		</div>
	</section>
{/block}
