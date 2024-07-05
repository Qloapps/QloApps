{*
* Since 2010 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through this link for complete license : https://store.webkul.com/license.html
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to https://store.webkul.com/customisation-guidelines/ for more information.
*
*  @author    Webkul IN <support@webkul.com>
*  @copyright Since 2010 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

<div class="modal-body">
    <div class="text-left errors-wrap"></div>
    <form id="customer-guest-details-form">
        <div class="form-group row">
            <div class="col-sm-2">
                <label class="control-label">{l s='Date'}</label>
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
                <input type="text" value="{$customerGuestDetail->email}" name="email">
            </div>
            <div class="col-sm-6">
                <label class="control-label">{l s='Phone'}</label>
                <input type="text" value="{$customerGuestDetail->phone}" name="phone">
            </div>
        </div>
    </form>

    {if isset($loaderImg) && $loaderImg}
        <div class="loading_overlay">
            <img src='{$loaderImg}' class="loading-img"/>
        </div>
    {/if}
</div>
