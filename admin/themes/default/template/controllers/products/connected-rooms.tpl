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
<script>
    $(document).ready(function() {
        {literal}
            if (!window._connectedRoomTooltipBound &&
                typeof $.fn.tooltip !== 'undefined' && typeof $.ui !== 'undefined' && typeof $.ui.tooltip !== 'undefined') {
                window._connectedRoomTooltipBound = true;
                $('.connected-room-tooltip-trigger').tooltip({
                    items: '.connected-room-tooltip-trigger',
                    content: function() {
                        var details = $(this).data('connected-details');
                        if (!details || details.length === 0) {
                            return "{/literal}{l s='No rooms connected' js=1}{literal}";
                        }
                        var html = '<div class="tooltip_cont">';
                        html += '<div class="tip_header"><div class="tip_date">{/literal}{l s="Connected Rooms" js=1}{literal}</div></div>';
                        html += '<div class="tip-body connected-tip-body">';
                        var grouped = {};
                        for (var i = 0; i < details.length; i++) {
                            var type = details[i].room_type_name;
                            if (!grouped[type]) { grouped[type] = []; }
                            grouped[type].push(details[i].connected_room_name);
                        }
                        for (var type in grouped) {
                            html += '<div>';
                            html += '<div class="tip_element_head">' + type + '</div>';
                            html += '<div class="tip_element_value">' + grouped[type].join(', ') + '</div>';
                            html += '</div>';
                        }
                        html += '</div></div>';
                        return html;
                    },
                    position: { my: "left top+10", at: "left bottom", collision: "flipfit", within: "#content" }
                });
            }
        {/literal}
    });
</script>
