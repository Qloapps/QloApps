{**
* 2010-2022 Webkul.
*
* NOTICE OF LICENSE
*
* All right is reserved,
* Please go through LICENSE.txt file inside our module
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to newer
* versions in the future. If you wish to customize this module for your
* needs please refer to CustomizationPolicy.txt file inside our module for more information.
*
* @author Webkul IN
* @copyright 2010-2022 Webkul IN
* @license LICENSE.txt
*}

{extends file='helpers/view/view.tpl'}

{block name='override_tpl'}
    <form action="{$link->getAdminLink('AdminHotelReviewHotelReview')|escape:'html':'UTF-8'}" method="post" class="form-horizontal" id="" enctype="multipart/form-data">
        <div class="panel">
            <div class="panel-heading">
                <i class="icon-info-circle"></i>
                {l s='Review Information' mod='qlohotelreview'}
            </div>
            <div class="form-wrapper">
                {if isset($id_hotel_review) && $id_hotel_review}
                    <input type="hidden" name="id_hotel_review" value="{$id_hotel_review|escape:"html":"UTF-8"}">
                {/if}

                {include file='./_partials/review.tpl'}

                <div class="form-group">
                    <label for="disapproval_message" class="control-label col-lg-3">
                        <span class="label-tooltip" data-toggle="tooltip" data-html="true"
                            title="{l s='Post a reply to this review.' mod='qlohotelreview'}">
                            {l s='Management reply' mod='qlohotelreview'}
                        </span>
                    </label>
                    <div class="col-lg-5">
                        <textarea
                        class="textarea-autoresize"
                        id="management_reply"
                        name="management_reply"
                        rows="4">{if is_array($reply)}{$reply.message}{/if}</textarea>
                    </div>
                </div>

                <div class="panel-footer">
                    <button type="submit" name="submitDelete" class="btn btn-default pull-right submit-delete">
                        <i class="process-icon- icon-trash"></i> {l s='Delete' mod='qlohotelreview'}
                    </button>
                    {if !$currentObject->approved}
                        <button type="submit" name="submitApprove" class="btn btn-default pull-right">
                            <i class="process-icon- icon-check"></i> {l s='Approve' mod='qlohotelreview'}
                        </button>
                    {else}
                        <button type="submit" name="submitUnapprove" class="btn btn-default pull-right">
                            <i class="process-icon- icon-times"></i> {l s='Unapprove' mod='qlohotelreview'}
                        </button>
                    {/if}
                    <button type="submit" name="submitReply" class="btn btn-default pull-right">
                        <i class="process-icon- icon-reply"></i> {l s='Reply' mod='qlohotelreview'}
                    </button>
                    <a href="javascript:void(0);" class="btn btn-default" onclick="window.history.back();">
                        <i class="process-icon-back"></i>
                        {l s='Back to list' mod='qlohotelreview'}
                    </a>
                </div>
            </div>
        </div>
    </form>
    {strip}
        {addJsDefL name=qlo_js_text_confirm}{l s='Are you sure?' mod='qlohotelreview' js=1}{/addJsDefL}
    {/strip}
{/block}
