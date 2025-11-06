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

{block name='header_hotel_block'}
	<div class="header-desc-container">
		<div class="header-desc-wrapper">
			<div class="header-desc-primary">
				<div class="container">
					<div class="row">
						<div class="col-md-offset-1 col-md-10 col-lg-offset-2 col-lg-8">
							<p class="header-desc-welcome">{l s='Welcome To' mod='hotelreservationsystem'}</p>
							<hr class="heasder-desc-hr-first"/>
							<div class="header-desc-inner-wrapper">
								{block name='header_hotel_chain_name'}
									<h1 class="header-hotel-name">{$WK_HTL_CHAIN_NAME|escape:'htmlall':'UTF-8'}</h1>
								{/block}
								{block name='header_hotel_description'}
									<p class="header-hotel-desc">{$WK_HTL_TAG_LINE|escape:'htmlall':'UTF-8'}</p>
								{/block}
								<hr class="heasder-desc-hr-second"/>
							</div>
						</div>
					</div>
					{block name='displayAfterHeaderHotelDesc'}
						{hook h="displayAfterHeaderHotelDesc"}
					{/block}
				</div>
			</div>
		</div>
	</div>
{/block}
