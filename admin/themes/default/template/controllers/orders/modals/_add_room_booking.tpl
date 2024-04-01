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
    <div id="new_product">
        <input type="hidden" id="add_product_product_id" name="add_product[product_id]" value="0" />
        <div class="form-group">
            <label class="control-label">{l s='Room Type:'}</label>
            <div class="input-group">
                <input type="text" id="add_product_product_name" class="form-control" value="" placeholder="{l s='Enter the name of the room type'}" />
                <div class="input-group-addon">
                    <i class="icon-search"></i>
                </div>
            </div>
        </div>
        <div class="add_room_fields" style="display:none;">
            <div class="row form-group">
                <div class="col-sm-6 room_check_in_div">
                    <label class="control-label">{l s='Check-In'}</label>
                    <div class="input-group">
                        <input type="text" class="form-control add_room_date_from" name="add_product[date_from]" readonly />
                        <div class="input-group-addon"><i class="icon-calendar"></i></div>
                    </div>
                </div>
                <div class="col-sm-6 room_check_out_div">
                    <label class="control-label">{l s='Check-Out'}</label>
                    <div class="input-group">
                        <input type="text" class="form-control add_room_date_to" name="add_product[date_to]" readonly/>
                        <div class="input-group-addon"><i class="icon-calendar"></i></div>
                    </div>
                </div>
            </div>

            <div class="row form-group">
                <div class="col-sm-6">
                    <label class="control-label">{l s='Price (Tax excl.)'}</label>
                    <div class="input-group">
                        {if $currency->format % 2}<div class="input-group-addon">{$currency->sign}</div>{/if}
                        <input class="form-control" type="text" name="add_product[product_price_tax_excl]" id="add_product_product_price_tax_excl" value=""  disabled="disabled"/>
                        {if !($currency->format % 2)}<div class="input-group-addon">{$currency->sign}</div>{/if}
                    </div>
                </div>
                <div class="col-sm-6">
                    <label class="control-label">{l s='Price (Tax incl.)'}</label>
                    <div class="input-group">
                        {if $currency->format % 2}<div class="input-group-addon">{$currency->sign}</div>{/if}
                        <input class="form-control" type="text" name="add_product[product_price_tax_incl]" id="add_product_product_price_tax_incl" value=""  disabled="disabled"/>
                        {if !($currency->format % 2)}<div class="input-group-addon">{$currency->sign}</div>{/if}
                    </div>
                </div>
            </div>

            <div class="productQuantity">
                {if $order->with_occupancy}
                    <div class="booking_occupancy form-group row">
                        <div class="col-sm-6">
                            <label class="control-label">{l s='Occupancy'}</label>
                            <div class="dropdown">
                                <button class="booking_guest_occupancy btn btn-default btn-left btn-block input-occupancy disabled form-control" type="button">
                                    <span>{l s='Select occupancy'}</span>
                                </button>
                                <input type="hidden" class="max_avail_type_qty" value="">
                                <div class="dropdown-menu booking_occupancy_wrapper well well-sm">
                                    <div class="booking_occupancy_inner">
                                        <input type="hidden" class="max_adults" value="">
                                        <input type="hidden" class="max_children" value="">
                                        <input type="hidden" class="max_guests" value="">
                                        <div class="occupancy_info_block col-sm-12" occ_block_index="0">
                                            <div class="occupancy_info_head col-sm-12"><label class="room_num_wrapper">{l s='Room - 1'}</label></div>
                                            <div class="col-sm-12">
                                                <div class="row">
                                                    <div class="form-group col-xs-6 occupancy_count_block">
                                                        <label>{l s='Adults'}</label>
                                                        <input type="number" class="form-control num_occupancy num_adults" name="occupancy[0][adults]" value="1" min="1">
                                                    </div>
                                                    <div class="form-group col-xs-6 occupancy_count_block">
                                                        <label>{l s='Child'} <span class="label-desc-txt"></span></label>
                                                        <input type="number" class="form-control num_occupancy num_children" name="occupancy[0][children]" value="0" min="0">
                                                        ({l s='Below'}  {$max_child_age|escape:'htmlall':'UTF-8'} {l s='years'})
                                                    </div>
                                                </div>
                                                <div class="row children_age_info_block" style="display:none">
                                                    <div class="form-group col-sm-12">
                                                        <label class="">{l s='All Children'}</label>
                                                        <div class="">
                                                            <div class="row children_ages">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr class="occupancy-info-separator col-sm-12">
                                        </div>
                                    </div>
                                    <div class="add_occupancy_block">
                                        <a class="add_new_occupancy_btn" href="#"><i class="icon-plus"></i> <span>{l s='Add Room'}</span></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                {else}
                    <div class="form-group row">
                        <div class="col-sm-6">
                            <label class="control-label">{l s='No. of rooms'}</label>
                            <input type="number" class="form-control" name="add_product[product_quantity]" id="add_product_product_quantity" value="1" disabled="disabled" min="1"/>
                        </div>
                    </div>
                {/if}
            </div>

            {if sizeof($invoices_collection)}
                <div class="form-group">
                    <label class="control-label">{l s='Invoice'}</label>
                    <select class="form-control" name="add_product[invoice]" id="add_product_product_invoice" disabled="disabled">
                        <optgroup class="existing" label="{l s='Existing'}">
                            {foreach from=$invoices_collection item=invoice}
                            <option value="{$invoice->id}">{$invoice->getInvoiceNumberFormatted($current_id_lang)}</option>
                            {/foreach}
                        </optgroup>
                        <optgroup label="{l s='New'}">
                            <option value="0">{l s='Create a new invoice'}</option>
                        </optgroup>
                    </select>
                </div>
            {/if}
        </div>
        <button type="button" class="btn btn-default" id="submitAddProduct" disabled="disabled" style="display:none;"></button>
    </div>
</div>
