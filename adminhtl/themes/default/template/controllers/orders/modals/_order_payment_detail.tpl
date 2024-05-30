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
