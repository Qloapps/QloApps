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
	<form method="POST" id="search_room_block_form">
		<div id="filter_results" class="row">
			{hook h='displayRoomFilterBlockBefore'}
			{block name='room_types_date_filter'}
				<div class="form-group col-md-4 col-sm-12 mb-2 mb-sm-0 pl-0 pr-sm-2">
					<input type="hidden" id="filter_check_in_time" name="filter_check_in_time" {if isset($date_from)}value="{$date_from|escape:'htmlall':'UTF-8'}"{/if}>
					<input type="hidden" id="filter_check_out_time" name="filter_check_out_time" {if isset($date_to)}value="{$date_to|escape:'htmlall':'UTF-8'}"{/if}>
					<div class="form-control header-rmsearch-input small input-date" id="filter_daterange_value" autocomplete="off" placeholder="{l s='Check-in - Check-out' mod='wkhotelfilterblock'}" tabindex="0" role="button" aria-label="{l s='Select check-in and check-out dates' mod='wkhotelfilterblock'}">
						<span class="align-self-center filter-date-label">{l s='Check In - Check Out' mod='wkhotelfilterblock'}</span>
					</div>
				</div>
			{/block}

				{block name='room_types_occupancy_filter'}
					<div class="form-group col-md-4 col-sm-12 mb-2 mb-sm-0 px-sm-2 px-0">
						<div class="dropdown">
							<button class="form-control input-occupancy header-rmsearch-input small text-left" type="button" id="filter_occupancy_value" aria-label="{l s='Modify occupancy' mod='wkhotelfilterblock'}">
								<span class="float-left">
									{if isset($occupancy_adults) && $occupancy_adults}
										{$occupancy_adults|intval} {if $occupancy_adults > 1}{l s='Adult(s)' mod='wkhotelfilterblock'}{else}{l s='Adult' mod='wkhotelfilterblock'}{/if}{if isset($occupancy_children) && $occupancy_children}, {$occupancy_children|intval} {if $occupancy_children > 1}{l s='Children' mod='wkhotelfilterblock'}{else}{l s='Child' mod='wkhotelfilterblock'}{/if}{/if}
									{else}
										{l s='1 Adult(s), 0 Children' mod='wkhotelfilterblock'}
									{/if}
								</span>
							</button>
						</div>
					</div>
				{/block}

			{block name='room_types_search_action'}
				<div class="form-group col-md-4 col-sm-12 mb-0 pr-0 pl-sm-2 pl-0">
					<button type="button" id="filter_search_now_submit" class="btn btn-primary btn-medium btn-block header-rmsearch-input">
						{l s='Search Now' mod='wkhotelfilterblock'}
					</button>
				</div>
			{/block}
		</div>
	</form>
{/block}
