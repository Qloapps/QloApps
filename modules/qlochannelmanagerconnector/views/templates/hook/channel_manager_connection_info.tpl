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

<div class="row">
    <div class="col-sm-5">
            <label class="qcm_booking_title">
            <i class="icon icon-users"></i> {l s='Channel Manager Bookings' mod='qlochannelmanagerconnector'}
        </label>
    </div>
    <div class="col-sm-7 channel_connection_info">
        <span class="channel_info_type">{l s='Connection status' mod='qlochannelmanagerconnector'} :</span> <span class="channel_connection_status">{l s='Connected' mod='qlochannelmanagerconnector'}</span>
        <span class="channel_info_type">{l s='Last updated' mod='qlochannelmanagerconnector'} :</span> <span>{$last_booking_datetime|escape:'htmlall':'UTF-8'}</span>
        <span class="channel_info_type connection_criteria"><i id="connection_details" class="icon-info-circle" data-toggle="popover" data-content="{l s='Connection status with channel manager is showing according to the bookings fetched from QloApps Channel Manager.' mod='qlochannelmanagerconnector'}"></i></span>
    </div>
</div>
