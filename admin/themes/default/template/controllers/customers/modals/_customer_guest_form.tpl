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

<div class="modal-body">
    <div class="text-left errors-wrap"></div>
    <form id="customer-guest-details-form">
        <div class="form-group row">
            <input type="hidden" value="{$customerGuestDetail->id}" name="id_customer_guest_detail">
            <div class="col-sm-2">
                <label class="control-label">{l s='Title'}</label>
                <select name="id_gender">
                    {foreach from=$genders key=k item=gender}
                        <option value="{$gender->id_gender}"{if $customerGuestDetail->id_gender == $gender->id_gender} selected="selected"{/if}>{$gender->name}</option>
                    {/foreach}
                </select>
            </div>
            <div class="col-sm-5">
                <label class="control-label">{l s='First Name'}</label>
                <input type="text" value="{$customerGuestDetail->firstname}" name="firstname">
            </div>
            <div class="col-sm-5">
                <label class="control-label">{l s='Last Name'}</label>
                <input type="text" value="{$customerGuestDetail->lastname}" name="lastname">
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-6">
                <label class="control-label">{l s='Email'}</label>
                <input type="text" value="{$customerGuestDetail->email}" name="email" readonly>
            </div>
            <div class="col-sm-6">
                <label class="control-label">{l s='Phone'}</label>
                <input type="text" value="{$customerGuestDetail->phone}" name="phone">
            </div>
        </div>
    </form>
</div>
