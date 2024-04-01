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

{include file='controllers/orders/modals/_extra_services_facilities.tpl'}
{include file='controllers/orders/modals/_extra_services_service_products.tpl'}

{* Css for handling extra demands changes *}
<style type="text/css">
	/*Extra demands CSS*/
	#room_extra_demand_content .modal-header {
		padding-bottom: 0px;}
	#rooms_type_extra_demands .modal-title {
		margin: 0px;}
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
	#rooms_extra_demands .room_demands_container,
	#rooms_extra_demands .room_services_container,
	#edit_product .extra-services-container .room_demands_container,
	#edit_product .extra-services-container .room_services_container {
		display: none;}
	#room_extra_demand_content #save_room_demands,
	#room_extra_demand_content #back_to_demands_btn,
	#room_extra_demand_content #save_service_service,
	#room_extra_demand_content #back_to_service_btn,
	#edit_product .extra-services-container #save_room_demands,
	#edit_product .extra-services-container #back_to_demands_btn,
	#edit_product .extra-services-container #save_service_service,
	#edit_product .extra-services-container #back_to_service_btn {
		display: none;}
</style>

