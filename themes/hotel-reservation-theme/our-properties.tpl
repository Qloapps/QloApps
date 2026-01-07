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

{block name='our_properties'}
{capture name=path}{l s='Our Properties'}{/capture}
	{if isset($hotelsInfo) && $hotelsInfo}
		{block name='our_properties_list_title'}
			<div class="title-container mt-5">
				<h1 class="text-center our-properties-header">{l s='Our Properties'}</h1>
				<div class="text-center our-properties-desc mb-4">
					<div>{$WK_HTL_SHORT_DESC|escape:'html':'UTF-8'}</div>
				</div>
			</div>
		{/block}

		{block name='displayPropertiesLocationBefore'}
			{hook h='displayPropertiesLocationBefore'}
		{/block}

		{block name='our_properties_location'}
			{if isset($hotelLocationArray) && $hotelLocationArray && isset($displayHotelMap) && $displayHotelMap}
				<div class="margin-top-20 margin-btm-20">
					<div class="col-12 col-sm-12" id="googleMapWrapper">
						<div id="map"></div>
					</div>
				</div>
			{/if}
			<div style="clear:both;"></div>
		{/block}

		{block name='displayPropertiesLocationAfter'}
			{hook h='displayPropertiesLocationAfter'}
		{/block}

		{block name='displayPropertiesListBefore'}
			{hook h='displayPropertiesListBefore'}
		{/block}

		<div class="properties-page p-4">
			{block name='our_properties_list'}
				<div class="row col-12 hotels-container {if $hotelsInfo|count == 1}justify-content-center{/if} ">
					{foreach $hotelsInfo as $hotel}
						<div class="rounded-lg  mb-4 col-6">
							<div class="card shadow-sm p-3">
								<div class="hotel-address-container row ">
									<div class="col-5">
										<img class="max-width-200 rounded-sm" style="width:100%" src="{$hotel['image_url']}">
									</div>
									<div class="col-7 p-1">
										<div class="hotel-name h9 mb-2"><span>{$hotel['hotel_name']}</span></div>
										<div class="hotel-branch-info-value mb-2 small"><i class="icon-location-dot"></i> {$hotel['address']}, {$hotel['city']}, {if {$hotel['state_name']}}{$hotel['state_name']},{/if} {$hotel['country_name']}, {$hotel['postcode']}</div>
										<div class="hotel-branch-info-value mb-2 small">
											<i class="icon-solid icon-mobile-screen"></i> {$hotel['phone']}
										</div>
										<div class="hotel-branch-info-value mb-2 small">
											<i class="icon-regular icon-envelope"></i> {$hotel['email']}
										</div>
										<div class="hotel-branch-info-actions">
											<a href="{$hotel['view_rooms_link']}" target="_blank" class="btn btn-primary view_rooms_btn col-sm-6 col-12">{l s='View Rooms'}</a>
											{if ($hotel['latitude'] != 0 || $hotel['longitude'] != 0) && $viewOnMap}
												<a class="btn htl-map-direction-btn col-sm-6 col-12" href="http://maps.google.com/maps?daddr=({$hotel['latitude']},{$hotel['longitude']})" target="_blank">{l s='View on map'}</a>
											{/if}
										</div>
									</div>
								</div>
							</div>
						</div>
					{/foreach}
				</div>
			{/block}

			{block name='displayPropertiesListAfter'}
				{hook h='displayPropertiesListAfter'}
			{/block}

			{if (isset($pageLimit)) && $pageLimit > 1}
				<form id="our-properties-list" method="post" action="{$currentPageUrl}">
					<input type="hidden" value="" name="pagination" id="pagination"/>
				</form>
				<div class="d-flex pagination-container justify-content-center">
					<ul class="pagination">
						{if !isset($pagination[1])}
							<li class="pagination-item"><a href="{$currentIndex}" data-pagination="1">1</a></li>
							{if !isset($pagination[2])}
								<li class="pagination-item disabled"><span >...</span></li>
							{/if}
						{/if}
						{foreach $pagination as $page}
							<li class="pagination-item {if $page == $currentPage}active{/if}"><a href="{$currentIndex}" data-pagination="{$page}">{$page}</a></li>
						{/foreach}
						{if !isset($pagination[$pageLimit])}
							{if !isset($pagination[$pageLimit-1])}
								<li class="pagination-item disabled"><span >...</span></li>
							{/if}
							<li class="pagination-item"><a href="{$currentIndex}" data-pagination="{$pageLimit}">{$pageLimit}</a></li>
						{/if}
					</ul>
				</div>
			{/if}
		</div>
	{else}
		<div class="text-center empty-properties-container">
			<div class="row">
				<div class="empty-properties-image-container"></div>
			</div>
			<div class="row">
				<h2>{l s='No Hotel Found!!'}</h2>
			</div>
		</div>
	{/if}
{/block}
