{*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{capture name=path}
	<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">
		{l s='My account'}
	</a>
	<span class="navigation-pipe">
		{$navigationPipe}
	</span>
	<span class="navigation_page">
		{l s='Return Merchandise Authorization (RMA)'}
	</span>
{/capture}

<h1 class="page-heading bottom-indent">
	{l s='Return Merchandise Authorization (RMA)'}
</h1>
{if isset($errorQuantity) && $errorQuantity}
	<p class="error">
		{l s='You do not have enough products to request an additional merchandise return.'}
	</p>
{/if}
{if isset($errorMsg) && $errorMsg}
	<p class="alert alert-danger">
		{l s='Please provide an explanation for your RMA.'}
	</p>
	<form method="POST"  id="returnOrderMessage">
		<p class="textarea form-group">
        	<label>{l s='Please provide an explanation for your RMA:'}</label>
			<textarea name="returnText" class="form-control"></textarea>
		</p>
		{foreach $ids_order_detail as $id_order_detail}
			<input type="hidden" name="ids_order_detail[{$id_order_detail|intval}]" value="{$id_order_detail|intval}"/>
		{/foreach}
		{foreach $order_qte_input as $key => $value}
			<input type="hidden" name="order_qte_input[{$key|intval}]" value="{$value|intval}"/>
		{/foreach}
		<input type="hidden" name="id_order" value="{$id_order|intval}"/>
		<input class="unvisible" type="submit" name="submitReturnMerchandise" value="{l s='Make an RMA slip'}"/>
		<p>
	        <button type="submit" name="submitReturnMerchandise" class="btn btn-default button button-small">
	        	<span>
	        		{l s='Make an RMA slip'}<i class="icon-chevron-right right"></i>
	        	</span>
	        </button>
	   	</p>
	</form>

{/if}
{if isset($errorDetail1) && $errorDetail1}
	<p class="alert alert-danger">
		{l s='Please check at least one product you would like to return.'}
	</p>
{/if}
{if isset($errorDetail2) && $errorDetail2}
	<p class="alert alert-danger">
		{l s='For each product you wish to add, please specify the desired quantity.'}
	</p>
{/if}
{if isset($errorNotReturnable) && $errorNotReturnable}
	<p class="alert alert-danger">
		{l s='This order cannot be returned.'}
	</p>
{/if}

<p class="info-title">
	{l s='Here is a list of pending merchandise returns'}.
</p>
<div class="block-center" id="block-history">
	{if $ordersReturn && count($ordersReturn)}
	<table id="order-list" class="table table-bordered footab">
		<thead>
			<tr>
				<th data-sort-ignore="true" class="first_item">{l s='Return'}</th>
				<th data-sort-ignore="true" class="item">{l s='Order'}</th>
				<th data-hide="phone" class="item">{l s='Package status'}</th>
				<th data-hide="phone,tablet" class="item">{l s='Date issued'}</th>
				<th data-sort-ignore="true" data-hide="phone,tablet" class="last_item">{l s='Return slip'}</th>
			</tr>
		</thead>
		<tbody>
			{foreach from=$ordersReturn item=return name=myLoop}
				<tr class="{if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{else}item{/if} {if $smarty.foreach.myLoop.index % 2}alternate_item{/if}">
					<td class="bold">
						<a
						class="color-myaccount"
						href="javascript:showOrder(0, {$return.id_order_return|intval}, '{$link->getPageLink('order-return', true)|escape:'html':'UTF-8'}');">
							{l s='#'}{$return.id_order_return|string_format:"%06d"}
						</a>
					</td>
					<td class="history_method">
						<a
						class="color-myaccount"
						href="javascript:showOrder(1, {$return.id_order|intval}, '{$link->getPageLink('order-detail', true)|escape:'html':'UTF-8'}');">
							{$return.reference}
						</a>
					</td>
					<td class="history_method" data-value="{$return.state}">
						<span class="label label-info">
							{$return.state_name|escape:'html':'UTF-8'}
						</span>
					</td>
					<td class="bold" data-value="{$return.date_add|regex_replace:"/[\-\:\ ]/":""}">
						{dateFormat date=$return.date_add full=0}
					</td>
					<td class="history_invoice">
						{if $return.state == 2}
							<a class="link-button" href="{$link->getPageLink('pdf-order-return', true, NULL, "id_order_return={$return.id_order_return|intval}")|escape:'html':'UTF-8'}" title="{l s='Order return'} {l s='#'}{$return.id_order_return|string_format:"%06d"}">
								<i class="icon-file-text"></i> {l s='Print out'}
							</a>
						{else}
							--
						{/if}
					</td>
				</tr>
			{/foreach}
		</tbody>
	</table>
	<div id="block-order-detail" class="unvisible">&nbsp;</div>
	{else}
		<p class="alert alert-warning">{l s='You have no merchandise return authorizations.'}</p>
	{/if}
</div>

<ul class="footer_links clearfix">
	<li>
		<a class="btn btn-default button button-small" href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">
			<span>
				<i class="icon-chevron-left"></i> {l s='Back to your account'}
			</span>
		</a>
	</li>
	<li>
		<a class="btn btn-default button button-small" href="{if isset($force_ssl) && $force_ssl}{$base_dir_ssl}{else}{$base_dir}{/if}">
			<span>
				<i class="icon-chevron-left"></i> {l s='Home'}
			</span>
		</a>
	</li>
</ul>
