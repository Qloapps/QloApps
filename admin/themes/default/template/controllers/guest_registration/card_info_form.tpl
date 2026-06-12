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

<div class="panel">
    <div class="panel-heading">
        <i class="icon-list-ul"></i>
        {l s='Guest Registration Card Information'}
    </div>

    <form id="grcCardInfoForm" method="post" action="{$guest_reg_card_form_action|escape:'html':'UTF-8'}" class="form-horizontal">
        <input type="hidden" name="submitGrcCardInfo" value="1" />

        <div class="form-group">
            <label class="control-label col-lg-3">
                <span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="{l s='Select sections and fields to display on the Guest Registration Card PDF.'}">{l s='Select fields'}</span>
            </label>
            <div class="col-xs-7">
                {$guest_reg_card_info_tree}
            </div>
            <div class="col-xs-2">
                <a href="#" class="btn btn-default" data-toggle="modal" data-target="#guestRegPreviewModal">
                    <i class="icon-eye"></i> {l s='Preview'}
                </a>
            </div>
        </div>

        <div class="panel-footer">
            <button type="submit" class="btn btn-default pull-right">
                <i class="process-icon-save"></i> {l s='Save'}
            </button>
        </div>
    </form>
</div>

<div class="modal fade" id="guestRegPreviewModal" tabindex="-1" role="dialog" aria-labelledby="guestRegPreviewModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="guestRegPreviewModalLabel">{$guest_reg_preview_title|escape:'html':'UTF-8'}</h4>
            </div>
            <div class="modal-body text-center">
                <img src="{$guest_reg_card_preview_img_url|escape:'html':'UTF-8'}" alt="{$guest_reg_card_preview_title|escape:'html':'UTF-8'}" class="img-responsive" style="max-width:100%;" />
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
(function ($) {
    $(document).ready(function () {
        // Bubble-up: checking a leaf checks the parent folder.
        // Unchecking a leaf keeps parent checked if any sibling is still checked.
        $('#grc-card-info-tree').on('click', ':input[type=checkbox]', function () {
            var $cb = $(this);
            if (!$cb.closest('.tree-item').length) {
                return;
            }
            var $parentFolderCb = $cb.closest('ul.tree').prev('span.tree-folder-name').find(':input[type=checkbox]:first');
            if (!$parentFolderCb.length) {
                return;
            }
            if ($cb.is(':checked')) {
                $parentFolderCb.prop('checked', true).parent().addClass('tree-selected');
            } else {
                var anySiblingChecked = $cb.closest('ul.tree').find(':input[type=checkbox]:checked').length > 0;
                if (anySiblingChecked) {
                    $parentFolderCb.prop('checked', true).parent().addClass('tree-selected');
                }
            }
        });
    });
}(jQuery));
</script>
