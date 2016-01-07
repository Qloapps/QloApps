<div class="row margin-lr-0" id="footer_top">
	<div class="col-xs-12 col-sm-6 col-lg-2 htl_admin_dtl">
		{l s='Call Us : ' mod='hotelreservationsystem'} 
		{if isset($hotel_global_contact_num) && hotel_global_contact_num}
			{$hotel_global_contact_num}
		{/if}
	</div>
    <div class="col-xs-12 col-sm-6 col-lg-4 htl_admin_dtl">
    	{l s='Email : ' mod='hotelreservationsystem'}
    	{if isset($hotel_global_email) && hotel_global_email}
    		{$hotel_global_email}
    	{/if}
    </div>
	{$HOOK_FOOTER_TOP}
</div>

<style type="text/css">
    #footer_top
    {
        background-color: #bf9958;
    }

    .htl_admin_dtl
    {
    	text-align: center;
        font-size:18px;
        color: #ffffff;
        padding-top: 25px; 
    }
	@media (max-width: 400px) {
		.htl_admin_dtl {
			font-size: 15px;
		}
    }
</style>