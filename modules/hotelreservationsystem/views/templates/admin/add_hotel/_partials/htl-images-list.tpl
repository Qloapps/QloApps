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

<div class="table-responsive">
    <table class="table" id="hotel-image-table">
        <thead>
            <tr>
                <th class="text-center">&nbsp;</th>
                <th class="text-center">{l s='Image Id' mod='hotelreservationsystem'}</th>
                <th class="text-center">{l s='Image' mod='hotelreservationsystem'}</th>
                <th class="text-center">{l s='Category' mod='hotelreservationsystem'}</th>
                <th class="text-center">{l s='Cover' mod='hotelreservationsystem'}</th>
                <th class="text-center">{l s='Action' mod='hotelreservationsystem'}</th>
            </tr>
        </thead>
        <tbody>
            {if isset($hotelImages) && $hotelImages}
                {foreach from=$hotelImages item=image name=hotelImage}
                    {include file="./htl-images-list-row.tpl"}
                {/foreach}
            {/if}
            <tr class="list-empty-tr" {if isset($hotelImages) && $hotelImages}style="display:none;"{/if}>
                <td class="list-empty" colspan="6">
                    <div class="list-empty-msg">
                        <i class="icon-warning-sign list-empty-icon"></i>
                        {l s='No Image Found' mod='hotelreservationsystem'}
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<div class="btn-group bulk-actions dropup">
    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
        {l s='Bulk actions' mod='hotelreservationsystem'} <span class="caret"></span>
    </button>
    <ul class="dropdown-menu">
        <li>
            <a href="#" id="hotel-image-select-all">
                <i class="icon-check-sign"></i>&nbsp;{l s='Select all' mod='hotelreservationsystem'}
            </a>
        </li>
        <li>
            <a href="#" id="hotel-image-unselect-all">
                <i class="icon-check-empty"></i>&nbsp;{l s='Unselect all' mod='hotelreservationsystem'}
            </a>
        </li>
        <li class="divider"></li>
        <li>
            <a href="#" id="hotel-image-bulk-update">
                <i class="icon-edit"></i>&nbsp;{l s='Update selection' mod='hotelreservationsystem'}
            </a>
        </li>
        <li>
            <a href="#" id="hotel-image-bulk-delete">
                <i class="icon-trash"></i>&nbsp;{l s='Delete selected' mod='hotelreservationsystem'}
            </a>
        </li>
    </ul>
</div>

<div class="modal fade" id="addHotelImagesModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><i class="icon-remove-sign"></i></button>
                <h4 class="modal-title"><i class="icon-image"></i> {l s='Add Images' mod='hotelreservationsystem'}</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="control-label col-lg-3">{l s='Images' mod='hotelreservationsystem'}</label>
                    <div class="col-lg-9">
                        <input type="file" id="hotel-images-file-input" class="hide" accept="image/gif, image/jpg, image/jpeg, image/png" multiple>
                        <div class="dummyfile input-group">
                            <span class="input-group-addon"><i class="icon-file"></i></span>
                            <input id="hotel-images-file-name" type="text" readonly>
                            <span class="input-group-btn">
                                <button type="button" id="hotel-images-file-add-btn" class="btn btn-default">
                                    <i class="icon-folder-open"></i> {l s='Add images' mod='hotelreservationsystem'}
                                </button>
                            </span>
                        </div>
                        <p class="help-block">{l s='Allowed image formats: .gif, .jpg, .jpeg and .png' mod='hotelreservationsystem'}</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-3" for="id_htl_image_category">{l s='Category' mod='hotelreservationsystem'}</label>
                    <div class="col-lg-9">
                        <select class="form-control" id="id_htl_image_category" name="id_htl_image_category">
                            <option value="0">{l s='Select category' mod='hotelreservationsystem'}</option>
                            {if isset($hotelImageCategories) && $hotelImageCategories}
                                {foreach from=$hotelImageCategories item=hotelImageCategory}
                                    <option value="{$hotelImageCategory.id_htl_image_category|intval}">{$hotelImageCategory.name|escape:'html':'UTF-8'}</option>
                                {/foreach}
                            {/if}
                        </select>
                        <p class="help-block" style="margin-bottom:2px;">{l s='If not selected, image will be uncategorized.' mod='hotelreservationsystem'} &mdash; <a href="{$link->getAdminLink('AdminHotelImageCategory')|escape:'html':'UTF-8'}&amp;addhtl_image_category" target="_blank" style="white-space:nowrap;">{l s='Create image category' mod='hotelreservationsystem'} <i class="icon-external-link" style="font-size:11px;"></i></a></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="hotel-images-upload-btn"><i class="icon-upload"></i> {l s='Upload' mod='hotelreservationsystem'}</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmDeleteHotelImageModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><i class="icon-remove-sign"></i></button>
                <h4 class="modal-title"><i class="icon-exclamation-triangle"></i> {l s='Confirm Delete' mod='hotelreservationsystem'}</h4>
            </div>
            <div class="modal-body">
                <p>{l s='Are you sure you want to delete the selected image(s)? This cannot be undone.' mod='hotelreservationsystem'}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="confirm-delete-hotel-image-btn">{l s='Delete' mod='hotelreservationsystem'}</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editHotelImageModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><i class="icon-remove-sign"></i></button>
                <h4 class="modal-title"><i class="icon-image"></i> {l s='Edit Image' mod='hotelreservationsystem'}</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit_hotel_image_id">
                <div class="form-group">
                    <label class="control-label col-lg-3" for="edit_id_htl_image_category">{l s='Category' mod='hotelreservationsystem'}</label>
                    <div class="col-lg-9">
                        <select class="form-control" id="edit_id_htl_image_category">
                            <option value="0">{l s='Select category' mod='hotelreservationsystem'}</option>
                            {if isset($hotelImageCategories) && $hotelImageCategories}
                                {foreach from=$hotelImageCategories item=hotelImageCategory}
                                    <option value="{$hotelImageCategory.id_htl_image_category|intval}">{$hotelImageCategory.name|escape:'html':'UTF-8'}</option>
                                {/foreach}
                            {/if}
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-3">{l s='Cover image' mod='hotelreservationsystem'}</label>
                    <div class="col-lg-9">
                        <span class="switch prestashop-switch fixed-width-lg">
                            <input type="radio" id="edit_hotel_image_cover_on" name="edit_hotel_image_cover" value="1">
                            <label for="edit_hotel_image_cover_on">{l s='Yes' mod='hotelreservationsystem'}</label>
                            <input type="radio" id="edit_hotel_image_cover_off" name="edit_hotel_image_cover" value="0">
                            <label for="edit_hotel_image_cover_off">{l s='No' mod='hotelreservationsystem'}</label>
                            <a class="slide-button btn"></a>
                        </span>
                        <p class="help-block">{l s='Setting this to Yes will make this the cover image, replacing the current one.' mod='hotelreservationsystem'}</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="save-edit-hotel-image-btn">
                    <i class="icon-save"></i> {l s='Save' mod='hotelreservationsystem'}
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="bulkUpdateHotelImageModal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><i class="icon-remove-sign"></i></button>
                <h4 class="modal-title"><i class="icon-edit"></i> {l s='Bulk Update selection' mod='hotelreservationsystem'}</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="control-label col-lg-3" for="bulk_id_htl_image_category">{l s='Category' mod='hotelreservationsystem'}</label>
                    <div class="col-lg-9">
                        <select class="form-control" id="bulk_id_htl_image_category">
                            <option value="0">{l s='Select category' mod='hotelreservationsystem'}</option>
                            {if isset($hotelImageCategories) && $hotelImageCategories}
                                {foreach from=$hotelImageCategories item=hotelImageCategory}
                                    <option value="{$hotelImageCategory.id_htl_image_category|intval}">{$hotelImageCategory.name|escape:'html':'UTF-8'}</option>
                                {/foreach}
                            {/if}
                        </select>
                        <p class="help-block">{l s='If not selected, selected images will be uncategorized.' mod='hotelreservationsystem'}</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="save-bulk-hotel-image-btn">
                    <i class="icon-save"></i> {l s='Save' mod='hotelreservationsystem'}
                </button>
            </div>
        </div>
    </div>
</div>
