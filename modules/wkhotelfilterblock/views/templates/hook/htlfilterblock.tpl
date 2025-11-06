{*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to support@qloapps.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to https://store.webkul.com/customisation-guidelines for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*}

{block name='room_types_filters_block'}
	<div id="filter_results" class="row block">
		<div class="col-sm-12">
			{block name='room_types_amenities_filter'}
				{if isset($config) && $config['SHOW_AMENITIES_FILTER'] && $all_feat}
					<div class="row margin-lr-0 layered_filter_cont">
						<div class="col-sm-12 layered_filter_heading">
							<div class="row margin-lr-0">
								<div class="pull-left lf_headingmain_wrapper">
									<span>{l s='Amenities' mod='wkhotelfilterblock'}</span>
									<hr class="theme-text-underline">
								</div>
								<span class="pull-right clear_filter">{l s='Clear Filter' mod='wkhotelfilterblock'}</span>
							</div>
						</div>
						<div class="col-sm-12 lf_sub_cont">
							{foreach $all_feat as $feat}
								<div class="layered_filt">
									<input type="checkbox" class="filter" data-type="amenities" value="{$feat.id_feature}">
									<span class="filters_name">{$feat.name}</span>
								</div>
							{/foreach}
						</div>
					</div>
				{/if}
			{/block}

			{block name='room_types_price_filter'}
				{if isset($config) && $config['SHOW_PRICE_FILTER']}
					<div class="row margin-lr-0 layered_filter_cont">
						<div class="col-sm-12 layered_filter_heading">
							<div class="row margin-lr-0">
								<div class="pull-left lf_headingmain_wrapper">
									<span>{l s='Price' mod='wkhotelfilterblock'}</span>
									<hr class="theme-text-underline">
								</div>
								<span class="pull-right clear_filter">{l s='Clear Filter' mod='wkhotelfilterblock'}</span>
							</div>
						</div>
						<div class="col-sm-12 lf_sub_cont">
							<div class="row margin-lr-0 price_filter_subcont">
								<span class="pull-left"><span id="filter_price_from">{displayPrice price=$min_price}</span></span>
								<span class="pull-right"><span id="filter_price_to">{displayPrice price=$max_price}</span></span>
							</div>
							<div id="filter_price_silder"></div>
						</div>
					</div>
				{/if}
			{/block}
		</div>
	</div>
	{block name='room_types_filters_js_vars'}
		{strip}
			{addJsDef num_days = $num_days}
			{addJsDef date_from = $date_from}
			{addJsDef date_to = $date_to}

			{addJsDef cat_link = $cat_link}
			{addJsDef min_price = $min_price}
			{addJsDef max_price = $max_price}
			{addJsDef warning_num = $warning_num}

			{addJsDefL name=viewMoreTxt}{l s='View More' js=1 mod='wkhotelfilterblock'}{/addJsDefL}
			{addJsDefL name=bookNowTxt}{l s='Book Now' js=1 mod='wkhotelfilterblock'}{/addJsDefL}
		{/strip}
	{/block}
{/block}
