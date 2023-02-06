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

{if is_array($booking_documents) && count($booking_documents)}
    {foreach from=$booking_documents item=booking_document}
        <tr>
            <td class="text-left">{$booking_document.id_htl_booking_document}</td>
            <td class="text-center">
                {if $booking_document.file_type == HotelBookingDocument::FILE_TYPE_IMAGE}
                    <a href="{$booking_document.file_link}" target="_blank">
                        <img class="img img-responsive img-thumbnail" src="{$booking_document.file_link}">
                    </a>
                {elseif $booking_document.file_type == HotelBookingDocument::FILE_TYPE_PDF}
                    <a class="btn btn-default" href="{$link->getAdminLink('AdminBookingDocument')}&action=getDocument&id_document={$booking_document.id_htl_booking_document}">
                        {l s='Download PDF'}
                    </a>
                {/if}
            </td>
            <td class="text-center">
                <a class="btn btn-default btn-delete-document" data-id-htl-booking-document="{$booking_document.id_htl_booking_document}">
                    <i class="icon icon-trash"></i>
                </a>
            </td>
        </tr>
    {/foreach}
{else}
    <tr>
        <td colspan="3" class="text-center">{l s='No documents.'}</td>
    </tr>
{/if}
