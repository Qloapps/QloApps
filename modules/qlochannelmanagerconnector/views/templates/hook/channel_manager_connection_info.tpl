{*
 * 2010-2023 Webkul.
 *
 * NOTICE OF LICENSE
 *
 * All right is reserved,
 * Please go through LICENSE.txt file inside our module
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please refer to CustomizationPolicy.txt file inside our module for more information.
 *
 * @author Webkul IN
 * @copyright 2010-2023 Webkul IN
 * @license LICENSE.txt
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
