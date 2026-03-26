{*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License version 3.0
* that is bundled with this package in the file LICENSE.txt
* It is also available through the world-wide-web at this URL:
* https://opensource.org/licenses/AFL-3.0
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your
* needs please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*}

{$style_tab}

<table width="100%" id="body" border="0" cellpadding="0" cellspacing="0" style="margin:0;">
	<tr>
		<td colspan="12">
			<table width="100%" cellpadding="3" cellspacing="0" nobr="true">
				<tr>
					<td width="100%" class="center" style="font-size: 14pt; font-weight: bold;">
						{l s='GUEST REGISTRATION CARD' pdf='true'}
					</td>
				</tr>
				<tr>
					<td width="100%" class="center">
						{if $registration_form.property.logo_path}
							<img src="{$registration_form.property.logo_path}" style="height:60px;" />
						{else}
							<strong>[PROPERTY LOGO]</strong>
						{/if}
					</td>
				</tr>
				<tr>
					<td width="100%" class="center" style="font-size: 12pt; font-weight: bold;">
						{$registration_form.hotel.name|escape:'html':'UTF-8'}
					</td>
				</tr>
				<tr>
					<td width="100%" class="center">
						{$registration_form.property.city_country|escape:'html':'UTF-8'}
					</td>
				</tr>
			</table>
		</td>
	</tr>

	<tr><td colspan="12" height="10">&nbsp;</td></tr>

	<tr>
		<td colspan="12">
			<table class="bordered-table" width="100%" cellpadding="5" cellspacing="0" nobr="true">
				<thead>
					<tr>
						{* <th class="header-left">{l s='1. GUEST INFORMATION' pdf='true'}</th> *}
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="white">
							<strong>{l s='TITLE:' pdf='true'}</strong>
							[ ] {l s='Mr.' pdf='true'} &nbsp; [ ] {l s='Ms.' pdf='true'} &nbsp;
						</td>
					</tr>
					<tr>
						<td class="white">
							<table width="100%" cellpadding="4" cellspacing="0">
								<tr>
									<td width="25%" class="bold">{l s='Full Name (As per ID):' pdf='true'}</td>
									<td width="75%" class="white">{$registration_form.guest.full_name|escape:'html':'UTF-8'}</td>
								</tr>
								<tr>
									<td width="25%" class="bold">{l s='Phone / Mobile:' pdf='true'}</td>
									<td width="25%" class="white">{$registration_form.guest.mobile|escape:'html':'UTF-8'}</td>
									<td width="15%" class="bold">{l s='Email:' pdf='true'}</td>
									<td width="35%" class="white">{$registration_form.guest.email|escape:'html':'UTF-8'}</td>
								</tr>
								<tr>
									<td width="25%" class="bold">{l s='Date of Birth:' pdf='true'}</td>
									<td width="25%" class="white">____ / ____ / ________</td>
									<td width="15%" class="bold">{l s='Nationality:' pdf='true'}</td>
									<td width="35%" class="white">________________________</td>
								</tr>
								<tr>
									<td width="25%" class="bold">{l s='City / Country:' pdf='true'}</td>
									<td width="45%" class="white">
										{if $registration_form.guest.city_country}
											{$registration_form.guest.city_country|escape:'html':'UTF-8'}
										{else}
											________________________
										{/if}
									</td>
									<td width="15%" class="bold">{l s='Postal Code:' pdf='true'}</td>
									<td width="15%" class="white">
										{if $registration_form.guest.postcode}
											{$registration_form.guest.postcode|escape:'html':'UTF-8'}
										{else}
											__________
										{/if}
									</td>
								</tr>
								<tr>
									<td width="25%" class="bold">{l s='Address:' pdf='true'}</td>
									<td width="75%" class="white">{$registration_form.guest.address}</td>
								</tr>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
		</td>
	</tr>

	<tr><td colspan="12" height="10">&nbsp;</td></tr>

	<tr>
		<td colspan="12">
			<table class="bordered-table" width="100%" cellpadding="5" cellspacing="0" nobr="true">
				<thead>
					<tr>
						{* <th class="header-left">{l s='2. TRAVEL INFORMATION' pdf='true'}</th> *}
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="white">
							<table width="100%" cellpadding="4" cellspacing="0">
								<tr>
									<td width="25%" class="bold">{l s='Arrived From:' pdf='true'}</td>
									<td width="30%" class="white">________________________</td>
									<td width="25%" class="bold">{l s='Next Destination:' pdf='true'}</td>
									<td width="20%" class="white">___________________</td>
								</tr>
								<tr>
                                    <td colspan="4" class="white">
                                        <strong>{l s='Purpose of Visit:' pdf='true'}</strong>	
                                        {if $registration_form.dynamic_fields.purpose_of_visit|@count}
                                            {foreach from=$registration_form.dynamic_fields.purpose_of_visit item=item}
                                                [ ] {$item.name|escape:'html':'UTF-8'} &nbsp;
                                            {/foreach}
                                        {else}
                                            ________________________
                                        {/if}
                                    </td>
                                </tr>
                                <tr>
                                    <td width="25%" class="bold">{l s='Flight / Train Number:' pdf='true'}</td>
                                    <td width="75%" colspan="3" class="white">___________________</td>
                                </tr>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
		</td>
	</tr>

	<tr><td colspan="12" height="10">&nbsp;</td></tr>

	<tr>
		<td colspan="12">
			<table class="bordered-table" width="100%" cellpadding="5" cellspacing="0" nobr="true">
				<thead>
					<tr>
						{* <th class="header-left">{l s='3. BOOKING INFORMATION' pdf='true'}</th> *}
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="white">
							<table width="100%" cellpadding="4" cellspacing="0">
								<tr>
									<td width="28%" class="bold">{l s='Booking Reference No.:' pdf='true'}</td>
									<td width="22%" class="white">{$registration_form.stay.booking_reference|escape:'html':'UTF-8'}</td>
									<td width="28%" class="bold">{l s='Vehicle Reg. No.:' pdf='true'}</td>
									<td width="22%" class="white">________________</td>
								</tr>
								<tr>
									<td width="28%" class="bold">{l s='Arrival Date & Time:' pdf='true'}</td>
									<td width="22%" class="white">
										{if $registration_form.stay.arrival_date_time}
											{$registration_form.stay.arrival_date_time|escape:'html':'UTF-8'}
										{else}
											____ / ____ / ____  ______
										{/if}
									</td>
									<td width="28%" class="bold">{l s='Departure Date & Time:' pdf='true'}</td>
									<td width="22%" class="white">
										{if $registration_form.stay.departure_date_time}
											{$registration_form.stay.departure_date_time|escape:'html':'UTF-8'}
										{else}
											____ / ____ / ____  ______
										{/if}
									</td>
								</tr>
								<tr>
									<td width="28%" class="bold">{l s='Room Type:' pdf='true'}</td>
									<td width="22%" class="white">{$registration_form.stay.room_type|escape:'html':'UTF-8'}</td>
									<td width="28%" class="bold">{l s='Room Number:' pdf='true'}</td>
									<td width="22%" class="white">{$registration_form.stay.room_number|escape:'html':'UTF-8'}</td>
								</tr>
								<tr>
									<td width="28%" class="bold">{l s='Number of Guests:' pdf='true'}</td>
									<td width="22%" class="white">
										{l s='Adults:' pdf='true'} {$registration_form.stay.adults|escape:'html':'UTF-8'} &nbsp;
										{l s='Children:' pdf='true'} {$registration_form.stay.children|escape:'html':'UTF-8'}
									</td>
									<td width="28%" class="bold">{l s='Rate per Night:' pdf='true'}</td>
									<td width="22%" class="white">
										{if $registration_form.stay.rate_per_night}
											{$registration_form.stay.rate_per_night|escape:'html':'UTF-8'}
										{else}
											__________
										{/if}
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
		</td>
	</tr>

	<tr><td colspan="12" height="10">&nbsp;</td></tr>

	<tr>
		<td colspan="12">
			<table class="bordered-table" width="100%" cellpadding="5" cellspacing="0" nobr="true">
				<thead>
					<tr>
						{* <th class="header-left">{l s='4. IDENTIFICATION DOCUMENT' pdf='true'}</th> *}
					</tr>
				</thead>
				<tbody>
					<tr>
                        <td class="white">
                            <strong>[ ] {l s='LOCAL GUEST' pdf='true'}</strong><br /><br />
                            <strong>{l s='Identity Proof:' pdf='true'}</strong> 
                            {if $registration_form.dynamic_fields.identity_proof|@count}
                                {foreach from=$registration_form.dynamic_fields.identity_proof item=item}
                                    [ ] {$item.name|escape:'html':'UTF-8'} &nbsp;
                                {/foreach}
                            {else}
                                ________________________________
                            {/if}
                            <br /><br />
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td width="20%" class="bold">{l s='ID Number:' pdf='true'}</td>
                                    <td width="80%" class="white">________________________________</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td class="white">
                            <strong>[ ] {l s='INTERNATIONAL GUEST' pdf='true'}</strong><br />
                            <table width="100%" cellpadding="4" cellspacing="0">
                                <tr>
                                    <td width="25%" class="bold">{l s='Passport No.:' pdf='true'}</td>
                                    <td width="25%" class="white">____________________</td>
                                    <td width="25%" class="bold">{l s='Place of Issue:' pdf='true'}</td>
                                    <td width="25%" class="white">____________________</td>
                                </tr>
                                <tr>
                                    <td width="25%" class="bold">{l s='Date of Issue:' pdf='true'}</td>
                                    <td width="25%" class="white">____ / ____ / ____</td>
                                    <td width="25%" class="bold">{l s='Date of Expiry:' pdf='true'}</td>
                                    <td width="25%" class="white">____ / ____ / ____</td>
                                </tr>
                                <tr>
                                    <td width="25%" class="bold">{l s='Visa Number:' pdf='true'}</td>
                                    <td width="25%" class="white">____________________</td>
                                    <td width="25%" class="bold">{l s='Valid Until:' pdf='true'}</td>
                                    <td width="25%" class="white">____ / ____ / ____</td>
                                </tr>
                                <tr>
                                    <td width="35%" class="bold">{l s='Arrival Date in Hotel Country:' pdf='true'}</td>
                                    <td width="65%" class="white">____ / ____ / ____</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
					<tr>
						<td class="white">
							<strong>[ ] {l s='INTERNATIONAL GUEST' pdf='true'}</strong><br />
							<table width="100%" cellpadding="4" cellspacing="0">
								<tr>
									<td width="25%" class="bold">{l s='Passport No.:' pdf='true'}</td>
									<td width="25%" class="white">____________________</td>
									<td width="25%" class="bold">{l s='Place of Issue:' pdf='true'}</td>
									<td width="25%" class="white">____________________</td>
								</tr>
								<tr>
									<td width="25%" class="bold">{l s='Date of Issue:' pdf='true'}</td>
									<td width="25%" class="white">____ / ____ / ____</td>
									<td width="25%" class="bold">{l s='Date of Expiry:' pdf='true'}</td>
									<td width="25%" class="white">____ / ____ / ____</td>
								</tr>
								<tr>
									<td width="25%" class="bold">{l s='Visa Number:' pdf='true'}</td>
									<td width="25%" class="white">____________________</td>
									<td width="25%" class="bold">{l s='Valid Until:' pdf='true'}</td>
									<td width="25%" class="white">____ / ____ / ____</td>
								</tr>
								<tr>
									<td width="35%" class="bold">{l s='Arrival Date in Hotel Country:' pdf='true'}</td>
									<td width="65%" class="white">____ / ____ / ____</td>
								</tr>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
		</td>
	</tr>

	{if $registration_form.config.show_additional_guests}
		<tr><td colspan="12" height="10">&nbsp;</td></tr>

		<tr>
			<td colspan="12">
				<table class="bordered-table" width="100%" cellpadding="5" cellspacing="0" nobr="true">
					<thead>
						<tr>
							<th class="header-left">{l s='ADDITIONAL GUESTS (if any)' pdf='true'}</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="white">
								<table width="100%" cellpadding="4" cellspacing="0">
									<tr>
										<th class="header small" width="8%">{l s='S.No.' pdf='true'}</th>
										<th class="header small" width="40%">{l s='Guest Name' pdf='true'}</th>
										<th class="header small" width="20%">{l s='ID Type' pdf='true'}</th>
										<th class="header small" width="17%">{l s='ID Number' pdf='true'}</th>
										<th class="header small" width="15%">{l s='Nationality' pdf='true'}</th>
									</tr>
									{section name=ag loop=$registration_form.property.additional_guests_rows}
										<tr class="color_line_even">
											<td class="center white">{$smarty.section.ag.iteration}</td>
											<td class="white">______________________________</td>
											<td class="white">________________</td>
											<td class="white">________________</td>
											<td class="white">____________</td>
										</tr>
									{/section}
									{if !$registration_form.property.additional_guests_rows}
										<tr class="color_line_even">
											<td class="center white">1</td>
											<td class="white">______________________________</td>
											<td class="white">________________</td>
											<td class="white">________________</td>
											<td class="white">____________</td>
										</tr>
									{/if}
								</table>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	{/if}

	{if $registration_form.config.show_billing_corporate_details}
		<tr><td colspan="12" height="10">&nbsp;</td></tr>

		<tr>
			<td colspan="12">
				<table class="bordered-table" width="100%" cellpadding="5" cellspacing="0" nobr="true">
					<thead>
						<tr>
							{* <th class="header-left">{l s='6. BILLING & CORPORATE DETAILS' pdf='true'}</th> *}
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="white">
								<table width="100%" cellpadding="4" cellspacing="0">
									<tr>
										<td width="25%" class="bold">{l s='Company / Agent:' pdf='true'}</td>
										<td width="75%" class="white">______________________________________________</td>
									</tr>
									<tr>
										<td width="25%" class="bold">{l s='Tax ID / VAT No.:' pdf='true'}</td>
										<td width="75%" class="white">______________________________________________</td>
									</tr>
								</table>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	{/if}

	{if $registration_form.config.show_payment_deposit}
		<tr><td colspan="12" height="10">&nbsp;</td></tr>

		<tr>
			<td colspan="12">
				<table class="bordered-table" width="100%" cellpadding="5" cellspacing="0" nobr="true">
					<thead>
						<tr>
							{* <th class="header-left">{l s='7. PAYMENT & DEPOSIT' pdf='true'}</th> *}
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="white">
								<strong>{l s='Payment Method:' pdf='true'}</strong>
								{if $registration_form.dynamic_fields.payment_method|@count}
									{foreach from=$registration_form.dynamic_fields.payment_method item=item}
										[ ] {$item.name|escape:'html':'UTF-8'} &nbsp;
									{/foreach}
								{else}
									________________________
								{/if}
							</td>
						</tr>
						<tr>
							<td class="white">
								<table width="100%" cellpadding="4" cellspacing="0">
									<tr>
										<td width="25%" class="bold">{l s='Credit Card Number:' pdf='true'}</td>
										<td width="40%" class="white">________________________________</td>
										<td width="20%" class="bold">{l s='Security Deposit:' pdf='true'}</td>
										<td width="15%" class="white">__________</td>
									</tr>
								</table>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	{/if}

	{if $registration_form.config.show_property_regulations}
		<tr><td colspan="12" height="10">&nbsp;</td></tr>

		<tr>
			<td colspan="12">
				<table class="bordered-table" width="100%" cellpadding="5" cellspacing="0" nobr="true">
					<thead>
						<tr>
							{* <th class="header-left">{l s='8. PROPERTY REGULATIONS' pdf='true'}</th> *}
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="white">
								<table width="100%" cellpadding="4" cellspacing="0">
									<tr>
										<td width="25%" class="bold">{l s='Guest Signature:' pdf='true'}</td>
										<td width="45%" class="white">______________________________</td>
										<td width="10%" class="bold">{l s='Date:' pdf='true'}</td>
										<td width="20%" class="white">____ / ____ / ________</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td class="white">
								<strong>{l s='Check-in Time:' pdf='true'}</strong>
								{if $registration_form.hotel.check_in_time}{$registration_form.hotel.check_in_time|escape:'html':'UTF-8'}{else}__________{/if}
								&nbsp;&nbsp;&nbsp;
								<strong>{l s='Check-out Time:' pdf='true'}</strong>
								{if $registration_form.hotel.check_out_time}{$registration_form.hotel.check_out_time|escape:'html':'UTF-8'}{else}__________{/if}
							</td>
						</tr>
						<tr>
							<td class="white">
								<strong>{l s='Hotel Policies:' pdf='true'}</strong><br />
								{if $registration_form.hotel.policies}
									{$registration_form.hotel.policies}
								{else}
									______________________________<br />
									______________________________<br />
									______________________________
								{/if}
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	{/if}

	{if $registration_form.config.show_office_use_only}
		<tr><td colspan="12" height="10">&nbsp;</td></tr>

		<tr>
			<td colspan="12">
				<table class="bordered-table" width="100%" cellpadding="5" cellspacing="0" nobr="true">
					<thead>
						<tr>
							{* <th class="header-left">{l s='FOR OFFICE USE ONLY' pdf='true'}</th> *}
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="white">
								<table width="100%" cellpadding="4" cellspacing="0">
									<tr>
										<td width="20%" class="bold">{l s='Staff Name:' pdf='true'}</td>
										<td width="30%" class="white">____________________</td>
										<td width="20%" class="bold">{l s='Check-in Time:' pdf='true'}</td>
										<td width="30%" class="white">____________________</td>
									</tr>
									<tr>
										<td width="20%" class="bold">{l s='ID Verified:' pdf='true'}</td>
										<td width="30%" class="white">[ ] {l s='Yes' pdf='true'} &nbsp; [ ] {l s='No' pdf='true'}</td>
										<td width="20%" class="bold">{l s='Registration No.:' pdf='true'}</td>
										<td width="30%" class="white">____________________</td>
									</tr>
								</table>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	{/if}

	{if $registration_form.config.show_footer}
		<tr><td colspan="12" height="10">&nbsp;</td></tr>

		<tr>
			<td colspan="12">
				<table width="100%" cellpadding="3" cellspacing="0" nobr="true">
					<tr>
						<td width="100%" class="center small">
							{if $registration_form.hotel.address}{$registration_form.hotel.address}{else}[Property Address]{/if}
							<br />
							{l s='Phone No.:' pdf='true'} {if $registration_form.hotel.phone}{$registration_form.hotel.phone|escape:'html':'UTF-8'}{else}__________{/if}
							&nbsp;|&nbsp;
							{l s='Email:' pdf='true'} {if $registration_form.hotel.email}{$registration_form.hotel.email|escape:'html':'UTF-8'}{else}__________{/if}
							&nbsp;|&nbsp;
							{l s='Website:' pdf='true'} {$registration_form.property.website|escape:'html':'UTF-8'}
						</td>
					</tr>
				</table>
			</td>
		</tr>
	{/if}

	{if isset($HOOK_DISPLAY_PDF)}
	<tr>
		<td colspan="12" height="10">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="12">
			{$HOOK_DISPLAY_PDF}
		</td>
	</tr>
	{/if}
</table>
