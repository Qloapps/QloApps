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

{extends file='helpers/form/form.tpl'}

{block name="input"}
	{if $input.type == 'text_customer'}
		<span>{$customer->firstname} {$customer->lastname}</span>
		<p>
			<a class="text-muted" href="{$url_customer}">{l s='View details on the customer page'}</a>
		</p>
	{elseif $input.type == 'text_order'}
		<span>{$text_order}</span>
		<p>
			<a class="text-muted" href="{$url_order}">{l s='View details on the order page'}</a>
		</p>
	{elseif $input.type == 'pdf_order_return'}
		<p>
			{if $state_order_return == 2}
				<a class="btn" href="{$link->getPageLink('pdf-order-return', true, NULL, "id_order_return={$id_order_return|intval}&adtoken={Tools::getAdminTokenLite('AdminReturn')}&id_employee={$employee->id|intval}")|escape:'html':'UTF-8'}">
					<i class="icon-file-text"></i> {l s='Print out'}
				</a>
			{else}
				--
			{/if}
		</p>
	{elseif $input.type == 'list_products'}
		<table class="table">
			<thead>
				<tr>
					<th>{l s='Reference'}</th>
					<th>{l s='Product name'}</th>
					<th class="text-center">{l s='Quantity'}</th>
					<th class="text-center">{l s='Action'}</th>
				</tr>
			</thead>
			<tbody>
				{foreach $returnedCustomizations as $returnedCustomization}
					<tr>
						<td>{$returnedCustomization['reference']}</td>
						<td>{$returnedCustomization['name']}</td>
						<td class="text-center">{$returnedCustomization['product_quantity']|intval}</td>
						<td class="text-center">
							<a class="btn btn-default" href="{$current|escape:'html':'UTF-8'}&amp;deleteorder_return_detail&amp;id_order_detail={$returnedCustomization['id_order_detail']}&amp;id_order_return={$id_order_return}&amp;id_customization={$returnedCustomization['id_customization']}&amp;token={$token|escape:'html':'UTF-8'}">
								<i class="icon-remove"></i>
								{l s='Delete'}
							</a>
						</td>
					</tr>
					{assign var='productId' value=$returnedCustomization.product_id}
					{assign var='productAttributeId' value=$returnedCustomization.product_attribute_id}
					{assign var='customizationId' value=$returnedCustomization.id_customization}
					{assign var='addressDeliveryId' value=$returnedCustomization.id_address_delivery}
					{foreach $customizedDatas.$productId.$productAttributeId.$addressDeliveryId.$customizationId.datas as $type => $datas}
						<tr>
							<td colspan="4">
								<div class="form-horizontal">
									{if $type == Product::CUSTOMIZE_FILE}
										{foreach from=$datas item='data'}
											<div class="form-group">
												<span class="col-lg-3 control-label"><strong>{l s='Attachment'}</strong></span>
												<div class="col-lg-9">
													<a href="displayImage.php?img={$data['value']}&amp;name={$returnedCustomization['id_order_detail']|intval}-file{$smarty.foreach.data.iteration.iteration}" class="_blank"><img class="img-thumbnail" src="{$picture_folder}{$data['value']}_small" alt="" /></a>
												</div>
											</div>
										{/foreach}
									{elseif $type == Product::CUSTOMIZE_TEXTFIELD}
											{foreach from=$datas item='data'}
												<div class="form-group">
													<span class="control-label col-lg-3"><strong>{if $data['name']}{$data['name']}{else}{l s='Text #%d' sprintf=$smarty.foreach.data.iteration}{/if}</strong></span>
													<div class="col-lg-9">
														<p class="form-control-static">
															{$data['value']}
														</p>
													</div>
												</div>
											{/foreach}
									{/if}
								</div>
							</td>
						</tr>
					{/foreach}
				{/foreach}

				{* Classic products *}
				{foreach $products as $k => $product}
					{if !isset($quantityDisplayed[$product['id_order_detail']]) || $product['product_quantity']|intval > $quantityDisplayed[$product['id_order_detail']]|intval}
						<tr>
							<td>{$product['product_reference']}</td>
							<td class="text-center">{$product['product_name']}</td>
							<td class="text-center">{$product['product_quantity']}</td>
							<td class="text-center">
								<a class="btn btn-default"  href="{$current|escape:'html':'UTF-8'}&amp;deleteorder_return_detail&amp;id_order_detail={$product['id_order_detail']}&amp;id_order_return={$id_order_return}&amp;token={$token|escape:'html':'UTF-8'}">
									<i class="icon-remove"></i>
									{l s='Delete'}
								</a>
							</td>
						</tr>
					{/if}
				{/foreach}
			</tbody>
		</table>
	{else}
		{$smarty.block.parent}
	{/if}
{/block}
