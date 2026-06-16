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
		<td colspan="12" class="center" style="font-size: 12pt; font-weight: bold; vertical-align: top;">
			{if $hotel}{$hotel->hotel_name|escape:'html':'UTF-8'}{/if}
		</td>
	</tr>
	<tr>
		<td colspan="12" class="center" style="vertical-align: top;">
			{if $hotel && $hotel->city}{$hotel->city|escape:'html':'UTF-8'}{if $hotel_country}, {/if}{/if}{if $hotel_country}{$hotel_country|escape:'html':'UTF-8'}{/if}
		</td>
	</tr>

	{* ===== SECTION 1: GUEST INFORMATION ===== *}
	{if !$guest_reg_card_fields || isset($guest_reg_card_fields[1])}
		{assign var=title value=(!$guest_reg_card_fields || isset($guest_reg_card_fields[1][1]))}
		{assign var=full_name value=(!$guest_reg_card_fields || isset($guest_reg_card_fields[1][2]))}
		{assign var=phone value=(!$guest_reg_card_fields || isset($guest_reg_card_fields[1][3]))}
		{assign var=email value=(!$guest_reg_card_fields || isset($guest_reg_card_fields[1][4]))}
		{assign var=dob value=(!$guest_reg_card_fields || isset($guest_reg_card_fields[1][5]))}
		{assign var=nationality value=(!$guest_reg_card_fields || isset($guest_reg_card_fields[1][6]))}
		{assign var=city_country value=(!$guest_reg_card_fields || isset($guest_reg_card_fields[1][7]))}
		{assign var=postal_code value=(!$guest_reg_card_fields || isset($guest_reg_card_fields[1][8]))}
		{assign var=address value=(!$guest_reg_card_fields || isset($guest_reg_card_fields[1][9]))}
		<tr><td colspan="12" height="10">&nbsp;</td></tr>
		<tr>
			<td colspan="12">
				<table class="bordered-table" width="100%" cellpadding="5" cellspacing="0" nobr="true">
					<tbody>
						<tr>
							<td class="white">
								<table width="100%" cellpadding="4" cellspacing="0">
									{if $title || $full_name}
									<tr>
										{if $title && $full_name}
											<td width="25%" class="bold">{l s='TITLE:' pdf='true'}</td>
											<td width="25%" class="white">
												<span style="font-family: freeserif;">&#9633;</span> {l s='Mr.' pdf='true'} &nbsp;
												<span style="font-family: freeserif;">&#9633;</span> {l s='Ms.' pdf='true'} &nbsp;
											</td>
											<td width="25%" class="bold">{l s='Full Name (As per ID):' pdf='true'}</td>
											<td width="25%" class="white">________________________</td>
										{elseif $title}
											<td width="25%" class="bold">{l s='TITLE:' pdf='true'}</td>
											<td width="75%" colspan="3" class="white">
												<span style="font-family: freeserif;">&#9633;</span> {l s='Mr.' pdf='true'} &nbsp;
												<span style="font-family: freeserif;">&#9633;</span> {l s='Ms.' pdf='true'} &nbsp;
											</td>
										{else}
											<td width="25%" class="bold">{l s='Full Name (As per ID):' pdf='true'}</td>
											<td width="75%" colspan="3" class="white">________________________</td>
										{/if}
									</tr>
									{/if}
									{if $phone || $email}
									<tr>
										{if $phone && $email}
											<td width="25%" class="bold">{l s='Phone / Mobile:' pdf='true'}</td>
											<td width="25%" class="white">________________________</td>
											<td width="25%" class="bold">{l s='Email:' pdf='true'}</td>
											<td width="25%" class="white">________________________</td>
										{elseif $phone}
											<td width="25%" class="bold">{l s='Phone / Mobile:' pdf='true'}</td>
											<td width="75%" colspan="3" class="white">________________________</td>
										{else}
											<td width="25%" class="bold">{l s='Email:' pdf='true'}</td>
											<td width="75%" colspan="3" class="white">________________________</td>
										{/if}
									</tr>
									{/if}
									{if $dob || $nationality}
									<tr>
										{if $dob && $nationality}
											<td width="25%" class="bold">{l s='Date of Birth:' pdf='true'}</td>
											<td width="25%" class="white">____ / ____ / ________</td>
											<td width="25%" class="bold">{l s='Nationality:' pdf='true'}</td>
											<td width="25%" class="white">________________________</td>
										{elseif $dob}
											<td width="25%" class="bold">{l s='Date of Birth:' pdf='true'}</td>
											<td width="75%" colspan="3" class="white">____ / ____ / ________</td>
										{else}
											<td width="25%" class="bold">{l s='Nationality:' pdf='true'}</td>
											<td width="75%" colspan="3" class="white">________________________</td>
										{/if}
									</tr>
									{/if}
									{if $city_country || $postal_code}
									<tr>
										{if $city_country && $postal_code}
											<td width="25%" class="bold">{l s='City / Country:' pdf='true'}</td>
											<td width="25%" class="white">________________________</td>
											<td width="25%" class="bold">{l s='Postal Code:' pdf='true'}</td>
											<td width="25%" class="white">________________________</td>
										{elseif $city_country}
											<td width="25%" class="bold">{l s='City / Country:' pdf='true'}</td>
											<td width="75%" colspan="3" class="white">________________________</td>
										{else}
											<td width="25%" class="bold">{l s='Postal Code:' pdf='true'}</td>
											<td width="75%" colspan="3" class="white">________________________</td>
										{/if}
									</tr>
									{/if}
									{if $address}
									<tr>
										<td width="25%" class="bold">{l s='Address:' pdf='true'}</td>
										<td width="75%" colspan="3" class="white">__________________________________________________________________________________</td>
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

	{* ===== SECTION 2: TRAVEL INFORMATION ===== *}
	{if !$guest_reg_card_fields || isset($guest_reg_card_fields[2])}
		{assign var=arrived_from value=(!$guest_reg_card_fields || isset($guest_reg_card_fields[2][1]))}
		{assign var=next_dest value=(!$guest_reg_card_fields || isset($guest_reg_card_fields[2][2]))}
		{assign var=flight value=(!$guest_reg_card_fields || isset($guest_reg_card_fields[2][3]))}
		{assign var=vehicle value=(!$guest_reg_card_fields || isset($guest_reg_card_fields[2][4]))}
		{assign var=purpose value=(!$guest_reg_card_fields || isset($guest_reg_card_fields[2][5]))}
		<tr><td colspan="12" height="10">&nbsp;</td></tr>
		<tr>
			<td colspan="12">
				<table class="bordered-table" width="100%" cellpadding="5" cellspacing="0" nobr="true">
					<tbody>
						<tr>
							<td class="white">
								<table width="100%" cellpadding="4" cellspacing="0">
									{if $arrived_from || $next_dest}
									<tr>
										{if $arrived_from && $next_dest}
											<td width="25%" class="bold">{l s='Arrived From:' pdf='true'}</td>
											<td width="25%" class="white">________________________</td>
											<td width="25%" class="bold">{l s='Next Destination:' pdf='true'}</td>
											<td width="25%" class="white">___________________</td>
										{elseif $arrived_from}
											<td width="25%" class="bold">{l s='Arrived From:' pdf='true'}</td>
											<td width="75%" colspan="3" class="white">________________________</td>
										{else}
											<td width="25%" class="bold">{l s='Next Destination:' pdf='true'}</td>
											<td width="75%" colspan="3" class="white">___________________</td>
										{/if}
									</tr>
									{/if}
									{if $flight || $vehicle}
									<tr>
										{if $flight && $vehicle}
											<td width="25%" class="bold">{l s='Flight / Train Number:' pdf='true'}</td>
											<td width="25%" class="white">________________________</td>
											<td width="25%" class="bold">{l s='Vehicle Reg. No.:' pdf='true'}</td>
											<td width="25%" class="white">___________________</td>
										{elseif $flight}
											<td width="25%" class="bold">{l s='Flight / Train Number:' pdf='true'}</td>
											<td width="75%" colspan="3" class="white">________________________</td>
										{else}
											<td width="25%" class="bold">{l s='Vehicle Reg. No.:' pdf='true'}</td>
											<td width="75%" colspan="3" class="white">___________________</td>
										{/if}
									</tr>
									{/if}
									{if $purpose}
									<tr>
										<td width="25%" class="bold">{l s='Purpose of Visit:' pdf='true'}</td>
										<td width="75%" colspan="3" class="white">________________________</td>
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

	{* ===== SECTION 3: BOOKING INFORMATION ===== *}
	{if !$guest_reg_card_fields || isset($guest_reg_card_fields[3])}
		{assign var=booking_ref value=(!$guest_reg_card_fields || isset($guest_reg_card_fields[3][1]))}
		{assign var=booking_rate value=(!$guest_reg_card_fields || isset($guest_reg_card_fields[3][2]))}
		{assign var=booking_arrival value=(!$guest_reg_card_fields || isset($guest_reg_card_fields[3][3]))}
		{assign var=booking_departure value=(!$guest_reg_card_fields || isset($guest_reg_card_fields[3][4]))}
		{assign var=booking_room_type value=(!$guest_reg_card_fields || isset($guest_reg_card_fields[3][5]))}
		{assign var=booking_room_number value=(!$guest_reg_card_fields || isset($guest_reg_card_fields[3][6]))}
		{assign var=num_guests value=(!$guest_reg_card_fields || isset($guest_reg_card_fields[3][7]))}
		<tr><td colspan="12" height="10">&nbsp;</td></tr>
		<tr>
			<td colspan="12">
				<table class="bordered-table" width="100%" cellpadding="5" cellspacing="0" nobr="true">
					<tbody>
						<tr>
							<td class="white">
								<table width="100%" cellpadding="4" cellspacing="0">
									{if $booking_ref || $booking_rate}
									<tr>
										{if $booking_ref && $booking_rate}
											<td width="25%" class="bold">{l s='Booking Reference No.:' pdf='true'}</td>
											<td width="25%" class="white">{$booking_reference|escape:'html':'UTF-8'}</td>
											<td width="25%" class="bold">{l s='Rate per Night:' pdf='true'}</td>
											<td width="25%" class="white">
												{if $rate_per_night}{$rate_per_night|escape:'html':'UTF-8'}{else}__________{/if}
											</td>
										{elseif $booking_ref}
											<td width="25%" class="bold">{l s='Booking Reference No.:' pdf='true'}</td>
											<td width="75%" colspan="3" class="white">{$booking_reference|escape:'html':'UTF-8'}</td>
										{else}
											<td width="25%" class="bold">{l s='Rate per Night:' pdf='true'}</td>
											<td width="75%" colspan="3" class="white">
												{if $rate_per_night}{$rate_per_night|escape:'html':'UTF-8'}{else}__________{/if}
											</td>
										{/if}
									</tr>
									{/if}
									{if $booking_arrival || $booking_departure}
									<tr>
										{if $booking_arrival && $booking_departure}
											<td width="25%" class="bold">{l s='Arrival Date & Time:' pdf='true'}</td>
											<td width="25%" class="white">
												{if $arrival_date_time}{$arrival_date_time|escape:'html':'UTF-8'}{else}____ / ____ / ____  ______{/if}
											</td>
											<td width="25%" class="bold">{l s='Departure Date & Time:' pdf='true'}</td>
											<td width="25%" class="white">
												{if $departure_date_time}{$departure_date_time|escape:'html':'UTF-8'}{else}____ / ____ / ____  ______{/if}
											</td>
										{elseif $booking_arrival}
											<td width="25%" class="bold">{l s='Arrival Date & Time:' pdf='true'}</td>
											<td width="75%" colspan="3" class="white">
												{if $arrival_date_time}{$arrival_date_time|escape:'html':'UTF-8'}{else}____ / ____ / ____  ______{/if}
											</td>
										{else}
											<td width="25%" class="bold">{l s='Departure Date & Time:' pdf='true'}</td>
											<td width="75%" colspan="3" class="white">
												{if $departure_date_time}{$departure_date_time|escape:'html':'UTF-8'}{else}____ / ____ / ____  ______{/if}
											</td>
										{/if}
									</tr>
									{/if}
									{if $booking_room_type || $booking_room_number}
									<tr>
										{if $booking_room_type && $booking_room_number}
											<td width="25%" class="bold">{l s='Room Type:' pdf='true'}</td>
											<td width="25%" class="white">{$room_type|escape:'html':'UTF-8'}</td>
											<td width="25%" class="bold">{l s='Room Number:' pdf='true'}</td>
											<td width="25%" class="white">{$room_number|escape:'html':'UTF-8'}</td>
										{elseif $booking_room_type}
											<td width="25%" class="bold">{l s='Room Type:' pdf='true'}</td>
											<td width="75%" colspan="3" class="white">{$room_type|escape:'html':'UTF-8'}</td>
										{else}
											<td width="25%" class="bold">{l s='Room Number:' pdf='true'}</td>
											<td width="75%" colspan="3" class="white">{$room_number|escape:'html':'UTF-8'}</td>
										{/if}
									</tr>
									{/if}
									{if $num_guests}
									<tr>
										<td width="25%" class="bold">{l s='Number of Guests:' pdf='true'}</td>
										<td width="25%" class="white">
											{l s='Adults:' pdf='true'} {$adults|escape:'html':'UTF-8'} &nbsp;
											{l s='Children:' pdf='true'} {$children|escape:'html':'UTF-8'}
										</td>
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

	{* ===== SECTION 4: IDENTIFICATION DOCUMENT ===== *}
	{if !$guest_reg_card_fields || isset($guest_reg_card_fields[4])}
		{assign var=id_proof value=(!$guest_reg_card_fields || isset($guest_reg_card_fields[4][1]))}
		{assign var=id_number value=(!$guest_reg_card_fields || isset($guest_reg_card_fields[4][2]))}
		{assign var=passport value=(!$guest_reg_card_fields || isset($guest_reg_card_fields[4][3]))}
		{assign var=place_of_issue value=(!$guest_reg_card_fields || isset($guest_reg_card_fields[4][4]))}
		{assign var=date_of_issue value=(!$guest_reg_card_fields || isset($guest_reg_card_fields[4][5]))}
		{assign var=date_of_expiry value=(!$guest_reg_card_fields || isset($guest_reg_card_fields[4][6]))}
		{assign var=visa value=(!$guest_reg_card_fields || isset($guest_reg_card_fields[4][7]))}
		{assign var=valid_until value=(!$guest_reg_card_fields || isset($guest_reg_card_fields[4][8]))}
		{assign var=arrival_in_country value=(!$guest_reg_card_fields || isset($guest_reg_card_fields[4][9]))}
		{assign var=local_id value=($id_proof || $id_number)}
		{assign var=intl_id value=($passport || $place_of_issue || $date_of_issue || $date_of_expiry || $visa || $valid_until || $arrival_in_country)}
		<tr><td colspan="12" height="10">&nbsp;</td></tr>
		<tr>
			<td colspan="12">
				<table class="bordered-table" width="100%" cellpadding="5" cellspacing="0" nobr="true">
					<tbody>
						{if $local_id}
						<tr>
							<td class="white">
								<strong><span style="font-family: freeserif; font-size: 14pt;">&#9633;</span> {l s='LOCAL GUEST' pdf='true'}</strong><br /><br />
								<table width="100%" cellpadding="4" cellspacing="0">
									{if $id_proof}
									<tr>
										<td width="25%" class="bold">{l s='Identity Proof:' pdf='true'}</td>
										<td width="75%" class="white">________________________________</td>
									</tr>
									{/if}
									{if $id_number}
									<tr>
										<td width="25%" class="bold">{l s='ID Number:' pdf='true'}</td>
										<td width="75%" class="white">________________________________</td>
									</tr>
									{/if}
								</table>
							</td>
						</tr>
						{/if}
						{if $intl_id}
						<tr>
							<td class="white">
								<strong><span style="font-family: freeserif; font-size: 14pt;">&#9633;</span> {l s='INTERNATIONAL GUEST' pdf='true'}</strong><br /><br />
								<table width="100%" cellpadding="4" cellspacing="0">
									{if $passport || $place_of_issue}
									<tr>
										{if $passport && $place_of_issue}
											<td width="25%" class="bold">{l s='Passport No.:' pdf='true'}</td>
											<td width="25%" class="white">____________________</td>
											<td width="25%" class="bold">{l s='Place of Issue:' pdf='true'}</td>
											<td width="25%" class="white">____________________</td>
										{elseif $passport}
											<td width="25%" class="bold">{l s='Passport No.:' pdf='true'}</td>
											<td width="75%" colspan="3" class="white">____________________</td>
										{else}
											<td width="25%" class="bold">{l s='Place of Issue:' pdf='true'}</td>
											<td width="75%" colspan="3" class="white">____________________</td>
										{/if}
									</tr>
									{/if}
									{if $date_of_issue || $date_of_expiry}
									<tr>
										{if $date_of_issue && $date_of_expiry}
											<td width="25%" class="bold">{l s='Date of Issue:' pdf='true'}</td>
											<td width="25%" class="white">____ / ____ / ____</td>
											<td width="25%" class="bold">{l s='Date of Expiry:' pdf='true'}</td>
											<td width="25%" class="white">____ / ____ / ____</td>
										{elseif $date_of_issue}
											<td width="25%" class="bold">{l s='Date of Issue:' pdf='true'}</td>
											<td width="75%" colspan="3" class="white">____ / ____ / ____</td>
										{else}
											<td width="25%" class="bold">{l s='Date of Expiry:' pdf='true'}</td>
											<td width="75%" colspan="3" class="white">____ / ____ / ____</td>
										{/if}
									</tr>
									{/if}
									{if $visa || $valid_until}
									<tr>
										{if $visa && $valid_until}
											<td width="25%" class="bold">{l s='Visa Number:' pdf='true'}</td>
											<td width="25%" class="white">____________________</td>
											<td width="25%" class="bold">{l s='Valid Until:' pdf='true'}</td>
											<td width="25%" class="white">____ / ____ / ____</td>
										{elseif $visa}
											<td width="25%" class="bold">{l s='Visa Number:' pdf='true'}</td>
											<td width="75%" colspan="3" class="white">____________________</td>
										{else}
											<td width="25%" class="bold">{l s='Valid Until:' pdf='true'}</td>
											<td width="75%" colspan="3" class="white">____ / ____ / ____</td>
										{/if}
									</tr>
									{/if}
									{if $arrival_in_country}
									<tr>
										<td width="25%" class="bold">{l s='Arrival Date in Country:' pdf='true'}</td>
										<td width="75%" class="white">____ / ____ / ____</td>
									</tr>
									{/if}
								</table>
							</td>
						</tr>
						{/if}
					</tbody>
				</table>
			</td>
		</tr>
	{/if}

	{* ===== SECTION 5: ADDITIONAL GUESTS ===== *}
	{if !$guest_reg_card_fields || isset($guest_reg_card_fields[5])}
		{assign var=addguest_name value=(!$guest_reg_card_fields || isset($guest_reg_card_fields[5][1]))}
		{assign var=addguest_id_type value=(!$guest_reg_card_fields || isset($guest_reg_card_fields[5][2]))}
		{assign var=addguest_id_number value=(!$guest_reg_card_fields || isset($guest_reg_card_fields[5][3]))}
		{assign var=addguest_nationality value=(!$guest_reg_card_fields || isset($guest_reg_card_fields[5][4]))}
		<tr><td colspan="12" height="10">&nbsp;</td></tr>
		<tr>
			<td colspan="12">
				<table class="bordered-table" width="100%" cellpadding="5" cellspacing="0" nobr="true">
					<thead>
						<tr>
							<th class="header-left">{l s=' ADDITIONAL GUESTS (if any)' pdf='true'}</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="white">
								<table width="100%" cellpadding="4" cellspacing="0">
									<tr>
										<th class="header small" width="8%">{l s='S.No.' pdf='true'}</th>
										{if $addguest_name}<th class="header small" width="40%">{l s='Guest Name' pdf='true'}</th>{/if}
										{if $addguest_id_type}<th class="header small" width="20%">{l s='ID Type' pdf='true'}</th>{/if}
										{if $addguest_id_number}<th class="header small" width="17%">{l s='ID Number' pdf='true'}</th>{/if}
										{if $addguest_nationality}<th class="header small" width="15%">{l s='Nationality' pdf='true'}</th>{/if}
									</tr>
									{section name=ag loop=$additional_guests_rows}
										<tr class="color_line_even">
											<td class="center white">{$smarty.section.ag.iteration}</td>
											{if $addguest_name}<td class="white">______________________________</td>{/if}
											{if $addguest_id_type}<td class="white">________________</td>{/if}
											{if $addguest_id_number}<td class="white">________________</td>{/if}
											{if $addguest_nationality}<td class="white">____________</td>{/if}
										</tr>
									{/section}
									{if !$additional_guests_rows}
										<tr class="color_line_even">
											<td class="center white">1</td>
											{if $addguest_name}<td class="white">______________________________</td>{/if}
											{if $addguest_id_type}<td class="white">________________</td>{/if}
											{if $addguest_id_number}<td class="white">________________</td>{/if}
											{if $addguest_nationality}<td class="white">____________</td>{/if}
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

	{* ===== SECTION 6: BILLING & CORPORATE DETAILS ===== *}
	{if !$guest_reg_card_fields || isset($guest_reg_card_fields[6])}
		{assign var=company value=(!$guest_reg_card_fields || isset($guest_reg_card_fields[6][1]))}
		{assign var=tax_id value=(!$guest_reg_card_fields || isset($guest_reg_card_fields[6][2]))}
		<tr><td colspan="12" height="10">&nbsp;</td></tr>
		<tr>
			<td colspan="12">
				<table class="bordered-table" width="100%" cellpadding="5" cellspacing="0" nobr="true">
					<tbody>
						<tr>
							<td class="white">
								<table width="100%" cellpadding="4" cellspacing="0">
									{if $company}
									<tr>
										<td width="25%" class="bold">{l s='Company / Agent:' pdf='true'}</td>
										<td width="75%" class="white">______________________________________________</td>
									</tr>
									{/if}
									{if $tax_id}
									<tr>
										<td width="25%" class="bold">{l s='Tax ID / VAT No.:' pdf='true'}</td>
										<td width="75%" class="white">______________________________________________</td>
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

	{* ===== SECTION 7: PAYMENT & DEPOSIT ===== *}
	{if !$guest_reg_card_fields || isset($guest_reg_card_fields[7])}
		{assign var=payment_method value=(!$guest_reg_card_fields || isset($guest_reg_card_fields[7][1]))}
		{assign var=card_number value=(!$guest_reg_card_fields || isset($guest_reg_card_fields[7][2]))}
		{assign var=security_deposit value=(!$guest_reg_card_fields || isset($guest_reg_card_fields[7][3]))}
		<tr><td colspan="12" height="10">&nbsp;</td></tr>
		<tr>
			<td colspan="12">
				<table class="bordered-table" width="100%" cellpadding="5" cellspacing="0" nobr="true">
					<tbody>
						<tr>
							<td class="white">
								<table width="100%" cellpadding="4" cellspacing="0">
									{if $payment_method}
									<tr>
										<td width="25%" class="bold">{l s='Payment Method:' pdf='true'}</td>
										<td width="75%" class="white">________________________________</td>
									</tr>
									{/if}
									{if $card_number || $security_deposit}
									<tr>
										{if $card_number && $security_deposit}
											<td width="25%" class="bold">{l s='Credit Card Number:' pdf='true'}</td>
											<td width="35%" class="white">________________________________</td>
											<td width="20%" class="bold">{l s='Security Deposit:' pdf='true'}</td>
											<td width="20%" class="white">__________________</td>
										{elseif $card_number}
											<td width="25%" class="bold">{l s='Credit Card Number:' pdf='true'}</td>
											<td width="75%" colspan="3" class="white">________________________________</td>
										{else}
											<td width="25%" class="bold">{l s='Security Deposit:' pdf='true'}</td>
											<td width="75%" colspan="3" class="white">__________________</td>
										{/if}
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

	{* ===== SECTION 8: GUEST SIGNATURE ===== *}
	{if !$guest_reg_card_fields || isset($guest_reg_card_fields[8])}
		{assign var=signature value=(!$guest_reg_card_fields || isset($guest_reg_card_fields[8][1]))}
		{assign var=sig_date value=(!$guest_reg_card_fields || isset($guest_reg_card_fields[8][2]))}
	<tr>
		<td colspan="12" height="10">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="12">
			<table class="bordered-table" width="100%" cellpadding="5" cellspacing="0" nobr="true">
				<tbody>
					<tr>
						<td class="white">
							<table width="100%" cellpadding="4" cellspacing="0">
								<tr>
									{if $signature && $sig_date}
										<td width="20%" class="bold">{l s='Guest Signature:' pdf='true'}</td>
										<td width="50%" class="white">______________________________________</td>
										<td width="10%" class="bold">{l s='Date:' pdf='true'}</td>
										<td width="20%" class="white">____ / ____ / ________</td>
									{elseif $signature}
										<td width="20%" class="bold">{l s='Guest Signature:' pdf='true'}</td>
										<td width="80%" colspan="3" class="white">______________________________________</td>
									{else}
										<td width="20%" class="bold">{l s='Date:' pdf='true'}</td>
										<td width="80%" colspan="3" class="white">____ / ____ / ________</td>
									{/if}
								</tr>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
		</td>
	</tr>
	{/if}

	{* ===== SECTION 9: PROPERTY REGULATIONS ===== *}
	{if !$guest_reg_card_fields || isset($guest_reg_card_fields[9])}
		{assign var=checkin_checkout value=(!$guest_reg_card_fields || isset($guest_reg_card_fields[9][1]))}
		{assign var=hotel_policies value=(!$guest_reg_card_fields || isset($guest_reg_card_fields[9][2]))}
		<tr><td colspan="12" height="10">&nbsp;</td></tr>
		<tr>
			<td colspan="12">
				<table class="bordered-table" width="100%" cellpadding="5" cellspacing="0" nobr="true">
					<thead>
						<tr>
							<th class="header-left">{l s='PROPERTY REGULATIONS' pdf='true'}</th>
						</tr>
					</thead>
					<tbody>
						{if $checkin_checkout}
						<tr>
							<td class="white">
								<strong> {l s='Check-in Time:' pdf='true'}</strong>
								{if $hotel && $hotel->check_in && $hotel->check_in != '00:00:00'}{$hotel->check_in|escape:'html':'UTF-8'}{else}__________{/if}
								&nbsp;&nbsp;&nbsp;
								<strong>{l s='Check-out Time:' pdf='true'}</strong>
								{if $hotel && $hotel->check_out && $hotel->check_out != '00:00:00'}{$hotel->check_out|escape:'html':'UTF-8'}{else}__________{/if}
							</td>
						</tr>
						{/if}
						{if $hotel_policies}
						<tr>
							<td class="white">
								<strong> {l s='Hotel Policies:' pdf='true'}</strong>
								{if $hotel && $hotel->policies && !($hotel->policies|@is_array)}
									{$hotel->policies}
								{else}
									______________________________<br />
									______________________________<br />
									______________________________
								{/if}
							</td>
						</tr>
						{/if}
					</tbody>
				</table>
			</td>
		</tr>
	{/if}

	{* ===== SECTION 10: FOR OFFICE USE ONLY ===== *}
	{if !$guest_reg_card_fields || isset($guest_reg_card_fields[10])}
		{assign var=staff_name value=(!$guest_reg_card_fields || isset($guest_reg_card_fields[10][1]))}
		{assign var=office_checkin_time value=(!$guest_reg_card_fields || isset($guest_reg_card_fields[10][2]))}
		{assign var=id_verified value=(!$guest_reg_card_fields || isset($guest_reg_card_fields[10][3]))}
		{assign var=reg_no value=(!$guest_reg_card_fields || isset($guest_reg_card_fields[10][4]))}
		<tr><td colspan="12" height="10">&nbsp;</td></tr>
		<tr>
			<td colspan="12">
				<table class="bordered-table" width="100%" cellpadding="5" cellspacing="0" nobr="true">
					<tbody>
						<tr>
							<td class="white">
								<table width="100%" cellpadding="4" cellspacing="0">
									{if $staff_name || $office_checkin_time}
									<tr>
										{if $staff_name && $office_checkin_time}
											<td width="20%" class="bold">{l s='Staff Name:' pdf='true'}</td>
											<td width="30%" class="white">____________________</td>
											<td width="20%" class="bold">{l s='Check-in Time:' pdf='true'}</td>
											<td width="30%" class="white">____________________</td>
										{elseif $staff_name}
											<td width="20%" class="bold">{l s='Staff Name:' pdf='true'}</td>
											<td width="80%" colspan="3" class="white">____________________</td>
										{else}
											<td width="20%" class="bold">{l s='Check-in Time:' pdf='true'}</td>
											<td width="80%" colspan="3" class="white">____________________</td>
										{/if}
									</tr>
									{/if}
									{if $id_verified || $reg_no}
									<tr>
										{if $id_verified && $reg_no}
											<td width="20%" class="bold">{l s='ID Verified:' pdf='true'}</td>
											<td width="30%" class="white"><span style="font-family: freeserif;">&#9633;</span> {l s='Yes' pdf='true'} &nbsp; <span style="font-family: freeserif;">&#9633;</span> {l s='No' pdf='true'}</td>
											<td width="20%" class="bold">{l s='Registration No.:' pdf='true'}</td>
											<td width="30%" class="white">____________________</td>
										{elseif $id_verified}
											<td width="20%" class="bold">{l s='ID Verified:' pdf='true'}</td>
											<td width="80%" colspan="3" class="white"><span style="font-family: freeserif;">&#9633;</span> {l s='Yes' pdf='true'} &nbsp; <span style="font-family: freeserif;">&#9633;</span> {l s='No' pdf='true'}</td>
										{else}
											<td width="20%" class="bold">{l s='Registration No.:' pdf='true'}</td>
											<td width="80%" colspan="3" class="white">____________________</td>
										{/if}
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
