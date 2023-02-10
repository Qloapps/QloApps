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

<div class="qcm_content_wrapper">
    <div class="row">
        <div class="col-md-12 qcm_block_wrapper">
            <div class="qcm_info_block">
                <div class="row">
                    <div class="col-sm-6 margin-bottom-10">
                        <span class="connect_status_heading">{l s='Connection status' mod='qlochannelmanagerconnector'} :</span> <span class="not_connect_txt">{l s='Not Connected' mod='qlochannelmanagerconnector'}</span>
                    </div>
                    <div class="col-sm-6 channel_connection_info margin-bottom-10">
                        <span class="channel_info_type">{l s='Last updated' mod='qlochannelmanagerconnector'} :</span> <span>{$current_datetime|escape:'htmlall':'UTF-8'}</span>
                    </div>

                    <div class="connect_info_txt col-sm-12 margin-bottom-10">
                        **{l s='Connection status with channel manager is showing according to the bookings fetched from QloApps Channel Manager.' mod='qlochannelmanagerconnector'}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row flex-display">
        <div class="col-md-6 qcm_block_wrapper padding-left-10 flex-display">
            <div class="qcm_info_block">
                <div class="row flex-display">
                    <div class="col-md-2 qcm_info_img_container">
                        <img src="{$link->getMediaLink("`$module_dir`/qlochannelmanagerconnector/views/img/channel_manager_connect.png")|escape:'htmlall':'UTF-8'}" class="img-responsive">
                    </div>
                    <div class="col-md-10 qcm_info_wrapper">
                        <div class="qcm_info_block_head">
                            {l s='How to connect with channel manager?' mod='qlochannelmanagerconnector'}
                        </div>
                        <div class="qcm_info_block_content">
                            {l s='You can connect with channel manager through few simple steps' mod='qlochannelmanagerconnector'} :
                            <ul>
                                <li>{l s='Enable QloApps webservice from Webservice tab.' mod='qlochannelmanagerconnector'} <a target="blank" href="{$link->getAdminLink('AdminWebservice')}"><i class="icon-external-link"></i></a></li>
                                <li>{l s='Create your webservice key and enable all the APIs from Webservice tab.' mod='qlochannelmanagerconnector'} <a target="blank" href="{$link->getAdminLink('AdminWebservice')}"><i class="icon-external-link"></i></a></li>
                                <li>{l s='Create account on Channel Manager' mod='qlochannelmanagerconnector'} <a target="blank" class="qcm-link" href="https://channels.qloapps.com/"><i class="icon-external-link"></i></a>. {l s=' Then enter QloApps webservice credentials under PMS settings of channel manager .' mod='qlochannelmanagerconnector'}</li>
                                <li>{l s='Synchronize and map QloApps hotels and room types in channel manager.' mod='qlochannelmanagerconnector'}</li>
                            </ul>

                            {l s='To read the connection process in detail, visit' mod='qlochannelmanagerconnector'} <a class="qcm-link" href="https://qloapps.com/qloapps-channel-manager/#section-24">{l s='Connection with PMS' mod='qlochannelmanagerconnector'}</a>
                        </div>
                        <div class="qcm_info_explore">
                            {l s='If you are still not connected with channel manager' mod='qlochannelmanagerconnector'} <a class="qcm-link" href="https://channels.qloapps.com/">{l s='Connect It Now' mod='qlochannelmanagerconnector'}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 qcm_block_wrapper padding-right-10 flex-display">
            <div class="qcm_info_block">
                <div class="row flex-display">
                    <div class="col-md-2 qcm_info_img_container">
                        <img src="{$link->getMediaLink("`$module_dir`/qlochannelmanagerconnector/views/img/channel_manager.png")|escape:'htmlall':'UTF-8'}" class="img-responsive">
                    </div>
                    <div class="col-md-10 qcm_info_wrapper">
                        <div class="qcm_info_block_head">
                            {l s='What is channel manager?' mod='qlochannelmanagerconnector'}
                        </div>
                        <div class="qcm_info_block_content">
                            {l s='A channel manager is a centralized software that synchronizes availabilities and details of the property across all platforms i.e. online travel agencies (OTA) and other online distribution channels.' mod='qlochannelmanagerconnector'}
                            <br>
                            {l s='QloApps channel manager synchronizes inventory, rates, restrictions with all connected OTA channels and automated bookings sync connected OTAs and QloApps PMS.' mod='qlochannelmanagerconnector'}
                        </div>
                        <div class="qcm_info_explore">
                            {l s='For channel manager information in detail' mod='qlochannelmanagerconnector'} <a class="qcm-link" href="https://qloapps.com/channel-manager/">{l s='Explore Channel Manager' mod='qlochannelmanagerconnector'}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 qcm_block_wrapper">
            <div class="qcm_info_block">
                <div class="row flex-display">
                    <div class="col-md-1 qcm_info_img_container">
                        <img src="{$link->getMediaLink("`$module_dir`/qlochannelmanagerconnector/views/img/channel_manager_advantage.png")|escape:'htmlall':'UTF-8'}" class="img-responsive">
                    </div>
                    <div class="col-md-11 qcm_info_wrapper">
                        <div class="qcm_info_block_head">
                            {l s='Advantages of Channel Manager' mod='qlochannelmanagerconnector'}
                        </div>
                        <div class="qcm_info_block_content">
                            <ul>
                                <li>{l s='Hoteliers need not to worry about problems like over-bookings, inconsistent inventory management, and missed opportunities.' mod='qlochannelmanagerconnector'}</li>
                                <li>{l s='Real-time sync with the world\'s most popular OTA channels like Booking, MakeMyTrip, Expedia, Airbnb, Agoda, Google Hotels, and many more.' mod='qlochannelmanagerconnector'}</li>
                                <li>{l s='Provides an intuitive & analytical dashboard that brings useful data and stats.' mod='qlochannelmanagerconnector'}</li>
                                <li>{l s='With the one-click rate and inventory updates, directly push availability and rates on the connected OTA channels.' mod='qlochannelmanagerconnector'}</li>
                                <li>{l s='Already integrated with QloApps PMS that auto-synchronize inventories for the bookings created on QloApps PMS and connected OTA channels.' mod='qlochannelmanagerconnector'}</li>
                                <li>{l s='Getting more property impressions by travellers on the world’s leading Online Travel Agencies as well as on the QloApps Hotel booking website will boost your online brand visibility and returns you more reservations.' mod='qlochannelmanagerconnector'}</li>
                            </ul>
                        </div>
                        <div class="qcm_info_explore">
                            {l s='To know channel manager freatures and work-flow in details' mod='qlochannelmanagerconnector'} <a class="qcm-link" href="https://qloapps.com/qloapps-channel-manager/">{l s='Visit Documentation' mod='qlochannelmanagerconnector'}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
