{*
* 2010-2023 Webkul.
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
*  @copyright 2010-2023 Webkul IN
*  @license   https://store.webkul.com/license.html
*}

<div class="modal-body">
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active">
            <a class="order_detail_link" href="#reallocate_room_tab" aria-controls="reallocate" role="tab" data-toggle="tab">{l s='Room Reallocation'}</a>
        </li>
        <li role="presentation">
            <a class="order_detail_link" href="#swap_room_tab" aria-controls="swap" role="tab" data-toggle="tab">{l s='Swap Room'}</a>
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
                        <div class="col-sm-6">
                            <label for="realloc_avail_rooms" class="control-label">{l s='Available rooms to reallocate'}</label>
                            <div class="realloc_avail_rooms_container">
                                <select class="form-control" name="realloc_avail_rooms" id="realloc_avail_rooms">
                                    <option value="0" selected="selected">{l s='Select Rooms'}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" class="form-control modal_curr_room_num" name="modal_curr_room_num">
                    <input type="hidden" class="form-control modal_date_from" name="modal_date_from">
                    <input type="hidden" class="form-control modal_date_to" name="modal_date_to">
                    <input type="hidden" class="form-control modal_id_room" name="modal_id_room">
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
                            <div class="swap_avail_rooms_container"></div>
                            <p class="error_text" id="swap_sel_rm_err_p"></p>
                        </div>
                    </div>
                </div>

                <input type="hidden" class="form-control modal_curr_room_num" name="modal_curr_room_num" readonly="true">
                <input type="hidden" class="form-control modal_date_from" name="modal_date_from">
                <input type="hidden" class="form-control modal_date_to" name="modal_date_to">
                <input type="hidden" class="form-control modal_id_room" name="modal_id_room">
                <input type="hidden" class="form-control modal_id_order" name="modal_id_order">

                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-close" data-dismiss="modal">{l s="Close"}</button>
                    <button type="submit" id="swap_allocated_rooms" name="swap_allocated_rooms" class="btn btn-primary" value="Swap"><i class="icon icon-refresh"></i> &nbsp;{l s="Swap"}</button>
                </div>
            </form>
        </div>
    </div>
</div>
