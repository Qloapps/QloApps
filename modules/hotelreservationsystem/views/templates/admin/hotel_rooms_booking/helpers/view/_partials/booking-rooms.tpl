<div class="col-sm-12" id="htl_rooms_list">
    <div class="panel">
        <ul class="nav nav-tabs">
            {foreach from=$booking_data['rm_data'] key=book_k item=book_v}
                <li {if $book_v@first}class="active"{/if} ><a href="#room_type_{$book_k}" data-toggle="tab">{$book_v['name']}</a></li>
            {/foreach}
        </ul>
        <div class="tab-content panel">
            {foreach from=$booking_data['rm_data'] key=book_k item=book_v}
                <div id="room_type_{$book_k}" class="tab-pane {if $book_v@first}active{/if}">
                    {* room type occupancy Details *}
                    <div>
                        <div class="form-group">
                            <b>{l s='Room Occupancy' mod='hotelreservationsystem'}:</b>&nbsp;&nbsp;&nbsp;
                            <span>{l s='Maximum adults' mod='hotelreservationsystem'} : {$book_v['room_type_info']['max_adults']}</span>&nbsp;&nbsp;&nbsp;<span>{l s='Maximum children' mod='hotelreservationsystem'} : {$book_v['room_type_info']['max_children']}</span>&nbsp;&nbsp;&nbsp;<span>{l s='Maximum guests' mod='hotelreservationsystem'} : {$book_v['room_type_info']['max_guests']}</span>
                        </div>
                    </div>
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#avail_room_data_{$book_k|escape:'htmlall':'UTF-8'}" data-toggle="tab">{l s='Available Rooms' mod='hotelreservationsystem'}</a></li>
                        <li><a href="#part_room_data_{$book_k|escape:'htmlall':'UTF-8'}" data-toggle="tab">{l s='Partially Available' mod='hotelreservationsystem'}</a></li>
                        <li><a href="#book_room_data_{$book_k|escape:'htmlall':'UTF-8'}" data-toggle="tab">{l s='Booked Rooms' mod='hotelreservationsystem'}</a></li>
                        <li><a href="#unavail_room_data_{$book_k|escape:'htmlall':'UTF-8'}" data-toggle="tab">{l s='Unavailable Rooms' mod='hotelreservationsystem'}</a></li>
                    </ul>
                    <div class="tab-content panel">
                        <div id="avail_room_data_{$book_k|escape:'htmlall':'UTF-8'}" class="tab-pane active">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th><span class="title_box">{l s='Room No.' mod='hotelreservationsystem'}</span></th>
                                            <th><span class="title_box">{l s='Duration' mod='hotelreservationsystem'}</span></th>
                                            <th><span class="title_box">{l s='Message' mod='hotelreservationsystem'}</span></th>
                                            <th><span class="title_box">{l s='Allotment Type' mod='hotelreservationsystem'}</span></th>
                                            {if $occupancy_required_for_booking}
                                                <th class="fixed-width-xxl"><span class="title_box">{l s='Guests' mod='hotelreservationsystem'}</span></th>
                                            {/if}
                                            <th><span class="title_box">{l s='Action' mod='hotelreservationsystem'}</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {foreach from=$book_v['data']['available'] key=avai_k item=avai_v}
                                            <tr>
                                                <td>{$avai_v['room_num']|escape:'htmlall':'UTF-8'} {hook h='displayRoomNumAfter' data=$avai_v type='available'}</td>
                                                <td>{dateFormat date=date('Y-m-d', strtotime($date_from))} - {dateFormat date=date('Y-m-d', strtotime($date_to))}</td>
                                                <td>{$avai_v['room_comment']|escape:'htmlall':'UTF-8'}</td>
                                                <td>
                                                    {foreach $allotment_types as $allotment_type}
                                                        <label class="control-label">
                                                            <input type="radio" value="{$allotment_type.id_allotment|intval}" name="bk_type_{$avai_v['id_room']|escape:'htmlall':'UTF-8'}" data-id-room="{$avai_v['id_room']|escape:'htmlall':'UTF-8'}" class="avai_bk_type" {if $allotment_type@first}checked="checked"{/if}>
                                                            <span>{$allotment_type.name|escape:'htmlall':'UTF-8'}</span>
                                                        </label>
                                                    {/foreach}
                                                    <input type="text" id="comment_{$avai_v['id_room']|escape:'htmlall':'UTF-8'}" class="form-control booking_type_comment" placeholder="{l s='Allotment message' mod='hotelreservationsystem'}">
                                                </td>
                                                {if $occupancy_required_for_booking}
                                                    <td class="booking_occupancy">
                                                        <div class="dropdown">
                                                            <button class="btn btn-default btn-left btn-block booking_guest_occupancy input-occupancy" type="button">
                                                                <span>{l s='Select occupancy' mod='hotelreservationsystem'}</span>
                                                            </button>
                                                            <div class="dropdown-menu booking_occupancy_wrapper well well-sm">
                                                                <input type="hidden" class="max_adults" value="{if isset($book_v)}{$book_v['max_adults']|escape:'html':'UTF-8'}{/if}">
                                                                <input type="hidden" class="max_children" value="{if isset($book_v)}{$book_v['max_children']|escape:'html':'UTF-8'}{/if}">
                                                                <input type="hidden" class="max_guests" value="{if isset($book_v)}{$book_v['max_guests']|escape:'html':'UTF-8'}{/if}">
                                                                <div class="booking_occupancy_inner row">
                                                                    <div class="occupancy_info_block col-sm-12" occ_block_index="0">
                                                                        <div class="occupancy_info_head col-sm-12"><label class="room_num_wrapper">{l s='Room - 1' mod='hotelreservationsystem'}</label></div>
                                                                        <div class="col-sm-12">
                                                                            <div class="row">
                                                                                <div class="form-group col-xs-6 occupancy_count_block">
                                                                                    <label>{l s='Adults' mod='hotelreservationsystem'}</label>
                                                                                    <input type="number" class="form-control num_occupancy num_adults" name="occupancy[0][adults]" value="1" min="1"  data-max="{if isset($book_v)}{$book_v['max_adults']|escape:'html':'UTF-8'}{/if}">
                                                                                </div>
                                                                                <div class="form-group col-xs-6 occupancy_count_block">
                                                                                    <label>{l s='Children' mod='hotelreservationsystem'} <span class="label-desc-txt"></span></label>
                                                                                    <input type="number" class="form-control num_occupancy num_children" name="occupancy[0][children]" value="0" min="0" data-max="{if isset($book_v)}{$book_v['max_children']|escape:'html':'UTF-8'}{else}{$max_child_in_room}{/if}">
                                                                                    ({l s='Below' mod='hotelreservationsystem'}  {$max_child_age|escape:'htmlall':'UTF-8'} {l s='years' mod='hotelreservationsystem'})
                                                                                </div>
                                                                            </div>
                                                                            <p style="display:none;"><span class="text-danger occupancy-input-errors"></span></p>
                                                                            <div class="row children_age_info_block" style="display:none">
                                                                                <div class="form-group col-sm-12">
                                                                                    <label class="">{l s='All Children' mod='hotelreservationsystem'}</label>
                                                                                    <div class="">
                                                                                        <div class="row children_ages">
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        {* <hr class="occupancy-info-separator"> *}
                                                                    </div>
                                                                </div>
                                                                {* <div class="add_occupancy_block">
                                                                    <a class="add_new_occupancy_btn" href="#"><i class="icon-plus"></i> <span>{l s='Add Room'}</span></a>
                                                                </div> *}
                                                            </div>
                                                        </div>
                                                    </td>
                                                {/if}
                                                <td>
                                                    <button type="button" data-id-cart="" data-id-cart-book-data="" data-id-product="{$avai_v['id_product']|escape:'htmlall':'UTF-8'}" data-id-room="{$avai_v['id_room']|escape:'htmlall':'UTF-8'}" data-id-hotel="{$avai_v['id_hotel']}" data-date-from="{$date_from|escape:'htmlall':'UTF-8'|date_format:'%Y-%m-%d'}" data-date-to ="{$date_to|escape:'htmlall':'UTF-8'|date_format:'%Y-%m-%d'}" class="btn btn-primary avai_add_cart">{l s='Add To Cart' mod='hotelreservationsystem'}</button>
                                                </td>
                                            </tr>
                                        {/foreach}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div id="part_room_data_{$book_k|escape:'htmlall':'UTF-8'}" class="tab-pane">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th><span class="title_box">{l s='Duration' mod='hotelreservationsystem'}</span></th>
                                            <th><span class="title_box">{l s='Room No.' mod='hotelreservationsystem'}</span></th>
                                            <th><span class="title_box">{l s='Allotment Type' mod='hotelreservationsystem'}</span></th>
                                            {if $occupancy_required_for_booking}
                                                <th class="fixed-width-xxl"><span class="title_box">{l s='Guests' mod='hotelreservationsystem'}</span></th>
                                            {/if}
                                            <th><span class="title_box">{l s='Action' mod='hotelreservationsystem'}</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {foreach from=$book_v['data']['partially_available'] key=part_k item=part_v}
                                            {foreach from=$part_v['rooms'] key=sub_part_k item=sub_part_v}
                                                <tr>
                                                    {if $sub_part_v@first}
                                                        <td rowspan="{$part_v['rooms']|count}">
                                                            <p>{dateFormat date=date('Y-m-d', strtotime($part_v['date_from']))} - {dateFormat date=date('Y-m-d', strtotime($part_v['date_to']))}</p>
                                                        </td>
                                                    {/if}
                                                    <td >{$sub_part_v['room_num']|escape:'htmlall':'UTF-8'} {hook h='displayRoomNumAfter' data=$sub_part_v type='partially_available'}</td>
                                                    <td>
                                                        {foreach $allotment_types as $allotment_type}
                                                            <label class="control-label">
                                                                <input type="radio" value="{$allotment_type.id_allotment|intval}" class="par_bk_type" name="bk_type_{$part_k|escape:'htmlall':'UTF-8'}_{$sub_part_k|escape:'htmlall':'UTF-8'}" data-id-room="{$sub_part_v['id_room']|escape:'htmlall':'UTF-8'}" data-sub-key="{$sub_part_k|escape:'htmlall':'UTF-8'}" {if $allotment_type@first}checked="checked"{/if}>
                                                                <span>{$allotment_type.name|escape:'htmlall':'UTF-8'}</span>
                                                            </label>
                                                        {/foreach}
                                                        <input type="text" id="comment_{$sub_part_v['id_room']|escape:'htmlall':'UTF-8'}_{$sub_part_k|escape:'htmlall':'UTF-8'}" class="form-control booking_type_comment" placeholder="{l s='Allotment message' mod='hotelreservationsystem'}">
                                                    </td>
                                                    {if $occupancy_required_for_booking}
                                                        <td class="booking_occupancy">
                                                            <div class="dropdown">
                                                                <button class="btn btn-default btn-left btn-block booking_guest_occupancy input-occupancy" type="button">
                                                                    <span>{l s='Select occupancy' mod='hotelreservationsystem'}</span>
                                                                </button>
                                                                <div class="dropdown-menu booking_occupancy_wrapper well well-sm">
                                                                    <input type="hidden" class="max_adults" value="{if isset($book_v)}{$book_v['max_adults']|escape:'html':'UTF-8'}{/if}">
                                                                    <input type="hidden" class="max_children" value="{if isset($book_v)}{$book_v['max_children']|escape:'html':'UTF-8'}{/if}">
                                                                    <input type="hidden" class="max_guests" value="{if isset($book_v)}{$book_v['max_guests']|escape:'html':'UTF-8'}{/if}">
                                                                    <div class="booking_occupancy_inner row">
                                                                        <div class="occupancy_info_block col-sm-12" occ_block_index="0">
                                                                            <div class="occupancy_info_head col-sm-12"><label class="room_num_wrapper">{l s='Room - 1' mod='hotelreservationsystem'}</label></div>
                                                                            <div class="col-sm-12">
                                                                                <div class="row">
                                                                                    <div class="form-group col-xs-6 occupancy_count_block">
                                                                                        <label>{l s='Adults' mod='hotelreservationsystem'}</label>
                                                                                        <input type="number" class="form-control num_occupancy num_adults" name="occupancy[0][adults]" value="1" min="1"  data-max="{if isset($book_v)}{$book_v['max_adults']|escape:'html':'UTF-8'}{/if}">
                                                                                    </div>
                                                                                    <div class="form-group col-xs-6 occupancy_count_block">
                                                                                        <label>{l s='Children' mod='hotelreservationsystem'} <span class="label-desc-txt"></span></label>
                                                                                        <input type="number" class="form-control num_occupancy num_children" name="occupancy[0][children]" value="0" min="0" data-max="{if isset($book_v)}{$book_v['max_children']|escape:'html':'UTF-8'}{else}{$max_child_in_room}{/if}">
                                                                                        ({l s='Below'}  {$max_child_age|escape:'htmlall':'UTF-8'} {l s='years' mod='hotelreservationsystem'})
                                                                                    </div>
                                                                                </div>
                                                                                <p style="display:none;"><span class="text-danger occupancy-input-errors"></span></p>
                                                                                <div class="row children_age_info_block" style="display:none">
                                                                                    <div class="form-group col-sm-12">
                                                                                        <label class="">{l s='All Children' mod='hotelreservationsystem'}</label>
                                                                                        <div class="">
                                                                                            <div class="row children_ages">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            {* <hr class="occupancy-info-separator"> *}
                                                                        </div>
                                                                    </div>
                                                                    {* <div class="add_occupancy_block">
                                                                        <a class="add_new_occupancy_btn" href="#"><i class="icon-plus"></i> <span>{l s='Add Room'}</span></a>
                                                                    </div> *}
                                                                </div>
                                                            </div>
                                                        </td>
                                                    {/if}
                                                    <td>
                                                        <button type="button" data-id-cart="" data-id-cart-book-data="" data-id-product="{$sub_part_v['id_product']|escape:'htmlall':'UTF-8'}" data-id-room="{$sub_part_v['id_room']|escape:'htmlall':'UTF-8'}" data-id-hotel="{$sub_part_v['id_hotel']|escape:'htmlall':'UTF-8'}" data-date-from="{$part_v['date_from']|escape:'htmlall':'UTF-8'|date_format:'%Y-%m-%d'}" data-date-to ="{$part_v['date_to']|escape:'htmlall':'UTF-8'|date_format:'%Y-%m-%d'}" data-sub-key="{$sub_part_k|escape:'htmlall':'UTF-8'}" class="btn btn-primary par_add_cart">{l s='Add To Cart' mod='hotelreservationsystem'}</button>
                                                    </td>
                                                </tr>
                                            {/foreach}
                                        {/foreach}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div id="book_room_data_{$book_k|escape:'htmlall':'UTF-8'}" class="tab-pane">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th><span class="title_box">{l s='Room No.' mod='hotelreservationsystem'}</span></th>
                                            <th><span class="title_box">{l s='Duration' mod='hotelreservationsystem'}</span></th>
                                            <th><span class="title_box">{l s='Message' mod='hotelreservationsystem'}</span></th>
                                            <th><span class="title_box">{l s='Allotment Type' mod='hotelreservationsystem'}</span></th>
                                            <th><span class="title_box">{l s='Reallocate' mod='hotelreservationsystem'}</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {foreach from=$book_v['data']['booked'] key=booked_k item=booked_v}
                                            {foreach from=$booked_v['detail'] key=rm_dtl_k item=rm_dtl_v}
                                                <tr>
                                                    {if $rm_dtl_v@first}
                                                        <td rowspan="{$booked_v['detail']|count}">{$booked_v['room_num']|escape:'htmlall':'UTF-8'} {hook h='displayRoomNumAfter' data=$booked_v key=$rm_dtl_k type='booked'}</td>
                                                    {/if}
                                                    <td>{dateFormat date=date('Y-m-d', strtotime($rm_dtl_v['date_from']))} - {dateFormat date=date('Y-m-d', strtotime($rm_dtl_v['date_to']))}</td>
                                                    <td>{$rm_dtl_v['comment']|escape:'htmlall':'UTF-8'}</td>
                                                    <td>
                                                        {if $rm_dtl_v['booking_type'] == HotelBookingDetail::ALLOTMENT_AUTO}{l s='Auto Allotment' mod='hotelreservationsystem'}{elseif $rm_dtl_v['booking_type'] == HotelBookingDetail::ALLOTMENT_MANUAL}{l s='Manual Allotment' mod='hotelreservationsystem'}{/if}
                                                    </td>
                                                    <td>
                                                        {if $rm_dtl_v['booking_type'] == HotelBookingDetail::ALLOTMENT_AUTO}
                                                            <button type="button" class="btn btn-primary room_reallocate_swap" id="reallocate_room_{$rm_dtl_v['id_htl_booking']}" data-room_type_name="{$rm_dtl_v['room_type_name']}" data-id_htl_booking="{$rm_dtl_v['id_htl_booking']}" data-id_order="{$rm_dtl_v['id_order']}" data-room_num='{$booked_v['room_num']}' data-currency_sign='{$rm_dtl_v['currency_sign']}' data-id_room_type='{$booked_v['id_product']}' data-cust_name='{$rm_dtl_v['alloted_cust_name']}' data-cust_email='{$rm_dtl_v['alloted_cust_email']}' data-avail_rm_swap='{$rm_dtl_v['avail_rooms_to_swap']|@json_encode}' data-avail_realloc_room_types='{$rm_dtl_v['avail_room_types_to_realloc']|@json_encode}'>
                                                                {l s='Reallocate Room' mod='hotelreservationsystem'}
                                                            </button>
                                                        {else}
                                                            --
                                                        {/if}
                                                    </td>
                                                </tr>
                                            {/foreach}
                                        {/foreach}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div id="unavail_room_data_{$book_k|escape:'htmlall':'UTF-8'}" class="tab-pane">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th><span class="title_box">{l s='Room No.' mod='hotelreservationsystem'}</span></th>
                                            <th><span class="title_box">{l s='Message' mod='hotelreservationsystem'}</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {foreach from=$book_v['data']['unavailable'] key=unavai_k item=unavai_v}
                                            <tr>
                                                <td>{$unavai_v['room_num']|escape:'htmlall':'UTF-8'}</td>
                                                <td>{$unavai_v['room_comment']|escape:'htmlall':'UTF-8'}</td>
                                            </tr>
                                        {/foreach}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            {/foreach}
        </div>
    </div>
</div>
