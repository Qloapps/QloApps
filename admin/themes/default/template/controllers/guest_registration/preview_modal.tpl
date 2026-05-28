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
                <img src="{$guest_reg_preview_img_url|escape:'html':'UTF-8'}" alt="{$guest_reg_preview_title|escape:'html':'UTF-8'}" class="img-responsive" style="max-width:100%;" />
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
(function ($) {
    var confKey = '{$guest_reg_conf_key|escape:'javascript'}';
    var selectedSections = {$guest_reg_selected_sections|json_encode};
    var previewBtnTitle = '{$guest_reg_preview_btn_title|escape:'javascript'}';

    $(document).ready(function () {
        // Inject eye-icon preview button next to the "Optional sections" label
        var $label = $('label[for="' + confKey + '"], .form-group').filter(function () {
            return $(this).find('input[name^="' + confKey + '"]').length > 0;
        }).first().find('label').first();

        if (!$label.length) {
            // Fallback: find the form-group that contains our checkboxes
            $label = $('input[name^="' + confKey + '"]').closest('.form-group').find('label').first();
        }

        if ($label.length && !$label.find('.guest-reg-preview-btn').length) {
            $label.append(
                ' <a href="#" class="guest-reg-preview-btn" title="' + previewBtnTitle + '" data-toggle="modal" data-target="#guestRegPreviewModal" style="margin-left:6px;">' +
                '<i class="icon-eye"></i>' +
                '</a>'
            );
        }

        // Rename checkboxes: QloApps renders them as confKey_0, confKey_1 etc.
        // but the controller expects confKey[] array. Remap so the POST key is array-style.
        $('input[type="checkbox"][name^="' + confKey + '"]').each(function () {
            var sectionId = $(this).val();
            if (sectionId === undefined || sectionId === '') {
                // Extract numeric suffix from name like QLO_GUEST_REG_OPTIONAL_SECTIONS_2
                var match = this.name.match(/_(\d+)$/);
                if (match) {
                    sectionId = match[1];
                }
            }
            $(this).attr('name', confKey + '[]');
            if (sectionId !== undefined && sectionId !== '') {
                $(this).val(sectionId);
            }
        });

        // Restore saved selections
        if (selectedSections && selectedSections.length) {
            $('input[type="checkbox"][name="' + confKey + '[]"]').each(function () {
                $(this).prop('checked', $.inArray(parseInt($(this).val(), 10), selectedSections) !== -1);
            });
        }

        // Prevent modal link from triggering form submit or nav
        $(document).on('click', '.guest-reg-preview-btn', function (e) {
            e.preventDefault();
        });
    });
}(jQuery));
</script>