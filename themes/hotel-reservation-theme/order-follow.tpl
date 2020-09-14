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
	<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">
		{l s='My account'}
	</a>
	<span class="navigation-pipe">
		{$navigationPipe}
	</span>
	<span class="navigation_page">
		{l s='Booking Refund Requests'}
	</span>
{/capture}

<div class="panel">
	<h1 class="page-heading bottom-indent">
		<i class="icon-tasks"></i> &nbsp;{l s='Booking Refund Requests'}
	</h1>
	{if $ordersReturns && $ordersReturns|count}
		<div class="table-responsive wk-datatable-wrapper">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>{l s='Refund Id'}</th>
						<th>{l s='Order'}</th>
						<th>{l s='Refund status'}</th>
						<th>{l s='Date requested'}</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					{foreach from=$ordersReturns item=return}
						<tr>
							<td>
								<a href="{$link->getPageLink('order-return', true)|escape:'html':'UTF-8'}&amp;id_order_return={$return.id_order_return|escape:'html':'UTF-8'}">{l s='#'}{$return.id_order_return|escape:'html':'UTF-8'}</a>
							</td>
							<td>
								#{$return.reference|escape:'html':'UTF-8'}
							</td>
							<td>
								<span class="badge wk-badge" style="background-color:{$return.state_color|escape:'html':'UTF-8'}">{$return.state_name|escape:'html':'UTF-8'}</span>
							</td>
							<td>
								{dateFormat date=$return.date_add full=0}
							</td>
							<td>
								<a class="btn btn-default" href="{$link->getPageLink('order-return', true)|escape:'html':'UTF-8'}&amp;id_order_return={$return.id_order_return|escape:'html':'UTF-8'}"><i class="icon-search-plus"></i> {l s='View details'}</a>
							</td>
						</tr>
					{/foreach}
				</tbody>
			</table>
		</div>
	{else}
		<div class="alert alert-warning">{l s='You have no booking refund requests.'}</div>
	{/if}
</div>

<ul class="footer_links clearfix">
	<li><a class="btn btn-default button button-small" href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}"><span><i class="icon-chevron-left"></i> {l s='Back to your account'}</span></a></li>
	<li><a class="btn btn-default button button-small" href="{if isset($force_ssl) && $force_ssl}{$base_dir_ssl}{else}{$base_dir}{/if}"><span><i class="icon-chevron-left"></i> {l s='Home'}</span></a></li>
</ul>