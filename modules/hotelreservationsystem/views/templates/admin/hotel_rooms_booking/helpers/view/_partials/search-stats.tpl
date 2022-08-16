<div class="htl_room_data_cont">
    <div class="row">
        <div class="col-sm-6">
            <div class="row">
                <div class="col-sm-12 htl_room_cat_data">
                    <p class="room_cat_header">{l s='Total Rooms' mod='hotelreservationsystem'}</p>
                    <p class="room_cat_data">{if isset($booking_data) && $booking_data}{$booking_data['stats']['total_rooms']|escape:'htmlall':'UTF-8'}{else}00{/if}</p>
                </div>
            </div>
            <hr class="hr_style" />
        </div>
        <div class="col-sm-6">
            <div class="row">
                <div class="col-sm-12 htl_room_cat_data">
                    <p class="room_cat_header">{l s='Available Rooms' mod='hotelreservationsystem'}</p>
                    <p class="room_cat_data" id="num_avail">{if isset($booking_data) && $booking_data}{$booking_data['stats']['num_avail']|escape:'htmlall':'UTF-8'}{else}00{/if}</p>
                </div>
            </div>
            <hr class="hr_style" />
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <div class="row">
                <div class="col-sm-12 htl_room_cat_data">
                    <p class="room_cat_header">{l s='Partially Available' mod='hotelreservationsystem'}</p>
                    <p class="room_cat_data" id="num_part">{if isset($booking_data) && $booking_data}{$booking_data['stats']['num_part_avai']|escape:'htmlall':'UTF-8'}{else}00{/if}</p>
                </div>
            </div>
            <hr class="hr_style" />
        </div>
        <div class="col-sm-6">
            <div class="row">
                <div class="col-sm-12 htl_room_cat_data">
                    <p class="room_cat_header">{l s='Booked Rooms' mod='hotelreservationsystem'}</p>
                    <p class="room_cat_data">{if isset($booking_data) && $booking_data}{$booking_data['stats']['num_booked']|escape:'htmlall':'UTF-8'}{else}00{/if}</p>
                </div>
            </div>
            <hr class="hr_style" />
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="row">
                <div class="col-sm-12 htl_room_cat_data">
                    <p class="room_cat_header">{l s='Unavailable Rooms' mod='hotelreservationsystem'}</p>
                    <p class="room_cat_data">{if isset($booking_data) && $booking_data}{$booking_data['stats']['num_unavail']|escape:'htmlall':'UTF-8'}{else}00{/if}</p>
                </div>
            </div>
            <hr class="hr_style" />
        </div>
        <div class="col-sm-6">
            <div class="row">
                <div class="col-sm-12 htl_room_cat_data">
                    <p class="room_cat_header">{l s='In-Cart Rooms' mod='hotelreservationsystem'}</p>
                    <p class="room_cat_data" id="cart_stats">{if isset($booking_data) && $booking_data}{$booking_data['stats']['num_cart']|escape:'htmlall':'UTF-8'}{else}00{/if}</p>
                </div>
            </div>
            <hr class="hr_style" />
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6 indi_cont clearfix">
            <span class="color_indicate bg-green"></span>
            <span class="indi_label">{l s='Available Rooms' mod='hotelreservationsystem'}</span>
        </div>
        <div class="col-sm-6 indi_cont clearfix">
            <span class="color_indicate bg-yellow"></span>
            <span class="indi_label">{l s='Partially Available' mod='hotelreservationsystem'}</span>
        </div>
        <div class="col-sm-6 indi_cont clearfix">
            <span class="color_indicate bg-red"></span>
            <span class="indi_label">{l s='Unavailable Rooms' mod='hotelreservationsystem'}</span>
        </div>
        <div class="col-sm-6 indi_cont clearfix">
            <span class="color_indicate bg-blue"></span>
            <span class="indi_label">{l s='Booked Rooms' mod='hotelreservationsystem'}</span>
        </div>
    </div>
</div>