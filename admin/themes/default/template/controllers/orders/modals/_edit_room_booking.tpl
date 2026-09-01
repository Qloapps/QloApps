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
    <div id="edit_product">
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active">
                <a href="#edit_room_tab" role="tab" data-toggle="tab">{l s='Rooms'}</a>
            </li>
            <li role="presentation">
                <a href="#room_type_demands_desc" aria-controls="facilities" role="tab" data-toggle="tab">{l s='Facilities'}</a>
            </li>
            <li role="presentation">
                <a href="#room_type_service_product_desc" aria-controls="services" role="tab" data-toggle="tab">{l s='Services'}</a>
            </li>
        </ul>

        <div class="tab-content clearfix">
            {include file='controllers/orders/modals/_edit_room_tab_content.tpl'}

            {* below tpl contains tab contents for facilities and service products *}
            {include file='controllers/orders/modals/_partials/_room_extra_services_content.tpl'}
        </div>
    </div>

    {if isset($loaderImg) && $loaderImg}
        <div class="loading_overlay">
            <img src='{$loaderImg}' class="loading-img"/>
        </div>
    {/if}
</div>