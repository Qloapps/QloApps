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
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active">
            <a  href="#reallocate_room_tab" aria-controls="reallocate" role="tab" data-toggle="tab">{l s='Room Reallocation'}</a>
        </li>
        <li role="presentation">
            <a  href="#swap_room_tab" aria-controls="swap" role="tab" data-toggle="tab">{l s='Swap Room'}</a>
        </li>
    </ul>
    <div class="tab-content order-panel active">
        <div role="tabpanel" class="tab-pane active" id="reallocate_room_tab">
            <div class="row">
                <dl class="list-detail col-sm-6">
                    <label class="label-title">{l s='Currently Alloted Customer'}</label>
                    <dd><i class="icon-user"></i> &nbsp;<span class="cust_name"></span></dd>
                    <dd><i class="icon-envelope"></i> &nbsp;<span class="cust_email"></span></dd>
                </dl>
                <dl class="list-detail col-sm-6">
                    <label class="label-title">{l s='Current Room Number'}</label>
                    <dd><i class="icon-bed"></i> &nbsp;<span class="modal_curr_room_num"></span></dd>
                </dl>
            </div>

            <hr>
            <form class="form-hozizontal" method="post" action="{$current_index}&amp;vieworder&amp;token={$smarty.get.token|escape:'html':'UTF-8'}&amp;id_order={$order->id|intval}">
                <div class="form-group">
                    <div class="row">
                        <div class="form-group col-sm-6">
                            <label for="realloc_avail_room_type" class="control-label model-label">{l s='Room Type To Reallocate:'}</label>
                            <input type="hidden" class="form-control modal_id_htl_booking" name="id_htl_booking">
                            <div class="realloc_avail_room_type_container">
                                <select class="form-control" name="realloc_avail_room_type" id="realloc_avail_room_type">
                                    <option value="0" selected="selected">{l s='Select Room Type'}</option>
                                </select>
                            </div>
                            <p class="error_text" id="realloc_sel_rm_type_err_p"></p>
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="realloc_avail_rooms" class="control-label model-label">{l s='Room To Reallocate:'}</label>
                            <div class="realloc_avail_rooms_container">
                                <select class="form-control" name="realloc_avail_rooms" id="realloc_avail_rooms">
                                    <option value="0" selected="selected">{l s='Select Rooms'}</option>
                                </select>
                            </div>
                            <p class="error_text" id="realloc_sel_rm_err_p"></p>
                        </div>
                    </div>
                    <div class="row" id="reallocation_price_diff_block" style="display:none;">
                        <div class="form-group col-sm-6">
                            <label for="reallocation_price_diff" class="control-label model-label">{l s='Price Difference'} *</label>
                            <div class="input-group">
                                <span class="input-group-addon">{$currency->prefix}{$currency->suffix}</span>
                                <input class="alert-warning" type="text" name="reallocation_price_diff" id="reallocation_price_diff" value="0" />
                                <span class="input-group-addon">{l s='Tax excl.'}</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="alert alert-warning col-sm-12 realloc_roomtype_change_message" style="display:none">
                            <p>{l s="If room type is changed while room reallocation then all additional facilities and services will be assigned to the selected room of new room type."}</p>
                            <p>{l s="If you want to change additional facilities or services, you can update by editing the room after reallocation."}</p>
                        </div>
                    </div>
                    <p class="error_text" id="realloc_sel_rm_err_p"></p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-close" data-dismiss="modal">{l s="Close"}</button>
                    <button type="submit" id="realloc_allocated_rooms" name="realloc_allocated_rooms" class="btn btn-primary" value="Reallocate"><i class="icon icon-refresh"></i> &nbsp;{l s="Reallocate"}</button>
                </div>
            </form>
        </div>
        <div role="tabpanel" class="tab-pane" id="swap_room_tab">
            <div class="row">
                <dl class="list-detail col-sm-6">
                    <label class="label-title">{l s='Currently Alloted Customer'}</label>
                    <dd><i class="icon-user"></i> &nbsp;<span class="cust_name"></span></dd>
                    <dd><i class="icon-envelope"></i> &nbsp;<span class="cust_email"></span></dd>
                </dl>
                <dl class="list-detail col-sm-6">
                    <label class="label-title">{l s='Current Room Number'}</label>
                    <dd><i class="icon-bed"></i> &nbsp;<span class="modal_curr_room_num"></span></dd>
                </dl>
            </div>
            <hr>
            <form method="post" action="{$current_index}&amp;vieworder&amp;token={$smarty.get.token|escape:'html':'UTF-8'}&amp;id_order={$order->id|intval}">
                <div class="form-group">
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-sm-6">
                            <label for="swap_avail_rooms" class="control-label model-label">{l s='Available rooms to swap'}</label>
                            <input type="hidden" class="form-control modal_id_htl_booking" name="id_htl_booking">
                            <div class="swap_avail_rooms_container"></div>

                            <p class="error_text" id="swap_sel_rm_err_p"></p>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-close" data-dismiss="modal">{l s="Close"}</button>
                    <button type="submit" id="swap_allocated_rooms" name="swap_allocated_rooms" class="btn btn-primary" value="Swap"><i class="icon icon-refresh"></i> &nbsp;{l s="Swap"}</button>
                </div>
            </form>
        </div>

        {* loader before loading data *}
        <div class="loading_overlay">
            <img src='{$loaderImg}' class="loading-img"/>
        </div>
    </div>
</div>
