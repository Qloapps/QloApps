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
{capture name=path}{l s='Your address'}{/capture}
<div class="box">
	<h1 class="page-subheading">{l s='Your address'}</h1>
	<p class="info-title">
		{if isset($id_address) && (isset($smarty.post.alias) || isset($address->alias))}
			{l s='Modify address'}
			{if isset($smarty.post.alias)}
				"{$smarty.post.alias}"
			{else}
				{if isset($address->alias)}"{$address->alias|escape:'html':'UTF-8'}"{/if}
			{/if}
		{else}
			{l s='To add an address, please fill out the form below.'}
		{/if}
	</p>
	{include file="$tpl_dir./errors.tpl"}
	<p class="required"><sup>*</sup>{l s='Required field'}</p>
	<form action="{$link->getPageLink('address', true)|escape:'html':'UTF-8'}" method="post" class="std" id="add_address">
		<!--h3 class="page-subheading">{if isset($id_address)}{l s='Your address'}{else}{l s='New address'}{/if}</h3-->
		{assign var="stateExist" value=false}
		{assign var="postCodeExist" value=false}
		{assign var="dniExist" value=false}
		{assign var="homePhoneExist" value=false}
		{assign var="mobilePhoneExist" value=false}
		{assign var="atLeastOneExists" value=false}
		{foreach from=$ordered_adr_fields item=field_name}
			{if $field_name eq 'company'}
				<div class="form-group">
					<label for="company">{l s='Company'}{if isset($required_fields) && in_array($field_name, $required_fields)} <sup>*</sup>{/if}</label>
					<input class="form-control validate" data-validate="{$address_validation.$field_name.validate}" type="text" id="company" name="company" value="{if isset($smarty.post.company)}{$smarty.post.company}{else}{if isset($address->company)}{$address->company|escape:'html':'UTF-8'}{/if}{/if}" />
				</div>
			{/if}
			{if $field_name eq 'dni'}
			{assign var="dniExist" value=true}
			<div class="required form-group dni">
				<label for="dni">{l s='Identification number'} <sup>*</sup></label>
				<input class="form-control" data-validate="{$address_validation.$field_name.validate}" type="text" name="dni" id="dni" value="{if isset($smarty.post.dni)}{$smarty.post.dni}{else}{if isset($address->dni)}{$address->dni|escape:'html':'UTF-8'}{/if}{/if}" />
				<span class="form_info">{l s='DNI / NIF / NIE'}</span>
			</div>
			{/if}
			{if $field_name eq 'firstname'}
				<div class="required form-group">
					<label for="firstname">{l s='First name'} <sup>*</sup></label>
					<input class="is_required validate form-control" data-validate="{$address_validation.$field_name.validate}" type="text" name="firstname" id="firstname" value="{if isset($smarty.post.firstname)}{$smarty.post.firstname}{else}{if isset($address->firstname)}{$address->firstname|escape:'html':'UTF-8'}{/if}{/if}" />
				</div>
			{/if}
			{if $field_name eq 'lastname'}
				<div class="required form-group">
					<label for="lastname">{l s='Last name'} <sup>*</sup></label>
					<input class="is_required validate form-control" data-validate="{$address_validation.$field_name.validate}" type="text" id="lastname" name="lastname" value="{if isset($smarty.post.lastname)}{$smarty.post.lastname}{else}{if isset($address->lastname)}{$address->lastname|escape:'html':'UTF-8'}{/if}{/if}" />
				</div>
			{/if}
			{if $field_name eq 'address1'}
				<div class="required form-group">
					<label for="address1">{l s='Address'} <sup>*</sup></label>
					<input class="is_required validate form-control" data-validate="{$address_validation.$field_name.validate}" type="text" id="address1" name="address1" value="{if isset($smarty.post.address1)}{$smarty.post.address1}{else}{if isset($address->address1)}{$address->address1|escape:'html':'UTF-8'}{/if}{/if}" />
				</div>
			{/if}
			{if $field_name eq 'address2'}
				<div class="required form-group">
					<label for="address2">{l s='Address (Line 2)'}{if isset($required_fields) && in_array($field_name, $required_fields)} <sup>*</sup>{/if}</label>
					<input class="validate form-control" data-validate="{$address_validation.$field_name.validate}" type="text" id="address2" name="address2" value="{if isset($smarty.post.address2)}{$smarty.post.address2}{else}{if isset($address->address2)}{$address->address2|escape:'html':'UTF-8'}{/if}{/if}" />
				</div>
			{/if}
			{if $field_name eq 'postcode'}
				{assign var="postCodeExist" value=true}
				<div class="required postcode form-group unvisible">
					<label for="postcode">{l s='Zip/Postal Code'} <sup>*</sup></label>
					<input class="is_required validate form-control" data-validate="{$address_validation.$field_name.validate}" type="text" id="postcode" name="postcode" value="{if isset($smarty.post.postcode)}{$smarty.post.postcode}{else}{if isset($address->postcode)}{$address->postcode|escape:'html':'UTF-8'}{/if}{/if}" />
				</div>
			{/if}
			{if $field_name eq 'city'}
				<div class="required form-group">
					<label for="city">{l s='City'} <sup>*</sup></label>
					<input class="is_required validate form-control" data-validate="{$address_validation.$field_name.validate}" type="text" name="city" id="city" value="{if isset($smarty.post.city)}{$smarty.post.city}{else}{if isset($address->city)}{$address->city|escape:'html':'UTF-8'}{/if}{/if}" maxlength="64" />
				</div>
				{* if customer hasn't update his layout address, country has to be verified but it's deprecated *}
			{/if}
			{if $field_name eq 'Country:name' || $field_name eq 'country' || $field_name eq 'Country:iso_code'}
				<div class="required form-group">
					<label for="id_country">{l s='Country'} <sup>*</sup></label>
					<select id="id_country" class="form-control" name="id_country">{$countries_list}</select>
				</div>
			{/if}
			{if $field_name eq 'State:name'}
				{assign var="stateExist" value=true}
				<div class="required id_state form-group">
					<label for="id_state">{l s='State'} <sup>*</sup></label>
					<select name="id_state" id="id_state" class="form-control">
						<option value="">-</option>
					</select>
				</div>
			{/if}
			{if $field_name eq 'phone'}
				{assign var="homePhoneExist" value=true}
				<div class="form-group phone-number">
					<label for="phone">{l s='Home phone'}{if isset($one_phone_at_least) && $one_phone_at_least} <sup>**</sup>{/if}</label>
					<input class="{if isset($one_phone_at_least) && $one_phone_at_least}is_required{/if} validate form-control" data-validate="{$address_validation.phone.validate}" type="tel" id="phone" name="phone" value="{if isset($smarty.post.phone)}{$smarty.post.phone}{else}{if isset($address->phone)}{$address->phone|escape:'html':'UTF-8'}{/if}{/if}"  />
				</div>
				{if isset($one_phone_at_least) && $one_phone_at_least}
					{assign var="atLeastOneExists" value=true}
					<p class="inline-infos required">** {l s='You must register at least one phone number.'}</p>
				{/if}
				<div class="clearfix"></div>
			{/if}
			{if $field_name eq 'phone_mobile'}
				{assign var="mobilePhoneExist" value=true}
				<div class="{if isset($one_phone_at_least) && $one_phone_at_least}required {/if}form-group">
					<label for="phone_mobile">{l s='Mobile phone'}{if isset($one_phone_at_least) && $one_phone_at_least} <sup>**</sup>{/if}</label>
					<input class="validate form-control" data-validate="{$address_validation.phone_mobile.validate}" type="tel" id="phone_mobile" name="phone_mobile" value="{if isset($smarty.post.phone_mobile)}{$smarty.post.phone_mobile}{else}{if isset($address->phone_mobile)}{$address->phone_mobile|escape:'html':'UTF-8'}{/if}{/if}" />
				</div>
			{/if}
		{/foreach}
		{if !$postCodeExist}
			<div class="required postcode form-group unvisible">
				<label for="postcode">{l s='Zip/Postal Code'} <sup>*</sup></label>
				<input class="is_required validate form-control" data-validate="{$address_validation.postcode.validate}" type="text" id="postcode" name="postcode" value="{if isset($smarty.post.postcode)}{$smarty.post.postcode}{else}{if isset($address->postcode)}{$address->postcode|escape:'html':'UTF-8'}{/if}{/if}" />
			</div>
		{/if}
		{if !$stateExist}
			<div class="required id_state form-group unvisible">
				<label for="id_state">{l s='State'} <sup>*</sup></label>
				<select name="id_state" id="id_state" class="form-control">
					<option value="">-</option>
				</select>
			</div>
		{/if}
		{if !$dniExist}
			<div class="required dni form-group unvisible">
				<label for="dni">{l s='Identification number'} <sup>*</sup></label>
				<input class="is_required form-control" data-validate="{$address_validation.dni.validate}" type="text" name="dni" id="dni" value="{if isset($smarty.post.dni)}{$smarty.post.dni}{else}{if isset($address->dni)}{$address->dni|escape:'html':'UTF-8'}{/if}{/if}" />
				<span class="form_info">{l s='DNI / NIF / NIE'}</span>
			</div>
		{/if}
		<div class="form-group">
			<label for="other">{l s='Additional information'}</label>
			<textarea class="validate form-control" data-validate="{$address_validation.other.validate}" id="other" name="other" cols="26" rows="3" >{if isset($smarty.post.other)}{$smarty.post.other}{else}{if isset($address->other)}{$address->other|escape:'html':'UTF-8'}{/if}{/if}</textarea>
		</div>
		{if !$homePhoneExist}
			<div class="form-group phone-number">
				<label for="phone">{l s='Home phone'}</label>
				<input class="{if isset($one_phone_at_least) && $one_phone_at_least}is_required{/if} validate form-control" data-validate="{$address_validation.phone.validate}" type="tel" id="phone" name="phone" value="{if isset($smarty.post.phone)}{$smarty.post.phone}{else}{if isset($address->phone)}{$address->phone|escape:'html':'UTF-8'}{/if}{/if}"  />
			</div>
		{/if}
		{if isset($one_phone_at_least) && $one_phone_at_least && !$atLeastOneExists}
			<p class="inline-infos required">{l s='You must register at least one phone number.'}</p>
		{/if}
		<div class="clearfix"></div>
		{if !$mobilePhoneExist}
			<div class="{if isset($one_phone_at_least) && $one_phone_at_least}required {/if}form-group">
				<label for="phone_mobile">{l s='Mobile phone'}{if isset($one_phone_at_least) && $one_phone_at_least} <sup>**</sup>{/if}</label>
				<input class="validate form-control" data-validate="{$address_validation.phone_mobile.validate}" type="tel" id="phone_mobile" name="phone_mobile" value="{if isset($smarty.post.phone_mobile)}{$smarty.post.phone_mobile}{else}{if isset($address->phone_mobile)}{$address->phone_mobile|escape:'html':'UTF-8'}{/if}{/if}" />
			</div>
		{/if}
		<div class="required form-group" id="adress_alias">
			<label for="alias">{l s='Please assign an address title for future reference.'} <sup>*</sup></label>
			<input type="text" id="alias" class="is_required validate form-control" data-validate="{$address_validation.alias.validate}" name="alias" value="{if isset($smarty.post.alias)}{$smarty.post.alias}{elseif isset($address->alias)}{$address->alias|escape:'html':'UTF-8'}{elseif !$select_address}{l s='My address'}{/if}" />

			{hook h='displayGDPRConsent' moduleName='addressform'}
		</div>
		<p class="submit2">
			{if isset($id_address)}<input type="hidden" name="id_address" value="{$id_address|intval}" />{/if}
			{if isset($back)}<input type="hidden" name="back" value="{$back}" />{/if}
			{if isset($mod)}<input type="hidden" name="mod" value="{$mod}" />{/if}
			{if isset($select_address)}<input type="hidden" name="select_address" value="{$select_address|intval}" />{/if}
			<input type="hidden" name="token" value="{$token}" />
			<button type="submit" name="submitAddress" id="submitAddress" class="btn btn-default button button-medium">
				<span>
					{l s='Save'}
					<i class="icon-chevron-right right"></i>
				</span>
			</button>
		</p>
	</form>
</div>
<ul class="footer_links clearfix">
	<li>
		<a class="btn btn-defaul button button-small" href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">
			<span><i class="icon-chevron-left"></i> {l s='Back to your account'}</span>
		</a>
	</li>
</ul>
{strip}
{if isset($smarty.post.id_state) && $smarty.post.id_state}
	{addJsDef idSelectedState=$smarty.post.id_state|intval}
{elseif isset($address->id_state) && $address->id_state}
	{addJsDef idSelectedState=$address->id_state|intval}
{else}
	{addJsDef idSelectedState=false}
{/if}
{if isset($smarty.post.id_country) && $smarty.post.id_country}
	{addJsDef idSelectedCountry=$smarty.post.id_country|intval}
{elseif isset($address->id_country) && $address->id_country}
	{addJsDef idSelectedCountry=$address->id_country|intval}
{else}
	{addJsDef idSelectedCountry=false}
{/if}
{if isset($countries)}
	{addJsDef countries=$countries}
{/if}
{/strip}
