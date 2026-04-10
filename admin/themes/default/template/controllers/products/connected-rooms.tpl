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
{if isset($htl_connected_rooms) && $htl_connected_rooms|@count > 0}
    <span class="connected-room-tooltip-trigger"
        data-connected-details='{json_encode($htl_connected_rooms)|escape:'html':'UTF-8'}'>
        <i class="icon-random connected-room-icon"></i>
    </span>
{/if}
<style>
    .connected-room-icon {
        color: #008abd;
        cursor: pointer;
    }

    /* page tooltip */
    .tooltip_cont {
        min-width: 280px;
        width: 100%;
        font-size: 14px;
        font-weight: 600;
        /* display: grid; */
    }

    .tooltip_cont .tip_header {
        margin-bottom: 10px;
    }

    .tooltip_cont .tip-body>div {
        margin-bottom: 6px;
    }

    .tip_element_head {
        font-size: 12px;
    }

    .tip_element_value {
        font-size: 16px;
        opacity: 0.6;
    }

    .tip-body {
        display: grid;
        grid-template-columns: auto auto;
    }
</style>
<script>
    $(document).ready(function() {
        {literal}
            if (typeof $.fn.tooltip !== 'undefined' && typeof $.ui !== 'undefined' && typeof $.ui.tooltip !==
                'undefined') {
                $('.connected-room-tooltip-trigger').tooltip({
                    items: '.connected-room-tooltip-trigger',
                    content: function() {
                        var details = $(this).data('connected-details');
                        if (!details || Object.keys(details).length === 0) {
                            return "{/literal}{l s='No rooms connected' js=1}{literal}";
                        }
                        var html = '<div class="tooltip_cont">';
                        html += '<div class="tip_header"><div class="tip_date">{/literal}{l s="Connected Rooms" js=1}{literal}</div></div>';
                        html += '<div class="tip-body">';
                        for (var type in details) {
                            html += '<div>';
                            html += '<div class="tip_element_head">' + type + '</div>';
                            html += '<div class="tip_element_value">';
                            var rooms = details[type];
                            for (var i = 0; i < rooms.length; i++) {
                                html += rooms[i].connected_room_num + (i < rooms.length - 1 ? ', ' :
                                    '');
                            }
                            html += '</div></div>';
                        }
                        html += '</div></div>';
                        return html;
                    },
                    position: {
                        my: "center bottom-10",
                        at: "center top",
                        collision: "flipfit"
                    }
                });
            }
        {/literal}
    });
</script>