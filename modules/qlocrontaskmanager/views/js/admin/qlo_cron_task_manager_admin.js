/**
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
*/

var QctmAdmin = {
    selectors: {
        copyButton: '#qctm-copy-btn',
        cronCommandInput: '#qctm-cron-command',
        regenerateButton: 'button[name="regenerateToken"], input[name="regenerateToken"]',
        textContainer: '#qctm-admin-js-text',
    },

    init: function () {
        this.bindEvents();
    },

    bindEvents: function () {
        var self = this;

        $(document).on('click', self.selectors.copyButton, function (e) {
            e.preventDefault();
            self.copyCronCommand(this);
        });

        $(document).on('click', self.selectors.regenerateButton, function (e) {
            if (!confirm(self.getRegenerateConfirmText())) {
                e.preventDefault();
                return false;
            }
        });
    },

    copyCronCommand: function (button) {
        var input = $(this.selectors.cronCommandInput).get(0);

        if (!input) {
            return;
        }

        if (this.canUseClipboardApi()) {
            this.copyWithClipboardApi(input, button);
        } else {
            this.copyWithFallback(input, button);
        }
    },

    canUseClipboardApi: function () {
        return typeof navigator !== 'undefined'
            && navigator.clipboard
            && typeof navigator.clipboard.writeText === 'function'
            && window.isSecureContext;
    },

    copyWithClipboardApi: function (input, button) {
        var self = this;

        self.selectInputValue(input);
        navigator.clipboard.writeText(input.value).then(function () {
            self.showCopiedState(button);
        }).catch(function () {
            self.copyWithFallback(input, button);
        });
    },

    copyWithFallback: function (input, button) {
        this.selectInputValue(input);

        if (document.execCommand('copy')) {
            this.showCopiedState(button);
        }
    },

    selectInputValue: function (input) {
        input.focus();
        input.select();
        input.setSelectionRange(0, input.value.length);
    },

    showCopiedState: function (button) {
        var $button = $(button);
        var originalHtml = $button.html();
        var copiedText = $button.data('copied-text') || 'Copied!';

        $button.html('<i class="icon-check"></i> ' + copiedText);

        setTimeout(function () {
            $button.html(originalHtml);
        }, 2000);
    },

    getRegenerateConfirmText: function () {
        return $(this.selectors.textContainer).data('regenerate-confirm') || '';
    },
};

var QctmCronHelper = {
    $input: null,
    cronRegex: /^(\*|[\d\/*,\-]+)\s+(\*|[\d\/*,\-]+)\s+(\*|[\d\/*,\-]+)\s+(\*|[\d\/*,\-]+)\s+(\*|[\d\/*,\-]+)$/,

    init: function () {
        this.$input = $('.qctm-cron-expression');

        if (!this.$input.length) {
            return;
        }
        this.$input.on('input keyup', this.onInput.bind(this));
    },

    onInput: function (e) {
        var $input = $(e.currentTarget);
        var val = $input.val();
        val = val.replace(/[^0-9*\/,\-\s]/g, '');
        $input.val(val);
        var $readable = $input.closest('.col-lg-4').find('.help-block');
        if (this.cronRegex.test(val)) {
            this.fetchReadable(val, $readable);
        } else {
            $readable.removeClass('cron-expression-valid').addClass('cron-expression-invalid').text(INVALID_CRON_EXPRESSION);
        }
    },

    fetchReadable: function (val, $readable) {
        var ajaxUrl = $('#qctm-ajax-url').val();

        if (!ajaxUrl || !val) {
            return;
        }

        $.ajax({
            url: ajaxUrl,
            type: 'GET',
            data: {
                action: 'getCronReadable',
                cron_expression: val
            },
            dataType: 'json',
            success: function (response) {
                if (response && response.valid) {
                    $readable.removeClass('cron-expression-invalid').addClass('cron-expression-valid').text(response.readable );
                } else {
                    $readable.removeClass('cron-expression-valid').addClass('cron-expression-invalid').text(response.readable);
                }
            }
        });
    }
};

$(document).ready(function () {
    QctmAdmin.init();
    QctmCronHelper.init();
});
