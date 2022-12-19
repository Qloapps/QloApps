<div class="col-sm-12" id="htl_rooms_list">
    <div class="panel">
        <ul class="nav nav-tabs">
            {foreach from=$booking_data['rm_data'] key=book_k item=book_v}
                <li {if $book_k == 0}class="active"{/if} ><a href="#room_type_{$book_k}" data-toggle="tab">{$book_v['name']}</a></li>
            {/foreach}
        </ul>
        <div class="tab-content panel">
            {foreach from=$booking_data['rm_data'] key=book_k item=book_v}
                <div id="room_type_{$book_k}" class="tab-pane {if $book_k == 0}active{/if}">
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
                                            <th><span class="title_box">{l s='Action' mod='hotelreservationsystem'}</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {foreach from=$book_v['data']['available'] key=avai_k item=avai_v}
                                            <tr>
                                                <td>{$avai_v['room_num']|escape:'htmlall':'UTF-8'}</td>
                                                <td>{dateFormat date=date('Y-m-d', strtotime($date_from))} - {dateFormat date=date('Y-m-d', strtotime($date_to))}</td>
                                                <td>{$avai_v['room_comment']|escape:'htmlall':'UTF-8'}</td>
                                                <td>
                                                    {foreach $allotment_types as $allotment_type}
                                                        <label class="control-label">
                                                            <input type="radio" value="{$allotment_type.id_allotment|intval}" name="bk_type_{$avai_v['id_room']|escape:'htmlall':'UTF-8'}" data-id-room="{$avai_v['id_room']|escape:'htmlall':'UTF-8'}" class="avai_bk_type" {if $allotment_type@first}checked="checked"{/if}>
                                                            <span>{$allotment_type.name|escape:'htmlall':'UTF-8'}</span>
                                                        </label>
                                                    {/foreach}
                                                    <input type="text" id="comment_{$avai_v['id_room']|escape:'htmlall':'UTF-8'}" class="form-control avai_comment" placeholder="{l s='Allotment message' mod='hotelreservationsystem'}">
                                                </td>
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
                                            <th><span class="title_box">{l s='Room No.' mod='hotelreservationsystem'}</span></th>
                                            <th class="text-center"><span class="title_box">{l s='Duration' mod='hotelreservationsystem'}</span></th>
                                            <th class="text-left"><span class="title_box">{l s='Allotment Type' mod='hotelreservationsystem'}</span></th>
                                            <th class="text-center"><span class="title_box">{l s='Action' mod='hotelreservationsystem'}</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {foreach from=$book_v['data']['partially_available'] key=part_k item=part_v}
                                            <tr>
                                                <td>{$part_v['room_num']|escape:'htmlall':'UTF-8'}</td>
                                                <td colspan="3">
                                                    <table class="table">
                                                        {foreach from=$part_v['avai_dates'] key=sub_part_k item=sub_part_v}
                                                            <tr>
                                                                <td class="text-center">
                                                                <p>{dateFormat date=date('Y-m-d', strtotime($sub_part_v['date_from']))} - {dateFormat date=date('Y-m-d', strtotime($sub_part_v['date_to']))}</p>
                                                                </td>
                                                                <td class="text-left">
                                                                    {foreach $allotment_types as $allotment_type}
                                                                        <label class="control-label">
                                                                            <input type="radio" value="{$allotment_type.id_allotment|intval}" class="par_bk_type" name="bk_type_{$part_v['id_room']|escape:'htmlall':'UTF-8'}_{$sub_part_k|escape:'htmlall':'UTF-8'}" data-id-room="{$part_v['id_room']|escape:'htmlall':'UTF-8'}" data-sub-key="{$sub_part_k|escape:'htmlall':'UTF-8'}" {if $allotment_type@first}checked="checked"{/if}>
                                                                            <span>{$allotment_type.name|escape:'htmlall':'UTF-8'}</span>
                                                                        </label>
                                                                    {/foreach}
                                                                    <input type="text" id="comment_{$part_v['id_room']|escape:'htmlall':'UTF-8'}_{$sub_part_k|escape:'htmlall':'UTF-8'}" class="form-control par_comment" placeholder="{l s='Allotment message' mod='hotelreservationsystem'}">
                                                                </td>
                                                                <td class="text-center">
                                                                    <button type="button" data-id-cart="" data-id-cart-book-data="" data-id-product="{$part_v['id_product']|escape:'htmlall':'UTF-8'}" data-id-room="{$part_v['id_room']|escape:'htmlall':'UTF-8'}" data-id-hotel="{$part_v['id_hotel']|escape:'htmlall':'UTF-8'}" data-date-from="{$sub_part_v['date_from']|escape:'htmlall':'UTF-8'|date_format:'%Y-%m-%d'}" data-date-to ="{$sub_part_v['date_to']|escape:'htmlall':'UTF-8'|date_format:'%Y-%m-%d'}" data-sub-key="{$sub_part_k|escape:'htmlall':'UTF-8'}" class="btn btn-primary par_add_cart">{l s='Add To Cart' mod='hotelreservationsystem'}</button>
                                                                </td>
                                                            </tr>
                                                        {/foreach}
                                                    </table>
                                                </td>
                                            </tr>
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
                                            <th class="text-center"><span class="title_box">{l s='Duration' mod='hotelreservationsystem'}</span></th>
                                            <th class="text-center"><span class="title_box">{l s='Message' mod='hotelreservationsystem'}</span></th>
                                            <th class="text-center"><span class="title_box">{l s='Allotment Type' mod='hotelreservationsystem'}</span></th>
                                            <th><span class="title_box">{l s='Reallocate' mod='hotelreservationsystem'}</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {foreach from=$book_v['data']['booked'] key=booked_k item=booked_v}
                                            <tr>
                                                <td>{$booked_v['room_num']|escape:'htmlall':'UTF-8'}</td>
                                                <td colspan="4">
                                                    <table class="table">
                                                        {foreach from=$booked_v['detail'] key=rm_dtl_k item=rm_dtl_v}
                                                            <tr>
                                                            <td class="col-xs-3">{dateFormat date=date('Y-m-d', strtotime($rm_dtl_v['date_from']))} - {dateFormat date=date('Y-m-d', strtotime($rm_dtl_v['date_to']))}</td>
                                                                <td class="col-xs-3">{$rm_dtl_v['comment']|escape:'htmlall':'UTF-8'}</td>
                                                                <td class="col-xs-3">
                                                                    {if $rm_dtl_v['booking_type'] == HotelBookingDetail::ALLOTMENT_AUTO}{l s='Auto Allotment' mod='hotelreservationsystem'}{elseif $rm_dtl_v['booking_type'] == HotelBookingDetail::ALLOTMENT_MANUAL}{l s='Manual Allotment' mod='hotelreservationsystem'}{/if}
                                                                </td>
                                                                <td>
                                                                    {if $rm_dtl_v['booking_type'] == HotelBookingDetail::ALLOTMENT_AUTO}
                                                                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#mySwappigModal" data-room_num={$booked_v['room_num']|escape:'htmlall':'UTF-8'} data-date_from={$rm_dtl_v['date_from']|escape:'htmlall':'UTF-8'} data-date_to={$rm_dtl_v['date_to']|escape:'htmlall':'UTF-8'} data-id_room={$booked_v['id_room']|escape:'htmlall':'UTF-8'} data-cust_name="{$rm_dtl_v['alloted_cust_name']|escape:'htmlall':'UTF-8'}" data-cust_email="{$rm_dtl_v['alloted_cust_email']|escape:'htmlall':'UTF-8'}" data-avail_rm_realloc={$rm_dtl_v['avail_rooms_to_realloc']|@json_encode} data-avail_rm_swap={$rm_dtl_v['avail_rooms_to_swap']|@json_encode}>
                                                                            {l s='Reallocate Room' mod='hotelreservationsystem'}
                                                                        </button>
                                                                    {else}
                                                                        --
                                                                    {/if}
                                                                </td>
                                                            </tr>
                                                        {/foreach}
                                                    </table>
                                                </td>
                                            </tr>
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