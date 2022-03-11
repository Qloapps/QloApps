{*
* 2010-2020 Webkul.
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
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

{if isset($hotels_info) && count($hotels_info)}
	{* searched information wrapper block*}
	{if isset($search_data) && $search_data}
		<div class="header-rmsearch-details-wrapper">
			<div class="container">
				<div class="row">
					<div class="col-sm-9 form-group">
						<div class="filter_header row">
							<div class="col-sm-12">
								<p>{l s='Searched results for' mod='wkroomsearchblock'}:</p>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-12 search_result_info">
								{$search_data['htl_dtl']['hotel_name']|escape:'htmlall':'UTF-8'}, {$search_data['htl_dtl']['city']|escape:'htmlall':'UTF-8'} {if !$search_data['order_date_restrict']}<img src="{$module_dir}views/img/icon-arrow-left.svg"> {$search_data['date_from']|escape:'htmlall':'UTF-8'|date_format:"%d %b %Y"} - {$search_data['date_to']|escape:'htmlall':'UTF-8'|date_format:"%d %b %Y"}<span class="faded-txt"> ({1+$search_data['num_days']|escape:'htmlall':'UTF-8'} {l s='Days' mod='wkroomsearchblock'} {$search_data['num_days']|escape:'htmlall':'UTF-8'} {if $search_data['num_days'] > 1}{l s='Nights' mod='wkroomsearchblock'}{else}{l s='Night' mod='wkroomsearchblock'}{/if})</span> {/if}
							</div>
						</div>
					</div>
					<div class="col-sm-3 form-group">
						<button class="btn btn-default modify_roomtype_search_btn pull-right">{l s='Modify Search' mod='wkroomsearchblock'}</button>
					</div>
				</div>
			</div>
		</div>
	{/if}

	{* search form wrapper block*}
	<div class="header-rmsearch-wrapper">
		<div class="container">
			<div class="filter_header">
				<p>{l s='Searched results for' mod='wkroomsearchblock'}</p>
			</div>
			{* search form *}
			{include file="./searchForm.tpl"}
			<a href="#" class="close_room_serach_wrapper"><img src="{$module_dir}views/img/icon-close.svg"></a>
		</div>
	</div>
{/if}