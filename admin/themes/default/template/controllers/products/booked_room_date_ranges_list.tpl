{**
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

{l s='This room already has bookings for highlighted date range(s):'}

<table width="70%">
    <tr>
        <th class="text-center">{l s='Order ID'}</th>
        <th class="text-center">{l s='Date From'}</th>
        <th class="text-center">{l s='Date To'}</th>
    </tr>
    {foreach from=$booked_rows_list item=booked_row}
        <tr>
            <td class="text-center">
                <a href="{$link->getAdminLink('AdminOrders')}&id_order={$booked_row->id_order}&vieworder" target="_blank"><strong>#{$booked_row->id_order|intval}</strong></a>
            </td>
            <td class="text-center">{dateFormat date=$booked_row->date_from}</td>
            <td class="text-center">{dateFormat date=$booked_row->date_to}</td>
        </tr>
    {/foreach}
</table>
