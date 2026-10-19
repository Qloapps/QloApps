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
	{if $show_section_guest_info}
		<tr><td colspan="12" height="10">&nbsp;</td></tr>
		<tr>
			<td colspan="12">
				<table class="bordered-table" width="100%" cellpadding="5" cellspacing="0" nobr="true">
					<tbody>
						<tr>
							<td class="white">
								<table width="100%" cellpadding="4" cellspacing="0">
									{if $labels.title || $labels.full_name}
									<tr>
										{if $labels.title && $labels.full_name}
											<td width="25%" class="bold">{l s=$labels.title pdf='true'}:</td>
											<td width="25%" class="white">
												<span style="font-family: freeserif;">&#9633;</span> {l s='Mr.' pdf='true'} &nbsp;
												<span style="font-family: freeserif;">&#9633;</span> {l s='Ms.' pdf='true'} &nbsp;
											</td>
											<td width="25%" class="bold">{l s=$labels.full_name pdf='true'}:</td>
											<td width="25%" class="white">________________________</td>
										{elseif $labels.title}
											<td width="25%" class="bold">{l s=$labels.title pdf='true'}:</td>
											<td width="75%" colspan="3" class="white">
												<span style="font-family: freeserif;">&#9633;</span> {l s='Mr.' pdf='true'} &nbsp;
												<span style="font-family: freeserif;">&#9633;</span> {l s='Ms.' pdf='true'} &nbsp;
											</td>
										{else}
											<td width="25%" class="bold">{l s=$labels.full_name pdf='true'}:</td>
											<td width="75%" colspan="3" class="white">________________________</td>
										{/if}
									</tr>
									{/if}
									{if $labels.phone || $labels.email}
									<tr>
										{if $labels.phone && $labels.email}
											<td width="25%" class="bold">{l s=$labels.phone pdf='true'}:</td>
											<td width="25%" class="white">________________________</td>
											<td width="25%" class="bold">{l s=$labels.email pdf='true'}:</td>
											<td width="25%" class="white">________________________</td>
										{elseif $labels.phone}
											<td width="25%" class="bold">{l s=$labels.phone pdf='true'}:</td>
											<td width="75%" colspan="3" class="white">________________________</td>
										{else}
											<td width="25%" class="bold">{l s=$labels.email pdf='true'}:</td>
											<td width="75%" colspan="3" class="white">________________________</td>
										{/if}
									</tr>
									{/if}
									{if $labels.dob || $labels.nationality}
									<tr>
										{if $labels.dob && $labels.nationality}
											<td width="25%" class="bold">{l s=$labels.dob pdf='true'}:</td>
											<td width="25%" class="white">____ / ____ / ________</td>
											<td width="25%" class="bold">{l s=$labels.nationality pdf='true'}:</td>
											<td width="25%" class="white">________________________</td>
										{elseif $labels.dob}
											<td width="25%" class="bold">{l s=$labels.dob pdf='true'}:</td>
											<td width="75%" colspan="3" class="white">____ / ____ / ________</td>
										{else}
											<td width="25%" class="bold">{l s=$labels.nationality pdf='true'}:</td>
											<td width="75%" colspan="3" class="white">________________________</td>
										{/if}
									</tr>
									{/if}
									{if $labels.city_country || $labels.postal_code}
									<tr>
										{if $labels.city_country && $labels.postal_code}
											<td width="25%" class="bold">{l s=$labels.city_country pdf='true'}:</td>
											<td width="25%" class="white">________________________</td>
											<td width="25%" class="bold">{l s=$labels.postal_code pdf='true'}:</td>
											<td width="25%" class="white">________________________</td>
										{elseif $labels.city_country}
											<td width="25%" class="bold">{l s=$labels.city_country pdf='true'}:</td>
											<td width="75%" colspan="3" class="white">________________________</td>
										{else}
											<td width="25%" class="bold">{l s=$labels.postal_code pdf='true'}:</td>
											<td width="75%" colspan="3" class="white">________________________</td>
										{/if}
									</tr>
									{/if}
									{if $labels.address}
									<tr>
										<td width="25%" class="bold">{l s=$labels.address pdf='true'}:</td>
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
	{if $show_section_travel_info}
		<tr><td colspan="12" height="10">&nbsp;</td></tr>
		<tr>
			<td colspan="12">
				<table class="bordered-table" width="100%" cellpadding="5" cellspacing="0" nobr="true">
					<tbody>
						<tr>
							<td class="white">
								<table width="100%" cellpadding="4" cellspacing="0">
									{if $labels.arrived_from || $labels.next_dest}
									<tr>
										{if $labels.arrived_from && $labels.next_dest}
											<td width="25%" class="bold">{l s=$labels.arrived_from pdf='true'}:</td>
											<td width="25%" class="white">________________________</td>
											<td width="25%" class="bold">{l s=$labels.next_dest pdf='true'}:</td>
											<td width="25%" class="white">___________________</td>
										{elseif $labels.arrived_from}
											<td width="25%" class="bold">{l s=$labels.arrived_from pdf='true'}:</td>
											<td width="75%" colspan="3" class="white">________________________</td>
										{else}
											<td width="25%" class="bold">{l s=$labels.next_dest pdf='true'}:</td>
											<td width="75%" colspan="3" class="white">___________________</td>
										{/if}
									</tr>
									{/if}
									{if $labels.flight || $labels.vehicle}
									<tr>
										{if $labels.flight && $labels.vehicle}
											<td width="25%" class="bold">{l s=$labels.flight pdf='true'}:</td>
											<td width="25%" class="white">________________________</td>
											<td width="25%" class="bold">{l s=$labels.vehicle pdf='true'}:</td>
											<td width="25%" class="white">___________________</td>
										{elseif $labels.flight}
											<td width="25%" class="bold">{l s=$labels.flight pdf='true'}:</td>
											<td width="75%" colspan="3" class="white">________________________</td>
										{else}
											<td width="25%" class="bold">{l s=$labels.vehicle pdf='true'}:</td>
											<td width="75%" colspan="3" class="white">___________________</td>
										{/if}
									</tr>
									{/if}
									{if $labels.purpose}
									<tr>
										<td width="25%" class="bold">{l s=$labels.purpose pdf='true'}:</td>
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
	{if $show_section_booking_info}
		<tr><td colspan="12" height="10">&nbsp;</td></tr>
		<tr>
			<td colspan="12">
				<table class="bordered-table" width="100%" cellpadding="5" cellspacing="0" nobr="true">
					<tbody>
						<tr>
							<td class="white">
								<table width="100%" cellpadding="4" cellspacing="0">
									{if $labels.booking_ref || $labels.booking_rate}
									<tr>
										{if $labels.booking_ref && $labels.booking_rate}
											<td width="25%" class="bold">{l s=$labels.booking_ref pdf='true'}:</td>
											<td width="25%" class="white">{$booking_reference|escape:'html':'UTF-8'}</td>
											<td width="25%" class="bold">{l s=$labels.booking_rate pdf='true'}:</td>
											<td width="25%" class="white">
												{if $rate_per_night}{$rate_per_night|escape:'html':'UTF-8'}{else}__________{/if}
											</td>
										{elseif $labels.booking_ref}
											<td width="25%" class="bold">{l s=$labels.booking_ref pdf='true'}:</td>
											<td width="75%" colspan="3" class="white">{$booking_reference|escape:'html':'UTF-8'}</td>
										{else}
											<td width="25%" class="bold">{l s=$labels.booking_rate pdf='true'}:</td>
											<td width="75%" colspan="3" class="white">
												{if $rate_per_night}{$rate_per_night|escape:'html':'UTF-8'}{else}__________{/if}
											</td>
										{/if}
									</tr>
									{/if}
									{if $labels.booking_arrival || $labels.booking_departure}
									<tr>
										{if $labels.booking_arrival && $labels.booking_departure}
											<td width="25%" class="bold">{l s=$labels.booking_arrival pdf='true'}:</td>
											<td width="25%" class="white">
												{if $arrival_date_time}{$arrival_date_time|escape:'html':'UTF-8'}{else}____ / ____ / ____  ______{/if}
											</td>
											<td width="25%" class="bold">{l s=$labels.booking_departure pdf='true'}:</td>
											<td width="25%" class="white">
												{if $departure_date_time}{$departure_date_time|escape:'html':'UTF-8'}{else}____ / ____ / ____  ______{/if}
											</td>
										{elseif $labels.booking_arrival}
											<td width="25%" class="bold">{l s=$labels.booking_arrival pdf='true'}:</td>
											<td width="75%" colspan="3" class="white">
												{if $arrival_date_time}{$arrival_date_time|escape:'html':'UTF-8'}{else}____ / ____ / ____  ______{/if}
											</td>
										{else}
											<td width="25%" class="bold">{l s=$labels.booking_departure pdf='true'}:</td>
											<td width="75%" colspan="3" class="white">
												{if $departure_date_time}{$departure_date_time|escape:'html':'UTF-8'}{else}____ / ____ / ____  ______{/if}
											</td>
										{/if}
									</tr>
									{/if}
									{if $labels.booking_room_type || $labels.booking_room_number}
									<tr>
										{if $labels.booking_room_type && $labels.booking_room_number}
											<td width="25%" class="bold">{l s=$labels.booking_room_type pdf='true'}:</td>
											<td width="25%" class="white">{$room_type|escape:'html':'UTF-8'}</td>
											<td width="25%" class="bold">{l s=$labels.booking_room_number pdf='true'}:</td>
											<td width="25%" class="white">{$room_number|escape:'html':'UTF-8'}</td>
										{elseif $labels.booking_room_type}
											<td width="25%" class="bold">{l s=$labels.booking_room_type pdf='true'}:</td>
											<td width="75%" colspan="3" class="white">{$room_type|escape:'html':'UTF-8'}</td>
										{else}
											<td width="25%" class="bold">{l s=$labels.booking_room_number pdf='true'}:</td>
											<td width="75%" colspan="3" class="white">{$room_number|escape:'html':'UTF-8'}</td>
										{/if}
									</tr>
									{/if}
									{if $labels.num_guests}
									<tr>
										<td width="25%" class="bold">{l s=$labels.num_guests pdf='true'}:</td>
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
	{if $show_section_identification}
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
									{if $labels.id_proof}
									<tr>
										<td width="25%" class="bold">{l s=$labels.id_proof pdf='true'}:</td>
										<td width="75%" class="white">________________________________</td>
									</tr>
									{/if}
									{if $labels.id_number}
									<tr>
										<td width="25%" class="bold">{l s=$labels.id_number pdf='true'}:</td>
										<td width="75%" class="white">________________________________</td>
									</tr>
									{/if}
								</table>
							</td>
						</tr>
						{/if}
						{if $int_id}
						<tr>
							<td class="white">
								<strong><span style="font-family: freeserif; font-size: 14pt;">&#9633;</span> {l s='INTERNATIONAL GUEST' pdf='true'}</strong><br /><br />
								<table width="100%" cellpadding="4" cellspacing="0">
									{if $labels.passport || $labels.place_of_issue}
									<tr>
										{if $labels.passport && $labels.place_of_issue}
											<td width="25%" class="bold">{l s=$labels.passport pdf='true'}:</td>
											<td width="25%" class="white">____________________</td>
											<td width="25%" class="bold">{l s=$labels.place_of_issue pdf='true'}:</td>
											<td width="25%" class="white">____________________</td>
										{elseif $labels.passport}
											<td width="25%" class="bold">{l s=$labels.passport pdf='true'}:</td>
											<td width="75%" colspan="3" class="white">____________________</td>
										{else}
											<td width="25%" class="bold">{l s=$labels.place_of_issue pdf='true'}:</td>
											<td width="75%" colspan="3" class="white">____________________</td>
										{/if}
									</tr>
									{/if}
									{if $labels.date_of_issue || $labels.date_of_expiry}
									<tr>
										{if $labels.date_of_issue && $labels.date_of_expiry}
											<td width="25%" class="bold">{l s=$labels.date_of_issue pdf='true'}:</td>
											<td width="25%" class="white">____ / ____ / ____</td>
											<td width="25%" class="bold">{l s=$labels.date_of_expiry pdf='true'}:</td>
											<td width="25%" class="white">____ / ____ / ____</td>
										{elseif $labels.date_of_issue}
											<td width="25%" class="bold">{l s=$labels.date_of_issue pdf='true'}:</td>
											<td width="75%" colspan="3" class="white">____ / ____ / ____</td>
										{else}
											<td width="25%" class="bold">{l s=$labels.date_of_expiry pdf='true'}:</td>
											<td width="75%" colspan="3" class="white">____ / ____ / ____</td>
										{/if}
									</tr>
									{/if}
									{if $labels.visa || $labels.valid_until}
									<tr>
										{if $labels.visa && $labels.valid_until}
											<td width="25%" class="bold">{l s=$labels.visa pdf='true'}:</td>
											<td width="25%" class="white">____________________</td>
											<td width="25%" class="bold">{l s=$labels.valid_until pdf='true'}:</td>
											<td width="25%" class="white">____ / ____ / ____</td>
										{elseif $labels.visa}
											<td width="25%" class="bold">{l s=$labels.visa pdf='true'}:</td>
											<td width="75%" colspan="3" class="white">____________________</td>
										{else}
											<td width="25%" class="bold">{l s=$labels.valid_until pdf='true'}:</td>
											<td width="75%" colspan="3" class="white">____ / ____ / ____</td>
										{/if}
									</tr>
									{/if}
									{if $labels.arrival_in_country}
									<tr>
										<td width="25%" class="bold">{l s=$labels.arrival_in_country pdf='true'}:</td>
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
	{if $show_section_additional_guests}
		<tr><td colspan="12" height="10">&nbsp;</td></tr>
		<tr>
			<td colspan="12">
				<table class="bordered-table" width="100%" cellpadding="5" cellspacing="0" nobr="true">
					<thead>
						<tr>
							<th class="header-left"> {$section_additional_guests|upper} ({l s='if any' pdf='true'})</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="white">
								<table width="100%" cellpadding="4" cellspacing="0">
									<tr>
										<th class="header small" width="8%">{l s='S.No.' pdf='true'}</th>
										{if $labels.addguest_name}<th class="header small" width="40%">{l s=$labels.addguest_name pdf='true'}</th>{/if}
										{if $labels.addguest_id_type}<th class="header small" width="20%">{l s=$labels.addguest_id_type pdf='true'}</th>{/if}
										{if $labels.addguest_id_number}<th class="header small" width="17%">{l s=$labels.addguest_id_number pdf='true'}</th>{/if}
										{if $labels.addguest_nationality}<th class="header small" width="15%">{l s=$labels.addguest_nationality pdf='true'}</th>{/if}
									</tr>
									{section name=ag loop=$additional_guests_rows}
										<tr class="color_line_even">
											<td class="center white">{$smarty.section.ag.iteration}</td>
											{if $labels.addguest_name}<td class="white">______________________________</td>{/if}
											{if $labels.addguest_id_type}<td class="white">________________</td>{/if}
											{if $labels.addguest_id_number}<td class="white">________________</td>{/if}
											{if $labels.addguest_nationality}<td class="white">____________</td>{/if}
										</tr>
									{/section}
									{if !$additional_guests_rows}
										<tr class="color_line_even">
											<td class="center white">1</td>
											{if $labels.addguest_name}<td class="white">______________________________</td>{/if}
											{if $labels.addguest_id_type}<td class="white">________________</td>{/if}
											{if $labels.addguest_id_number}<td class="white">________________</td>{/if}
											{if $labels.addguest_nationality}<td class="white">____________</td>{/if}
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
	{if $show_section_billing_corporate}
		<tr><td colspan="12" height="10">&nbsp;</td></tr>
		<tr>
			<td colspan="12">
				<table class="bordered-table" width="100%" cellpadding="5" cellspacing="0" nobr="true">
					<tbody>
						<tr>
							<td class="white">
								<table width="100%" cellpadding="4" cellspacing="0">
									{if $labels.company}
									<tr>
										<td width="25%" class="bold">{l s=$labels.company pdf='true'}:</td>
										<td width="75%" class="white">______________________________________________</td>
									</tr>
									{/if}
									{if $labels.tax_id}
									<tr>
										<td width="25%" class="bold">{l s=$labels.tax_id pdf='true'}:</td>
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
	{if $show_section_payment_deposit}
		<tr><td colspan="12" height="10">&nbsp;</td></tr>
		<tr>
			<td colspan="12">
				<table class="bordered-table" width="100%" cellpadding="5" cellspacing="0" nobr="true">
					<tbody>
						<tr>
							<td class="white">
								<table width="100%" cellpadding="4" cellspacing="0">
									{if $labels.payment_method}
									<tr>
										<td width="25%" class="bold">{l s=$labels.payment_method pdf='true'}:</td>
										<td width="75%" class="white">________________________________</td>
									</tr>
									{/if}
									{if $labels.card_number || $labels.security_deposit}
									<tr>
										{if $labels.card_number && $labels.security_deposit}
											<td width="25%" class="bold">{l s=$labels.card_number pdf='true'}:</td>
											<td width="35%" class="white">________________________________</td>
											<td width="20%" class="bold">{l s=$labels.security_deposit pdf='true'}:</td>
											<td width="20%" class="white">__________________</td>
										{elseif $labels.card_number}
											<td width="25%" class="bold">{l s=$labels.card_number pdf='true'}:</td>
											<td width="75%" colspan="3" class="white">________________________________</td>
										{else}
											<td width="25%" class="bold">{l s=$labels.security_deposit pdf='true'}:</td>
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
	{if $show_section_guest_signature}
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
									{if $labels.signature && $labels.sig_date}
										<td width="20%" class="bold">{l s=$labels.signature pdf='true'}:</td>
										<td width="50%" class="white">______________________________________</td>
										<td width="10%" class="bold">{l s=$labels.sig_date pdf='true'}:</td>
										<td width="20%" class="white">____ / ____ / ________</td>
									{elseif $labels.signature}
										<td width="20%" class="bold">{l s=$labels.signature pdf='true'}:</td>
										<td width="80%" colspan="3" class="white">______________________________________</td>
									{else}
										<td width="20%" class="bold">{l s=$labels.sig_date pdf='true'}:</td>
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
	{if $show_section_property_regs}
		<tr><td colspan="12" height="10">&nbsp;</td></tr>
		<tr>
			<td colspan="12">
				<table class="bordered-table" width="100%" cellpadding="5" cellspacing="0" nobr="true">
					<thead>
						<tr>
							<th class="header-left">{$section_property_regs|upper}</th>
						</tr>
					</thead>
					<tbody>
						{if $labels.checkin_time}
						<tr>
							<td class="white">
								<strong> {l s=$labels.checkin_time pdf='true'}:</strong>
								{if $hotel && $hotel->check_in && $hotel->check_in != '00:00:00'}{$hotel->check_in|escape:'html':'UTF-8'}{else}__________{/if}
								&nbsp;&nbsp;&nbsp;
								<strong>{l s=$labels.checkout_time pdf='true'}:</strong>
								{if $hotel && $hotel->check_out && $hotel->check_out != '00:00:00'}{$hotel->check_out|escape:'html':'UTF-8'}{else}__________{/if}
							</td>
						</tr>
						{/if}
						{if $labels.hotel_policies}
						<tr>
							<td class="white">
								<strong> {l s=$labels.hotel_policies pdf='true'}:</strong>
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
	{if $show_section_office_use}
		<tr><td colspan="12" height="10">&nbsp;</td></tr>
		<tr>
			<td colspan="12">
				<table class="bordered-table" width="100%" cellpadding="5" cellspacing="0" nobr="true">
					<tbody>
						<tr>
							<td class="white">
								<table width="100%" cellpadding="4" cellspacing="0">
									{if $labels.staff_name || $labels.office_checkin_time}
									<tr>
										{if $labels.staff_name && $labels.office_checkin_time}
											<td width="20%" class="bold">{l s=$labels.staff_name pdf='true'}:</td>
											<td width="30%" class="white">____________________</td>
											<td width="20%" class="bold">{l s=$labels.office_checkin_time pdf='true'}:</td>
											<td width="30%" class="white">____________________</td>
										{elseif $labels.staff_name}
											<td width="20%" class="bold">{l s=$labels.staff_name pdf='true'}:</td>
											<td width="80%" colspan="3" class="white">____________________</td>
										{else}
											<td width="20%" class="bold">{l s=$labels.office_checkin_time pdf='true'}:</td>
											<td width="80%" colspan="3" class="white">____________________</td>
										{/if}
									</tr>
									{/if}
									{if $labels.id_verified || $labels.reg_no}
									<tr>
										{if $labels.id_verified && $labels.reg_no}
											<td width="20%" class="bold">{l s=$labels.id_verified pdf='true'}:</td>
											<td width="30%" class="white"><span style="font-family: freeserif;">&#9633;</span> {l s='Yes' pdf='true'} &nbsp; <span style="font-family: freeserif;">&#9633;</span> {l s='No' pdf='true'}</td>
											<td width="20%" class="bold">{l s=$labels.reg_no pdf='true'}:</td>
											<td width="30%" class="white">____________________</td>
										{elseif $labels.id_verified}
											<td width="20%" class="bold">{l s=$labels.id_verified pdf='true'}:</td>
											<td width="80%" colspan="3" class="white"><span style="font-family: freeserif;">&#9633;</span> {l s='Yes' pdf='true'} &nbsp; <span style="font-family: freeserif;">&#9633;</span> {l s='No' pdf='true'}</td>
										{else}
											<td width="20%" class="bold">{l s=$labels.reg_no pdf='true'}:</td>
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
