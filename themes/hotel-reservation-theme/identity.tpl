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

{block name='identity'}
    {capture name=path}
        <li class="breadcrumb-item"><a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">{l s='My account'}</a></li>
        <li class="breadcrumb-item"><span class="navigation_page">{l s='Personal information'}</span></li>
    {/capture}

    <div class="container-md">
        <div class="row justify-content-center m-md-4">
            <div class="qlo-account-card card col-12 col-md-9 p-5 mb-md-4">
                {block name='identity_heading'}
                    <h1 class="page-heading text-center text-bolder h5 font-weight-bold">{l s='Your Personal Information'}</h1>
                {/block}
                <hr>

                {block name='errors'}
                    {include file="$tpl_dir./errors.tpl"}
                {/block}

                {if isset($confirmation) && $confirmation}
                    <p class="alert alert-success">
                        {l s='Your personal information has been successfully updated.'}
                        {if isset($pwd_changed)}<br>{l s='Your password has been sent to your email:'} {$email}{/if}
                    </p>
                {else}
                    <p class="text-center small">{l s='Please be sure to update your personal information if it has changed.'}</p>
                    <p class="float-left text-danger required"><sup>*</sup>{l s='Required fields'}</p>

                    {block name='identity_form'}
                        <form action="{$link->getPageLink('identity', true)|escape:'html':'UTF-8'}" method="post" class="std qlo-identity-form">
                            <fieldset class="mt-4">
                                <div class="form-group">
                                    <label class="required d-block mb-2">{l s='Social title'}</label>
                                    {foreach from=$genders key=k item=gender}
                                        <div class="form-check form-check-inline mr-4 mb-2">
                                            <input class="form-check-input" type="radio" name="id_gender" id="id_gender{$gender->id}" value="{$gender->id|intval}" {if isset($smarty.post.id_gender) && $smarty.post.id_gender == $gender->id}checked="checked"{/if}>
                                            <label class="form-check-label" for="id_gender{$gender->id}">{$gender->name}</label>
                                        </div>
                                    {/foreach}
                                </div>

                                <div class="required form-group">
                                    <label for="firstname" class="required">{l s='First Name'}</label>
                                    <input class="is_required validate form-control" data-validate="isName" type="text" id="firstname" name="firstname" value="{$smarty.post.firstname}">
                                </div>

                                <div class="required form-group">
                                    <label for="lastname" class="required">{l s='Last Name'}</label>
                                    <input class="is_required validate form-control" data-validate="isName" type="text" name="lastname" id="lastname" value="{$smarty.post.lastname}">
                                </div>

                                <div class="required form-group">
                                    <label for="email" class="required">{l s='Email'}</label>
                                    <input class="is_required validate form-control" data-validate="isEmail" type="email" name="email" id="email" value="{$smarty.post.email}">
                                </div>

                                <div class="form-group">
                                    <label for="phone" {if isset($one_phone_at_least) && $one_phone_at_least}class="required"{/if}>{l s='Phone'}</label>
                                    <input class="{if isset($one_phone_at_least) && $one_phone_at_least}is_required {/if}validate form-control" data-validate="isPhoneNumber" type="tel" name="phone" id="phone" value="{$smarty.post.phone}">
                                </div>

                                {if isset($birthday) && $birthday}
                                    <div class="form-group">
                                        <label>{l s='Date of Birth'}</label>
                                        <div class="row">
                                            <div class="col-4">
                                                <select name="days" id="days" class="form-control">
                                                    <option value="">-</option>
                                                    {foreach from=$days item=v}
                                                        <option value="{$v}" {if ($sl_day == $v)}selected="selected"{/if}>{$v}&nbsp;&nbsp;</option>
                                                    {/foreach}
                                                </select>
                                            </div>
                                            <div class="col-4">
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
                                                <select id="months" name="months" class="form-control">
                                                    <option value="">-</option>
                                                    {foreach from=$months key=k item=v}
                                                        <option value="{$k}" {if ($sl_month == $k)}selected="selected"{/if}>{l s=$v}&nbsp;</option>
                                                    {/foreach}
                                                </select>
                                            </div>
                                            <div class="col-4">
                                                <select id="years" name="years" class="form-control">
                                                    <option value="">-</option>
                                                    {foreach from=$years item=v}
                                                        <option value="{$v}" {if ($sl_year == $v)}selected="selected"{/if}>{$v}&nbsp;&nbsp;</option>
                                                    {/foreach}
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                {/if}

                                <div class="required form-group">
                                    <label for="old_passwd" class="required">{l s='Current Password'}</label>
                                    <input class="is_required validate form-control" type="password" data-validate="isPasswd" name="old_passwd" id="old_passwd">
                                </div>

                                <div class="password form-group">
                                    <label for="passwd">{l s='New Password'}</label>
                                    <input class="validate form-control" type="password" data-validate="isPasswd" name="passwd" id="passwd">
                                </div>

                                <div class="password form-group">
                                    <label for="confirmation">{l s='Confirm Password'}</label>
                                    <input class="validate form-control" type="password" data-validate="isPasswd" name="confirmation" id="confirmation">
                                </div>

                                {if isset($newsletter) && $newsletter}
                                    <div class="form-group form-check">
                                        <input class="form-check-input" type="checkbox" id="newsletter" name="newsletter" value="1" {if isset($smarty.post.newsletter) && $smarty.post.newsletter == 1} checked="checked"{/if}>
                                        <label class="form-check-label" for="newsletter">
                                            {l s='Sign up for our newsletter!'}
                                            {if isset($required_fields) && array_key_exists('newsletter', $field_required)}<sup> *</sup>{/if}
                                        </label>
                                    </div>
                                {/if}

                                {if isset($optin) && $optin}
                                    <div class="form-group form-check">
                                        <input class="form-check-input" type="checkbox" name="optin" id="optin" value="1" {if isset($smarty.post.optin) && $smarty.post.optin == 1} checked="checked"{/if}>
                                        <label class="form-check-label" for="optin">
                                            {l s='Receive special offers from our partners!'}
                                            {if isset($required_fields) && array_key_exists('optin', $field_required)}<sup> *</sup>{/if}
                                        </label>
                                    </div>
                                {/if}

                                {if $b2b_enable}
                                    <h2 class="h6 font-weight-bold mt-4 mb-3">{l s='Your company information'}</h2>
                                    <div class="form-group">
                                        <label for="company">{l s='Company'}</label>
                                        <input type="text" class="form-control" id="company" name="company" value="{if isset($smarty.post.company)}{$smarty.post.company}{/if}">
                                    </div>
                                    <div class="form-group">
                                        <label for="siret">{l s='SIRET'}</label>
                                        <input type="text" class="form-control" id="siret" name="siret" value="{if isset($smarty.post.siret)}{$smarty.post.siret}{/if}">
                                    </div>
                                    <div class="form-group">
                                        <label for="ape">{l s='APE'}</label>
                                        <input type="text" class="form-control" id="ape" name="ape" value="{if isset($smarty.post.ape)}{$smarty.post.ape}{/if}">
                                    </div>
                                    <div class="form-group">
                                        <label for="website">{l s='Website'}</label>
                                        <input type="text" class="form-control" id="website" name="website" value="{if isset($smarty.post.website)}{$smarty.post.website}{/if}">
                                    </div>
                                {/if}

                                {block name='displayCustomerIdentityForm'}
                                    {if isset($HOOK_CUSTOMER_IDENTITY_FORM)}
                                        {$HOOK_CUSTOMER_IDENTITY_FORM}
                                    {/if}
                                {/block}

                                <div class="form-group mb-0 pt-2">
                                    <button type="submit" name="submitIdentity" class="btn btn-primary btn-medium">
                                        <span>{l s='Update'}</span>
                                    </button>
                                </div>
                            </fieldset>
                        </form>
                    {/block}
                {/if}
            </div>
        </div>

        {block name='identity_footer_links'}
            <ul class="footer_links clearfix">
                <li>
                    <a class="link text-secondary" href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">
                        <span><i class="icon-angles-left"></i> {l s='Back to My account'}</span>
                    </a>
                </li>
            </ul>
        {/block}
    </div>
{/block}
