{**
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

<div class="row">
	<div class="col-xs-12">
		<section id="dashguestcycle" class="dashboard-top widget allow_push">
			<div class="badges-wrapper">
				{hook h="displayDashboardBadgeListBefore"}
				<div class="badge-wrapper">
					<div class="badge-item label-tooltip" data-toggle="tooltip" data-original-title="{l s='The number of arrivals scheduled for today.' mod='dashguestcycle'}">
						<div class="badge-strip" style="background-color: #266FFE;"></div>
						<div class="badge-content-wrapper">
							<div class="title-wrapper">
								<p class="text-center">{l s="Arrivals" mod='dashguestcycle'}</p>
							</div>
							<div class="value-wrapper">
								<p class="text-center">
									<span id="dgc_arrived"></span>/<span id="dgc_total_arrivals"></span>
								</p>
							</div>
						</div>
					</div>
				</div>
				<div class="badge-wrapper">
					<div class="badge-item label-tooltip" data-toggle="tooltip" data-original-title="{l s='The number of departures scheduled for today.' mod='dashguestcycle'}">
						<div class="badge-strip" style="background-color: #72C3F0;"></div>
						<div class="badge-content-wrapper">
							<div class="title-wrapper">
								<p class="text-center">{l s="Departures" mod='dashguestcycle'}</p>
							</div>
							<div class="value-wrapper">
								<p class="text-center">
									<span id="dgc_departed"></span>/<span id="dgc_total_departures"></span>
								</p>
							</div>
						</div>
					</div>
				</div>
				<div class="badge-wrapper">
					<div class="badge-item label-tooltip" data-toggle="tooltip" data-original-title="{l s='The number of new bookings created today so far.' mod='dashguestcycle'}">
						<div class="badge-strip" style="background-color: #56CE56;"></div>
						<div class="badge-content-wrapper">
							<div class="title-wrapper">
								<p class="text-center">{l s="New Bookings" mod='dashguestcycle'}</p>
							</div>
							<div class="value-wrapper">
								<p class="text-center">
									<span id="dgc_new_bookings"></span>
								</p>
							</div>
						</div>
					</div>
				</div>
				<div class="badge-wrapper">
					<div class="badge-item label-tooltip" data-toggle="tooltip" data-original-title="{l s='The count of rooms that are currently occupied by guests.' mod='dashguestcycle'}">
						<div class="badge-strip" style="background-color: #FFC148;"></div>
						<div class="badge-content-wrapper">
							<div class="title-wrapper">
								<p class="text-center">{l s='Occupied Rooms' mod='dashguestcycle'}</p>
							</div>
							<div class="value-wrapper">
								<p class="text-center">
									<span id="dgc_occupied"></span>
								</p>
							</div>
						</div>
					</div>
				</div>
				<div class="badge-wrapper">
					<div class="badge-item label-tooltip" data-toggle="tooltip" data-original-title="{l s='The number of new messages received from guests today.' mod='dashguestcycle'}">
						<div class="badge-strip" style="background-color: #A569DF;"></div>
						<div class="badge-content-wrapper">
							<div class="title-wrapper">
								<p class="text-center">
									{l s="Guest Messages" mod='dashguestcycle'}
								</p>
							</div>
							<div class="value-wrapper">
								<p class="text-center">
									<span id="dgc_new_messages"></span>
								</p>
							</div>
						</div>
					</div>
				</div>
				<div class="badge-wrapper">
					<div class="badge-item label-tooltip" data-toggle="tooltip" data-original-title="{l s='The number of bookings cancelled today so far.' mod='dashguestcycle'}">
						<div class="badge-strip" style="background-color: #FF4036;"></div>
						<div class="badge-content-wrapper">
							<div class="title-wrapper">
								<p class="text-center">
									{l s="Cancelled Bookings" mod='dashguestcycle'}
								</p>
							</div>
							<div class="value-wrapper">
								<p class="text-center">
									<span id="dgc_cancelled_bookings"></span>
								</p>
							</div>
						</div>
					</div>
				</div>
				<div class="badge-wrapper">
					<div class="badge-item label-tooltip" data-toggle="tooltip" data-original-title="{l s='The number of adults and children scheduled to stay today.' mod='dashguestcycle'}">
						<div class="badge-strip" style="background-color: #FF809E;"></div>
						<div class="badge-content-wrapper">
							<div class="title-wrapper">
								<p class="text-center">
									{l s="Guests (Adults/Children)" mod='dashguestcycle'}
								</p>
							</div>
							<div class="value-wrapper">
								<p class="text-center">
									<span id="dgc_guests_adults"></span>/<span id="dgc_guests_children"></span>
								</p>
							</div>
						</div>
					</div>
				</div>
				{hook h="displayDashboardBadgeListAfter"}
			</div>
		</section>
	</div>
</div>
