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

{block name='room_type_page_search'}
	{if isset($hotels_info) && count($hotels_info)}
		{block name='room_type_page_search_info'}
			{* searched information wrapper block*}
			{if isset($search_data) && $search_data}
				<div class="header-rmsearch-details-wrapper">
					<div class="container">
						<div class="row">
							<div class="col-sm-9 form-group">
								<div class="filter_header row">
									<div class="col-sm-12">
										<p>{l s='Searched results for' mod='wkroomsearchblock'}:
										<button class="btn btn-default visible-xs modify_roomtype_search_btn pull-right"><i class="icon-pencil"></i></button>
										</p>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-12 search_result_info">
										{$search_data['htl_dtl']['hotel_name']|escape:'htmlall':'UTF-8'}, {$search_data['htl_dtl']['city']|escape:'htmlall':'UTF-8'} {if !$search_data['order_date_restrict']}<img src="{$module_dir}views/img/icon-arrow-left.svg"> {if (isset($search_data['date_from']))}{$search_data['date_from']|escape:'htmlall':'UTF-8'|date_format:"%d %b %Y"}{/if} - {if isset($search_data['date_to'])}{$search_data['date_to']|escape:'htmlall':'UTF-8'|date_format:"%d %b %Y"}{/if}<span class="faded-txt"> ({1+$search_data['num_days']|escape:'htmlall':'UTF-8'} {l s='Days' mod='wkroomsearchblock'} {$search_data['num_days']|escape:'htmlall':'UTF-8'} {if $search_data['num_days'] > 1}{l s='Nights' mod='wkroomsearchblock'}{else}{l s='Night' mod='wkroomsearchblock'}{/if})</span> {/if}
									</div>
								</div>
							</div>
							<div class="col-sm-3 form-group hidden-xs">
								<button class="btn btn-default modify_roomtype_search_btn pull-right">{l s='Modify Search' mod='wkroomsearchblock'}</button>
							</div>
						</div>
					</div>
				</div>
			{/if}
		{/block}

		{* search form wrapper block*}
		{block name='room_type_page_search_panel'}
			<div class="header-rmsearch-wrapper">
				<div class="container">
					<div class="filter_header">
						<p>{l s='Searched results for' mod='wkroomsearchblock'}</p>
					</div>
					{* search form *}
					{block name='search_form'}
						{include file="./searchForm.tpl"}
					{/block}
					<a href="#" class="close_room_serach_wrapper"><img src="{$module_dir}views/img/icon-close.svg"></a>
				</div>
			</div>
		{/block}
	{/if}
{/block}
