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