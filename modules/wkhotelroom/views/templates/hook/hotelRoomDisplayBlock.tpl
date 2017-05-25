<div id="hotelRoomsBlock" class="row home_block_container">
    <div class="col-xs-12 col-sm-12">
        <div class="row home_block_desc_wrapper">
            <div class="col-md-offset-1 col-md-10 col-lg-offset-2 col-lg-8">
                <p class="home_block_heading">{$HOTEL_ROOM_DISPLAY_HEADING}</p>
                <p class="home_block_description">{$HOTEL_ROOM_DISPLAY_DESCRIPTION}</p>
                <hr class="home_block_desc_line"/>
            </div>
        </div>
        {if $hotelRoomDisplay}
            <div class="row home_block_content">
                <div class="col-sm-12 col-xs-12">
                    {assign var='htlRoomBlockIteration' value=0}
                    {foreach from=$hotelRoomDisplay item=roomDisplay name=htlRoom}
                        {if $smarty.foreach.htlRoom.iteration%2}
                            <div class="row">
                        {/if}
                                <div class="col-sm-12 col-md-6 margin-btm-30">
                                    <img src="{$roomDisplay.image}" alt="{$roomDisplay.name}" class="img-responsive width-100">
                                    <div class="hotelRoomDescContainer">
                                        <div class="row margin-lr-0">
                                            <p class="htlRoomTypeNameText pull-left">{$roomDisplay.name}</p>
                                            <p class="htlRoomTypePriceText pull-right">{convertPrice price = $roomDisplay.price}/&nbsp;{l s='Per Night' mod='wkhotelroom'}</p>
                                        </div>
                                        <div class="row margin-lr-0 htlRoomTypeDescText">
                                            {$roomDisplay.description}
                                        </div>
                                        <div class="row margin-lr-0">
                                            <a target="blank" class="btn btn-default button htlRoomTypeBookNow" href="{$link->getProductLink($roomDisplay.id_product)|escape:'html':'UTF-8'}"><span>{l s='book now' mod='wkhotelroom'}</span></a>
                                        </div>
                                    </div>
                                </div>
                        {if !($smarty.foreach.htlRoom.iteration%2)}
                            </div>
                        {/if}
                        {assign var='htlRoomBlockIteration' value=$smarty.foreach.htlRoom.iteration}
                    {/foreach}
                    {if $htlRoomBlockIteration%2}
                        </div>
                    {/if}
                </div>
            </div>
        {/if}
    </div>
    <hr class="home_block_seperator"/>
</div>