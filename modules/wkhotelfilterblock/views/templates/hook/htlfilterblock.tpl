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
	<div id="filter_results" class="block">
		{*  to be udpated *}
		<div>
			<input type="hidden" id="filter_check_in_time" name="filter_check_in_time" {if isset($date_from)}value="{$date_from|escape:'htmlall':'UTF-8'}"{/if}>
			<input type="hidden" id="filter_check_out_time" name="filter_check_in_time" {if isset($date_to)}value="{$date_to|escape:'htmlall':'UTF-8'}"{/if}>
			<div class="form-control input-date " id="filter_daterange_value"  autocomplete="off" placeholder="{l s='Check-in - Check-out' mod='wkhotelfilterblock'}" tabindex="-1"><span class="align-self-center">{l s='Check-in' mod='wkhotelfilterblock'} <i class="icon icon-minus"></i> {l s='Check-out' mod='wkhotelfilterblock'}</span></div>
		</div>
			{* {block name='room_types_amenities_filter'}
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
			{/block} *}

	</div>
{/block}
