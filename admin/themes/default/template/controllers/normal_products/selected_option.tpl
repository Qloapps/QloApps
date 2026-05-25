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

{if empty($selected_option_css_initialized)}
    {assign var='selected_option_css_initialized' value=true scope='global'}
    <style>
    .selected-option-info {
        cursor: pointer;
    }
    .selected-option-info .icon-info-sign {
        color: #00aff0;
        font-size: 16px;
    }
    .ui-tooltip.selected-option-tooltip {
        background: #ffffff;
        border: 1px solid #ddd;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        color: #333;
        padding: 8px 12px;
        font-size: 13px;
    }
    .ui-tooltip.selected-option-tooltip ul {
        margin: 0;
        padding: 0 0 0 16px;
        list-style: disc;
    }
    .ui-tooltip.selected-option-tooltip ul li {
        padding: 2px 0;
    }
    </style>
{/if}

{capture name='options_list'}
    <ul>
        {foreach $selected_options as $opt}
            <li>{$opt|escape:'html'}</li>
        {/foreach}
    </ul>
{/capture}
<span class="selected-option-info" data-options="{$smarty.capture.options_list|escape:'html'}">
    {$selected_options|@count} Selected <i class="icon-info-sign"></i>
</span>

{if empty($selected_option_js_initialized)}
    {assign var='selected_option_js_initialized' value=true scope='global'}
    <script>
        $(document).ready(function () {
            function initSelectedOptionTooltip() {
                $('.selected-option-info').tooltip({
                    items: '.selected-option-info',
                    content: function () {
                        return $(this).data('options');
                    },
                    tooltipClass: 'selected-option-tooltip',
                    trigger: 'hover',
                    open: function (event, ui) {
                        if (typeof event.originalEvent === 'undefined') {
                            return false;
                        }
                        var $id = $(ui.tooltip).attr('id');
                        if ($('div.ui-tooltip').not('#' + $id).length) {
                            return false;
                        }
                    },
                    close: function (event, ui) {
                        ui.tooltip.hover(
                            function () { $(this).stop(true).fadeTo(400, 1); },
                            function () { $(this).fadeOut(400, function () { $(this).remove(); }); }
                        );
                    }
                });
            }
            initSelectedOptionTooltip();
            $(document).ajaxSuccess(function () {
                initSelectedOptionTooltip();
            });
        });
    </script>
{/if}
