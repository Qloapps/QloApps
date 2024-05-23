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

{if isset($smarty.get.confirm)}
	<p class="alert alert-success">{l s='Your message has been successfully sent to our team.'}</p>
{/if}
{include file="$tpl_dir./errors.tpl"}
<div class="margin-top-50 htl-contact-page">
	<div class="row">
		<p class="contact-header col-sm-offset-2 col-sm-8">{l s='Contact Us'}</p>
		<p class="contact-desc col-sm-offset-2 col-sm-8">{l s='Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry`s standard dummy text.'}</p>
	</div>
	<div class="row margin-top-50">
		{if (isset($gblHtlAddress) && $gblHtlAddress) && (isset($gblHtlPhone) && $gblHtlPhone) && (isset($gblHtlEmail) && $gblHtlEmail)}
			<div class="col-sm-6">
				<div class="htl-global-address-div col-md-8 col-sm-12">
					{if isset($gblHtlPhone) && $gblHtlPhone }
						<div>
							<p class="global-address-header">{l s='Main Branch'}</p>
							<p class="global-address-value">
								{$gblHtlAddress}
							</p>
						</div>
					{/if}
					{if isset($gblHtlPhone) && $gblHtlPhone}
						<div>
							<p class="global-address-header">{l s='Phone'}</p>
							<p class="global-address-value">
								{$gblHtlPhone}
							</p>
						</div>
					{/if}
					{if isset($gblHtlEmail) && $gblHtlEmail}
						<div>
							<p class="global-address-header">{l s='Mail Us'}</p>
							<p class="global-address-value">
								{$gblHtlEmail}
							</p>
						</div>
					{/if}
				</div>
			</div>
		{/if}
		<div class="col-sm-6 {if !(isset($gblHtlAddress) && $gblHtlAddress) && !(isset($gblHtlPhone) && $gblHtlPhone) && !(isset($gblHtlEmail) && $gblHtlEmail)} col-sm-offset-3 {/if}">
			<form action="{$link->getPageLink('contact')}" method="post" class="contact-form-box" enctype="multipart/form-data">
				{if isset($customerThread.id_contact) && $customerThread.id_contact && $contacts|count}
					{assign var=flag value=true}
					{foreach from=$contacts item=contact}
						{if $contact.id_contact == $customerThread.id_contact}
							<input type="text" class="form-control" id="contact_name" name="contact_name" value="{$contact.name|escape:'html':'UTF-8'}" readonly="readonly" />
							<input type="hidden" name="id_contact" value="{$contact.id_contact|intval}" />
							{$flag=false}
						{/if}
					{/foreach}
					{if $flag && isset($contacts.0.id_contact)}
						<input type="text" class="form-control" id="contact_name" name="contact_name" value="{$contacts.0.name|escape:'html':'UTF-8'}" readonly="readonly" />
						<input type="hidden" name="id_contact" value="{$contacts.0.id_contact|intval}" />
					{/if}
				{else}
					<div class="form-group row">
						<div class="col-sm-12">
							<label for="message" class="control-label">
								{l s='Subject'}
							</label>
							<div class="dropdown">
								<button class="form-control contact_type_input" type="button" data-toggle="dropdown">
									<span id="contact_type" class="pull-left">{l s='Choose'}</span>
									<input type="hidden" id="id_contact" name="id_contact" value="0">
									<span class="arrow_span">
										<i class="icon icon-angle-down"></i>
									</span>
								</button>
								<ul class="dropdown-menu contact_type_ul">
									{foreach from=$contacts item=contact}
										<li  value="{$contact.id_contact|intval}"{if isset($smarty.request.id_contact) && $smarty.request.id_contact == $contact.id_contact} selected="selected"{/if}>{$contact.name|escape:'html':'UTF-8'}
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
				{/if}
				<div class="form-group row">
					<div class="col-sm-12">
						<label for="price" class="control-label">
							{l s='Email'}
						</label>
						{if isset($customerThread.email)}
							<input class="form-control contact_input" type="email" id="email" name="from" value="{$customerThread.email|escape:'html':'UTF-8'}" readonly="readonly" />
						{else}
							<input class="form-control contact_input validate" type="email" id="email" name="from" data-validate="isEmail" value="{$email|escape:'html':'UTF-8'}" />
						{/if}
					</div>
				</div>
				<div class="form-group row">
					<div class="col-sm-12">
						<label for="message" class="control-label">
							{l s='Message/Query'}
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
				{hook h='displayGDPRConsent' moduleName='contactform'}
				<div class="form-group">
					<input type="text" name="url" value="" class="hidden" />
					<input type="hidden" name="contactKey" value="{$contactKey}" />
					<button class="btn button button-medium contact_btn" type="submit" name="submitMessage" id="submitMessage" ><span>{l s='Send Message'}</span></button>
				</div>
			</form>
		</div>
	</div>
	{if isset($hotelsInfo) && $hotelsInfo}
		<div class="row hotels-container">
			<div class="col-sm-12 hotel-header">
				<span>{l s='Our Hotels'}</span>
			</div>
			{foreach $hotelsInfo as $hotel}
				<div class="col-sm-6 margin-bottom-50">
					<div class="hotel-city-container">
						<span class="htl-map-icon"></span><span>{$hotel['city']}</span>
					</div>
					<div class="hotel-address-container">
						<div class="col-xs-4">
							<img class="htl-img" style="width:100%" src="{$hotel['image_url']}">
						</div>
						<div class="col-xs-8">
							<p class="hotel-name"><span>{$hotel['hotel_name']}</span></p>
							<p class="hotel-branch-info-value">{$hotel['address']}, {$hotel['city']}, {if {$hotel['state_name']}}{$hotel['state_name']},{/if} {$hotel['country_name']}, {$hotel['postcode']}</p>
							{if $hotel['latitude'] != 0 || $hotel['longitude'] != 0}
								<p class="hotel-branch-info-value">
									<a class="btn htl-map-direction-btn" href="http://maps.google.com/maps?daddr=({$hotel['latitude']},{$hotel['longitude']})" target="_blank">
										<span class="">{l s='View on map'}</span>
									</a>
								</p>
							{/if}
							<p class="hotel-branch-info-value">
								<span class="htl-address-icon htl-phone-icon"></span>{$hotel['phone']}
							</p>
							<p class="hotel-branch-info-value">
								<span class="htl-address-icon htl-email-icon"></span>{$hotel['email']}
							</p>
						</div>
					</div>
				</div>
			{/foreach}
		</div>
	{/if}
	{if isset($hotelLocationArray)}
		<div class="row">
			<div class="col-xs-12 col-sm-12" id="googleMapWrapper">
				<div id="map"></div>
			</div>
		</div>
	{/if}
	<div style="clear:both;"></div>
</div>

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
