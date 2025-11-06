<?php
/**
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
*/

require_once 'classes/HotelReservationSystemDb.php';
require_once 'classes/HotelRoomType.php';
require_once 'classes/HotelRoomInformation.php';
require_once 'classes/HotelBranchInformation.php';
require_once 'classes/HotelImage.php';
require_once 'classes/HotelFeatures.php';
require_once 'classes/HotelBranchFeatures.php';
require_once 'classes/HotelBookingDetail.php';
require_once 'classes/HotelCartBookingData.php';
require_once 'classes/HotelAdvancedPayment.php';
require_once 'classes/HotelOrderRefundRules.php';
require_once 'classes/HotelOrderRestrictDate.php';
require_once 'classes/HotelHelper.php';
require_once 'classes/HotelRoomTypeFeaturePricing.php';
require_once 'classes/HotelRoomTypeFeaturePricingRestriction.php';
require_once 'classes/ChannelOrderPayment.php';

require_once 'classes/HotelBookingDemands.php';
require_once 'classes/HotelRoomTypeGlobalDemand.php';
require_once 'classes/HotelRoomTypeGlobalDemandAdvanceOption.php';
require_once 'classes/HotelRoomTypeDemandPrice.php';
require_once 'classes/HotelRoomTypeDemand.php';
require_once 'classes/HotelRoomTypeRestrictionDateRange.php';

require_once 'classes/HotelRoomDisableDates.php';
require_once 'classes/HotelBranchRefundRules.php';
require_once 'classes/HotelBedType.php';
require_once 'classes/HotelRoomTypeBedType.php';

// linked products
require_once 'classes/RoomTypeServiceProduct.php';
require_once 'classes/RoomTypeServiceProductPrice.php';
require_once 'classes/ServiceProductCartDetail.php';
require_once 'classes/ServiceProductOrderDetail.php';
require_once 'classes/ServiceProductOption.php';


require_once 'classes/HotelSettingsLink.php';
require_once 'classes/HotelBookingDocument.php';

// Web services classes
require_once 'classes/WebserviceSpecificManagementHotelAri.php';
