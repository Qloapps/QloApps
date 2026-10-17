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
<div class="alert alert-warning" id="ghc-hotel-list-warning">
    <button type="button" class="close" aria-label="{l s='Close' mod='qlogooglehotelconnector'}">&times;</button>
    {if !$is_connected}
        <strong>{l s='Connect to Google Hotel to get more commission-free direct bookings.' mod='qlogooglehotelconnector'}</strong>
        &nbsp;<a href="{$config_url|escape:'html':'UTF-8'}" class="alert-link">{l s='Connect in Google Hotel Setup' mod='qlogooglehotelconnector'}</a>
    {/if}

    {if !empty($hotels_with_issues)}
        <div class="qlo-ghc-warning-title">{l s='The following hotels are not listed on Google Hotel:' mod='qlogooglehotelconnector'}</div>
        <ul class="qlo-ghc-missing-list">
            {foreach from=$hotels_with_issues item=hotel}
            <li><a href="{$hotel.edit_url|escape:'html':'UTF-8'}" class="alert-link">{$hotel.hotel_name|escape:'html':'UTF-8'}</a></li>
            {/foreach}
        </ul>
    {/if}
</div>
<script type="text/javascript">
    {literal}
    (function () {
        var box = document.getElementById('ghc-hotel-list-warning');
        if (!box) { return; }
        var name = 'qghc_dismiss_hotel_list';
        // Dismissed earlier? Hide it right away. Clearing cookies brings the warning back.
        if (document.cookie.indexOf(name + '=1') !== -1) {
            box.parentNode.removeChild(box);
            return;
        }
        var btn = box.querySelector('.close');
        if (btn) {
            btn.addEventListener('click', function () {
                document.cookie = name + '=1; path=/';
                box.parentNode.removeChild(box);
            });
        }
    })();
    {/literal}
</script>
