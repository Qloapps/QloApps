{if $order_detail_data}
    <div class="row">
        <div class="col-lg-12">
            <div class="table-responsive">
                <table class="table" id="customer_cart_details">
                    <thead>
                        <tr>
                            <th><span class="title_box">{l s='Room No.'}</span></th>
                            <th><span class="title_box">{l s='Image'}</th>
                            <th><span class="title_box">{l s='Room Type'}</span></th>
                            <th><span class="title_box">{l s='Hotel Name'}</span></th>
                            <th><span class="title_box">{l s='Duration'}</span></th>
                            <th class="text-center fixed-width-lg"><span class="title_box">{l s='Occupancy'}</span></th>
                            <th><span class="title_box">{l s='Room Price (Tax excl.)'}</span></th>
                            <th><span class="title_box">{l s='Extra Services/Fee (Tax excl.)'}</span></th>
                            <th><span class="title_box">{l s='Total Tax'}</span></th>
                            <th><span class="title_box">{l s='Total Price (Tax incl.)'}</span></th>
                            {if (isset($refundReqBookings) && $refundReqBookings) || (isset($isCancelledRoom) && $isCancelledRoom)}
                                <th><span class="title_box">{l s='Refund/Cancellation Status'}</span></th>
                                <th><span class="title_box">{l s='Refunded amount'}</span></th>
                            {/if}
                            {if ($can_edit && !$order->hasBeenDelivered())}
                            <th class="fixed-width-xs"><span class="title_box">{l s='Actions'}</th>
                            {/if}
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$order_detail_data item=data}
                            {* Include product line partial *}
                            {include file='controllers/orders/_product_line.tpl'}
                        {/foreach}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
{else}
    <div class="list-empty">
        <div class="list-empty-msg">
            <i class="icon-warning-sign list-empty-icon"></i>
            {l s='Room information not available.'}
        </div>
    </div>
{/if}