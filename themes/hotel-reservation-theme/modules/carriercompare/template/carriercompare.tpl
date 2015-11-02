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
{if !$opc}
<form class="box" id="compare_shipping_form" method="post" action="#" >
	<fieldset id="compare_shipping">
		<h1 class="page-heading bottom-indent">{l s='Estimate the cost of shipping & taxes.' mod='carriercompare'}</h1>
		<div class="form-group">
			<label for="id_country">{l s='Country' mod='carriercompare'}</label>
			<select name="id_country" id="id_country" class="form-control">
				{foreach from=$countries item=country}
					<option value="{$country.id_country}" {if $id_country == $country.id_country}selected="selected"{/if}>{$country.name|escape:'html':'UTF-8'}</option>
				{/foreach}
			</select>
		</div>
		<div id="states" class="form-group" style="display: none;">
			<label for="id_state">{l s='State' mod='carriercompare'}</label>
			<select name="id_state" id="id_state" class="form-control">
				<option></option>
			</select>
		</div>
		<div class="form-group last">
			<label for="zipcode">{l s='Zip/postal code' mod='carriercompare'}</label>
			<input class="form-control" type="text" name="zipcode" id="zipcode" value="{$zipcode|escape:'html':'UTF-8'}"/> ({l s='Needed for certain carriers.' mod='carriercompare'})
		</div>
		<div id="carriercompare_errors" style="display: none;">
			<ul id="carriercompare_errors_list"></ul><br />
		</div>		
		<div id="SE_AjaxDisplay">
			<img src="{$new_base_dir}loader.gif" alt="Loading data" /><br />
			<p></p>
		</div>
		<div id="availableCarriers" style="display: none;">
			<table cellspacing="0" cellpadding="0" id="availableCarriers_table" class="table table-bordered">
				<thead>
					<tr>
						<th class="carrier_action first_item"></th>
						<th class="carrier_name item">{l s='Carrier' mod='carriercompare'}</th>
						<th class="carrier_infos item">{l s='Information' mod='carriercompare'}</th>
						<th class="carrier_price last_item">{l s='Price' mod='carriercompare'}</th>
					</tr>
				</thead>
				<tbody id="carriers_list">
					
				</tbody>
			</table>
		</div>
		<p class="alert alert-warning text-center" id="noCarrier" style="display: none;">
			{l s='No carrier has been made available for this selection.' mod='carriercompare'}
		</p>
		<p class="SE_SubmitRefreshCard">
            <button class="btn btn-default button button-small" id="carriercompare_submit" type="button" name="carriercompare_submit">
            	<span>{l s='Update cart' mod='carriercompare'}<i class="icon-chevron-right right"></i></span>
            </button>
            <button id="update_carriers_list" type="button" class="btn btn-default button button-small">
            	<span>{l s='Update carrier list' mod='carriercompare'}<i class="icon-chevron-right right"></i></span>
            </button>
		</p>
	</fieldset>
</form>
{addJsDef taxEnabled=$use_taxes}
{addJsDef displayPrice=$priceDisplay}
{addJsDef id_carrier=$id_carrier|intval}
{addJsDef id_state=$id_state|intval}
{addJsDef SE_RefreshMethod=$refresh_method|intval}

{addJsDefL name=SE_RedirectTS}{l s='Refreshing the page and updating your cart...' mod='carriercompare' js=1}{/addJsDefL}
{addJsDefL name=SE_RefreshStateTS}{l s='Checking available states...' mod='carriercompare' js=1}{/addJsDefL}
{addJsDefL name=SE_RetrievingInfoTS}{l s='Retrieving information...' mod='carriercompare' js=1}{/addJsDefL}
{addJsDefL name=txtFree}{l s='Free!' mod='carriercompare' js=1}{/addJsDefL}
{/if}