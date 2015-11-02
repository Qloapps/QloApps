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
<div class="bootstrap">
	<div class="col-lg-2">
		<img src="{$image}" alt="{$displayName}" class="img-thumbnail" />
		{if isset($badges)}
			{foreach $badges as $badge}
				{if is_array($badge)}
					{foreach $badge as $_badge}
						<img src="{$_badge}" alt="" class="clearfix quickview-badge" />
					{/foreach}
				{else}
					<img src="{$badge}" alt="" class="clearfix quickview-badge" />
				{/if}
			{/foreach}
		{/if}
	</div>
	<div class="col-lg-10">
		<h1>{$displayName}</h1>
		<div class="row">
			<div class="col-sm-6">
				{if (int)$nb_rates > 0}
				<span class="rating">
					<span class="star{if $avg_rate == 5} active{/if}"></span>
					<span class="star{if $avg_rate == 4} active{/if}"></span>
					<span class="star{if $avg_rate == 3} active{/if}"></span>
					<span class="star{if $avg_rate == 2} active{/if}"></span>
					<span class="star{if $avg_rate == 1} active{/if}"></span>
				</span>
				<p class="small">{if (int)$nb_rates > 1}{l s="(%s votes)" sprintf=$nb_rates}{else}{l s="(%s vote)" sprintf=$nb_rates}{/if}</p>
			{/if}
			</div>
			<div class="col-sm-6">
				{if (int)$price}
					<div class="quickview-price">
						{displayPrice price=$price currency=$id_currency}
					</div>
				{/if}
			</div>
		</div>
		<hr />
		<h3>{l s="Description"}</h3>
		<p class="text-justify">{$description_full}</p>
		{if isset($additional_description) && trim($additional_description) != ''}
			<hr />
			<h3>{l s="Merchant benefits"}</h3>
			<p class="text-justify">{$additional_description}</p>
		{/if}
		<hr />
		{if $is_addons_partner}
			<a class="btn btn-success btn-lg pull-right" href="{$url}">{l s='Install module'}</a>
		{else}
			<a class="btn btn-success btn-lg pull-right" href="{$url}" onclick="return !window.open(this.href);">{l s='View on PrestaShop Addons'}</a>
		{/if}
	</div>
</div>