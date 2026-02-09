{*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License version 3.0
* that is bundled with this package in the file LICENSE.md
* It is also available through the world-wide-web at this URL:
* https://opensource.org/license/osl-3-0-php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to support@qloapps.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade this module to a newer
* versions in the future. If you wish to customize this module for your needs
* please refer to https://store.webkul.com/customisation-guidelines for more information.
*
* @author Webkul IN
* @copyright Since 2010 Webkul
* @license https://opensource.org/license/osl-3-0-php Open Software License version 3.0
*}

{include file='controllers/orders/modals/_extra_services_facilities_tab_content.tpl'}
{include file='controllers/orders/modals/_extra_services_service_products_tab_content.tpl'}

{* Css for handling extra demands changes *}
<style type="text/css">
	/*Extra demands CSS*/
	#edit_product .extra-services-container .room_demands_container,
	#edit_product .extra-services-container .room_services_container {
		display: none;}
	#edit_product .extra-services-container #save_room_demands,
	#edit_product .extra-services-container #back_to_demands_btn,
	#edit_product .extra-services-container #save_service_service,
	#edit_product .extra-services-container #back_to_service_btn {
		display: none;}
</style>

