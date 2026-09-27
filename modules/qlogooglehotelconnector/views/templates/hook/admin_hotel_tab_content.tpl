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
<div class="tab-pane" id="hotel-tab">
    {if !$has_ws_key}

        <div class="alert alert-warning qlo-ghc-alert-margin">
            {l s='Google Hotel is not configured yet. Please set up the webservice key before enabling hotels.' mod='qlogooglehotelconnector'}
            &nbsp;<a href="{$config_url|escape:'html':'UTF-8'}" class="alert-link">
                {l s='Go to Google Hotel Connector configuration' mod='qlogooglehotelconnector'}
                <i class="icon-external-link"></i>
            </a>
        </div>

    {elseif $id_hotel}

        {* ── Weekly review notice — shown until google_status is active ─ *}
        {if !isset($property.id) || !$property.id || $property.google_status != $google_status_connected}
        <div class="alert alert-info qlo-ghc-alert-offset">
            {l s='Google reviews newly activated properties on a weekly basis. Once you enable the Google Channel for a hotel, it may take up to 1 week for the property to be processed and become available on Google. Once approved, the status will be automatically updated here.' mod='qlogooglehotelconnector'}
        </div>
        {/if}

        {* ── Google Status label ─────────────────────────────────────── *}
        <div class="form-group">
            <label class="control-label col-sm-3">
                <span>{l s='Google Status' mod='qlogooglehotelconnector'}</span>
            </label>
            <div class="col-sm-6 qlo-ghc-col-padded">
                {if isset($property.id) && $property.id && $property.google_status == $google_status_connected}
                    <span class="label label-success qlo-ghc-status-label">
                        <i class="icon-check"></i>&nbsp;{l s='Connected' mod='qlogooglehotelconnector'}
                    </span>
                {elseif isset($property.id) && $property.id && $property.google_status == $google_status_pending}
                    <span class="label label-warning qlo-ghc-status-label">
                        <i class="icon-clock-o"></i>&nbsp;{l s='Pending' mod='qlogooglehotelconnector'}
                    </span>
                {else}
                    <span class="label label-default qlo-ghc-status-label">
                        <i class="icon-times-circle"></i>&nbsp;{l s='Not Connected' mod='qlogooglehotelconnector'}
                    </span>
                {/if}
            </div>
        </div>

        {* ── Missing fields warning ──────────────────────────────────── *}
        {if !empty($missing_fields)}
        <div class="alert alert-warning qlo-ghc-alert-offset">
            <strong>{l s='The following required fields are missing. Please fill them in before enabling Google Hotel:' mod='qlogooglehotelconnector'}
            </strong>
            <ul class="qlo-ghc-missing-list">
                {foreach $missing_fields as $field}
                    <li>{$field|escape:'html':'UTF-8'}</li>
                {/foreach}
            </ul>
        </div>
        {/if}

        {* ── Hotel Enable / Disable ───────────────────────────────────── *}
        <div class="form-group">
            <label class="control-label col-sm-3">
                <span>{l s='Enable on Google Hotel' mod='qlogooglehotelconnector'}</span>
            </label>
            <div class="col-sm-6">
                {if !empty($missing_fields)}
                    {* Required fields are missing: lock the toggle to Off *}
                    <input type="hidden" name="hotel_status" value="0">
                    <span class="switch prestashop-switch fixed-width-lg qlo-ghc-switch-disabled">
                        <input type="radio" value="1" id="hotel_status_on"  disabled="disabled">
                        <label for="hotel_status_on">{l s='Yes' mod='qlogooglehotelconnector'}</label>
                        <input type="radio" value="0" id="hotel_status_off" disabled="disabled" checked="checked">
                        <label for="hotel_status_off">{l s='No' mod='qlogooglehotelconnector'}</label>
                        <a class="slide-button btn"></a>
                    </span>
                    <p class="help-block qlo-ghc-helpblock-top">
                        {l s='Fill in the missing fields above to enable Google Hotel.' mod='qlogooglehotelconnector'}
                    </p>
                {else}
                    <span class="switch prestashop-switch fixed-width-lg">
                        <input type="radio"
                               value="1"
                               id="hotel_status_on"
                               name="hotel_status"
                               {if $is_post && isset($smarty.post.hotel_status) && $smarty.post.hotel_status == '1'}
                                   checked="checked"
                               {elseif !$is_post && isset($property.id) && $property.id}
                                   checked="checked"
                               {/if}>
                        <label for="hotel_status_on">{l s='Yes' mod='qlogooglehotelconnector'}</label>
                        <input type="radio"
                               value="0"
                               id="hotel_status_off"
                               name="hotel_status"
                               {if $is_post && isset($smarty.post.hotel_status) && $smarty.post.hotel_status == '0'}
                                   checked="checked"
                               {elseif !$is_post && (!isset($property.id) || !$property.id)}
                                   checked="checked"
                               {/if}>
                        <label for="hotel_status_off">{l s='No' mod='qlogooglehotelconnector'}</label>
                        <a class="slide-button btn"></a>
                    </span>
                {/if}
            </div>
        </div>

        {* ── Room Types (shown when hotel is enabled and all required fields are set) *}
        {assign var=status_enabled value=false}
        {if empty($missing_fields)}
            {if $is_post && isset($smarty.post.hotel_status) && $smarty.post.hotel_status == '1'}
                {assign var=status_enabled value=true}
            {elseif !$is_post && isset($property.id) && $property.id}
                {assign var=status_enabled value=true}
            {/if}
        {/if}

        <div class="form-group{if !$status_enabled} qlo-ghc-hidden{/if}" id="room-types-section">
            <label class="control-label col-sm-3">
                <span>{l s='Room Types' mod='qlogooglehotelconnector'}</span>
            </label>
            <div class="col-sm-6">
                {if $room_types}
                    <div class="row">
                        <div class="col-sm-12">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th class="fixed-width-xs">
                                            <input type="checkbox"
                                                   id="room_types_all"
                                                   onclick="checkAllRoomTypes(this.checked)" />
                                        </th>
                                        <th>
                                            {l s='Room Type' mod='qlogooglehotelconnector'}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {foreach $room_types as $room_type}
                                        {assign var=cb_id value='room_type_'|cat:$room_type.id_product}
                                        <tr>
                                            <td>
                                                <input type="checkbox"
                                                       id="{$cb_id|escape:'html':'UTF-8'}"
                                                       name="room_types[]"
                                                       class="qlo-ghc-room-type-cb"
                                                       value="{$room_type.id_product|intval}"
                                                       {if $is_post && isset($checked_room_types[$room_type.id_product])}
                                                           checked="checked"
                                                       {elseif !$is_post && isset($room_type_mappings[$room_type.id_product])}
                                                           checked="checked"
                                                       {/if} />
                                            </td>
                                            <td>
                                                <label for="{$cb_id|escape:'html':'UTF-8'}" class="qlo-ghc-label-normal">
                                                    {$room_type.room_type|escape:'html':'UTF-8'}
                                                </label>
                                            </td>
                                        </tr>
                                    {/foreach}
                                </tbody>
                            </table>
                        </div>
                    </div>
                {else}
                    <p class="help-block">
                        {l s='No room types found for this hotel.' mod='qlogooglehotelconnector'}
                    </p>
                {/if}
            </div>
        </div>

    {else}
        {* New hotel — show only the enable toggle; room types are available after first save *}
        <div class="alert alert-info qlo-ghc-alert-offset">
            {l s='Save the hotel first. You will be able to select room types when editing this hotel.' mod='qlogooglehotelconnector'}
        </div>

        <div class="form-group">
            <label class="control-label col-sm-3">
                <span>{l s='Enable on Google Hotel' mod='qlogooglehotelconnector'}</span>
            </label>
            <div class="col-sm-6">
                <span class="switch prestashop-switch fixed-width-lg">
                    <input type="radio" value="1" id="hotel_status_on" name="hotel_status"
                           {if $is_post && isset($smarty.post.hotel_status) && $smarty.post.hotel_status == '1'}checked="checked"{/if}>
                    <label for="hotel_status_on">{l s='Yes' mod='qlogooglehotelconnector'}</label>
                    <input type="radio" value="0" id="hotel_status_off" name="hotel_status"
                           {if !$is_post || !isset($smarty.post.hotel_status) || $smarty.post.hotel_status == '0'}checked="checked"{/if}>
                    <label for="hotel_status_off">{l s='No' mod='qlogooglehotelconnector'}</label>
                    <a class="slide-button btn"></a>
                </span>
            </div>
        </div>
    {/if}
</div>
