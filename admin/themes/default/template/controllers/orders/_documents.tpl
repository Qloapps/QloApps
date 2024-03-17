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
<div class="table-responsive">
	<table class="table" id="documents_table">
		<thead>
			<tr>
				<th>
					<span class="title_box ">{l s='Date'}</span>
				</th>
				<th>
					<span class="title_box ">{l s='Document'}</span>
				</th>
				<th>
					<span class="title_box ">{l s='Number'}</span>
				</th>
				<th>
					<span class="title_box ">{l s='Amount'}</span>
				</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			{foreach from=$order->getDocuments() item=document}
				{if get_class($document) eq 'OrderInvoice'}
					<tr id="invoice_{$document->id}">
				{elseif get_class($document) eq 'OrderSlip'}
					<tr id="orderslip_{$document->id}">
				{/if}
					<td>{dateFormat date=$document->date_add}</td>
					<td>
						{if get_class($document) eq 'OrderInvoice'}
							{l s='Invoice'}
						{elseif get_class($document) eq 'OrderSlip'}
							{l s='Credit Slip'}
						{/if}
					</td>
					<td>
						{if get_class($document) eq 'OrderInvoice'}
							<a class="_blank" title="{l s='See the document'}" href="{$link->getAdminLink('AdminPdf')|escape:'html':'UTF-8'}&amp;submitAction=generateInvoicePDF&amp;id_order_invoice={$document->id}">
						{elseif get_class($document) eq 'OrderSlip'}
							<a class="_blank" title="{l s='See the document'}" href="{$link->getAdminLink('AdminPdf')|escape:'html':'UTF-8'}&amp;submitAction=generateOrderSlipPDF&amp;id_order_slip={$document->id}">
						{/if}
						{if get_class($document) eq 'OrderInvoice'}
							{$document->getInvoiceNumberFormatted($current_id_lang, $order->id_shop)}
						{elseif get_class($document) eq 'OrderSlip'}
							{Configuration::get('PS_CREDIT_SLIP_PREFIX', $current_id_lang)}{'%06d'|sprintf:$document->id}
						{/if}
						</a>
					</td>
					<td>
					{if get_class($document) eq 'OrderInvoice'}
						{displayPrice price=$document->total_paid_tax_incl currency=$currency->id}&nbsp;
						{if $document->getTotalPaid()}
							<span>
							{if $document->getRestPaid() > 0}
								({displayPrice price=$document->getRestPaid() currency=$currency->id} {l s='not paid'})
							{elseif $document->getRestPaid() < 0}
								({displayPrice price=-$document->getRestPaid() currency=$currency->id} {l s='overpaid'})
							{/if}
							</span>
						{/if}
					{elseif get_class($document) eq 'OrderSlip'}
						{displayPrice price=$document->total_products_tax_incl+$document->total_shipping_tax_incl currency=$currency->id}
					{/if}
					</td>
					<td class="text-right document_action">
						{if get_class($document) eq 'OrderInvoice'}
							{if $document->getRestPaid()}
								<a href="#form_add_payment_panel" class="js-set-payment btn btn-default anchor" data-amount="{$document->getRestPaid()}" data-id-invoice="{$document->id}" title="{l s='Set payment form'}">
									<i class="icon-money"></i>
									{l s='Enter payment'}
								</a>
							{/if}

							<a href="#" class="btn btn-default" onclick="$('#invoiceNote{$document->id}').show(); return false;" title="{if $document->note eq ''}{l s='Add note'}{else}{l s='Edit note'}{/if}">
								{if $document->note eq ''}
									<i class="icon-plus-sign-alt"></i>
									{l s='Add note'}
								{else}
									<i class="icon-pencil"></i>
									{l s='Edit note'}
								{/if}
							</a>
						{/if}
					</td>
				</tr>
				{if get_class($document) eq 'OrderInvoice'}
					<tr id="invoiceNote{$document->id}" style="display:none">
						<td colspan="5">
							<form action="{$current_index}&amp;viewOrder&amp;id_order={$order->id}{if isset($smarty.get.token)}&amp;token={$smarty.get.token|escape:'html':'UTF-8'}{/if}" method="post">
								<p>
									<label for="editNote{$document->id}" class="t">{l s='Note'}</label>
									<input type="hidden" name="id_order_invoice" value="{$document->id}" />
									<textarea name="note" id="editNote{$document->id}" class="edit-note textarea-autosize">{$document->note|escape:'html':'UTF-8'}</textarea>
								</p>
								<p>
									<button type="submit" name="submitEditNote" class="btn btn-default">
										<i class="icon-save"></i>
										{l s='Save'}
									</button>
									<a class="btn btn-default" href="#" id="cancelNote" onclick="$('#invoiceNote{$document->id}').hide();return false;">
										<i class="icon-remove"></i>
										{l s='Cancel'}
									</a>
								</p>
							</form>
						</td>
					</tr>
				{/if}
			{foreachelse}
				<tr>
					<td colspan="5" class="list-empty">
						<div class="list-empty-msg">
							<i class="icon-warning-sign list-empty-icon"></i>
							{l s='There is no available document'}
						</div>
						{if isset($invoice_management_active) && $invoice_management_active}
							<a class="btn btn-default" href="{$current_index}&amp;viewOrder&amp;submitGenerateInvoice&amp;id_order={$order->id}{if isset($smarty.get.token)}&amp;token={$smarty.get.token|escape:'html':'UTF-8'}{/if}">
								<i class="icon-repeat"></i>
								{l s='Generate invoice'}
							</a>
						{/if}
					</td>
				</tr>
			{/foreach}
		</tbody>
	</table>
</div>
