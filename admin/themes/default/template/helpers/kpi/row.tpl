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

<div class="panel kpi-container{if isset($no_wrapping) && $no_wrapping} no-wrapping{/if}">
	<div class="kpis-wrap">
		{foreach from=$kpis item=kpi}
			<div class="kpi-wrap"{if isset($kpi->visible) && !$kpi->visible}style="display: none;"{/if}>
				{$kpi->generate()}
			</div>
		{/foreach}
	</div>

	<div class="actions-wrap">
		{if $refresh}
			<button class="btn btn-default" type="button" onclick="refresh_kpis();">
				<i class="icon-refresh"></i>
			</button>
		{/if}

		<div class="dropdown">
			<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
				<i class="icon-ellipsis-vertical"></i>
			</button>

			<ul class="dropdown-menu">
				{foreach from=$kpis item=kpi}
					<li>
						<label>
							<input type="checkbox" class="kpi-display-toggle" data-kpi-id="{$kpi->id}" {if $kpi->visible}checked{/if}>
							{$kpi->title}
						</label>
					</li>
				{/foreach}
			</ul>
		</div>

		<button class="btn btn-default" type="button" onclick="toggleKpiView();">
			<i class="icon-retweet"></i>
		</button>
	</div>
</div>
