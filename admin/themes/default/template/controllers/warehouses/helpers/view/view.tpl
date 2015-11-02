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

{extends file="helpers/view/view.tpl"}
{block name="override_tpl"}
{if isset($warehouse)}
<div class="panel">
	<h3><i class="icon-cogs"></i> {l s='General information'}</h3>
	<div class="form-horizontal">
		<div class="row">
			<label class="control-label col-lg-3">{l s='Reference:'}</label>
			<div class="col-lg-9"><p class="form-control-static">{$warehouse->reference}</p></div>
		</div>
		<div class="row">
			<label class="control-label col-lg-3">{l s='Name:'}</label>
			<div class="col-lg-9"><p class="form-control-static">{$warehouse->name}</p></div>
		</div>
		<div class="row">
			<label class="control-label col-lg-3">{l s='Manager:'}</label>
			<div class="col-lg-9"><p class="form-control-static">{$employee->lastname} {$employee->firstname}</p></div>
		</div>
		<div class="row">
			<label class="control-label col-lg-3">{l s='Country:'}</label>
			<div class="col-lg-9"><p class="form-control-static">{if $address->country != ''}{$address->country}{else}{l s='N/A'}{/if}</p></div>
		</div>
		<div class="row">
			<label class="control-label col-lg-3">{l s='Phone:'}</label>
			<div class="col-lg-9"><p class="form-control-static">{if $address->phone != ''}{$address->phone}{else}{l s='N/A'}{/if}</p></div>
		</div>
		<div class="row">
			<label class="control-label col-lg-3">{l s='Management type:'}</label>
			<div class="col-lg-9"><p class="form-control-static">{l s=$warehouse->management_type}</p></div>
		</div>
		<div class="row">
			<label class="control-label col-lg-3">{l s='Valuation currency:'}</label>
			<div class="col-lg-9"><p class="form-control-static">{$currency->name} ({$currency->sign})</p></div>
		</div>
		<div class="row">
			<label class="control-label col-lg-3">{l s='Product references:'}</label>
			<div class="col-lg-9"><p class="form-control-static">{$warehouse_num_products}</p></div>
		</div>
		<div class="row">
			<label class="control-label col-lg-3">{l s='Physical product quantities:'}</label>
			<div class="col-lg-9"><p class="form-control-static">{$warehouse_quantities}</p></div>
		</div>
		<div class="row">
			<label class="control-label col-lg-3">{l s='Stock valuation:'}</label>
			<div class="col-lg-9"><p class="form-control-static">{$warehouse_value}</p></div>
		</div>
	</div>
</div>
<div class="panel">
	<h3><i class="icon-sitemap"></i> {l s='Shops'}</h3>
	{if isset($shops) && count($shops) > 0}
	<div class="alert alert-info">{l s='The following are the shops associated with this warehouse.'}</div>
	<table class="table">
		<thead>
			<tr>
				<th class="fixed-width-xs align-center"><span class="title_box">{l s='ID'}</span></th>
				<th><span class="title_box">{l s='Name'}</span></th>
			</tr>
		</thead>
		<tbody>
			{foreach $shops as $shop}
			<tr>
				<td class="fixed-width-xs align-center">{$shop.id_shop}</td>
				<td>{$shop.name}</td>
			</tr>
			{/foreach}
		</tbody>
	</table>
	{else}
	<div class="alert alert-warning">{l s='Currently there is no shop associated with this warehouse.'}</div>
	{/if}
</div>
<div class="panel">
	<h3><i class="icon-archive"></i> {l s='Stock'}</h3>
	<a class="btn btn-link" href="{$link->getAdminLink('AdminStockInstantState')|escape:'html':'UTF-8'}&amp;id_warehouse={$warehouse->idintval}&amp;token={getAdminToken tab='AdminStockInstantState'}">{l s='See products details'} <i class="icon-external-link-sign"></i></a>
</div>
<div class="panel">
	<h3><i class="icon-reorder"></i> {l s='History'}</h3>
	<a class="btn btn-link" href="{$link->getAdminLink('AdminStockMvt')|escape:'html':'UTF-8'}&amp;id_warehouse={$warehouse->id|intval}&amp;token={getAdminToken tab='AdminStockMvt'}">{l s='See warehouse\'s activity details'} <i class="icon-external-link-sign"></i></a>
</div>
{else}
	<div class="panel"><div class="alert alert danger">{l s='This warehouse does not exist.'}</div></div>
{/if}
{/block}
