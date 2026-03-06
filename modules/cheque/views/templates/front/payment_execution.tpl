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

{capture name=path}
	<li class="breadcrumb-item"><a href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html':'UTF-8'}" title="{l s='Go back to the Checkout' mod='cheque'}">{l s='Checkout' mod='cheque'}</a></li>
	<li class="breadcrumb-item"><span class="navigation_page">{l s='Order summary' mod='cheque'}</span></li>
{/capture}

<div class="container-md">
<h3 class="page-heading-light mb-md-3">{l s='Order Summary' mod='cheque'}</h3>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./errors.tpl"}
{include file="$tpl_dir./order-steps.tpl"}

{if isset($nbProducts) && $nbProducts <= 0}
	<p class="alert alert-warning">{l s='Your shopping cart is empty.' mod='cheque'}</p>
{else}

	<form action="{$link->getModuleLink('cheque', 'validation', [], true)|escape:'html':'UTF-8'}" method="post">
		<div class="qlo-account-card card p-4 p-md-5 mb-3">
			<h3 class="page-subheading mb-3">{l s='Check Payment' mod='cheque'}</h3>
			<p class="mb-3">
				<strong>{l s='You have chosen to pay by check. Here is a short summary of your order:' mod='cheque'}</strong>
			</p>
			<p class="mb-2">
				- {l s='The total amount of your order comes to:' mod='cheque'}
				<span id="amount" class="price">{displayPrice price=$total}</span>
				{if $use_taxes == 1 && $display_tax_label}
					{l s='(tax incl.)' mod='cheque'}
				{/if}
			</p>
			{if isset($currencies) && $currencies|@count > 1}
				<p class="mb-2">- {l s='We accept several currencies to receive payments by check.' mod='cheque'}</p>
				<div class="form-group row mb-3">
					<label class="col-12 font-weight-bold">{l s='Choose one of the following:' mod='cheque'}</label>
					<div class="col-12 col-md-6 col-lg-5">
						<select id="currency_payment" class="form-control" name="currency_payment">
						{foreach from=$currencies item=currency}
							<option value="{$currency.id_currency}"{if isset($currencies) && $currency.id_currency == $cust_currency} selected="selected"{/if}>{$currency.name}</option>
						{/foreach}
						</select>
					</div>
				</div>
			{else}
				<p class="mb-2">
					- {l s='We allow the following currencies to be sent by check:' mod='cheque'} <strong>{$currencies.0.name}</strong>
					<input type="hidden" name="currency_payment" value="{$currencies.0.id_currency}" />
				</p>
			{/if}
			<p class="mb-2">- {l s='Check owner and address information will be displayed on the next page.' mod='cheque'}</p>
			<p class="mb-0">- {l s='Please confirm your order by clicking \'I confirm my order\'' mod='cheque'}.</p>
		</div>
		<div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mt-3">
			<a href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html':'UTF-8'}" class="link text-secondary mb-3 mb-sm-0">
				<i class="icon-angles-left"></i>{l s=' Other payment methods' mod='cheque'}
			</a>
			{*By webkul To Check Order restrict condition before Payment by the customer*}
			{if !$restrict_order}
                <button class="btn btn-primary btn-medium confirm_order" type="submit">
                    <span>{l s='I confirm my order' mod='cheque'}</span>
                </button>
            {/if}
		</div>
	</form>
{/if}
</div>
