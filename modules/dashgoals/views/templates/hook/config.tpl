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
<section id="dashgoals_config" class="dash_config hide">
	<header><i class="icon-wrench"></i> {l s='Configuration' mod='dashgoals'}</header>
	<form class="defaultForm form-horizontal" method="post" action="{$link->getAdminLink('AdminDashboard')|escape:'html':'UTF-8'}">
		<table class="table table-condensed table-striped table-bordered">
			<thead>
				<tr>
					<th class="fixed-width-md">{$goals_year}</th>
					<th class="fixed-width-md">{l s='Traffic' mod='dashgoals'}</th>
					<th class="fixed-width-md">{l s='Conversion Rate' mod='dashgoals'}</th>
					<th class="fixed-width-lg">{l s='Average Cart Value' mod='dashgoals'}</th>
					<th>{l s='Sales' mod='dashgoals'}</th>
				</tr>
			</thead>
			<tbody>
				{foreach $goals_months as $month}
				<tr>
					<td>
						{$month.label}
					</td>
					<td>
						<div class="input-group">
							<input id="dashgoals_traffic_{$month@key}" name="dashgoals_traffic_{$month@key}" class="dashgoals_config_input form-control"
								value="{$month.values.traffic|intval}" />
						</div>
					</td>
					<td>
						<div class="input-group">
							<input id="dashgoals_conversion_{$month@key}" name="dashgoals_conversion_{$month@key}" class="dashgoals_config_input form-control"
								value="{$month.values.conversion|floatval}" />
							<span class="input-group-addon">%</span>
						</div>
					</td>
					<td>
						<div class="input-group">
							<span class="input-group-addon">{$currency->iso_code|escape}</span>
							<input id="dashgoals_avg_cart_value_{$month@key}" name="dashgoals_avg_cart_value_{$month@key}" class="dashgoals_config_input form-control"
								value="{$month.values.avg_cart_value|intval}" />
						</div>
					</td>
					<td id="dashgoals_sales_{$month@key}" class="dashgoals_sales">
					</td>
				</tr>
				{/foreach}
			</tbody>
		</table>
		<div class="panel-footer">
			<button class="btn btn-default pull-right" name="submitDashGoals" type="submit"><i class="process-icon-save"></i> {l s='Save' mod='dashgoals'}</button>
		</div>
	</form>
</section>