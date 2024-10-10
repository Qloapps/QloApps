{**
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

{if isset($orderOverBookings) && $orderOverBookings}
    <div class="panel panel-danger">
        <div class="panel-heading">
            <i class="icon-warning"></i> {l s='Overbookings found in this order !!'}
        </div>
        <div class="panel-content">
            <div class="table-responsive form-group">
                <table class="table table-striped">
                    <tr>
                        <th>{l s='Room No.'}</th>
                        <th>{l s='Room type.'}</th>
                        <th>{l s='Duration'}</th>
                        <th>{l s='Occupancy'}</th>
                        <th>{l s='Confirmed booking order'}</th>
                        <th>{l s='Reallocate/Swap'}</th>
                        <th>{l s='Resolve'}</th>
                    </tr>
                    {foreach from=$orderOverBookings item=data}
                        {if !$data.is_refunded}
                            <tr>
                                <td>{$data['room_num']}</td>
                                <td>{$data['room_type_name']}</td>
                                {assign var="is_full_date" value=($show_full_date && ($data['date_from']|date_format:'%D' == $data['date_to']|date_format:'%D'))}
                                <td>{dateFormat date=$data['date_from'] full=$is_full_date} {l s='To'} {dateFormat date=$data['date_to'] full=$is_full_date}</td>
                                <td>
                                    {if $order->with_occupancy && $data['children']}
                                        <div class="dropdown">
                                            <a  data-toggle="dropdown">
                                                <span>{if $data['adults']}{$data['adults']}{/if} {if $data['adults'] > 1}{l s='Adults'}{else}{l s='Adult'}{/if}{if {$data['children']}}, {$data['children']} {if $data['children'] > 1}{l s='Children'}{else}{l s='Child'}{/if}{/if}</span>
                                            </a>
                                            <div class="dropdown-menu well well-sm">
                                                <label>{l s='Children Ages'}</label>
                                                {if isset($data['child_ages']) && $data['child_ages']}
                                                    {foreach $data['child_ages'] as $childAge}
                                                        <p class="">
                                                            {if $childAge == 0}
                                                                {l s='Child %s : Under 1'  sprintf=[$childAge@iteration]}
                                                            {else}
                                                                {l s='Child %s : %s' sprintf=[$childAge@iteration, $childAge]} {if $childAge > 1}{l s='years'}{else}{l s='year'}{/if}
                                                            {/if}
                                                        </p>
                                                    {/foreach}
                                                {/if}
                                            </div>
                                        </div>
                                    {else}
                                        <span>{if $data['adults']}{$data['adults']}{/if} {if $data['adults'] > 1}{l s='Adults'}{else}{l s='Adult'}{/if}{if {$data['children']}}, {$data['children']} {if $data['children'] > 1}{l s='Children'}{else}{l s='Child'}{/if}{/if}</span>
                                    {/if}
                                </td>
                                <td>
                                    {if isset($data['booked_room_info']['id_order']) && $data['booked_room_info']['id_order']}
                                        <a href="{$link->getAdminLink('AdminOrders')|escape:'html':'UTF-8'}&amp;vieworder&amp;id_order={$data['booked_room_info']['id_order']}">#{$data['booked_room_info']['id_order']}</a>
                                    {else}
                                        {l s='N/A'}
                                    {/if}
                                </td>
                                <td>
                                    <a href="#" class="btn btn-default reallocate_overbooking" id_htl_booking="{$data['id']}"><i class="icon-refresh"></i> {l s='Reallocate/Swap Room'}</a>
                                </td>
                                <td>
                                    {if isset($data['booked_room_info']) && $data['booked_room_info']}
                                        <span class="badge badge-information">{l s='Already booked'}</span>
                                    {else}
                                        <a href="{$link->getAdminLink('AdminOrders')|escape:'html':'UTF-8'}&amp;vieworder&amp;id_order={$data['id_order']}&amp;resolve_overbooking={$data['id']}" class="btn btn-default resolve_overbooking" id_htl_booking="{$data['id']}"><i class="icon-refresh"></i> {l s='Resolve'}</a>
                                    {/if}
                                </td>
                            </tr>
                        {/if}
                    {/foreach}
                </table>
            </div>

            <div class="help-block">
                <p>- {l s='You can resolve room overbooking with \'Resolve\' column only when the overbooked room is now free for booked duration.'}</p>
                <p>- {l s='You can also reallocate an overbooked room with an available room to resolve room\'s overbooking.'}</p>
            </div>
        </div>
    </div>
{/if}