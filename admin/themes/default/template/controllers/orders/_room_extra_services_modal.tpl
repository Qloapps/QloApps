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

<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
		<span aria-hidden="true">&times;</span>
	</button>
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active"><a href="#room_type_demands_desc" aria-controls="facilities" role="tab" data-toggle="tab">{l s='Facilities'}</a></li>
		<li role="presentation"><a href="#room_type_service_product_desc" aria-controls="services" role="tab" data-toggle="tab">{l s='Services'}</a></li>
	</ul>
</div>
<div class="modal-body" id="rooms_extra_demands">
	<div class="tab-content clearfix">
		{include file='controllers/orders/_room_facilities_block.tpl'}
		{include file='controllers/orders/_room_services_block.tpl'}
	</div>
</div>
<div class="modal-footer">
</div>



{* Css for handling extra demands changes *}
<style type="text/css">
	/*Extra demands CSS*/
	#room_extra_demand_content .modal-header {
		padding-bottom: 0px;}
	#rooms_type_extra_demands .modal-title {
		margin: 0px;}
	#rooms_type_extra_demands .demand_edit_badge {
		font-size: 14px;}
	/* #rooms_extra_demands .room_ordered_demands td, #rooms_extra_demands .room_demand_detail { */
		/* font-size: 14px;} */
	#rooms_extra_demands .demand_header {
		padding: 10px;
		color: #333;
    	border-bottom: 1px solid #ddd;}
	#rooms_extra_demands .room_demand_block {
		margin-bottom: 15px;
		color: #333;
		font-size: 14px;}
	#rooms_extra_demands .facility_nav_btn {
		margin-bottom: 20px;}
	#rooms_extra_demands .room_demands_container, #rooms_extra_demands .room_services_container {
		display: none;}
	#room_extra_demand_content #save_room_demands, #room_extra_demand_content #back_to_demands_btn, #room_extra_demand_content #save_service_service, #room_extra_demand_content #back_to_service_btn {
		display: none;}
</style>

