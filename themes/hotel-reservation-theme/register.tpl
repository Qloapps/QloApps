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

{block name='register'}
	{block name='errors'}
		{include file="$tpl_dir./errors.tpl"}
	{/block}
    {assign var=stateExist value=false}
    {assign var=postCodeExist value=false}
    {assign var=dniExist value=false}
    <div class="row bg-color-grey-light p-md-5 justify-content-center">
        <div class="card col-lg-7 col-md-9 p-5 mb-4">
            {block name='authentication_account_creation_form'}
                <form action="{$link->getPageLink('register', true)|escape:'html':'UTF-8'}" method="post" id="account-creation_form" class="std box">
                    {block name='displayCustomerAccountFormTop'}
                        {$HOOK_CREATE_ACCOUNT_TOP}
                    {/block}
                    <div class="account_creation">
                        <h3 class="page-heading text-center text-bolder h5 font-weight-bold">{l s='Create Your Account'}</h3>
                        <hr>
						<div class="page-subheading text-center small">{l s='Please enter your details to create your account.'}</div>
						<div class="mt-5">
                            <div class="form-group d-flex ml-n3">
                                {foreach from=$genders key=k item=gender}
                                    <div class="radio-inline form-check">
                                        <label for="id_gender{$gender->id}" class="top">
                                            <input checked="" type="radio" name="id_gender" id="id_gender{$gender->id}" value="{$gender->id}" {if isset($smarty.post.id_gender) && $smarty.post.id_gender == $gender->id}checked="checked"{/if} />
                                            {$gender->name}
                                        </label>
                                    </div>
                                {/foreach}
                            </div>
                            <div class="required form-group">
                                <label for="customer_firstname">{l s='First name'} <sup>*</sup></label>
                                <input placeholder="{l s='Enter your first name'}" onkeyup="$('#firstname').val(this.value);" type="text" class="is_required validate form-control" data-validate="isName" id="customer_firstname" name="customer_firstname" value="{if isset($smarty.post.customer_firstname)}{$smarty.post.customer_firstname}{/if}" />
                            </div>
                            <div class="required form-group">
                                <label for="customer_lastname">{l s='Last name'} <sup>*</sup></label>
                                <input placeholder="{l s='Enter your last name'}" onkeyup="$('#lastname').val(this.value);" type="text" class="is_required validate form-control" data-validate="isName" id="customer_lastname" name="customer_lastname" value="{if isset($smarty.post.customer_lastname)}{$smarty.post.customer_lastname}{/if}" />
                            </div>
                            <div class="required form-group">
                                <label for="email">{l s='Email'} <sup>*</sup></label>
                                <input placeholder="{l s='Enter your eamil'}" type="email" class="is_required validate form-control" data-validate="isEmail" id="email" name="email" value="{if isset($smarty.post.email)}{$smarty.post.email}{/if}" />
                            </div>
                            <div class="required form-group">
                                <label for="customer_phone">{l s='Phone'} {if isset($one_phone_at_least) && $one_phone_at_least}<sup>*</sup>{/if}</label>
                                <input placeholder="{l s='Enter your phone'}" onkeyup="$('#phone').val(this.value);" class="is_required validate form-control" data-validate="isPhoneNumber" type="phone" name="customer_phone" id="customer_phone" value="{if isset($smarty.post.customer_phone)}{$smarty.post.customer_phone}{/if}" />
                            </div>
                            <div class="required password form-group">
                                <label for="passwd">{l s='Password'} <sup>*</sup></label>
                                <input placeholder="{l s='Enter your password'}" type="password" class="is_required validate form-control" data-validate="isPasswd" name="passwd" id="passwd" />
                                <span class="help_block">{l s='(Five characters minimum)'}</span>
                            </div>
                            {if isset($birthday) && $birthday}
                                <div class="form-group">
                                    <label>{l s='Date of Birth'}</label>
                                    <div class="row col-12 gap-1">
                                        <div class="col-xs-4">
                                            <select id="days" name="days" class="form-control">
                                                <option value="">-</option>
                                                {foreach from=$days item=day}
                                                    <option value="{$day}" {if ($sl_day == $day)} selected="selected"{/if}>{$day}&nbsp;&nbsp;</option>
                                                {/foreach}
                                            </select>
                                            {*
                                                {l s='January'}
                                                {l s='February'}
                                                {l s='March'}
                                                {l s='April'}
                                                {l s='May'}
                                                {l s='June'}
                                                {l s='July'}
                                                {l s='August'}
                                                {l s='September'}
                                                {l s='October'}
                                                {l s='November'}
                                                {l s='December'}
                                            *}
                                        </div>
                                        <div class="col-xs-4">
                                            <select id="months" name="months" class="form-control">
                                                <option value="">-</option>
                                                {foreach from=$months key=k item=month}
                                                    <option value="{$k}" {if ($sl_month == $k)} selected="selected"{/if}>{l s=$month}&nbsp;</option>
                                                {/foreach}
                                            </select>
                                        </div>
                                        <div class="col-xs-4">
                                            <select id="years" name="years" class="form-control">
                                                <option value="">-</option>
                                                {foreach from=$years item=year}
                                                    <option value="{$year}" {if ($sl_year == $year)} selected="selected"{/if}>{$year}&nbsp;&nbsp;</option>
                                                {/foreach}
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            {/if}
                            {if isset($newsletter) && $newsletter}
                                <div class="custom-control custom-checkbox text-secondary form-group">
                                    <input type="checkbox" class="custom-control-input" name="newsletter" id="newsletter" value="1" {if isset($smarty.post.newsletter) AND $smarty.post.newsletter == 1} checked="checked"{/if} />
                                    <label for="newsletter" class="custom-control-label">{l s='Sign up for our newsletter!'}</label>
                                    {if array_key_exists('newsletter', $field_required)}
                                        <sup> *</sup>
                                    {/if}
                                </div>
                            {/if}
                            {if isset($optin) && $optin}
                                <div class="custom-control custom-checkbox text-secondary form-group">
                                    <input type="checkbox" name="optin" id="optin" class="custom-control-input" value="1" {if isset($smarty.post.optin) AND $smarty.post.optin == 1} checked="checked"{/if} />
                                    <label for="optin" class="custom-control-label">{l s='Receive special offers from our partners!'}</label>
                                    {if array_key_exists('optin', $field_required)}
                                        <sup> *</sup>
                                    {/if}
                                </div>
                            {/if}
                        </div>
                        {if $b2b_enable}
                            <div class="account_creation">
                                <h3>{l s='Your company information'}</h3>
                                <hr>
                                <p class="form-group">
                                    <label for="">{l s='Company'}</label>
                                    <input type="text" class="form-control" id="company" name="company" placeholder="{l s='Enter your Company'}" value="{if isset($smarty.post.company)}{$smarty.post.company}{/if}" />
                                </p>
                                <p class="form-group">
                                    <label for="siret">{l s='SIRET'}</label>
                                    <input type="text" class="form-control" id="siret" name="siret" placeholder="{l s='Enter your SIRET'}" value="{if isset($smarty.post.siret)}{$smarty.post.siret}{/if}" />
                                </p>
                                <p class="form-group">
                                    <label for="ape">{l s='APE'}</label>
                                    <input type="text" class="form-control" id="ape" name="ape" placeholder="{l s='Enter your APE'}" value="{if isset($smarty.post.ape)}{$smarty.post.ape}{/if}" />
                                </p>
                                <p class="form-group">
                                    <label for="website">{l s='Website'}</label>
                                    <input type="text" class="form-control" id="website" name="website" placeholder="{l s='Enter your Website'}" value="{if isset($smarty.post.website)}{$smarty.post.website}{/if}" />
                                </p>
                            </div>
                        {/if}
                        {if isset($PS_REGISTRATION_PROCESS_TYPE) && $PS_REGISTRATION_PROCESS_TYPE}

                            <div class="account_creation">
                                <h3 class="page-subheading">{l s='Your address'}</h3>
                                {foreach from=$dlv_all_fields item=field_name}
                                    {if $field_name eq "company"}
                                        {if !$b2b_enable}
                                            <p class="form-group">
                                                <label for="company">{l s='Company'}{if in_array($field_name, $required_fields)} <sup>*</sup>{/if}</label>
                                                <input type="text" class="form-control" id="company" name="company" placeholder="{l s='Enter your company name'}" value="{if isset($smarty.post.company)}{$smarty.post.company}{/if}" />
                                            </p>
                                        {/if}
                                    {elseif $field_name eq "vat_number"}
                                        <div id="vat_number" style="display:none;">
                                            <p class="form-group">
                                                <label for="vat_number">{l s='VAT number'}{if in_array($field_name, $required_fields)} <sup>*</sup>{/if}</label>
                                                <input type="text" class="form-control" id="vat_number" name="vat_number" placeholder="{l s='Enter your VAT number'}" value="{if isset($smarty.post.vat_number)}{$smarty.post.vat_number}{/if}" />
                                            </p>
                                        </div>
                                    {elseif $field_name eq "firstname"}
                                        <p class="required form-group">
                                            <label for="firstname">{l s='First name'} <sup>*</sup></label>
                                            <input type="text" class="form-control" id="firstname" name="firstname" placeholder="{l s='Enter your first name'}" value="{if isset($smarty.post.firstname)}{$smarty.post.firstname}{/if}" />
                                        </p>
                                    {elseif $field_name eq "lastname"}
                                        <p class="required form-group">
                                            <label for="lastname">{l s='Last name'} <sup>*</sup></label>
                                            <input type="text" class="form-control" id="lastname" name="lastname"  placeholder="{l s='Enter your last name'}"  value="{if isset($smarty.post.lastname)}{$smarty.post.lastname}{/if}" />
                                        </p>
                                    {elseif $field_name eq "address1"}
                                        <p class="required form-group">
                                            <label for="address1">{l s='Address'} <sup>*</sup></label>
                                            <input type="text" class="form-control" name="address1" id="address1" placeholder="{l s='Enter your address'}"  value="{if isset($smarty.post.address1)}{$smarty.post.address1}{/if}" />
                                            <span class="small text-underline text-danger">{l s='Street address, P.O. Box, Company name, etc.'}</span>
                                        </p>
                                    {elseif $field_name eq "address2"}
                                        <p class="form-group is_customer_param">
                                            <label for="address2">{l s='Address (Line 2)'}{if in_array($field_name, $required_fields)} <sup>*</sup>{/if}</label>
                                            <input type="text" class="form-control" name="address2" id="address2" value="{if isset($smarty.post.address2)}{$smarty.post.address2}{/if}" />
                                            <span class="help_block">{l s='Apartment, suite, unit, building, floor, etc...'}</span>
                                        </p>
                                    {elseif $field_name eq "postcode"}
                                        {assign var='postCodeExist' value=true}
                                        <p class="required postcode form-group">
                                            <label for="postcode">{l s='Zip/Postal Code'} <sup>*</sup></label>
                                            <input type="text" class="validate form-control" name="postcode" id="postcode" placeholder="{l s='Enter your postcode'}" data-validate="isPostCode" value="{if isset($smarty.post.postcode)}{$smarty.post.postcode}{/if}"/>
                                        </p>
                                    {elseif $field_name eq "city"}
                                        <p class="required form-group">
                                            <label for="city">{l s='City'} <sup>*</sup></label>
                                            <input type="text" class="form-control" name="city" id="city" placeholder="{l s='Enter your City'}" value="{if isset($smarty.post.city)}{$smarty.post.city}{/if}" />
                                        </p>
                                        <!-- if customer hasn't update his layout address, country has to be verified but it's deprecated -->
                                    {elseif $field_name eq "Country:name" || $field_name eq "country"}
                                        <p class="required select form-group">
                                            <label for="id_country">{l s='Country'} <sup>*</sup></label>
                                            <select name="id_country" id="id_country" class="form-control">
                                                <option value="">-</option>
                                                {foreach from=$countries item=v}
                                                <option value="{$v.id_country}"{if (isset($smarty.post.id_country) AND $smarty.post.id_country == $v.id_country) OR (!isset($smarty.post.id_country) && $sl_country == $v.id_country)} selected="selected"{/if}>{$v.name}</option>
                                                {/foreach}
                                            </select>
                                        </p>
                                    {elseif $field_name eq "State:name" || $field_name eq 'state'}
                                        {assign var='stateExist' value=true}
                                        <p class="required id_state select form-group">
                                            <label for="id_state">{l s='State'} <sup>*</sup></label>
                                            <select name="id_state" id="id_state" class="form-control">
                                                <option value="">-</option>
                                            </select>
                                        </p>
                                    {/if}
                                {/foreach}
                                {if $postCodeExist eq false}
                                    <p class="required postcode form-group unvisible">
                                        <label for="postcode">{l s='Zip/Postal Code'} <sup>*</sup></label>
                                        <input type="text" class="validate form-control" name="postcode" id="postcode" data-validate="isPostCode" value="{if isset($smarty.post.postcode)}{$smarty.post.postcode}{/if}"/>
                                    </p>
                                {/if}
                                {if $stateExist eq false}
                                    <p class="required id_state select unvisible form-group">
                                        <label for="id_state">{l s='State'} <sup>*</sup></label>
                                        <select name="id_state" id="id_state" class="form-control">
                                            <option value="">-</option>
                                        </select>
                                    </p>
                                {/if}
                                <p class="textarea form-group">
                                    <label for="other">{l s='Additional information'}</label>
                                    <textarea class="form-control" name="other" id="other" cols="26" rows="3">{if isset($smarty.post.other)}{$smarty.post.other}{/if}</textarea>
                                </p>
                                {if isset($one_phone_at_least) && $one_phone_at_least}
                                    <p class="small text-danger">{l s='You must register at least one phone number.'}</p>
                                {/if}
                                <p class="form-group">
                                    <label class="" for="phone">{l s='Home phone'}</label>
                                    <input type="text" class="form-control" name="phone" id="phone" placeholder="{l s='Enter your Home phone number'}" value="{if isset($smarty.post.phone)}{$smarty.post.phone}{/if}" />
                                </p>
                                <p class="{if isset($one_phone_at_least) && $one_phone_at_least}required {/if}form-group">
                                    <label for="phone_mobile">{l s='Mobile phone'}{if isset($one_phone_at_least) && $one_phone_at_least} <sup>**</sup>{/if}</label>
                                    <input type="text" class="form-control" name="phone_mobile" id="phone_mobile" placeholder="{l s='Enter your Mobile phone number'}" value="{if isset($smarty.post.phone_mobile)}{$smarty.post.phone_mobile}{/if}" />
                                </p>
                                <p class="required form-group" id="address_alias">
                                    <label for="alias">{l s='Assign an address alias for future reference.'} <sup>*</sup></label>
                                    <input type="text" class="form-control" name="alias" id="alias" value="{if isset($smarty.post.alias)}{$smarty.post.alias}{else}{l s='My address'}{/if}" />
                                </p>
                            </div>
                            <div class="account_creation dni">
                                <h3 class="page-subheading">{l s='Tax identification'}</h3>
                                <p class="required form-group">
                                    <label for="dni">{l s='Identification number'} <sup>*</sup></label>
                                    <input type="text" class="form-control" name="dni" id="dni" value="{if isset($smarty.post.dni)}{$smarty.post.dni}{/if}" />
                                    <span class="form_info">{l s='DNI / NIF / NIE'}</span>
                                </p>
                            </div>
                        {/if}
                        {block name='displayCustomerAccountForm'}
                            {$HOOK_CREATE_ACCOUNT_FORM}
                        {/block}
                        <div class="submit clearfix">
                            <input type="hidden" name="email_create" value="1" />
                            <input type="hidden" name="is_new_customer" value="1" />
                            {if isset($back)}<input type="hidden" class="hidden" name="back" value="{$back|escape:'html':'UTF-8'}" />{/if}
                            <p class="float-right text-danger required"><span><sup>*</sup>{l s='Required field'}</span></p>
                            {block name='authentication_account_submit'}
                                <button type="submit" name="submitAccount" id="submitAccount" class="btn btn-primary col-12">
                                    <span>{l s='Register'}&nbsp;<i class="icon-chevron-right right"></i></span>
                                </button>
                            {/block}

                            {block name='authentication_register_link_block'}
                                <div class="text-center mt-5 mb-1">
                                    {l s='Already have an account?'} <a class="text-primary" href="{$link->getPageLink('authentication')}">{l s='Sign in'}</a>
                                </div>
                            {/block}
                        </div>
                    </div>
                </form>
            {/block}
        </div>
    </div>

{/block}
