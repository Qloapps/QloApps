{*
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
    <div class="form-group row">
        <div class="col-sm-6">
            <label class="control-label">{l s='Payment Date'}</label>
            <b><div id="payment_date"></div></b>
        </div>
        <div class="col-sm-6">
            <label class="control-label">{l s='Payment Method'}</label>
            <b><div id="payment_method"></div></b>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-sm-6">
            <label class="control-label">{l s='Payment Source'}</label>
            <b><div id="payment_source"></div></b>
        </div>
        <div class="col-sm-6">
            <label class="control-label">{l s='Transaction Id'}</label>
            <b><div id="transaction_id"></div></b>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-sm-6">
            <label class="control-label">{l s='Amount'}</label>
            <b><div id="amount"></div></b>
        </div>
        <div class="col-sm-6">
            <label class="control-label">{l s='Invoice'}</label>
            <b><div id="invoice_number"></div></b>
        </div>
    </div>
    {* <div class="form-group row">
        <div class="col-sm-6">
            <label class="control-label">{l s='Card Number'}</label>
            <b><div id="card_number"></div></b>
        </div>
        <div class="col-sm-6">
            <label class="control-label">{l s='Card Brand'}</label>
            <b><div id="card_brand"></div></b>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-sm-6">
            <label class="control-label">{l s='Card Expiration'}</label>
            <b><div id="card_expiration"></div></b>
        </div>
        <div class="col-sm-6">
            <label class="control-label">{l s='Card Holder'}</label>
            <b><div id="card_holder"></div></b>
        </div>
    </div> *}

    {if isset($loaderImg) && $loaderImg}
        <div class="loading_overlay">
            <img src='{$loaderImg}' class="loading-img"/>
        </div>
    {/if}
</div>
