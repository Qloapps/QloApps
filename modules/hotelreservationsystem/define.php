<?php
/**
* 2010-2020 Webkul.
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
*  @copyright 2010-2020 Webkul IN
*  @license   https://store.webkul.com/license.html
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
require_once 'classes/ChannelOrderPayment.php';

require_once 'classes/HotelBookingDemands.php';
require_once 'classes/HotelRoomTypeGlobalDemand.php';
require_once 'classes/HotelRoomTypeGlobalDemandAdvanceOption.php';
require_once 'classes/HotelRoomTypeDemandPrice.php';
require_once 'classes/HotelRoomTypeDemand.php';
require_once 'classes/HotelRoomTypeRestrictionDateRange.php';

require_once 'classes/QloWebservice.php';
require_once 'classes/QloRoomType.php';
require_once 'classes/HotelRoomDisableDates.php';
require_once 'classes/HotelBranchRefundRules.php';

require_once 'classes/HotelSettingsLink.php';

// Web services classes
require_once 'classes/WebserviceSpecificManagementQlo.php';