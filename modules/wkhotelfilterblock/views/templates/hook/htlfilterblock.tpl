{*
* 2010-2018 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright 2010-2018 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

<div id="filter_results" class="row block">
	<div class="col-sm-12">
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
						<span class="pull-left">{$currency->prefix} <span id="filter_price_from">{$min_price}</span> {$currency->suffix}</span>
						<span class="pull-right">{$currency->prefix} <span id="filter_price_to">{$max_price}</span> {$currency->suffix}</span>
					</div>
					<div id="filter_price_silder"></div>
				</div>
			</div>
		{/if}
	</div>
</div>
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