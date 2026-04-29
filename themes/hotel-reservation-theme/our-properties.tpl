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
			<div class="title-container">
				<h1 class="text-center our-properties-header">{l s='Our Properties'}</h1>
				<div class="text-center our-properties-desc">
					<p>{$WK_HTL_SHORT_DESC|escape:'html':'UTF-8'}</p>
				</div>
			</div>
		{/block}

		{block name='displayPropertiesLocationBefore'}
			{hook h='displayPropertiesLocationBefore'}
		{/block}

		{block name='our_properties_location'}
			{if isset($hotelLocationArray) && $hotelLocationArray && isset($displayHotelMap) && $displayHotelMap}
				<div class="margin-top-20 margin-btm-20">
					<div class="col-xs-12 col-sm-12" id="googleMapWrapper">
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

		<div class="properties-page">
			{block name='our_properties_list'}
				<div class="row hotels-container">
					{foreach $hotelsInfo as $hotel}
						<div class="{if $hotelsInfo|count != 1}col-md-6 col-xs-12{else}col-md-6 col-md-offset-3{/if} margin-btm-30">
							<div class="hotel-address-container">
								<div class="col-xs-5">
									<img class="htl-img" style="width:100%" src="{$hotel['image_url']}">
								</div>
								<div class="col-xs-7">
									<p class="hotel-name"><span>{$hotel['hotel_name']}</span></p>
									<p class="hotel-branch-info-value"><span class="htl-map-icon"></span>{$hotel['address']}, {$hotel['city']}, {if {$hotel['state_name']}}{$hotel['state_name']},{/if} {$hotel['country_name']}, {$hotel['postcode']}</p>
									<p class="hotel-branch-info-value">
										<span class="htl-address-icon htl-phone-icon"></span>{$hotel['phone']}
									</p>
									<p class="hotel-branch-info-value">
										<span class="htl-address-icon htl-email-icon"></span>{$hotel['email']}
									</p>
									<div class="hotel-branch-info-actions">
										<a href="{$hotel['view_rooms_link']}" target="_blank" class="btn btn-primary view_rooms_btn col-sm-6 col-xs-12">{l s='View Property'}</a>
										{if ($hotel['latitude'] != 0 || $hotel['longitude'] != 0) && $viewOnMap}
											<a class="btn htl-map-direction-btn col-sm-6 col-xs-12" href="http://maps.google.com/maps?daddr=({$hotel['latitude']},{$hotel['longitude']})" target="_blank">{l s='View on map'}</a>
										{/if}
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
				<div class="row pagination-container">
					<ul class="pagination">
						{if !isset($pagination[1])}
							<li><a href="{$currentIndex}" data-pagination="1">1</a></li>
							{if !isset($pagination[2])}
								<li><span disabled>...</span></li>
							{/if}
						{/if}
						{foreach $pagination as $page}
							<li><a href="{$currentIndex}" data-pagination="{$page}" {if $page == $currentPage}class="active"{/if}>{$page}</a></li>
						{/foreach}
						{if !isset($pagination[$pageLimit])}
							{if !isset($pagination[$pageLimit-1])}
								<li><span disabled>...</span></li>
							{/if}
							<li><a href="{$currentIndex}" data-pagination="{$pageLimit}">{$pageLimit}</a></li>
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
