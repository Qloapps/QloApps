{*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{block name='contact_form'}
	<div class="my-5 container-md">
		{if isset($smarty.get.confirm)}
			<p class="alert alert-success">{l s='Your message has been successfully sent to our team.'}</p>
		{/if}
		{block name='errors'}
			{include file="$tpl_dir./errors.tpl"}
		{/block}
		<div class="text-center mb-5">
			<div class="contact-header mb-3 heading h4 font-weight-bold ">{l s='Contact Us'}</div>
			<div class="contact-desc">{l s='Reach out to us for any inquiries or assistance. We\'re here to help make your experience with us exceptional.'}</div>
		</div>
		<div class="row margin-top-50">
			{if (isset($gblHtlAddress) && $gblHtlAddress) && (isset($gblHtlPhone) && $gblHtlPhone) && (isset($gblHtlEmail) && $gblHtlEmail)}
				<div class="col-sm-6 my-4">
					{block name='contact_form_info'}
						<div class="htl-global-address-div col-12 card shadow py-4 rounded-lg">
							<div class="my-3">
								<div class="mb-3 text-secondary small"><i class="icon-building"></i> {l s='Main Branch'}</div>
								<div class="global-address-value">{$gblHtlAddress}</div>
							</div>
							<div class="mb-3">
								<div class="mb-3 text-secondary small"><i class="icon-phone"></i> {l s='Phone'}</div>
								<div class="global-address-value">{$gblHtlPhone}</div>
							</div>
							<div class="mb-3">
								<div class="mb-3 text-secondary small"><i class="icon-envelope"></i> {l s='Mail Us'}</div>
								<div class="global-address-value">{$gblHtlEmail}</div>
							</div>
							{if isset($gblHtlRegistrationNumber) && $gblHtlRegistrationNumber}
								<div class="mb-3">
									<div class="mb-3 text-secondary small"><i class="icon-book"></i> {l s='Registration number'}</div>
									<div class="global-address-value">{$gblHtlRegistrationNumber}</div>
								</div>
							{/if}
							{if isset($gblHtlFax) && $gblHtlFax}
								<div class="mb-3">
									<div class="mb-3 text-secondary small"><i class="icon-fax"></i> {l s='Fax'}</div>
									<div class="global-address-value">{$gblHtlFax}</div>
								</div>
							{/if}
						</div>
					{/block}
				</div>
			{/if}
			<div class="col-sm-6 {if !(isset($gblHtlAddress) && $gblHtlAddress) && !(isset($gblHtlPhone) && $gblHtlPhone) && !(isset($gblHtlEmail) && $gblHtlEmail)} col-sm-offset-3 {/if}">
				{block name='contact_form_content'}
				{if isset($customerThread.token)}
					<form action="{$link->getPageLink('contact', null, null, array('token' => $customerThread.token))}" method="post" class="contact-form-box card shadow p-4" enctype="multipart/form-data">
				{else}
					<form action="{$link->getPageLink('contact')}" method="post" class="contact-form-box card shadow p-4" enctype="multipart/form-data">
				{/if}
					{if isset($displayContactName) && $displayContactName}
						<div class="form-group row">
							<div class="col-sm-12">
								<label for="user_name" class="control-label">
									{l s='Name'}{if isset($contactNameRequired) && $contactNameRequired}*{/if}
								</label>
								<input class="form-control contact_input" type="text" id="user_name" name="user_name" value="{if isset($smarty.post.user_name)}{$smarty.post.user_name}{elseif isset($customerThread.user_name)}{$customerThread.user_name|escape:'html':'UTF-8'}{elseif isset($customerName)}{$customerName}{/if}" {if isset($customerThread.user_name)} readonly{/if}/>
							</div>
						</div>
					{/if}
						<div class="form-group row">
							<div class="col-sm-12">
								<label for="Email" class="control-label">
									{l s='Email'}*
								</label>
								{if isset($customerThread.email)}
									<input class="form-control contact_input" type="email" id="email" name="from" value="{if isset($customerThread.email)}{$customerThread.email|escape:'html':'UTF-8'}" readonly="readonly"{/if} />
								{else}
									<input class="form-control contact_input validate" type="email" id="email" name="from" data-validate="isEmail" value="{if isset($smarty.post.email)}{$smarty.post.email}{else}{$email|escape:'html':'UTF-8'}{/if}" />
								{/if}
							</div>
						</div>
					{if isset($displayContactPhone) && $displayContactPhone}
						<div class="form-group row">
							<div class="col-sm-12">
								<label for="phone" class="control-label">
									{l s='Phone'}{if isset($contactPhoneRequired) && $contactPhoneRequired}*{/if}
								</label>
								<input class="form-control contact_input" type="text" id="phone" name="phone" value="{if isset($smarty.post.phone)}{$smarty.post.phone}{else if isset($customerThread.phone)}{$customerThread.phone|escape:'html':'UTF-8'}{elseif isset($customerPhone)}{$customerPhone}{/if}" {if isset($customerThread.phone)}readonly="readonly"{/if}/>
							</div>
						</div>
					{/if}
						<div class="form-group row">
							<div class="col-sm-12">
								<label for="subject" class="control-label">
									{l s='Title'}*
								</label>
								<input class="form-control contact_input" type="text" id="subject" name="subject" value="{if isset($smarty.post.subject)}{$smarty.post.subject}{else if isset($customerThread.subject)}{$customerThread.subject|escape:'html':'UTF-8'}{/if}" {if isset($customerThread.subject)}readonly="readonly"{/if}/>
							</div>
						</div>
						{if !isset($customerThread.id_contact) && isset($allowContactSelection) && $allowContactSelection}
							<div class="form-group row">
								<div class="col-sm-12">
									<label for="message" class="control-label">
										{l s='Send To'}*
									</label>
									<div class="dropdown">
										<button class="form-control contact_type_input" type="button" data-toggle="dropdown">
											<span id="contact_type" class="float-left">{l s='Choose'}</span>
											<input type="hidden" id="id_contact" name="id_contact" value="0">
											<span class="arrow_span float-right">
												<i class="icon icon-angle-down"></i>
											</span>
										</button>
										<ul class="dropdown-menu contact_type_ul w-100">
											{foreach from=$contacts item=contact}
												<li class="dropdown-item " value="{$contact.id_contact|intval}"{if isset($smarty.request.id_contact) && $smarty.request.id_contact == $contact.id_contact} selected="selected"{/if}>{$contact.name|escape:'html':'UTF-8'}
												</li>
											{/foreach}

											{if isset($all_hotels_info) && $all_hotels_info}
												{foreach from=$all_hotels_info key=htl_k item=htl_v}
												{/foreach}
											{/if}
										</ul>
									</div>
								</div>
							</div>
						{elseif isset($customerThread.id_contact) && isset($allowContactSelection) && $allowContactSelection}
							<input type="hidden" id="id_contact" name="id_contact" value="{$customerThread.id_contact|escape:'html':'UTF-8'}"/>
						{/if}
						<div class="form-group row">
							<div class="col-sm-12">
								<label for="message" class="control-label">
									{l s='Message/Query'}*
								</label>
								<textarea class="form-control contact_textarea" id="message" name="message">{if isset($message)}{$message|escape:'html':'UTF-8'|stripslashes}{/if}</textarea>
							</div>
						</div>
						{if $fileupload == 1}
							<div class="form-group row">
								<div class="col-sm-12">
									<label for="fileUpload" class="control-label">
										{l s='Attach File'}
									</label>
									<input type="hidden" name="MAX_FILE_SIZE" value="{if isset($max_upload_size) && $max_upload_size}{$max_upload_size|intval}{else}2000000{/if}" />
									<input type="file" name="fileUpload" id="fileUpload" class="form-control" />
								</div>
							</div>
						{/if}
						<div class="form-group help_block">
							{l s='* Required fields'}
						</div>
						{hook h='displayGDPRConsent' moduleName='contactform'}
						{hook h='displayContactFormFieldsAfter'}
						<div class="form-group">
							<input type="hidden" name="url" value="" />
							<input type="hidden" name="contactKey" value="{$contactKey}" />
							<button class="btn btn-primary contact_btn" type="submit" name="submitMessage" id="submitMessage" ><span>{l s='Send Message'}</span></button>
						</div>
					</form>
				{/block}
			</div>
		</div>
		{block name='displayBeforeHotelBranchInformation'}
			{hook h='displayBeforeHotelBranchInformation'}
		{/block}
		{block name='contact_form_hotel_branches'}
			{if isset($displayHotels) && $displayHotels && isset($hotelsInfo) && $hotelsInfo}
				<div class="row hotels-container">
					<div class="col-sm-12 heading h4 text-center my-5">
						<span>{l s='Our Hotels'}</span>
					</div>
					{foreach $hotelsInfo as $hotel}
						<div class="col-sm-6 margin-bottom-50">
							<div class="hotel-city-container h9 mb-2">
								<span class="icon-location-dot"></span><span>{$hotel['city']}</span>
							</div>
							<div class="hotel-address-container card ">
								<div class="row col-12 p-3 ">
									<div class="col-5">
										<img class="max-width-200 rounded-sm" style="width:100%" src="{$hotel['image_url']}">
									</div>
									<div class="col-7 p-1">
										<div class="hotel-name h9 mb-2"><span>{$hotel['hotel_name']}</span></div>
										<div class="hotel-branch-info-value mb-2 small"><i class="icon-location-dot"></i>{$hotel['address']}, {$hotel['city']}, {if {$hotel['state_name']}}{$hotel['state_name']},{/if} {$hotel['country_name']}, {$hotel['postcode']}</div>
										{if ($hotel['latitude'] != 0 || $hotel['longitude'] != 0) && $viewOnMap}
											<div class="hotel-branch-info-value mb-2 small">
												<a class="btn htl-map-direction-btn" href="http://maps.google.com/maps?daddr=({$hotel['latitude']},{$hotel['longitude']})" target="_blank">
													<span class="">{l s='View on map'}</span>
												</a>
											</div>
										{/if}
										<div class="hotel-branch-info-value mb-2 small">
											<i class="icon-solid icon-mobile-screen"></i>{$hotel['phone']}
										</div>
										<div class="hotel-branch-info-value mb-2 small">
											<i class="icon-regular icon-envelope"></i>{$hotel['email']}
										</div>
									</div>
								</div>
							</div>
						</div>
					{/foreach}
				</div>
			{/if}
		{/block}
		{block name='displayAfterHotelBranchInformation'}
			{hook h='displayAfterHotelBranchInformation'}
		{/block}
		{block name='contact_form_hotel_locations'}
			{if isset($displayHotelMap) && $displayHotelMap && isset($hotelLocationArray)}
				<div class="row {if !(isset($displayHotels) && $displayHotels && isset($hotelsInfo) && $hotelsInfo)} margin-top-20{/if}">
					<div class="col-xs-12 col-sm-12" id="googleMapWrapper">
						<div id="map"></div>
					</div>
				</div>
			{/if}
			<div style="clear:both;"></div>
		{/block}
	</div>

	{block name='contact_form_js_vars'}
		{strip}
			{addJsDefL name='contact_fileDefaultHtml'}{l s='No file selected' js=1}{/addJsDefL}
			{addJsDefL name='contact_fileButtonHtml'}{l s='Choose File' js=1}{/addJsDefL}
			{addJsDefL name='contact_map_get_dirs'}{l s='Get Directions' js=1}{/addJsDefL}
		{/strip}
		{if isset($hotelLocationArray)}
			{strip}
				{addJsDef hotelLocationArray = $hotelLocationArray}
			{/strip}
		{else}
			{strip}
				{addJsDef hotelLocationArray = 0}
			{/strip}
		{/if}
	{/block}
{/block}
